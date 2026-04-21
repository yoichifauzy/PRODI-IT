<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HeroSlide;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class HeroSlideController extends Controller
{
    public function index(): View
    {
        $heroSlides = HeroSlide::query()
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->paginate(12)
            ->withQueryString();

        return view('admin.hero-slides.index', [
            'heroSlides' => $heroSlides,
        ]);
    }

    public function create(): View
    {
        return view('admin.hero-slides.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['nullable', 'string', 'max:255'],
            'subtitle' => ['nullable', 'string', 'max:255'],
            'image' => ['required', 'image', 'max:5120'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'is_active' => ['nullable', 'boolean'],
            'start_at' => ['nullable', 'date'],
            'end_at' => ['nullable', 'date', 'after_or_equal:start_at'],
        ]);

        $validated['image_path'] = $request->file('image')?->store('hero-slides', 'public');
        $validated['sort_order'] = (int) ($validated['sort_order'] ?? 0);
        $validated['is_active'] = (bool) ($validated['is_active'] ?? false);

        unset($validated['image']);

        HeroSlide::query()->create($validated);

        return redirect()
            ->route('admin.hero-slides.index')
            ->with('success', 'Slide hero berhasil ditambahkan.');
    }

    public function show(HeroSlide $heroSlide): RedirectResponse
    {
        return redirect()->route('admin.hero-slides.edit', $heroSlide);
    }

    public function edit(HeroSlide $heroSlide): View
    {
        return view('admin.hero-slides.edit', [
            'heroSlide' => $heroSlide,
        ]);
    }

    public function update(Request $request, HeroSlide $heroSlide): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['nullable', 'string', 'max:255'],
            'subtitle' => ['nullable', 'string', 'max:255'],
            'image' => ['nullable', 'image', 'max:5120'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'is_active' => ['nullable', 'boolean'],
            'start_at' => ['nullable', 'date'],
            'end_at' => ['nullable', 'date', 'after_or_equal:start_at'],
        ]);

        if ($request->hasFile('image')) {
            if ($heroSlide->image_path !== null && Storage::disk('public')->exists($heroSlide->image_path)) {
                Storage::disk('public')->delete($heroSlide->image_path);
            }

            $validated['image_path'] = $request->file('image')?->store('hero-slides', 'public');
        }

        $validated['sort_order'] = (int) ($validated['sort_order'] ?? 0);
        $validated['is_active'] = (bool) ($validated['is_active'] ?? false);

        unset($validated['image']);

        $heroSlide->update($validated);

        return redirect()
            ->route('admin.hero-slides.index')
            ->with('success', 'Slide hero berhasil diperbarui.');
    }

    public function destroy(HeroSlide $heroSlide): RedirectResponse
    {
        if ($heroSlide->image_path !== null && Storage::disk('public')->exists($heroSlide->image_path)) {
            Storage::disk('public')->delete($heroSlide->image_path);
        }

        $heroSlide->delete();

        return redirect()
            ->route('admin.hero-slides.index')
            ->with('success', 'Slide hero berhasil dihapus.');
    }
}
