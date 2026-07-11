<?php

use App\Http\Controllers\Admin\ActivityController as AdminActivityController;
use App\Http\Controllers\Admin\ProfileController as AdminProfileController;
use App\Http\Controllers\Admin\AspirationController as AdminAspirationController;
use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\CourseController as AdminCourseController;
use App\Http\Controllers\Admin\ResearchCommunitySyncController as AdminResearchCommunitySyncController;
use App\Http\Controllers\Admin\GalleryController as AdminGalleryController;
use App\Http\Controllers\Admin\HeroSlideController as AdminHeroSlideController;
use App\Http\Controllers\Admin\LecturerStaffController as AdminLecturerStaffController;
use App\Http\Controllers\Admin\ProjectController as AdminProjectController;
use App\Http\Controllers\Admin\TracerAlumniController as AdminTracerAlumniController;
use App\Http\Controllers\Admin\DocumentController as AdminDocumentController;
use App\Http\Controllers\Admin\AcademicCalendarController as AdminAcademicCalendarController;

use App\Http\Controllers\PublicSite\AcademicCalendarController;
use App\Http\Controllers\PublicSite\AspirationController as PublicAspirationController;
use App\Http\Controllers\PublicSite\HomeController;
use App\Http\Controllers\PublicSite\PublicPageController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/tentang-kami', [PublicPageController::class, 'profile'])->name('public.profile');
Route::post('/aspirations', [PublicAspirationController::class, 'store'])->name('aspirations.store');
Route::get('/kegiatan', [PublicPageController::class, 'activities'])->name('public.activities');
Route::get('/galeri', [PublicPageController::class, 'galleries'])->name('public.galleries');
Route::get('/kegiatan/{activity}', [PublicPageController::class, 'activityShow'])->name('public.activities.show');
Route::get('/dosen-dan-staff', [PublicPageController::class, 'lecturerStaff'])->name('public.lecturer-staff');
Route::get('/kurikulum', [PublicPageController::class, 'curriculum'])->name('public.curriculum');
Route::get('/penelitian', [PublicPageController::class, 'research'])->name('public.research');
Route::get('/api/research/suggestions', [PublicPageController::class, 'researchSuggestions'])->name('public.research.suggestions');
Route::get('/pengabdian-masyarakat', [PublicPageController::class, 'communityService'])->name('public.community-service');
Route::get('/project-mahasiswa', [PublicPageController::class, 'projects'])->name('public.projects');
Route::get('/project-mahasiswa/{project}', [PublicPageController::class, 'projectShow'])->name('public.projects.show');
Route::get('/capaian-pembelajaran', [PublicPageController::class, 'learningOutcomes'])->name('public.learning-outcomes');
Route::get('/tracer-alumni', [PublicPageController::class, 'tracerAlumni'])->name('public.tracer-alumni');

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

        Route::resource('academic-calendars', AdminAcademicCalendarController::class)->except(['show']);

        // Hero Slides (Mapped to Banner Model internally)
        Route::post('hero-slides/reorder', [App\Http\Controllers\Admin\HeroSlideController::class, 'reorder'])->name('hero-slides.reorder');
        Route::resource('hero-slides', AdminHeroSlideController::class)->except(['show', 'create', 'edit']);


        Route::resource('activities', AdminActivityController::class)->except(['show']);
        // Galleries (AJAX-based, no separate create/edit pages)
        Route::post('galleries/reorder', [App\Http\Controllers\Admin\GalleryController::class, 'reorder'])->name('galleries.reorder');
        Route::resource('galleries', AdminGalleryController::class)->only(['index', 'store', 'update', 'destroy']);

        Route::resource('lecturer-staff', AdminLecturerStaffController::class)->except(['show']);
        Route::get('courses', [AdminCourseController::class, 'index'])->name('courses.index');
        Route::post('courses/sync', [AdminCourseController::class, 'sync'])->name('courses.sync');
        Route::get('research-community', [AdminResearchCommunitySyncController::class, 'index'])->name('research-community.index');
        Route::post('research-community/sync', [AdminResearchCommunitySyncController::class, 'sync'])->name('research-community.sync');
        Route::get('learning-outcomes', [App\Http\Controllers\Admin\LearningOutcomeController::class, 'index'])->name('learning-outcomes.index');
        Route::post('learning-outcomes/sync', [App\Http\Controllers\Admin\LearningOutcomeController::class, 'sync'])->name('learning-outcomes.sync');
        Route::resource('projects', AdminProjectController::class)->except(['show']);
        Route::post('projects/{project}/toggle-feature', [AdminProjectController::class, 'toggleFeature'])->name('projects.toggle-feature');
        Route::resource('tracer-alumni', AdminTracerAlumniController::class)
            ->parameters(['tracer-alumni' => 'tracerAlumni'])
            ->only(['index', 'destroy']);
        Route::post('tracer-alumni/sync', [AdminTracerAlumniController::class, 'sync'])->name('tracer-alumni.sync');
        Route::post('tracer-alumni/banner', [AdminTracerAlumniController::class, 'updateBanner'])->name('tracer-alumni.banner.update');
        Route::post('tracer-alumni/banner/reorder', [AdminTracerAlumniController::class, 'reorderBanner'])->name('tracer-alumni.banner.reorder');
        Route::delete('tracer-alumni/banner/{banner}', [AdminTracerAlumniController::class, 'destroyBanner'])->name('tracer-alumni.banner.destroy');

        Route::get('/profile', [AdminProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/profile', [AdminProfileController::class, 'update'])->name('profile.update');

        Route::resource('documents', \App\Http\Controllers\Admin\DocumentController::class)->except(['show']);

        Route::get('/aspirations', [AdminAspirationController::class, 'index'])->name('aspirations.index');
        Route::get('/aspirations/{aspiration}', [AdminAspirationController::class, 'show'])->name('aspirations.show');
        Route::patch('/aspirations/{aspiration}', [AdminAspirationController::class, 'update'])->name('aspirations.update');
        Route::delete('/aspirations/{aspiration}', [AdminAspirationController::class, 'destroy'])->name('aspirations.destroy');
    });
});
