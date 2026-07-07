<?php

namespace App\Http\Controllers\PublicSite;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Course;
use App\Models\Gallery;
use App\Models\LearningOutcome;
use App\Models\LecturerStaff;
use App\Models\Profile;
use App\Models\Project;
use App\Models\Research;
use App\Models\CommunityService;
use App\Models\TracerAlumni;
use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class PublicPageController extends Controller
{
    public function lecturerStaff(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));
        $type = trim((string) $request->query('type', ''));

        $members = LecturerStaff::query()
            ->where('is_active', true)
            ->when($type !== '' && in_array($type, LecturerStaff::TYPES, true), fn($query) => $query->where('type', $type))
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($inner) use ($search): void {
                    $inner
                        ->where('name', 'like', "%{$search}%")
                        ->orWhere('position', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->orderBy('type')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('public.lecturer-staff', [
            'members' => $members,
            'search' => $search,
            'type' => $type,
            'types' => LecturerStaff::TYPES,
        ]);
    }

    public function curriculum(Request $request): View
    {
        $courses = Course::query()
            ->orderBy('semester')
            ->orderBy('major_selection')
            ->orderBy('code')
            ->get();

        $semesters = $courses->pluck('semester')->filter()->unique()->sort()->values();

        $selectedSemester = $request->query('semester');
        if (!$selectedSemester || !$semesters->contains($selectedSemester)) {
            $selectedSemester = $semesters->first();
        }

        $semesterCourses = $courses->where('semester', $selectedSemester);
        
        $majorOptions = $semesterCourses->pluck('major_selection')->filter()->unique()->sort()->values();
        
        $selectedMajor = $request->query('major');
        if (!$selectedMajor || !$majorOptions->contains($selectedMajor)) {
            $selectedMajor = $majorOptions->first() ?? null;
        }

        $visibleCourses = $semesterCourses;
        if ($selectedMajor) {
            $visibleCourses = $semesterCourses->where('major_selection', $selectedMajor);
        } else if ($majorOptions->isEmpty()) {
            // No major options, show all in semester (which means they don't have major or are all null)
        } else {
            // Show courses where major is null or match selected
            $visibleCourses = $semesterCourses->whereNull('major_selection'); 
        }

        return view('public.curriculum', [
            'semesters' => $semesters,
            'selectedSemester' => $selectedSemester,
            'majorOptions' => $majorOptions,
            'selectedMajor' => $selectedMajor,
            'courses' => $courses,
            'visibleCourses' => $visibleCourses
        ]);
    }



    public function activities(): View
    {
        $activities = Activity::query()
            ->visibleOnPublic()
            ->orderByDesc('event_date')
            ->orderByDesc('id')
            ->paginate(12)
            ->withQueryString();

        $runningActivities = Activity::query()
            ->upcomingRunningCard()
            ->get();

        return view('public.activities', [
            'activities'        => $activities,
            'runningActivities' => $runningActivities,
        ]);
    }

    public function activityShow(Activity $activity): View
    {
        $isVisible = Activity::query()
            ->visibleOnPublic()
            ->whereKey($activity->id)
            ->exists();

        abort_unless($isVisible, 404);

        $relatedActivities = Activity::query()
            ->visibleOnPublic()
            ->where('id', '!=', $activity->id)
            // ->orderByRaw('CASE WHEN sort_order IS NULL OR sort_order = 0 THEN 9999 ELSE sort_order END')
            ->orderByDesc('event_date')
            ->orderByDesc('id')
            ->take(6)
            ->get();

        return view('public.activity-detail', [
            'activity' => $activity,
            'relatedActivities' => $relatedActivities,
        ]);
    }

    public function galleries(Request $request): View
    {
        $galleries = Gallery::query()
            ->orderBy('position')
            ->orderByDesc('id')
            ->get();

        $categories = $galleries->pluck('category')->filter()->unique()->sort()->values();
        $years      = $galleries->pluck('year')->filter()->unique()->sortDesc()->values();

        return view('public.galleries', [
            'galleries'  => $galleries,
            'categories' => $categories,
            'years'      => $years,
        ]);
    }


    public function projects(): View
    {
        // Pilihan (unggulan) — horizontal scroll, maks 12
        $featuredProjects = Project::query()
            ->where('is_feature', true)
            ->orderByDesc('year')
            ->orderByDesc('id')
            ->take(12)
            ->get();

        // Reguler — semua project non-unggulan, tampilkan semuanya (JS paginasi)
        $regularProjects = Project::query()
            ->where('is_feature', false)
            ->orderByDesc('year')
            ->orderByDesc('id')
            ->get();

        return view('public.projects', [
            'featuredProjects' => $featuredProjects,
            'regularProjects'  => $regularProjects,
        ]);
    }

    public function learningOutcomes(): View
    {
        $outcomes = LearningOutcome::orderBy('code')->get();
        return view('public.learning-outcomes', compact('outcomes'));
    }

    public function projectShow(Project $project): View
    {
        $relatedProjects = Project::query()
            ->where('id', '!=', $project->id)
            ->orderByDesc('is_feature')
            ->orderByDesc('year')
            ->orderByDesc('id')
            ->take(3)
            ->get();

        return view('public.project-detail', [
            'project'         => $project,
            'relatedProjects' => $relatedProjects,
        ]);
    }

    public function tracerAlumni(Request $request): View
    {
        $graduationYears = TracerAlumni::query()
            ->whereNotNull('graduation_year')
            ->distinct()
            ->orderByDesc('graduation_year')
            ->pluck('graduation_year');

        $selectedYear = null;
        $selectedYearRaw = $request->query('year');
        if ($selectedYearRaw !== null && $selectedYearRaw !== '') {
            $candidateYear = (int) $selectedYearRaw;
            if ($candidateYear !== 0 && $graduationYears->contains($candidateYear)) {
                $selectedYear = $candidateYear;
            }
        }

        $rows = TracerAlumni::query()
            ->orderByDesc('graduation_year')
            ->orderBy('nim')
            ->get();

        $visibleRows = $selectedYear !== null
            ? $rows->where('graduation_year', $selectedYear)
            : $rows;

        return view('public.tracer-alumni', [
            'graduationYears' => $graduationYears,
            'selectedYear' => $selectedYear,
            'rows' => $rows,
            'visibleRows' => $visibleRows,
        ]);
    }

    public function research(Request $request): View
    {
        $selectedYear = (string) $request->query('year', '');
        $searchTerm = trim((string) $request->query('q', ''));

        $researches = Research::query()
            // ->where('status', 'published')
            ->orderByDesc('year')
            ->orderBy('title')
            ->get();

        $researchYears = $researches
            ->pluck('year')
            ->filter()
            ->unique()
            ->sortDesc()
            ->values();

        if ($selectedYear !== '' && !$researchYears->contains((int) $selectedYear)) {
            $selectedYear = '';
        }

        return view('public.research', [
            'researches' => $researches,
            'researchYears' => $researchYears,
            'selectedYear' => $selectedYear,
            'search' => $searchTerm,
        ]);
    }

    public function researchSuggestions(Request $request): JsonResponse
    {
        $query = trim((string) $request->query('q', ''));

        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $suggestions = Research::query()
            // ->where('status', 'published')
            ->where(function ($q) use ($query): void {
                $q->where('title', 'like', "%{$query}%")
                    ->orWhere('researcher_name', 'like', "%{$query}%");
            })
            ->select('id', 'title', 'researcher_name', 'year')
            ->orderBy('title')
            ->limit(8)
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'title' => $item->title,
                    'researcher' => $item->researcher_name,
                    'year' => $item->year,
                    'display' => "{$item->title} ({$item->year})",
                ];
            });

        return response()->json($suggestions);
    }

    public function profile(): View
    {
        $profile = Profile::first();

        return view('public.profile', [
            'profile' => $profile,
        ]);
    }

    public function communityService(): View
    {
        $services = CommunityService::query()
            // ->where('status', 'published')
            ->orderByDesc('year')
            ->orderBy('title')
            ->get();

        return view('public.community-service', [
            'services' => $services,
        ]);
    }
}
