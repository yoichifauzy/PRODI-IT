<?php

namespace Tests\Feature;

use App\Models\Activity;
use App\Models\Announcement;
use App\Models\Gallery;
use App\Models\GalleryItem;
use App\Models\Project;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublishVisibilityScopesTest extends TestCase
{
    use RefreshDatabase;

    public function test_announcement_published_scope_includes_due_scheduled_drafts(): void
    {
        $visiblePublished = $this->createAnnouncement('published', null);
        $visibleScheduledDraft = $this->createAnnouncement('draft', Carbon::now()->subMinutes(10));
        $hiddenFutureDraft = $this->createAnnouncement('draft', Carbon::now()->addMinutes(10));
        $hiddenFuturePublished = $this->createAnnouncement('published', Carbon::now()->addMinutes(10));

        $visibleIds = Announcement::query()->published()->pluck('id')->all();

        $this->assertContains($visiblePublished->id, $visibleIds);
        $this->assertContains($visibleScheduledDraft->id, $visibleIds);
        $this->assertNotContains($hiddenFutureDraft->id, $visibleIds);
        $this->assertNotContains($hiddenFuturePublished->id, $visibleIds);
    }

    public function test_project_visible_scope_follows_publish_rules(): void
    {
        $visiblePublishedNoTime = $this->createProject('published', null);
        $visibleDraftDue = $this->createProject('draft', Carbon::now()->subHour());
        $hiddenDraftNoTime = $this->createProject('draft', null);
        $hiddenPublishedFuture = $this->createProject('published', Carbon::now()->addHour());

        $visibleIds = Project::query()->visibleOnPublic()->pluck('id')->all();

        $this->assertContains($visiblePublishedNoTime->id, $visibleIds);
        $this->assertContains($visibleDraftDue->id, $visibleIds);
        $this->assertNotContains($hiddenDraftNoTime->id, $visibleIds);
        $this->assertNotContains($hiddenPublishedFuture->id, $visibleIds);
    }

    public function test_activity_visible_scope_requires_public_flag_and_due_schedule(): void
    {
        $visibleNoSchedule = $this->createActivity(true, null);
        $visibleDueSchedule = $this->createActivity(true, Carbon::now()->subMinutes(5));
        $hiddenFutureSchedule = $this->createActivity(true, Carbon::now()->addMinutes(5));
        $hiddenByFlag = $this->createActivity(false, Carbon::now()->subMinutes(5));

        $visibleIds = Activity::query()->visibleOnPublic()->pluck('id')->all();

        $this->assertContains($visibleNoSchedule->id, $visibleIds);
        $this->assertContains($visibleDueSchedule->id, $visibleIds);
        $this->assertNotContains($hiddenFutureSchedule->id, $visibleIds);
        $this->assertNotContains($hiddenByFlag->id, $visibleIds);
    }

    public function test_gallery_item_visible_scope_shows_null_or_due_publish_time(): void
    {
        $gallery = Gallery::query()->create([
            'name' => 'Galeri Test',
            'slug' => 'galeri-test',
            'status' => 'published',
            'published_at' => Carbon::now()->subDay(),
        ]);

        $visibleNoSchedule = GalleryItem::query()->create([
            'gallery_id' => $gallery->id,
            'title' => 'No Schedule',
            'image_path' => 'gallery-items/no-schedule.jpg',
            'published_at' => null,
        ]);

        $visibleDueSchedule = GalleryItem::query()->create([
            'gallery_id' => $gallery->id,
            'title' => 'Due Schedule',
            'image_path' => 'gallery-items/due.jpg',
            'published_at' => Carbon::now()->subMinutes(15),
        ]);

        $hiddenFutureSchedule = GalleryItem::query()->create([
            'gallery_id' => $gallery->id,
            'title' => 'Future Schedule',
            'image_path' => 'gallery-items/future.jpg',
            'published_at' => Carbon::now()->addMinutes(15),
        ]);

        $visibleIds = GalleryItem::query()->visibleOnPublic()->pluck('id')->all();

        $this->assertContains($visibleNoSchedule->id, $visibleIds);
        $this->assertContains($visibleDueSchedule->id, $visibleIds);
        $this->assertNotContains($hiddenFutureSchedule->id, $visibleIds);
    }

    private function createAnnouncement(string $status, mixed $publishedAt): Announcement
    {
        return Announcement::query()->create([
            'title' => 'Announcement ' . uniqid(),
            'slug' => 'announcement-' . uniqid(),
            'content' => 'Content',
            'status' => $status,
            'published_at' => $publishedAt,
        ]);
    }

    private function createProject(string $status, mixed $publishedAt): Project
    {
        return Project::query()->create([
            'title' => 'Project ' . uniqid(),
            'slug' => 'project-' . uniqid(),
            'student_name' => 'Mahasiswa',
            'status' => $status,
            'published_at' => $publishedAt,
        ]);
    }

    private function createActivity(bool $isPublished, mixed $publishedAt): Activity
    {
        return Activity::query()->create([
            'category' => 'Test',
            'title' => 'Activity ' . uniqid(),
            'event_date' => Carbon::now()->toDateString(),
            'is_published' => $isPublished,
            'published_at' => $publishedAt,
        ]);
    }
}
