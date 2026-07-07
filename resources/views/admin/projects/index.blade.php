@extends('layouts.admin')

@section('title', 'Kelola Project Mahasiswa')

@section('content')
<div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="text-2xl font-bold text-slate-800">Project Mahasiswa</h1>
        <p class="text-sm text-slate-500">Kelola karya dan project unggulan mahasiswa prodi.</p>
    </div>
    <a href="{{ route('admin.projects.create') }}"
       class="inline-flex items-center gap-2 rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-700 transition">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
        </svg>
        Tambah Project
    </a>
</div>

@if(session('success'))
<div class="mb-4 rounded-lg bg-emerald-50 border border-emerald-200 p-4 text-sm text-emerald-700">{{ session('success') }}</div>
@endif
@if(session('error'))
<div class="mb-4 rounded-lg bg-rose-50 border border-rose-200 p-4 text-sm text-rose-700">{{ session('error') }}</div>
@endif

{{-- Filter --}}
<div class="mb-5 flex flex-wrap items-center gap-3">
    <input type="text" id="filter-search" value="{{ $search }}" placeholder="Cari judul, nama, NIM…"
           class="w-full sm:w-72 rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm placeholder-slate-400 focus:outline-none focus:ring-1 focus:ring-slate-400">
    <span class="ml-auto text-xs text-slate-400"><span id="count-label">{{ $projects->total() }}</span> project</span>
</div>

{{-- Alert AJAX --}}
<div id="js-alert" class="hidden mb-4"></div>

{{-- Card Grid --}}
<div id="projects-grid" class="grid gap-5 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
    @forelse ($projects as $project)
    @php
        $thumb = $project->image_path ? asset('storage/' . $project->image_path) : asset('image/galeri/image3.jpeg');
    @endphp
    <div class="project-admin-card group relative flex flex-col overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm hover:shadow-md transition-shadow"
         data-id="{{ $project->id }}"
         data-title="{{ strtolower($project->title) }}"
         data-student="{{ strtolower($project->student_name) }}">

        {{-- Thumbnail --}}
        <div class="relative overflow-hidden bg-slate-100">
            <img src="{{ $thumb }}" alt="{{ $project->title }}"
                 class="h-44 w-full object-cover transition-transform duration-500 group-hover:scale-105">

            {{-- Featured badge --}}
            @if($project->is_feature)
            <span class="is-feature-badge absolute left-2 top-2 flex items-center gap-1 rounded-full bg-amber-400 px-2 py-0.5 text-xs font-bold text-amber-900">
                <svg class="h-3 w-3 fill-amber-900" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                Unggulan
            </span>
            @endif
        </div>

        {{-- Info --}}
        <div class="flex flex-1 flex-col p-3">
            <h3 class="text-sm font-bold text-slate-800 leading-snug line-clamp-2">{{ $project->title }}</h3>
            <p class="mt-0.5 text-xs text-slate-500">{{ $project->student_name }}
                @if($project->student_nim)
                    <span class="text-slate-400">· {{ $project->student_nim }}</span>
                @endif
            </p>
            @if($project->year)
            <p class="mt-1 text-xs text-slate-400">{{ $project->year }}</p>
            @endif
        </div>

        {{-- Action buttons --}}
        <div class="flex items-center gap-1 border-t border-slate-100 px-3 py-2">
            {{-- Star toggle --}}
            <button type="button"
                    onclick="toggleFeature({{ $project->id }}, this)"
                    title="{{ $project->is_feature ? 'Hapus dari Unggulan' : 'Jadikan Unggulan' }}"
                    class="feature-btn flex h-8 w-8 items-center justify-center rounded-md transition {{ $project->is_feature ? 'bg-amber-100 text-amber-500 hover:bg-amber-200' : 'bg-slate-100 text-slate-400 hover:bg-slate-200 hover:text-amber-500' }}">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="{{ $project->is_feature ? 'currentColor' : 'none' }}" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z"/>
                </svg>
            </button>

            {{-- Edit --}}
            <a href="{{ route('admin.projects.edit', $project) }}"
               class="flex flex-1 items-center justify-center gap-1.5 rounded-md bg-slate-100 px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-200 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Edit
            </a>

            {{-- Delete --}}
            <form method="POST" action="{{ route('admin.projects.destroy', $project) }}"
                  onsubmit="return confirm('Hapus project {{ addslashes($project->title) }}?')">
                @csrf
                @method('DELETE')
                <button type="submit"
                        class="flex items-center justify-center gap-1.5 rounded-md bg-rose-50 px-3 py-1.5 text-xs font-semibold text-rose-600 hover:bg-rose-100 transition">
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
        <i class="fa-solid fa-diagram-project text-4xl mb-3 block"></i>
        <p class="text-sm">Belum ada project mahasiswa.</p>
    </div>
    @endforelse
