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
                        <button
                            type="button"
                            class="announcement-card"
                            data-announcement-card
                            data-announcement-open
                            data-announcement-id="{{ $announcement->id }}"
                        >
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
                        @php
                            $cover = $announcement->cover_image;
                            $coverUrl = '';
                            $statusLabel = $announcement->status === 'draft' ? __('public.announcements.status_published') : strtoupper($announcement->status);
                            if ($cover) {
                                $coverUrl = \Illuminate\Support\Str::startsWith($cover, ['http://', 'https://']) ? $cover : asset('storage/' . $cover);
                            }
                        @endphp
                        <tr class="announcement-summary-row border-t border-slate-100" id="announcement-{{ $announcement->id }}">
                            <td class="px-4 py-3 font-semibold text-[var(--text-main)]">{{ $announcement->title }}</td>
                            <td class="px-4 py-3">{{ $announcement->status === 'draft' ? __('public.announcements.status_published') : strtoupper($announcement->status) }}</td>
                            <td class="px-4 py-3">{{ optional($announcement->published_at)->format('d M Y H:i') ?: '-' }}</td>
                            <td class="px-4 py-3">
                                <button
                                    type="button"
                                    class="announcement-toggle-btn"
                                    data-announcement-open
                                    data-announcement-id="{{ $announcement->id }}"
                                >{{ __('public.announcements.action_detail') }}</button>
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

        <div class="sr-only" aria-hidden="true">
            @foreach ($announcements as $announcement)
                @php
                    $cover = $announcement->cover_image;
                    $coverUrl = '';
                    $statusLabel = $announcement->status === 'draft' ? __('public.announcements.status_published') : strtoupper($announcement->status);
                    if ($cover) {
                        $coverUrl = \Illuminate\Support\Str::startsWith($cover, ['http://', 'https://']) ? $cover : asset('storage/' . $cover);
                    }
                @endphp
                <div
                    id="announcement-data-{{ $announcement->id }}"
                    data-title="{{ $announcement->title }}"
                    data-status="{{ $statusLabel }}"
                    data-date="{{ optional($announcement->published_at)->format('d M Y H:i') ?: '-' }}"
                    data-cover="{{ $coverUrl }}"
                >
                    <div data-content>
                        {!! nl2br(e($announcement->content)) !!}
                    </div>
                </div>
            @endforeach
        </div>

        <div id="announcement-modal" class="announcement-modal hidden" aria-hidden="true">
            <div class="announcement-modal-backdrop" data-announcement-close></div>
            <div class="announcement-modal-dialog" role="dialog" aria-modal="true" aria-labelledby="announcement-modal-title">
                <button type="button" class="announcement-modal-close" data-announcement-close aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <img class="announcement-modal-cover hidden" data-announcement-modal-cover alt="" />
                <div class="announcement-modal-header">
                    <p class="announcement-modal-status" data-announcement-modal-status></p>
                    <h3 id="announcement-modal-title" class="announcement-modal-title" data-announcement-modal-title></h3>
                    <p class="announcement-modal-date" data-announcement-modal-date></p>
                </div>
                <div class="announcement-modal-body" data-announcement-modal-content></div>
            </div>
        </div>
    </section>

    <style>
        .announcement-modal {
            position: fixed;
            inset: 0;
            z-index: 80;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
        }

        .announcement-modal.hidden {
            display: none;
        }

        .announcement-modal-backdrop {
            position: absolute;
            inset: 0;
            background: rgba(15, 23, 42, 0.58);
            backdrop-filter: blur(10px);
        }

        .announcement-modal-dialog {
            position: relative;
            width: min(860px, 95vw);
            max-height: 85vh;
            overflow: hidden;
            border-radius: 22px;
            background: #ffffff;
            box-shadow: 0 35px 90px rgba(15, 23, 42, 0.45);
            transform: translateY(22px) scale(0.95);
            opacity: 0;
            transform-origin: 50% 40%;
            transition: transform 260ms cubic-bezier(0.16, 1, 0.3, 1), opacity 200ms ease;
        }

        .announcement-modal.is-open .announcement-modal-dialog {
            transform: translateY(0) scale(1);
            opacity: 1;
        }

        .announcement-modal-cover {
            width: 100%;
            height: 240px;
            object-fit: cover;
            display: block;
        }

        .announcement-modal-header {
            padding: 1.25rem 1.5rem 1rem;
            border-bottom: 1px solid var(--border-soft);
            background: linear-gradient(135deg, rgba(255, 237, 213, 0.7), rgba(255, 255, 255, 0.9));
        }

        .announcement-modal-status {
            font-size: 0.75rem;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: var(--accent);
            font-weight: 700;
        }

        .announcement-modal-title {
            margin-top: 0.35rem;
            font-size: 1.6rem;
            font-weight: 800;
            color: var(--text-main);
        }

        .announcement-modal-date {
            margin-top: 0.35rem;
            font-size: 0.9rem;
            color: var(--text-soft);
        }

        .announcement-modal-body {
            padding: 1.25rem 1.5rem 1.6rem;
            max-height: calc(85vh - 220px);
            overflow: auto;
            color: var(--text-soft);
            line-height: 1.7;
            background: #ffffff;
        }

        .announcement-modal-close {
            position: absolute;
            top: 0.75rem;
            right: 0.75rem;
            height: 2.25rem;
            width: 2.25rem;
            border-radius: 999px;
            border: 1px solid rgba(148, 163, 184, 0.4);
            background: rgba(255, 255, 255, 0.85);
            color: #0f172a;
            font-size: 1.6rem;
            line-height: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 8px 16px rgba(15, 23, 42, 0.2);
            transition: transform 0.2s ease, background 0.2s ease;
            z-index: 2;
        }

        .announcement-modal-close:hover {
            transform: scale(1.05);
            background: #ffffff;
        }

        body.modal-open {
            overflow: hidden;
        }

        @media (max-width: 640px) {
            .announcement-modal-dialog {
                border-radius: 18px;
            }

            .announcement-modal-cover {
                height: 180px;
            }

            .announcement-modal-title {
                font-size: 1.35rem;
            }
        }
    </style>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const openButtons = Array.from(document.querySelectorAll('[data-announcement-open]'));
            const modal = document.getElementById('announcement-modal');
            const modalTitle = modal?.querySelector('[data-announcement-modal-title]');
            const modalStatus = modal?.querySelector('[data-announcement-modal-status]');
            const modalDate = modal?.querySelector('[data-announcement-modal-date]');
            const modalCover = modal?.querySelector('[data-announcement-modal-cover]');
            const modalContent = modal?.querySelector('[data-announcement-modal-content]');

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
                    marqueeTrack.style.removeProperty('--marquee-start');
                    marqueeTrack.style.removeProperty('--marquee-end');
                    marqueeTrack.style.removeProperty('--marquee-duration');

                    const wrapWidth = marqueeWrap.clientWidth;
                    const laneWidth = marqueeLane.scrollWidth;
                    if (!wrapWidth || !laneWidth) {
                        marqueeTrack.classList.add('is-compact');
                        return;
                    }

                    // Start completely outside right edge and end completely outside left edge.
                    const startShift = Math.ceil(wrapWidth);
                    const endShift = -Math.ceil(laneWidth);
                    const travelDistance = startShift + laneWidth;
                    const pixelsPerSecond = 90;
                    const durationSeconds = Math.max(10, Math.min(40, travelDistance / pixelsPerSecond));

                    marqueeTrack.style.setProperty('--marquee-start', `${startShift}px`);
                    marqueeTrack.style.setProperty('--marquee-end', `${endShift}px`);
                    marqueeTrack.style.setProperty('--marquee-duration', `${durationSeconds.toFixed(2)}s`);

                    marqueeTrack.classList.add(laneWidth > wrapWidth + 10 ? 'is-overflowing' : 'is-compact');
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

            const closeModal = () => {
                if (!modal || modal.classList.contains('hidden')) {
                    return;
                }

                modal.classList.remove('is-open');
                document.body.classList.remove('modal-open');

                window.setTimeout(() => {
                    modal.classList.add('hidden');
                }, 220);
            };

            const openModal = (announcementId) => {
                if (!modal || !announcementId) {
                    return;
                }

                const dataNode = document.getElementById(`announcement-data-${announcementId}`);
                if (!dataNode) {
                    return;
                }

                if (modalTitle) {
                    modalTitle.textContent = dataNode.getAttribute('data-title') || '';
                }

                if (modalStatus) {
                    modalStatus.textContent = dataNode.getAttribute('data-status') || '';
                }

                if (modalDate) {
                    modalDate.textContent = dataNode.getAttribute('data-date') || '';
                }

                const coverUrl = dataNode.getAttribute('data-cover') || '';
                if (modalCover instanceof HTMLImageElement) {
                    if (coverUrl !== '') {
                        modalCover.src = coverUrl;
                        modalCover.alt = dataNode.getAttribute('data-title') || '';
                        modalCover.classList.remove('hidden');
                    } else {
                        modalCover.classList.add('hidden');
                        modalCover.removeAttribute('src');
                        modalCover.removeAttribute('alt');
                    }
                }

                const contentNode = dataNode.querySelector('[data-content]');
                if (modalContent) {
                    modalContent.innerHTML = contentNode?.innerHTML || '';
                }

                modal.classList.remove('hidden');
                window.requestAnimationFrame(() => {
                    modal.classList.add('is-open');
                });

                document.body.classList.add('modal-open');
            };

            openButtons.forEach((button) => {
                button.addEventListener('click', () => {
                    const announcementId = button.getAttribute('data-announcement-id');
                    if (!announcementId) {
                        return;
                    }

                    clearSummaryHighlight();
                    clearCardActive();
                    button.closest('[data-announcement-card]')?.classList.add('is-active');

                    openModal(announcementId);
                });
            });

            document.addEventListener('click', (event) => {
                if (event.target.closest('[data-announcement-close]')) {
                    closeModal();
                }
            });

            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape') {
                    closeModal();
                }
            });
        });
    </script>
@endpush
