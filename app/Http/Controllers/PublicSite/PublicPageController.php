<?php

namespace App\Http\Controllers\PublicSite;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Announcement;
use App\Models\Curriculum;
use App\Models\LecturerStaff;
use App\Models\Project;
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

    public function lecturerStaffBlogs(LecturerStaff $lecturerStaff): View
    {
        abort_unless($lecturerStaff->is_active, 404);

        $blogs = $lecturerStaff->blogs()
            ->where('is_published', true)
            ->orderByDesc('activity_date')
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->get();

        return view('public.lecturer-staff-blogs', [
            'member' => $lecturerStaff,
            'blogs' => $blogs,
        ]);
    }

    public function curriculum(Request $request): View
    {
        $curricula = Curriculum::query()
            ->with(['courses' => fn($query) => $query->orderBy('semester')->orderBy('sort_order')])
            ->orderByDesc('is_active')
            ->orderBy('name')
            ->get();

        $selectedCurriculumId = $request->integer('curriculum');
        $selectedCurriculum = $curricula->firstWhere('id', $selectedCurriculumId);

        return view('public.curriculum', [
            'curricula' => $curricula,
            'selectedCurriculum' => $selectedCurriculum,
        ]);
    }

    public function activities(): View
    {
        $activities = Activity::query()
            ->visibleOnPublic()
            ->orderByDesc('event_date')
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->paginate(12)
            ->withQueryString();

        return view('public.activities', [
            'activities' => $activities,
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
            ->orderByDesc('event_date')
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->take(6)
            ->get();

        return view('public.activity-detail', [
            'activity' => $activity,
            'relatedActivities' => $relatedActivities,
        ]);
    }

    public function projects(): View
    {
        $featured = Project::query()
            ->visibleOnPublic()
            ->orderByDesc('is_featured')
            ->latest('published_at')
            ->latest('id')
            ->take(9)
            ->get();

        $regularProjects = Project::query()
            ->visibleOnPublic()
            ->when($featured->isNotEmpty(), fn($query) => $query->whereNotIn('id', $featured->pluck('id')->all()))
            ->latest('published_at')
            ->latest('id')
            ->get();

        return view('public.projects', [
            'featuredProjects' => $featured,
            'regularProjects' => $regularProjects,
        ]);
    }

    public function projectShow(Project $project): View
    {
        $isVisible = Project::query()
            ->visibleOnPublic()
            ->whereKey($project->id)
            ->exists();

        abort_unless($isVisible, 404);

        $relatedProjects = Project::query()
            ->visibleOnPublic()
            ->where('id', '!=', $project->id)
            ->latest('published_at')
            ->latest('id')
            ->take(6)
            ->get();

        return view('public.project-detail', [
            'project' => $project,
            'relatedProjects' => $relatedProjects,
        ]);
    }

    public function tracerAlumni(Request $request): View
    {
        $graduationYears = TracerAlumni::query()
            ->where('is_active', true)
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
            ->where('is_active', true)
            ->when($selectedYear !== null, fn($query) => $query->where('graduation_year', $selectedYear))
            ->orderByDesc('graduation_year')
            ->orderBy('nim')
            ->get();

        return view('public.tracer-alumni', [
            'graduationYears' => $graduationYears,
            'selectedYear' => $selectedYear,
            'rows' => $rows,
        ]);
    }

    public function announcements(): View
    {
        $announcements = Announcement::query()
            ->published()
            ->latest('published_at')
            ->latest('id')
            ->take(20)
            ->get();

        $announcementSync = Announcement::publishedSyncPayload();

        return view('public.announcements', [
            'announcements' => $announcements,
            'announcementSync' => $announcementSync,
        ]);
    }

    public function announcementsSync(): JsonResponse
    {
        return response()
            ->json(Announcement::publishedSyncPayload())
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache');
    }
}
