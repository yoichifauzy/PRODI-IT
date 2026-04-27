@extends('layouts.public')

@section('title', ($galleryItem->title ?: ($galleryItem->gallery?->name ?? __('public.gallery_detail.fallback_title'))) . ' | ' . __('public.gallery_detail.page_suffix'))

@section('content')
    @include('public.partials.page-hero', [
        'title' => __('public.gallery_detail.hero_title'),
        'subtitle' => __('public.gallery_detail.hero_subtitle'),
    ])

    <section class="section-wrap public-page-shell">
        <div class="mb-6 flex flex-wrap gap-3">
            <a href="{{ route('public.galleries', ['gallery' => $galleryItem->gallery?->slug]) }}" class="back-link-btn">
                <i class="fa-solid fa-arrow-left"></i>
                {{ __('public.gallery_detail.back') }}
            </a>
            <a href="{{ route('public.galleries') }}" class="back-link-btn">
                <i class="fa-solid fa-table-cells-large"></i>
                {{ __('public.gallery_detail.back_all') }}
            </a>
        </div>

        @php
            $image = $galleryItem->image_path ? asset('storage/' . $galleryItem->image_path) : asset('image/galeri/image3.jpeg');
            $categoryName = $galleryItem->gallery?->name ?? __('public.galleries.fallback_category');
            $shareUrl = route('public.galleries.show', $galleryItem);
            $shareText = __('public.gallery_detail.share.message_prefix') . ' ' . ($galleryItem->title ?: $categoryName);
            $encodedShareUrl = rawurlencode($shareUrl);
            $encodedShareText = rawurlencode($shareText);
        @endphp

        <article class="project-detail-layout">
            <img src="{{ $image }}" alt="{{ $galleryItem->title ?: $categoryName }}" class="project-detail-cover" />
            <div class="p-5 md:p-6">
                <span class="activities-type-badge inline-flex">{{ strtoupper($categoryName) }}</span>
                <h1 class="mt-3 text-2xl md:text-3xl font-black text-[var(--text-main)]">{{ $galleryItem->title ?: $categoryName }}</h1>
                <p class="mt-2 text-[var(--text-soft)]">{{ $galleryItem->caption ?: __('public.gallery_detail.caption_empty') }}</p>

                <div class="project-detail-meta-grid mt-5">
                    <p class="project-detail-meta-item">
                        <span class="font-semibold">{{ __('public.gallery_detail.meta_category') }}</span>
                        <span>{{ $categoryName }}</span>
                    </p>
                    <p class="project-detail-meta-item">
                        <span class="font-semibold">{{ __('public.gallery_detail.meta_taken_at') }}</span>
                        <span>{{ optional($galleryItem->taken_at)->format('d M Y') ?: '-' }}</span>
                    </p>
                </div>

                <div class="project-share-wrap mt-5" data-no-auto-translate>
                    <p class="project-share-title">{{ __('public.gallery_detail.share.title') }}</p>
                    <div class="project-share-actions">
                        <a href="https://api.whatsapp.com/send?text={{ $encodedShareText }}%20{{ $encodedShareUrl }}" target="_blank" rel="noopener" class="project-share-btn" aria-label="{{ __('public.gallery_detail.share.aria_whatsapp') }}">
                            <i class="fa-brands fa-whatsapp"></i>
                            <span>{{ __('public.gallery_detail.share.whatsapp') }}</span>
                        </a>
                        <a href="https://twitter.com/intent/tweet?text={{ $encodedShareText }}&url={{ $encodedShareUrl }}" target="_blank" rel="noopener" class="project-share-btn" aria-label="{{ __('public.gallery_detail.share.aria_x') }}">
                            <i class="fa-brands fa-x-twitter"></i>
                            <span>{{ __('public.gallery_detail.share.x') }}</span>
                        </a>
                        <button type="button" class="project-share-btn" data-gallery-copy-link="{{ $shareUrl }}">
                            <i class="fa-regular fa-copy"></i>
                            <span>{{ __('public.gallery_detail.share.copy') }}</span>
                        </button>
                    </div>
                    <p class="project-share-feedback" data-gallery-copy-feedback hidden>{{ __('public.gallery_detail.share.copied') }}</p>
                </div>
            </div>
        </article>

        @if ($relatedGalleryItems->isNotEmpty())
            <section class="mt-10">
                <div class="public-subhead">
                    <h2 class="text-2xl font-black text-[var(--text-main)]">{{ __('public.gallery_detail.related_title') }}</h2>
                </div>

                <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
                    @foreach ($relatedGalleryItems as $item)
                        @php
                            $relatedImage = $item->image_path ? asset('storage/' . $item->image_path) : asset('image/galeri/image3.jpeg');
                            $relatedTitle = $item->title ?: ($item->gallery?->name ?? __('public.galleries.fallback_category'));
                        @endphp
                        <a href="{{ route('public.galleries.show', $item) }}" class="activities-grid-card group block h-full overflow-hidden">
                            <div class="activities-grid-media">
                                <img src="{{ $relatedImage }}" alt="{{ $relatedTitle }}" class="h-48 w-full object-cover transition-transform duration-500 group-hover:scale-110" />
                                <span class="activities-type-badge">{{ strtoupper($item->gallery?->name ?? __('public.galleries.fallback_category')) }}</span>
                            </div>
                            <div class="p-4">
                                <h3 class="mb-1 text-lg font-bold text-[var(--text-main)] line-clamp-2">{{ $relatedTitle }}</h3>
                                <p class="text-sm text-[var(--text-soft)]">{{ optional($item->taken_at)->format('d M Y') ?: '-' }}</p>
                            </div>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif
    </section>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const copyButton = document.querySelector('[data-gallery-copy-link]');
            const feedback = document.querySelector('[data-gallery-copy-feedback]');

            if (!copyButton) {
                return;
            }

            let feedbackTimeout = null;

            const fallbackCopy = (value) => {
                const helper = document.createElement('textarea');
                helper.value = value;
                helper.setAttribute('readonly', 'readonly');
                helper.style.position = 'fixed';
                helper.style.opacity = '0';
                document.body.appendChild(helper);
                helper.select();
                document.execCommand('copy');
                document.body.removeChild(helper);
            };

            copyButton.addEventListener('click', async () => {
                const link = copyButton.getAttribute('data-gallery-copy-link');
                if (!link) {
                    return;
                }

                try {
                    if (window.isSecureContext && navigator.clipboard?.writeText) {
                        await navigator.clipboard.writeText(link);
                    } else {
                        fallbackCopy(link);
                    }
                } catch (error) {
                    fallbackCopy(link);
                }

                if (!feedback) {
                    return;
                }

                feedback.hidden = false;
                if (feedbackTimeout) {
                    window.clearTimeout(feedbackTimeout);
                }

                feedbackTimeout = window.setTimeout(() => {
                    feedback.hidden = true;
                }, 2200);
            });
        });
    </script>
@endpush
