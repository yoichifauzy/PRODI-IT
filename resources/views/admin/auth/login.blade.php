<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>PGT IT | Login Admin</title>
    <link rel="icon" type="image/png" href="{{ asset('logo/logo_prodi_it_round.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="preconnect" href="https://cdnjs.cloudflare.com">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <script>
        (() => {
            const savedTheme = localStorage.getItem('site-theme');
            if (savedTheme === 'dark') {
                document.documentElement.classList.add('theme-dark');
            }
        })();
    </script>
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>
<body class="site-body login-body min-h-screen">
    <div id="site-preloader" class="site-preloader" aria-hidden="true">
        <div class="site-preloader-content">
            <div class="site-preloader-syntax">&lt;/&gt;</div>
            <div class="site-preloader-progress">
                <div id="preloader-progress-fill" class="site-preloader-progress-fill"></div>
            </div>
        </div>
    </div>

    <div class="relative min-h-screen bg-[radial-gradient(circle_at_top_right,rgba(255,122,26,0.26),transparent_44%),radial-gradient(circle_at_bottom_left,rgba(255,77,0,0.2),transparent_40%)] px-4 py-12 sm:px-6 lg:px-8">
        <div class="mx-auto flex min-h-[calc(100vh-6rem)] w-full max-w-md items-center">
            <div class="w-full space-y-7">
                <div class="text-center">
                    <div class="mb-4 flex justify-between">
                        <a href="{{ route('home') }}" class="inline-flex items-center gap-2 rounded-lg bg-white px-3 py-2 text-sm font-medium text-slate-700 shadow-md transition hover:bg-slate-100">
                            <i class="fa-solid fa-arrow-left"></i>
                            <span>Kembali</span>
                        </a>

                        <button id="theme-toggle" type="button" class="theme-toggle" aria-label="Toggle dark mode">
                            <i id="theme-icon" class="fa-regular fa-moon" aria-hidden="true"></i>
                        </button>
                    </div>

                    <div class="mb-6 flex items-center justify-center gap-4">
                        <img src="{{ asset('storage/image/tentang-kami/prodi-it.png') }}" alt="Logo Prodi IT" class="h-20 w-20 rounded-full border border-orange-200 bg-white object-cover p-0.5 shadow-lg transition-transform duration-300 hover:scale-105" />
                        <img src="{{ asset('storage/image/tentang-kami/teknofo.png') }}" alt="Logo Teknofo" class="h-20 w-20 rounded-full border border-orange-200 bg-white object-cover p-0.5 shadow-lg transition-transform duration-300 hover:scale-105" />
                    </div>

                    <div class="mb-6">
                        <h1 class="mb-2 text-3xl font-bold text-orange-700">PGT IT Admin Panel</h1>
                        <p class="text-sm tracking-wide text-slate-500">Program Studi Teknologi Informasi</p>
                    </div>

                    <h2 class="mb-2 text-2xl font-extrabold text-slate-900">Masuk ke Akun Anda</h2>
                    <p class="text-sm text-slate-600">Silakan masuk dengan akun admin untuk mengakses dashboard.</p>
                </div>

                <div class="rounded-2xl border border-orange-100 bg-white/95 p-8 shadow-2xl backdrop-blur">
                    @if (session('error'))
                        <div class="mb-4 rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-700">
                            {{ session('error') }}
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

                    <form method="POST" action="{{ route('admin.login.attempt') }}" class="space-y-6">
                        @csrf
                        <div>
                            <label for="email" class="mb-2 block text-sm font-semibold text-slate-700">Email</label>
                            <div class="relative">
                                <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                                    <i class="fa-regular fa-envelope"></i>
                                </span>
                                <input id="email" name="email" type="email" required value="{{ old('email') }}" autofocus placeholder="admin@pgt-it.ac.id" class="w-full rounded-lg border border-slate-300 bg-white py-3 pl-10 pr-3 text-slate-900 shadow-sm transition-all duration-200 focus:border-orange-500 focus:outline-none focus:ring-2 focus:ring-orange-200" />
                            </div>
                        </div>

                        <div>
                            <label for="password" class="mb-2 block text-sm font-semibold text-slate-700">Password</label>
                            <div class="relative">
                                <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                                    <i class="fa-solid fa-lock"></i>
                                </span>
                                <input id="password" name="password" type="password" required placeholder="Masukkan password Anda" class="w-full rounded-lg border border-slate-300 bg-white py-3 pl-10 pr-10 text-slate-900 shadow-sm transition-all duration-200 focus:border-orange-500 focus:outline-none focus:ring-2 focus:ring-orange-200" />
                                <button id="password-visibility-toggle" type="button" class="absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400 hover:text-slate-600">
                                    <i id="password-eye-open" class="fa-regular fa-eye"></i>
                                    <i id="password-eye-off" class="fa-regular fa-eye-slash hidden"></i>
                                </button>
                            </div>
                        </div>

                        <div class="rounded-lg border border-orange-100 bg-orange-50 px-3 py-2 text-xs text-orange-700">
                            Keamanan aktif: sesi admin tidak menggunakan mode "remember me" dan akan berakhir saat browser ditutup.
                        </div>

                        <button type="submit" class="flex w-full items-center justify-center gap-2 rounded-lg bg-orange-600 px-4 py-3 text-base font-semibold text-white shadow-md transition-all duration-200 hover:scale-[1.01] hover:bg-orange-700">
                            <i class="fa-solid fa-right-to-bracket"></i>
                            <span>Masuk</span>
                        </button>
                    </form>

                    <div class="mt-6">
                        <div class="relative">
                            <div class="absolute inset-0 flex items-center">
                                <div class="w-full border-t border-slate-200"></div>
                            </div>
                            <div class="relative flex justify-center text-sm">
                                <span class="bg-white px-2 text-slate-400">Atau</span>
                            </div>
                        </div>

                        <div class="mt-6 text-center">
                            <a href="{{ route('home') }}" class="inline-flex items-center text-sm font-medium text-orange-600 transition-colors duration-200 hover:text-orange-500">
                                <i class="fa-solid fa-arrow-left mr-2"></i>
                                <span>Kembali ke Beranda</span>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="text-center">
                    <p class="text-xs text-slate-500">© 2026 <span class="font-semibold text-orange-600">PGT IT</span>. All rights reserved.</p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
