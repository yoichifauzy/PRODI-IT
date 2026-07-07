<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BannerController extends Controller
{
    /**
     * Tampilkan daftar banner hero.
     */
    public function index(): View
    {
        $banners = Banner::where('category', 'hero')
            ->orderBy('position', 'asc')
            ->orderBy('id', 'desc')
            ->get();

        return view('admin.banners.index', [
            'banners' => $banners,
        ]);
    }

    /**
     * Simpan banner baru.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'image' => ['required', 'image', 'max:10240'],
        ]);

        $imagePath = $request->file('image')?->store('banners', 'public');
        
        $maxPosition = Banner::where('category', 'hero')->max('position') ?? 0;

        Banner::create([
            'category' => 'hero',
            'image_path' => $imagePath,
            'position' => $maxPosition + 1,
            'created_by' => $request->user()?->id ?? null,
        ]);

        return redirect()
            ->route('admin.banners.index')
            ->with('success', 'Banner berhasil ditambahkan.');
    }

    /**
     * Update banner yang ada (hanya ganti gambar).
     */
    public function update(Request $request, Banner $banner): RedirectResponse
    {
        $request->validate([
            'image' => ['required', 'image', 'max:10240'],
        ]);

        if ($request->hasFile('image')) {
            if ($banner->image_path && Storage::disk('public')->exists($banner->image_path)) {
                Storage::disk('public')->delete($banner->image_path);
            }

            $imagePath = $request->file('image')?->store('banners', 'public');
            $banner->update([
                'image_path' => $imagePath,
            ]);
        }

        return redirect()
            ->route('admin.banners.index')
            ->with('success', 'Banner berhasil diperbarui.');
    }

    /**
     * Hapus banner.
     */
    public function destroy(Banner $banner): RedirectResponse
    {
        if ($banner->image_path && Storage::disk('public')->exists($banner->image_path)) {
            Storage::disk('public')->delete($banner->image_path);
        }

        $banner->delete();

        return redirect()
            ->route('admin.banners.index')
            ->with('success', 'Banner berhasil dihapus.');
    }

    /**
     * Update posisi banner via Drag and Drop (AJAX).
     */
    public function reorder(Request $request): JsonResponse
    {
        $request->validate([
            'ordered_ids' => ['required', 'array'],
            'ordered_ids.*' => ['integer', 'exists:banners,id'],
        ]);

        $orderedIds = $request->input('ordered_ids');
        
        foreach ($orderedIds as $index => $id) {
            Banner::where('id', $id)->update(['position' => $index + 1]);
        }

        return response()->json(['success' => true, 'message' => 'Posisi berhasil diperbarui.']);
    }
}
