<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Aspiration;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $stats = [
            'aspiration_total' => Aspiration::query()->count(),
            'aspiration_unread' => Aspiration::query()->where('status', 'unread')->count(),
            'project_total' => \App\Models\Project::query()->count(),
            'project_unggulan' => \App\Models\Project::query()->where('is_feature', true)->count(),
            'lecturer_total' => \App\Models\LecturerStaff::query()->where('is_active', true)->count(),
            'alumni_total' => \App\Models\TracerAlumni::query()->count(),
            'course_total' => \App\Models\Course::query()->count(),
            'gallery_total' => \App\Models\Gallery::query()->count(),
        ];

        $latestAspirations = Aspiration::query()
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', [
            'stats' => $stats,
            'latestAspirations' => $latestAspirations,
        ]);
    }
}
