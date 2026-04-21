<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LecturerStaff;
use App\Models\LecturerStaffBlog;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class LecturerStaffBlogController extends Controller
{
    public function index(LecturerStaff $lecturerStaff): View
    {
        $blogs = $lecturerStaff->blogs()
            ->orderByDesc('activity_date')
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->paginate(12);

        return view('admin.lecturer-staff-blogs.index', [
            'lecturerStaff' => $lecturerStaff,
            'blogs' => $blogs,
        ]);
    }

    public function create(LecturerStaff $lecturerStaff): View
    {
        return view('admin.lecturer-staff-blogs.create', [
            'lecturerStaff' => $lecturerStaff,
            'blog' => new LecturerStaffBlog(),
        ]);
    }

    public function store(Request $request, LecturerStaff $lecturerStaff): RedirectResponse
    {
        $payload = $this->validatePayload($request);
        $payload['lecturer_staff_id'] = $lecturerStaff->id;
        $payload['slug'] = $this->generateUniqueSlug((string) ($payload['slug'] ?? $payload['title']));

        LecturerStaffBlog::query()->create($payload);

        return redirect()
            ->route('admin.lecturer-staff.blogs.index', $lecturerStaff)
            ->with('success', 'Blog dosen/staff berhasil ditambahkan.');
    }

    public function edit(LecturerStaff $lecturerStaff, LecturerStaffBlog $blog): View
    {
        $this->ensureOwnership($lecturerStaff, $blog);

        return view('admin.lecturer-staff-blogs.edit', [
            'lecturerStaff' => $lecturerStaff,
            'blog' => $blog,
        ]);
    }

    public function update(Request $request, LecturerStaff $lecturerStaff, LecturerStaffBlog $blog): RedirectResponse
    {
        $this->ensureOwnership($lecturerStaff, $blog);

        $payload = $this->validatePayload($request, $blog);
        $payload['slug'] = $this->generateUniqueSlug((string) ($payload['slug'] ?? $payload['title']), $blog->id);

        $blog->update($payload);

        return redirect()
            ->route('admin.lecturer-staff.blogs.index', $lecturerStaff)
            ->with('success', 'Blog dosen/staff berhasil diperbarui.');
    }

    public function destroy(LecturerStaff $lecturerStaff, LecturerStaffBlog $blog): RedirectResponse
    {
        $this->ensureOwnership($lecturerStaff, $blog);

        if ($blog->cover_image !== null && Storage::disk('public')->exists($blog->cover_image)) {
            Storage::disk('public')->delete($blog->cover_image);
        }

        $blog->delete();

        return redirect()
            ->route('admin.lecturer-staff.blogs.index', $lecturerStaff)
            ->with('success', 'Blog dosen/staff berhasil dihapus.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validatePayload(Request $request, ?LecturerStaffBlog $blog = null): array
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'alpha_dash'],
            'description' => ['nullable', 'string'],
            'location' => ['nullable', 'string', 'max:255'],
            'activity_date' => ['nullable', 'date'],
            'cover_image_file' => ['nullable', 'image', 'max:5120'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'is_published' => ['nullable', 'boolean'],
        ]);

        if ($request->hasFile('cover_image_file')) {
            if ($blog?->cover_image !== null && Storage::disk('public')->exists($blog->cover_image)) {
                Storage::disk('public')->delete($blog->cover_image);
            }

            $validated['cover_image'] = $request->file('cover_image_file')?->store('lecturer-staff-blogs', 'public');
        } elseif ($blog !== null) {
            $validated['cover_image'] = $blog->cover_image;
        }

        unset($validated['cover_image_file']);

        $validated['sort_order'] = (int) ($validated['sort_order'] ?? 0);
        $validated['is_published'] = (bool) ($validated['is_published'] ?? false);

        return $validated;
    }

    private function ensureOwnership(LecturerStaff $lecturerStaff, LecturerStaffBlog $blog): void
    {
        abort_if($blog->lecturer_staff_id !== $lecturerStaff->id, 404);
    }

    private function generateUniqueSlug(string $value, ?int $ignoreId = null): string
    {
        $base = Str::slug($value);
        if ($base === '') {
            $base = 'blog-dosen';
        }

        $slug = $base;
        $counter = 1;

        while (
            LecturerStaffBlog::query()
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
