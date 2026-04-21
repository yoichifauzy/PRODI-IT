<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Gallery;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class GalleryController extends Controller
{
    public function index(): View
    {
        $galleries = Gallery::query()
            ->withCount('items')
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('admin.galleries.index', [
            'galleries' => $galleries,
        ]);
    }

    public function create(): View
    {
        return view('admin.galleries.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $payload = $this->validatePayload($request);
        $payload['slug'] = $this->generateUniqueSlug(
            (string) ($payload['slug'] ?? $payload['name'])
        );
        $payload['created_by'] = $request->user()?->id;

        if (($payload['status'] ?? 'draft') === 'published' && empty($payload['published_at'])) {
            $payload['published_at'] = now();
        }

        if (($payload['status'] ?? 'draft') !== 'published') {
            $payload['published_at'] = null;
        }

        Gallery::query()->create($payload);

        return redirect()
            ->route('admin.galleries.index')
            ->with('success', 'Filter galeri berhasil ditambahkan.');
    }

    public function show(Gallery $gallery): RedirectResponse
    {
        return redirect()->route('admin.galleries.edit', $gallery);
    }

    public function edit(Gallery $gallery): View
    {
        $gallery->load(['items' => fn($query) => $query->orderBy('sort_order')->latest('id')]);

        return view('admin.galleries.edit', [
            'gallery' => $gallery,
        ]);
    }

    public function update(Request $request, Gallery $gallery): RedirectResponse
    {
        $payload = $this->validatePayload($request, $gallery);
        $payload['slug'] = $this->generateUniqueSlug(
            (string) ($payload['slug'] ?? $payload['name']),
            $gallery->id
        );

        if (($payload['status'] ?? 'draft') === 'published' && empty($payload['published_at'])) {
            $payload['published_at'] = now();
        }

        if (($payload['status'] ?? 'draft') !== 'published') {
            $payload['published_at'] = null;
        }

        $gallery->update($payload);

        return redirect()
            ->route('admin.galleries.index')
            ->with('success', 'Filter galeri berhasil diperbarui.');
    }

    public function destroy(Gallery $gallery): RedirectResponse
    {
        $gallery->delete();

        return redirect()
            ->route('admin.galleries.index')
            ->with('success', 'Filter galeri berhasil dihapus.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validatePayload(Request $request, ?Gallery $gallery = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'alpha_dash'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'in:draft,published'],
            'published_at' => ['nullable', 'date'],
        ]);
    }

    private function generateUniqueSlug(string $value, ?int $ignoreId = null): string
    {
        $base = Str::slug($value);
        if ($base === '') {
            $base = 'galeri';
        }

        $slug = $base;
        $counter = 1;

        while (
            Gallery::query()
            ->when($ignoreId !== null, fn($query) => $query->where('id', '!=', $ignoreId))
            ->where('slug', $slug)
            ->exists()
        ) {
            $slug = $base . '-' . $counter;
            $counter++;
        }

        return $slug;
    }
}
