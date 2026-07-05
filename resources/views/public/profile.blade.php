@extends('layouts.public')

@section('title', __('public.about.page_title'))

@section('content')
    @php
        $isEn = app()->getLocale() === 'en';
        $aboutSectionTitle = $isEn ? __('public.home.about.section_title') : 'Profil Program Studi';
        $aboutSectionSubtitle = $isEn ? __('public.home.about.section_subtitle') : 'Mengenal lebih dekat visi, misi, dan identitas Prodi Teknologi Informasi.';
        $aboutHeading = $isEn ? __('public.home.about.heading') : 'Teknologi Informasi';
        $aboutDescriptionPrimary = $profile?->description_primary ?? ($isEn ? __('public.home.about.description_primary') : '');
        $aboutDescriptionSecondary = $profile?->description_secondary ?? ($isEn ? __('public.home.about.description_secondary') : '');
        $tentangImageOne = !empty($profile?->image_one_path) ? asset('storage/' . $profile->image_one_path) : asset('storage/image/tentang-kami/prodi-it.png');
        $tentangImageTwo = !empty($profile?->image_two_path) ? asset('storage/' . $profile->image_two_path) : asset('storage/image/tentang-kami/teknofo.png');

        $defaultMissionItems = trans('public.home.vision.default_mission_items');
        if (!is_array($defaultMissionItems) || $defaultMissionItems === []) {
            $defaultMissionItems = [];
        }

        $missionSource = $profile?->mission_text;
        $missionItems = is_array($missionSource) && !empty($missionSource)
            ? $missionSource
            : (filled($missionSource) && is_string($missionSource) ? preg_split('/\n+/', trim($missionSource)) : $defaultMissionItems);

        $missionItems = array_filter($missionItems, fn($item) => trim($item) !== '');
    @endphp

    @include('public.partials.page-hero', [
        'title' => $aboutSectionTitle,
        'subtitle' => $aboutSectionSubtitle,
    ])

    <!-- Profil IKTE Section -->
    <section class="py-16 md:py-24 px-4 md:px-8 bg-white">
        <div class="max-w-6xl mx-auto">
            <div class="mb-12">
                <a href="{{ route('home') }}"
                   class="mb-5 inline-flex items-center gap-2 rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:border-slate-400 hover:bg-slate-50">
                    <span aria-hidden="true">&larr;</span>
                    {{ __('public.about.back_to_home') }}
                </a>
                <h2 class="text-4xl md:text-5xl font-bold mb-3">{{ $aboutHeading }}</h2>
                <div class="h-1 w-24 bg-red-600"></div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-12 items-center">
                <!-- Text Content -->
                <div class="order-2 lg:order-1">
                    <p class="text-lg leading-relaxed text-gray-700 mb-4">
                        {{ $aboutDescriptionPrimary }}
                    </p>
                    <p class="text-lg leading-relaxed text-gray-700">
                        {{ $aboutDescriptionSecondary }}
                    </p>
                </div>

                <!-- Images -->
                <div class="order-1 lg:order-2">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="flex flex-col items-center justify-center">
                            <img src="{{ $tentangImageOne }}" alt="Prodi IT Logo" class="h-64 w-auto object-contain rounded-lg shadow-lg" />
                        </div>
                        <div class="flex flex-col items-center justify-center">
                            <img src="{{ $tentangImageTwo }}" alt="Teknofo Logo" class="h-64 w-auto object-contain rounded-lg shadow-lg" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Visi & Misi Section -->
    <section class="py-16 md:py-24 px-4 md:px-8 bg-gray-50">
        <div class="max-w-6xl mx-auto">
            <div class="text-center mb-16">
                <h2 class="text-4xl md:text-5xl font-bold mb-3">{{ __('public.home.vision.section_title') }}</h2>
                <p class="text-gray-600 text-lg" style="color:var(--text-soft)">{{ __('public.home.vision.section_subtitle') }}</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Visi Card -->
                <div class="bg-white rounded-2xl border border-gray-200 p-8 shadow-md hover:shadow-lg transition-shadow">
                    <div class="flex items-center gap-4 mb-6">
                        <div class="w-12 h-12 rounded-full bg-red-600 flex items-center justify-center text-white">
                            <i class="fa-solid fa-eye text-xl"></i>
                        </div>
                        <h3 class="text-2xl md:text-3xl font-bold text-gray-900">
                            {{ $isEn ? __('public.home.vision.vision_title') : __('public.home.vision.vision_title') }}
                        </h3>
                    </div>
                    <p class="text-gray-700 leading-relaxed text-lg" style="color:var(--text-soft)">
                        {{ $profile?->vision_text ?: ($isEn ? __('public.home.vision.default_vision') : __('public.home.vision.default_vision')) }}
                    </p>
                </div>

                <!-- Misi Card -->
                <div class="bg-white rounded-2xl border border-gray-200 p-8 shadow-md hover:shadow-lg transition-shadow">
                    <div class="flex items-center gap-4 mb-6">
                        <div class="w-12 h-12 rounded-full bg-yellow-400 flex items-center justify-center text-gray-900">
                            <i class="fa-solid fa-shield text-xl"></i>
                        </div>
                        <h3 class="text-2xl md:text-3xl font-bold text-gray-900">
                            {{ $isEn ? __('public.home.vision.mission_title') : __('public.home.vision.mission_title') }}
                        </h3>
                    </div>
                    <ul class="space-y-3">
                        @foreach ($missionItems as $item)
                            @if (trim($item) !== '')
                                <li class="flex items-start gap-3">
                                    <span class="mt-1 inline-block h-2.5 w-2.5 shrink-0 rounded-full bg-red-600 flex-shrink-0"></span>
                                    <span class="text-gray-700" style="color:var(--text-soft)">{{ $item }}</span>
                                </li>
                            @endif
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-16 md:py-24 px-4 md:px-8 bg-gradient-to-r from-amber-900 to-amber-700 text-white">
        <div class="max-w-4xl mx-auto text-center">
            <h2 class="text-3xl md:text-4xl font-bold mb-4">{{ __('public.about.cta_title') }}</h2>
            <p class="text-lg text-amber-100 mb-8">{{ __('public.about.cta_subtitle') }}</p>
            <div class="flex flex-wrap gap-4 justify-center">
                <a href="{{ route('home') }}#kegiatan" class="solid-cta bg-orange-500 hover:bg-orange-600 text-white px-8 py-3 rounded-lg font-semibold transition-colors">
                    {{ __('public.about.visit_team') }}
                </a>
                <a href="{{ route('home') }}#kegiatan" class="border-2 border-orange-500 text-orange-300 hover:text-white px-8 py-3 rounded-lg font-semibold transition-colors">
                    {{ __('public.about.see_activities') }}
                </a>
            </div>
        </div>
    </section>
@endsection
