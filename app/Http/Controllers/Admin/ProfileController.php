<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Profile;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function edit(): View
    {
        $profile = Profile::first();

        // If no profile exists, create an empty one to simplify the view logic
        if (!$profile) {
            $profile = Profile::create();
        }

        return view('admin.profile.edit', [
            'profile' => $profile,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'description_primary' => ['nullable', 'string'],
            'description_secondary' => ['nullable', 'string'],
            'vision_text' => ['nullable', 'string'],
            'mission_text' => ['nullable', 'string'],
            'image_one_path' => ['nullable', 'image', 'max:5120'],
            'image_two_path' => ['nullable', 'image', 'max:5120'],
            'video_path' => ['nullable', 'file', 'mimes:mp4,mov,webm', 'max:51200'],
        ]);

        $profile = Profile::first();
        if (!$profile) {
            $profile = new Profile();
        }

        $profile->description_primary = $validated['description_primary'] ?? null;
        $profile->description_secondary = $validated['description_secondary'] ?? null;
        $profile->vision_text = $validated['vision_text'] ?? null;
        
        $missionInput = $validated['mission_text'] ?? null;
        if (filled($missionInput)) {
            $missionItems = preg_split('/\r\n|\r|\n/', trim($missionInput));
            $profile->mission_text = array_values(array_filter(array_map('trim', $missionItems)));
        } else {
            $profile->mission_text = null;
        }

        if ($request->hasFile('image_one_path')) {
            if ($profile->image_one_path && Storage::disk('public')->exists($profile->image_one_path)) {
                Storage::disk('public')->delete($profile->image_one_path);
            }
            $profile->image_one_path = $request->file('image_one_path')->store('profile', 'public');
        }

        if ($request->hasFile('image_two_path')) {
            if ($profile->image_two_path && Storage::disk('public')->exists($profile->image_two_path)) {
                Storage::disk('public')->delete($profile->image_two_path);
            }
            $profile->image_two_path = $request->file('image_two_path')->store('profile', 'public');
        }

        if ($request->hasFile('video_path')) {
            if ($profile->video_path && Storage::disk('public')->exists($profile->video_path)) {
                Storage::disk('public')->delete($profile->video_path);
            }
            $profile->video_path = $request->file('video_path')->store('profile/videos', 'public');
        }

        $profile->save();

        return redirect()
            ->route('admin.profile.edit')
            ->with('success', 'Profil dan Visi Misi berhasil diperbarui.');
    }
}
