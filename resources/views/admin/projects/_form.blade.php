@php
    $status = old('status', $project->status ?? 'draft');
    $publishedAt = old('published_at', isset($project) && $project->published_at ? $project->published_at->format('Y-m-d\TH:i') : '');
    $isFeatured = old('is_featured', $project->is_featured ?? false);
@endphp

<div class="grid gap-4">
    <div>
        <label for="title" class="mb-2 block text-sm font-medium text-slate-700">Judul Project</label>
        <input id="title" name="title" required value="{{ old('title', $project->title ?? '') }}" class="w-full rounded-md border border-slate-300 px-3 py-2" />
    </div>

    <div>
        <label for="slug" class="mb-2 block text-sm font-medium text-slate-700">Slug (Opsional)</label>
        <input id="slug" name="slug" value="{{ old('slug', $project->slug ?? '') }}" class="w-full rounded-md border border-slate-300 px-3 py-2" />
    </div>

    <div class="grid gap-4 md:grid-cols-3">
        <div>
            <label for="student_name" class="mb-2 block text-sm font-medium text-slate-700">Nama Mahasiswa</label>
            <input id="student_name" name="student_name" required value="{{ old('student_name', $project->student_name ?? '') }}" class="w-full rounded-md border border-slate-300 px-3 py-2" />
        </div>
        <div>
            <label for="student_nim" class="mb-2 block text-sm font-medium text-slate-700">NIM</label>
            <input id="student_nim" name="student_nim" value="{{ old('student_nim', $project->student_nim ?? '') }}" class="w-full rounded-md border border-slate-300 px-3 py-2" />
        </div>
        <div>
            <label for="year" class="mb-2 block text-sm font-medium text-slate-700">Tahun</label>
            <input id="year" type="number" min="2000" max="2100" name="year" value="{{ old('year', $project->year ?? now()->year) }}" class="w-full rounded-md border border-slate-300 px-3 py-2" />
        </div>
    </div>

    <div>
        <label for="summary" class="mb-2 block text-sm font-medium text-slate-700">Deskripsi</label>
        <textarea id="summary" name="summary" rows="4" class="w-full rounded-md border border-slate-300 px-3 py-2">{{ old('summary', $project->summary ?? '') }}</textarea>
    </div>

    <div class="grid gap-4 md:grid-cols-2">
        <div>
            <label for="demo_url" class="mb-2 block text-sm font-medium text-slate-700">Demo URL (Opsional)</label>
            <input id="demo_url" name="demo_url" type="url" value="{{ old('demo_url', $project->demo_url ?? '') }}" class="w-full rounded-md border border-slate-300 px-3 py-2" />
        </div>
        <div>
            <label for="repository_url" class="mb-2 block text-sm font-medium text-slate-700">Repository URL (Opsional)</label>
            <input id="repository_url" name="repository_url" type="url" value="{{ old('repository_url', $project->repository_url ?? '') }}" class="w-full rounded-md border border-slate-300 px-3 py-2" />
        </div>
    </div>

    <div>
        <label for="thumbnail_file" class="mb-2 block text-sm font-medium text-slate-700">Gambar Project (Opsional)</label>
        <input id="thumbnail_file" type="file" name="thumbnail_file" accept="image/*" class="w-full rounded-md border border-slate-300 px-3 py-2" />

        @if (isset($project) && $project->thumbnail)
            <img src="{{ asset('storage/' . $project->thumbnail) }}" alt="{{ $project->title }}" class="mt-3 h-40 w-full rounded-lg object-cover" />
        @endif
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
            <p class="mt-2 text-xs text-slate-500">
                Status Draft wajib isi waktu publish. Status Published boleh kosong untuk upload langsung.
            </p>
        </div>
    </div>

    <label class="inline-flex items-center gap-2 rounded-md border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-700">
        <input type="hidden" name="is_featured" value="0" />
        <input type="checkbox" name="is_featured" value="1" @checked((string) $isFeatured === '1' || $isFeatured === true || $isFeatured === 1) />
        Tandai sebagai project unggulan
    </label>
</div>
