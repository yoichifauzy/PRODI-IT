@extends('layouts.public')

@section('title', __('public.announcements.page_title'))

@section('content')
    @include('public.partials.page-hero', [
        'title' => __('public.announcements.hero_title'),
        'subtitle' => __('public.announcements.hero_subtitle'),
    ])

    <div
        class="sr-only"
        data-announcement-sync-url="{{ route('public.announcements.sync') }}"
        data-announcement-signature="{{ $announcementSync['signature'] ?? '' }}"
        data-announcement-sync-interval="10000"
        aria-hidden="true"
    ></div>

    <section class="section-wrap public-page-shell">
        <header class="public-page-intro">
            <h2 class="public-page-title">{{ __('public.announcements.intro_title') }}</h2>
            <p class="public-page-copy">{{ __('public.announcements.intro_copy') }}</p>
            <div class="public-page-meta">
                <span class="meta-pill">{{ __('public.announcements.meta_total') }}: {{ $announcements->count() }}</span>
            </div>
        </header>

        <div class="announcement-marquee mb-8">
            <div class="announcement-marquee-track">
                <div class="announcement-marquee-lane">
                    @foreach ($announcements as $announcement)
                        @php
                            $cover = $announcement->cover_image;
                            $coverUrl = '';
                            $statusLabel = $announcement->status === 'draft' ? __('public.announcements.status_published') : strtoupper($announcement->status);
                            if ($cover) {
                                $coverUrl = \Illuminate\Support\Str::startsWith($cover, ['http://', 'https://']) ? $cover : asset('storage/' . $cover);
                            }
                        @endphp
                        <button type="button" class="announcement-card" data-announcement-card data-announcement-target="announcement-{{ $announcement->id }}" data-announcement-detail="announcement-detail-{{ $announcement->id }}">
                            @if ($coverUrl !== '')
                                <img src="{{ $coverUrl }}" alt="{{ $announcement->title }}" class="announcement-card-image" />
                            @endif
                            <div class="p-3 text-left">
                                <p class="text-xs font-semibold uppercase tracking-wide text-[var(--accent)]">{{ $statusLabel }}</p>
                                <h3 class="mt-1 text-sm font-bold text-[var(--text-main)]">{{ $announcement->title }}</h3>
                                <p class="mt-1 text-xs text-[var(--text-soft)]">{{ optional($announcement->published_at)->format('d M Y H:i') ?: '-' }}</p>
                            </div>
                        </button>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="announcement-table-shell overflow-x-auto rounded-xl border border-[var(--border-soft)] bg-white shadow-sm">
            <table class="min-w-full text-sm">
                <thead class="announcement-table-head text-left">
                    <tr>
                        <th class="px-4 py-3">{{ __('public.announcements.table_title') }}</th>
                        <th class="px-4 py-3">{{ __('public.announcements.table_status') }}</th>
                        <th class="px-4 py-3">{{ __('public.announcements.table_publish') }}</th>
                        <th class="px-4 py-3">{{ __('public.announcements.table_action') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($announcements as $announcement)
                        <tr class="announcement-summary-row border-t border-slate-100" id="announcement-{{ $announcement->id }}">
                            <td class="px-4 py-3 font-semibold text-[var(--text-main)]">{{ $announcement->title }}</td>
                            <td class="px-4 py-3">{{ $announcement->status === 'draft' ? __('public.announcements.status_published') : strtoupper($announcement->status) }}</td>
                            <td class="px-4 py-3">{{ optional($announcement->published_at)->format('d M Y H:i') ?: '-' }}</td>
                            <td class="px-4 py-3">
                                <button type="button" class="announcement-toggle-btn" data-announcement-toggle data-announcement-target="announcement-detail-{{ $announcement->id }}" data-announcement-summary="announcement-{{ $announcement->id }}">{{ __('public.announcements.action_detail') }}</button>
                            </td>
                        </tr>
                        <tr id="announcement-detail-{{ $announcement->id }}" class="announcement-detail-row hidden border-t border-slate-100 bg-slate-50">
                            <td colspan="4" class="px-4 py-4 text-[var(--text-soft)]">
                                {!! nl2br(e($announcement->content)) !!}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-8 text-center text-[var(--text-soft)]">{{ __('public.announcements.empty') }}</td>
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
            const toggleButtons = Array.from(document.querySelectorAll('[data-announcement-toggle]'));

            // Keep marquee moving without duplicating source announcement cards.
            (function initAnnouncementMarquee() {
                const marqueeWrap = document.querySelector('.announcement-marquee');
                const marqueeTrack = document.querySelector('.announcement-marquee-track');
                const marqueeLane = marqueeTrack?.querySelector('.announcement-marquee-lane');

                if (!marqueeWrap || !marqueeTrack || !marqueeLane) return;
                if (!marqueeLane.querySelector('[data-announcement-card]')) {
                    return;
                }

                const syncMarquee = () => {
                    marqueeTrack.classList.remove('is-overflowing', 'is-compact');
                    marqueeTrack.style.removeProperty('--marquee-shift');

                    const overflow = marqueeLane.scrollWidth - marqueeWrap.clientWidth;
                    if (overflow > 10) {
                        marqueeTrack.classList.add('is-overflowing');
                        marqueeTrack.style.setProperty('--marquee-shift', `-${Math.ceil(overflow)}px`);
                        return;
                    }

                    marqueeTrack.classList.add('is-compact');
                };

                syncMarquee();

                let resizeTimer = null;
                window.addEventListener('resize', () => {
                    window.clearTimeout(resizeTimer);
                    resizeTimer = window.setTimeout(syncMarquee, 120);
                });

                marqueeTrack.querySelectorAll('img').forEach((img) => {
                    if (!img.complete) {
                        img.addEventListener('load', syncMarquee, { once: true });
                    }
                });
            })();

            const clearCardActive = () => {
                document.querySelectorAll('[data-announcement-card]').forEach((card) => {
                    card.classList.remove('is-active');
                });
            };

            const clearSummaryHighlight = () => {
                document.querySelectorAll('.announcement-summary-row').forEach((row) => {
                    row.classList.remove('is-highlighted');
                });
            };

            const closeAllDetails = () => {
                document.querySelectorAll('[id^="announcement-detail-"]').forEach((row) => {
                    row.classList.add('hidden');
                });
            };

            const openDetailByRowId = (rowId, summaryId = null) => {
                const row = document.getElementById(rowId);
                if (!row) {
                    return;
                }

                closeAllDetails();
                row.classList.remove('hidden');

                clearSummaryHighlight();
                if (summaryId) {
                    const summaryRow = document.getElementById(summaryId);
                    summaryRow?.classList.add('is-highlighted');
                }
            };

            toggleButtons.forEach((button) => {
                button.addEventListener('click', () => {
                    const targetId = button.getAttribute('data-announcement-target');
                    const summaryId = button.getAttribute('data-announcement-summary');
                    if (!targetId) {
                        return;
                    }

                    const targetRow = document.getElementById(targetId);
                    const isHidden = targetRow?.classList.contains('hidden');
                    closeAllDetails();
                    clearSummaryHighlight();
                    clearCardActive();

                    if (isHidden && targetRow) {
                        targetRow.classList.remove('hidden');
                        if (summaryId) {
                            document.getElementById(summaryId)?.classList.add('is-highlighted');
                        }
                    }
                });
            });

            document.addEventListener('click', (event) => {
                const card = event.target.closest('[data-announcement-card]');
                if (!card) {
                    return;
                }

                const summaryId = card.getAttribute('data-announcement-target');
                const detailId = card.getAttribute('data-announcement-detail');
                if (!summaryId || !detailId) {
                    return;
                }

                const summaryRow = document.getElementById(summaryId);
                if (!summaryRow) {
                    return;
                }

                clearCardActive();
                card.classList.add('is-active');

                openDetailByRowId(detailId, summaryId);

                summaryRow.scrollIntoView({ behavior: 'smooth', block: 'center' });
                summaryRow.classList.add('ring-2', 'ring-orange-400');
                window.setTimeout(() => {
                    summaryRow.classList.remove('ring-2', 'ring-orange-400');
                }, 1000);
            });
        });
    </script>
@endpush
