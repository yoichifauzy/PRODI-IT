@extends('layouts.public')

@section('title', __('public.lecturer_staff_blogs.page_title'))

@section('content')
    @include('public.partials.page-hero', [
        'title' => __('public.lecturer_staff_blogs.hero_title'),
        'subtitle' => __('public.lecturer_staff_blogs.hero_subtitle'),
    ])

    <section class="section-wrap public-page-shell">
        <div class="mb-6">
            <a href="{{ route('public.lecturer-staff') }}" class="back-link-btn">
                <i class="fa-solid fa-arrow-left"></i>
                {{ __('public.lecturer_staff_blogs.back') }}
            </a>
        </div>

        <header class="public-page-intro">
            <h2 class="public-page-title">{{ $member->name }}</h2>
            <p class="public-page-copy">{{ $member->position }} - {{ strtoupper($member->type) }}</p>
            <div class="public-page-meta">
                <span class="meta-pill">{{ __('public.lecturer_staff_blogs.meta_total_blog') }}: {{ $blogs->count() }}</span>
                @if ($member->email)
                    <span class="meta-pill">{{ __('public.lecturer_staff_blogs.meta_email') }}: {{ $member->email }}</span>
                @endif
            </div>
        </header>

        <div class="space-y-8">
            @forelse ($blogs as $blog)
                @php
                    $cover = $blog->cover_image ? asset('storage/' . $blog->cover_image) : asset('image/galeri/image3.jpeg');
                @endphp
                <article class="staff-blog-entry">
                    <img src="{{ $cover }}" alt="{{ $blog->title }}" class="staff-blog-cover" />
                    <div class="p-5 md:p-6">
                        <h3 class="text-2xl font-black text-[var(--text-main)]">{{ $blog->title }}</h3>
                        <div class="staff-blog-meta">
                            <p class="staff-blog-meta-item">
                                <span class="meta-label">{{ __('public.lecturer_staff_blogs.label_date') }}</span>
                                <span>{{ optional($blog->activity_date)->format('d M Y') ?: '-' }}</span>
                            </p>
                            <p class="staff-blog-meta-item">
                                <span class="meta-label">{{ __('public.lecturer_staff_blogs.label_slug') }}</span>
                                <span>/{{ $blog->slug }}</span>
                            </p>
                            <p class="staff-blog-meta-item">
                                <span class="meta-label">{{ __('public.lecturer_staff_blogs.label_location') }}</span>
                                <span>{{ $blog->location ?: '-' }}</span>
                            </p>
                        </div>
                        <p class="text-[var(--text-soft)] leading-relaxed">{{ $blog->description ?: __('public.lecturer_staff_blogs.description_empty') }}</p>
                    </div>
                </article>
            @empty
                <div class="rounded-xl border border-dashed border-[var(--border-soft)] bg-white p-8 text-center text-[var(--text-soft)]">
                    {{ __('public.lecturer_staff_blogs.empty') }}
                </div>
            @endforelse
        </div>
    </section>
@endsection
