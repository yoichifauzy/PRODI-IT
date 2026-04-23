@extends('layouts.public')

@section('title', ($event->title ?? __('public.calendar_page.detail_fallback_title')) . ' | ' . __('public.calendar_page.page_title'))

@section('content')
    @include('public.partials.page-hero', [
        'title' => __('public.calendar_page.detail_title'),
        'subtitle' => __('public.calendar_page.detail_subtitle'),
    ])

    <section class="section-wrap public-page-shell">
        <div class="mb-5">
            <a href="{{ route('calendar.index') }}" class="back-link-btn">
                <i class="fa-solid fa-arrow-left"></i>
                <span>{{ __('public.calendar_page.back_to_calendar') }}</span>
            </a>
        </div>

        <article class="rounded-2xl border border-[var(--border-soft)] bg-white p-6 shadow-sm md:p-8">
            <p class="mb-2 text-xs font-semibold uppercase tracking-wide text-[var(--accent)]">
                {{ strtoupper($event->event_type) }}
            </p>
            <h2 class="mb-3 text-2xl font-bold text-[var(--text-main)] md:text-3xl">{{ $event->title }}</h2>

            <div class="mb-4 grid gap-2 text-sm text-[var(--text-soft)]">
                <p>
                    <span class="font-semibold text-[var(--text-main)]">{{ __('public.calendar_page.date_label') }}:</span>
                    {{ optional($event->start_date)->format('d F Y') ?: '-' }}
                    @if ($event->end_date)
                        - {{ $event->end_date->format('d F Y') }}
                    @endif
                </p>
                @if ($event->location)
                    <p>
                        <span class="font-semibold text-[var(--text-main)]">{{ __('public.calendar_page.location_prefix') }}:</span>
                        {{ $event->location }}
                    </p>
                @endif
            </div>

            <div class="rounded-xl border border-[var(--border-soft)] bg-[var(--bg-soft)] p-4 text-[var(--text-main)]">
                {!! nl2br(e($event->description ?: __('public.calendar_page.detail_empty_description'))) !!}
            </div>

            @if ($event->googleCalendarUrl() !== '')
                <a href="{{ $event->googleCalendarUrl() }}" target="_blank" rel="noopener noreferrer" class="mt-5 inline-flex items-center gap-2 text-sm font-semibold text-[var(--accent)] hover:underline">
                    <i class="fa-regular fa-calendar-plus"></i>
                    <span>{{ __('public.calendar_page.open_google') }}</span>
                </a>
            @endif
        </article>
    </section>
@endsection
