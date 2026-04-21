<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ProjectController extends Controller
{
    public function index(Request $request): View
    {
        $search = (string) $request->query('q', '');

        $projects = Project::query()
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($inner) use ($search): void {
                    $inner
                        ->where('title', 'like', "%{$search}%")
                        ->orWhere('student_name', 'like', "%{$search}%")
                        ->orWhere('student_nim', 'like', "%{$search}%");
                });
            })
            ->latest('published_at')
            ->latest('id')
            ->paginate(12)
            ->withQueryString();

        return view('admin.projects.index', [
            'projects' => $projects,
            'search' => $search,
        ]);
    }

    public function create(): View
    {
        return view('admin.projects.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $payload = $this->validatePayload($request);
        $payload['slug'] = $this->generateUniqueSlug((string) ($payload['slug'] ?? $payload['title']));
        $payload['created_by'] = $request->user()?->id;

        $this->enforcePublishRules($payload);

        if (($payload['status'] ?? 'draft') === 'published' && empty($payload['published_at'])) {
            $payload['published_at'] = now();
        }

        Project::query()->create($payload);

        return redirect()
            ->route('admin.projects.index')
            ->with('success', 'Project mahasiswa berhasil ditambahkan.');
    }

    public function show(Project $project): RedirectResponse
    {
        return redirect()->route('admin.projects.edit', $project);
    }

    public function edit(Project $project): View
    {
        return view('admin.projects.edit', [
            'project' => $project,
        ]);
    }

    public function update(Request $request, Project $project): RedirectResponse
    {
        $payload = $this->validatePayload($request, $project);
        $payload['slug'] = $this->generateUniqueSlug(
            (string) ($payload['slug'] ?? $payload['title']),
            $project->id
        );

        $this->enforcePublishRules($payload);

        if (($payload['status'] ?? 'draft') === 'published' && empty($payload['published_at'])) {
            $payload['published_at'] = now();
        }

        $project->update($payload);

        return redirect()
            ->route('admin.projects.index')
            ->with('success', 'Project mahasiswa berhasil diperbarui.');
    }

    public function destroy(Project $project): RedirectResponse
    {
        if ($project->thumbnail !== null && Storage::disk('public')->exists($project->thumbnail)) {
            Storage::disk('public')->delete($project->thumbnail);
        }

        $project->delete();

        return redirect()
            ->route('admin.projects.index')
            ->with('success', 'Project mahasiswa berhasil dihapus.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validatePayload(Request $request, ?Project $project = null): array
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'alpha_dash'],
            'student_name' => ['required', 'string', 'max:255'],
            'student_nim' => ['nullable', 'string', 'max:30'],
            'year' => ['nullable', 'integer', 'min:2000', 'max:2100'],
            'summary' => ['nullable', 'string'],
            'demo_url' => ['nullable', 'url', 'max:2048'],
            'repository_url' => ['nullable', 'url', 'max:2048'],
            'thumbnail_file' => ['nullable', 'image', 'max:5120'],
            'status' => ['required', 'in:draft,published'],
            'is_featured' => ['nullable', 'boolean'],
            'published_at' => ['nullable', 'date'],
        ]);

        if ($request->hasFile('thumbnail_file')) {
            if ($project?->thumbnail !== null && Storage::disk('public')->exists($project->thumbnail)) {
                Storage::disk('public')->delete($project->thumbnail);
            }

            $validated['thumbnail'] = $request->file('thumbnail_file')?->store('projects', 'public');
        }

        unset($validated['thumbnail_file']);

        $validated['is_featured'] = (bool) ($validated['is_featured'] ?? false);

        return $validated;
    }

    /**
     * @param array<string, mixed> $payload
     */
    private function enforcePublishRules(array $payload): void
    {
        $status = (string) ($payload['status'] ?? 'draft');
        $hasPublishAt = !empty($payload['published_at']);

        if ($status === 'draft' && !$hasPublishAt) {
            throw ValidationException::withMessages([
                'published_at' => 'Waktu publish wajib diisi ketika status Draft.',
            ]);
        }
    }

    private function generateUniqueSlug(string $value, ?int $ignoreId = null): string
    {
        $base = Str::slug($value);
        if ($base === '') {
            $base = 'project-mahasiswa';
        }

        $slug = $base;
        $counter = 1;

        while (
            Project::query()
            ->when($ignoreId !== null, fn($query) => $query->where('id', '!=', $ignoreId))
            ->where('slug', $slug)
            ->exists()
        ) {
            $slug = $base . '-' . $counter;
            $counter++;
        }

        return $slug;
    }
}
