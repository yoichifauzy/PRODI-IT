<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicEvent;
use App\Models\Announcement;
use App\Models\Aspiration;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $stats = [
            'announcement_total' => Announcement::query()->count(),
            'announcement_published' => Announcement::query()->where('status', 'published')->count(),
            'aspiration_total' => Aspiration::query()->count(),
            'aspiration_unread' => Aspiration::query()->where('status', 'unread')->count(),
            'event_total' => AcademicEvent::query()->count(),
            'event_published' => AcademicEvent::query()->where('is_published', true)->count(),
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
