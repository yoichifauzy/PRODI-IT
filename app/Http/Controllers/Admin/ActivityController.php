<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ActivityController extends Controller
{
    public function index(Request $request): View
    {
        $search = (string) $request->query('q', '');

        $activities = Activity::query()
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($inner) use ($search): void {
                    $inner
                        ->where('title', 'like', "%{$search}%")
                        ->orWhere('category', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhere('location', 'like', "%{$search}%");
                });
            })
            ->orderByDesc('event_date')
            ->orderBy('sort_order')
            ->paginate(12)
            ->withQueryString();

        return view('admin.activities.index', [
            'activities' => $activities,
            'search' => $search,
        ]);
    }

    public function create(): View
    {
        return view('admin.activities.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validatePayload($request);
        $validated['created_by'] = $request->user()?->id;

        Activity::query()->create($validated);

        return redirect()
            ->route('admin.activities.index')
            ->with('success', 'Dokumentasi kegiatan berhasil ditambahkan.');
    }

    public function show(Activity $activity): RedirectResponse
    {
        return redirect()->route('admin.activities.edit', $activity);
    }

    public function edit(Activity $activity): View
    {
        return view('admin.activities.edit', [
            'activity' => $activity,
        ]);
    }

    public function update(Request $request, Activity $activity): RedirectResponse
    {
        $validated = $this->validatePayload($request, $activity);

        $activity->update($validated);

        return redirect()
            ->route('admin.activities.index')
            ->with('success', 'Dokumentasi kegiatan berhasil diperbarui.');
    }

    public function destroy(Activity $activity): RedirectResponse
    {
        if ($activity->image_path !== null && Storage::disk('public')->exists($activity->image_path)) {
            Storage::disk('public')->delete($activity->image_path);
        }

        $activity->delete();

        return redirect()
            ->route('admin.activities.index')
            ->with('success', 'Dokumentasi kegiatan berhasil dihapus.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validatePayload(Request $request, ?Activity $activity = null): array
    {
        $validated = $request->validate([
            'category' => ['required', 'string', 'max:120'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'location' => ['nullable', 'string', 'max:255'],
            'event_date' => ['required', 'date'],
            'published_at' => ['nullable', 'date'],
            'image' => [$activity === null ? 'required' : 'nullable', 'image', 'max:5120'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'is_published' => ['nullable', 'boolean'],
        ]);

        if ($request->hasFile('image')) {
            if ($activity?->image_path !== null && Storage::disk('public')->exists($activity->image_path)) {
                Storage::disk('public')->delete($activity->image_path);
            }
            $validated['image_path'] = $request->file('image')?->store('activities', 'public');
        }

        unset($validated['image']);

        $validated['sort_order'] = (int) ($validated['sort_order'] ?? 0);
        $validated['is_published'] = (bool) ($validated['is_published'] ?? false);

        // If publish time is set, treat it as scheduled publication to public page.
        if (!empty($validated['published_at'])) {
            $validated['is_published'] = true;
        }

        return $validated;
    }
}
