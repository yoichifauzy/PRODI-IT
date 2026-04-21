@php
    $eventDate = old('event_date', isset($activity) && $activity->event_date ? $activity->event_date->format('Y-m-d') : now()->format('Y-m-d'));
    $publishedAt = old('published_at', isset($activity) && $activity->published_at ? $activity->published_at->format('Y-m-d\TH:i') : '');
    $isPublished = old('is_published', $activity->is_published ?? true);
@endphp

<div class="grid gap-4">
    <div class="grid gap-4 md:grid-cols-3">
        <div>
            <label for="category" class="mb-2 block text-sm font-medium text-slate-700">Label / Kategori</label>
            <input id="category" name="category" required value="{{ old('category', $activity->category ?? '') }}" class="w-full rounded-md border border-slate-300 px-3 py-2" placeholder="Lomba" />
        </div>
        <div>
            <label for="event_date" class="mb-2 block text-sm font-medium text-slate-700">Tanggal Kegiatan</label>
            <input id="event_date" type="date" name="event_date" required value="{{ $eventDate }}" class="w-full rounded-md border border-slate-300 px-3 py-2" />
        </div>
        <div>
            <label for="published_at" class="mb-2 block text-sm font-medium text-slate-700">Waktu Publish (Opsional)</label>
            <input id="published_at" type="datetime-local" name="published_at" value="{{ $publishedAt }}" class="w-full rounded-md border border-slate-300 px-3 py-2" />
            <p class="mt-2 text-xs text-slate-500">Kosongkan untuk upload langsung ketika status ditampilkan aktif.</p>
        </div>
    </div>

    <div>
        <label for="title" class="mb-2 block text-sm font-medium text-slate-700">Judul Card</label>
        <input id="title" name="title" required value="{{ old('title', $activity->title ?? '') }}" class="w-full rounded-md border border-slate-300 px-3 py-2" placeholder="E-LINK" />
    </div>

    <div>
        <label for="description" class="mb-2 block text-sm font-medium text-slate-700">Deskripsi</label>
        <textarea id="description" name="description" rows="5" class="w-full rounded-md border border-slate-300 px-3 py-2" placeholder="COMING SOON">{{ old('description', $activity->description ?? '') }}</textarea>
    </div>

    <div>
        <label for="location" class="mb-2 block text-sm font-medium text-slate-700">Lokasi</label>
        <input id="location" name="location" value="{{ old('location', $activity->location ?? '') }}" class="w-full rounded-md border border-slate-300 px-3 py-2" placeholder="Aula & Lab Otomasi Politeknik Gajah Tunggal" />
    </div>

    <div>
        <label for="image" class="mb-2 block text-sm font-medium text-slate-700">Gambar Kegiatan {{ isset($activity) ? '(Opsional jika tidak diubah)' : '' }}</label>
        <input id="image" type="file" name="image" accept="image/*" {{ isset($activity) ? '' : 'required' }} class="w-full rounded-md border border-slate-300 px-3 py-2" />

        @if (isset($activity) && $activity->image_path)
            <img src="{{ asset('storage/' . $activity->image_path) }}" alt="{{ $activity->title }}" class="mt-3 h-40 w-full rounded-lg object-cover" />
        @endif
    </div>

    <div class="grid gap-4 md:grid-cols-2">
        <div>
            <label for="sort_order" class="mb-2 block text-sm font-medium text-slate-700">Urutan</label>
            <input id="sort_order" type="number" min="0" name="sort_order" value="{{ old('sort_order', $activity->sort_order ?? 0) }}" class="w-full rounded-md border border-slate-300 px-3 py-2" />
        </div>
        <label class="mt-7 inline-flex items-center gap-2 rounded-md border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-700">
            <input type="hidden" name="is_published" value="0" />
            <input type="checkbox" name="is_published" value="1" @checked((string) $isPublished === '1' || $isPublished === true || $isPublished === 1) />
            Tampilkan di halaman publik
        </label>
    </div>
</div>
