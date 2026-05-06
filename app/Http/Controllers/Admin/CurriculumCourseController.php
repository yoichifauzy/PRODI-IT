<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Curriculum;
use App\Models\CurriculumCourse;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CurriculumCourseController extends Controller
{
    public function index(Request $request): View
    {
        $curriculumId = $request->integer('curriculum_id');

        $courses = CurriculumCourse::query()
            ->with('curriculum')
            ->when($curriculumId > 0, fn($query) => $query->where('curriculum_id', $curriculumId))
            // ->orderBy('curriculum_id')
            ->orderBy('code')
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view('admin.curriculum-courses.index', [
            'courses' => $courses,
            'curricula' => Curriculum::query()->orderBy('name')->orderBy('id')->get(),
            'curriculumId' => $curriculumId,
        ]);
    }

    public function create(): View
    {
        return view('admin.curriculum-courses.create', [
            'curricula' => Curriculum::query()->orderBy('name')->orderBy('id')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $payload = $this->validatePayload($request);
        CurriculumCourse::query()->create($payload);

        return redirect()
            ->route('admin.curriculum-courses.index')
            ->with('success', 'Mata kuliah kurikulum berhasil ditambahkan.');
    }

    public function show(CurriculumCourse $curriculumCourse): RedirectResponse
    {
        return redirect()->route('admin.curriculum-courses.edit', $curriculumCourse);
    }

    public function edit(CurriculumCourse $curriculumCourse): View
    {
        return view('admin.curriculum-courses.edit', [
            'curriculumCourse' => $curriculumCourse,
            'curricula' => Curriculum::query()->orderBy('name')->orderBy('id')->get(),
        ]);
    }

    public function update(Request $request, CurriculumCourse $curriculumCourse): RedirectResponse
    {
        $payload = $this->validatePayload($request, $curriculumCourse);
        $curriculumCourse->update($payload);

        return redirect()
            ->route('admin.curriculum-courses.index')
            ->with('success', 'Mata kuliah kurikulum berhasil diperbarui.');
    }

    public function destroy(CurriculumCourse $curriculumCourse): RedirectResponse
    {
        $curriculumCourse->delete();

        return redirect()
            ->route('admin.curriculum-courses.index')
            ->with('success', 'Mata kuliah kurikulum berhasil dihapus.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validatePayload(Request $request, ?CurriculumCourse $curriculumCourse = null): array
    {
        $ignoreId = $curriculumCourse?->id;

        $validated = $request->validate([
            'curriculum_id' => ['required', 'exists:curricula,id'],
            'code' => [
                'required',
                'string',
                'max:20',
                Rule::unique('curriculum_courses', 'code')
                    ->where(fn($query) => $query
                        ->where('curriculum_id', $request->input('curriculum_id')))
                    ->ignore($ignoreId),
            ],
            'name' => ['required', 'string', 'max:255'],
            'credits_theory' => ['nullable', 'integer', 'min:0', 'max:9'],
            'credits_practice' => ['nullable', 'integer', 'min:0', 'max:9'],
            // WAJIB AKTIF agar data tersimpan ke database:
            'short_syllabus' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
        ]);

        // Simpan SKS teori/praktik dan total.
        $creditsTheory = (int) ($request->input('credits_theory', 0));
        $creditsPractice = (int) ($request->input('credits_practice', 0));
        $validated['credits_theory'] = $creditsTheory;
        $validated['credits_practice'] = $creditsPractice;
        $validated['credits'] = $creditsTheory + $creditsPractice;

        $validated['sort_order'] = (int) ($request->input('sort_order', 0));
        $validated['short_syllabus'] = $request->input('short_syllabus');

        return $validated;
    }
}
