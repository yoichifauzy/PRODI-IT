<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ImportSheet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SheetController extends Controller
{
    public function index()
    {
        $sheets = ImportSheet::orderBy('created_at', 'desc')->get();
        return view('admin.sheets.index', compact('sheets'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:file,url',
            'file' => 'required_if:type,file|mimes:xlsx,xls,csv',
            'url' => 'required_if:type,url|url'
        ]);

        $data = [
            'name' => $request->name,
            'type' => $request->type,
            'is_active' => false // Default false
        ];

        if ($request->type === 'file' && $request->hasFile('file')) {
            // Simpan file ke storage/app/imports
            $path = $request->file('file')->store('imports');
            $data['file_path'] = $path;
        } else {
            $data['url'] = $request->url;
        }

        ImportSheet::create($data);
        return back()->with('success', 'Data Sheet berhasil ditambahkan.');
    }

    public function setActive(ImportSheet $sheet)
    {
        // Nonaktifkan semua, lalu aktifkan yang dipilih
        ImportSheet::query()->update(['is_active' => false]);
        $sheet->update(['is_active' => true]);

        return back()->with('success', "Sheet '{$sheet->name}' sekarang aktif digunakan.");
    }

    public function destroy(ImportSheet $sheet)
    {
        if ($sheet->file_path) {
            Storage::delete($sheet->file_path);
        }
        $sheet->delete();
        return back()->with('success', 'Sheet berhasil dihapus.');
    }
}