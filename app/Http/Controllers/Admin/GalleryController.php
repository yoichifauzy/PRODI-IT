<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Gallery;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class GalleryController extends Controller
{
    /**
     * Tampilkan halaman admin galeri dengan card grid.
     * Diurutkan berdasarkan position ASC, lalu id DESC.
     */
    public function index()
    {
        $galleries = Gallery::query()
            ->orderBy('position')
            ->orderByDesc('id')
            ->paginate(48);

        $categories = Gallery::query()
            ->select('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        $years = Gallery::query()
            ->select('year')
            ->distinct()
            ->orderByDesc('year')
            ->pluck('year');

        return view('admin.galleries.index', [
            'galleries'  => $galleries,
            'categories' => $categories,
            'years'      => $years,
        ]);
    }

    /**
     * Upload satu atau banyak gambar sekaligus — response JSON (AJAX).
     * Semua gambar dalam satu batch berbagi judul yang sama (tanpa suffix nomor).
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'images'   => ['required', 'array', 'max:30'],
            'images.*' => ['required', 'image', 'max:10240'],
            'category' => ['required', 'string', 'max:100'],
            'title'    => ['nullable', 'string', 'max:255'],
        ]);

        $category  = $request->input('category');
        $title     = $request->input('title') ?: $category;  // Judul sama untuk semua, tanpa suffix (2),(3)
        $year      = (int) now()->year;
        $userId    = Auth::id();
        $created   = [];

        // Ambil position maksimum yang ada, tambahkan dari sana
        $maxPos = Gallery::max('position') ?? 0;

        foreach ($request->file('images') as $i => $file) {
            $path = $file->store('galleries', 'public');

            $gallery = Gallery::create([
                'title'      => $title,          // Semua pakai judul yang sama
                'category'   => $category,
                'year'       => $year,
                'image_path' => $path,
                'position'   => $maxPos + $i + 1,
                'created_by' => $userId,
            ]);

            $created[] = [
                'id'        => $gallery->id,
                'title'     => $gallery->title,
                'category'  => $gallery->category,
                'year'      => $gallery->year,
                'position'  => $gallery->position,
                'image_url' => asset('storage/' . $gallery->image_path),
            ];
        }

        return response()->json([
            'success' => true,
            'message' => count($created) . ' gambar berhasil diunggah.',
            'images'  => $created,
        ]);
    }

    /**
     * Update judul/kategori/tahun satu item — response JSON (AJAX dari inline card).
     */
    public function update(Request $request, Gallery $gallery): JsonResponse
    {
        $data = $request->validate([
            'title'    => ['sometimes', 'string', 'max:255'],
            'category' => ['sometimes', 'string', 'max:100'],
            'year'     => ['sometimes', 'integer', 'min:2000', 'max:2099'],
        ]);

        $gallery->update($data);

        return response()->json(['success' => true]);
    }

    /**
     * Simpan urutan baru setelah drag & drop — response JSON (AJAX).
     */
    public function reorder(Request $request): JsonResponse
    {
        $request->validate([
            'ordered_ids'   => ['required', 'array'],
            'ordered_ids.*' => ['integer', 'exists:galleries,id'],
        ]);

        foreach ($request->input('ordered_ids') as $pos => $id) {
            Gallery::where('id', $id)->update(['position' => $pos + 1]);
        }

        return response()->json(['success' => true, 'message' => 'Urutan berhasil disimpan.']);
    }

    /**
     * Hapus satu item — response JSON (AJAX).
     */
    public function destroy(Gallery $gallery): JsonResponse
    {
        if ($gallery->image_path && Storage::disk('public')->exists($gallery->image_path)) {
            Storage::disk('public')->delete($gallery->image_path);
        }

        $gallery->delete();

        return response()->json(['success' => true, 'message' => 'Gambar berhasil dihapus.']);
    }
}
