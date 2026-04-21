@extends('layouts.public')

@section('title', ($activity->title ?? __('public.activity_detail.fallback_title')) . ' | ' . __('public.activity_detail.page_suffix'))

@section('content')
    @include('public.partials.page-hero', [
        'title' => __('public.activity_detail.hero_title'),
        'subtitle' => __('public.activity_detail.hero_subtitle'),
    ])

    <section class="section-wrap public-page-shell">
        <div class="mb-6">
            <a href="{{ route('public.activities') }}" class="back-link-btn">
                <i class="fa-solid fa-arrow-left"></i>
                {{ __('public.activity_detail.back') }}
            </a>
        </div>

        @php
            $image = $activity->image_path ? asset('storage/' . $activity->image_path) : asset('image/galeri/image3.jpeg');
        @endphp

        <article class="activity-detail-shell">
            <div class="activity-detail-head">
                <span class="activity-detail-badge">{{ strtoupper($activity->category) }}</span>
                <h1 class="activity-detail-title">{{ $activity->title }}</h1>
                <div class="activity-detail-meta">
                    <p>
                        <i class="fa-regular fa-calendar"></i>
                        <span>{{ optional($activity->event_date)->format('d F Y') ?: '-' }}</span>
                    </p>
                    <p>
                        <i class="fa-solid fa-location-dot"></i>
                        <span>{{ $activity->location ?: '-' }}</span>
                    </p>
                </div>
            </div>

            <div class="activity-detail-image-wrap">
                <img src="{{ $image }}" alt="{{ $activity->title }}" class="activity-detail-image" />
            </div>

            <div class="activity-detail-content">
                <h2 class="activity-detail-subtitle">{{ __('public.activity_detail.subtitle') }}</h2>
                <p class="activity-detail-description">{{ $activity->description ?: __('public.activity_detail.description_empty') }}</p>

                <div class="activity-detail-info-box">
                    <h3>{{ __('public.activity_detail.info_title') }}</h3>
                    <p>{{ __('public.activity_detail.info_copy') }}</p>
                </div>
            </div>
        </article>

        @if ($relatedActivities->isNotEmpty())
            <section class="mt-10">
                <div class="public-subhead">
                    <h2 class="text-2xl font-black text-[var(--text-main)]">{{ __('public.activity_detail.related_title') }}</h2>
                </div>

                <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
                    @foreach ($relatedActivities as $item)
                        @php
                            $relatedImage = $item->image_path ? asset('storage/' . $item->image_path) : asset('image/galeri/image3.jpeg');
                        @endphp
                        <a href="{{ route('public.activities.show', $item) }}" class="activities-grid-card group">
                            <div class="activities-grid-media">
                                <img src="{{ $relatedImage }}" alt="{{ $item->title }}" class="h-48 w-full object-cover transition-transform duration-500 group-hover:scale-110" />
                                <span class="activities-type-badge">{{ strtoupper($item->category) }}</span>
                            </div>
                            <div class="p-4">
                                <h3 class="mb-1 text-lg font-bold text-[var(--text-main)] line-clamp-2">{{ $item->title }}</h3>
                                <p class="text-sm text-[var(--text-soft)]">{{ optional($item->event_date)->format('d M Y') ?: '-' }}</p>
                            </div>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif
    </section>
@endsection
