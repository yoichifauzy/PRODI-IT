<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin Prodi TI')</title>
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>
<body class="min-h-screen bg-slate-100 text-slate-900">
    <header class="border-b border-slate-200 bg-white">
        <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-3">
            <div class="flex items-center gap-4">
                <a href="{{ route('home') }}" class="site-brand flex shrink-0 flex-col items-center justify-center gap-1">
                    <span class="flex items-center gap-2">
                        <img src="{{ asset('logo/logo_politeknik.png') }}" alt="Logo Politeknik" class="brand-logo h-8 w-8 md:h-10 md:w-10" />
                        <img src="{{ asset('logo/logo_prodi_it.png') }}" alt="Logo Prodi TI" class="brand-logo h-8 w-8 md:h-10 md:w-10" />
                    </span>
                    <span class="brand-caption">PGT IT 2026</span>
                </a>
                <div>
                    <p class="text-sm text-slate-500">Panel Admin</p>
                    <p class="font-semibold">Program Studi Teknologi Informasi</p>
                </div>
            </div>
            <div class="flex items-center gap-4 text-sm">
                <span class="text-slate-600">{{ auth()->user()?->name }}</span>
                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button type="submit" class="rounded-md bg-slate-900 px-3 py-2 text-white">Logout</button>
                </form>
            </div>
        </div>
    </header>

    <div class="mx-auto grid max-w-7xl gap-6 px-4 py-6 lg:grid-cols-[220px_1fr]">
        <aside class="rounded-xl border border-slate-200 bg-white p-4">
            <nav class="space-y-2 text-sm">
                <a href="{{ route('admin.dashboard') }}" class="block rounded-md px-3 py-2 {{ request()->routeIs('admin.dashboard') ? 'bg-slate-900 text-white' : 'hover:bg-slate-100' }}">Dashboard</a>
                <a href="{{ route('admin.hero-slides.index') }}" class="block rounded-md px-3 py-2 {{ request()->routeIs('admin.hero-slides.*') ? 'bg-slate-900 text-white' : 'hover:bg-slate-100' }}">Hero Section</a>
                <a href="{{ route('admin.about-section.edit') }}" class="block rounded-md px-3 py-2 {{ request()->routeIs('admin.about-section.*') ? 'bg-slate-900 text-white' : 'hover:bg-slate-100' }}">Tentang Kami</a>
                <a href="{{ route('admin.activities.index') }}" class="block rounded-md px-3 py-2 {{ request()->routeIs('admin.activities.*') ? 'bg-slate-900 text-white' : 'hover:bg-slate-100' }}">Kegiatan Kami</a>
                <a href="{{ route('admin.galleries.index') }}" class="block rounded-md px-3 py-2 {{ request()->routeIs('admin.galleries.*') ? 'bg-slate-900 text-white' : 'hover:bg-slate-100' }}">Filter Galeri</a>
                <a href="{{ route('admin.gallery-items.index') }}" class="block rounded-md px-3 py-2 {{ request()->routeIs('admin.gallery-items.*') ? 'bg-slate-900 text-white' : 'hover:bg-slate-100' }}">Item Galeri</a>
                <a href="{{ route('admin.lecturer-staff.index') }}" class="block rounded-md px-3 py-2 {{ request()->routeIs('admin.lecturer-staff.*') ? 'bg-slate-900 text-white' : 'hover:bg-slate-100' }}">Dosen & Staff</a>
                <a href="{{ route('admin.curricula.index') }}" class="block rounded-md px-3 py-2 {{ request()->routeIs('admin.curricula.*') ? 'bg-slate-900 text-white' : 'hover:bg-slate-100' }}">Kurikulum</a>
                <a href="{{ route('admin.curriculum-courses.index') }}" class="block rounded-md px-3 py-2 {{ request()->routeIs('admin.curriculum-courses.*') ? 'bg-slate-900 text-white' : 'hover:bg-slate-100' }}">Mata Kuliah Kurikulum</a>
                <a href="{{ route('admin.projects.index') }}" class="block rounded-md px-3 py-2 {{ request()->routeIs('admin.projects.*') ? 'bg-slate-900 text-white' : 'hover:bg-slate-100' }}">Project Mahasiswa</a>
                <a href="{{ route('admin.tracer-alumni.index') }}" class="block rounded-md px-3 py-2 {{ request()->routeIs('admin.tracer-alumni.*') ? 'bg-slate-900 text-white' : 'hover:bg-slate-100' }}">Tracer Alumni</a>
                <a href="{{ route('admin.announcements.index') }}" class="block rounded-md px-3 py-2 {{ request()->routeIs('admin.announcements.*') ? 'bg-slate-900 text-white' : 'hover:bg-slate-100' }}">Pengumuman</a>
                <a href="{{ route('admin.academic-events.index') }}" class="block rounded-md px-3 py-2 {{ request()->routeIs('admin.academic-events.*') ? 'bg-slate-900 text-white' : 'hover:bg-slate-100' }}">Kalender Akademik</a>
                <a href="{{ route('admin.vision-missions.index') }}" class="block rounded-md px-3 py-2 {{ request()->routeIs('admin.vision-missions.*') ? 'bg-slate-900 text-white' : 'hover:bg-slate-100' }}">Visi & Misi</a>
                <a href="{{ route('admin.aspirations.index') }}" class="block rounded-md px-3 py-2 {{ request()->routeIs('admin.aspirations.*') ? 'bg-slate-900 text-white' : 'hover:bg-slate-100' }}">Aspirasi</a>
            </nav>
        </aside>

        <main>
            @if (session('success'))
                <div class="mb-4 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-4 rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                    <ul class="list-disc pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </main>
    </div>
</body>
</html>
