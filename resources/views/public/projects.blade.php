@extends('layouts.public')

@section('title', __('public.projects.page_title'))

@section('content')
    @include('public.partials.page-hero', [
        'title' => __('public.projects.hero_title'),
        'subtitle' => __('public.projects.hero_subtitle'),
    ])

    <section class="section-wrap public-page-shell">
        <header class="public-page-intro">
            <h2 class="public-page-title">{{ __('public.projects.intro_title') }}</h2>
            <p class="public-page-copy">{{ __('public.projects.intro_copy') }}</p>
            <div class="public-page-meta">
                <span class="meta-pill">{{ __('public.projects.meta_featured') }}: {{ $featuredProjects->count() }}</span>
                <span class="meta-pill">{{ __('public.projects.meta_regular') }}: {{ $regularProjects->count() }}</span>
            </div>
        </header>

        <div class="public-subhead">
            <h3 class="text-2xl font-black text-[var(--text-main)]">{{ __('public.projects.featured_title') }}</h3>
        </div>

        <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
            @forelse ($featuredProjects as $project)
                @php
                    $thumbnail = $project->thumbnail ? asset('storage/' . $project->thumbnail) : asset('image/galeri/image3.jpeg');
                @endphp
                <a
                    href="{{ route('public.projects.show', $project) }}"
                    class="project-spotlight-card {{ $project->is_featured ? 'is-featured' : '' }}"
                >
                    <div class="relative">
                        <img src="{{ $thumbnail }}" alt="{{ $project->title }}" class="h-44 w-full object-cover" />
                        <span class="project-rank-badge">#{{ $loop->iteration }}</span>
                    </div>
                    <div class="p-4">
                        <h3 class="mb-1 text-lg font-bold">{{ $project->title }}</h3>
                        <p class="text-sm text-[var(--text-soft)]">{{ $project->student_name }}{{ $project->student_nim ? ' (' . $project->student_nim . ')' : '' }}</p>
                    </div>
                </a>
            @empty
                <div class="rounded-xl border border-dashed border-[var(--border-soft)] bg-white p-8 text-center text-[var(--text-soft)] md:col-span-2 xl:col-span-3">
                    {{ __('public.projects.featured_empty') }}
                </div>
            @endforelse
        </div>

        <section class="project-regular-wrap mt-10">
            <div class="mb-6 border-t border-dashed border-[var(--border-soft)] pt-6 text-center">
                <h3 class="text-2xl font-bold">{{ __('public.projects.regular_title') }}</h3>
            </div>
            <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
                @forelse ($regularProjects as $project)
                    @php
                        $thumbnail = $project->thumbnail ? asset('storage/' . $project->thumbnail) : asset('image/galeri/image3.jpeg');
                    @endphp
                    <a href="{{ route('public.projects.show', $project) }}" class="project-spotlight-card">
                        <img src="{{ $thumbnail }}" alt="{{ $project->title }}" class="h-44 w-full object-cover" />
                        <div class="p-4">
                            <h3 class="mb-1 text-lg font-bold">{{ $project->title }}</h3>
                            <p class="text-sm text-[var(--text-soft)]">{{ $project->student_name }}{{ $project->student_nim ? ' (' . $project->student_nim . ')' : '' }}</p>
                        </div>
                    </a>
                @empty
                    <div class="rounded-xl border border-dashed border-[var(--border-soft)] bg-white p-8 text-center text-[var(--text-soft)] md:col-span-2 xl:col-span-3">
                        {{ __('public.projects.regular_empty') }}
                    </div>
                @endforelse
            </div>
        </section>
    </section>
@endsection
