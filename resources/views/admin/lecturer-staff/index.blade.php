@extends('layouts.admin')

@section('title', 'Kelola Dosen & Staff')

@section('content')
<div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="text-2xl font-bold text-slate-800">Dosen & Staff</h1>
        <p class="text-sm text-slate-500">Kelola data dosen dan staff program studi.</p>
    </div>
    <a href="{{ route('admin.lecturer-staff.create') }}"
       class="inline-flex items-center gap-2 rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-700 transition">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
        </svg>
        Tambah Data
    </a>
</div>

@if(session('success'))
<div class="mb-4 rounded-lg bg-emerald-50 border border-emerald-200 p-4 text-sm text-emerald-700">{{ session('success') }}</div>
@endif

{{-- Filter Bar --}}
<div class="mb-5 flex flex-wrap items-center gap-3">
    <input type="text" id="filter-search" placeholder="Cari nama / posisi…"
           class="w-full sm:w-64 rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm placeholder-slate-400 focus:outline-none focus:ring-1 focus:ring-slate-400">

    <select id="filter-type"
            class="rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700 focus:outline-none focus:ring-1 focus:ring-slate-400">
        <option value="">Semua Tipe</option>
        @foreach ($types as $itemType)
            <option value="{{ strtolower($itemType) }}" {{ strtolower($type) === strtolower($itemType) ? 'selected' : '' }}>{{ strtoupper($itemType) }}</option>
        @endforeach
    </select>

    <span class="ml-auto text-xs text-slate-400"><span id="count-label">{{ $members->total() }}</span> orang</span>
</div>

{{-- Card Grid --}}
<div id="staff-grid" class="grid gap-5 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
    @forelse ($members as $member)
    @php
        $photo = $member->photo_path ? asset('storage/' . $member->photo_path) : asset('logo/logo_prodi_it.png');
    @endphp
    <div class="staff-admin-card group relative flex flex-col overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm hover:shadow-md transition-shadow"
         data-name="{{ strtolower($member->name) }}"
         data-position="{{ strtolower($member->position) }}"
         data-type="{{ strtolower($member->type) }}">

        {{-- Foto --}}
        <div class="relative overflow-hidden bg-slate-100">
            <img src="{{ $photo }}" alt="{{ $member->name }}"
                 class="h-44 w-full object-cover object-top transition-transform duration-500 group-hover:scale-105">

            {{-- Type badge --}}
            <span class="absolute left-2 top-2 rounded-full bg-slate-900/80 px-2 py-0.5 text-xs font-semibold text-white backdrop-blur-sm">
                {{ strtoupper($member->type) }}
            </span>

            {{-- Active badge --}}
            @if(!$member->is_active)
            <span class="absolute right-2 top-2 rounded-full bg-rose-500/90 px-2 py-0.5 text-xs font-semibold text-white">
                Nonaktif
            </span>
            @endif
        </div>

        {{-- Info --}}
        <div class="flex flex-1 flex-col p-3">
            <h3 class="text-sm font-bold text-slate-800 leading-snug line-clamp-2">{{ $member->name }}</h3>
            <p class="mt-0.5 text-xs text-slate-500 line-clamp-1">{{ $member->position }}</p>
            @if($member->email)
            <p class="mt-1 text-xs text-slate-400 truncate">{{ $member->email }}</p>
            @endif
        </div>

        {{-- Action buttons --}}
        <div class="flex items-center gap-1 border-t border-slate-100 px-3 py-2">
            <a href="{{ route('admin.lecturer-staff.edit', $member) }}"
               class="flex-1 flex items-center justify-center gap-1.5 rounded-md bg-slate-100 px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-200 transition"
               title="Edit">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Edit
            </a>
            <form method="POST" action="{{ route('admin.lecturer-staff.destroy', $member) }}"
                  onsubmit="return confirm('Hapus {{ addslashes($member->name) }}?')">
                @csrf
                @method('DELETE')
                <button type="submit"
                        class="flex items-center justify-center gap-1.5 rounded-md bg-rose-50 px-3 py-1.5 text-xs font-semibold text-rose-600 hover:bg-rose-100 transition"
                        title="Hapus">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    Hapus
                </button>
            </form>
        </div>
    </div>
    @empty
    <div class="col-span-full py-20 text-center text-slate-400">
        <i class="fa-solid fa-users text-4xl mb-3 block"></i>
        <p class="text-sm">Belum ada data dosen/staff.</p>
    </div>
    @endforelse
</div>

{{-- Pagination --}}
@if($members->hasPages())
<div class="mt-8 flex justify-center">
    {{ $members->links() }}
</div>
@endif

@push('scripts')
<script>
(function () {
    const searchEl = document.getElementById('filter-search');
    const typeEl   = document.getElementById('filter-type');
    const countEl  = document.getElementById('count-label');
    const cards    = Array.from(document.querySelectorAll('.staff-admin-card'));
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
    }

    [searchEl, typeEl].forEach(el => {
        el.addEventListener('input', () => { clearTimeout(timer); timer = setTimeout(filter, 150); });
    });
})();
</script>
@endpush
@endsection
