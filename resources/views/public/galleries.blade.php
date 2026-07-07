@extends('layouts.public')

@section('title', 'Galeri — ' . config('app.name'))

@section('content')
    @include('public.partials.page-hero', [
        'title'    => 'Galeri Kegiatan',
        'subtitle' => 'Dokumentasi berbagai kegiatan dan momen penting di Program Studi Teknologi Informasi.',
    ])

    <section class="section-wrap public-page-shell">

        {{-- Filter Bar --}}
        <div class="mb-8 flex flex-wrap items-center justify-center gap-3">
            <input type="text" id="gallery-search" placeholder="Cari foto…"
                   class="w-full sm:w-60 rounded-xl border border-[var(--border-soft)] bg-white px-4 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-[var(--accent)]">

            <select id="gallery-category-filter"
                    class="rounded-xl border border-[var(--border-soft)] bg-white px-4 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-[var(--accent)]">
                <option value="">Semua Kategori</option>
                @foreach($categories as $cat)
                    <option value="{{ strtolower($cat) }}">{{ $cat }}</option>
                @endforeach
            </select>

            <select id="gallery-year-filter"
                    class="rounded-xl border border-[var(--border-soft)] bg-white px-4 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-[var(--accent)]">
                <option value="">Semua Tahun</option>
                @foreach($years as $y)
                    <option value="{{ $y }}">{{ $y }}</option>
                @endforeach
            </select>
        </div>

        {{-- Grid --}}
        <div id="gallery-grid" class="grid gap-4 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
            @forelse($galleries as $index => $item)
            <div class="gallery-pub-card group relative overflow-hidden rounded-2xl bg-white shadow-sm cursor-pointer"
                 data-title="{{ strtolower($item->title) }}"
                 data-category="{{ strtolower($item->category) }}"
                 data-year="{{ $item->year }}"
                 data-index="{{ $index }}"
                 onclick="openLightboxAt(parseInt(this.dataset.index))">

                {{-- Gambar --}}
                <div class="aspect-square overflow-hidden bg-slate-100">
                    <img src="{{ asset('storage/' . $item->image_path) }}"
                         alt="{{ $item->title }}"
                         loading="lazy"
                         class="h-full w-full object-cover transition-transform duration-500 ease-out group-hover:scale-110">
                </div>

                {{-- Overlay info (muncul saat hover) --}}
                <div class="absolute inset-0 flex flex-col justify-end bg-gradient-to-t from-black/70 via-black/20 to-transparent p-4 opacity-0 transition-opacity duration-300 group-hover:opacity-100">
                    <h3 class="text-sm font-semibold leading-tight text-white">{{ $item->title }}</h3>
                    <div class="mt-1 flex items-center gap-2">
                        <span class="rounded-full bg-white/20 px-2 py-0.5 text-xs text-white/90 backdrop-blur-sm">{{ $item->category }}</span>
                        <span class="text-xs text-white/70">{{ $item->year }}</span>
                    </div>
                </div>
            </div>
            @empty
            <div id="empty-state" class="col-span-full py-24 text-center text-[var(--text-soft)]">
                <i class="fa-regular fa-image text-5xl mb-4 block opacity-40"></i>
                <p>Belum ada foto dalam galeri.</p>
            </div>
            @endforelse
        </div>

        {{-- Empty state ketika filter tidak cocok --}}
        <div id="no-result" class="hidden py-16 text-center text-[var(--text-soft)]">
            <i class="fa-solid fa-magnifying-glass text-3xl mb-3 block opacity-30"></i>
            <p class="text-sm">Tidak ada foto yang cocok dengan filter.</p>
        </div>

    </section>

    {{-- ═══ LIGHTBOX FULLSCREEN ═══ --}}
    <div id="lightbox"
         class="fixed inset-0 z-50 hidden flex-col bg-black"
         role="dialog" aria-modal="true" aria-label="Lightbox galeri">

        {{-- Top bar --}}
        <div class="flex items-center justify-between px-5 py-3 shrink-0">
            <span id="lb-counter" class="text-sm font-medium text-white/50"></span>
            <button type="button" id="lb-close" onclick="closeLightbox()"
                    class="flex h-9 w-9 items-center justify-center rounded-full bg-white/10 text-white hover:bg-white/20 transition"
                    title="Tutup (Esc)">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Main area: prev | image | next --}}
        <div class="relative flex flex-1 items-center justify-center overflow-hidden min-h-0">

            {{-- Prev button --}}
            <button type="button" id="lb-prev" onclick="lightboxNav(-1)"
                    class="absolute left-3 z-10 flex h-12 w-12 items-center justify-center rounded-full bg-white/10 text-white hover:bg-white/25 transition-all hover:scale-110 disabled:opacity-25 disabled:cursor-not-allowed"
                    title="Sebelumnya (←)">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                </svg>
            </button>

            {{-- Image --}}
            <div class="flex h-full w-full items-center justify-center px-20" onclick="closeLightbox()">
                <img id="lb-img" src="" alt=""
                     class="max-h-full max-w-full object-contain rounded-xl shadow-2xl transition-opacity duration-200"
                     onclick="event.stopPropagation()">
            </div>

            {{-- Next button --}}
            <button type="button" id="lb-next" onclick="lightboxNav(1)"
                    class="absolute right-3 z-10 flex h-12 w-12 items-center justify-center rounded-full bg-white/10 text-white hover:bg-white/25 transition-all hover:scale-110 disabled:opacity-25 disabled:cursor-not-allowed"
                    title="Berikutnya (→)">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                </svg>
            </button>
        </div>

        {{-- Info panel — kiri bawah --}}
        <div class="shrink-0 px-6 py-5">
            <div class="flex items-end justify-between">
                <div class="max-w-xl">
                    <h2 id="lb-title" class="text-xl font-bold leading-tight text-white"></h2>
                    <div class="mt-2 flex items-center gap-3">
                        <span id="lb-category" class="rounded-full bg-white/15 px-3 py-1 text-sm font-medium text-white/90 backdrop-blur-sm"></span>
                        <span id="lb-year" class="text-sm text-white/60"></span>
                    </div>
                </div>
                {{-- Thumbnail strip hint --}}
                <p class="text-xs text-white/30 hidden sm:block">← → untuk navigasi &nbsp;·&nbsp; Esc untuk tutup</p>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
