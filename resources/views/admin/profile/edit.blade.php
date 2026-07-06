@extends('layouts.admin')

@section('title', 'Kelola Profil Web Prodi')

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-slate-900">Profil Program Studi</h1>
        <p class="text-sm text-slate-600">Atur konten Tentang Kami dan Visi Misi pada halaman utama.</p>
    </div>

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
    <div>
        <span class="mb-2 block text-sm font-medium text-slate-700">Gambar 1</span>
        <label for="image_one_path" class="group relative flex h-40 cursor-pointer flex-col items-center justify-center overflow-hidden rounded-xl border-2 border-dashed border-slate-300 bg-slate-50 transition-all hover:bg-slate-100 hover:border-slate-400">
            <input id="image_one_path" type="file" name="image_one_path" accept="image/*" class="sr-only" />
            
            @if ($profile->image_one_path)
                <img src="{{ asset('storage/' . $profile->image_one_path) }}" alt="Gambar 1" class="h-full w-full object-cover" />
                <div class="absolute inset-0 flex items-center justify-center bg-slate-900/60 opacity-0 transition-opacity duration-300 group-hover:opacity-100">
                    <span class="rounded-lg bg-white/20 px-3 py-1.5 text-sm font-medium text-white shadow-sm backdrop-blur-sm">Ganti Gambar</span>
                </div>
            @else
                <div class="flex flex-col items-center justify-center text-slate-400 group-hover:text-slate-600">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="mb-2 h-8 w-8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 16.5V9.75m0 0l3 3m-3-3l-3 3M6.75 19.5a4.5 4.5 0 01-1.41-8.775 5.25 5.25 0 0110.233-2.33 3 3 0 013.758 3.848A3.752 3.752 0 0118 19.5H6.75z" />
                    </svg>
                    <span class="text-xs font-medium">Klik untuk upload</span>
                </div>
            @endif
        </label>
    </div>

    <div>
        <span class="mb-2 block text-sm font-medium text-slate-700">Gambar 2</span>
        <label for="image_two_path" class="group relative flex h-40 cursor-pointer flex-col items-center justify-center overflow-hidden rounded-xl border-2 border-dashed border-slate-300 bg-slate-50 transition-all hover:bg-slate-100 hover:border-slate-400">
            <input id="image_two_path" type="file" name="image_two_path" accept="image/*" class="sr-only" />
            
            @if ($profile->image_two_path)
                <img src="{{ asset('storage/' . $profile->image_two_path) }}" alt="Gambar 2" class="h-full w-full object-cover" />
                <div class="absolute inset-0 flex items-center justify-center bg-slate-900/60 opacity-0 transition-opacity duration-300 group-hover:opacity-100">
                    <span class="rounded-lg bg-white/20 px-3 py-1.5 text-sm font-medium text-white shadow-sm backdrop-blur-sm">Ganti Gambar</span>
                </div>
            @else
                <div class="flex flex-col items-center justify-center text-slate-400 group-hover:text-slate-600">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="mb-2 h-8 w-8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 16.5V9.75m0 0l3 3m-3-3l-3 3M6.75 19.5a4.5 4.5 0 01-1.41-8.775 5.25 5.25 0 0110.233-2.33 3 3 0 013.758 3.848A3.752 3.752 0 0118 19.5H6.75z" />
                    </svg>
                    <span class="text-xs font-medium">Klik untuk upload</span>
                </div>
            @endif
        </label>
    </div>

    <div>
        <span class="mb-2 block text-sm font-medium text-slate-700">Video Singkat (Opsional)</span>
        <label for="video_path" class="group relative flex h-40 cursor-pointer flex-col items-center justify-center overflow-hidden rounded-xl border-2 border-dashed border-slate-300 bg-slate-50 transition-all hover:bg-slate-100 hover:border-slate-400">
            <input id="video_path" type="file" name="video_path" accept="video/mp4,video/webm" class="sr-only" />
            
            @if ($profile->video_path)
                <video class="pointer-events-none h-full w-full object-cover" preload="metadata">
                    <source src="{{ asset('storage/' . $profile->video_path) }}#t=0.1" type="video/mp4">
                </video>
                <div class="absolute inset-0 flex items-center justify-center bg-slate-900/60 opacity-0 transition-opacity duration-300 group-hover:opacity-100">
                    <span class="rounded-lg bg-white/20 px-3 py-1.5 text-sm font-medium text-white shadow-sm backdrop-blur-sm">Ganti Video</span>
                </div>
            @else
                <div class="flex flex-col items-center justify-center text-slate-400 group-hover:text-slate-600">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="mb-2 h-8 w-8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5l4.72-4.72a.75.75 0 011.28.53v11.38a.75.75 0 01-1.28.53l-4.72-4.72M4.5 18.75h9a2.25 2.25 0 002.25-2.25v-9a2.25 2.25 0 00-2.25-2.25h-9A2.25 2.25 0 002.25 7.5v9a2.25 2.25 0 002.25 2.25z" />
                    </svg>
                    <span class="text-xs font-medium">Klik untuk upload</span>
                </div>
            @endif
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
@endsection
