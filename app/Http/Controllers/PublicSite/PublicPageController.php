<?php

namespace App\Http\Controllers\PublicSite;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Announcement;
use App\Models\Curriculum;
use App\Models\Gallery;
use App\Models\GalleryItem;
use App\Models\LecturerStaff;
use App\Models\Project;
use App\Models\Research;
use App\Models\Setting;
use App\Models\CommunityService;
use App\Models\TracerAlumni;
use App\Models\VisionMission;
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
        $allCurricula = Curriculum::query()
            ->with(['courses' => function ($query) {
                $query->orderBy('sort_order')->orderBy('code');
            }])
            ->orderBy('name')
            ->orderBy('id')
            ->get();

        // Tombol filter utama hanya menampilkan nama yang unik
        $uniqueCurricula = $allCurricula->unique('name');

        $selectedCurriculumId = $request->integer('curriculum');
        $selectedCurriculum = $selectedCurriculumId > 0
            ? $allCurricula->firstWhere('id', $selectedCurriculumId)
            : $allCurricula->first();

        if ($selectedCurriculum === null) {
            $selectedCurriculum = $allCurricula->first();
        }

        $majorOptions = collect();
        if ($selectedCurriculum) {
            $majorOptions = $allCurricula->where('name', $selectedCurriculum->name);
        }

        return view('public.curriculum', [
            'curricula' => $uniqueCurricula,
            'allCurricula' => $allCurricula,
            'selectedCurriculum' => $selectedCurriculum,
            'majorOptions' => $majorOptions,
        ]);
    }



    public function activities(): View
    {
        $activities = Activity::query()
            ->visibleOnPublic()
            ->orderByRaw('CASE WHEN sort_order IS NULL OR sort_order = 0 THEN 9999 ELSE sort_order END')
            ->orderByDesc('event_date')
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
            ->orderByRaw('CASE WHEN sort_order IS NULL OR sort_order = 0 THEN 9999 ELSE sort_order END')
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
        $selectedGallery = trim((string) $request->query('gallery', ''));

        $galleries = Gallery::query()
            ->where('status', 'published')
            ->where(function ($query): void {
                $query->whereNull('published_at')->orWhere('published_at', '<=', now());
            })
            ->orderBy('name')
            ->get(['id', 'name', 'slug']);

        if ($selectedGallery !== '' && !$galleries->contains('slug', $selectedGallery)) {
            $selectedGallery = '';
        }

        $galleryItems = GalleryItem::query()
            ->with('gallery:id,name,slug,status,published_at')
            ->visibleOnPublic()
            ->whereHas('gallery', function ($query): void {
                $query
                    ->where('status', 'published')
                    ->where(function ($inner): void {
                        $inner->whereNull('published_at')->orWhere('published_at', '<=', now());
                    });
            })
            ->when($selectedGallery !== '', function ($query) use ($selectedGallery): void {
                $query->whereHas('gallery', fn($gallery) => $gallery->where('slug', $selectedGallery));
            })
            ->orderByRaw('CASE WHEN sort_order IS NULL OR sort_order = 0 THEN 9999 ELSE sort_order END')
            ->orderByDesc('taken_at')
            ->orderByDesc('id')
            ->paginate(12)
            ->withQueryString();

        return view('public.galleries', [
            'galleryItems' => $galleryItems,
            'galleries' => $galleries,
            'selectedGallery' => $selectedGallery,
        ]);
    }

    public function galleryShow(GalleryItem $galleryItem): View
    {
        $isVisible = GalleryItem::query()
            ->with('gallery:id,name,slug,status,published_at')
            ->visibleOnPublic()
            ->whereKey($galleryItem->id)
            ->whereHas('gallery', function ($query): void {
                $query
                    ->where('status', 'published')
                    ->where(function ($inner): void {
                        $inner->whereNull('published_at')->orWhere('published_at', '<=', now());
                    });
            })
            ->exists();

        abort_unless($isVisible, 404);

        $relatedGalleryItems = GalleryItem::query()
            ->with('gallery:id,name,slug,status,published_at')
            ->visibleOnPublic()
            ->where('gallery_id', $galleryItem->gallery_id)
            ->whereKeyNot($galleryItem->id)
            ->whereHas('gallery', function ($query): void {
                $query
                    ->where('status', 'published')
                    ->where(function ($inner): void {
                        $inner->whereNull('published_at')->orWhere('published_at', '<=', now());
                    });
            })
            ->orderByRaw('CASE WHEN sort_order IS NULL OR sort_order = 0 THEN 9999 ELSE sort_order END')
            ->orderByDesc('taken_at')
            ->orderByDesc('id')
            ->take(6)
            ->get();

        return view('public.gallery-detail', [
            'galleryItem' => $galleryItem,
            'relatedGalleryItems' => $relatedGalleryItems,
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

    public function learningOutcomes(): View
    {
        // Placeholder content for learning outcomes page. Content will be updated periodically.
        return view('public.learning-outcomes');
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

    public function research(): View
    {
        $researches = Research::query()
            ->where('status', 'published')
            ->orderByDesc('year')
            ->orderBy('title')
            ->get();

        return view('public.research', [
            'researches' => $researches,
        ]);
    }

    public function about(): View
    {
        $visionMission = VisionMission::query()->where('is_active', true)->first();

        $aboutKeys = [
            'about_section_title',
            'about_section_subtitle',
            'about_heading',
            'about_description_primary',
            'about_description_secondary',
            'about_image_one',
            'about_image_two',
            'about_video_path',
        ];

        $aboutSettings = Setting::query()
            ->whereIn('key', $aboutKeys)
            ->get()
            ->pluck('value', 'key');

        return view('public.about', [
            'visionMission' => $visionMission,
            'aboutSettings' => $aboutSettings,
        ]);
    }

    public function communityService(): View
    {
        $services = CommunityService::query()
            ->where('status', 'published')
            ->orderByDesc('activity_date')
            ->orderBy('title')
            ->get();

        return view('public.community-service', [
            'services' => $services,
        ]);
    }
}
