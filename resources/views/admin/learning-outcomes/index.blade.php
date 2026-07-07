@extends('layouts.admin')

@section('title', 'Capaian Pembelajaran Lulusan (CPL)')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h1 class="text-lg font-bold text-slate-800">Capaian Pembelajaran Lulusan</h1>
        <p class="text-sm text-slate-400">Sync dari sheet CPL.</p>
    </div>

    <form action="{{ route('admin.learning-outcomes.sync') }}" method="POST" class="flex flex-col gap-3 sm:flex-row sm:items-center">
        @csrf
        <select name="document_id" id="document_id"
                class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700 focus:border-slate-900 focus:outline-none focus:ring-1 focus:ring-slate-900 sm:w-64">
            <option value="">-- Pilih Dokumen --</option>
            @foreach($docs as $doc)
                <option value="{{ $doc->id }}" {{ ($defaultDoc && $defaultDoc->id === $doc->id) ? 'selected' : '' }}>
                    {{ $doc->name }}
                </option>
            @endforeach
        </select>

        <button type="submit"
                class="inline-flex items-center justify-center gap-2 rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white shadow-sm transition-all hover:bg-slate-800">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
            </svg>
            Sync Sekarang
        </button>
    </form>
</div>

@if(session('success'))
<div class="mb-4 rounded-lg bg-emerald-50 border border-emerald-200 p-4 text-sm text-emerald-700">{{ session('success') }}</div>
@endif
@if(session('error'))
<div class="mb-4 rounded-lg bg-rose-50 border border-rose-200 p-4 text-sm text-rose-700">{{ session('error') }}</div>
@endif

{{-- Filter --}}
<div class="mb-4">
    <input type="text" id="search-input" placeholder="Cari kode / deskripsi…"
           class="w-full sm:w-80 rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm placeholder-slate-400 focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500">
</div>

{{-- Table --}}
<div class="rounded-xl border border-slate-200 bg-white overflow-hidden shadow-sm">
    <div class="border-b border-slate-100 bg-slate-50 px-5 py-3 flex items-center justify-between">
        <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Capaian Pembelajaran (CPL)</p>
        <span class="text-xs text-slate-400" id="cpl-count">{{ $outcomes->count() }} item</span>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-100 text-sm">
            <thead class="bg-white">
                <tr>
                    <th class="px-4 py-2.5 text-center text-xs font-semibold text-slate-500 w-10">No</th>
                    <th class="px-4 py-2.5 text-left text-xs font-semibold text-slate-500 w-28">Kode</th>
                    <th class="px-4 py-2.5 text-left text-xs font-semibold text-slate-500">Deskripsi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100" id="table-cpl">
                @forelse($outcomes as $o)
                <tr class="hover:bg-slate-50 js-row-cpl"
                    data-code="{{ strtolower($o->code) }}"
                    data-desc="{{ strtolower($o->description) }}">
                    <td class="px-4 py-2.5 text-center text-slate-400 text-xs js-row-number"></td>
                    <td class="px-4 py-2.5 text-slate-700 text-xs font-mono font-semibold">{{ $o->code }}</td>
                    <td class="px-4 py-2.5 text-slate-700">{{ $o->description }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" class="px-4 py-12 text-center text-slate-400 text-sm">
                        Belum ada data CPL. Pilih dokumen dan klik Sync Sekarang.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div id="pag-cpl" class="px-5 py-3 border-t border-slate-100 flex justify-end flex-wrap gap-2"></div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('search-input');
    const rows = Array.from(document.querySelectorAll('.js-row-cpl'));
    const pagContainer = document.getElementById('pag-cpl');
    const countEl = document.getElementById('cpl-count');
    let currentPage = 1;
    const perPage = 15;

    function update() {
        const q = searchInput.value.toLowerCase();
        const filtered = rows.filter(r =>
            q === '' || r.dataset.code.includes(q) || r.dataset.desc.includes(q)
        );

        rows.forEach(r => r.classList.add('hidden'));

        const totalPages = Math.max(1, Math.ceil(filtered.length / perPage));
        if (currentPage > totalPages) currentPage = totalPages;

        const start = (currentPage - 1) * perPage;
        const visible = filtered.slice(start, start + perPage);
        visible.forEach((r, i) => {
            r.classList.remove('hidden');
            const numCell = r.querySelector('.js-row-number');
            if (numCell) numCell.textContent = start + i + 1;
        });

        if (countEl) countEl.textContent = filtered.length + ' item';
        renderPag(totalPages);
    }

    function renderPag(total) {
        if (total <= 1) { pagContainer.innerHTML = ''; return; }
        pagContainer.innerHTML = `<div class="flex gap-2">
            ${Array.from({length: total}, (_, i) => i + 1).map(p => `
                <button type="button" class="px-3 py-1 border ${p === currentPage ? 'bg-slate-700 text-white' : 'bg-white'} rounded text-xs" data-p="${p}">${p}</button>
            `).join('')}
        </div>`;
        pagContainer.querySelectorAll('button').forEach(btn => {
            btn.onclick = () => { currentPage = parseInt(btn.dataset.p); update(); };
        });
    }

    searchInput.addEventListener('input', () => { currentPage = 1; update(); });
    update();
});
</script>
@endpush
@endsection