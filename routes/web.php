<?php

use App\Http\Controllers\Admin\AnnouncementController as AdminAnnouncementController;
use App\Http\Controllers\Admin\ActivityController as AdminActivityController;
use App\Http\Controllers\Admin\AboutSectionController as AdminAboutSectionController;
use App\Http\Controllers\Admin\AspirationController as AdminAspirationController;
use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\CurriculumController as AdminCurriculumController;
use App\Http\Controllers\Admin\CurriculumCourseController as AdminCurriculumCourseController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\GalleryController as AdminGalleryController;
use App\Http\Controllers\Admin\GalleryItemController as AdminGalleryItemController;
use App\Http\Controllers\Admin\HeroSlideController as AdminHeroSlideController;
use App\Http\Controllers\Admin\LecturerStaffController as AdminLecturerStaffController;
use App\Http\Controllers\Admin\LecturerStaffBlogController as AdminLecturerStaffBlogController;
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
Route::get('/kalender-akademik', [AcademicCalendarController::class, 'index'])->name('calendar.index');
Route::post('/aspirations', [PublicAspirationController::class, 'store'])->name('aspirations.store');
Route::get('/kegiatan', [PublicPageController::class, 'activities'])->name('public.activities');
Route::get('/kegiatan/{activity}', [PublicPageController::class, 'activityShow'])->name('public.activities.show');
Route::get('/dosen-dan-staff', [PublicPageController::class, 'lecturerStaff'])->name('public.lecturer-staff');
Route::get('/dosen-dan-staff/{lecturerStaff}', [PublicPageController::class, 'lecturerStaffBlogs'])->name('public.lecturer-staff.blogs');
Route::get('/kurikulum', [PublicPageController::class, 'curriculum'])->name('public.curriculum');
Route::get('/project-mahasiswa', [PublicPageController::class, 'projects'])->name('public.projects');
Route::get('/project-mahasiswa/{project:slug}', [PublicPageController::class, 'projectShow'])->name('public.projects.show');
Route::get('/tracer-alumni', [PublicPageController::class, 'tracerAlumni'])->name('public.tracer-alumni');
Route::get('/pengumuman', [PublicPageController::class, 'announcements'])->name('public.announcements');
Route::get('/pengumuman/sync', [PublicPageController::class, 'announcementsSync'])->name('public.announcements.sync');

Route::prefix('admin')->name('admin.')->group(function (): void {
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
        Route::resource('vision-missions', AdminVisionMissionController::class)->except(['show']);
        Route::resource('hero-slides', AdminHeroSlideController::class)->except(['show']);
        Route::resource('activities', AdminActivityController::class)->except(['show']);
        Route::resource('galleries', AdminGalleryController::class)->except(['show']);
        Route::resource('gallery-items', AdminGalleryItemController::class)->except(['show']);
        Route::resource('lecturer-staff', AdminLecturerStaffController::class)->except(['show']);
        Route::prefix('lecturer-staff/{lecturerStaff}/blogs')->name('lecturer-staff.blogs.')->group(function (): void {
            Route::get('/', [AdminLecturerStaffBlogController::class, 'index'])->name('index');
            Route::get('/create', [AdminLecturerStaffBlogController::class, 'create'])->name('create');
            Route::post('/', [AdminLecturerStaffBlogController::class, 'store'])->name('store');
            Route::get('/{blog}/edit', [AdminLecturerStaffBlogController::class, 'edit'])->name('edit');
            Route::put('/{blog}', [AdminLecturerStaffBlogController::class, 'update'])->name('update');
            Route::delete('/{blog}', [AdminLecturerStaffBlogController::class, 'destroy'])->name('destroy');
        });
        Route::resource('curricula', AdminCurriculumController::class)->except(['show']);
        Route::resource('curriculum-courses', AdminCurriculumCourseController::class)->except(['show']);
        Route::resource('projects', AdminProjectController::class)->except(['show']);
        Route::resource('tracer-alumni', AdminTracerAlumniController::class)
            ->parameters(['tracer-alumni' => 'tracerAlumni'])
            ->except(['show']);

        Route::get('/about-section', [AdminAboutSectionController::class, 'edit'])->name('about-section.edit');
        Route::put('/about-section', [AdminAboutSectionController::class, 'update'])->name('about-section.update');

        Route::get('/aspirations', [AdminAspirationController::class, 'index'])->name('aspirations.index');
        Route::get('/aspirations/{aspiration}', [AdminAspirationController::class, 'show'])->name('aspirations.show');
        Route::patch('/aspirations/{aspiration}', [AdminAspirationController::class, 'update'])->name('aspirations.update');
        Route::delete('/aspirations/{aspiration}', [AdminAspirationController::class, 'destroy'])->name('aspirations.destroy');
    });
});
