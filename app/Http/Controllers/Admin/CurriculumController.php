<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Curriculum;
use App\Models\CurriculumCourse;
use App\Models\CurriculumDraft;
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

        $draftCount = CurriculumDraft::count();
        $isDraftMode = $draftCount > 0;

        if ($isDraftMode) {
            $allCurricula = CurriculumDraft::query()
                ->with(['courses' => fn($q) => $q->orderBy('sort_order')->orderBy('code')])
                ->orderBy('name')->orderBy('id')->get();
            $this->markCoursePreviewStatuses($allCurricula);
        } else {
            $allCurricula = Curriculum::query()
                ->with(['courses' => fn($q) => $q->orderBy('sort_order')->orderBy('code')])
                ->orderBy('name')->orderBy('id')->get();
            $allCurricula->each(function (Curriculum $curriculum): void {
                $curriculum->courses->each->setAttribute('admin_sync_status', 'published');
            });
        }

        $uniqueCurricula = $allCurricula->unique('name');

        return view('admin.curricula.index', [
            'sheetUrl' => $sheetUrl,
            'allCurricula' => $allCurricula,
            'curricula' => $uniqueCurricula,
            'isDraftMode' => $isDraftMode,
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

    private function markCoursePreviewStatuses($draftCurricula): void
    {
        $publishedCourseKeys = CurriculumCourse::query()
            ->with('curriculum:id,name')
            ->get(['id', 'curriculum_id', 'code', 'name', 'credits_theory', 'credits_practice'])
            ->mapWithKeys(function (CurriculumCourse $course) {
                return [$this->coursePreviewKey(
                    $course->curriculum?->name,
                    $course->code,
                    $course->name,
                    $course->credits_theory,
                    $course->credits_practice
                ) => true];
            });

        $draftCurricula->each(function (CurriculumDraft $curriculum) use ($publishedCourseKeys): void {
            $curriculum->courses->each(function ($course) use ($curriculum, $publishedCourseKeys): void {
                $key = $this->coursePreviewKey(
                    $curriculum->name,
                    $course->code,
                    $course->name,
                    $course->credits_theory,
                    $course->credits_practice
                );

                $course->setAttribute('admin_sync_status', $publishedCourseKeys->has($key) ? 'published' : 'draft');
            });
        });
    }

    private function coursePreviewKey(?string $curriculumName, ?string $code, ?string $name, int $creditsTheory, int $creditsPractice): string
    {
        return implode('|', [
            $this->normalizePreviewValue($curriculumName),
            $this->normalizePreviewValue($code),
            $this->normalizePreviewValue($name),
            $creditsTheory,
            $creditsPractice,
        ]);
    }

    private function normalizePreviewValue(?string $value): string
    {
        return mb_strtolower(trim(preg_replace('/\s+/', ' ', (string) $value)));
    }
}
