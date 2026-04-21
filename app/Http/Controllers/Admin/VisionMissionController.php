<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreVisionMissionRequest;
use App\Http\Requests\Admin\UpdateVisionMissionRequest;
use App\Models\VisionMission;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class VisionMissionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $visionMissions = VisionMission::query()
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.vision-missions.index', [
            'visionMissions' => $visionMissions,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('admin.vision-missions.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreVisionMissionRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['is_active'] = (bool) ($data['is_active'] ?? false);
        $data['created_by'] = $request->user()?->id;
        $data['updated_by'] = $request->user()?->id;

        if ($data['is_active']) {
            VisionMission::query()->update(['is_active' => false]);
        }

        VisionMission::query()->create($data);

        return redirect()
            ->route('admin.vision-missions.index')
            ->with('success', 'Data visi dan misi berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(VisionMission $visionMission): RedirectResponse
    {
        return redirect()->route('admin.vision-missions.edit', $visionMission);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(VisionMission $visionMission): View
    {
        return view('admin.vision-missions.edit', [
            'visionMission' => $visionMission,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateVisionMissionRequest $request, VisionMission $visionMission): RedirectResponse
    {
        $data = $request->validated();
        $data['is_active'] = (bool) ($data['is_active'] ?? false);
        $data['updated_by'] = $request->user()?->id;

        if ($data['is_active']) {
            VisionMission::query()
                ->where('id', '!=', $visionMission->id)
                ->update(['is_active' => false]);
        }

        $visionMission->update($data);

        return redirect()
            ->route('admin.vision-missions.index')
            ->with('success', 'Data visi dan misi berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(VisionMission $visionMission): RedirectResponse
    {
        $visionMission->delete();

        return redirect()
            ->route('admin.vision-missions.index')
            ->with('success', 'Data visi dan misi berhasil dihapus.');
    }
}
