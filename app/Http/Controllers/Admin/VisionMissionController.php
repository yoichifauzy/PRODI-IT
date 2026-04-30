<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
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
}
