<?php

namespace Tests\Feature;

use App\Models\Gallery;
use App\Models\GalleryItem;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicGalleryDetailTest extends TestCase
{
    use RefreshDatabase;

    public function test_gallery_list_cards_link_to_detail_page(): void
    {
        $gallery = Gallery::query()->create([
            'name' => 'Kegiatan Kampus',
            'slug' => 'kegiatan-kampus',
            'status' => 'published',
            'published_at' => Carbon::now()->subMinutes(10),
        ]);

        $item = GalleryItem::query()->create([
            'gallery_id' => $gallery->id,
            'title' => 'Dokumentasi Kegiatan',
            'caption' => 'Caption galeri',
            'image_path' => 'gallery/test-image.jpg',
            'taken_at' => Carbon::now()->toDateString(),
            'published_at' => Carbon::now()->subMinutes(5),
        ]);

        $listResponse = $this->get(route('public.galleries'));

        $listResponse->assertOk();
        $listResponse->assertSee(route('public.galleries.show', $item), false);

        $detailResponse = $this->get(route('public.galleries.show', $item));

        $detailResponse->assertOk();
        $detailResponse->assertDontSee('Published At');
        $detailResponse->assertDontSee('Tanggal Publish');
        $detailResponse->assertDontSee('Detail Link');
        $detailResponse->assertDontSee('Tautan Detail');
    }
}
