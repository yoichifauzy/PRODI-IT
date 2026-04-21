<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AboutSectionController extends Controller
{
    public function edit(): View
    {
        $keys = [
            'about_section_title',
            'about_section_subtitle',
            'about_heading',
            'about_description_primary',
            'about_description_secondary',
            'about_image_one',
            'about_image_two',
            'about_video_path',
        ];

        $settings = Setting::query()
            ->whereIn('key', $keys)
            ->pluck('value', 'key');

        return view('admin.about-section.edit', [
            'settings' => $settings,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'about_section_title' => ['nullable', 'string', 'max:255'],
            'about_section_subtitle' => ['nullable', 'string', 'max:255'],
            'about_heading' => ['nullable', 'string', 'max:255'],
            'about_description_primary' => ['nullable', 'string'],
            'about_description_secondary' => ['nullable', 'string'],
            'about_image_one' => ['nullable', 'image', 'max:5120'],
            'about_image_two' => ['nullable', 'image', 'max:5120'],
            'about_video_file' => ['nullable', 'file', 'mimes:mp4,mov,webm', 'max:51200'],
        ]);

        $this->upsert('about_section_title', (string) ($validated['about_section_title'] ?? ''), 'string', 'about');
        $this->upsert('about_section_subtitle', (string) ($validated['about_section_subtitle'] ?? ''), 'string', 'about');
        $this->upsert('about_heading', (string) ($validated['about_heading'] ?? ''), 'string', 'about');
        $this->upsert('about_description_primary', (string) ($validated['about_description_primary'] ?? ''), 'text', 'about');
        $this->upsert('about_description_secondary', (string) ($validated['about_description_secondary'] ?? ''), 'text', 'about');

        if ($request->hasFile('about_image_one')) {
            $this->replaceFileSetting('about_image_one', $request->file('about_image_one')?->store('about', 'public'));
        }

        if ($request->hasFile('about_image_two')) {
            $this->replaceFileSetting('about_image_two', $request->file('about_image_two')?->store('about', 'public'));
        }

        if ($request->hasFile('about_video_file')) {
            $this->replaceFileSetting('about_video_path', $request->file('about_video_file')?->store('about/videos', 'public'));
        }

        return redirect()
            ->route('admin.about-section.edit')
            ->with('success', 'Konten section Tentang Kami berhasil diperbarui.');
    }

    private function upsert(string $key, string $value, string $type, string $group): void
    {
        Setting::query()->updateOrCreate(
            ['key' => $key],
            ['value' => $value, 'type' => $type, 'group' => $group]
        );
    }

    private function replaceFileSetting(string $key, ?string $newPath): void
    {
        $setting = Setting::query()->where('key', $key)->first();

        if ($setting !== null && $setting->value !== null && Storage::disk('public')->exists($setting->value)) {
            Storage::disk('public')->delete($setting->value);
        }

        $this->upsert($key, (string) ($newPath ?? ''), 'file', 'about');
    }
}
