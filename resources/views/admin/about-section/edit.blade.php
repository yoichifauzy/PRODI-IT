@extends('layouts.admin')

@section('title', 'Kelola Tentang Kami')

@section('content')
    <div class="mb-4">
        <h1 class="text-2xl font-bold text-slate-900">Tentang Kami</h1>
        <p class="text-sm text-slate-600">Atur konten section Tentang Kami pada halaman utama.</p>
    </div>

    <form method="POST" action="{{ route('admin.about-section.update') }}" enctype="multipart/form-data" class="rounded-xl border border-slate-200 bg-white p-4">
        @csrf
        @method('PUT')

        <div class="grid gap-4">
            <div>
                <label for="about_section_title" class="mb-2 block text-sm font-medium text-slate-700">Judul Section</label>
                <input id="about_section_title" name="about_section_title" value="{{ old('about_section_title', $settings['about_section_title'] ?? 'Tentang Kami') }}" class="w-full rounded-md border border-slate-300 px-3 py-2" />
            </div>

            <div>
                <label for="about_section_subtitle" class="mb-2 block text-sm font-medium text-slate-700">Subtitle Section</label>
                <input id="about_section_subtitle" name="about_section_subtitle" value="{{ old('about_section_subtitle', $settings['about_section_subtitle'] ?? 'Profil singkat Program Studi Teknologi Informasi') }}" class="w-full rounded-md border border-slate-300 px-3 py-2" />
            </div>

            <div>
                <label for="about_heading" class="mb-2 block text-sm font-medium text-slate-700">Judul Konten</label>
                <input id="about_heading" name="about_heading" value="{{ old('about_heading', $settings['about_heading'] ?? 'Program Studi Teknologi Informasi') }}" class="w-full rounded-md border border-slate-300 px-3 py-2" />
            </div>

            <div>
                <label for="about_description_primary" class="mb-2 block text-sm font-medium text-slate-700">Deskripsi Utama</label>
                <textarea id="about_description_primary" name="about_description_primary" rows="4" class="w-full rounded-md border border-slate-300 px-3 py-2">{{ old('about_description_primary', $settings['about_description_primary'] ?? '') }}</textarea>
            </div>

            <div>
                <label for="about_description_secondary" class="mb-2 block text-sm font-medium text-slate-700">Deskripsi Kedua</label>
                <textarea id="about_description_secondary" name="about_description_secondary" rows="4" class="w-full rounded-md border border-slate-300 px-3 py-2">{{ old('about_description_secondary', $settings['about_description_secondary'] ?? '') }}</textarea>
            </div>

            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <label for="about_image_one" class="mb-2 block text-sm font-medium text-slate-700">Gambar 1</label>
                    <input id="about_image_one" type="file" name="about_image_one" accept="image/*" class="w-full rounded-md border border-slate-300 px-3 py-2" />

                    @if (!empty($settings['about_image_one']))
                        <img src="{{ asset('storage/' . $settings['about_image_one']) }}" alt="About Image 1" class="mt-3 h-32 w-full rounded-md object-cover" />
                    @endif
                </div>

                <div>
                    <label for="about_image_two" class="mb-2 block text-sm font-medium text-slate-700">Gambar 2</label>
                    <input id="about_image_two" type="file" name="about_image_two" accept="image/*" class="w-full rounded-md border border-slate-300 px-3 py-2" />

                    @if (!empty($settings['about_image_two']))
                        <img src="{{ asset('storage/' . $settings['about_image_two']) }}" alt="About Image 2" class="mt-3 h-32 w-full rounded-md object-cover" />
                    @endif
                </div>
            </div>

            <div>
                <label for="about_video_file" class="mb-2 block text-sm font-medium text-slate-700">Video Tentang Kami (Opsional)</label>
                <input id="about_video_file" type="file" name="about_video_file" accept="video/mp4,video/webm,video/quicktime" class="w-full rounded-md border border-slate-300 px-3 py-2" />

                @if (!empty($settings['about_video_path']))
                    <video class="mt-3 h-44 w-full rounded-md" controls preload="metadata">
                        <source src="{{ asset('storage/' . $settings['about_video_path']) }}" type="video/mp4">
                    </video>
                @endif
            </div>
        </div>

        <div class="mt-5">
            <button type="submit" class="rounded-md bg-slate-900 px-4 py-2 text-sm font-semibold text-white">Simpan Perubahan</button>
        </div>
    </form>
@endsection
