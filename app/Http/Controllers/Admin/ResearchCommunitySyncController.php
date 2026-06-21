<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CommunityService;
use App\Models\Research;
use App\Models\Setting;
use App\Services\ResearchCommunityImportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ResearchCommunitySyncController extends Controller
{
    private const DEFAULT_SHEET_URL = 'https://docs.google.com/spreadsheets/d/1q-lc6xKs0R8tLbldCDEfPAVrXkqh9d-9lg9R-KSfCJQ/edit?usp=sharing';

    public function __construct(private ResearchCommunityImportService $service) {}

    public function index()
    {
        $setting = Setting::query()->firstOrCreate(
            ['key' => 'research_community_sheet_url'],
            ['value' => self::DEFAULT_SHEET_URL, 'type' => 'string', 'group' => 'research_community']
        );

        $sheetUrl = (string) ($setting->value ?: self::DEFAULT_SHEET_URL);

        $researches = Research::query()->orderByDesc('year')->orderBy('title')->get();
        $communityServices = CommunityService::query()->orderByDesc('activity_date')->orderBy('title')->get();

        return view('admin.research-community.index', [
            'sheetUrl' => $sheetUrl,
            'researches' => $researches,
            'communityServices' => $communityServices,
        ]);
    }

    public function updateLink(Request $request)
    {
        $validated = $request->validate([
            'sheet_url' => 'required|url',
        ]);

        Setting::query()->updateOrCreate(
            ['key' => 'research_community_sheet_url'],
            ['value' => $validated['sheet_url'], 'type' => 'string', 'group' => 'research_community']
        );

        return redirect()->route('admin.research-community.index')->with('success', 'Link spreadsheet berhasil disimpan.');
    }

    public function syncNow()
    {
        $setting = Setting::query()->firstOrCreate(
            ['key' => 'research_community_sheet_url'],
            ['value' => self::DEFAULT_SHEET_URL, 'type' => 'string', 'group' => 'research_community']
        );

        $sheetUrl = (string) ($setting->value ?: self::DEFAULT_SHEET_URL);

        try {
            $data = $this->service->parseGoogleSheetPublic($sheetUrl);
            $this->importData($data, (int) Auth::id());
        } catch (\Exception $e) {
            return back()->withErrors(['sheet_url' => $e->getMessage()]);
        }

        return redirect()->route('admin.research-community.index')->with('success', 'Sinkronisasi selesai.');
    }

    public function upload(Request $request)
    {
        $request->validate(['file' => 'required|file']);

        try {
            $data = $this->service->parseUploadedFile($request->file('file'));
            $this->importData($data, (int) Auth::id());
        } catch (\Exception $e) {
            return back()->withErrors(['file' => $e->getMessage()]);
        }

        return redirect()->route('admin.research-community.index')->with('success', 'Upload berhasil dan data disinkronkan.');
    }

    public function download()
    {
        $filename = 'penelitian_pengabdian_export_' . date('Ymd_His') . '.csv';

        $handle = fopen('php://memory', 'w');
        fputcsv($handle, ['---PENELITIAN---']);
        fputcsv($handle, ['Tahun', 'Judul Penelitian', 'Peneliti']);
        foreach (Research::query()->orderByDesc('year')->get() as $row) {
            fputcsv($handle, [$row->year, $row->title, $row->researcher_name]);
        }

        fputcsv($handle, []);

        fputcsv($handle, ['---PENGABDIAN---']);
        fputcsv($handle, ['Tahun', 'Nama Program', 'Lokasi']);
        foreach (CommunityService::query()->orderByDesc('activity_date')->get() as $row) {
            $year = $row->activity_date ? $row->activity_date->format('Y') : '';
            fputcsv($handle, [$year, $row->title, $row->location]);
        }

        rewind($handle);
        $content = stream_get_contents($handle);
        fclose($handle);

        return response($content, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    private function importData(array $data, int $createdBy): void
    {
        DB::beginTransaction();
        try {
            DB::table('researches')->delete();
            DB::table('community_services')->delete();

            foreach ($data['penelitian'] ?? [] as $row) {
                $year = (int) ($row['Tahun'] ?? 0);
                $title = trim((string) ($row['Judul Penelitian'] ?? ''));
                $researcher = trim((string) ($row['Peneliti'] ?? ''));

                if ($year <= 0 || $title === '' || $researcher === '') {
                    continue;
                }

                Research::create([
                    'title' => $title,
                    'researcher_name' => $researcher,
                    'researcher_role' => 'dosen',
                    'year' => $year,
                    'status' => 'published',
                    'created_by' => $createdBy,
                ]);
            }

            foreach ($data['pengabdian'] ?? [] as $row) {
                $year = (int) ($row['Tahun'] ?? 0);
                $program = trim((string) ($row['Nama Program'] ?? ''));
                $location = trim((string) ($row['Lokasi'] ?? ''));

                if ($year <= 0 || $program === '') {
                    continue;
                }

                CommunityService::create([
                    'title' => $program,
                    'activity_date' => $year . '-01-01',
                    'location' => $location,
                    'status' => 'published',
                    'created_by' => $createdBy,
                ]);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
