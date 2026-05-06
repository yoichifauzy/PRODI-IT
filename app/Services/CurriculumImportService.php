<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;

class CurriculumImportService
{
    private const KURIKULUM_HEADER = ['Nama Kurikulum'];
    private const MATAKULIAH_HEADER = ['Nama Kurikulum', 'Kode', 'Nama', 'SKS Teori', 'SKS Praktek'];

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
        // try to extract spreadsheet id
        if (preg_match('#/spreadsheets/d/([a-zA-Z0-9-_]+)#', $sheetUrl, $m)) {
            $id = $m[1];
        } else {
            throw new \Exception('Invalid Google Sheets URL');
        }

        $kurCandidates = ['kurikulum', 'Kurikulum', 'KURIKULUM'];
        $matCandidates = ['matakuliah', 'Matakuliah', 'MataKuliah', 'Mata Kuliah', 'Mata kuliah'];

        $kur = null;
        $mat = null;
        $matRaw = null;
        $kurHeader = null;
        $matHeader = null;
        $kurHtml = false;
        $matHtml = false;
        $matNeedsColumns = false;

        foreach ($kurCandidates as $name) {
            $probe = $this->probeSheetCsvByName($id, $name, self::KURIKULUM_HEADER, false);
            if ($probe['html']) {
                $kurHtml = true;
            }
            if ($probe['header'] !== null) {
                $kurHeader = $probe['header'];
            }
            if ($probe['csv'] !== null) {
                $kur = $probe['csv'];
                break;
            }
        }

        foreach ($matCandidates as $name) {
            $probe = $this->probeSheetCsvByName($id, $name, self::MATAKULIAH_HEADER, true);
            if ($probe['html']) {
                $matHtml = true;
            }
            if ($probe['header'] !== null) {
                $matHeader = $probe['header'];
            }
            if (!empty($probe['needsDataColumns'])) {
                $matNeedsColumns = true;
            }
            if (!empty($probe['raw'])) {
                $matRaw = $probe['raw'];
            }
            if ($probe['csv'] !== null) {
                $mat = $probe['csv'];
                $matNeedsColumns = false;
                break;
            }
        }


        if ($kur === null || $mat === null) {
            $meta = $this->fetchSheetMetadata($id);
            $metaKur = $meta['kurikulum'] ?? $meta[$this->normalizeSheetName('kurikulum')] ?? null;
            if ($kur === null && $metaKur !== null) {
                $probe = $this->probeSheetCsvByGid($id, $metaKur, self::KURIKULUM_HEADER, false);
                if ($probe['html']) {
                    $kurHtml = true;
                }
                if ($probe['header'] !== null) {
                    $kurHeader = $probe['header'];
                }
                if ($probe['csv'] !== null) {
                    $kur = $probe['csv'];
                }
            }
            $metaMat = $meta['matakuliah'] ?? $meta[$this->normalizeSheetName('matakuliah')] ?? $meta[$this->normalizeSheetName('mata kuliah')] ?? null;
            if ($mat === null && $metaMat !== null) {
                $probe = $this->probeSheetCsvByGid($id, $metaMat, self::MATAKULIAH_HEADER, true);
                if ($probe['html']) {
                    $matHtml = true;
                }
                if ($probe['header'] !== null) {
                    $matHeader = $probe['header'];
                }
                if (!empty($probe['needsDataColumns'])) {
                    $matNeedsColumns = true;
                }
                if (!empty($probe['raw'])) {
                    $matRaw = $probe['raw'];
                }
                if ($probe['csv'] !== null) {
                    $mat = $probe['csv'];
                    $matNeedsColumns = false;
                }
            }
            if ($mat === null && isset($meta['mata kuliah'])) {
                $probe = $this->probeSheetCsvByGid($id, $meta['mata kuliah'], self::MATAKULIAH_HEADER, true);
                if ($probe['html']) {
                    $matHtml = true;
                }
                if ($probe['header'] !== null) {
                    $matHeader = $probe['header'];
                }
                if (!empty($probe['needsDataColumns'])) {
                    $matNeedsColumns = true;
                }
                if (!empty($probe['raw'])) {
                    $matRaw = $probe['raw'];
                }
                if ($probe['csv'] !== null) {
                    $mat = $probe['csv'];
                    $matNeedsColumns = false;
                }
            }
        }


