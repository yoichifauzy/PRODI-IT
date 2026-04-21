@extends('layouts.public')

@section('title', __('public.curriculum.page_title'))

@section('content')
    @include('public.partials.page-hero', [
        'title' => __('public.curriculum.hero_title'),
        'subtitle' => __('public.curriculum.hero_subtitle'),
    ])

    <section class="section-wrap public-page-shell">
        <header class="public-page-intro">
            <h2 class="public-page-title">{{ __('public.curriculum.intro_title') }}</h2>
            <p class="public-page-copy">{{ __('public.curriculum.intro_copy') }}</p>
        </header>

        <div class="curriculum-filter-wrap mb-6 flex flex-wrap justify-center gap-3">
            @foreach ($curricula as $curriculum)
                <a href="{{ route('public.curriculum', ['curriculum' => $curriculum->id]) }}" class="curriculum-filter-btn {{ optional($selectedCurriculum)->id === $curriculum->id ? 'is-active' : '' }}">
                    {{ $curriculum->name }}
                </a>
            @endforeach
        </div>

        @if ($selectedCurriculum)
            <div class="public-panel curriculum-panel rounded-2xl border border-[var(--border-soft)] bg-white p-5 shadow-sm">
                <div class="mb-4 flex flex-wrap items-end justify-between gap-3">
                    <div>
                        <h2 class="mb-1 text-2xl font-bold">{{ $selectedCurriculum->name }}</h2>
                        <p class="text-sm text-[var(--text-soft)]">{{ __('public.curriculum.meta_academic_year') }}: {{ $selectedCurriculum->academic_year ?: '-' }}</p>
                    </div>
                    <span class="meta-pill">{{ __('public.curriculum.meta_total_courses') }}: {{ $selectedCurriculum->courses->count() }}</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="curriculum-table min-w-full text-sm">
                        <thead class="curriculum-table-head text-left">
                            <tr>
                                <th class="px-4 py-3">{{ __('public.curriculum.table_semester') }}</th>
                                <th class="px-4 py-3">{{ __('public.curriculum.table_code') }}</th>
                                <th class="px-4 py-3">{{ __('public.curriculum.table_course') }}</th>
                                <th class="px-4 py-3">{{ __('public.curriculum.table_credits') }}</th>
                                <th class="px-4 py-3">{{ __('public.curriculum.table_syllabus') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($selectedCurriculum->courses as $course)
                                <tr class="border-t border-slate-100">
                                    <td class="px-4 py-3">{{ $course->semester }}</td>
                                    <td class="px-4 py-3">{{ $course->code }}</td>
                                    <td class="px-4 py-3">{{ $course->name }}</td>
                                    <td class="px-4 py-3">{{ $course->credits }}</td>
                                    <td class="px-4 py-3">{{ $course->short_syllabus ?: '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-8 text-center text-[var(--text-soft)]">{{ __('public.curriculum.empty_courses') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @else
            <div class="public-empty-state rounded-2xl border border-dashed border-[var(--border-soft)] bg-white p-8 text-center text-lg font-semibold text-[var(--text-soft)]">
                {{ __('public.curriculum.empty_not_selected') }}
            </div>
        @endif
    </section>
@endsection
