<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TracerAlumni;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TracerAlumniController extends Controller
{
    public function index(Request $request): View
    {
        $filters = [
            'q' => trim((string) $request->query('q', '')),
            'nim' => trim((string) $request->query('nim', '')),
            'graduation_year' => trim((string) $request->query('graduation_year', '')),
            'company_name' => trim((string) $request->query('company_name', '')),
            'company_level' => trim((string) $request->query('company_level', '')),
            'department' => trim((string) $request->query('department', '')),
            'relevance' => trim((string) $request->query('relevance', '')),
            'notes' => trim((string) $request->query('notes', '')),
            'is_active' => trim((string) $request->query('is_active', '')),
        ];

        $search = $filters['q'];

        $alumni = TracerAlumni::query()
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($inner) use ($search): void {
                    $inner
                        ->where('nim', 'like', "%{$search}%")
                        ->orWhere('graduation_year', 'like', "%{$search}%")
                        ->orWhere('company_name', 'like', "%{$search}%")
                        ->orWhere('company_level', 'like', "%{$search}%")
                        ->orWhere('department', 'like', "%{$search}%")
                        ->orWhere('relevance', 'like', "%{$search}%")
                        ->orWhere('notes', 'like', "%{$search}%");
                });
            })
            ->when($filters['nim'] !== '', fn($query) => $query->where('nim', 'like', "%{$filters['nim']}%"))
            ->when($filters['graduation_year'] !== '', fn($query) => $query->where('graduation_year', $filters['graduation_year']))
            ->when($filters['company_name'] !== '', fn($query) => $query->where('company_name', 'like', "%{$filters['company_name']}%"))
            ->when($filters['company_level'] !== '', fn($query) => $query->where('company_level', 'like', "%{$filters['company_level']}%"))
            ->when($filters['department'] !== '', fn($query) => $query->where('department', 'like', "%{$filters['department']}%"))
            ->when($filters['relevance'] !== '', fn($query) => $query->where('relevance', 'like', "%{$filters['relevance']}%"))
            ->when($filters['notes'] !== '', fn($query) => $query->where('notes', 'like', "%{$filters['notes']}%"))
            ->when($filters['is_active'] !== '', fn($query) => $query->where('is_active', $filters['is_active'] === '1'))
            ->orderBy('nim')
            ->paginate(20)
            ->withQueryString();

        return view('admin.tracer-alumni.index', [
            'alumni' => $alumni,
            'filters' => $filters,
        ]);
    }

    public function create(): View
    {
        return view('admin.tracer-alumni.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $payload = $this->validatePayload($request);
        TracerAlumni::query()->create($payload);

        return redirect()
            ->route('admin.tracer-alumni.index')
            ->with('success', 'Data tracer alumni berhasil ditambahkan.');
    }

    public function show(TracerAlumni $tracerAlumni): RedirectResponse
    {
        return redirect()->route('admin.tracer-alumni.edit', $tracerAlumni);
    }

    public function edit(TracerAlumni $tracerAlumni): View
    {
        return view('admin.tracer-alumni.edit', [
            'tracerAlumni' => $tracerAlumni,
        ]);
    }

    public function update(Request $request, TracerAlumni $tracerAlumni): RedirectResponse
    {
        $payload = $this->validatePayload($request, $tracerAlumni);
        $tracerAlumni->update($payload);

        return redirect()
            ->route('admin.tracer-alumni.index')
            ->with('success', 'Data tracer alumni berhasil diperbarui.');
    }

    public function destroy(TracerAlumni $tracerAlumni): RedirectResponse
    {
        $tracerAlumni->delete();

        return redirect()
            ->route('admin.tracer-alumni.index')
            ->with('success', 'Data tracer alumni berhasil dihapus.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validatePayload(Request $request, ?TracerAlumni $tracerAlumni = null): array
    {
        $validated = $request->validate([
            'nim' => [
                'required',
                'string',
                'max:30',
                Rule::unique('tracer_alumnis', 'nim')->ignore($tracerAlumni?->id),
            ],
            'graduation_year' => ['required', 'integer', 'min:2000', 'max:2100'],
            'company_name' => ['required', 'string', 'max:255'],
            'company_level' => ['nullable', 'string', 'max:255'],
            'department' => ['nullable', 'string', 'max:255'],
            'relevance' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['is_active'] = (bool) ($validated['is_active'] ?? false);

        return $validated;
    }
}
