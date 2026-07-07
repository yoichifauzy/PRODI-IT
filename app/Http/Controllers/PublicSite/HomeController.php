<?php

namespace App\Http\Controllers\PublicSite;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Gallery;
use App\Models\GalleryItem;
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

        $galleries = Gallery::query()
            ->where('status', 'published')
            ->where(function ($query): void {
                $query->whereNull('published_at')->orWhere('published_at', '<=', now());
            })
            ->orderBy('name')
            ->get();

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
            ->orderByRaw('CASE WHEN sort_order IS NULL OR sort_order = 0 THEN 9999 ELSE sort_order END')
            ->orderByDesc('taken_at')
            ->orderByDesc('id')
            ->get()
            ->map(function (GalleryItem $item): array {
                $gallery = $item->gallery;

                return [
                    'category' => $gallery?->slug ?? 'all',
                    'category_label' => $gallery?->name ?? 'Galeri',
                    'title' => $item->title ?: ($gallery?->name ?? 'Galeri'),
                    'caption' => $item->caption,
                    'image' => asset('storage/' . $item->image_path),
                ];
            })
            ->values();

        $galleryCategories = ['all' => 'Semua'];
        foreach ($galleries as $gallery) {
            $galleryCategories[$gallery->slug] = $gallery->name;
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
