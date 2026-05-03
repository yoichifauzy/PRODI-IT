@extends('layouts.public')

@section('title', __('public.learning_outcomes.page_title'))

@section('content')
    <!-- Hero -->
    <section class="page-hero relative overflow-hidden py-20">
        <div class="page-hero-overlay absolute inset-0"></div>
        <span class="page-hero-orb page-hero-orb-left" aria-hidden="true"></span>
        <span class="page-hero-orb page-hero-orb-right" aria-hidden="true"></span>
        <div class="relative z-10 mx-auto max-w-6xl px-4 text-center text-white">
            <p class="page-hero-kicker">{{ __('public.hero.kicker') }}</p>
            <h1 class="page-hero-title mb-4">{{ __('public.learning_outcomes.hero_title') }}</h1>
            <p class="page-hero-subtitle mx-auto max-w-3xl">{{ __('public.learning_outcomes.hero_subtitle') }}</p>
            <div class="page-hero-divider mx-auto mt-6 h-1 w-24 rounded-full bg-[var(--accent)]"></div>
        </div>
    </section>

    <!-- Content -->
    <section class="py-16 md:py-24 px-4 md:px-8 bg-white">
        <div class="max-w-5xl mx-auto">
            <div class="mb-8">
                <h2 class="text-2xl font-bold mb-2">Capaian Pembelajaran Program Studi Teknologi Informasi</h2>
                <p class="text-gray-600">Daftar capaian pembelajaran Prodi TI akan diperbarui secara berkala di halaman ini.</p>
            </div>

            <div class="rounded-lg border border-[var(--border-soft)] bg-[var(--surface)] p-6">
                <p class="text-[var(--text-soft)]">Konten capaian pembelajaran masih kosong. Tim Prodi akan menambahkan daftar capaian pembelajaran di halaman ini dalam pembaruan berikutnya.</p>
            </div>
        </div>
    </section>
@endsection
