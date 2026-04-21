<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreAcademicEventRequest;
use App\Http\Requests\Admin\UpdateAcademicEventRequest;
use App\Models\AcademicEvent;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AcademicEventController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $search = (string) $request->query('q', '');
        $month = $this->resolveMonth((string) $request->query('month', ''));

        $monthStart = $month->startOfMonth();
        $monthEnd = $month->endOfMonth();

        $events = AcademicEvent::query()
            ->when($search !== '', function ($q) use ($search): void {
                $q->where(function ($query) use ($search): void {
                    $query
                        ->where('title', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhere('location', 'like', "%{$search}%");
                });
            })
            ->where('start_date', '<=', $monthEnd->toDateString())
            ->where(function ($q) use ($monthStart): void {
                $q->whereNull('end_date')->orWhere('end_date', '>=', $monthStart->toDateString());
            })
            ->orderBy('start_date')
            ->orderBy('title')
            ->paginate(12)
            ->withQueryString();

        return view('admin.academic-events.index', [
            'events' => $events,
            'search' => $search,
            'month' => $month,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('admin.academic-events.create', [
            'eventTypes' => AcademicEvent::EVENT_TYPES,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAcademicEventRequest $request): RedirectResponse
    {
        $payload = $this->normalizePayload($request->validated());
        $payload['created_by'] = $request->user()?->id;

        AcademicEvent::query()->create($payload);

        return redirect()
            ->route('admin.academic-events.index')
            ->with('success', 'Event akademik berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(AcademicEvent $academicEvent): RedirectResponse
    {
        return redirect()->route('admin.academic-events.edit', $academicEvent);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AcademicEvent $academicEvent): View
    {
        return view('admin.academic-events.edit', [
            'academicEvent' => $academicEvent,
            'eventTypes' => AcademicEvent::EVENT_TYPES,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAcademicEventRequest $request, AcademicEvent $academicEvent): RedirectResponse
    {
        $payload = $this->normalizePayload($request->validated(), $academicEvent->id);
        $academicEvent->update($payload);

        return redirect()
            ->route('admin.academic-events.index')
            ->with('success', 'Event akademik berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AcademicEvent $academicEvent): RedirectResponse
    {
        $academicEvent->delete();

        return redirect()
            ->route('admin.academic-events.index')
            ->with('success', 'Event akademik berhasil dihapus.');
    }

    private function resolveMonth(string $month): CarbonImmutable
    {
        if (preg_match('/^\d{4}-\d{2}$/', $month) === 1) {
            try {
                $parsed = CarbonImmutable::createFromFormat('Y-m', $month);

                if ($parsed instanceof CarbonImmutable) {
                    return $parsed->startOfMonth();
                }
            } catch (\Throwable) {
                return CarbonImmutable::now()->startOfMonth();
            }
        }

        return CarbonImmutable::now()->startOfMonth();
    }

    /**
     * @param array<string, mixed> $validated
     * @return array<string, mixed>
     */
    private function normalizePayload(array $validated, ?int $ignoreId = null): array
    {
        $slugSeed = isset($validated['slug']) && $validated['slug'] !== ''
            ? (string) $validated['slug']
            : (string) $validated['title'];

        $validated['slug'] = $this->generateUniqueSlug($slugSeed, $ignoreId);
        $validated['is_published'] = (bool) ($validated['is_published'] ?? false);

        return $validated;
    }

    private function generateUniqueSlug(string $value, ?int $ignoreId = null): string
    {
        $base = Str::slug($value);
        if ($base === '') {
            $base = 'event-akademik';
        }

        $slug = $base;
        $counter = 1;

        while (
            AcademicEvent::query()
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
