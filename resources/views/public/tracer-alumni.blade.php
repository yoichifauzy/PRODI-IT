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
                <span class="meta-pill" data-tracer-selected-meta>{{ __('public.tracer.meta_selected_year') }}: {{ $selectedYear ?: __('public.tracer.all_graduates') }}</span>
                <span class="meta-pill" data-tracer-total-meta>{{ __('public.tracer.meta_total_data') }}: {{ $visibleRows->count() }}</span>
            </div>
        </header>

        <div class="tracer-filter-wrap mb-6 flex flex-wrap justify-center gap-3">
            <a
                href="{{ route('public.tracer-alumni') }}"
                class="tracer-filter-btn {{ $selectedYear === null ? 'is-active' : '' }}"
                data-tracer-filter
                data-year=""
            >
                {{ __('public.tracer.all_graduates') }}
            </a>
            @foreach ($graduationYears as $year)
                <a
                    href="{{ route('public.tracer-alumni', ['year' => $year]) }}"
                    class="tracer-filter-btn {{ $selectedYear === (int) $year ? 'is-active' : '' }}"
                    data-tracer-filter
                    data-year="{{ $year }}"
                >
                    {{ __('public.tracer.filter_year', ['year' => $year]) }}
                </a>
            @endforeach
        </div>

        <div
            class="tracer-hero-panel public-panel rounded-2xl border border-[var(--border-soft)] bg-white p-5 shadow-sm"
            data-tracer-panel
            data-selected-year="{{ $selectedYear }}"
            data-base-url="{{ route('public.tracer-alumni') }}"
            data-meta-selected-id="{{ trans('public.tracer.meta_selected_year', [], 'id') }}"
            data-meta-selected-en="{{ trans('public.tracer.meta_selected_year', [], 'en') }}"
            data-meta-total-id="{{ trans('public.tracer.meta_total_data', [], 'id') }}"
            data-meta-total-en="{{ trans('public.tracer.meta_total_data', [], 'en') }}"
            data-all-id="{{ trans('public.tracer.all_graduates', [], 'id') }}"
            data-all-en="{{ trans('public.tracer.all_graduates', [], 'en') }}"
            data-hero-label-id="{{ trans('public.tracer.hero_label', ['year' => ':year'], 'id') }}"
            data-hero-label-en="{{ trans('public.tracer.hero_label', ['year' => ':year'], 'en') }}"
            data-summary-selected-id="{{ trans('public.tracer.summary_selected', ['year' => ':year'], 'id') }}"
            data-summary-selected-en="{{ trans('public.tracer.summary_selected', ['year' => ':year'], 'en') }}"
            data-summary-all-id="{{ trans('public.tracer.summary_all', [], 'id') }}"
            data-summary-all-en="{{ trans('public.tracer.summary_all', [], 'en') }}"
        >
            <div class="tracer-hero-image-wrap">
                <img src="{{ asset('image/galeri/image3.jpeg') }}" alt="{{ __('public.tracer.hero_title') }}" class="h-80 w-full rounded-xl object-cover" />
                <span class="tracer-hero-label" data-tracer-hero-label>{{ __('public.tracer.hero_label', ['year' => $selectedYear ?: __('public.tracer.all_graduates')]) }}</span>
            </div>
            <p class="mt-4 text-[var(--text-soft)]" data-tracer-summary>
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
                        <tr
                            class="border-t border-slate-100 {{ $selectedYear !== null && (int) $row->graduation_year !== $selectedYear ? 'hidden' : '' }}"
                            data-tracer-row
                            data-year="{{ $row->graduation_year }}"
                        >
                            <td class="px-4 py-3">{{ $row->nim }}</td>
                            <td class="px-4 py-3">{{ $row->graduation_year ?: '-' }}</td>
                            <td class="px-4 py-3">{{ $row->company_name }}</td>
                            <td class="px-4 py-3">{{ $row->company_level ?: '-' }}</td>
                            <td class="px-4 py-3">{{ $row->department ?: '-' }}</td>
                            <td class="px-4 py-3">{{ $row->relevance ?: '-' }}</td>
                        </tr>
                    @empty
                        <tr data-tracer-empty>
                            <td colspan="6" class="px-4 py-8 text-center text-[var(--text-soft)]">{{ __('public.tracer.empty') }}</td>
                        </tr>
                    @endforelse
                    @if ($rows->isNotEmpty())
                        <tr class="{{ $visibleRows->isNotEmpty() ? 'hidden' : '' }}" data-tracer-empty>
                            <td colspan="6" class="px-4 py-8 text-center text-[var(--text-soft)]">{{ __('public.tracer.empty') }}</td>
                        </tr>
                    @endif
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
            const tracerPanel = document.querySelector('[data-tracer-panel]');
            const filterButtons = Array.from(document.querySelectorAll('[data-tracer-filter]'));
            const tracerRows = Array.from(document.querySelectorAll('[data-tracer-row]'));
            const emptyRow = document.querySelector('[data-tracer-empty]');
            const selectedMeta = document.querySelector('[data-tracer-selected-meta]');
            const totalMeta = document.querySelector('[data-tracer-total-meta]');
            const heroLabel = document.querySelector('[data-tracer-hero-label]');
            const summary = document.querySelector('[data-tracer-summary]');

            if (!revealButton || !tableWrap || !tracerPanel) {
                return;
            }

            const getCurrentLang = () => (localStorage.getItem('site-lang') === 'en' ? 'en' : 'id');
            const getPanelText = (name) => tracerPanel.getAttribute(`data-${name}-${getCurrentLang()}`)
                || tracerPanel.getAttribute(`data-${name}-id`)
                || '';

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

            const renderSummary = (year) => {
                if (!summary) {
                    return;
                }

                if (!year) {
                    summary.textContent = getPanelText('summary-all');
                    return;
                }

                const template = getPanelText('summary-selected');
                const before = template.split(':year')[0] || '';
                const after = template.split(':year').slice(1).join(':year') || '';

                summary.textContent = '';
                summary.append(document.createTextNode(before));
                const strong = document.createElement('strong');
                strong.textContent = year;
                summary.append(strong);
                summary.append(document.createTextNode(after));
            };

            const applyTracerFilter = (year, shouldPushState = true) => {
                const selectedYear = year || '';
                let visibleCount = 0;

                tracerRows.forEach((row) => {
                    const rowYear = row.getAttribute('data-year') || '';
                    const isVisible = selectedYear === '' || rowYear === selectedYear;
                    row.classList.toggle('hidden', !isVisible);

                    if (isVisible) {
                        visibleCount += 1;
                    }
                });

                filterButtons.forEach((button) => {
                    button.classList.toggle('is-active', (button.getAttribute('data-year') || '') === selectedYear);
                });

                if (emptyRow) {
                    emptyRow.classList.toggle('hidden', visibleCount > 0);
                }

                const allLabel = getPanelText('all');
                const selectedLabel = selectedYear || allLabel;

                if (selectedMeta) {
                    selectedMeta.textContent = `${getPanelText('meta-selected')}: ${selectedLabel}`;
                }

                if (totalMeta) {
                    totalMeta.textContent = `${getPanelText('meta-total')}: ${visibleCount}`;
                }

                if (heroLabel) {
                    heroLabel.textContent = getPanelText('hero-label').replace(':year', selectedLabel);
                }

                renderSummary(selectedYear);
                tracerPanel.setAttribute('data-selected-year', selectedYear);

                if (shouldPushState) {
                    const url = new URL(tracerPanel.getAttribute('data-base-url') || window.location.href, window.location.origin);
                    if (selectedYear) {
                        url.searchParams.set('year', selectedYear);
                    }

                    window.history.pushState({ tracerYear: selectedYear }, '', url);
                }
            };

            filterButtons.forEach((button) => {
                button.addEventListener('click', (event) => {
                    event.preventDefault();
                    applyTracerFilter(button.getAttribute('data-year') || '');
                });
            });

            window.addEventListener('popstate', () => {
                const year = new URLSearchParams(window.location.search).get('year') || '';
                applyTracerFilter(year, false);
            });

            revealButton.addEventListener('click', () => {
                const isHidden = tableWrap.classList.toggle('hidden');
                syncButtonLabel(isHidden);

                if (!isHidden) {
                    tableWrap.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            });

            document.addEventListener('site-language-changed', () => {
                syncButtonLabel(tableWrap.classList.contains('hidden'));
                applyTracerFilter(tracerPanel.getAttribute('data-selected-year') || '', false);
            });
        });
    </script>
@endpush
