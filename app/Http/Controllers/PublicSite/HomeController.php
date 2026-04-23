<?php

namespace App\Http\Controllers\PublicSite;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Gallery;
use App\Models\GalleryItem;
use App\Models\HeroSlide;
use App\Models\Setting;
use App\Models\VisionMission;
use Illuminate\Contracts\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $visionMission = VisionMission::query()
            ->where('is_active', true)
            ->latest('updated_at')
            ->first();

        if ($visionMission === null) {
            $visionMission = VisionMission::query()->latest()->first();
        }

        $heroSlides = HeroSlide::query()
            ->where('is_active', true)
            ->where(function ($query): void {
                $query->whereNull('start_at')->orWhere('start_at', '<=', now());
            })
            ->where(function ($query): void {
                $query->whereNull('end_at')->orWhere('end_at', '>=', now());
            })
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->get();

        $activities = Activity::query()
            ->visibleOnPublic()
            ->orderByRaw('CASE WHEN sort_order IS NULL OR sort_order = 0 THEN 9999 ELSE sort_order END')
            ->orderByDesc('event_date')
            ->orderByDesc('id')
            ->take(9)
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
            ->pluck('value', 'key');

        return view('public.home', [
            'visionMission' => $visionMission,
            'heroSlidesFromDb' => $heroSlides,
            'activitiesFromDb' => $activities,
            'galleryCategoriesFromDb' => $galleryCategories,
            'galleryItemsFromDb' => $galleryItems,
            'aboutSettings' => $aboutSettings,
        ]);
    }
}
