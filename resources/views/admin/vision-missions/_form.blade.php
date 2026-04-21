@php
    $isActive = old('is_active', $visionMission->is_active ?? true);
@endphp

<div class="grid gap-4">
    <div>
        <label for="vision_title" class="mb-2 block text-sm font-medium text-slate-700">Judul Visi</label>
        <input id="vision_title" name="vision_title" value="{{ old('vision_title', $visionMission->vision_title ?? 'Visi') }}" required class="w-full rounded-md border border-slate-300 px-3 py-2 focus:border-slate-500 focus:outline-none" />
    </div>

    <div>
        <label for="vision_text" class="mb-2 block text-sm font-medium text-slate-700">Isi Visi</label>
        <textarea id="vision_text" name="vision_text" rows="5" required class="w-full rounded-md border border-slate-300 px-3 py-2 focus:border-slate-500 focus:outline-none">{{ old('vision_text', $visionMission->vision_text ?? '') }}</textarea>
    </div>

    <div>
        <label for="mission_title" class="mb-2 block text-sm font-medium text-slate-700">Judul Misi</label>
        <input id="mission_title" name="mission_title" value="{{ old('mission_title', $visionMission->mission_title ?? 'Misi') }}" required class="w-full rounded-md border border-slate-300 px-3 py-2 focus:border-slate-500 focus:outline-none" />
    </div>

    <div>
        <label for="mission_text" class="mb-2 block text-sm font-medium text-slate-700">Isi Misi</label>
        <textarea id="mission_text" name="mission_text" rows="6" required class="w-full rounded-md border border-slate-300 px-3 py-2 focus:border-slate-500 focus:outline-none">{{ old('mission_text', $visionMission->mission_text ?? '') }}</textarea>
        <p class="mt-2 text-xs text-slate-500">Pisahkan setiap poin misi dengan baris baru.</p>
    </div>

    <label class="inline-flex items-center gap-2 text-sm text-slate-700">
        <input type="checkbox" name="is_active" value="1" @checked((string) $isActive === '1' || $isActive === true || $isActive === 1)>
        Jadikan data aktif untuk ditampilkan di homepage
    </label>
</div>
