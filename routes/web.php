<?php

use App\Http\Controllers\Admin\AnnouncementController as AdminAnnouncementController;
use App\Http\Controllers\Admin\ActivityController as AdminActivityController;
use App\Http\Controllers\Admin\AboutSectionController as AdminAboutSectionController;
use App\Http\Controllers\Admin\AspirationController as AdminAspirationController;
use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\CurriculumController as AdminCurriculumController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\CurriculumImportController as AdminCurriculumImportController;
use App\Http\Controllers\Admin\ResearchCommunitySyncController as AdminResearchCommunitySyncController;
use App\Http\Controllers\Admin\GalleryController as AdminGalleryController;
use App\Http\Controllers\Admin\GalleryItemController as AdminGalleryItemController;
use App\Http\Controllers\Admin\HeroSlideController as AdminHeroSlideController;
use App\Http\Controllers\Admin\LecturerStaffController as AdminLecturerStaffController;
use App\Http\Controllers\Admin\ProjectController as AdminProjectController;
use App\Http\Controllers\Admin\TracerAlumniController as AdminTracerAlumniController;
use App\Http\Controllers\Admin\AcademicEventController as AdminAcademicEventController;
use App\Http\Controllers\Admin\VisionMissionController as AdminVisionMissionController;
use App\Http\Controllers\PublicSite\AcademicCalendarController;
use App\Http\Controllers\PublicSite\AspirationController as PublicAspirationController;
use App\Http\Controllers\PublicSite\HomeController;
use App\Http\Controllers\PublicSite\PublicPageController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/tentang-kami', [PublicPageController::class, 'about'])->name('public.about');
Route::get('/kalender-akademik', [AcademicCalendarController::class, 'index'])->name('calendar.index');
Route::get('/kalender-akademik/event/{academicEvent:slug}', [AcademicCalendarController::class, 'show'])->name('calendar.events.show');
Route::post('/aspirations', [PublicAspirationController::class, 'store'])->name('aspirations.store');
Route::get('/kegiatan', [PublicPageController::class, 'activities'])->name('public.activities');
Route::get('/galeri', [PublicPageController::class, 'galleries'])->name('public.galleries');
Route::get('/galeri/{galleryItem}', [PublicPageController::class, 'galleryShow'])->name('public.galleries.show');
Route::get('/kegiatan/{activity}', [PublicPageController::class, 'activityShow'])->name('public.activities.show');
Route::get('/dosen-dan-staff', [PublicPageController::class, 'lecturerStaff'])->name('public.lecturer-staff');
Route::get('/kurikulum', [PublicPageController::class, 'curriculum'])->name('public.curriculum');
Route::get('/penelitian', [PublicPageController::class, 'research'])->name('public.research');
Route::get('/api/research/suggestions', [PublicPageController::class, 'researchSuggestions'])->name('public.research.suggestions');
Route::get('/pengabdian-masyarakat', [PublicPageController::class, 'communityService'])->name('public.community-service');
Route::get('/project-mahasiswa', [PublicPageController::class, 'projects'])->name('public.projects');
Route::get('/project-mahasiswa/{project:slug}', [PublicPageController::class, 'projectShow'])->name('public.projects.show');
Route::get('/capaian-pembelajaran', [PublicPageController::class, 'learningOutcomes'])->name('public.learning-outcomes');
Route::get('/tracer-alumni', [PublicPageController::class, 'tracerAlumni'])->name('public.tracer-alumni');
Route::get('/pengumuman', [PublicPageController::class, 'announcements'])->name('public.announcements');
Route::get('/pengumuman/sync', [PublicPageController::class, 'announcementsSync'])->name('public.announcements.sync');

Route::prefix('adminit')->name('admin.')->group(function (): void {
    Route::middleware('guest')->group(function (): void {
        Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [AdminAuthController::class, 'login'])->name('login.attempt');
    });

    Route::post('/logout', [AdminAuthController::class, 'logout'])
        ->middleware('auth')
        ->name('logout');

    Route::middleware(['auth', 'admin', 'admin.session'])->group(function (): void {
        Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

        Route::resource('announcements', AdminAnnouncementController::class)->except(['show']);
        Route::resource('academic-events', AdminAcademicEventController::class)->except(['show']);
        Route::resource('vision-missions', AdminVisionMissionController::class)->only(['index', 'edit', 'update']);
        Route::resource('hero-slides', AdminHeroSlideController::class)->except(['show']);
        Route::resource('activities', AdminActivityController::class)->except(['show']);
        Route::resource('galleries', AdminGalleryController::class)->except(['show']);
        Route::resource('gallery-items', AdminGalleryItemController::class)->except(['show']);
        Route::resource('lecturer-staff', AdminLecturerStaffController::class)->except(['show']);
        Route::get('curricula', [AdminCurriculumController::class, 'index'])->name('curricula.index');
        Route::post('curricula/link', [AdminCurriculumImportController::class, 'updateLink'])->name('curricula.link.update');
        Route::post('curricula/upload', [AdminCurriculumImportController::class, 'upload'])->name('curricula.upload');
        Route::post('curricula/sync', [AdminCurriculumImportController::class, 'syncNow'])->name('curricula.sync');
        Route::post('curricula/sync/validate', [AdminCurriculumImportController::class, 'syncValidate'])->name('curricula.sync.validate');
        Route::post('curricula/sync/discard', [AdminCurriculumImportController::class, 'syncDiscard'])->name('curricula.sync.discard');
        Route::get('curricula/download', [AdminCurriculumImportController::class, 'download'])->name('curricula.download');
        Route::get('research-community', [AdminResearchCommunitySyncController::class, 'index'])->name('research-community.index');
        Route::post('research-community/link', [AdminResearchCommunitySyncController::class, 'updateLink'])->name('research-community.link.update');
        Route::post('research-community/upload', [AdminResearchCommunitySyncController::class, 'upload'])->name('research-community.upload');
        Route::post('research-community/sync', [AdminResearchCommunitySyncController::class, 'syncNow'])->name('research-community.sync');
        Route::post('research-community/sync/validate', [AdminResearchCommunitySyncController::class, 'syncValidate'])->name('research-community.sync.validate');
        Route::post('research-community/sync/discard', [AdminResearchCommunitySyncController::class, 'syncDiscard'])->name('research-community.sync.discard');
        Route::get('research-community/download', [AdminResearchCommunitySyncController::class, 'download'])->name('research-community.download');
        Route::resource('projects', AdminProjectController::class)->except(['show']);
        Route::resource('tracer-alumni', AdminTracerAlumniController::class)
            ->parameters(['tracer-alumni' => 'tracerAlumni'])
            ->except(['show']);
        Route::resource('tracer-alumni-slides', App\Http\Controllers\Admin\TracerAlumniSlideController::class)
            ->parameters(['tracer-alumni-slides' => 'tracerAlumniSlide'])
            ->except(['show']);

        Route::get('/about-section', [AdminAboutSectionController::class, 'edit'])->name('about-section.edit');
        Route::put('/about-section', [AdminAboutSectionController::class, 'update'])->name('about-section.update');

        Route::get('/aspirations', [AdminAspirationController::class, 'index'])->name('aspirations.index');
        Route::get('/aspirations/{aspiration}', [AdminAspirationController::class, 'show'])->name('aspirations.show');
        Route::patch('/aspirations/{aspiration}', [AdminAspirationController::class, 'update'])->name('aspirations.update');
        Route::delete('/aspirations/{aspiration}', [AdminAspirationController::class, 'destroy'])->name('aspirations.destroy');
    });
});