@php
    $mappedImages = $galleries->map(fn($g) => [
        'src'      => asset('storage/' . $g->image_path),
        'title'    => $g->title,
        'category' => $g->category,
        'year'     => $g->year,
    ]);
@endphp
<script>
const allImages = @json($mappedImages);
// Indeks yang sedang aktif di lightbox
let lbIndex   = 0;
let lbVisible = [...allImages.map((_, i) => i)]; // default semua

const lb        = document.getElementById('lightbox');
const lbImg     = document.getElementById('lb-img');
const lbTitle   = document.getElementById('lb-title');
const lbCat     = document.getElementById('lb-category');
const lbYear    = document.getElementById('lb-year');
const lbCounter = document.getElementById('lb-counter');
const lbPrev    = document.getElementById('lb-prev');
const lbNext    = document.getElementById('lb-next');

function openLightboxAt(index) {
    // Kita perlu tahu visible order dari DOM saat ini
    const visibleCards = Array.from(document.querySelectorAll('.gallery-pub-card:not(.hidden)'));
    lbVisible = visibleCards.map(c => parseInt(c.dataset.index));

    // Cari posisi index dalam lbVisible
    const posInVisible = lbVisible.indexOf(index);
    lbIndex = posInVisible >= 0 ? posInVisible : 0;

    lb.classList.remove('hidden');
    lb.classList.add('flex');
    document.body.style.overflow = 'hidden';
    renderLightbox();
}

function renderLightbox() {
    const realIdx = lbVisible[lbIndex];
    const item    = allImages[realIdx];
    if (!item) return;

    lbImg.style.opacity = '0';
    lbImg.src           = item.src;
    lbImg.alt           = item.title;
    lbImg.onload = () => { lbImg.style.opacity = '1'; };

    lbTitle.textContent = item.title;
    lbCat.textContent   = item.category;
    lbYear.textContent  = item.year;
    lbCounter.textContent = (lbIndex + 1) + ' / ' + lbVisible.length;

    lbPrev.disabled = lbIndex === 0;
    lbNext.disabled = lbIndex >= lbVisible.length - 1;
}

function lightboxNav(dir) {
    const next = lbIndex + dir;
    if (next < 0 || next >= lbVisible.length) return;
    lbIndex = next;
    renderLightbox();
}

function closeLightbox() {
    lb.classList.add('hidden');
    lb.classList.remove('flex');
    document.body.style.overflow = '';
    lbImg.src = '';
}

document.addEventListener('keydown', e => {
    if (!lb.classList.contains('flex')) return;
    if (e.key === 'Escape')      closeLightbox();
    if (e.key === 'ArrowLeft')   lightboxNav(-1);
    if (e.key === 'ArrowRight')  lightboxNav(1);
});

// ──────────────────────────────────────────────────────────────────────────────
// Client-side filter
// ──────────────────────────────────────────────────────────────────────────────
(function() {
    const searchEl = document.getElementById('gallery-search');
    const catEl    = document.getElementById('gallery-category-filter');
    const yearEl   = document.getElementById('gallery-year-filter');
    const countEl  = document.getElementById('gallery-count');
    const noResult = document.getElementById('no-result');
    const cards    = Array.from(document.querySelectorAll('.gallery-pub-card'));
    let timer      = null;

    function filter() {
        const q    = searchEl.value.toLowerCase();
        const cat  = catEl.value.toLowerCase();
        const year = yearEl.value;
        let shown  = 0;

        cards.forEach(card => {
            const ok = (q === '' || card.dataset.title.includes(q) || card.dataset.category.includes(q))
                    && (cat  === '' || card.dataset.category === cat)
                    && (year === '' || card.dataset.year     === year);
            card.classList.toggle('hidden', !ok);
            if (ok) shown++;
        });

        countEl.textContent = shown;
        if (noResult) noResult.classList.toggle('hidden', shown > 0);
    }

    [searchEl, catEl, yearEl].forEach(el => {
        el.addEventListener('input', () => { clearTimeout(timer); timer = setTimeout(filter, 150); });
    });
})();
</script>
@endpush
