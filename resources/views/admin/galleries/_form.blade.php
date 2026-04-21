@php
    $status = old('status', $gallery->status ?? 'draft');
    $publishedAt = old('published_at', isset($gallery) && $gallery->published_at ? $gallery->published_at->format('Y-m-d\TH:i') : '');
@endphp

<div class="grid gap-4">
    <div>
        <label for="name" class="mb-2 block text-sm font-medium text-slate-700">Nama Filter</label>
        <input id="name" name="name" required value="{{ old('name', $gallery->name ?? '') }}" class="w-full rounded-md border border-slate-300 px-3 py-2" placeholder="MABES IKTE" />
    </div>

    <div>
        <label for="slug" class="mb-2 block text-sm font-medium text-slate-700">Slug (Opsional)</label>
        <input id="slug" name="slug" value="{{ old('slug', $gallery->slug ?? '') }}" class="w-full rounded-md border border-slate-300 px-3 py-2" placeholder="mabes-ikte" />
    </div>

    <div>
        <label for="description" class="mb-2 block text-sm font-medium text-slate-700">Deskripsi (Opsional)</label>
        <textarea id="description" name="description" rows="4" class="w-full rounded-md border border-slate-300 px-3 py-2">{{ old('description', $gallery->description ?? '') }}</textarea>
    </div>

    <div class="grid gap-4 md:grid-cols-2">
        <div>
            <label for="status" class="mb-2 block text-sm font-medium text-slate-700">Status</label>
            <select id="status" name="status" class="w-full rounded-md border border-slate-300 px-3 py-2">
                <option value="draft" @selected($status === 'draft')>Draft</option>
                <option value="published" @selected($status === 'published')>Published</option>
            </select>
        </div>

        <div>
            <label for="published_at" class="mb-2 block text-sm font-medium text-slate-700">Waktu Publish (Opsional)</label>
            <input id="published_at" type="datetime-local" name="published_at" value="{{ $publishedAt }}" class="w-full rounded-md border border-slate-300 px-3 py-2" />
        </div>
    </div>
</div>