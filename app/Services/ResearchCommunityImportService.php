<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;

class ResearchCommunityImportService
{
    private const RESEARCH_HEADER = ['Tahun', 'Judul Penelitian', 'Peneliti'];
    // Column B (index 1) previously named 'Tahun Program' — now expects 'Nama Program'
    private const COMMUNITY_HEADER = ['Tahun', 'Nama Program', 'Lokasi'];

    public function parseUploadedFile(UploadedFile $file): array
    {
        $ext = strtolower($file->getClientOriginalExtension());

        if ($ext === 'csv' || $ext === 'txt') {
            $content = file_get_contents($file->getRealPath());
            return $this->parseCombinedCsv($content);
        }

        if (class_exists('\Maatwebsite\\Excel\\Facades\\Excel')) {
            $sheets = \Maatwebsite\Excel\Facades\Excel::toArray(new \stdClass(), $file);
            return $this->normalizeSheetsArray($sheets);
        }

        throw new \Exception('Unsupported file type. Install maatwebsite/excel or upload CSV.');
    }

    public function parseGoogleSheetPublic(string $sheetUrl): array
    {
        if (preg_match('#/spreadsheets/d/([a-zA-Z0-9-_]+)#', $sheetUrl, $m)) {
            $id = $m[1];
        } else {
            throw new \Exception('Invalid Google Sheets URL');
        }

        $researchCandidates = ['penelitian', 'Penelitian', 'PENELITIAN'];
        $communityCandidates = ['pengabdian', 'Pengabdian', 'PENGABDIAN'];

        $research = $this->findSheetByName($id, $researchCandidates, self::RESEARCH_HEADER);
        $community = $this->findSheetByName($id, $communityCandidates, self::COMMUNITY_HEADER);

        if ($research === null || $community === null) {
            throw new \Exception('Tidak dapat menemukan sheet "penelitian" atau "pengabdian" yang sesuai. Pastikan nama sheet dan header tepat.');
        }

        return $this->parseCombinedCsvSections($research, $community);
    }

    private function findSheetByName(string $id, array $names, array $expectedHeader): ?string
    {
        foreach ($names as $name) {
            $csv = $this->fetchSheetCsvByName($id, $name);
            if ($csv === null) {
                continue;
            }

            if ($this->looksLikeHtml($csv)) {
                continue;
            }

            if ($this->headerMatches($csv, $expectedHeader)) {
                return $csv;
            }
        }

        return null;
    }

    private function fetchSheetCsvByName(string $id, string $name): ?string
    {
        $url = "https://docs.google.com/spreadsheets/d/{$id}/gviz/tq?tqx=out:csv&sheet=" . rawurlencode($name);
        $csv = @file_get_contents($url);
        if ($csv === false || trim($csv) === '') {
            return null;
        }
        return $csv;
    }

    private function normalizeSheetsArray(array $sheets): array
    {
        $researchRows = [];
        $communityRows = [];

        foreach ($sheets as $sheet) {
            if (!is_array($sheet) || empty($sheet)) {
                continue;
            }

            $header = $this->normalizeHeader(array_map(static fn($cell) => (string) ($cell ?? ''), $sheet[0] ?? []));

            if ($this->headersEqual($header, self::RESEARCH_HEADER)) {
                $researchRows = $this->rowsToAssoc($sheet, $header);
                continue;
            }

            if ($this->headersEqual($header, self::COMMUNITY_HEADER)) {
                $communityRows = $this->rowsToAssoc($sheet, $header);
            }
        }

        if (empty($researchRows) || empty($communityRows)) {
            throw new \Exception('Format sheet tidak valid. Pastikan ada sheet dengan header Penelitian dan Pengabdian yang sesuai.');
        }

        return [
            'penelitian' => $researchRows,
            'pengabdian' => $communityRows,
        ];
    }

    private function rowsToAssoc(array $sheetRows, array $header): array
    {
        $result = [];

        for ($i = 1; $i < count($sheetRows); $i++) {
            $row = is_array($sheetRows[$i]) ? $sheetRows[$i] : [];

            $assoc = [];
            for ($c = 0; $c < count($header); $c++) {
                $assoc[$header[$c]] = (string) ($row[$c] ?? '');
            }

            // Skip fully empty lines.
            if (implode('', array_map('trim', $assoc)) === '') {
                continue;
            }

            $result[] = $assoc;
        }

        return $result;
    }

    private function parseCombinedCsv(string $content): array
    {
        return $this->parseCombinedCsvSections($content, '');
    }