        if ($kur === null) {
            $probe = $this->probeSheetCsvByGid($id, 0, self::KURIKULUM_HEADER, false);
            if ($probe['html']) {
                $kurHtml = true;
            }
            if ($probe['header'] !== null) {
                $kurHeader = $probe['header'];
            }
            if ($probe['csv'] !== null) {
                $kur = $probe['csv'];
            }
        }
        if ($mat === null) {
            $probe = $this->probeSheetCsvByGid($id, 1, self::MATAKULIAH_HEADER, true);
            if ($probe['html']) {
                $matHtml = true;
            }
            if ($probe['header'] !== null) {
                $matHeader = $probe['header'];
            }
            if (!empty($probe['needsDataColumns'])) {
                $matNeedsColumns = true;
            }
            if (!empty($probe['raw'])) {
                $matRaw = $probe['raw'];
            }
            if ($probe['csv'] !== null) {
                $mat = $probe['csv'];
                $matNeedsColumns = false;
            }
        }

        if ($mat === null && $matRaw !== null && $matHeader !== null && $this->isMinimalMatakuliahHeader($matHeader)) {
            $mat = $matRaw;
        }

        if ($kurHtml || $matHtml) {
            throw new \Exception('Spreadsheet belum bisa diakses publik. Pastikan share "Anyone with the link" atau gunakan Publish to web.');
        }

        if ($kur === null && $kurHeader !== null) {
            throw new \Exception('Header sheet "kurikulum" harus: ' . $this->expectedHeaderDisplay(self::KURIKULUM_HEADER) . '. Header saat ini: ' . implode(', ', $kurHeader));
        }

        if ($mat === null && $matHeader !== null) {
            throw new \Exception('Header sheet "matakuliah" harus: ' . $this->expectedHeaderDisplay(self::MATAKULIAH_HEADER) . '. Header saat ini: ' . implode(', ', $matHeader));
        }

        if ($kur === null || $mat === null) {
            throw new \Exception('Tidak dapat menemukan sheet "kurikulum" atau "matakuliah" yang sesuai. Pastikan nama sheet dan header tepat.');
        }

