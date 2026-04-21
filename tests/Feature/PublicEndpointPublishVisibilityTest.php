<?php

namespace Tests\Feature;

use App\Models\Activity;
use App\Models\Announcement;
use App\Models\Project;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicEndpointPublishVisibilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_project_endpoints_only_show_due_items(): void
    {
        $visibleProject = Project::query()->create([
            'title' => 'Project Visible',
            'slug' => 'project-visible',
            'student_name' => 'Mahasiswa Visible',
            'status' => 'published',
            'published_at' => Carbon::now()->subMinutes(5),
        ]);

        $hiddenProject = Project::query()->create([
            'title' => 'Project Hidden',
            'slug' => 'project-hidden',
            'student_name' => 'Mahasiswa Hidden',
            'status' => 'published',
            'published_at' => Carbon::now()->addMinutes(5),
        ]);

        $listResponse = $this->get(route('public.projects'));

        $listResponse->assertOk();
        $listResponse->assertSee('Project Visible');
        $listResponse->assertDontSee('Project Hidden');

        $this->get(route('public.projects.show', $visibleProject))->assertOk();
        $this->get(route('public.projects.show', $hiddenProject))->assertNotFound();
    }

    public function test_activity_endpoints_only_show_due_items(): void
    {
        $visibleActivity = Activity::query()->create([
            'category' => 'Visible',
            'title' => 'Activity Visible',
            'event_date' => Carbon::now()->toDateString(),
            'is_published' => true,
            'published_at' => Carbon::now()->subMinutes(5),
        ]);

        $hiddenActivity = Activity::query()->create([
            'category' => 'Hidden',
            'title' => 'Activity Hidden',
            'event_date' => Carbon::now()->toDateString(),
            'is_published' => true,
            'published_at' => Carbon::now()->addMinutes(5),
        ]);

        $listResponse = $this->get(route('public.activities'));

        $listResponse->assertOk();
        $listResponse->assertSee('Activity Visible');
        $listResponse->assertDontSee('Activity Hidden');

        $this->get(route('public.activities.show', $visibleActivity))->assertOk();
        $this->get(route('public.activities.show', $hiddenActivity))->assertNotFound();
    }

    public function test_announcement_endpoints_only_show_due_items(): void
    {
        Announcement::query()->create([
            'title' => 'Announcement Visible Published',
            'slug' => 'announcement-visible-published',
            'content' => 'Visible content',
            'status' => 'published',
            'published_at' => Carbon::now()->subMinutes(5),
        ]);

        Announcement::query()->create([
            'title' => 'Announcement Visible Draft Due',
            'slug' => 'announcement-visible-draft-due',
            'content' => 'Visible draft due content',
            'status' => 'draft',
            'published_at' => Carbon::now()->subMinutes(5),
        ]);

        Announcement::query()->create([
            'title' => 'Announcement Hidden Future',
            'slug' => 'announcement-hidden-future',
            'content' => 'Hidden content',
            'status' => 'published',
            'published_at' => Carbon::now()->addMinutes(5),
        ]);

        $listResponse = $this->get(route('public.announcements'));

        $listResponse->assertOk();
        $listResponse->assertSee('Announcement Visible Published');
        $listResponse->assertSee('Announcement Visible Draft Due');
        $listResponse->assertDontSee('Announcement Hidden Future');

        $syncResponse = $this->get(route('public.announcements.sync'));

        $syncResponse->assertOk();
        $syncResponse->assertJsonPath('count', 2);
    }
}