    private function parseCombinedCsvSections(string $researchCsv, string $communityCsv): array
    {
        $researchRows = [];
        $communityRows = [];

        if (trim($researchCsv) === '') {
            throw new \Exception('Sheet "penelitian" tidak ditemukan.');
        }

        if (trim($communityCsv) === '') {
            throw new \Exception('Sheet "pengabdian" tidak ditemukan.');
        }

        $researchRows = $this->parseSheetRows($researchCsv, self::RESEARCH_HEADER, 'penelitian');
        $communityRows = $this->parseSheetRows($communityCsv, self::COMMUNITY_HEADER, 'pengabdian');

        return ['penelitian' => $researchRows, 'pengabdian' => $communityRows];
    }

    private function parseSheetRows(string $csv, array $expectedHeader, string $sheetName): array
    {
        $rows = $this->csvToRows($csv);
        if (empty($rows)) {
            throw new \Exception('Sheet "' . $sheetName . '" kosong.');
        }

        $header = $this->normalizeHeader($rows[0]);
        $this->assertHeaderMatch($header, $expectedHeader, $sheetName);

        $result = [];
        for ($i = 1; $i < count($rows); $i++) {
            $row = $rows[$i];
            if (count($row) === 0) {
                continue;
            }
            $assoc = [];
            for ($c = 0; $c < count($header); $c++) {
                $assoc[$header[$c]] = $row[$c] ?? '';
            }
            $result[] = $assoc;
        }

        return $result;
    }

    private function headerMatches(string $csv, array $expected): bool
    {
        $rows = $this->csvToRows($csv);
        if (empty($rows)) {
            return false;
        }
        $header = $this->normalizeHeader($rows[0]);
        return $this->headersEqual($header, $expected);
    }

    private function normalizeHeader(array $header): array
    {
        $normalized = [];
        foreach ($header as $idx => $value) {
            $v = (string) $value;
            if ($idx === 0) {
                $v = ltrim($v, "\xEF\xBB\xBF");
            }
            $v = str_replace("\xC2\xA0", ' ', $v);
            $v = preg_replace('/\s+/', ' ', $v) ?? '';
            $v = trim($v);
            $normalized[] = $v;
        }

        while (count($normalized) > 0 && $normalized[count($normalized) - 1] === '') {
            array_pop($normalized);
        }

        return $normalized;
    }

    private function assertHeaderMatch(array $header, array $expected, string $sheetName): void
    {
        $expectedNorm = $this->normalizeHeader($expected);
        if (count($header) !== count($expectedNorm)) {
            throw new \Exception('Header sheet "' . $sheetName . '" harus: ' . implode(', ', $expectedNorm) . '. Header saat ini: ' . implode(', ', $header));
        }

        foreach ($expectedNorm as $i => $name) {
            if (! isset($header[$i]) || strtolower($header[$i]) !== strtolower($name)) {
                throw new \Exception('Header sheet "' . $sheetName . '" harus: ' . implode(', ', $expectedNorm) . '. Header saat ini: ' . implode(', ', $header));
            }
        }
    }

    private function headersEqual(array $header, array $expected): bool
    {
        $expectedNorm = $this->normalizeHeader($expected);
        if (count($header) !== count($expectedNorm)) {
            return false;
        }
        foreach ($expectedNorm as $i => $name) {
            if (! isset($header[$i]) || strtolower($header[$i]) !== strtolower($name)) {
                return false;
            }
        }
        return true;
    }

    private function looksLikeHtml(string $body): bool
    {
        $trim = ltrim($body);
        return str_starts_with($trim, '<!DOCTYPE')
            || str_starts_with($trim, '<html')
            || str_contains($trim, '<head>')
            || str_contains($trim, '<title>');
    }

    private function csvToRows(string $csvContent): array
    {
        $lines = preg_split('/\r\n|\r|\n/', $csvContent);
        $rows = [];
        $delimiter = $this->detectDelimiter($lines);
        foreach ($lines as $line) {
            if (trim($line) === '') {
                $rows[] = [];
                continue;
            }
            $fp = fopen('php://memory', 'r+');
            fwrite($fp, $line);
            rewind($fp);
            $row = str_getcsv(stream_get_contents($fp), $delimiter);
            fclose($fp);
            if ($row === false) {
                $row = array_map('trim', explode($delimiter, $line));
            }
            $rows[] = $row;
        }
        return $rows;
    }

    private function detectDelimiter(array $lines): string
    {
        $candidates = [',', ';', "\t", '|'];
        $sample = '';
        foreach ($lines as $line) {
            if (trim($line) !== '') {
                $sample = $line;
                break;
            }
        }

        if ($sample === '') {
            return ',';
        }

        $best = ',';
        $bestCount = 0;
        foreach ($candidates as $delim) {
            $count = substr_count($sample, $delim);
            if ($count > $bestCount) {
                $bestCount = $count;
                $best = $delim;
            }
        }

        return $best;
    }
}
