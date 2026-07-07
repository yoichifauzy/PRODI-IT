<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\AssetLink;
use App\Models\Banner;
use App\Models\TracerAlumni;
use App\Services\SheetSyncService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class TracerAlumniController extends Controller
{
    public function __construct(private SheetSyncService $sheet) {}

    public function index(Request $request)
{
    $docs = AssetLink::orderBy('name')->get();
    $defaultDoc = AssetLink::where('name', 'tracer-alumni')->first(); // sesuaikan

    $alumni = TracerAlumni::query()
        ->orderByDesc('graduation_year')
        ->orderBy('name')
        ->get();

    $banner = Banner::where('category', 'alumni')->first();

    $years = TracerAlumni::query()
        ->select('graduation_year')
        ->distinct()
        ->orderByDesc('graduation_year')
        ->pluck('graduation_year');

    return view('admin.tracer-alumni.index', compact('docs', 'defaultDoc', 'alumni', 'years', 'banner'));
}

    public function sync(Request $request)
    {
        $request->validate(['document_id' => 'required|exists:asset_links,id']);
        $doc = AssetLink::find($request->document_id);

        try {
            $spreadsheetId = $this->sheet->extractId($doc->url);
            // Read ALL sheets in the file; columns A-G required cols: A(0)=tahun, B(1)=NIM
            $allSheets = $this->sheet->parseAllSheets($spreadsheetId, [0, 1]);
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }

        DB::beginTransaction();
        try {
            TracerAlumni::truncate();

            $now     = now();
            $userId  = Auth::id();
            $inserts = [];

            foreach ($allSheets as $sheetName => $rows) {
                foreach ($rows as $row) {
                    // A=0 Lulusan Tahun, B=1 NIM, C=2 Nama, D=3 Penempatan/Company, E=4 Departemen, F=5 Kesesuaian, G=6 Contact
                    $year = (int) ($row[0] ?? 0);
                    $nim  = $row[1] ?? '';
                    if ($year <= 0 || $nim === '') continue;

                    $inserts[] = [
                        'document_id'     => $doc->id,
                        'graduation_year' => $year,
                        'nim'             => $nim,
                        'name'            => $row[2] ?? '',
                        'company_name'    => $row[3] ?? '',
                        'department'      => $row[4] ?? '',
                        'relevance'       => $row[5] ?? '',
                        'contact'         => $row[6] ?? null,
                        'created_by'      => $userId,
                        'created_at'      => $now,
                        'updated_at'      => $now,
                    ];
                }
            }

            if (!empty($inserts)) {
                foreach (array_chunk($inserts, 500) as $chunk) {
                    TracerAlumni::insert($chunk);
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyimpan: ' . $e->getMessage());
        }

        return back()->with('success', 'Sync berhasil. ' . count($inserts) . ' data alumni disimpan.');
    }

    public function destroy(TracerAlumni $tracerAlumni)
    {
        $tracerAlumni->delete();
        return back()->with('success', 'Data berhasil dihapus.');
    }

    public function updateBanner(Request $request)
    {
        $request->validate([
            'image' => ['required', 'image', 'max:10240'],
        ]);

        $banner = Banner::where('category', 'alumni')->first();

        if ($banner && $banner->image_path && Storage::disk('public')->exists($banner->image_path)) {
            Storage::disk('public')->delete($banner->image_path);
        }

        $imagePath = $request->file('image')->store('banners/alumni', 'public');

        if ($banner) {
            $banner->update(['image_path' => $imagePath, 'updated_by' => Auth::id()]);
        } else {
            Banner::create([
                'category' => 'alumni',
                'image_path' => $imagePath,
                'position' => 1,
                'created_by' => Auth::id(),
            ]);
        }

        return back()->with('success', 'Banner Alumni berhasil diperbarui.');
    }
}
