<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreAnnouncementRequest;
use App\Http\Requests\Admin\UpdateAnnouncementRequest;
use App\Models\Announcement;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AnnouncementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $announcements = Announcement::query()
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.announcements.index', [
            'announcements' => $announcements,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('admin.announcements.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAnnouncementRequest $request): RedirectResponse
    {
        $data = $this->normalizePayload($request->validated(), $request, $request->user()?->id);

        Announcement::query()->create($data);

        return redirect()
            ->route('admin.announcements.index')
            ->with('success', 'Pengumuman berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Announcement $announcement): RedirectResponse
    {
        return redirect()->route('admin.announcements.edit', $announcement);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Announcement $announcement): View
    {
        return view('admin.announcements.edit', [
            'announcement' => $announcement,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAnnouncementRequest $request, Announcement $announcement): RedirectResponse
    {
        $data = $this->normalizePayload(
            $request->validated(),
            $request,
            $request->user()?->id,
            $announcement->id,
            $announcement
        );

        $announcement->update($data);

        return redirect()
            ->route('admin.announcements.index')
            ->with('success', 'Pengumuman berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Announcement $announcement): RedirectResponse
    {
        if ($announcement->cover_image !== null && !Str::startsWith($announcement->cover_image, ['http://', 'https://'])) {
            if (Storage::disk('public')->exists($announcement->cover_image)) {
                Storage::disk('public')->delete($announcement->cover_image);
            }
        }

        // Permanently delete so slug and row are removed from database.
        $announcement->forceDelete();

        return redirect()
            ->route('admin.announcements.index')
            ->with('success', 'Pengumuman berhasil dihapus.');
    }

    /**
     * @param array<string, mixed> $validated
     * @return array<string, mixed>
     */
    private function normalizePayload(
        array $validated,
        Request $request,
        ?int $userId,
        ?int $ignoreId = null,
        ?Announcement $announcement = null
    ): array {
        $slugSeed = isset($validated['slug']) && $validated['slug'] !== ''
            ? (string) $validated['slug']
            : (string) $validated['title'];

        $validated['slug'] = $this->generateUniqueSlug($slugSeed, $ignoreId);

        $normalizedExcerpt = trim((string) preg_replace('/\s+/', ' ', strip_tags((string) $validated['content'])));
        $validated['excerpt'] = Str::limit($normalizedExcerpt, 500);

        $existingCoverImage = $announcement?->cover_image;
        $coverImageUrl = trim((string) ($validated['cover_image_url'] ?? ''));

        if ($request->hasFile('cover_image_file')) {
            if ($existingCoverImage !== null && !Str::startsWith($existingCoverImage, ['http://', 'https://'])) {
                if (Storage::disk('public')->exists($existingCoverImage)) {
                    Storage::disk('public')->delete($existingCoverImage);
                }
            }

            $validated['cover_image'] = $request->file('cover_image_file')?->store('announcements', 'public');
        } elseif ($coverImageUrl !== '') {
            if ($existingCoverImage !== null && !Str::startsWith($existingCoverImage, ['http://', 'https://'])) {
                if (Storage::disk('public')->exists($existingCoverImage)) {
                    Storage::disk('public')->delete($existingCoverImage);
                }
            }

            $validated['cover_image'] = $coverImageUrl;
        } elseif ($announcement !== null) {
            $validated['cover_image'] = $existingCoverImage;
        } else {
            $validated['cover_image'] = null;
        }

        unset($validated['cover_image_url'], $validated['cover_image_file']);

        if (($validated['status'] ?? 'draft') === 'published' && empty($validated['published_at'])) {
            $validated['published_at'] = now();
        }

        $validated['updated_by'] = $userId;
        if ($ignoreId === null) {
            $validated['created_by'] = $userId;
        }

        return $validated;
    }

    private function generateUniqueSlug(string $value, ?int $ignoreId = null): string
    {
        $base = Str::slug($value);
        if ($base === '') {
            $base = 'pengumuman';
        }

        $slug = $base;
        $counter = 1;

        while (
            Announcement::query()
            ->when($ignoreId !== null, fn($q) => $q->where('id', '!=', $ignoreId))
            ->where('slug', $slug)
            ->exists()
        ) {
            $slug = $base . '-' . $counter;
            $counter++;
        }

        return $slug;
    }
}
