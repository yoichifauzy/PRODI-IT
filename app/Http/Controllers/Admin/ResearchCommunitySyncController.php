<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\AssetLink;
use App\Models\CommunityService;
use App\Models\Research;
use App\Services\SheetSyncService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ResearchCommunitySyncController extends Controller
{
    public function __construct(private SheetSyncService $sheet) {}

    public function index(Request $request)
    {
        $docs = AssetLink::orderBy('name')->get();
        $defaultDoc = AssetLink::where('name', 'penelitian-pengabdian')->first();
        $search = trim((string) $request->query('q', ''));

        // Hapus ->paginate(10) dan ganti dengan ->get()
        $researches = Research::query()
            ->when($search !== '', fn($q) => $q->where('title', 'like', "%{$search}%")->orWhere('researcher_name', 'like', "%{$search}%"))
            ->orderByDesc('year')->orderBy('title')
            ->get(); // <-- Pake get() biar semua data masuk ke array JS
                
        $communityServices = CommunityService::query()
            ->when($search !== '', fn($q) => $q->where('title', 'like', "%{$search}%")->orWhere('location', 'like', "%{$search}%"))
            ->orderByDesc('year')->orderBy('title')
            ->get(); // <-- Pake get()

        return view('admin.research-community.index', compact('docs', 'defaultDoc', 'researches', 'communityServices', 'search'));
    }

    public function sync(Request $request)
    {
        $request->validate(['document_id' => 'required|exists:asset_links,id']);
        $doc = AssetLink::find($request->document_id);

        try {
            $spreadsheetId = $this->sheet->extractId($doc->url);
            // Tab "penelitian": A=Tahun, B=Judul Penelitian, C=Peneliti
            $penelitianCsv = $this->sheet->fetchSheetByName($spreadsheetId, 'penelitian');
            $penelitianRows = $this->sheet->parseCsv($penelitianCsv, [0, 1, 2]);

            // Tab "pengabdian": A=Tahun, B=Judul, C=Lokasi
            $pengabdianCsv = $this->sheet->fetchSheetByName($spreadsheetId, 'pengabdian');
            $pengabdianRows = $this->sheet->parseCsv($pengabdianCsv, [0, 1, 2]);
            
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }

        DB::beginTransaction();
        try {
            Research::truncate();
            CommunityService::truncate();

            $now    = now();
            $userId = Auth::id();

            $researchInserts = [];
            foreach ($penelitianRows as $row) {
                // A=0 Tahun, B=1 Judul, C=2 Peneliti
                if ((int)$row[0] <= 0 || $row[1] === '') continue;
                $researchInserts[] = [
                    'document_id'     => $doc->id,
                    'year'            => (int) $row[0],
                    'title'           => $row[1],
                    'researcher_name' => $row[2] ?? '',
                    'created_by'      => $userId,
                    'created_at'      => $now,
                    'updated_at'      => $now,
                ];
            }

            $communityInserts = [];
            foreach ($pengabdianRows as $row) {
                // A=0 Tahun, B=1 Judul, C=2 Lokasi
                if ((int)$row[0] <= 0 || $row[1] === '') continue;
                $communityInserts[] = [
                    'document_id' => $doc->id,
                    'year'        => (int) $row[0],
                    'title'       => $row[1],
                    'location'    => $row[2] ?? null,
                    'created_by'  => $userId,
                    'created_at'  => $now,
                    'updated_at'  => $now,
                ];
            }
                
            if (!empty($researchInserts)) {
                foreach (array_chunk($researchInserts, 500) as $chunk) {
                    Research::insert($chunk);
                }
            }
            if (!empty($communityInserts)) {
                foreach (array_chunk($communityInserts, 500) as $chunk) {
                    CommunityService::insert($chunk);
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyimpan: ' . $e->getMessage());
        }

        $total = count($researchInserts) + count($communityInserts);
        return back()->with('success', "Sync berhasil. {$total} data disimpan (" . count($researchInserts) . " penelitian, " . count($communityInserts) . " pengabdian).");
    }
}
