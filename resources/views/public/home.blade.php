@extends('layouts.public')

@section('title', __('public.home.page_title'))

@section('content')
    @php
        $defaultHeroSlides = [
            asset('storage/image/content/image1.jpeg'),
            asset('storage/image/content/image2.jpeg'),
            asset('storage/image/content/image3.jpeg'),
            asset('storage/image/content/image1.jpeg'),
            asset('storage/image/content/image2.jpeg'),
            asset('storage/image/content/image3.jpeg'),
        ];

        $heroSlides = isset($heroSlidesFromDb) && $heroSlidesFromDb->isNotEmpty()
            ? $heroSlidesFromDb->map(fn($slide) => asset('storage/' . $slide->image_path))->values()->all()
            : $defaultHeroSlides;

        $defaultKegiatanImage = asset('storage/image/kegiatan/image2.jpeg');

        $aboutSectionTitle = $aboutSettings['about_section_title'] ?? __('public.home.about.section_title');
        $aboutSectionSubtitle = $aboutSettings['about_section_subtitle'] ?? __('public.home.about.section_subtitle');
        $aboutHeading = $aboutSettings['about_heading'] ?? __('public.home.about.heading');
        $aboutDescriptionPrimary = $aboutSettings['about_description_primary'] ?? __('public.home.about.description_primary');
        $aboutDescriptionSecondary = $aboutSettings['about_description_secondary'] ?? __('public.home.about.description_secondary');
        $tentangImageOne = !empty($aboutSettings['about_image_one']) ? asset('storage/' . $aboutSettings['about_image_one']) : asset('storage/image/tentang-kami/prodi-it.png');
        $tentangImageTwo = !empty($aboutSettings['about_image_two']) ? asset('storage/' . $aboutSettings['about_image_two']) : asset('storage/image/tentang-kami/teknofo.png');
        $aboutVideoSrc = !empty($aboutSettings['about_video_path']) ? asset('storage/' . $aboutSettings['about_video_path']) : asset('video/video_about.mp4');

        $heroWordsId = trans('public.home.hero.words.id', [], 'id');
        $heroWordsEn = trans('public.home.hero.words.en', [], 'en');
        if (!is_array($heroWordsId) || $heroWordsId === []) {
            $heroWordsId = ['PRODI TEKNOLOGI INFORMASI'];
        }
        if (!is_array($heroWordsEn) || $heroWordsEn === []) {
            $heroWordsEn = ['INFORMATION TECHNOLOGY STUDY PROGRAM'];
        }
        $heroWordsDefault = app()->getLocale() === 'en' ? ($heroWordsEn[0] ?? '') : ($heroWordsId[0] ?? '');

        $defaultKegiatanItems = [
            ['title' => 'E-LINK Competition', 'category' => 'Lomba', 'date' => '25 Feb 2026', 'description' => 'Kompetisi teknologi lintas kampus yang mendorong inovasi digital berbasis solusi industri serta menantang peserta menyusun produk kreatif yang siap diujicoba.', 'location' => 'Aula & Lab Otomasi Politeknik Gajah Tunggal'],
            ['title' => 'JABRIK: Jajan Bareng IKTE', 'category' => 'Kewirausahaan', 'date' => '10 Nov 2025', 'description' => 'Kegiatan kolaboratif untuk memperkuat jiwa entrepreneur mahasiswa lewat produk kreatif sekaligus melatih strategi pemasaran, komunikasi, dan teamwork antar divisi.', 'location' => 'Taman Elektronika'],
            ['title' => 'STUBAN: Study Banding', 'category' => 'Studi Banding', 'date' => '07 Nov 2025', 'description' => 'Forum berbagi praktik baik organisasi mahasiswa dan budaya akademik antar perguruan tinggi guna memperluas jejaring serta meningkatkan kualitas program kerja.', 'location' => 'Aula Politeknik Gajah Tunggal'],
            ['title' => 'MABES IKTE', 'category' => 'Organisasi', 'date' => '02 Okt 2025', 'description' => 'Program penguatan karakter kepemimpinan dan sinergi antar divisi organisasi kemahasiswaan melalui simulasi project, evaluasi tim, serta mentoring berkala.', 'location' => 'Gedung Kemahasiswaan'],
            ['title' => 'RISE Bootcamp', 'category' => 'Pelatihan', 'date' => '16 Sep 2025', 'description' => 'Pelatihan intensif pengembangan skill coding, desain produk, dan presentasi proyek digital agar mahasiswa mampu merancang solusi teknologi yang terukur.', 'location' => 'Lab Komputer'],
            ['title' => 'Makrab Angkatan', 'category' => 'Keakraban', 'date' => '12 Sep 2025', 'description' => 'Agenda membangun kebersamaan lintas angkatan untuk memperkuat budaya kolaboratif prodi, menumbuhkan empati, dan membentuk komunikasi tim yang sehat.', 'location' => 'Villa Cisarua'],
            ['title' => 'Olah Raga Bersama', 'category' => 'Olahraga', 'date' => '02 Agu 2025', 'description' => 'Aktivitas olahraga rutin untuk meningkatkan kesehatan, sportivitas, dan semangat tim dalam suasana rekreatif yang mendukung kebugaran civitas prodi.', 'location' => 'Lapangan Kampus'],
            ['title' => 'Open Recruitment', 'category' => 'OPREC', 'date' => '22 Jul 2025', 'description' => 'Seleksi terbuka anggota baru untuk regenerasi organisasi dan penguatan program kerja melalui rangkaian seleksi sikap, komitmen, dan potensi kontribusi.', 'location' => 'Auditorium'],
            ['title' => 'Wisuda Organisasi', 'category' => 'Wisuda', 'date' => '30 Jun 2025', 'description' => 'Momen apresiasi kontribusi pengurus dan kader aktif selama satu periode kepengurusan disertai refleksi capaian serta penyerahan estafet kepemimpinan.', 'location' => 'Hall Utama'],
        ];

        $kegiatanItems = isset($activitiesFromDb) && $activitiesFromDb->isNotEmpty()
            ? $activitiesFromDb->map(function ($activity) {
                return [
                    'id' => $activity->id,
                    'title' => $activity->title,
                    'category' => $activity->category,
                    'date' => optional($activity->event_date)->format('d M Y') ?: '-',
                    'description' => $activity->description,
                    'location' => $activity->location,
                    'image' => $activity->image_path ? asset('storage/' . $activity->image_path) : null,
                ];
            })->values()->all()
            : $defaultKegiatanItems;

        $defaultGalleryCategories = [
            'all' => __('public.home.gallery.category_all'),
            'farewell' => 'FAREWELL DOSEN',
            'jabrik' => 'JABRIK',
            'kompetisi' => 'KOMPETISI',
            'mabes' => 'MABES IKTE',
            'makrab' => 'MAKRAB',
            'olahraga' => 'OLAHRAGA',
            'oprec' => 'OPREC',
            'organisasi' => 'ORGANISASI',
            'rabi' => 'RABI',
            'rise' => 'RISE',
            'stuban' => 'STUBAN',
            'wisuda' => 'WISUDA',
        ];

        $galleryCategories = isset($galleryCategoriesFromDb) && count($galleryCategoriesFromDb) > 1
            ? $galleryCategoriesFromDb
            : $defaultGalleryCategories;

        $defaultGalleryImage = asset('image/galeri/image3.jpeg');
        $defaultGalleryItems = [
            ['category' => 'farewell', 'category_label' => 'Farewell Dosen', 'title' => 'Farewell Dosen', 'caption' => null, 'image' => $defaultGalleryImage],
            ['category' => 'jabrik', 'category_label' => 'Jabrik', 'title' => 'Jabrik', 'caption' => null, 'image' => $defaultGalleryImage],
            ['category' => 'kompetisi', 'category_label' => 'Kompetisi', 'title' => 'Kompetisi', 'caption' => null, 'image' => $defaultGalleryImage],
            ['category' => 'mabes', 'category_label' => 'Mabes IKTE', 'title' => 'Mabes IKTE', 'caption' => null, 'image' => $defaultGalleryImage],
            ['category' => 'makrab', 'category_label' => 'Makrab', 'title' => 'Makrab', 'caption' => null, 'image' => $defaultGalleryImage],
            ['category' => 'olahraga', 'category_label' => 'Olahraga', 'title' => 'Olahraga', 'caption' => null, 'image' => $defaultGalleryImage],
            ['category' => 'stuban', 'category_label' => 'Study Banding', 'title' => 'Study Banding', 'caption' => null, 'image' => $defaultGalleryImage],
            ['category' => 'wisuda', 'category_label' => 'Wisuda', 'title' => 'Wisuda', 'caption' => null, 'image' => $defaultGalleryImage],
        ];

        $galleryItems = isset($galleryItemsFromDb) && $galleryItemsFromDb->isNotEmpty()
            ? $galleryItemsFromDb->values()->all()
            : $defaultGalleryItems;

        $googleCalendarEmbedSrc = rawurlencode((string) config('services.google_calendar.embed_id'));

        $akreditasiArticles = [
            [
                'title' => __('public.home.accreditation.article_1_title'),
                'excerpt' => __('public.home.accreditation.article_1_excerpt'),
            ],
            [
                'title' => __('public.home.accreditation.article_2_title'),
                'excerpt' => __('public.home.accreditation.article_2_excerpt'),
            ],
            [
                'title' => __('public.home.accreditation.article_3_title'),
                'excerpt' => __('public.home.accreditation.article_3_excerpt'),
            ],
        ];

        $defaultVision = __('public.home.vision.default_vision');
        $defaultMissionItems = trans('public.home.vision.default_mission_items');
        if (!is_array($defaultMissionItems) || $defaultMissionItems === []) {
            $defaultMissionItems = [];
        }

        $missionSource = $visionMission?->mission_text;
        $missionItems = filled($missionSource)
            ? (preg_split('/\r\n|\r|\n/', $missionSource) ?: [])
            : $defaultMissionItems;
    @endphp

    <section id="hero" data-nav-theme="hero" class="hero-section relative isolate overflow-hidden">
        <div class="absolute inset-0">
            @foreach ($heroSlides as $slide)
                <div
                    data-hero-slide
                    class="hero-slide absolute inset-0 bg-cover bg-center transition-opacity duration-700 {{ $loop->first ? 'opacity-100' : 'opacity-0' }}"
                    style="background-image: url('{{ $slide }}');"
                ></div>
            @endforeach
        </div>

        <div class="absolute inset-0 bg-black/55"></div>

        <div class="relative z-10 flex min-h-[92vh] items-center justify-center px-4 py-16 text-center text-white">
            <div class="w-full max-w-6xl">
                <p class="hero-campus-title mb-4">{{ __('public.home.hero.campus_title') }}</p>
                <h1 class="mx-auto mb-6 max-w-5xl text-5xl font-black leading-tight md:text-7xl">
                    <span
                        id="typewriter-text"
                        class="hero-type-glow"
                        data-words-id='@json($heroWordsId)'
                        data-words-en='@json($heroWordsEn)'
                        data-words='@json(app()->getLocale() === "en" ? $heroWordsEn : $heroWordsId)'
                    >{{ $heroWordsDefault }}</span>
                    <span class="typing-cursor">|</span>
                </h1>
                <p class="mx-auto mb-8 max-w-3xl text-xl italic md:text-2xl">{{ __('public.home.hero.motto') }}</p>

                <div class="flex flex-wrap justify-center gap-4">
                    <a href="#tentang" class="hero-btn hero-btn-primary" data-i18n="hero.cta.about">{{ __('public.home.about.section_title') }}</a>
                    <a href="#aspirasi" class="hero-btn hero-btn-primary" data-i18n="hero.cta.aspiration">{{ __('public.home.aspiration.section_title') }}</a>
                </div>
            </div>
        </div>

        <div class="absolute bottom-8 left-1/2 flex -translate-x-1/2 transform space-x-2">
            @foreach ($heroSlides as $slide)
                <button
                    data-hero-dot
                    data-index="{{ $loop->index }}"
                    class="hero-dot {{ $loop->first ? 'is-active' : '' }}"
                    aria-label="{{ __('public.home.hero.slide_aria', ['number' => $loop->iteration]) }}"
                ></button>
            @endforeach
        </div>
    </section>

    <section id="tentang" data-nav-theme="light" class="reveal-section section-wrap">
        <div class="section-head">
            <h2 class="section-title" data-i18n="section.about.title">{{ $aboutSectionTitle }}</h2>
            <p class="section-subtitle" data-i18n="section.about.subtitle">{{ $aboutSectionSubtitle }}</p>
            <span class="section-line"></span>
        </div>

        <div class="grid gap-10 lg:grid-cols-2 lg:items-center">
            <div class="order-last">
                <h3 class="mb-5 text-4xl font-bold leading-tight" data-i18n="about.heading">{{ $aboutHeading }}</h3>
                <p class="mb-4 text-xl leading-relaxed text-[var(--text-soft)]" data-i18n="about.description_primary">
                    {{ $aboutDescriptionPrimary }}
                </p>
                <p class="mb-7 text-xl leading-relaxed text-[var(--text-soft)]" data-i18n="about.description_secondary">
                    {{ $aboutDescriptionSecondary }}
                </p>

                <a href="{{ route('public.lecturer-staff') }}" class="solid-cta" data-i18n="about.explore_more">{{ __('public.home.about.explore_more') }}</a>
            </div>

            <div>
                <div class="relative cursor-pointer group" data-video-open>
                    <div class="grid grid-cols-2 gap-4">
                        <img src="{{ $tentangImageOne }}" alt="Prodi IT" class="h-[300px] w-full rounded-xl border border-[var(--border-soft)] object-cover shadow-lg" />
                        <img src="{{ $tentangImageTwo }}" alt="Teknofo" class="h-[300px] w-full rounded-xl border border-[var(--border-soft)] object-cover shadow-lg" />
                    </div>

                    <div class="absolute inset-0 flex items-center justify-center">
                        <span class="video-play-btn"><i class="fa-solid fa-play"></i></span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="visi-misi" data-nav-theme="soft" class="reveal-section section-wrap section-alt">
        <div class="section-head">
            <h2 class="section-title" data-i18n="section.vision.title">{{ __('public.home.vision.section_title') }}</h2>
            <p class="section-subtitle" data-i18n="section.vision.subtitle">{{ __('public.home.vision.section_subtitle') }}</p>
            <span class="section-line"></span>
        </div>

        <div class="grid gap-6 lg:grid-cols-2">
            <article class="rounded-2xl border border-[var(--border-soft)] bg-white p-6 shadow-md">
                <h3 class="mb-4 text-3xl font-bold text-[var(--accent)]">{{ $visionMission?->vision_title ?? __('public.home.vision.vision_title') }}</h3>
                <p class="leading-relaxed text-[var(--text-soft)]">
                    {{ $visionMission?->vision_text ?: $defaultVision }}
                </p>
            </article>

            <article class="rounded-2xl border border-[var(--border-soft)] bg-white p-6 shadow-md">
                <h3 class="mb-4 text-3xl font-bold text-[var(--accent)]">{{ $visionMission?->mission_title ?? __('public.home.vision.mission_title') }}</h3>
                <ul class="space-y-3 text-[var(--text-soft)]">
                    @foreach ($missionItems as $item)
                        @if (trim($item) !== '')
                            <li class="flex items-start gap-3">
                                <span class="mt-1 inline-block h-2.5 w-2.5 shrink-0 rounded-full bg-[var(--accent)]"></span>
                                <span>{{ $item }}</span>
                            </li>
                        @endif
                    @endforeach
                </ul>
            </article>
        </div>
    </section>

    <section id="acara" data-nav-theme="soft" class="reveal-section section-wrap section-alt">
        <div class="section-head">
            <h2 class="section-title" data-i18n="section.activities.title">{{ __('public.home.activities.section_title') }}</h2>
            <p class="section-subtitle" data-i18n="section.activities.subtitle">{{ __('public.home.activities.section_subtitle') }}</p>
            <span class="section-line"></span>
        </div>
        <div class="max-w-7xl mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($kegiatanItems as $kegiatan)
                    @php
                        $activityUrl = isset($kegiatan['id']) && $kegiatan['id'] !== null
                            ? route('public.activities.show', ['activity' => $kegiatan['id']])
                            : route('public.activities');
                    @endphp
                    <a href="{{ $activityUrl }}" class="activity-card group block w-full">
                        <img src="{{ $kegiatan['image'] ?? $defaultKegiatanImage }}" alt="{{ $kegiatan['title'] }}" class="h-48 w-full object-cover" />
                        <div class="p-4">
                            <div class="mb-2 flex items-center justify-between text-xs">
                                <span class="rounded-full bg-orange-100 px-3 py-1 font-medium text-[var(--accent)]">{{ $kegiatan['category'] }}</span>
                                <span class="text-[var(--text-soft)]">{{ $kegiatan['date'] }}</span>
                            </div>
                            <h3 class="mb-2 text-lg font-bold leading-tight">{{ $kegiatan['title'] }}</h3>
                            <p class="activity-description mb-2.5 text-[13px] leading-relaxed text-[var(--text-soft)]">{{ $kegiatan['description'] }}</p>
                            <p class="activity-location text-[var(--text-soft)]">
                                <i class="fa-solid fa-location-dot"></i>
                                <span class="text-[13px]">{{ $kegiatan['location'] }}</span>
                            </p>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>

        <div class="mt-10 text-center">
            <a href="{{ route('public.activities') }}" class="activity-more-btn">
                <span data-i18n="section.activities.view_all">{{ __('public.home.activities.view_all') }}</span>
                <i class="fa-solid fa-arrow-right"></i>
            </a>
        </div>
    </section>

    <section id="galeri" data-nav-theme="light" class="reveal-section section-wrap">
        <div class="section-head">
            <h2 class="section-title" data-i18n="section.gallery.title">{{ __('public.home.gallery.section_title') }}</h2>
            <p class="section-subtitle" data-i18n="section.gallery.subtitle">{{ __('public.home.gallery.section_subtitle') }}</p>
            <span class="section-line"></span>
        </div>

        <div class="mb-8 flex flex-wrap justify-center gap-3">
            @foreach ($galleryCategories as $slug => $name)
                <button
                    type="button"
                    data-gallery-filter="{{ $slug }}"
                    class="gallery-filter-btn {{ $slug === 'all' ? 'is-active' : '' }}"
                >
                    {{ $name }}
                </button>
            @endforeach
        </div>

        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ($galleryItems as $item)
                <article
                    data-gallery-item="{{ $item['category'] }}"
                    data-gallery-open
                    data-gallery-image="{{ $item['image'] }}"
                    data-gallery-title="{{ $item['title'] }}"
                    data-gallery-category="{{ $item['category_label'] ?? strtoupper($item['category']) }}"
                    class="gallery-card group cursor-pointer"
                >
                    <div class="gallery-media relative overflow-hidden">
                        <img src="{{ $item['image'] }}" alt="{{ $item['title'] }}" class="gallery-image h-56 w-full object-cover transition-transform duration-500 ease-out group-hover:scale-110" />
                        <div class="gallery-overlay">
                            <p class="text-xs uppercase tracking-[0.2em] text-orange-200">{{ strtoupper($item['category_label'] ?? $item['category']) }}</p>
                            <p class="mt-1 text-lg font-bold text-white">{{ $item['title'] }}</p>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>

        <div class="mt-10 text-center">
            <a href="{{ route('public.galleries') }}" class="solid-cta gallery-more-btn gap-2">
                <span data-i18n="section.gallery.view_all">{{ __('public.home.gallery.view_all') }}</span>
                <i class="fa-solid fa-arrow-right"></i>
            </a>
        </div>
    </section>

    <section id="kalender" data-nav-theme="soft" class="reveal-section section-wrap section-alt">
        <div class="section-head">
            <h2 class="section-title" data-i18n="section.calendar.title">{{ __('public.home.calendar.section_title') }}</h2>
            <p class="section-subtitle" data-i18n="section.calendar.subtitle">{{ __('public.home.calendar.section_subtitle') }}</p>
            <span class="section-line"></span>
        </div>

        <div class="overflow-hidden rounded-2xl border border-[var(--border-soft)] bg-white shadow-md">
            <iframe
                title="{{ __('public.home.calendar.iframe_title') }}"
                src="https://calendar.google.com/calendar/embed?height=700&wkst=1&bgcolor=%23ffffff&ctz=Asia%2FJakarta&showTitle=0&showPrint=0&showTabs=1&showCalendars=0&showTz=1&src={{ $googleCalendarEmbedSrc }}&color=%230B8043"
                style="border: 0"
                width="100%"
                height="700"
                frameborder="0"
                scrolling="no"
            ></iframe>
        </div>
    </section>

    <section id="aspirasi" data-nav-theme="light" class="reveal-section section-wrap">
        <div class="section-head">
            <h2 class="section-title" data-i18n="section.aspiration.title">{{ __('public.home.aspiration.section_title') }}</h2>
            <p class="section-subtitle" data-i18n="section.aspiration.subtitle">{{ __('public.home.aspiration.section_subtitle') }}</p>
            <span class="section-line"></span>
        </div>

        @if (session('success'))
            <div class="mb-6 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-emerald-700">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-6 rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-rose-700">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('aspirations.store') }}" method="POST" class="mx-auto max-w-5xl rounded-2xl border border-[var(--border-soft)] bg-white p-6 shadow-lg md:p-8">
            @csrf
            <div class="mb-6 grid gap-6 md:grid-cols-2">
                <div>
                    <label for="full_name" class="mb-2 block text-sm font-semibold">{{ __('public.home.aspiration.field_full_name') }}</label>
                    <input id="full_name" name="full_name" value="{{ old('full_name') }}" required class="form-input" />
                </div>
                <div>
                    <label for="email" class="mb-2 block text-sm font-semibold">{{ __('public.home.aspiration.field_email') }}</label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" required class="form-input" />
                </div>
            </div>
            <div class="mb-6 grid gap-6 md:grid-cols-2">
                <div>
                    <label for="nim" class="mb-2 block text-sm font-semibold">{{ __('public.home.aspiration.field_nim') }}</label>
                    <input id="nim" name="nim" value="{{ old('nim') }}" class="form-input" />
                </div>
                <div>
                    <label for="subject" class="mb-2 block text-sm font-semibold">{{ __('public.home.aspiration.field_subject') }}</label>
                    <input id="subject" name="subject" value="{{ old('subject') }}" required class="form-input" />
                </div>
            </div>
            <div class="grid gap-5">
                <div class="md:col-span-2">
                    <label for="message" class="mb-2 block text-sm font-semibold">{{ __('public.home.aspiration.field_message') }}</label>
                    <textarea id="message" name="message" rows="6" required class="form-input">{{ old('message') }}</textarea>
                </div>
                <div class="md:col-span-2">
                    <button type="submit" class="solid-cta w-full">{{ __('public.home.aspiration.submit') }}</button>
                </div>
            </div>
        </form>
    </section>

    <section id="akreditasi" data-nav-theme="soft" class="reveal-section section-wrap section-alt">
        <div class="section-head">
            <h2 class="section-title" data-i18n="section.accreditation.title">{{ __('public.home.accreditation.section_title') }}</h2>
            <p class="section-subtitle" data-i18n="section.accreditation.subtitle">{{ __('public.home.accreditation.section_subtitle') }}</p>
            <span class="section-line"></span>
        </div>

        <div class="grid gap-8 lg:grid-cols-2">
            <div class="space-y-4">
                @foreach ($akreditasiArticles as $article)
                    <article class="rounded-xl border border-[var(--border-soft)] bg-white p-5 shadow-sm">
                        <h3 class="mb-2 text-2xl font-bold">{{ $article['title'] }}</h3>
                        <p class="text-[var(--text-soft)]">{{ $article['excerpt'] }}</p>
                    </article>
                @endforeach
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <article class="rounded-xl border border-[var(--border-soft)] bg-white p-5 text-center shadow-sm">
                    <img src="{{ asset('logo/logo_prodi_it.png') }}" alt="{{ __('public.home.accreditation.cert_prodi_alt') }}" class="mx-auto mb-4 h-28 w-28 object-contain" />
                    <h4 class="font-bold">{{ __('public.home.accreditation.cert_prodi_title') }}</h4>
                    <p class="mt-2 text-sm text-[var(--text-soft)]">{{ __('public.home.accreditation.cert_prodi_excerpt') }}</p>
                </article>

                <article class="rounded-xl border border-[var(--border-soft)] bg-white p-5 text-center shadow-sm">
                    <img src="{{ asset('logo/logo_politeknik.png') }}" alt="{{ __('public.home.accreditation.cert_institution_alt') }}" class="mx-auto mb-4 h-28 w-28 object-contain" />
                    <h4 class="font-bold">{{ __('public.home.accreditation.cert_institution_title') }}</h4>
                    <p class="mt-2 text-sm text-[var(--text-soft)]">{{ __('public.home.accreditation.cert_institution_excerpt') }}</p>
                </article>

                <article class="rounded-xl border border-[var(--border-soft)] bg-white p-5 text-center shadow-sm sm:col-span-2">
                    <h4 class="mb-2 font-bold">{{ __('public.home.accreditation.status_title') }}</h4>
                    <p class="text-sm text-[var(--text-soft)]">{{ __('public.home.accreditation.status_excerpt') }}</p>
                </article>
            </div>
        </div>
    </section>

    <div id="gallery-lightbox" class="gallery-lightbox hidden">
        <button type="button" class="gallery-lightbox-close" data-gallery-close aria-label="{{ __('public.home.lightbox.close') }}">
            <i class="fa-solid fa-xmark"></i>
        </button>

        <button type="button" class="gallery-lightbox-nav gallery-lightbox-prev" data-gallery-prev aria-label="{{ __('public.home.lightbox.prev') }}">
            <i class="fa-solid fa-chevron-left"></i>
        </button>

        <figure class="gallery-lightbox-figure">
            <div class="gallery-lightbox-counter" id="gallery-lightbox-counter">1 / 1</div>
            <img id="gallery-lightbox-image" src="" alt="{{ __('public.home.lightbox.preview_alt') }}" class="gallery-lightbox-image" />
            <figcaption class="gallery-lightbox-caption">
                <h4 id="gallery-lightbox-title" class="text-3xl font-bold text-white">-</h4>
                <p id="gallery-lightbox-category" class="mt-1 text-sm uppercase tracking-[0.18em] text-orange-300">-</p>
            </figcaption>
        </figure>

        <button type="button" class="gallery-lightbox-nav gallery-lightbox-next" data-gallery-next aria-label="{{ __('public.home.lightbox.next') }}">
            <i class="fa-solid fa-chevron-right"></i>
        </button>
    </div>

    <div id="about-video-modal" class="fixed inset-0 z-[80] hidden items-center justify-center bg-black/70 p-4">
        <div class="relative w-full max-w-4xl overflow-hidden rounded-2xl bg-black shadow-2xl">
            <button data-video-close class="absolute right-3 top-3 z-10 rounded-md bg-white/90 px-3 py-1 text-sm font-semibold text-slate-900">{{ __('public.home.video.close') }}</button>
            <video id="about-video-player" controls class="h-full w-full" preload="metadata">
                <source src="{{ $aboutVideoSrc }}" type="video/mp4">
            </video>
        </div>
    </div>
@endsection

