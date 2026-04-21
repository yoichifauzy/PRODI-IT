@php
    $status = old('status', $announcement->status ?? 'draft');
    $publishedAt = old('published_at', optional($announcement->published_at ?? null)->format('Y-m-d\\TH:i'));
    $createdAt = isset($announcement) && $announcement->exists
        ? optional($announcement->created_at)->format('d M Y H:i')
        : null;
    $storedCoverImage = $announcement->cover_image ?? '';
    $coverImage = old(
        'cover_image_url',
        \Illuminate\Support\Str::startsWith($storedCoverImage, ['http://', 'https://']) ? $storedCoverImage : ''
    );
    $coverImageSource = old('cover_image_url');
    if ($coverImageSource === null || $coverImageSource === '') {
        $coverImageSource = $storedCoverImage;
    }
    $coverImagePreview = '';

    if ($coverImageSource !== '') {
        $coverImagePreview = \Illuminate\Support\Str::startsWith($coverImageSource, ['http://', 'https://'])
            ? $coverImageSource
            : asset('storage/' . $coverImageSource);
    }
@endphp

<div class="grid gap-4">
    <div>
        <label for="title" class="mb-2 block text-sm font-medium text-slate-700">Judul</label>
        <input id="title" name="title" value="{{ old('title', $announcement->title ?? '') }}" required class="w-full rounded-md border border-slate-300 px-3 py-2 focus:border-slate-500 focus:outline-none" />
    </div>

    <div>
        <label for="slug" class="mb-2 block text-sm font-medium text-slate-700">Slug (Opsional)</label>
        <input id="slug" name="slug" value="{{ old('slug', $announcement->slug ?? '') }}" class="w-full rounded-md border border-slate-300 px-3 py-2 focus:border-slate-500 focus:outline-none" />
    </div>

    <div>
        <label for="content" class="mb-2 block text-sm font-medium text-slate-700">Konten</label>
        <textarea id="content" name="content" rows="8" required class="w-full rounded-md border border-slate-300 px-3 py-2 focus:border-slate-500 focus:outline-none">{{ old('content', $announcement->content ?? '') }}</textarea>
    </div>

    <div>
        <label for="cover_image_url" class="mb-2 block text-sm font-medium text-slate-700">URL Cover Image (Opsional)</label>
        <input id="cover_image_url" name="cover_image_url" value="{{ $coverImage }}" class="w-full rounded-md border border-slate-300 px-3 py-2 focus:border-slate-500 focus:outline-none" />
    </div>

    <div>
        <label for="cover_image_file" class="mb-2 block text-sm font-medium text-slate-700">Upload Cover Image (Opsional)</label>
        <input id="cover_image_file" type="file" name="cover_image_file" accept="image/*" class="w-full rounded-md border border-slate-300 px-3 py-2 focus:border-slate-500 focus:outline-none" />
        <p class="mt-2 text-xs text-slate-500">Maksimal ukuran gambar: 5 MB.</p>

        @if ($coverImagePreview !== '')
            <img src="{{ $coverImagePreview }}" alt="Preview Cover" class="mt-3 h-40 w-full rounded-lg object-cover" />
        @endif
    </div>

    <div class="grid gap-4 md:grid-cols-2">
        <div>
            <label for="status" class="mb-2 block text-sm font-medium text-slate-700">Status</label>
            <select id="status" name="status" class="w-full rounded-md border border-slate-300 px-3 py-2 focus:border-slate-500 focus:outline-none">
                <option value="draft" @selected($status === 'draft')>Draft</option>
                <option value="published" @selected($status === 'published')>Published</option>
                <option value="archived" @selected($status === 'archived')>Archived</option>
            </select>
        </div>
        <div>
            <label for="published_at" class="mb-2 block text-sm font-medium text-slate-700">Publish At (Opsional)</label>
            <input id="published_at" name="published_at" type="datetime-local" value="{{ $publishedAt }}" class="w-full rounded-md border border-slate-300 px-3 py-2 focus:border-slate-500 focus:outline-none" />
            <p class="mt-2 text-xs text-slate-500">Gunakan untuk jadwal tayang otomatis. Jika status Published dan kosong, sistem akan langsung publish sekarang.</p>
        </div>
    </div>

    @if ($createdAt)
        <div class="rounded-md border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-700">
            <span class="font-medium">Tanggal Dibuat:</span> {{ $createdAt }}
        </div>
    @endif
</div>
