@extends('layouts.public')

@section('title', __('public.lecturer_staff.page_title'))

@section('content')
    @include('public.partials.page-hero', [
        'title' => __('public.lecturer_staff.hero_title'),
        'subtitle' => __('public.lecturer_staff.hero_subtitle'),
    ])

    <section class="section-wrap public-page-shell">
        <header class="public-page-intro">
            <h2 class="public-page-title">{{ __('public.lecturer_staff.intro_title') }}</h2>
            <p class="public-page-copy">{{ __('public.lecturer_staff.intro_copy') }}</p>
            <div class="public-page-meta">
                <span class="meta-pill">{{ __('public.lecturer_staff.meta_total_personnel') }}: {{ $members->count() }}</span>
                @if ($type !== '')
                    <span class="meta-pill">{{ __('public.lecturer_staff.meta_category') }}: {{ strtoupper($type) }}</span>
                @endif
                @if ($search !== '')
                    <span class="meta-pill">{{ __('public.lecturer_staff.meta_search') }}: {{ $search }}</span>
                @endif
            </div>
        </header>

        <form method="GET" action="{{ route('public.lecturer-staff') }}" class="staff-search-wrap public-panel mb-8 grid gap-3 rounded-2xl border border-[var(--border-soft)] bg-white p-4 shadow-sm md:grid-cols-[1fr_220px_auto]">
            <input type="text" name="q" value="{{ $search }}" placeholder="{{ __('public.lecturer_staff.search_placeholder') }}" class="form-input" />
            <select name="type" class="form-input">
                <option value="">{{ __('public.lecturer_staff.filter_all_types') }}</option>
                @foreach ($types as $itemType)
                    <option value="{{ $itemType }}" @selected($type === $itemType)>{{ strtoupper($itemType) }}</option>
                @endforeach
            </select>
            <button type="submit" class="solid-cta">{{ __('public.lecturer_staff.search_button') }}</button>
        </form>

        <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
            @forelse ($members as $member)
                <article class="staff-card staff-card-enter" style="animation-delay: {{ ($loop->index % 9) * 90 }}ms;">
                    <a href="{{ route('public.lecturer-staff.blogs', $member) }}" class="staff-card-hit-area" aria-label="{{ __('public.lecturer_staff.open_blog_aria', ['name' => $member->name]) }}"></a>
                    <div class="staff-card-glow"></div>
                    <div class="staff-card-content">
                        <div class="mb-4 flex items-center gap-4">
                            @php
                                $photo = $member->photo_path ? asset('storage/' . $member->photo_path) : asset('logo/logo_prodi_it.png');
                            @endphp
                            <img src="{{ $photo }}" alt="{{ $member->name }}" class="staff-avatar" />
                            <div>
                                <h3 class="text-xl font-bold text-[var(--text-main)]">{{ $member->name }}</h3>
                                <p class="text-sm text-[var(--text-soft)]">{{ strtoupper($member->type) }}</p>
                            </div>
                        </div>

                        <p class="staff-role-chip mb-2 text-sm font-semibold text-[var(--accent)]">{{ $member->position }}</p>
                        <p class="mb-3 text-sm text-[var(--text-soft)]">{{ $member->bio ?: __('public.lecturer_staff.bio_fallback') }}</p>

                        @if ($member->email)
                            <a href="mailto:{{ $member->email }}" class="staff-email-link relative z-20">{{ $member->email }}</a>
                        @endif

                        <p class="staff-card-blog-cta">{{ __('public.lecturer_staff.blog_cta') }}</p>
                    </div>
                </article>
            @empty
                <div class="rounded-xl border border-dashed border-[var(--border-soft)] bg-white p-8 text-center text-[var(--text-soft)] md:col-span-2 xl:col-span-3">
                    {{ __('public.lecturer_staff.empty') }}
                </div>
            @endforelse
        </div>
    </section>
@endsection