        return $this->parseCombinedCsvSections($kur, $mat);
    }

    protected function parseCombinedCsv(string $content): array
    {

        return $this->parseCombinedCsvSections($content, '');
    }

    protected function parseCombinedCsvSections(string $kurCsv, string $matCsv): array
    {
        $kur = [];
        $mat = [];

        if (trim($kurCsv) === '') {
            throw new \Exception('Sheet "kurikulum" tidak ditemukan.');
        }

        if (trim($matCsv) === '') {
            throw new \Exception('Sheet "matakuliah" tidak ditemukan.');
        }


        if (! empty($kurCsv)) {
            $rows = $this->csvToRows($kurCsv);
            if (empty($rows)) {
                throw new \Exception('Sheet "kurikulum" kosong.');
            }

            $header = $this->normalizeHeader($rows[0]);
            $this->assertHeaderMatch($header, self::KURIKULUM_HEADER, 'kurikulum');

            for ($i = 1; $i < count($rows); $i++) {
                $r = $rows[$i];
                if (count($r) === 0) {
                    continue;
                }
                $first = trim((string) ($r[0] ?? ''));
                if ($first !== '') {
                    $kur[] = $first;
                }
            }
        }


        if (! empty($matCsv)) {
            $rows = $this->csvToRows($matCsv);
            if (empty($rows)) {
                throw new \Exception('Sheet "matakuliah" kosong.');
            }

            $header = $this->normalizeHeader($rows[0]);
            $isMinimalMatakuliah = $this->isMinimalMatakuliahHeader($header);
            $this->assertHeaderMatch($header, self::MATAKULIAH_HEADER, 'matakuliah');

            if ($isMinimalMatakuliah) {
                $header = self::MATAKULIAH_HEADER;
            }

            for ($idx = 1; $idx < count($rows); $idx++) {
                $r = $rows[$idx];
                if (count($r) === 0) {
                    continue;
                }
                $assoc = [];
                for ($i = 0; $i < count($header); $i++) {
                    $assoc[$header[$i]] = $r[$i] ?? '';
                }
                $mat[] = $assoc;
            }
        }

        return ['kurikulum' => $kur, 'matakuliah' => $mat];
    }

    protected function csvToRows(string $csvContent): array
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

    protected function normalizeSheetsArray(array $sheets): array
    {

        $kur = [];
        $mat = [];

        if (! isset($sheets[0]) || ! isset($sheets[1])) {
            throw new \Exception('File harus memiliki 2 sheet: "kurikulum" dan "matakuliah".');
        }

        $sheet0 = $sheets[0];
        if (empty($sheet0)) {
            throw new \Exception('Sheet "kurikulum" kosong.');
        }
        $header0 = $this->normalizeHeader($sheet0[0]);
        $this->assertHeaderMatch($header0, self::KURIKULUM_HEADER, 'kurikulum');
        for ($i = 1; $i < count($sheet0); $i++) {
            $name = trim((string) ($sheet0[$i][0] ?? ''));
            if ($name !== '') {
                $kur[] = $name;
            }
        }

        $sheet1 = $sheets[1];
        if (empty($sheet1)) {
            throw new \Exception('Sheet "matakuliah" kosong.');
        }
        $header1 = $this->normalizeHeader($sheet1[0]);
        $isMinimalMatakuliah = $this->isMinimalMatakuliahHeader($header1);
        $this->assertHeaderMatch($header1, self::MATAKULIAH_HEADER, 'matakuliah');

        if ($isMinimalMatakuliah) {
            $header1 = self::MATAKULIAH_HEADER;
        }

        for ($idx = 1; $idx < count($sheet1); $idx++) {
            $r = $sheet1[$idx];
            $assoc = [];
            foreach ($header1 as $i => $h) {
                $assoc[$h] = $r[$i] ?? '';
            }
            $mat[] = $assoc;
        }

        return ['kurikulum' => $kur, 'matakuliah' => $mat];
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
            if (strcasecmp($v, 'Nama Kuliah') === 0) {
                $v = 'Nama Kurikulum';
            }
            $normalized[] = $v;
        }

        // drop trailing empty columns
        while (count($normalized) > 0 && $normalized[count($normalized) - 1] === '') {
            array_pop($normalized);
        }

        return $normalized;
    }

    private function assertHeaderMatch(array $header, array $expected, string $sheetName): void
    {
        $expectedNorm = $this->normalizeHeader($expected);
        $expectedDisplay = $this->expectedHeaderDisplay($expectedNorm);
        $actualDisplay = implode(', ', $header);

        if ($sheetName === 'matakuliah' && $this->isMinimalMatakuliahHeader($header)) {
            return;
        }

        if (count($header) !== count($expectedNorm)) {
            throw new \Exception('Header sheet "' . $sheetName . '" harus: ' . $expectedDisplay . '. Header saat ini: ' . $actualDisplay);
        }

        foreach ($expectedNorm as $i => $name) {
            if (! isset($header[$i]) || strtolower($header[$i]) !== strtolower($name)) {
                throw new \Exception('Header sheet "' . $sheetName . '" harus: ' . $expectedDisplay . '. Header saat ini: ' . $actualDisplay);
            }
        }
    }

    private function expectedHeaderDisplay(array $expected): string
    {
        $display = $expected;
        if (isset($display[0]) && $display[0] === 'Nama Kurikulum') {
            $display[0] = 'Nama Kurikulum';
        }
        return implode(', ', $display);
    }

    private function probeSheetCsvByName(string $id, string $name, array $expectedHeader, bool $allowMinimalMatakuliah = false): array
    {
        $csv = $this->fetchSheetCsvByName($id, $name);
        if ($csv === null) {
            return ['csv' => null, 'header' => null, 'html' => false, 'needsDataColumns' => false, 'raw' => null];
        }

        if ($this->looksLikeHtml($csv)) {
            return ['csv' => null, 'header' => null, 'html' => true, 'needsDataColumns' => false, 'raw' => $csv];
        }

        $header = $this->extractHeader($csv);
        if ($header !== null && $this->headersEqual($header, $expectedHeader, $allowMinimalMatakuliah)) {
            if ($allowMinimalMatakuliah && $this->isMinimalMatakuliahHeader($header) && ! $this->hasSecondaryColumns($csv)) {
                return ['csv' => null, 'header' => $header, 'html' => false, 'needsDataColumns' => true, 'raw' => $csv];
            }
            return ['csv' => $csv, 'header' => $header, 'html' => false, 'needsDataColumns' => false, 'raw' => $csv];
        }

        return ['csv' => null, 'header' => $header, 'html' => false, 'needsDataColumns' => false, 'raw' => $csv];
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

    private function probeSheetCsvByGid(string $id, int $gid, array $expectedHeader, bool $allowMinimalMatakuliah = false): array
    {
        $csv = $this->fetchSheetCsvByGid($id, $gid);
        if ($csv === null) {
            return ['csv' => null, 'header' => null, 'html' => false, 'needsDataColumns' => false, 'raw' => null];
        }

        if ($this->looksLikeHtml($csv)) {
            return ['csv' => null, 'header' => null, 'html' => true, 'needsDataColumns' => false, 'raw' => $csv];
        }

        $header = $this->extractHeader($csv);
        if ($header !== null && $this->headersEqual($header, $expectedHeader, $allowMinimalMatakuliah)) {
            if ($allowMinimalMatakuliah && $this->isMinimalMatakuliahHeader($header) && ! $this->hasSecondaryColumns($csv)) {
                return ['csv' => null, 'header' => $header, 'html' => false, 'needsDataColumns' => true, 'raw' => $csv];
            }
            return ['csv' => $csv, 'header' => $header, 'html' => false, 'needsDataColumns' => false, 'raw' => $csv];
        }

        return ['csv' => null, 'header' => $header, 'html' => false, 'needsDataColumns' => false, 'raw' => $csv];
    }

    private function fetchSheetCsvByGid(string $id, int $gid): ?string
    {
        $url = "https://docs.google.com/spreadsheets/d/{$id}/gviz/tq?tqx=out:csv&gid={$gid}";
        $csv = @file_get_contents($url);
        if ($csv === false || trim($csv) === '') {
            return null;
        }
        return $csv;
    }

    private function headerMatches(string $csv, array $expected): bool
    {
        $rows = $this->csvToRows($csv);
        if (empty($rows)) {
            return false;
        }
        $header = $this->normalizeHeader($rows[0]);
        return $this->headersEqual($header, $expected, false);
    }

    private function extractHeader(string $csv): ?array
    {
        $rows = $this->csvToRows($csv);
        if (empty($rows)) {
            return null;
        }

        return $this->normalizeHeader($rows[0]);
    }

    private function looksLikeHtml(string $body): bool
    {
        $trim = ltrim($body);
        return str_starts_with($trim, '<!DOCTYPE')
            || str_starts_with($trim, '<html')
            || str_contains($trim, '<head>')
            || str_contains($trim, '<title>');
    }

    private function headersEqual(array $header, array $expected, bool $allowMinimalMatakuliah): bool
    {
        $expectedNorm = $this->normalizeHeader($expected);
        if (count($header) !== count($expectedNorm)) {
            if ($allowMinimalMatakuliah && $this->isMinimalMatakuliahHeader($header)) {
                return true;
            }
            return false;
        }
        foreach ($expectedNorm as $i => $name) {
            if (! isset($header[$i]) || strtolower($header[$i]) !== strtolower($name)) {
                return false;
            }
        }
        return true;
    }

    private function isMinimalMatakuliahHeader(array $header): bool
    {
        return count($header) === 1 && strtolower($header[0]) === 'nama kurikulum';
    }

    private function hasSecondaryColumns(string $csv): bool
    {
        $rows = $this->csvToRows($csv);
        foreach ($rows as $idx => $row) {
            if ($idx === 0) {
                continue;
            }
            for ($i = 1; $i < count($row); $i++) {
                if (trim((string) ($row[$i] ?? '')) !== '') {
                    return true;
                }
            }
        }

        return false;
    }

    private function fetchSheetMetadata(string $id): array
    {
        $url = "https://docs.google.com/spreadsheets/d/{$id}/edit";
        $html = @file_get_contents($url);
        if ($html === false || trim($html) === '') {
            return [];
        }

        $meta = [];
        if (preg_match_all('/"title":"([^"]+)","sheetId":(\d+)/', $html, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $m) {
                $title = stripcslashes($m[1]);
                $title = strtolower(trim($title));
                if (! isset($meta[$title])) {
                    $meta[$title] = (int) $m[2];
                }
                $normalized = $this->normalizeSheetName($title);
                if (! isset($meta[$normalized])) {
                    $meta[$normalized] = (int) $m[2];
                }
            }
        }

        return $meta;
    }

    private function normalizeSheetName(string $name): string
    {
        $name = strtolower(trim($name));
        $name = preg_replace('/[^a-z0-9]+/', '', $name) ?? '';
        return $name;
    }
}
