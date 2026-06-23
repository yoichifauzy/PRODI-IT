<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CommunityService;
use App\Models\CommunityServiceDraft;
use App\Models\Research;
use App\Models\ResearchDraft;
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

        $draftCount = ResearchDraft::count() + CommunityServiceDraft::count();
        $isDraftMode = $draftCount > 0;

        if ($isDraftMode) {
            $researches = ResearchDraft::query()->orderByDesc('year')->orderBy('title')->get();
            $communityServices = CommunityServiceDraft::query()->orderByDesc('activity_date')->orderBy('title')->get();
            $this->markResearchPreviewStatuses($researches);
            $this->markCommunityPreviewStatuses($communityServices);
        } else {
            $researches = Research::query()->orderByDesc('year')->orderBy('title')->get();
            $communityServices = CommunityService::query()->orderByDesc('activity_date')->orderBy('title')->get();
            $researches->each->setAttribute('admin_sync_status', 'published');
            $communityServices->each->setAttribute('admin_sync_status', 'published');
        }

        return view('admin.research-community.index', [
            'sheetUrl' => $sheetUrl,
            'researches' => $researches,
            'communityServices' => $communityServices,
            'isDraftMode' => $isDraftMode,
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
            DB::table('researches_drafts')->delete();
            DB::table('community_services_drafts')->delete();

            foreach ($data['penelitian'] ?? [] as $row) {
                $year = (int) ($row['Tahun'] ?? 0);
                $title = trim((string) ($row['Judul Penelitian'] ?? ''));
                $researcher = trim((string) ($row['Peneliti'] ?? ''));

                if ($year <= 0 || $title === '' || $researcher === '') {
                    continue;
                }

                ResearchDraft::create([
                    'title' => $title,
                    'researcher_name' => $researcher,
                    'researcher_role' => 'dosen',
                    'year' => $year,
                    'status' => 'published', // or draft, since it is in drafts table it doesn't matter
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

                CommunityServiceDraft::create([
                    'title' => $program,
                    'activity_date' => $year . '-01-01',
                    'location' => $location,
                    'status' => 'published', // or draft
                    'created_by' => $createdBy,
                ]);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    private function markResearchPreviewStatuses($researches): void
    {
        $publishedKeys = Research::query()
            ->get(['title', 'researcher_name', 'year'])
            ->mapWithKeys(fn(Research $research) => [$this->researchPreviewKey($research->title, $research->researcher_name, (int) $research->year) => true]);

        $researches->each(function (ResearchDraft $draft) use ($publishedKeys): void {
            $key = $this->researchPreviewKey($draft->title, $draft->researcher_name, (int) $draft->year);
            $draft->setAttribute('admin_sync_status', $publishedKeys->has($key) ? 'published' : 'draft');
        });
    }

    private function markCommunityPreviewStatuses($communityServices): void
    {
        $publishedKeys = CommunityService::query()
            ->get(['title', 'location', 'activity_date'])
            ->mapWithKeys(function (CommunityService $service) {
                return [$this->communityPreviewKey($service->title, $service->location, $service->activity_date?->format('Y-m-d')) => true];
            });

        $communityServices->each(function (CommunityServiceDraft $draft) use ($publishedKeys): void {
            $key = $this->communityPreviewKey($draft->title, $draft->location, $draft->activity_date?->format('Y-m-d'));
            $draft->setAttribute('admin_sync_status', $publishedKeys->has($key) ? 'published' : 'draft');
        });
    }

    private function researchPreviewKey(?string $title, ?string $researcher, int $year): string
    {
        return implode('|', [$year, $this->normalizePreviewValue($title), $this->normalizePreviewValue($researcher)]);
    }

    private function communityPreviewKey(?string $title, ?string $location, ?string $activityDate): string
    {
        return implode('|', [$activityDate ?: '', $this->normalizePreviewValue($title), $this->normalizePreviewValue($location)]);
    }

    private function normalizePreviewValue(?string $value): string
    {
        return mb_strtolower(trim(preg_replace('/\s+/', ' ', (string) $value)));
    }

    public function syncValidate()
    {
        DB::beginTransaction();
        try {
            DB::table('researches')->delete();
            DB::table('community_services')->delete();

            $researchDrafts = ResearchDraft::all();
            foreach ($researchDrafts as $draft) {
                Research::create([
                    'title' => $draft->title,
                    'researcher_name' => $draft->researcher_name,
                    'researcher_role' => $draft->researcher_role,
                    'year' => $draft->year,
                    'publication' => $draft->publication,
                    'link' => $draft->link,
                    'abstract' => $draft->abstract,
                    'status' => $draft->status,
                    'created_by' => $draft->created_by,
                ]);
            }

            $communityDrafts = CommunityServiceDraft::all();
            foreach ($communityDrafts as $draft) {
                CommunityService::create([
                    'title' => $draft->title,
                    'activity_date' => $draft->activity_date,
                    'location' => $draft->location,
                    'organizer' => $draft->organizer,
                    'summary' => $draft->summary,
                    'documentation_cover' => $draft->documentation_cover,
                    'status' => $draft->status,
                    'created_by' => $draft->created_by,
                ]);
            }

            DB::table('researches_drafts')->delete();
            DB::table('community_services_drafts')->delete();

            DB::commit();
            return redirect()->route('admin.research-community.index')->with('success', 'Data penelitian dan pengabdian berhasil dipublikasikan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal mempublikasikan data: ' . $e->getMessage());
        }
    }

    public function syncDiscard()
    {
        DB::beginTransaction();
        try {
            DB::table('researches_drafts')->delete();
            DB::table('community_services_drafts')->delete();
            DB::commit();
            return redirect()->route('admin.research-community.index')->with('success', 'Perubahan draft berhasil dibatalkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal membatalkan perubahan: ' . $e->getMessage());
        }
    }
}
