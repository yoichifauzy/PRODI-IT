<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LecturerStaff;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LecturerStaffController extends Controller
{
    public function index(Request $request): View
    {
        $type = (string) $request->query('type', '');

        $members = LecturerStaff::query()
            ->when(in_array($type, LecturerStaff::TYPES, true), fn($query) => $query->where('type', $type))
            ->orderBy('type')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('admin.lecturer-staff.index', [
            'members' => $members,
            'type' => $type,
            'types' => LecturerStaff::TYPES,
        ]);
    }

    public function create(): View
    {
        return view('admin.lecturer-staff.create', [
            'types' => LecturerStaff::TYPES,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $payload = $this->validatePayload($request);

        LecturerStaff::query()->create($payload);

        return redirect()
            ->route('admin.lecturer-staff.index')
            ->with('success', 'Data dosen/staff berhasil ditambahkan.');
    }

    public function show(LecturerStaff $lecturerStaff): RedirectResponse
    {
        return redirect()->route('admin.lecturer-staff.edit', $lecturerStaff);
    }

    public function edit(LecturerStaff $lecturerStaff): View
    {
        return view('admin.lecturer-staff.edit', [
            'lecturerStaff' => $lecturerStaff,
            'types' => LecturerStaff::TYPES,
        ]);
    }

    public function update(Request $request, LecturerStaff $lecturerStaff): RedirectResponse
    {
        $payload = $this->validatePayload($request, $lecturerStaff);
        $lecturerStaff->update($payload);

        return redirect()
            ->route('admin.lecturer-staff.index')
            ->with('success', 'Data dosen/staff berhasil diperbarui.');
    }

    public function destroy(LecturerStaff $lecturerStaff): RedirectResponse
    {
        if ($lecturerStaff->photo_path !== null && Storage::disk('public')->exists($lecturerStaff->photo_path)) {
            Storage::disk('public')->delete($lecturerStaff->photo_path);
        }

        $lecturerStaff->blogs()
            ->whereNotNull('cover_image')
            ->pluck('cover_image')
            ->each(function ($coverImage): void {
                if (Storage::disk('public')->exists((string) $coverImage)) {
                    Storage::disk('public')->delete((string) $coverImage);
                }
            });

        $lecturerStaff->delete();

        return redirect()
            ->route('admin.lecturer-staff.index')
            ->with('success', 'Data dosen/staff berhasil dihapus.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validatePayload(Request $request, ?LecturerStaff $lecturerStaff = null): array
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'position' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:' . implode(',', LecturerStaff::TYPES)],
            'email' => ['nullable', 'email', 'max:255'],
            'bio' => ['nullable', 'string'],
            'photo' => ['nullable', 'image', 'max:5120'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        if ($request->hasFile('photo')) {
            if ($lecturerStaff?->photo_path !== null && Storage::disk('public')->exists($lecturerStaff->photo_path)) {
                Storage::disk('public')->delete($lecturerStaff->photo_path);
            }

            $validated['photo_path'] = $request->file('photo')?->store('lecturer-staff', 'public');
        }

        unset($validated['photo']);

        $validated['sort_order'] = (int) ($validated['sort_order'] ?? 0);
        $validated['is_active'] = (bool) ($validated['is_active'] ?? false);

        return $validated;
    }
}
