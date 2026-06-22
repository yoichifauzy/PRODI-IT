<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Curriculum;
use App\Models\CurriculumCourse;
use App\Models\CurriculumDraft;
use App\Models\CurriculumCourseDraft;
use App\Models\Setting;
use App\Services\CurriculumImportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CurriculumImportController extends Controller
{
    protected CurriculumImportService $service;

    private const DEFAULT_SHEET_URL = 'https://docs.google.com/spreadsheets/d/1d-MGrDU54pP-0uUyl5Txd4S3fV1ukCMqMUtsoxpZ9NM/edit?usp=sharing';

    public function __construct(CurriculumImportService $service)
    {
        $this->service = $service;
    }

    public function updateLink(Request $request)
    {
        $validated = $request->validate([
            'sheet_url' => 'required|url',
        ]);

        $sheetUrl = $validated['sheet_url'];

        Setting::query()->updateOrCreate(
            ['key' => 'curriculum_sheet_url'],
            ['value' => $sheetUrl, 'type' => 'string', 'group' => 'curriculum']
        );

        return redirect()->route('admin.curricula.index')->with('success', 'Link spreadsheet berhasil disimpan.');
    }

    public function syncNow()
    {
        $setting = Setting::query()->firstOrCreate(
            ['key' => 'curriculum_sheet_url'],
            ['value' => self::DEFAULT_SHEET_URL, 'type' => 'string', 'group' => 'curriculum']
        );

        $sheetUrl = (string) ($setting->value ?: self::DEFAULT_SHEET_URL);

        try {
            $data = $this->service->parseGoogleSheetPublic($sheetUrl);
            $this->importData($data, (int) Auth::id());
        } catch (\Exception $e) {
            return back()->withErrors(['sheet_url' => $e->getMessage()]);
        }

        return redirect()->route('admin.curricula.index')->with('success', 'Sinkronisasi selesai.');
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

        return redirect()->route('admin.curricula.index')->with('success', 'Upload berhasil dan data disinkronkan.');
    }

    public function download()
    {
        // simple CSV export: first section kurikulum then matakuliah
        $filename = 'kurikulum_matakuliah_export_' . date('Ymd_His') . '.csv';

        $handle = fopen('php://memory', 'w');
        // curricula
        fputcsv($handle, ['---KURIKULUM---']);
        fputcsv($handle, ['Nama Kurikulum']);
        foreach (Curriculum::all() as $c) {
            fputcsv($handle, [$c->name]);
        }

        // separator
        fputcsv($handle, []);

        // matakuliah
        fputcsv($handle, ['---MATAKULIAH---']);
        fputcsv($handle, ['Nama Kurikulum', 'Kode', 'Nama', 'SKS Teori', 'SKS Praktek']);
        foreach (CurriculumCourse::with('curriculum')->get() as $m) {
            $theory = (int) ($m->credits_theory ?? $m->credits ?? 0);
            $praktek = (int) ($m->credits_practice ?? 0);
            fputcsv($handle, [$m->curriculum->name ?? '', $m->code, $m->name, $theory, $praktek]);
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
            DB::table('curriculum_course_drafts')->delete();
            DB::table('curricula_drafts')->delete();

            $curriculumNames = array_values(array_unique(array_filter(array_map('trim', $data['kurikulum'] ?? []))));
            $curriculaByName = [];

            foreach ($curriculumNames as $name) {
                $curriculum = CurriculumDraft::create(['name' => $name, 'created_by' => $createdBy]);
                $curriculaByName[$name] = $curriculum;
            }

            foreach ($data['matakuliah'] ?? [] as $row) {
                $currName = trim($row['Nama Kurikulum'] ?? '');
                if ($currName === '') {
                    continue;
                }

                $code = trim($row['Kode'] ?? '');
                $name = trim($row['Nama'] ?? '');
                if ($code === '' || $name === '') {
                    continue;
                }

                $sksTheory = intval($row['SKS Teori'] ?? 0);
                $sksPraktek = intval($row['SKS Praktek'] ?? 0);
                $credits = $sksTheory + $sksPraktek;

                if (! isset($curriculaByName[$currName])) {
                    $curriculaByName[$currName] = CurriculumDraft::create(['name' => $currName, 'created_by' => $createdBy]);
                }

                CurriculumCourseDraft::create([
                    'curriculum_draft_id' => $curriculaByName[$currName]->id,
                    'code' => $code,
                    'name' => $name,
                    'credits_theory' => $sksTheory,
                    'credits_practice' => $sksPraktek,
                    'credits' => $credits,
                ]);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function syncValidate()
    {
        DB::beginTransaction();
        try {
            // Delete old public data
            DB::table('curriculum_courses')->delete();
            DB::table('curricula')->delete();

            // Copy drafts to public
            $drafts = CurriculumDraft::with('courses')->get();
            foreach ($drafts as $draft) {
                $curriculum = Curriculum::create([
                    'name' => $draft->name,
                    'description' => $draft->description,
                    'created_by' => $draft->created_by,
                ]);

                foreach ($draft->courses as $courseDraft) {
                    CurriculumCourse::create([
                        'curriculum_id' => $curriculum->id,
                        'code' => $courseDraft->code,
                        'name' => $courseDraft->name,
                        'credits_theory' => $courseDraft->credits_theory,
                        'credits_practice' => $courseDraft->credits_practice,
                        'credits' => $courseDraft->credits,
                        'short_syllabus' => $courseDraft->short_syllabus,
                        'sort_order' => $courseDraft->sort_order,
                    ]);
                }
            }

            // Delete drafts after validation
            DB::table('curriculum_course_drafts')->delete();
            DB::table('curricula_drafts')->delete();

            DB::commit();
            return redirect()->route('admin.curricula.index')->with('success', 'Data kurikulum draft berhasil dipublikasikan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal mempublikasikan data: ' . $e->getMessage());
        }
    }

    public function syncDiscard()
    {
        DB::beginTransaction();
        try {
            DB::table('curriculum_course_drafts')->delete();
            DB::table('curricula_drafts')->delete();
            DB::commit();
            return redirect()->route('admin.curricula.index')->with('success', 'Perubahan draft berhasil dibatalkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal membatalkan perubahan: ' . $e->getMessage());
        }
    }
}
