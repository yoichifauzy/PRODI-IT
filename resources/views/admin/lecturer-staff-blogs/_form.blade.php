@php
    $isPublished = old('is_published', $blog->is_published ?? true);
    $activityDate = old('activity_date', optional($blog->activity_date ?? null)->format('Y-m-d'));
@endphp

<div class="grid gap-4">
    <div>
        <label for="title" class="mb-2 block text-sm font-medium text-slate-700">Judul Kegiatan</label>
        <input id="title" name="title" required value="{{ old('title', $blog->title ?? '') }}" class="w-full rounded-md border border-slate-300 px-3 py-2" />
    </div>

    <div>
        <label for="slug" class="mb-2 block text-sm font-medium text-slate-700">Slug Singkat (Opsional)</label>
        <input id="slug" name="slug" value="{{ old('slug', $blog->slug ?? '') }}" class="w-full rounded-md border border-slate-300 px-3 py-2" />
    </div>

    <div class="grid gap-4 md:grid-cols-2">
        <div>
            <label for="activity_date" class="mb-2 block text-sm font-medium text-slate-700">Tanggal Kegiatan</label>
            <input id="activity_date" type="date" name="activity_date" value="{{ $activityDate }}" class="w-full rounded-md border border-slate-300 px-3 py-2" />
        </div>
        <div>
            <label for="location" class="mb-2 block text-sm font-medium text-slate-700">Lokasi</label>
            <input id="location" name="location" value="{{ old('location', $blog->location ?? '') }}" class="w-full rounded-md border border-slate-300 px-3 py-2" />
        </div>
    </div>

    <div>
        <label for="description" class="mb-2 block text-sm font-medium text-slate-700">Deskripsi</label>
        <textarea id="description" name="description" rows="6" class="w-full rounded-md border border-slate-300 px-3 py-2">{{ old('description', $blog->description ?? '') }}</textarea>
    </div>

    <div>
        <label for="cover_image_file" class="mb-2 block text-sm font-medium text-slate-700">Gambar Kegiatan (Opsional)</label>
        <input id="cover_image_file" type="file" name="cover_image_file" accept="image/*" class="w-full rounded-md border border-slate-300 px-3 py-2" />
        <p class="mt-2 text-xs text-slate-500">Maksimal ukuran gambar: 5 MB.</p>

        @if (!empty($blog->cover_image))
            <img src="{{ asset('storage/' . $blog->cover_image) }}" alt="{{ $blog->title }}" class="mt-3 h-40 w-full rounded-lg object-cover" />
        @endif
    </div>

    <div class="grid gap-4 md:grid-cols-2">
        <div>
            <label for="sort_order" class="mb-2 block text-sm font-medium text-slate-700">Urutan</label>
            <input id="sort_order" type="number" min="0" name="sort_order" value="{{ old('sort_order', $blog->sort_order ?? 0) }}" class="w-full rounded-md border border-slate-300 px-3 py-2" />
        </div>

        <label class="mt-7 inline-flex items-center gap-2 rounded-md border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-700">
            <input type="hidden" name="is_published" value="0" />
            <input type="checkbox" name="is_published" value="1" @checked((string) $isPublished === '1' || $isPublished === true || $isPublished === 1) />
            Tampilkan di halaman publik
        </label>
    </div>
</div>
