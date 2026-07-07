@extends('layouts.public')

@section('title', __('public.lecturer_staff.page_title'))

@section('content')
    @include('public.partials.page-hero', [
        'title'    => __('public.lecturer_staff.hero_title'),
        'subtitle' => __('public.lecturer_staff.hero_subtitle'),
    ])

    <section class="section-wrap public-page-shell">

        {{-- Filter Bar — real-time, no submit --}}
        <div class="mb-8 flex flex-wrap items-center gap-3">
            <input type="text" id="staff-search" placeholder="{{ __('public.lecturer_staff.search_placeholder') }}"
                   class="w-full sm:w-72 rounded-xl border border-[var(--border-soft)] bg-white px-4 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-[var(--accent)]">

            <select id="staff-type-filter"
                    class="rounded-xl border border-[var(--border-soft)] bg-white px-4 py-2.5 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-[var(--accent)]">
                <option value="">{{ __('public.lecturer_staff.filter_all_types') }}</option>
                @foreach ($types as $itemType)
                    <option value="{{ strtolower($itemType) }}">{{ strtoupper($itemType) }}</option>
                @endforeach
            </select>

            <span class="ml-auto text-xs text-[var(--text-soft)]">
                <span id="staff-count">{{ $members->count() }}</span> orang
            </span>
        </div>

        {{-- Card Grid --}}
        <div id="staff-grid" class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
            @forelse ($members as $member)
                @php
                    $photo = $member->photo_path ? asset('storage/' . $member->photo_path) : asset('logo/logo_prodi_it.png');
                @endphp
                <article class="staff-card staff-card-enter staff-pub-card"
                         data-name="{{ strtolower($member->name) }}"
                         data-position="{{ strtolower($member->position) }}"
                         data-type="{{ strtolower($member->type) }}"
                         style="animation-delay: {{ ($loop->index % 9) * 90 }}ms;">
                    <div class="staff-card-glow"></div>
                    <div class="staff-card-content">
                        <div class="mb-4 flex items-center gap-4">
                            <img src="{{ $photo }}" alt="{{ $member->name }}" class="staff-avatar" />
                            <div>
                                <h3 class="text-xl font-bold text-[var(--text-main)]">{{ $member->name }}</h3>
                                <p class="text-sm text-[var(--text-soft)]">{{ strtoupper($member->type) }}</p>
                            </div>
                        </div>

                        <p class="staff-role-chip mb-2 text-sm font-semibold text-white">{{ $member->position }}</p>
                        <p class="mb-3 text-sm text-[var(--text-soft)]">{{ $member->bio ?: __('public.lecturer_staff.bio_fallback') }}</p>

                        @if ($member->email)
                            <a href="mailto:{{ $member->email }}" class="staff-email-link relative z-20 text-black">{{ $member->email }}</a>
                        @endif
                    </div>
                </article>
            @empty
                <div id="staff-empty-db" class="rounded-xl border border-dashed border-[var(--border-soft)] bg-white p-8 text-center text-[var(--text-soft)] md:col-span-2 xl:col-span-3">
                    {{ __('public.lecturer_staff.empty') }}
                </div>
            @endforelse
        </div>

        {{-- No result after filter --}}
        <div id="staff-no-result" class="hidden py-16 text-center text-[var(--text-soft)]">
            <i class="fa-solid fa-magnifying-glass text-3xl mb-3 block opacity-30"></i>
            <p class="text-sm">Tidak ada dosen/staff yang cocok.</p>
        </div>

    </section>
@endsection

@push('scripts')
<script>
(function () {
    const searchEl  = document.getElementById('staff-search');
    const typeEl    = document.getElementById('staff-type-filter');
    const countEl   = document.getElementById('staff-count');
    const noResult  = document.getElementById('staff-no-result');
    const cards     = Array.from(document.querySelectorAll('.staff-pub-card'));
    let timer = null;

    function filter() {
        const q    = searchEl.value.toLowerCase().trim();
        const type = typeEl.value.toLowerCase();
        let shown  = 0;

        cards.forEach(card => {
            const matchQ    = q    === '' || card.dataset.name.includes(q) || card.dataset.position.includes(q);
            const matchType = type === '' || card.dataset.type === type;
            const ok = matchQ && matchType;
            card.classList.toggle('hidden', !ok);
            if (ok) shown++;
        });

        countEl.textContent = shown;
        if (noResult) noResult.classList.toggle('hidden', shown > 0);
    }

    [searchEl, typeEl].forEach(el => {
        el.addEventListener('input', () => { clearTimeout(timer); timer = setTimeout(filter, 150); });
    });
})();
</script>
@endpush
