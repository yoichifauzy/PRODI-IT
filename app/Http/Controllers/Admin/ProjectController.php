<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProjectController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));

        $projects = Project::query()
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($inner) use ($search): void {
                    $inner
                        ->where('title', 'like', "%{$search}%")
                        ->orWhere('student_name', 'like', "%{$search}%")
                        ->orWhere('student_nim', 'like', "%{$search}%");
                });
            })
            ->orderByDesc('is_feature')
            ->orderByDesc('year')
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        return view('admin.projects.index', [
            'projects' => $projects,
            'search'   => $search,
        ]);
    }

    public function create(): View
    {
        return view('admin.projects.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $payload = $this->validatePayload($request);
        $payload['created_by'] = Auth::id();

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
        return view('admin.projects.edit', ['project' => $project]);
    }

    public function update(Request $request, Project $project): RedirectResponse
    {
        $payload = $this->validatePayload($request, $project);
        $project->update($payload);

        return redirect()
            ->route('admin.projects.index')
            ->with('success', 'Project mahasiswa berhasil diperbarui.');
    }

    /**
     * Toggle is_feature via AJAX — dipanggil dari tombol bintang di card.
     */
    public function toggleFeature(Project $project): JsonResponse
    {
        $project->update(['is_feature' => !$project->is_feature]);

        return response()->json([
            'success'    => true,
            'is_feature' => $project->is_feature,
        ]);
    }

    public function destroy(Project $project): RedirectResponse
    {
        if ($project->image_path !== null && Storage::disk('public')->exists($project->image_path)) {
            Storage::disk('public')->delete($project->image_path);
        }

        $project->delete();

        return redirect()
            ->route('admin.projects.index')
            ->with('success', 'Project mahasiswa berhasil dihapus.');
    }

    /** @return array<string, mixed> */
    private function validatePayload(Request $request, ?Project $project = null): array
    {
        $validated = $request->validate([
            'title'        => ['required', 'string', 'max:255'],
            'student_name' => ['required', 'string', 'max:255'],
            'student_nim'  => ['nullable', 'string', 'max:30'],
            'year'         => ['nullable', 'integer', 'min:2000', 'max:2100'],
            'description'  => ['nullable', 'string'],
            'image_file'   => ['nullable', 'image', 'max:5120'],
            'is_feature'   => ['nullable', 'boolean'],
        ]);

        if ($request->hasFile('image_file')) {
            if ($project?->image_path !== null && Storage::disk('public')->exists($project->image_path)) {
                Storage::disk('public')->delete($project->image_path);
            }
            $validated['image_path'] = $request->file('image_file')?->store('projects', 'public');
        }

        unset($validated['image_file']);
        $validated['is_feature'] = (bool) ($validated['is_feature'] ?? false);

        return $validated;
    }
}
