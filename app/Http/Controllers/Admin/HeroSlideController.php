<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class HeroSlideController extends Controller
{
    /**
     * Tampilkan daftar banner hero.
     */
    public function index(): View
    {
        $heroSlides = Banner::where('category', 'hero')
            ->orderBy('position', 'asc')
            ->orderBy('id', 'desc')
            ->get();

        return view('admin.hero-slides.index', [
            'heroSlides' => $heroSlides,
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

        $imagePath = $request->file('image')?->store('banners/hero', 'public');
        
        $maxPosition = Banner::where('category', 'hero')->max('position') ?? 0;

        Banner::create([
            'category' => 'hero',
            'image_path' => $imagePath,
            'position' => $maxPosition + 1,
            'created_by' => $request->user()?->id ?? null,
        ]);

        return redirect()
            ->route('admin.hero-slides.index')
            ->with('success', 'Slide Hero berhasil ditambahkan.');
    }

    /**
     * Update banner yang ada (hanya ganti gambar).
     */
    public function update(Request $request, $id): RedirectResponse
    {
        $hero_slide = Banner::findOrFail($id);
        
        // UBAH 'required' MENJADI 'nullable' DI SINI
        $request->validate([
            'image' => ['nullable', 'image', 'max:10240'],
        ]);

        // Logika kamu di bawah ini sudah aman
        if ($request->hasFile('image')) {
            if ($hero_slide->image_path && Storage::disk('public')->exists($hero_slide->image_path)) {
                Storage::disk('public')->delete($hero_slide->image_path);
            }

            $imagePath = $request->file('image')?->store('banners/hero', 'public');
            $hero_slide->update([
                'image_path' => $imagePath,
            ]);
        }

        return redirect()
            ->route('admin.hero-slides.index')
            ->with('success', 'Slide Hero berhasil diperbarui.');
    }

    /**
     * Hapus banner.
     */
    public function destroy($id): RedirectResponse
    {
        $hero_slide = Banner::findOrFail($id);

        if ($hero_slide->image_path && Storage::disk('public')->exists($hero_slide->image_path)) {
            Storage::disk('public')->delete($hero_slide->image_path);
        }

        $hero_slide->delete();

        return redirect()
            ->route('admin.hero-slides.index')
            ->with('success', 'Slide Hero berhasil dihapus.');
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
            Banner::where('id', $id)->where('category', 'hero')->update(['position' => $index + 1]);
        }

        return response()->json(['success' => true, 'message' => 'Posisi berhasil diperbarui.']);
    }
}
