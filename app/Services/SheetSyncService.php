<?php

namespace App\Services;

/**
 * SheetSyncService
 *
 * Reads data from a public Google Sheets URL using the CSV export endpoint.
 * Supports reading a single default sheet (first sheet), or named sheets.
 *
 * Google Sheet must be shared as "Anyone with the link can view".
 */
class SheetSyncService
{
    /**
     * Extract the spreadsheet ID from a Google Sheets URL.
     */
    public function extractId(string $url): string
    {
        if (preg_match('#/spreadsheets/d/([a-zA-Z0-9_-]+)#', $url, $m)) {
            return $m[1];
        }
        throw new \Exception('URL Google Sheets tidak valid. Pastikan format URL benar.');
    }

    /**
     * Fetch CSV content for a named sheet tab.
     */
    public function fetchSheetByName(string $spreadsheetId, string $sheetName): string
    {
        $url = "https://docs.google.com/spreadsheets/d/{$spreadsheetId}/gviz/tq?tqx=out:csv&sheet=" . rawurlencode($sheetName);
        return $this->fetchCsv($url);
    }

    /**
     * Fetch CSV content for the first sheet (gid=0) or by gid.
     */
    public function fetchSheetByGid(string $spreadsheetId, int $gid = 0): string
    {
        $url = "https://docs.google.com/spreadsheets/d/{$spreadsheetId}/gviz/tq?tqx=out:csv&gid={$gid}";
        return $this->fetchCsv($url);
    }

    /**
     * Parse CSV content into an array of rows.
     * Skips the first row (header).
     * Skips rows where all specified required column indices are empty.
     *
     * @param  string  $csv          Raw CSV string
     * @param  int[]   $requiredCols Column indices (0-based) that must be non-empty
     * @return array<int, array<int, string>>
     */
    public function parseCsv(string $csv, array $requiredCols = [0]): array
    {
        $rows = $this->splitCsvRows($csv);

        // Skip header row
        array_shift($rows);

        $results = [];
        foreach ($rows as $row) {
            // Skip rows where any required column is empty
            $skip = false;
            foreach ($requiredCols as $col) {
                if (!isset($row[$col]) || trim((string) $row[$col]) === '') {
                    $skip = true;
                    break;
                }
            }
            if ($skip) {
                continue;
            }
            $results[] = array_map(fn($v) => trim((string) $v), $row);
        }

        return $results;
    }

    /**
     * Parse all sheets from a Google Sheets file by fetching all tab GIDs.
     * Returns a map of [ sheetName => rows[] ] for each sheet.
     * Skips first row of each sheet.
     *
     * @param  string  $spreadsheetId
     * @param  int[]   $requiredCols
     * @return array<string, array<int, array<int, string>>>
     */
    public function parseAllSheets(string $spreadsheetId, array $requiredCols = [0]): array
    {
        $sheetMeta = $this->fetchSheetMeta($spreadsheetId);
        $results   = [];

        if (empty($sheetMeta)) {
            // Fallback: just read gid=0
            $csv  = $this->fetchSheetByGid($spreadsheetId, 0);
            $rows = $this->parseCsv($csv, $requiredCols);
            $results['Sheet1'] = $rows;
            return $results;
        }

        foreach ($sheetMeta as $name => $gid) {
            try {
                $csv  = $this->fetchSheetByGid($spreadsheetId, $gid);
                if (trim($csv) === '') {
                    continue;
                }
                $rows = $this->parseCsv($csv, $requiredCols);
                if (!empty($rows)) {
                    $results[$name] = $rows;
                }
            } catch (\Exception) {
                // Skip unreadable sheets
            }
        }

        return $results;
    }

    // -------------------------------------------------------------------------
    // Private helpers
    // -------------------------------------------------------------------------

    private function fetchCsv(string $url): string
    {
        $ctx = stream_context_create([
            'http' => ['timeout' => 15],
        ]);

        $body = @file_get_contents($url, false, $ctx);

        if ($body === false || trim($body) === '') {
            throw new \Exception(
                'Tidak dapat mengambil data dari sheet. Pastikan Google Sheets sudah diatur "Anyone with the link can view" atau "Publish to web".'
            );
        }

        if ($this->looksLikeHtml($body)) {
            throw new \Exception(
                'Sheet mengembalikan halaman HTML, bukan CSV. Pastikan akses publik sudah diaktifkan.'
            );
        }

        return $body;
    }

    private function looksLikeHtml(string $body): bool
    {
        $trim = ltrim($body);
        return str_starts_with($trim, '<!DOCTYPE')
            || str_starts_with($trim, '<html')
            || str_contains($trim, '<head>')
            || str_contains($trim, '<title>');
    }

    /**
     * @return array<int, array<int, string>>
     */
    private function splitCsvRows(string $csv): array
    {
        $rows  = [];
        $lines = preg_split('/\r\n|\r|\n/', $csv) ?: [];

        foreach ($lines as $line) {
            if (trim($line) === '') {
                continue;
            }
            $mem = fopen('php://memory', 'r+');
            fwrite($mem, $line);
            rewind($mem);
            $row = fgetcsv($mem);
            fclose($mem);

            if ($row !== false) {
                $rows[] = $row;
            }
        }

        return $rows;
    }

    /**
     * Attempt to fetch sheet names and GIDs from the public spreadsheet HTML.
     * Returns [ sheetName => gid ] pairs.
     *
     * @return array<string, int>
     */
    private function fetchSheetMeta(string $spreadsheetId): array
    {
        $url  = "https://docs.google.com/spreadsheets/d/{$spreadsheetId}/edit";
        $html = @file_get_contents($url);

        if (!$html) {
            return [];
        }

        $meta = [];
        if (preg_match_all('/"title":"([^"]+)","sheetId":(\d+)/', $html, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $m) {
                $name = stripcslashes($m[1]);
                $gid  = (int) $m[2];
                if (!isset($meta[$name])) {
                    $meta[$name] = $gid;
                }
            }
        }

        return $meta;
    }
}
