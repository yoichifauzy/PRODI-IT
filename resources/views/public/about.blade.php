@extends('layouts.public')

@section('title', __('public.about.page_title'))

@section('content')
    @php
        $aboutSectionTitle = $aboutSettings['about_section_title'] ?? __('public.home.about.section_title');
        $aboutSectionSubtitle = $aboutSettings['about_section_subtitle'] ?? __('public.home.about.section_subtitle');
        $aboutHeading = $aboutSettings['about_heading'] ?? __('public.home.about.heading');
        $aboutDescriptionPrimary = $aboutSettings['about_description_primary'] ?? __('public.home.about.description_primary');
        $aboutDescriptionSecondary = $aboutSettings['about_description_secondary'] ?? __('public.home.about.description_secondary');
        $tentangImageOne = !empty($aboutSettings['about_image_one']) ? asset('storage/' . $aboutSettings['about_image_one']) : asset('storage/image/tentang-kami/prodi-it.png');
        $tentangImageTwo = !empty($aboutSettings['about_image_two']) ? asset('storage/' . $aboutSettings['about_image_two']) : asset('storage/image/tentang-kami/teknofo.png');

        $defaultMissionItems = trans('public.home.vision.default_mission_items');
        if (!is_array($defaultMissionItems) || $defaultMissionItems === []) {
            $defaultMissionItems = [];
        }

        $missionSource = $visionMission?->mission_text;
        $missionItems = filled($missionSource)
            ? preg_split('/\n+/', trim($missionSource))
            : $defaultMissionItems;

        $missionItems = array_filter($missionItems, fn($item) => trim($item) !== '');
    @endphp

    <!-- Hero Section -->
    <section class="hero-section relative min-h-screen w-full overflow-hidden bg-gradient-to-r from-amber-900 via-amber-800 to-amber-700 flex items-center justify-center">
        <div class="absolute inset-0 bg-orange-600 opacity-40" style="background-image: url('data:image/svg+xml,%3Csvg width=\'40\' height=\'40\' viewBox=\'0 0 40 40\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'0.15\' fill-rule=\'evenodd\'%3E%3Cpath d=\'M0 0h20v20H0V0zm20 20h20v20H20V20z\'/%3E%3C/g%3E%3C/svg%3E');"></div>

        <div class="relative z-10 text-center text-white px-4 py-20">
            <h1 class="text-5xl md:text-6xl font-bold mb-4 leading-tight">{{ $aboutSectionTitle }}</h1>
            <p class="text-xl md:text-2xl leading-relaxed max-w-3xl mx-auto">{{ $aboutSectionSubtitle }}</p>
            <div class="h-1 w-24 bg-red-600 mx-auto mt-8"></div>
        </div>
    </section>

    <!-- Profil IKTE Section -->
    <section class="py-16 md:py-24 px-4 md:px-8 bg-white">
        <div class="max-w-6xl mx-auto">
            <div class="mb-12">
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
                <p class="text-gray-600 text-lg">{{ __('public.home.vision.section_subtitle') }}</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Visi Card -->
                <div class="bg-white rounded-2xl border border-gray-200 p-8 shadow-md hover:shadow-lg transition-shadow">
                    <div class="flex items-center gap-4 mb-6">
                        <div class="w-12 h-12 rounded-full bg-red-600 flex items-center justify-center text-white">
                            <i class="fa-solid fa-eye text-xl"></i>
                        </div>
                        <h3 class="text-2xl md:text-3xl font-bold text-gray-900">
                            {{ $visionMission?->vision_title ?? __('public.home.vision.vision_title') }}
                        </h3>
                    </div>
                    <p class="text-gray-700 leading-relaxed text-lg">
                        {{ $visionMission?->vision_text ?: __('public.home.vision.default_vision') }}
                    </p>
                </div>

                <!-- Misi Card -->
                <div class="bg-white rounded-2xl border border-gray-200 p-8 shadow-md hover:shadow-lg transition-shadow">
                    <div class="flex items-center gap-4 mb-6">
                        <div class="w-12 h-12 rounded-full bg-yellow-400 flex items-center justify-center text-gray-900">
                            <i class="fa-solid fa-shield text-xl"></i>
                        </div>
                        <h3 class="text-2xl md:text-3xl font-bold text-gray-900">
                            {{ $visionMission?->mission_title ?? __('public.home.vision.mission_title') }}
                        </h3>
                    </div>
                    <ul class="space-y-3">
                        @foreach ($missionItems as $item)
                            @if (trim($item) !== '')
                                <li class="flex items-start gap-3">
                                    <span class="mt-1 inline-block h-2.5 w-2.5 shrink-0 rounded-full bg-red-600 flex-shrink-0"></span>
                                    <span class="text-gray-700">{{ $item }}</span>
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
