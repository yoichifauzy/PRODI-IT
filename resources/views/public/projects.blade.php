@extends('layouts.public')

@section('title', __('public.projects.page_title'))

@section('content')
    @include('public.partials.page-hero', [
        'title'    => __('public.projects.hero_title'),
        'subtitle' => __('public.projects.hero_subtitle'),
    ])

    <section class="section-wrap public-page-shell">

        {{-- ══════════════════════════════════════════════════ --}}
        {{-- SECTION 1 — PROJECT PILIHAN (horizontal scroll)   --}}
        {{-- ══════════════════════════════════════════════════ --}}
        <div class="mb-3 flex items-center justify-between">
            <h2 class="text-2xl font-black text-[var(--text-main)] flex items-center gap-2">
                Project Unggulan
            </h2>
        </div>

        @if($featuredProjects->isNotEmpty())
        <div class="relative">
            {{-- Scroll container --}}
            <div id="featured-scroll"
                 class="flex gap-5 overflow-x-auto pb-4 snap-x snap-mandatory scroll-smooth"
                 style="scrollbar-width: thin; scrollbar-color: rgba(0,0,0,.15) transparent;">
                @foreach ($featuredProjects as $i => $project)
                @php
                    $thumb = $project->image_path ? asset('storage/' . $project->image_path) : asset('image/galeri/image3.jpeg');
                @endphp
                <a href="{{ route('public.projects.show', $project) }}"
                   class="group flex-shrink-0 w-72 snap-start overflow-hidden rounded-2xl bg-white shadow-sm hover:shadow-lg transition-shadow border border-[var(--border-soft)]">
                    <div class="relative overflow-hidden">
                        <img src="{{ $thumb }}" alt="{{ $project->title }}"
                             class="h-44 w-full object-cover transition-transform duration-500 group-hover:scale-105">
                        <span class="absolute left-3 top-3 flex items-center gap-1 rounded-full bg-amber-400 px-2.5 py-1 text-xs font-bold text-amber-900">
                            <svg class="h-3 w-3 fill-amber-900" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                            <!-- #{{ $i + 1 }} -->
                        </span>
                    </div>
                    <div class="p-4">
                        <h3 class="font-bold text-[var(--text-main)] line-clamp-2 leading-snug">{{ $project->title }}</h3>
                        <p class="mt-1 text-sm text-[var(--text-soft)] line-clamp-1">
                            {{ $project->student_name }}
                            @if($project->student_nim)
                                <span class="text-[var(--text-soft)]/60">· {{ $project->student_nim }}</span>
                            @endif
                        </p>
                        @if($project->year)
                        <p class="mt-1 text-xs text-[var(--text-soft)]/60">{{ $project->year }}</p>
                        @endif
                    </div>
                </a>
                @endforeach
            </div>

            {{-- Scroll shadow hints --}}
            <div class="pointer-events-none absolute right-0 top-0 bottom-4 w-12 bg-gradient-to-l from-white/70 to-transparent rounded-r-2xl"></div>
        </div>
        @else
        <div class="mb-8 rounded-xl border border-dashed border-[var(--border-soft)] bg-white p-8 text-center text-[var(--text-soft)]">
            Belum ada project unggulan.
        </div>
        @endif

        {{-- ══════════════════════════════════════════════════ --}}
        {{-- SECTION 2 — PROJECT REGULER (paginasi client-side)--}}
        {{-- ══════════════════════════════════════════════════ --}}
        <div class="mt-12 border-t border-dashed border-[var(--border-soft)] pt-10">
            <div class="mb-6 flex items-center justify-between">
                <h2 class="text-2xl font-black text-[var(--text-main)]">Semua Project</h2>
                <!-- <span class="text-xs text-[var(--text-soft)]" id="regular-count">{{ $regularProjects->count() }} project</span> -->
            </div>

            {{-- Grid regular projects (max 12 per page = 4 baris × 3 kolom) --}}
            <div id="regular-grid" class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                @forelse ($regularProjects as $index => $project)
                @php
                    $thumb = $project->image_path ? asset('storage/' . $project->image_path) : asset('image/galeri/image3.jpeg');
                @endphp
                <a href="{{ route('public.projects.show', $project) }}"
                   class="regular-card group overflow-hidden rounded-2xl bg-white shadow-sm hover:shadow-md transition-shadow border border-[var(--border-soft)]"
                   data-page="{{ floor($index / 12) + 1 }}">
                    <div class="overflow-hidden">
                        <img src="{{ $thumb }}" alt="{{ $project->title }}"
                             class="h-40 w-full object-cover transition-transform duration-500 group-hover:scale-105">
                    </div>
                    <div class="p-4">
                        <h3 class="font-bold text-[var(--text-main)] text-sm line-clamp-2 leading-snug">{{ $project->title }}</h3>
                        <p class="mt-1 text-xs text-[var(--text-soft)] line-clamp-1">
                            {{ $project->student_name }}
                            @if($project->student_nim)
                                <span class="opacity-60">· {{ $project->student_nim }}</span>
                            @endif
                        </p>
                        @if($project->year)
                        <p class="mt-1 text-xs text-[var(--text-soft)]/50">{{ $project->year }}</p>
                        @endif
                    </div>
                </a>
                @empty
                <div class="col-span-full rounded-xl border border-dashed border-[var(--border-soft)] bg-white p-8 text-center text-[var(--text-soft)]">
                    Belum ada project.
                </div>
                @endforelse
            </div>

            {{-- Pagination buttons --}}
            <div id="regular-pagination" class="mt-8 flex flex-wrap justify-center gap-2"></div>
        </div>

    </section>
