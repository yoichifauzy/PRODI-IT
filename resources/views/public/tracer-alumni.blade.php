@extends('layouts.public')

@section('title', __('public.tracer.page_title'))

@section('content')
    @include('public.partials.page-hero', [
        'title' => __('public.tracer.hero_title'),
        'subtitle' => __('public.tracer.hero_subtitle'),
    ])

    <section class="section-wrap public-page-shell">
        <header class="public-page-intro">
            <h2 class="public-page-title">{{ __('public.tracer.intro_title') }}</h2>
            <p class="public-page-copy">{{ __('public.tracer.intro_copy') }}</p>
            <div class="public-page-meta">
                <span class="meta-pill">{{ __('public.tracer.meta_selected_year') }}: {{ $selectedYear ?: __('public.tracer.all_graduates') }}</span>
                <span class="meta-pill">{{ __('public.tracer.meta_total_data') }}: {{ $rows->count() }}</span>
            </div>
        </header>

        <div class="tracer-filter-wrap mb-6 flex flex-wrap justify-center gap-3">
            <a href="{{ route('public.tracer-alumni') }}" class="tracer-filter-btn {{ $selectedYear === null ? 'is-active' : '' }}">
                {{ __('public.tracer.all_graduates') }}
            </a>
            @foreach ($graduationYears as $year)
                <a href="{{ route('public.tracer-alumni', ['year' => $year]) }}" class="tracer-filter-btn {{ $selectedYear === (int) $year ? 'is-active' : '' }}">
                    {{ __('public.tracer.filter_year', ['year' => $year]) }}
                </a>
            @endforeach
        </div>

        <div class="tracer-hero-panel public-panel rounded-2xl border border-[var(--border-soft)] bg-white p-5 shadow-sm">
            <div class="tracer-hero-image-wrap">
                <img src="{{ asset('image/galeri/image3.jpeg') }}" alt="{{ __('public.tracer.hero_title') }}" class="h-80 w-full rounded-xl object-cover" />
                <span class="tracer-hero-label">{{ __('public.tracer.hero_label', ['year' => $selectedYear ?: __('public.tracer.all_graduates')]) }}</span>
            </div>
            <p class="mt-4 text-[var(--text-soft)]">
                {!! $selectedYear
                    ? __('public.tracer.summary_selected', ['year' => '<strong>' . e((string) $selectedYear) . '</strong>'])
                    : __('public.tracer.summary_all') !!}
            </p>
            <div class="mt-4">
                <button
                    type="button"
                    class="solid-cta tracer-reveal-btn"
                    data-tracer-reveal
                    data-label-show-id="{{ trans('public.tracer.toggle_show', [], 'id') }}"
                    data-label-hide-id="{{ trans('public.tracer.toggle_hide', [], 'id') }}"
                    data-label-show-en="{{ trans('public.tracer.toggle_show', [], 'en') }}"
                    data-label-hide-en="{{ trans('public.tracer.toggle_hide', [], 'en') }}"
                    aria-expanded="true"
                >{{ __('public.tracer.toggle_hide') }}</button>
            </div>
        </div>

        <div id="tracer-table-wrap" class="mt-6 overflow-x-auto rounded-xl border border-[var(--border-soft)] bg-white shadow-sm">
            <table class="tracer-data-table min-w-full text-sm">
                <thead class="tracer-table-head text-left">
                    <tr>
                        <th class="px-4 py-3">{{ __('public.tracer.table_nim') }}</th>
                        <th class="px-4 py-3">{{ __('public.tracer.table_graduation') }}</th>
                        <th class="px-4 py-3">{{ __('public.tracer.table_company') }}</th>
                        <th class="px-4 py-3">{{ __('public.tracer.table_level') }}</th>
                        <th class="px-4 py-3">{{ __('public.tracer.table_department') }}</th>
                        <th class="px-4 py-3">{{ __('public.tracer.table_relevance') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($rows as $row)
                        <tr class="border-t border-slate-100">
                            <td class="px-4 py-3">{{ $row->nim }}</td>
                            <td class="px-4 py-3">{{ $row->graduation_year ?: '-' }}</td>
                            <td class="px-4 py-3">{{ $row->company_name }}</td>
                            <td class="px-4 py-3">{{ $row->company_level ?: '-' }}</td>
                            <td class="px-4 py-3">{{ $row->department ?: '-' }}</td>
                            <td class="px-4 py-3">{{ $row->relevance ?: '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-[var(--text-soft)]">{{ __('public.tracer.empty') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const revealButton = document.querySelector('[data-tracer-reveal]');
            const tableWrap = document.getElementById('tracer-table-wrap');

            if (!revealButton || !tableWrap) {
                return;
            }

            const getCurrentLang = () => (localStorage.getItem('site-lang') === 'en' ? 'en' : 'id');

            const getLabel = (isHidden) => {
                const lang = getCurrentLang();
                const state = isHidden ? 'show' : 'hide';
                return revealButton.getAttribute(`data-label-${state}-${lang}`)
                    || revealButton.getAttribute(`data-label-${state}-id`)
                    || '';
            };

            const syncButtonLabel = (isHidden) => {
                revealButton.textContent = getLabel(isHidden);
                revealButton.setAttribute('aria-expanded', String(!isHidden));
            };

            syncButtonLabel(tableWrap.classList.contains('hidden'));

            revealButton.addEventListener('click', () => {
                const isHidden = tableWrap.classList.toggle('hidden');
                syncButtonLabel(isHidden);

                if (!isHidden) {
                    tableWrap.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            });

            document.addEventListener('site-language-changed', () => {
                syncButtonLabel(tableWrap.classList.contains('hidden'));
            });
        });
    </script>
@endpush