</div>

{{-- Pagination --}}
@if($projects->hasPages())
<div class="mt-8 flex justify-center">
    {{ $projects->links() }}
</div>
@endif

@push('scripts')
<script>
const CSRF = '{{ csrf_token() }}';

// ── Star toggle ───────────────────────────────────────────────────────────
async function toggleFeature(id, btn) {
    try {
        const res  = await fetch(`/adminit/projects/${id}/toggle-feature`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' }
        });
        const data = await res.json();
        if (!data.success) throw new Error();

        const card     = btn.closest('.project-admin-card');
        const starred  = data.is_feature;
        const svg      = btn.querySelector('svg');

        btn.classList.toggle('bg-amber-100', starred);
        btn.classList.toggle('text-amber-500', starred);
        btn.classList.toggle('bg-slate-100', !starred);
        btn.classList.toggle('text-slate-400', !starred);
        svg.setAttribute('fill', starred ? 'currentColor' : 'none');
        btn.title = starred ? 'Hapus dari Unggulan' : 'Jadikan Unggulan';

        const imgWrapper = card.querySelector('.relative.overflow-hidden');
        let badge = card.querySelector('.is-feature-badge');

        if (starred && !badge) {
            badge = document.createElement('span');
            badge.className = 'is-feature-badge absolute left-2 top-2 flex items-center gap-1 rounded-full bg-amber-400 px-2 py-0.5 text-xs font-bold text-amber-900';
            badge.innerHTML = '<svg class="h-3 w-3 fill-amber-900" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg> Unggulan';
            if (imgWrapper) imgWrapper.prepend(badge);
        } else if (!starred && badge) {
            badge.remove();
        }

        showAlert(starred ? 'Ditandai sebagai unggulan.' : 'Dihapus dari unggulan.', 'success');
    } catch {
        showAlert('Gagal mengubah status unggulan.', 'error');
    }
}

// ── Client-side search filter ─────────────────────────────────────────────
(function() {
    const searchEl = document.getElementById('filter-search');
    const countEl  = document.getElementById('count-label');
    const cards    = Array.from(document.querySelectorAll('.project-admin-card'));
    let timer = null;

    function filter() {
        const q = searchEl.value.toLowerCase().trim();
        let shown = 0;
        cards.forEach(card => {
            const ok = q === '' || card.dataset.title.includes(q) || card.dataset.student.includes(q);
            card.classList.toggle('hidden', !ok);
            if (ok) shown++;
        });
        countEl.textContent = shown;
    }

    searchEl.addEventListener('input', () => { clearTimeout(timer); timer = setTimeout(filter, 150); });
})();

// ── Alert ─────────────────────────────────────────────────────────────────
function showAlert(msg, type) {
    const el  = document.getElementById('js-alert');
    const cls = type === 'success'
        ? 'rounded-lg bg-emerald-50 border border-emerald-200 p-3 text-sm text-emerald-700'
        : 'rounded-lg bg-rose-50 border border-rose-200 p-3 text-sm text-rose-700';
    el.className = cls;
    el.textContent = msg;
    el.classList.remove('hidden');
    setTimeout(() => el.classList.add('hidden'), 3500);
}
</script>
@endpush
@endsection
