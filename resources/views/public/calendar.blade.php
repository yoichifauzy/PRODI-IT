@extends('layouts.public')

@section('title', __('public.calendar_page.page_title'))

@section('content')
    <section class="mx-auto max-w-6xl px-4 py-12">
        <div class="mb-8 flex flex-wrap items-center justify-between gap-3">
            <div>
                <h1 class="text-3xl font-bold text-slate-900">{{ __('public.calendar_page.heading_title') }}</h1>
                <p class="mt-2 text-slate-600">{{ __('public.calendar_page.heading_subtitle') }}</p>
            </div>

            <form method="GET" action="{{ route('calendar.index') }}" class="flex items-center gap-2">
                <input type="month" name="month" value="{{ $selectedMonth }}" class="rounded-md border border-slate-300 px-3 py-2 focus:border-slate-500 focus:outline-none" />
                <button type="submit" class="rounded-md bg-slate-900 px-4 py-2 text-sm font-semibold text-white">{{ __('public.calendar_page.apply_button') }}</button>
            </form>
        </div>

        <div class="mb-6 flex flex-wrap items-center justify-between gap-3 rounded-xl border border-slate-200 bg-white p-4">
            <a href="{{ route('calendar.index', ['month' => $prevMonth->format('Y-m')]) }}" class="rounded-md border border-slate-300 px-3 py-2 text-sm text-slate-700 hover:bg-slate-50">{{ __('public.calendar_page.prev_month') }}</a>
            <p class="text-lg font-semibold text-slate-900">{{ $month->translatedFormat('F Y') }}</p>
            <a href="{{ route('calendar.index', ['month' => $nextMonth->format('Y-m')]) }}" class="rounded-md border border-slate-300 px-3 py-2 text-sm text-slate-700 hover:bg-slate-50">{{ __('public.calendar_page.next_month') }}</a>
        </div>

        <div class="grid gap-4">
            @forelse ($events as $event)
                @php
                    $typeStyle = match($event->event_type) {
                        'krs' => 'bg-blue-100 text-blue-700',
                        'uts' => 'bg-amber-100 text-amber-700',
                        'uas' => 'bg-rose-100 text-rose-700',
                        'holiday' => 'bg-emerald-100 text-emerald-700',
                        'seminar' => 'bg-violet-100 text-violet-700',
                        default => 'bg-slate-200 text-slate-700',
                    };
                @endphp

                <article class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                    <div class="mb-3 flex flex-wrap items-center gap-2">
                        <span class="rounded-full px-2 py-1 text-xs font-semibold {{ $typeStyle }}">{{ strtoupper($event->event_type) }}</span>
                        <span class="text-xs text-slate-500">{{ $event->start_date->format('d M Y') }}@if($event->end_date) - {{ $event->end_date->format('d M Y') }}@endif</span>
                    </div>
                    <h2 class="mb-2 text-xl font-semibold text-slate-900">
                        <a href="{{ route('calendar.events.show', ['academicEvent' => $event->slug]) }}" class="hover:text-[var(--accent)] hover:underline">
                            {{ $event->title }}
                        </a>
                    </h2>
                    @if ($event->description)
                        <p class="mb-3 whitespace-pre-line text-slate-700">{{ $event->description }}</p>
                    @endif
                    <div class="flex flex-wrap items-center gap-3 text-sm text-slate-600">
                        @if ($event->location)
                            <span>{{ __('public.calendar_page.location_prefix') }}: {{ $event->location }}</span>
                        @endif
                        @if ($event->googleCalendarUrl() !== '')
                            <a href="{{ $event->googleCalendarUrl() }}" target="_blank" rel="noopener noreferrer" class="text-slate-900 underline">{{ __('public.calendar_page.open_google') }}</a>
                        @endif
                    </div>
                </article>
            @empty
                <div class="rounded-xl border border-dashed border-slate-300 bg-white p-10 text-center text-slate-500">
                    {{ __('public.calendar_page.empty_month') }}
                </div>
            @endforelse
        </div>
    </section>
@endsection
