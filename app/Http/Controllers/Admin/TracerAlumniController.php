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
        $search = (string) $request->query('q', '');

        $alumni = TracerAlumni::query()
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($inner) use ($search): void {
                    $inner
                        ->where('nim', 'like', "%{$search}%")
                        ->orWhere('company_name', 'like', "%{$search}%")
                        ->orWhere('department', 'like', "%{$search}%")
                        ->orWhere('relevance', 'like', "%{$search}%");
                });
            })
            ->orderBy('nim')
            ->paginate(20)
            ->withQueryString();

        return view('admin.tracer-alumni.index', [
            'alumni' => $alumni,
            'search' => $search,
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
