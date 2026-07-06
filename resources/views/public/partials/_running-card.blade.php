{{--
    Partial: Running Card Marquee
    Gunakan: @include('public.partials._running-card', ['runningActivities' => $runningActivities])
    Hanya di-render jika $runningActivities tidak kosong.
--}}
@if(isset($runningActivities) && $runningActivities->isNotEmpty())
<div class="mb-8">

    {{-- Kita gunakan class 'announcement-marquee' yang sudah ada di app.css agar animasi marquee sama persis --}}
    <div class="announcement-marquee activity-marquee-wrap">
        <div class="announcement-marquee-track activity-marquee-track">
            <div class="announcement-marquee-lane activity-marquee-lane flex gap-4">
                @foreach ($runningActivities as $item)
                    @php
                        $img = $item->image_path
                            ? asset('storage/' . $item->image_path)
                            : asset('image/galeri/image3.jpeg');
                        $url = route('public.activities.show', $item);

                        // Label status
                        $today     = \Carbon\Carbon::today();
                        $eventDate = $item->event_date;
                        if ($eventDate->isToday()) {
                            $statusLabel = 'Hari Ini';
                            $statusClass = 'bg-rose-500 text-white';
                        } elseif ($eventDate->isFuture()) {
                            $daysLeft = $today->diffInDays($eventDate);
                            $statusLabel = $daysLeft === 1 ? 'Besok' : "H-{$daysLeft}";
                            $statusClass = 'bg-amber-400 text-slate-900';
                        } else {
                            $daysAgo = $eventDate->diffInDays($today);
                            $statusLabel = "H+{$daysAgo}";
                            $statusClass = 'bg-slate-300 text-slate-700';
                        }
                    @endphp

                    <a href="{{ $url }}"
                       class="activity-marquee-card group block w-[280px] flex-shrink-0 rounded-xl border border-slate-200 bg-white shadow-sm overflow-hidden transition hover:shadow-md"
                       data-activity-card>
                        <div class="relative h-36 overflow-hidden">
                            <img src="{{ $img }}" alt="{{ $item->title }}"
                                 class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105" />
                            <span class="absolute top-2 left-2 rounded-full px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide {{ $statusClass }}">
                                {{ $statusLabel }}
                            </span>
                            <span class="absolute top-2 right-2 rounded-full bg-orange-100 px-2 py-0.5 text-[10px] font-semibold uppercase text-orange-700 shadow-sm">
                                {{ $item->category }}
                            </span>
                        </div>
                        <div class="p-4">
                            <p class="mb-1 text-[11px] text-slate-400">
                                <i class="fa-regular fa-calendar mr-1"></i>
                                {{ $item->event_date->format('d M Y') }}
                                @if($item->start_at)
                                    · {{ $item->start_at->format('H:i') }}
                                @endif
                            </p>
                            <h3 class="text-sm font-bold text-slate-900 line-clamp-2 leading-snug">{{ $item->title }}</h3>
                            
                            <div class="mt-3 flex items-center justify-between">
                                <span class="text-[11px] font-medium text-slate-500 hover:text-orange-600 transition-colors">
                                    Lihat Detail &rarr;
                                </span>
                                @php $calUrl = $item->googleCalendarUrl(); @endphp
                                @if($calUrl)
                                    <object><a href="{{ $calUrl }}"
                                       target="_blank"
                                       rel="noopener noreferrer"
                                       class="inline-flex items-center justify-center h-6 w-6 rounded-full bg-slate-100 text-slate-500 hover:bg-orange-100 hover:text-orange-600 transition-colors"
                                       title="Tambah ke Kalender">
                                        <i class="fa-brands fa-google text-[10px]"></i>
                                    </a></object>
                                @endif
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const initActivityMarquee = () => {
            const wrap = document.querySelector('.activity-marquee-wrap');
            const track = document.querySelector('.activity-marquee-track');
            const lane = track?.querySelector('.activity-marquee-lane');

            if (!wrap || !track || !lane) return;
            if (!lane.querySelector('[data-activity-card]')) return;

            const syncMarquee = () => {
                track.classList.remove('is-overflowing', 'is-compact');
                track.style.removeProperty('--marquee-start');
                track.style.removeProperty('--marquee-end');
                track.style.removeProperty('--marquee-duration');

                const wrapWidth = wrap.clientWidth;
                const laneWidth = lane.scrollWidth;
                
                if (!wrapWidth || !laneWidth) {
                    track.classList.add('is-compact');
                    return;
                }

                const startShift = Math.ceil(wrapWidth);
                const endShift = -Math.ceil(laneWidth);
                const travelDistance = startShift + laneWidth;
                const pixelsPerSecond = 80; // kecepatan
                const durationSeconds = Math.max(10, Math.min(45, travelDistance / pixelsPerSecond));

                track.style.setProperty('--marquee-start', `${startShift}px`);
                track.style.setProperty('--marquee-end', `${endShift}px`);
                track.style.setProperty('--marquee-duration', `${durationSeconds.toFixed(2)}s`);

                track.classList.add(laneWidth > wrapWidth + 10 ? 'is-overflowing' : 'is-compact');
            };

            syncMarquee();

            let resizeTimer = null;
            window.addEventListener('resize', () => {
                window.clearTimeout(resizeTimer);
                resizeTimer = window.setTimeout(syncMarquee, 120);
            });

            track.querySelectorAll('img').forEach((img) => {
                if (!img.complete) {
                    img.addEventListener('load', syncMarquee, { once: true });
                }
            });
        };

        initActivityMarquee();
    });
</script>
@endif
