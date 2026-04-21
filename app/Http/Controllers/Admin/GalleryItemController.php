<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Gallery;
use App\Models\GalleryItem;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GalleryItemController extends Controller
{
    public function index(Request $request): View
    {
        $galleryId = $request->integer('gallery_id');

        $items = GalleryItem::query()
            ->with('gallery')
            ->when($galleryId !== 0, fn($query) => $query->where('gallery_id', $galleryId))
            ->orderByDesc('published_at')
            ->orderByDesc('taken_at')
            ->orderBy('sort_order')
            ->paginate(18)
            ->withQueryString();

        $galleries = Gallery::query()->orderBy('name')->get();

        return view('admin.gallery-items.index', [
            'items' => $items,
            'galleries' => $galleries,
            'galleryId' => $galleryId,
        ]);
    }

    public function create(): View
    {
        return view('admin.gallery-items.create', [
            'galleries' => Gallery::query()->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $payload = $this->validatePayload($request);
        GalleryItem::query()->create($payload);

        return redirect()
            ->route('admin.gallery-items.index')
            ->with('success', 'Item galeri berhasil ditambahkan.');
    }

    public function show(GalleryItem $galleryItem): RedirectResponse
    {
        return redirect()->route('admin.gallery-items.edit', $galleryItem);
    }

    public function edit(GalleryItem $galleryItem): View
    {
        return view('admin.gallery-items.edit', [
            'galleryItem' => $galleryItem,
            'galleries' => Gallery::query()->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, GalleryItem $galleryItem): RedirectResponse
    {
        $payload = $this->validatePayload($request, $galleryItem);
        $galleryItem->update($payload);

        return redirect()
            ->route('admin.gallery-items.index')
            ->with('success', 'Item galeri berhasil diperbarui.');
    }

    public function destroy(GalleryItem $galleryItem): RedirectResponse
    {
        if (Storage::disk('public')->exists($galleryItem->image_path)) {
            Storage::disk('public')->delete($galleryItem->image_path);
        }

        $galleryItem->delete();

        return redirect()
            ->route('admin.gallery-items.index')
            ->with('success', 'Item galeri berhasil dihapus.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validatePayload(Request $request, ?GalleryItem $galleryItem = null): array
    {
        $validated = $request->validate([
            'gallery_id' => ['required', 'exists:galleries,id'],
            'title' => ['nullable', 'string', 'max:255'],
            'caption' => ['nullable', 'string'],
            'image' => [$galleryItem === null ? 'required' : 'nullable', 'image', 'max:5120'],
            'taken_at' => ['nullable', 'date'],
            'published_at' => ['nullable', 'date'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
        ]);

        if ($request->hasFile('image')) {
            if ($galleryItem !== null && Storage::disk('public')->exists($galleryItem->image_path)) {
                Storage::disk('public')->delete($galleryItem->image_path);
            }

            $validated['image_path'] = $request->file('image')?->store('gallery-items', 'public');
        }

        unset($validated['image']);
        $validated['sort_order'] = (int) ($validated['sort_order'] ?? 0);

        return $validated;
    }
}
