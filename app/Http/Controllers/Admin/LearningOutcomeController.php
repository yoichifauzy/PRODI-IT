<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AssetLink;
use App\Models\LearningOutcome;
use App\Services\SheetSyncService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LearningOutcomeController extends Controller
{
    public function __construct(private SheetSyncService $sheet) {}

    public function index(Request $request)
    {
        $docs       = AssetLink::orderBy('name')->get();
        $defaultDoc = AssetLink::where('name', 'capaian-pembelajaran')->first();
        $search     = trim((string) $request->query('q', ''));

        $outcomes = LearningOutcome::query()
            ->when($search !== '', fn($q) => $q
                ->where('code', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%")
            )
            ->orderBy('code')
            ->get();

        return view('admin.learning-outcomes.index', compact('docs', 'defaultDoc', 'outcomes', 'search'));
    }

    public function sync(Request $request)
    {
        $request->validate(['document_id' => 'required|exists:asset_links,id']);
        $doc = AssetLink::find($request->document_id);

        try {
            $spreadsheetId = $this->sheet->extractId($doc->url);
            // Sheet pertama (gid=0) — kolom A=Kode, B=Deskripsi, baris pertama dilewati (header)
            $csv  = $this->sheet->fetchSheetByGid($spreadsheetId, 0);
            $rows = $this->sheet->parseCsv($csv, [0, 1]);
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }

        DB::beginTransaction();
        try {
            LearningOutcome::truncate();

            $now    = now();
            $userId = Auth::id();
            $inserts = [];

            foreach ($rows as $row) {
                $code = trim($row[0] ?? '');
                $desc = trim($row[1] ?? '');
                if ($code === '' || $desc === '') continue;

                $inserts[] = [
                    'code'        => $code,
                    'description' => $desc,
                    'created_by'  => $userId,
                    'created_at'  => $now,
                    'updated_at'  => $now,
                ];
            }

            if (!empty($inserts)) {
                foreach (array_chunk($inserts, 500) as $chunk) {
                    LearningOutcome::insert($chunk);
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyimpan: ' . $e->getMessage());
        }

        return back()->with('success', 'Sync berhasil. ' . count($inserts) . ' CPL disimpan.');
    }
}
