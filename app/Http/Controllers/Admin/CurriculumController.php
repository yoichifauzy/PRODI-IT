<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Curriculum;
use App\Models\Setting;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CurriculumController extends Controller
{
    public function index(): View
    {
        $defaultUrl = 'https://docs.google.com/spreadsheets/d/1d-MGrDU54pP-0uUyl5Txd4S3fV1ukCMqMUtsoxpZ9NM/edit?usp=sharing';

        $setting = Setting::query()->firstOrCreate(
            ['key' => 'curriculum_sheet_url'],
            ['value' => $defaultUrl, 'type' => 'string', 'group' => 'curriculum']
        );

        $sheetUrl = (string) ($setting->value ?: $defaultUrl);

        $allCurricula = Curriculum::query()
            ->with(['courses' => fn($q) => $q->orderBy('sort_order')->orderBy('code')])
            ->orderBy('name')->orderBy('id')->get();

        $uniqueCurricula = $allCurricula->unique('name');

        return view('admin.curricula.index', [
            'sheetUrl' => $sheetUrl,
            'allCurricula' => $allCurricula,
            'curricula' => $uniqueCurricula,
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
        $payload = $this->validatePayload($request, $curriculum);

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
    private function validatePayload(Request $request, ?Curriculum $curriculum = null): array
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('curricula', 'name')->ignore($curriculum?->id),
            ],
            'description' => ['nullable', 'string'],
        ]);

        return $validated;
    }
}
