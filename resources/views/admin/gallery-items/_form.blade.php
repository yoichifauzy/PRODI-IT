@php
    $takenAt = old('taken_at', isset($galleryItem) && $galleryItem->taken_at ? $galleryItem->taken_at->format('Y-m-d') : now()->format('Y-m-d'));
    $publishedAt = old('published_at', isset($galleryItem) && $galleryItem->published_at ? $galleryItem->published_at->format('Y-m-d\TH:i') : '');
    $selectedGalleryId = (string) old('gallery_id', request('gallery_id', $galleryItem->gallery_id ?? ''));
@endphp

<div class="grid gap-4">
    <div>
        <label for="gallery_id" class="mb-2 block text-sm font-medium text-slate-700">Filter Galeri</label>
        <select id="gallery_id" name="gallery_id" required class="w-full rounded-md border border-slate-300 px-3 py-2">
            <option value="">-- Pilih Filter --</option>
            @foreach ($galleries as $gallery)
                <option value="{{ $gallery->id }}" @selected($selectedGalleryId === (string) $gallery->id)>{{ $gallery->name }}</option>
            @endforeach
        </select>
    </div>

    <div class="grid gap-4 md:grid-cols-2">
        <div>
            <label for="title" class="mb-2 block text-sm font-medium text-slate-700">Judul (Opsional)</label>
            <input id="title" name="title" value="{{ old('title', $galleryItem->title ?? '') }}" class="w-full rounded-md border border-slate-300 px-3 py-2" />
        </div>
        <div>
            <label for="taken_at" class="mb-2 block text-sm font-medium text-slate-700">Tanggal</label>
            <input id="taken_at" type="date" name="taken_at" value="{{ $takenAt }}" class="w-full rounded-md border border-slate-300 px-3 py-2" />
        </div>
    </div>

    <div>
        <label for="published_at" class="mb-2 block text-sm font-medium text-slate-700">Waktu Publish (Opsional)</label>
        <input id="published_at" type="datetime-local" name="published_at" value="{{ $publishedAt }}" class="w-full rounded-md border border-slate-300 px-3 py-2" />
        <p class="mt-2 text-xs text-slate-500">Kosongkan untuk upload langsung.</p>
    </div>

    <div>
        <label for="caption" class="mb-2 block text-sm font-medium text-slate-700">Caption (Opsional)</label>
        <textarea id="caption" name="caption" rows="4" class="w-full rounded-md border border-slate-300 px-3 py-2">{{ old('caption', $galleryItem->caption ?? '') }}</textarea>
    </div>

    <div>
        <label for="image" class="mb-2 block text-sm font-medium text-slate-700">Gambar {{ isset($galleryItem) ? '(Opsional jika tidak diubah)' : '' }}</label>
        <input id="image" type="file" name="image" accept="image/*" {{ isset($galleryItem) ? '' : 'required' }} class="w-full rounded-md border border-slate-300 px-3 py-2" />

        @if (isset($galleryItem) && $galleryItem->image_path)
            <img src="{{ asset('storage/' . $galleryItem->image_path) }}" alt="{{ $galleryItem->title ?: 'Item Galeri' }}" class="mt-3 h-40 w-full rounded-lg object-cover" />
        @endif
    </div>

    <div>
        <label for="sort_order" class="mb-2 block text-sm font-medium text-slate-700">Urutan</label>
        <input id="sort_order" type="number" min="0" name="sort_order" value="{{ old('sort_order', $galleryItem->sort_order ?? 0) }}" class="w-full rounded-md border border-slate-300 px-3 py-2" />
    </div>
</div>
