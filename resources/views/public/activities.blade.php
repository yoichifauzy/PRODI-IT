@extends('layouts.public')

@section('title', __('public.activities.page_title'))

@section('content')
    @include('public.partials.page-hero', [
        'title' => __('public.activities.hero_title'),
        'subtitle' => __('public.activities.hero_subtitle'),
    ])

    <section class="section-wrap public-page-shell">
        <header class="public-page-intro">
            <h2 class="public-page-title">{{ __('public.activities.intro_title') }}</h2>
            <p class="public-page-copy">{{ __('public.activities.intro_copy') }}</p>
            <div class="public-page-meta">
                <span class="meta-pill">{{ __('public.activities.meta_total') }}: {{ $activities->total() }}</span>
                <span class="meta-pill">{{ __('public.activities.meta_page') }}: {{ $activities->currentPage() }} / {{ $activities->lastPage() }}</span>
            </div>
        </header>

        <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
            @forelse ($activities as $activity)
                @php
                    $image = $activity->image_path ? asset('storage/' . $activity->image_path) : asset('image/galeri/image3.jpeg');
                @endphp
                <a href="{{ route('public.activities.show', $activity) }}" class="activities-grid-card group">
                    <div class="activities-grid-media">
                        <img src="{{ $image }}" alt="{{ $activity->title }}" class="h-56 w-full object-cover transition-transform duration-500 group-hover:scale-110" />
                        <span class="activities-type-badge">{{ strtoupper($activity->category) }}</span>
                    </div>

                    <div class="p-5">
                        <p class="activities-date-line">
                            <i class="fa-regular fa-calendar"></i>
                            <span>{{ optional($activity->event_date)->format('d F Y') ?: '-' }}</span>
                        </p>

                        <h3 class="mb-2 text-xl font-bold text-[var(--text-main)] line-clamp-2">{{ $activity->title }}</h3>
                        <p class="text-sm leading-relaxed text-[var(--text-soft)] line-clamp-3">
                            {{ \Illuminate\Support\Str::limit($activity->description ?: __('public.activities.description_empty'), 160) }}
                        </p>

                        <p class="activities-location-line mt-4">
                            <i class="fa-solid fa-location-dot"></i>
                            <span>{{ $activity->location ?: '-' }}</span>
                        </p>

                        <p class="activities-readmore mt-4">
                            {{ __('public.activities.read_more') }}
                            <i class="fa-solid fa-arrow-right"></i>
                        </p>
                    </div>
                </a>
            @empty
                <div class="rounded-xl border border-dashed border-[var(--border-soft)] bg-white p-8 text-center text-[var(--text-soft)] md:col-span-2 xl:col-span-3">
                    {{ __('public.activities.empty') }}
                </div>
            @endforelse
        </div>

        @if ($activities->hasPages())
            <div class="mt-8 rounded-xl border border-[var(--border-soft)] bg-white px-4 py-3 shadow-sm">
                {{ $activities->links() }}
            </div>
        @endif
    </section>
@endsection
