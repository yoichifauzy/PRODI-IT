@php
    $isActive = old('is_active', $lecturerStaff->is_active ?? true);
    $selectedType = old('type', $lecturerStaff->type ?? 'lecturer');
@endphp

<div class="grid gap-4">
    <div class="grid gap-4 md:grid-cols-2">
        <div>
            <label for="name" class="mb-2 block text-sm font-medium text-slate-700">Nama</label>
            <input id="name" name="name" required value="{{ old('name', $lecturerStaff->name ?? '') }}" class="w-full rounded-md border border-slate-300 px-3 py-2" />
        </div>
        <div>
            <label for="position" class="mb-2 block text-sm font-medium text-slate-700">Jabatan / Posisi</label>
            <input id="position" name="position" required value="{{ old('position', $lecturerStaff->position ?? '') }}" class="w-full rounded-md border border-slate-300 px-3 py-2" />
        </div>
    </div>

    <div class="grid gap-4 md:grid-cols-2">
        <div>
            <label for="type" class="mb-2 block text-sm font-medium text-slate-700">Tipe</label>
            <select id="type" name="type" class="w-full rounded-md border border-slate-300 px-3 py-2">
                @foreach ($types as $type)
                    <option value="{{ $type }}" @selected($selectedType === $type)>{{ strtoupper($type) }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="email" class="mb-2 block text-sm font-medium text-slate-700">Email (Opsional)</label>
            <input id="email" name="email" type="email" value="{{ old('email', $lecturerStaff->email ?? '') }}" class="w-full rounded-md border border-slate-300 px-3 py-2" />
        </div>
    </div>

    <div>
        <label for="bio" class="mb-2 block text-sm font-medium text-slate-700">Bio (Opsional)</label>
        <textarea id="bio" name="bio" rows="4" class="w-full rounded-md border border-slate-300 px-3 py-2">{{ old('bio', $lecturerStaff->bio ?? '') }}</textarea>
    </div>

    <div>
        <label for="photo" class="mb-2 block text-sm font-medium text-slate-700">Foto (Opsional)</label>
        <input id="photo" type="file" name="photo" accept="image/*" class="w-full rounded-md border border-slate-300 px-3 py-2" />

        @if (isset($lecturerStaff) && $lecturerStaff->photo_path)
            <img src="{{ asset('storage/' . $lecturerStaff->photo_path) }}" alt="{{ $lecturerStaff->name }}" class="mt-3 h-36 w-36 rounded-full object-cover" />
        @endif
    </div>

    <div class="grid gap-4 md:grid-cols-2">
        <div>
            <label for="sort_order" class="mb-2 block text-sm font-medium text-slate-700">Urutan</label>
            <input id="sort_order" type="number" min="0" name="sort_order" value="{{ old('sort_order', $lecturerStaff->sort_order ?? 0) }}" class="w-full rounded-md border border-slate-300 px-3 py-2" />
        </div>

        <label class="mt-7 inline-flex items-center gap-2 rounded-md border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-700">
            <input type="hidden" name="is_active" value="0" />
            <input type="checkbox" name="is_active" value="1" @checked((string) $isActive === '1' || $isActive === true || $isActive === 1) />
            Aktif ditampilkan
        </label>
    </div>
</div>