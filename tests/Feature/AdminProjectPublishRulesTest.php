<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminProjectPublishRulesTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_cannot_create_draft_project_without_publish_time(): void
    {
        $admin = $this->createAdminUser();

        $response = $this->actingAs($admin)->post(route('admin.projects.store'), [
            'title' => 'Draft Tanpa Jadwal',
            'student_name' => 'Mahasiswa A',
            'status' => 'draft',
            'published_at' => null,
        ]);

        $response->assertSessionHasErrors(['published_at']);
        $this->assertDatabaseMissing('projects', [
            'title' => 'Draft Tanpa Jadwal',
        ]);
    }

    public function test_admin_can_create_published_project_without_publish_time_and_it_is_auto_filled(): void
    {
        $admin = $this->createAdminUser();

        $response = $this->actingAs($admin)->post(route('admin.projects.store'), [
            'title' => 'Published Langsung',
            'student_name' => 'Mahasiswa B',
            'status' => 'published',
            'published_at' => null,
        ]);

        $response->assertRedirect(route('admin.projects.index'));

        $project = Project::query()->where('title', 'Published Langsung')->first();

        $this->assertNotNull($project);
        $this->assertSame('published', $project->status);
        $this->assertNotNull($project->published_at);
    }

    public function test_admin_cannot_update_project_to_draft_without_publish_time(): void
    {
        $admin = $this->createAdminUser();

        $project = Project::query()->create([
            'title' => 'Project Awal',
            'slug' => 'project-awal',
            'student_name' => 'Mahasiswa C',
            'status' => 'published',
            'published_at' => now(),
        ]);

        $response = $this->actingAs($admin)->put(route('admin.projects.update', $project), [
            'title' => 'Project Awal',
            'slug' => 'project-awal',
            'student_name' => 'Mahasiswa C',
            'status' => 'draft',
            'published_at' => null,
        ]);

        $response->assertSessionHasErrors(['published_at']);

        $project->refresh();
        $this->assertSame('published', $project->status);
    }

    private function createAdminUser(): User
    {
        return User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
        ]);
    }
}
