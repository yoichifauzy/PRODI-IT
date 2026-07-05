<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Services\GoogleCalendarSyncService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ActivityController extends Controller
{
    public function __construct(private readonly GoogleCalendarSyncService $calendarService) {}

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
            ->orderByDesc('id')
            ->paginate(12)
            ->withQueryString();

        return view('admin.activities.index', [
            'activities' => $activities,
            'search'     => $search,
        ]);
    }

    public function create(): View
    {
        $existingCategories = Activity::query()
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        return view('admin.activities.create', [
            'existingCategories' => $existingCategories,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validatePayload($request);
        $validated['created_by'] = $request->user()?->id;

        $activity = Activity::query()->create($validated);

        $this->syncToGoogleCalendar($activity);

        return redirect()
            ->route('admin.activities.index')
            ->with('success', 'Kegiatan berhasil ditambahkan.');
    }

    public function show(Activity $activity): RedirectResponse
    {
        return redirect()->route('admin.activities.edit', $activity);
    }

    public function edit(Activity $activity): View
    {
        $existingCategories = Activity::query()
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        return view('admin.activities.edit', [
            'activity'           => $activity,
            'existingCategories' => $existingCategories,
        ]);
    }

    public function update(Request $request, Activity $activity): RedirectResponse
    {
        $validated = $this->validatePayload($request, $activity);
        $activity->update($validated);

        $this->syncToGoogleCalendar($activity->fresh() ?? $activity);

        return redirect()
            ->route('admin.activities.index')
            ->with('success', 'Kegiatan berhasil diperbarui.');
    }

    public function destroy(Activity $activity): RedirectResponse
    {
        // Hapus dari Google Calendar terlebih dahulu
        $this->calendarService->deleteActivity($activity);

        // Hapus gambar lokal jika ada
        if ($activity->image_path !== null && Storage::disk('public')->exists($activity->image_path)) {
            Storage::disk('public')->delete($activity->image_path);
        }

        $activity->delete();

        return redirect()
            ->route('admin.activities.index')
            ->with('success', 'Kegiatan berhasil dihapus.');
    }

    /**
     * API endpoint: kembalikan daftar kategori yang sudah pernah dipakai
     * untuk fitur autocomplete/datalist di form.
     */
    public function categoryOptions(): JsonResponse
    {
        $categories = Activity::query()
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        return response()->json($categories);
    }

    // =========================================================
    //  PRIVATE HELPERS
    // =========================================================

    /**
     * Simpan/update event ke Google Calendar dan isi kolom google_event_url.
     */
    private function syncToGoogleCalendar(Activity $activity): void
    {
        $googleUrl = $this->calendarService->syncOrDeleteActivity($activity);

        if ($googleUrl !== null && $googleUrl !== '') {
            $activity->forceFill(['google_event_url' => $googleUrl])->save();
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function validatePayload(Request $request, ?Activity $activity = null): array
    {
        $validated = $request->validate([
            'category'    => ['required', 'string', 'max:120'],
            'title'       => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'location'    => ['nullable', 'string', 'max:255'],
            'event_date'  => ['required', 'date'],
            'start_at'    => ['nullable', 'date_format:H:i'],
            'end_at'      => ['nullable', 'date_format:H:i', 'after_or_equal:start_at'],
            'image'       => [$activity === null ? 'required' : 'nullable', 'image', 'max:5120'],
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            if ($activity?->image_path !== null && Storage::disk('public')->exists($activity->image_path)) {
                Storage::disk('public')->delete($activity->image_path);
            }
            $validated['image_path'] = $request->file('image')?->store('activities', 'public');
        }

        unset($validated['image']);

        // Gabungkan event_date + start_at/end_at menjadi full datetime
        if (!empty($validated['start_at'])) {
            $validated['start_at'] = $validated['event_date'] . ' ' . $validated['start_at'] . ':00';
        }
        if (!empty($validated['end_at'])) {
            $validated['end_at'] = $validated['event_date'] . ' ' . $validated['end_at'] . ':00';
        }

        return $validated;
    }
}
