@extends('layouts.public')

@section('title', ($project->title ?? __('public.project_detail.fallback_title')) . ' | ' . config('app.name'))

@section('content')
    @include('public.partials.page-hero', [
        'title'    => __('public.project_detail.hero_title'),
        'subtitle' => __('public.project_detail.hero_subtitle'),
    ])

    <section class="section-wrap public-page-shell">

        {{-- Back --}}
        <div class="mb-6">
            <a href="{{ route('public.projects') }}" class="back-link-btn">
                <i class="fa-solid fa-arrow-left"></i>
                Kembali ke Daftar Project
            </a>
        </div>

        @php
            $thumb      = $project->image_path ? asset('storage/' . $project->image_path) : asset('image/galeri/image3.jpeg');
            $shareUrl   = url()->current();
            $shareText  = 'Project: ' . $project->title;
            $encUrl     = rawurlencode($shareUrl);
            $encText    = rawurlencode($shareText);
        @endphp

        {{-- Detail card --}}
        <article class="project-detail-layout overflow-hidden rounded-2xl border border-[var(--border-soft)] bg-white shadow-sm">

            {{-- Cover --}}
            <img src="{{ $thumb }}" alt="{{ $project->title }}" class="project-detail-cover w-full object-cover max-h-80">

            <div class="p-5 md:p-8">

                {{-- Badge unggulan --}}
                @if($project->is_feature)
                <span class="mb-3 inline-flex items-center gap-1.5 rounded-full bg-amber-100 px-3 py-1 text-xs font-bold text-amber-700">
                    <svg class="h-3.5 w-3.5 fill-amber-500" viewBox="0 0 24 24"><path d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z"/></svg>
                    Project Unggulan
                </span>
                @endif

                {{-- Title --}}
                <h1 class="text-2xl md:text-3xl font-black text-[var(--text-main)] leading-tight">{{ $project->title }}</h1>

                {{-- Description --}}
                @if($project->description)
                <p class="mt-4 text-[var(--text-soft)] leading-relaxed whitespace-pre-line">{{ $project->description }}</p>
                @endif

                {{-- Meta info --}}
                <div class="project-detail-meta-grid mt-6">
                    <div class="project-detail-meta-item">
                        <span class="font-semibold text-[var(--text-main)]">Mahasiswa</span>
                        <span class="text-[var(--text-soft)]">{{ $project->student_name }}</span>
                    </div>
                    @if($project->student_nim)
                    <div class="project-detail-meta-item">
                        <span class="font-semibold text-[var(--text-main)]">NIM</span>
                        <span class="text-[var(--text-soft)]">{{ $project->student_nim }}</span>
                    </div>
                    @endif
                    @if($project->year)
                    <div class="project-detail-meta-item">
                        <span class="font-semibold text-[var(--text-main)]">Tahun</span>
                        <span class="text-[var(--text-soft)]">{{ $project->year }}</span>
                    </div>
                    @endif
                </div>

                {{-- Share --}}
                <div class="project-share-wrap mt-8" data-no-auto-translate>
                    <p class="project-share-title">{{ __('public.project_detail.share.title') }}</p>
                    <div class="project-share-actions">
                        <a href="https://api.whatsapp.com/send?text={{ $encText }}%20{{ $encUrl }}"
                           target="_blank" rel="noopener" class="project-share-btn"
                           aria-label="{{ __('public.project_detail.share.aria_whatsapp') }}">
                            <i class="fa-brands fa-whatsapp"></i>
                            <span>{{ __('public.project_detail.share.whatsapp') }}</span>
                        </a>
                        <a href="https://twitter.com/intent/tweet?text={{ $encText }}&url={{ $encUrl }}"
                           target="_blank" rel="noopener" class="project-share-btn"
                           aria-label="{{ __('public.project_detail.share.aria_x') }}">
                            <i class="fa-brands fa-x-twitter"></i>
                            <span>{{ __('public.project_detail.share.x') }}</span>
                        </a>
                        <button type="button" class="project-share-btn" data-project-copy-link="{{ $shareUrl }}">
                            <i class="fa-regular fa-copy"></i>
                            <span>{{ __('public.project_detail.share.copy') }}</span>
                        </button>
                    </div>
                    <p class="project-share-feedback" data-project-copy-feedback hidden>
                        {{ __('public.project_detail.share.copied') }}
                    </p>
                </div>
            </div>
        </article>

        {{-- Related projects --}}
        @if ($relatedProjects->isNotEmpty())
        <section class="mt-12">
            <div class="public-subhead mb-6">
                <h2 class="text-xl font-black text-[var(--text-main)]">{{ __('public.project_detail.related_title') }}</h2>
            </div>
            <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($relatedProjects as $item)
                @php $relThumb = $item->image_path ? asset('storage/' . $item->image_path) : asset('image/galeri/image3.jpeg'); @endphp
                <a href="{{ route('public.projects.show', $item) }}"
                   class="group overflow-hidden rounded-2xl border border-[var(--border-soft)] bg-white shadow-sm hover:shadow-md transition-shadow">
                    <img src="{{ $relThumb }}" alt="{{ $item->title }}" class="h-40 w-full object-cover transition-transform duration-500 group-hover:scale-105">
                    <div class="p-4">
                        <h3 class="font-bold text-[var(--text-main)] text-sm line-clamp-2">{{ $item->title }}</h3>
                        <p class="mt-1 text-xs text-[var(--text-soft)]">{{ $item->student_name }}</p>
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
    const copyBtn  = document.querySelector('[data-project-copy-link]');
    const feedback = document.querySelector('[data-project-copy-feedback]');
    if (!copyBtn) return;

    let timer = null;
    copyBtn.addEventListener('click', async () => {
        const link = copyBtn.getAttribute('data-project-copy-link');
        try {
            if (window.isSecureContext && navigator.clipboard?.writeText) {
                await navigator.clipboard.writeText(link);
            } else {
                const ta = document.createElement('textarea');
                ta.value = link; ta.style.position = 'fixed'; ta.style.opacity = '0';
                document.body.appendChild(ta); ta.select(); document.execCommand('copy');
                document.body.removeChild(ta);
            }
        } catch {}
        if (feedback) {
            feedback.hidden = false;
            clearTimeout(timer);
            timer = setTimeout(() => { feedback.hidden = true; }, 2200);
        }
    });
});
</script>
@endpush
