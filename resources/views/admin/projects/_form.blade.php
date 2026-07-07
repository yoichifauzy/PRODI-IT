@php
    $isFeatured = old('is_feature', $project->is_feature ?? false);
    $currentImage = $project->image_path ?? null;
@endphp

<div class="grid gap-5">

    {{-- Judul --}}
    <div>
        <label for="title" class="mb-1.5 block text-sm font-semibold text-slate-700">Judul Project <span class="text-rose-500">*</span></label>
        <input id="title" name="title" required value="{{ old('title', $project->title ?? '') }}"
               class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-slate-400 @error('title') border-rose-400 @enderror">
        @error('title')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
    </div>

    {{-- Mahasiswa --}}
    <div class="grid gap-4 md:grid-cols-3">
        <div class="md:col-span-2">
            <label for="student_name" class="mb-1.5 block text-sm font-semibold text-slate-700">Nama Mahasiswa <span class="text-rose-500">*</span></label>
            <input id="student_name" name="student_name" required value="{{ old('student_name', $project->student_name ?? '') }}"
                   class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-slate-400 @error('student_name') border-rose-400 @enderror">
            @error('student_name')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label for="student_nim" class="mb-1.5 block text-sm font-semibold text-slate-700">NIM</label>
            <input id="student_nim" name="student_nim" value="{{ old('student_nim', $project->student_nim ?? '') }}"
                   class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-slate-400">
        </div>
    </div>

    {{-- Tahun --}}
    <div class="w-40">
        <label for="year" class="mb-1.5 block text-sm font-semibold text-slate-700">Tahun</label>
        <input id="year" type="number" name="year" min="2000" max="2100"
               value="{{ old('year', $project->year ?? now()->year) }}"
               class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-slate-400">
    </div>

    {{-- Deskripsi --}}
    <div>
        <label for="description" class="mb-1.5 block text-sm font-semibold text-slate-700">Deskripsi</label>
        <textarea id="description" name="description" rows="5"
                  class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-slate-400">{{ old('description', $project->description ?? '') }}</textarea>
    </div>

    {{-- Gambar --}}
    <div>
        <label for="image_file" class="mb-1.5 block text-sm font-semibold text-slate-700">Gambar Project</label>
        @if($currentImage)
        <div class="mb-3 relative w-full max-w-sm">
            <img src="{{ asset('storage/' . $currentImage) }}" alt="Gambar saat ini"
                 class="h-40 w-full rounded-xl object-cover border border-slate-200">
            <span class="absolute bottom-2 left-2 rounded-md bg-black/50 px-2 py-0.5 text-xs text-white">Gambar saat ini</span>
        </div>
        @endif
        <input id="image_file" type="file" name="image_file" accept="image/*"
               class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm file:mr-3 file:rounded-md file:border-0 file:bg-slate-100 file:px-3 file:py-1 file:text-xs file:font-semibold file:text-slate-700 hover:file:bg-slate-200">
        <p class="mt-1 text-xs text-slate-400">JPG, PNG, WEBP — maks 5MB</p>
        @error('image_file')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
    </div>

    {{-- Unggulan toggle --}}
    <div class="flex items-center gap-3 rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">
        <input type="hidden" name="is_feature" value="0">
        <label class="relative inline-flex cursor-pointer items-center gap-3">
            <input type="checkbox" name="is_feature" value="1"
                   id="is_feature"
                   {{ ((string)$isFeatured === '1' || $isFeatured === true || $isFeatured === 1) ? 'checked' : '' }}
                   class="sr-only peer">
            <div class="h-6 w-11 rounded-full bg-slate-200 peer-focus:ring-2 peer-focus:ring-amber-400 peer-checked:bg-amber-400 transition-colors after:absolute after:top-0.5 after:left-0.5 after:h-5 after:w-5 after:rounded-full after:bg-white after:shadow after:transition-transform peer-checked:after:translate-x-5"></div>
            <span class="text-sm font-semibold text-slate-700">Tandai sebagai Project Unggulan</span>
        </label>
        <svg class="ml-auto h-5 w-5 text-amber-400" viewBox="0 0 24 24" fill="currentColor">
            <path d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z"/>
        </svg>
    </div>

</div>
