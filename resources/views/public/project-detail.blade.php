@extends('layouts.public')

@section('title', ($project->title ?? __('public.project_detail.fallback_title')) . ' | ' . __('public.project_detail.page_suffix'))

@section('content')
    @include('public.partials.page-hero', [
        'title' => __('public.project_detail.hero_title'),
        'subtitle' => __('public.project_detail.hero_subtitle'),
    ])

    <section class="section-wrap public-page-shell">
        <div class="mb-6">
            <a href="{{ url()->previous() !== url()->current() ? url()->previous() : route('public.projects') }}" class="back-link-btn">
                <i class="fa-solid fa-arrow-left"></i>
                {{ __('public.project_detail.back') }}
            </a>
        </div>

        @php
            $thumbnail = $project->thumbnail ? asset('storage/' . $project->thumbnail) : asset('image/galeri/image3.jpeg');
            $shareUrl = route('public.projects.show', $project);
            $shareText = __('public.project_detail.share.message_prefix') . ' ' . $project->title;
            $encodedShareUrl = rawurlencode($shareUrl);
            $encodedShareText = rawurlencode($shareText);
        @endphp

        <article class="project-detail-layout">
            <img src="{{ $thumbnail }}" alt="{{ $project->title }}" class="project-detail-cover" />
            <div class="p-5 md:p-6">
                <h1 class="text-2xl md:text-3xl font-black text-[var(--text-main)]">{{ $project->title }}</h1>
                <p class="mt-2 text-[var(--text-soft)]">{{ $project->summary ?: __('public.project_detail.summary_empty') }}</p>

                <div class="project-share-wrap mt-5" data-no-auto-translate>
                    <p class="project-share-title" data-i18n="project.share.title">{{ __('public.project_detail.share.title') }}</p>
                    <div class="project-share-actions">
                        <a href="https://api.whatsapp.com/send?text={{ $encodedShareText }}%20{{ $encodedShareUrl }}" target="_blank" rel="noopener" class="project-share-btn" aria-label="{{ __('public.project_detail.share.aria_whatsapp') }}">
                            <i class="fa-brands fa-whatsapp"></i>
                            <span data-i18n="project.share.whatsapp">{{ __('public.project_detail.share.whatsapp') }}</span>
                        </a>
                        <a href="https://twitter.com/intent/tweet?text={{ $encodedShareText }}&url={{ $encodedShareUrl }}" target="_blank" rel="noopener" class="project-share-btn" aria-label="{{ __('public.project_detail.share.aria_x') }}">
                            <i class="fa-brands fa-x-twitter"></i>
                            <span data-i18n="project.share.x">{{ __('public.project_detail.share.x') }}</span>
                        </a>
                        <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ $encodedShareUrl }}" target="_blank" rel="noopener" class="project-share-btn" aria-label="{{ __('public.project_detail.share.aria_linkedin') }}">
                            <i class="fa-brands fa-linkedin"></i>
                            <span data-i18n="project.share.linkedin">{{ __('public.project_detail.share.linkedin') }}</span>
                        </a>
                        <button type="button" class="project-share-btn" data-project-copy-link="{{ $shareUrl }}">
                            <i class="fa-regular fa-copy"></i>
                            <span data-i18n="project.share.copy">{{ __('public.project_detail.share.copy') }}</span>
                        </button>
                    </div>
                    <p class="project-share-feedback" data-project-copy-feedback data-i18n="project.share.copied" hidden>{{ __('public.project_detail.share.copied') }}</p>
                </div>

                <div class="project-detail-meta-grid mt-5">
                    <p class="project-detail-meta-item">
                        <span class="font-semibold">{{ __('public.project_detail.meta_slug') }}</span>
                        <span>/{{ $project->slug }}</span>
                    </p>
                    <p class="project-detail-meta-item">
                        <span class="font-semibold">{{ __('public.project_detail.meta_student_name') }}</span>
                        <span>{{ $project->student_name }}</span>
                    </p>
                    <p class="project-detail-meta-item">
                        <span class="font-semibold">{{ __('public.project_detail.meta_nim') }}</span>
                        <span>{{ $project->student_nim ?: '-' }}</span>
                    </p>
                    <p class="project-detail-meta-item">
                        <span class="font-semibold">{{ __('public.project_detail.meta_year') }}</span>
                        <span>{{ $project->year ?: '-' }}</span>
                    </p>
                    <p class="project-detail-meta-item">
                        <span class="font-semibold">{{ __('public.project_detail.meta_published_at') }}</span>
                        <span>{{ optional($project->published_at)->format('d M Y H:i') ?: '-' }}</span>
                    </p>
                    <p class="project-detail-meta-item">
                        <span class="font-semibold">{{ __('public.project_detail.meta_demo_url') }}</span>
                        <span>
                            @if (filled($project->demo_url))
                                <a href="{{ $project->demo_url }}" target="_blank" rel="noopener" class="project-detail-link">{{ $project->demo_url }}</a>
                            @else
                                -
                            @endif
                        </span>
                    </p>
                    <p class="project-detail-meta-item">
                        <span class="font-semibold">{{ __('public.project_detail.meta_repository_url') }}</span>
                        <span>
                            @if (filled($project->repository_url))
                                <a href="{{ $project->repository_url }}" target="_blank" rel="noopener" class="project-detail-link">{{ $project->repository_url }}</a>
                            @else
                                -
                            @endif
                        </span>
                    </p>
                </div>
            </div>
        </article>

        @if ($relatedProjects->isNotEmpty())
            <section class="mt-10">
                <div class="public-subhead">
                    <h2 class="text-2xl font-black text-[var(--text-main)]">{{ __('public.project_detail.related_title') }}</h2>
                </div>
                <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
                    @foreach ($relatedProjects as $item)
                        @php
                            $relatedThumb = $item->thumbnail ? asset('storage/' . $item->thumbnail) : asset('image/galeri/image3.jpeg');
                        @endphp
                        <a href="{{ route('public.projects.show', $item) }}" class="project-spotlight-card">
                            <img src="{{ $relatedThumb }}" alt="{{ $item->title }}" class="h-44 w-full object-cover" />
                            <div class="p-4">
                                <h3 class="mb-1 text-lg font-bold">{{ $item->title }}</h3>
                                <p class="text-sm text-[var(--text-soft)]">{{ $item->student_name }}{{ $item->student_nim ? ' (' . $item->student_nim . ')' : '' }}</p>
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
            const copyButton = document.querySelector('[data-project-copy-link]');
            const feedback = document.querySelector('[data-project-copy-feedback]');

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
                const link = copyButton.getAttribute('data-project-copy-link');
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
