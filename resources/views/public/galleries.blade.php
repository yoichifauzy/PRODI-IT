@extends('layouts.public')

@section('title', __('public.galleries.page_title'))

@section('content')
    @include('public.partials.page-hero', [
        'title' => __('public.galleries.hero_title'),
        'subtitle' => __('public.galleries.hero_subtitle'),
    ])

    <section class="section-wrap public-page-shell">
        <header class="public-page-intro">
            <h2 class="public-page-title">{{ __('public.galleries.intro_title') }}</h2>
            <p class="public-page-copy">{{ __('public.galleries.intro_copy') }}</p>
            <div class="public-page-meta">
                <span class="meta-pill">{{ __('public.galleries.meta_total') }}: {{ $galleryItems->total() }}</span>
                <span class="meta-pill">{{ __('public.galleries.meta_page') }}: {{ $galleryItems->currentPage() }} / {{ $galleryItems->lastPage() }}</span>
            </div>
        </header>

        <div class="mb-6 flex flex-wrap justify-center gap-3">
            <a href="{{ route('public.galleries') }}" class="gallery-filter-btn {{ $selectedGallery === '' ? 'is-active' : '' }}">
                {{ __('public.home.gallery.category_all') }}
            </a>
            @foreach ($galleries as $gallery)
                <a href="{{ route('public.galleries', ['gallery' => $gallery->slug]) }}" class="gallery-filter-btn {{ $selectedGallery === $gallery->slug ? 'is-active' : '' }}">
                    {{ $gallery->name }}
                </a>
            @endforeach
        </div>

        <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
            @forelse ($galleryItems as $item)
                @php
                    $image = asset('storage/' . $item->image_path);
                    $categoryName = $item->gallery?->name ?? __('public.galleries.fallback_category');
                    $title = $item->title ?: $categoryName;
                @endphp
                <a href="{{ route('public.galleries.show', $item) }}" class="activities-grid-card group block h-full overflow-hidden transition-transform duration-200 hover:-translate-y-1 focus-visible:-translate-y-1 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-orange-400">
                    <div class="activities-grid-media">
                        <img src="{{ $image }}" alt="{{ $title }}" class="h-56 w-full object-cover transition-transform duration-500 group-hover:scale-110" />
                        <span class="activities-type-badge">{{ strtoupper($categoryName) }}</span>
                    </div>

                    <div class="p-5">
                        <p class="activities-date-line">
                            <i class="fa-regular fa-calendar"></i>
                            <span>{{ optional($item->taken_at)->format('d F Y') ?: '-' }}</span>
                        </p>

                        <h3 class="mb-2 mt-2 text-xl font-bold text-[var(--text-main)] line-clamp-2">{{ $title }}</h3>
                        <p class="text-sm leading-relaxed text-[var(--text-soft)] line-clamp-3">
                            {{ \Illuminate\Support\Str::limit($item->caption ?: __('public.galleries.caption_empty'), 160) }}
                        </p>
                    </div>
                </a>
            @empty
                <div class="rounded-xl border border-dashed border-[var(--border-soft)] bg-white p-8 text-center text-[var(--text-soft)] md:col-span-2 xl:col-span-3">
                    {{ __('public.galleries.empty') }}
                </div>
            @endforelse
        </div>

        @if ($galleryItems->hasPages())
            <div class="mt-8 rounded-xl border border-[var(--border-soft)] bg-white px-4 py-3 shadow-sm">
                {{ $galleryItems->links() }}
            </div>
        @endif
    </section>
@endsection
