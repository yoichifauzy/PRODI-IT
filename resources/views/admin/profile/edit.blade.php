@extends('layouts.admin')

@section('title', 'Kelola Profil Web Prodi')

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-slate-900">Profil Program Studi</h1>
        <p class="text-sm text-slate-600">Atur konten Tentang Kami dan Visi Misi pada halaman utama.</p>
    </div>

    @if (session('success'))
    <div class="mb-6 rounded-lg bg-emerald-50 p-4 text-emerald-700 border border-emerald-200">
        {{ session('success') }}
    </div>
    @endif

    @if ($errors->any())
    <div class="mb-6 rounded-lg bg-rose-50 p-4 text-rose-700 border border-rose-200">
        <ul class="list-disc pl-5">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form method="POST" action="{{ route('admin.profile.update') }}" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- SECTION 1: TENTANG KAMI -->
        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <h2 class="mb-4 border-b border-slate-100 pb-2 text-lg font-semibold text-slate-800">Tentang Kami</h2>
            
            <div class="grid gap-4">
                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label for="description_primary" class="mb-2 block text-sm font-medium text-slate-700">Deskripsi Utama</label>
                        <textarea id="description_primary" name="description_primary" rows="4" class="w-full rounded-md border border-slate-300 px-3 py-2 focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500">{{ old('description_primary', $profile->description_primary) }}</textarea>
                    </div>

                    <div>
                        <label for="description_secondary" class="mb-2 block text-sm font-medium text-slate-700">Deskripsi Kedua</label>
                        <textarea id="description_secondary" name="description_secondary" rows="4" class="w-full rounded-md border border-slate-300 px-3 py-2 focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500">{{ old('description_secondary', $profile->description_secondary) }}</textarea>
                    </div>
                </div>

    <div class="grid gap-4 md:grid-cols-3">
    {{-- GAMBAR 1 --}}
    <div>
        <span class="mb-2 block text-sm font-medium text-slate-700">Gambar 1</span>
        <label for="image_one_path" class="group relative flex h-40 cursor-pointer flex-col items-center justify-center overflow-hidden rounded-xl border-2 border-dashed border-slate-300 bg-slate-50 transition-all hover:bg-slate-100 hover:border-slate-400">
            <input id="image_one_path" type="file" name="image_one_path" accept="image/*" class="sr-only" onchange="previewMedia(this, 'preview-img-1', 'placeholder-1', 'overlay-1')" />
            
            {{-- Preview Image --}}
            <img id="preview-img-1" src="{{ $profile->image_one_path ? asset('storage/' . $profile->image_one_path) : '' }}" class="h-full w-full object-cover {{ $profile->image_one_path ? '' : 'hidden' }}" />
            
            {{-- Overlay Ganti --}}
            <div id="overlay-1" class="absolute inset-0 flex items-center justify-center bg-slate-900/60 opacity-0 transition-opacity duration-300 group-hover:opacity-100 {{ $profile->image_one_path ? '' : 'hidden' }}">
                <span class="rounded-lg bg-white/20 px-3 py-1.5 text-sm font-medium text-white shadow-sm backdrop-blur-sm">Ganti Gambar</span>
            </div>
            
            {{-- Placeholder Upload --}}
            <div id="placeholder-1" class="flex flex-col items-center justify-center text-slate-400 group-hover:text-slate-600 {{ $profile->image_one_path ? 'hidden' : '' }}">
                <i class="fa-solid fa-cloud-arrow-up mb-2 text-2xl"></i>
                <span class="text-xs font-medium">Klik untuk upload</span>
            </div>
        </label>
    </div>

    {{-- GAMBAR 2 --}}
    <div>
        <span class="mb-2 block text-sm font-medium text-slate-700">Gambar 2</span>
        <label for="image_two_path" class="group relative flex h-40 cursor-pointer flex-col items-center justify-center overflow-hidden rounded-xl border-2 border-dashed border-slate-300 bg-slate-50 transition-all hover:bg-slate-100 hover:border-slate-400">
            <input id="image_two_path" type="file" name="image_two_path" accept="image/*" class="sr-only" onchange="previewMedia(this, 'preview-img-2', 'placeholder-2', 'overlay-2')" />
            
            {{-- Preview Image --}}
            <img id="preview-img-2" src="{{ $profile->image_two_path ? asset('storage/' . $profile->image_two_path) : '' }}" class="h-full w-full object-cover {{ $profile->image_two_path ? '' : 'hidden' }}" />
            
            {{-- Overlay Ganti --}}
            <div id="overlay-2" class="absolute inset-0 flex items-center justify-center bg-slate-900/60 opacity-0 transition-opacity duration-300 group-hover:opacity-100 {{ $profile->image_two_path ? '' : 'hidden' }}">
                <span class="rounded-lg bg-white/20 px-3 py-1.5 text-sm font-medium text-white shadow-sm backdrop-blur-sm">Ganti Gambar</span>
            </div>
            
            {{-- Placeholder Upload --}}
            <div id="placeholder-2" class="flex flex-col items-center justify-center text-slate-400 group-hover:text-slate-600 {{ $profile->image_two_path ? 'hidden' : '' }}">
                <i class="fa-solid fa-cloud-arrow-up mb-2 text-2xl"></i>
                <span class="text-xs font-medium">Klik untuk upload</span>
            </div>
        </label>
    </div>

    {{-- VIDEO SINGKAT --}}
    <div>
        <span class="mb-2 block text-sm font-medium text-slate-700">Video Singkat (Opsional)</span>
        <label for="video_path" class="group relative flex h-40 cursor-pointer flex-col items-center justify-center overflow-hidden rounded-xl border-2 border-dashed border-slate-300 bg-slate-50 transition-all hover:bg-slate-100 hover:border-slate-400">
            <input id="video_path" type="file" name="video_path" accept="video/mp4,video/webm" class="sr-only" onchange="previewMedia(this, 'preview-video', 'placeholder-video', 'overlay-video', true)" />
            
            {{-- Preview Video --}}
            <video id="preview-video" src="{{ $profile->video_path ? asset('storage/' . $profile->video_path) . '#t=0.1' : '' }}" class="pointer-events-none h-full w-full object-cover {{ $profile->video_path ? '' : 'hidden' }}" preload="metadata" muted></video>
            
            {{-- Overlay Ganti --}}
            <div id="overlay-video" class="absolute inset-0 flex items-center justify-center bg-slate-900/60 opacity-0 transition-opacity duration-300 group-hover:opacity-100 {{ $profile->video_path ? '' : 'hidden' }}">
                <span class="rounded-lg bg-white/20 px-3 py-1.5 text-sm font-medium text-white shadow-sm backdrop-blur-sm">Ganti Video</span>
            </div>
            
            {{-- Placeholder Upload --}}
            <div id="placeholder-video" class="flex flex-col items-center justify-center text-slate-400 group-hover:text-slate-600 {{ $profile->video_path ? 'hidden' : '' }}">
                <i class="fa-solid fa-film mb-2 text-2xl"></i>
                <span class="text-xs font-medium">Klik untuk upload</span>
            </div>
        </label>
    </div>
