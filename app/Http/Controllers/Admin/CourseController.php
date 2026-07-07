<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\AssetLink;
use App\Models\Course;
use App\Services\SheetSyncService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CourseController extends Controller
{
    public function __construct(private SheetSyncService $sheet) {}

    public function index(Request $request)
    {
        $docs = AssetLink::orderBy('name')->get();
        $defaultDoc = AssetLink::where('name', 'kurikulum')->first(); // sesuaikan nama doc
        $search = trim((string) $request->query('q', ''));

        $courses = Course::query()
            ->when($search !== '', fn($q) => $q->where('code', 'like', "%{$search}%")->orWhere('name', 'like', "%{$search}%"))
            ->orderBy('semester')
            ->orderBy('code')
            ->get();

        return view('admin.courses.index', compact('docs', 'defaultDoc', 'courses', 'search'));
    }

    public function sync(Request $request)
    {
        $request->validate(['document_id' => 'required|exists:asset_links,id']);
        $doc = AssetLink::find($request->document_id);

        try {
            $spreadsheetId = $this->sheet->extractId($doc->url);
            $csv           = $this->sheet->fetchSheetByGid($spreadsheetId, 0);
            $rows          = $this->sheet->parseCsv($csv, [0, 2, 3]); // Semester, Kode MK, Nama MK wajib
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }

        DB::beginTransaction();
        try {
            Course::truncate();

            $now     = now();
            $userId  = Auth::id();
            $inserts = [];

            foreach ($rows as $row) {
                // A=0 Semester, B=1 Jurusan (optional), C=2 Kode, D=3 Nama, E=4 SKS Teori, F=5 SKS Praktik
                $inserts[] = [
                    'document_id'      => $doc->id,
                    'semester'         => $row[0],
                    'major_selection'  => $row[1] !== '' ? $row[1] : null,
                    'code'             => $row[2],
                    'name'             => $row[3],
                    'credits_theory'   => (int) ($row[4] ?? 0),
                    'credits_practice' => (int) ($row[5] ?? 0),
                    'created_by'       => $userId,
                    'created_at'       => $now,
                    'updated_at'       => $now,
                ];
            }

            if (!empty($inserts)) {
                foreach (array_chunk($inserts, 500) as $chunk) {
                    Course::insert($chunk);
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyimpan: ' . $e->getMessage());
        }

        return back()->with('success', 'Sync berhasil. ' . count($inserts) . ' mata kuliah disimpan.');
    }
}
