<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'PGT IT')</title>
    <link rel="icon" type="image/png" href="{{ asset('logo/logo_prodi_it_round.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="preconnect" href="https://cdnjs.cloudflare.com">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <script>
        (() => {
            const savedTheme = localStorage.getItem('site-theme');
            if (savedTheme === 'dark') {
                document.documentElement.classList.add('theme-dark');
            }

            const savedMotion = localStorage.getItem('motion-level');
            if (savedMotion === 'reduced' || savedMotion === 'normal') {
                document.documentElement.setAttribute('data-motion-level', savedMotion);
            } else {
                const sysReduced = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
                document.documentElement.setAttribute('data-motion-level', sysReduced ? 'reduced' : 'normal');
            }
        })();
    </script>
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>
<body class="site-body min-h-screen">
    @php
        $currentLocale = app()->getLocale() === 'en' ? 'en' : 'id';
    @endphp

    <div id="site-preloader" class="site-preloader" aria-hidden="true">
        <div class="site-preloader-content">
            <div class="site-preloader-syntax">&lt;/&gt;</div>
            <div class="site-preloader-progress">
                <div id="preloader-progress-fill" class="site-preloader-progress-fill"></div>
            </div>
        </div>
    </div>

    <header id="site-header" class="site-header sticky top-0 z-50" data-nav-state="top">
        <div class="mx-auto flex w-full max-w-7xl items-center gap-4 px-4 py-3">
            <a href="{{ route('home') }}" class="site-brand flex shrink-0 flex-col items-center justify-center gap-1">
                <span class="flex items-center gap-2">
                    <img src="{{ asset('logo/logo_politeknik.png') }}" alt="Logo Politeknik" class="brand-logo h-8 w-8 md:h-10 md:w-10" />
                    <img src="{{ asset('logo/logo_prodi_it.png') }}" alt="Logo Prodi TI" class="brand-logo h-8 w-8 md:h-10 md:w-10" />
                </span>
                <span class="brand-caption">PGT IT 2026</span>
            </a>

            <nav class="main-nav ml-2 hidden items-center gap-2 text-sm font-semibold md:flex">
                <a href="{{ route('home') }}" class="menu-link nav-link"><span data-i18n="nav.home">{{ __('public.nav.home') }}</span></a>

                <div class="nav-dropdown" data-nav-dropdown>
                    <button type="button" class="menu-link nav-link nav-trigger" aria-haspopup="true" aria-expanded="false" aria-controls="desktop-dropdown-profile" data-nav-trigger>
                        <span data-i18n="nav.profile_services">{{ __('public.nav.profile_services') }}</span>
                        <i class="fa-solid fa-chevron-down"></i>
                    </button>
                    <div id="desktop-dropdown-profile" class="nav-dropdown-menu" data-nav-menu role="menu">
                        <a href="{{ route('home') }}#tentang" class="dropdown-item" data-i18n="nav.about">{{ __('public.nav.about') }}</a>
                        <a href="{{ route('public.lecturer-staff') }}" class="dropdown-item" data-i18n="nav.lecturer_staff">{{ __('public.nav.lecturer_staff') }}</a>
                        <a href="{{ route('home') }}#akreditasi" class="dropdown-item" data-i18n="nav.accreditation">{{ __('public.nav.accreditation') }}</a>
                        <a href="{{ route('home') }}#kontak" class="dropdown-item" data-i18n="nav.contact">{{ __('public.nav.contact') }}</a>
                        <a href="{{ route('public.announcements') }}" class="dropdown-item" data-i18n="nav.announcements">{{ __('public.nav.announcements') }}</a>
                    </div>
                </div>

                <div class="nav-dropdown" data-nav-dropdown>
                    <button type="button" class="menu-link nav-link nav-trigger" aria-haspopup="true" aria-expanded="false" aria-controls="desktop-dropdown-academic" data-nav-trigger>
                        <span data-i18n="nav.academic">{{ __('public.nav.academic') }}</span>
                        <i class="fa-solid fa-chevron-down"></i>
                    </button>
                    <div id="desktop-dropdown-academic" class="nav-dropdown-menu" data-nav-menu role="menu">
                        <a href="{{ route('public.curriculum') }}" class="dropdown-item" data-i18n="nav.curriculum">{{ __('public.nav.curriculum') }}</a>
                        <a href="{{ route('home') }}#akreditasi" class="dropdown-item" data-i18n="nav.learning_outcomes">{{ __('public.nav.learning_outcomes') }}</a>
                    </div>
                </div>

                <div class="nav-dropdown" data-nav-dropdown>
                    <button type="button" class="menu-link nav-link nav-trigger" aria-haspopup="true" aria-expanded="false" aria-controls="desktop-dropdown-tridharma" data-nav-trigger>
                        <span data-i18n="nav.tridharma">{{ __('public.nav.tridharma') }}</span>
                        <i class="fa-solid fa-chevron-down"></i>
                    </button>
                    <div id="desktop-dropdown-tridharma" class="nav-dropdown-menu" data-nav-menu role="menu">
                        <a href="{{ route('public.research') }}" class="dropdown-item" data-i18n="nav.research">
                            {{ __('public.nav.research') }}
                        </a>
                        <a href="{{ route('public.community-service') }}" class="dropdown-item" data-i18n="nav.community_service">
                            {{ __('public.nav.community_service') }}
                        </a>
                        <a href="{{ route('home') }}#akreditasi" class="dropdown-item" data-i18n="nav.spmi">
                            {{ __('public.nav.spmi') }}
                        </a>
                    </div>
                </div>

                <div class="nav-dropdown" data-nav-dropdown>
                    <button type="button" class="menu-link nav-link nav-trigger" aria-haspopup="true" aria-expanded="false" aria-controls="desktop-dropdown-student" data-nav-trigger>
                        <span data-i18n="nav.student_alumni">{{ __('public.nav.student_alumni') }}</span>
                        <i class="fa-solid fa-chevron-down"></i>
                    </button>
                    <div id="desktop-dropdown-student" class="nav-dropdown-menu" data-nav-menu role="menu">
                        <a href="{{ route('public.projects') }}" class="dropdown-item" data-i18n="nav.student_projects">{{ __('public.nav.student_projects') }}</a>
                        <a href="{{ route('home') }}#kegiatan" class="dropdown-item" data-i18n="nav.events">{{ __('public.nav.events') }}</a>
                        <a href="{{ route('home') }}#galeri" class="dropdown-item" data-i18n="nav.gallery">{{ __('public.nav.gallery') }}</a>
                        <a href="{{ route('public.tracer-alumni') }}" class="dropdown-item" data-i18n="nav.tracer_alumni">{{ __('public.nav.tracer_alumni') }}</a>
                    </div>
                </div>

                <a href="{{ route('home') }}#aspirasi" class="menu-link nav-link"><span data-i18n="nav.aspiration">{{ __('public.nav.aspiration') }}</span></a>
                <span class="nav-hover-indicator" data-nav-indicator aria-hidden="true"></span>
            </nav>

            <div class="ml-auto flex items-center gap-2">
                <div class="lang-switch hidden sm:inline-flex" role="group" aria-label="Language switcher">
                    <a href="{{ request()->fullUrlWithQuery(['lang' => 'id']) }}" class="lang-btn {{ $currentLocale === 'id' ? 'is-active' : '' }}" data-lang-switch="id" aria-label="Bahasa Indonesia">🇮🇩</a>
                    <a href="{{ request()->fullUrlWithQuery(['lang' => 'en']) }}" class="lang-btn {{ $currentLocale === 'en' ? 'is-active' : '' }}" data-lang-switch="en" aria-label="English">🇬🇧</a>
                </div>

                <button id="theme-toggle" type="button" class="theme-toggle" aria-label="Toggle dark mode">
                    <i id="theme-icon" class="fa-regular fa-moon" aria-hidden="true"></i>
                </button>

                <button id="mobile-nav-toggle" type="button" class="theme-toggle mobile-nav-toggle md:hidden" aria-expanded="false" aria-controls="mobile-nav-wrap" aria-label="Toggle navigation menu">
                    <i id="mobile-nav-icon" class="fa-solid fa-bars" aria-hidden="true"></i>
                </button>
            </div>
        </div>

        <div id="mobile-nav-wrap" class="mobile-nav-wrap border-t border-slate-200/70 md:hidden" data-mobile-nav-state="closed">
            <div class="mx-auto max-w-7xl px-4 py-3">
                <nav class="space-y-2 text-sm font-semibold">
                    <a href="{{ route('home') }}" class="mobile-nav-link" data-i18n="nav.home">{{ __('public.nav.home') }}</a>

                    <details class="mobile-dropdown">
                        <summary>
                            <span data-i18n="nav.profile_services">{{ __('public.nav.profile_services') }}</span>
                            <i class="fa-solid fa-chevron-down" aria-hidden="true"></i>
                        </summary>
                        <div class="mobile-dropdown-content">
                            <a href="{{ route('home') }}#tentang" data-i18n="nav.about">{{ __('public.nav.about') }}</a>
                            <a href="{{ route('public.lecturer-staff') }}" data-i18n="nav.lecturer_staff">{{ __('public.nav.lecturer_staff') }}</a>
                            <a href="{{ route('home') }}#akreditasi" data-i18n="nav.accreditation">{{ __('public.nav.accreditation') }}</a>
                            <a href="{{ route('home') }}#kontak" data-i18n="nav.contact">{{ __('public.nav.contact') }}</a>
                            <a href="{{ route('public.announcements') }}" data-i18n="nav.announcements">{{ __('public.nav.announcements') }}</a>
                        </div>
                    </details>

                    <details class="mobile-dropdown">
                        <summary>
                            <span data-i18n="nav.academic">{{ __('public.nav.academic') }}</span>
                            <i class="fa-solid fa-chevron-down" aria-hidden="true"></i>
                        </summary>
                        <div class="mobile-dropdown-content">
                            <a href="{{ route('public.curriculum') }}" data-i18n="nav.curriculum">{{ __('public.nav.curriculum') }}</a>
                            <a href="{{ route('home') }}#akreditasi" data-i18n="nav.learning_outcomes">{{ __('public.nav.learning_outcomes') }}</a>
                        </div>
                    </details>

                    <details class="mobile-dropdown">
                        <summary>
                            <span data-i18n="nav.tridharma">{{ __('public.nav.tridharma') }}</span>
                            <i class="fa-solid fa-chevron-down" aria-hidden="true"></i>
                        </summary>
                        <div class="mobile-dropdown-content">
                            <a href="{{ route('home') }}#akreditasi" data-i18n="nav.research">{{ __('public.nav.research') }}</a>
                            <a href="{{ route('home') }}#akreditasi" data-i18n="nav.community_service">{{ __('public.nav.community_service') }}</a>
                            <a href="{{ route('home') }}#akreditasi" data-i18n="nav.spmi">{{ __('public.nav.spmi') }}</a>
                        </div>
                    </details>

                    <details class="mobile-dropdown">
                        <summary>
                            <span data-i18n="nav.student_alumni">{{ __('public.nav.student_alumni') }}</span>
                            <i class="fa-solid fa-chevron-down" aria-hidden="true"></i>
                        </summary>
                        <div class="mobile-dropdown-content">
                            <a href="{{ route('public.projects') }}" data-i18n="nav.student_projects">{{ __('public.nav.student_projects') }}</a>
                            <a href="{{ route('home') }}#kegiatan" data-i18n="nav.events">{{ __('public.nav.events') }}</a>
                            <a href="{{ route('home') }}#galeri" data-i18n="nav.gallery">{{ __('public.nav.gallery') }}</a>
                            <a href="{{ route('public.tracer-alumni') }}" data-i18n="nav.tracer_alumni">{{ __('public.nav.tracer_alumni') }}</a>
                        </div>
                    </details>

                    <a href="{{ route('home') }}#aspirasi" class="mobile-nav-link" data-i18n="nav.aspiration">{{ __('public.nav.aspiration') }}</a>
                </nav>
            </div>
        </div>
    </header>

    <main>
        @yield('content')
    </main>

    <footer id="kontak" class="site-footer mt-16 border-t">
        <div class="mx-auto max-w-7xl px-6 py-12">
            <div class="grid grid-cols-1 gap-10 md:grid-cols-4">
                <div>
                    <h3 class="mb-4 text-xl font-bold text-[var(--accent)]">PRODI IT</h3>
                    <p class="leading-relaxed text-[var(--text-soft)]">
                        {{ __('public.footer.description') }}
                    </p>
                </div>

                <div>
                    <h4 class="mb-4 text-lg font-semibold">{{ __('public.footer.quick_links') }}</h4>
                    <ul class="space-y-2">
                        <li><a href="{{ route('home') }}#hero" class="footer-link">{{ __('public.footer.link_home') }}</a></li>
                        <li><a href="{{ route('home') }}#tentang" class="footer-link">{{ __('public.nav.about') }}</a></li>
                        <li><a href="{{ route('public.lecturer-staff') }}" class="footer-link">{{ __('public.nav.lecturer_staff') }}</a></li>
                        <li><a href="{{ route('public.curriculum') }}" class="footer-link">{{ __('public.nav.curriculum') }}</a></li>
                        <li><a href="{{ route('public.projects') }}" class="footer-link">{{ __('public.nav.student_projects') }}</a></li>
                        <li><a href="{{ route('public.tracer-alumni') }}" class="footer-link">{{ __('public.nav.tracer_alumni') }}</a></li>
                        <li><a href="{{ route('public.announcements') }}" class="footer-link">{{ __('public.nav.announcements') }}</a></li>
                        <li><a href="{{ route('public.announcements') }}" class="footer-link">{{ __('public.footer.link_archive') }}</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="mb-4 text-lg font-semibold">{{ __('public.footer.contact') }}</h4>
                    <ul class="space-y-2 text-[var(--text-soft)]">
                        <li>Email: <a href="mailto:prodi-ti@domain.id" class="footer-link">prodi-ti@domain.id</a></li>
                        <li>{{ __('public.footer.phone') }}: +62 899-9999-9999</li>
                        <li data-i18n-ignore>{{ __('public.footer.campus_address') }}: Jl. Gajah Tunggal No.16, RT.001/RW.002, Alam Jaya, Kec. Jatiuwung, Kota Tangerang, Banten 15133</li>
                    </ul>
                </div>

                <div>
                    <h4 class="mb-4 text-lg font-semibold">{{ __('public.footer.follow') }}</h4>
                    <div class="flex space-x-4">
                        <a href="https://www.youtube.com/@IKTE_PoliteknikGT" class="social-icon youtube" aria-label="YouTube">
                            <i class="fa-brands fa-youtube"></i>
                        </a>
                        <a href="https://www.instagram.com/ikte_politeknikgt" class="social-icon instagram" aria-label="Instagram">
                            <i class="fa-brands fa-instagram"></i>
                        </a>
                        <a href="https://www.tiktok.com/@ikte_politeknikgt" class="social-icon tiktok" aria-label="TikTok">
                            <i class="fa-brands fa-tiktok"></i>
                        </a>
                        <a href="https://wa.me/6289999999999" class="social-icon whatsapp" aria-label="WhatsApp">
                            <i class="fa-brands fa-whatsapp"></i>
                        </a>
                    </div>

                    <div class="mt-4">
                        <a href="https://maps.app.goo.gl/XnurLaFJAcGDcjNW9" target="_blank" rel="noopener" class="block rounded-lg overflow-hidden border border-[var(--border-soft)]">
                            <iframe src="https://www.google.com/maps?q=Jl+Gajah+Tunggal+No+16+Alam+Jaya+Jatiuwung+Tangerang&z=15&output=embed" style="width:100%;height:140px;border:0;" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                        </a>
                    </div>
                </div>
            </div>

            <div class="mt-10 border-t pt-6 text-center text-sm text-[var(--text-soft)]">
                <p>© 2026 <span class="font-semibold text-[var(--accent)]">PRODI IT</span>. {{ __('public.footer.rights') }}</p>
            </div>
        </div>
    </footer>

    <button id="scroll-to-top" type="button" class="scroll-top-btn" aria-label="{{ __('public.common.back_to_top') }}">
        <i class="fa-solid fa-arrow-up"></i>
    </button>

    @stack('scripts')
</body>
</html>