</div>
            </div>
        </div>

        <!-- SECTION 2: VISI MISI -->
        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <h2 class="mb-4 border-b border-slate-100 pb-2 text-lg font-semibold text-slate-800">Visi & Misi</h2>
            
            <div class="grid gap-4">
                <div>
                    <label for="vision_text" class="mb-2 block text-sm font-medium text-slate-700">Visi</label>
                    <textarea id="vision_text" name="vision_text" rows="3" placeholder="Contoh: Menjadi program studi unggulan di bidang Teknologi Informasi..." class="w-full rounded-md border border-slate-300 px-3 py-2 focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500">{{ old('vision_text', $profile->vision_text) }}</textarea>
                </div>

                <div>
                    <label for="mission_text" class="mb-2 block text-sm font-medium text-slate-700">Misi</label>
                    <textarea id="mission_text" name="mission_text" rows="6" placeholder="Contoh:&#10;Menyelenggarakan pendidikan...&#10;Melakukan penelitian..." class="w-full rounded-md border border-slate-300 px-3 py-2 focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500">{{ old('mission_text', is_array($profile->mission_text) ? implode("\n", $profile->mission_text) : $profile->mission_text) }}</textarea>
                    <p class="mt-1 text-xs text-slate-500">Gunakan baris baru untuk memisahkan setiap poin misi.</p>
                </div>
            </div>
        </div>

        <!-- TOMBOL SUBMIT -->
        <div class="flex justify-end pt-2">
            <button type="submit" class="rounded-md bg-slate-900 px-6 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-slate-900 focus:ring-offset-2">
                Simpan Profil
            </button>
        </div>
    </form>

    @push('scripts')
<script>
    function previewMedia(input, previewId, placeholderId, overlayId, isVideo = false) {
        // Cek apakah user benar-benar memilih file
        if (input.files && input.files[0]) {
            const file = input.files[0];
            
            // Buat URL sementara (lokal) untuk file yang di-upload
            const mediaUrl = URL.createObjectURL(file);
            
            // Ambil elemen HTML yang dibutuhkan
            const previewEl = document.getElementById(previewId);
            const placeholderEl = document.getElementById(placeholderId);
            const overlayEl = document.getElementById(overlayId);

            // Set URL sementara ke tag img / video
            previewEl.src = mediaUrl;

            // Jika itu video, kita paksa load ulang agar thumbnail barunya ter-render
            if (isVideo) {
                previewEl.load();
            }

            // Munculkan gambar/video dan hapus tulisan "Klik untuk upload"
            previewEl.classList.remove('hidden');
            if (overlayEl) overlayEl.classList.remove('hidden');
            if (placeholderEl) placeholderEl.classList.add('hidden');
        }
    }
</script>
@endpush

@endsection