@endsection

@push('scripts')
<script>
// ── Client-side pagination untuk regular projects ──────────────────────────
(function () {
    const PER_PAGE  = 12;
    const cards     = Array.from(document.querySelectorAll('.regular-card'));
    const paginEl   = document.getElementById('regular-pagination');
    const countEl   = document.getElementById('regular-count');
    const totalPages = Math.ceil(cards.length / PER_PAGE);
    let currentPage  = 1;

    if (cards.length === 0 || totalPages <= 1) {
        // Tampilkan semua, sembunyikan pagination
        cards.forEach(c => c.classList.remove('hidden'));
        return;
    }

    function renderPage(page) {
        currentPage = page;
        cards.forEach((card, i) => {
            const cardPage = Math.ceil((i + 1) / PER_PAGE);
            card.classList.toggle('hidden', cardPage !== page);
        });

        // Update pagination buttons
        paginEl.innerHTML = '';

        // Prev
        const prev = makePaginBtn('←', page > 1, () => renderPage(page - 1));
        paginEl.appendChild(prev);

        // Page numbers
        for (let p = 1; p <= totalPages; p++) {
            const btn = makePaginBtn(String(p), true, () => renderPage(p));
            if (p === page) {
                btn.classList.add('bg-[var(--accent)]', 'text-white', 'border-[var(--accent)]');
                btn.classList.remove('bg-white', 'text-[var(--text-main)]');
            }
            paginEl.appendChild(btn);
        }

        // Next
        const next = makePaginBtn('→', page < totalPages, () => renderPage(page + 1));
        paginEl.appendChild(next);

        // Scroll ke bagian regular
        document.getElementById('regular-grid')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    function makePaginBtn(label, enabled, onClick) {
        const btn = document.createElement('button');
        btn.textContent = label;
        btn.disabled = !enabled;
        btn.className = [
            'min-w-[2.25rem] rounded-lg border border-[var(--border-soft)] bg-white px-3 py-1.5',
            'text-sm font-semibold text-[var(--text-main)] transition',
            enabled ? 'hover:bg-[var(--accent)]/10 hover:border-[var(--accent)] cursor-pointer' : 'opacity-30 cursor-not-allowed',
        ].join(' ');
        if (enabled) btn.addEventListener('click', onClick);
        return btn;
    }

    renderPage(1);
})();
</script>
@endpush
