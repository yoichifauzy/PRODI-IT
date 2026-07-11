<?php

namespace App\Http\Controllers\PublicSite;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;

class CurriculumController extends Controller
{
    public function index(Request $request)
    {
        $courses = Course::query()
            ->get()
            ->map(function ($c) {
                $c->major_selection = $c->major_selection ?: 'Umum';
                return $c;
            });

        // Group by semester, sorted numerically (Semester 1, 2, ... 10, not alphabetically)
        $coursesBySemester = $courses
            ->groupBy('semester')
            ->sortBy(function ($group, $semester) {
                preg_match('/\d+/', $semester, $m);
                return isset($m[0]) ? (int) $m[0] : 999;
            });

        $selectedSemester = $request->query('semester', $coursesBySemester->keys()->first());

        if (! $coursesBySemester->has($selectedSemester)) {
            $selectedSemester = $coursesBySemester->keys()->first();
        }

        $semesterCourses = $coursesBySemester->get($selectedSemester, collect());

        $majorsInSemester = $semesterCourses->pluck('major_selection')->unique()->values();

        $selectedMajor = $request->query('major', $majorsInSemester->first());

        if (! $majorsInSemester->contains($selectedMajor)) {
            $selectedMajor = $majorsInSemester->first();
        }

        return view('public.curriculum', compact(
            'coursesBySemester',
            'selectedSemester',
            'selectedMajor'
        ));
    }
}