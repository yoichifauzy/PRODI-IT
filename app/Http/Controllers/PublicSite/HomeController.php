<?php

namespace App\Http\Controllers\PublicSite;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Gallery;
use App\Models\Banner;
use App\Models\Profile;
use Illuminate\Contracts\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $profile = Profile::first();

        $heroSlides = Banner::query()
            ->where('category', 'hero')
            ->orderBy('position')
            ->orderByDesc('id')
            ->get();

        $activities = Activity::query()
            ->visibleOnPublic()
            ->orderByDesc('event_date')
            ->orderByDesc('id')
            ->take(9)
            ->get();

        $runningActivities = Activity::query()
            ->upcomingRunningCard()
            ->get();

        // Galeri: ambil dari model baru (tanpa gallery_items)
        $galleryItems = Gallery::query()
            ->orderByDesc('year')
            ->orderByDesc('id')
            ->take(12)
            ->get()
            ->map(fn($item) => [
                'category'       => $item->category,
                'category_label' => $item->category,
                'title'          => $item->title,
                'caption'        => null,
                'image'          => asset('storage/' . $item->image_path),
            ])
            ->values();

        $galleryCategories = ['all' => 'Semua'];
        foreach ($galleryItems->pluck('category')->unique() as $cat) {
            $galleryCategories[$cat] = $cat;
        }



        return view('public.home', [
            'profile'             => $profile,
            'heroSlidesFromDb'    => $heroSlides,
            'activitiesFromDb'    => $activities,
            'runningActivities'   => $runningActivities,
            'galleryCategoriesFromDb' => $galleryCategories,
            'galleryItemsFromDb'  => $galleryItems,
        ]);
    }
}
