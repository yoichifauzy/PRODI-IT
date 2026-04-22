<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Curriculum;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CurriculumController extends Controller
{
    public function index(): View
    {
        $curricula = Curriculum::query()
        ->with(['courses' => function($q) {
            // Urutin biar pas tampil di tabel udah rapi per semester
            $q->orderBy('code'); 
        }])
        ->orderByDesc('is_active')
        ->orderBy('name')
        ->get();

    return view('public.curriculum', [
        'curricula' => $curricula,
    ]);
    }

    public function create(): View
    {
        return view('admin.curricula.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $payload = $this->validatePayload($request);
        $payload['created_by'] = $request->user()?->id;

        if ($payload['is_active']) {
            Curriculum::query()->update(['is_active' => false]);
        }

        Curriculum::query()->create($payload);

        return redirect()
            ->route('admin.curricula.index')
            ->with('success', 'Kurikulum berhasil ditambahkan.');
    }

    public function show(Curriculum $curriculum): RedirectResponse
    {
        return redirect()->route('admin.curricula.edit', $curriculum);
    }

    public function edit(Curriculum $curriculum): View
    {
        $curriculum->load(['courses' => fn($query) => $query->orderBy('code')]);

        return view('admin.curricula.edit', [
            'curriculum' => $curriculum,
        ]);
    }

    public function update(Request $request, Curriculum $curriculum): RedirectResponse
    {
        $payload = $this->validatePayload($request);

        if ($payload['is_active']) {
            Curriculum::query()
                ->where('id', '!=', $curriculum->id)
                ->update(['is_active' => false]);
        }

        $curriculum->update($payload);

        return redirect()
            ->route('admin.curricula.index')
            ->with('success', 'Kurikulum berhasil diperbarui.');
    }

    public function destroy(Curriculum $curriculum): RedirectResponse
    {
        $curriculum->delete();

        return redirect()
            ->route('admin.curricula.index')
            ->with('success', 'Kurikulum berhasil dihapus.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validatePayload(Request $request): array
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'major_selection' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['is_active'] = (bool) ($validated['is_active'] ?? false);

        return $validated;
    }
}
