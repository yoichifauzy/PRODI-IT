@php
    $isActive = old('is_active', $curriculum->is_active ?? false);
@endphp

<div class="grid gap-4">
    <div>
        <label for="name" class="mb-2 block text-sm font-medium text-slate-700">Nama Kurikulum</label>
        <input id="name" name="name" required value="{{ old('name', $curriculum->name ?? '') }}" class="w-full rounded-md border border-slate-300 px-3 py-2" placeholder="Kurikulum Merdeka TI" />
    </div>

    <div>
        <label for="academic_year" class="mb-2 block text-sm font-medium text-slate-700">Tahun Akademik</label>
        <input id="academic_year" name="academic_year" value="{{ old('academic_year', $curriculum->academic_year ?? '') }}" class="w-full rounded-md border border-slate-300 px-3 py-2" placeholder="2026/2027" />
    </div>

    <div>
        <label for="description" class="mb-2 block text-sm font-medium text-slate-700">Deskripsi</label>
        <textarea id="description" name="description" rows="4" class="w-full rounded-md border border-slate-300 px-3 py-2">{{ old('description', $curriculum->description ?? '') }}</textarea>
    </div>

    <label class="inline-flex items-center gap-2 rounded-md border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-700">
        <input type="hidden" name="is_active" value="0" />
        <input type="checkbox" name="is_active" value="1" @checked((string) $isActive === '1' || $isActive === true || $isActive === 1) />
        Jadikan kurikulum aktif
    </label>
</div>