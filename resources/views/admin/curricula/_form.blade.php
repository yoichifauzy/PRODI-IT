@php
    $curriculum = $curriculum ?? null;
@endphp

<div class="grid gap-4">
    <div>
        <label for="name" class="mb-2 block text-sm font-medium text-slate-700">Nama Kurikulum</label>
        <input id="name" name="name" required value="{{ old('name', $curriculum?->name ?? '') }}" class="w-full rounded-md border border-slate-300 px-3 py-2" placeholder="Kurikulum Merdeka TI" />
    </div>
    <div>
        <label for="description" class="mb-2 block text-sm font-medium text-slate-700">Deskripsi</label>
        <textarea id="description" name="description" rows="4" class="w-full rounded-md border border-slate-300 px-3 py-2">{{ old('description', $curriculum?->description ?? '') }}</textarea>
    </div>
</div>
