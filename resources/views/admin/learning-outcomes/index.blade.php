@extends('layouts.admin')

@section('title', 'Capaian Pembelajaran Lulusan')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="text-lg font-bold text-slate-800">Capaian Pembelajaran Lulusan</h1>
        <p class="text-sm text-slate-400">Sync dari sheet CPL.</p>
    </div>

    <form action="{{ route('admin.learning-outcomes.sync') }}" method="POST" class="flex flex-col gap-3 sm:flex-row sm:items-center">
            @csrf
            {{-- Dropdown (Style disamakan dengan input search Kegiatan) --}}
            <select name="document_id" id="document_id" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700 focus:border-slate-900 focus:outline-none focus:ring-1 focus:ring-slate-900 sm:w-64">
                <option value="">-- Pilih Dokumen --</option>
                @foreach($docs as $doc)
                    <option value="{{ $doc->id }}" {{ ($defaultDoc && $defaultDoc->id === $doc->id) ? 'selected' : '' }}>
                        {{ $doc->name }}
                    </option>
                @endforeach
            </select>

            {{-- Action Button (Style disamakan dengan tombol Tambah Kegiatan) --}}
            <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white shadow-sm transition-all hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-slate-900 focus:ring-offset-2">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                </svg>
                Sync Sekarang
            </button>
        </form>
</div>


{{-- Filter --}}
<div class="mb-4">
    <form id="filter-form" onsubmit="return false;" class="flex flex-wrap gap-2">
        <input type="text" name="q" id="search-input" value="{{ $search }}" placeholder="Cari judul / nama..."
               class="w-full sm:w-80 rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700 placeholder-slate-400 focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500">
        @if($search)
            <a href="{{ route('admin.learning-outcomes.index') }}" class="inline-flex items-center rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm text-slate-500 hover:bg-slate-50 transition">Reset</a>
        @endif
    </form>
</div>

{{-- Stacked Panels --}}
<div class="flex flex-col gap-6">

    {{-- Penelitian --}}
    <div class="rounded-xl border border-slate-200 bg-white overflow-hidden">
        <div class="border-b border-slate-100 bg-slate-50 px-5 py-3">
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Penelitian</p>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100 text-sm">
                <thead class="bg-white">
                    <tr>
                        <th class="px-4 py-2.5 text-center text-xs font-semibold text-slate-500 w-12">No</th>
                        <th class="px-4 py-2.5 text-left text-xs font-semibold text-slate-500 w-24">Kode</th>
                        <th class="px-4 py-2.5 text-left text-xs font-semibold text-slate-500">Deskripsi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100" id="table-research">
                    @forelse($researches as $r)
                    <tr class="hover:bg-slate-50 js-row-research" data-title="{{ strtolower($r->title) }}" data-researcher="{{ strtolower($r->researcher_name) }}">
                        <td class="px-4 py-2.5 text-center text-slate-400 text-xs js-row-number"></td>
                        <td class="px-4 py-2.5 text-slate-400 text-xs">{{ $r->code }}</td>
                        <td class="px-4 py-2.5 text-slate-800">{{ $r->description }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-4 py-10 text-center text-slate-400 text-sm">Belum ada data capaian pembelajaran lulusan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div id="pag-research" class="px-5 py-3 border-t border-slate-100 flex justify-end flex-wrap gap-2"></div>
    </div>

    {{-- Pengabdian --}}
    <div class="rounded-xl border border-slate-200 bg-white overflow-hidden">
        <div class="border-b border-slate-100 bg-slate-50 px-5 py-3">
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Pengabdian</p>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100 text-sm">
                <thead class="bg-white">
                    <tr>
                        <th class="px-4 py-2.5 text-center text-xs font-semibold text-slate-500 w-12">No</th>
                        <th class="px-4 py-2.5 text-left text-xs font-semibold text-slate-500 w-24">Tahun</th>
                        <th class="px-4 py-2.5 text-left text-xs font-semibold text-slate-500">Judul</th>
                        <th class="px-4 py-2.5 text-left text-xs font-semibold text-slate-500 w-1/4">Lokasi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100" id="table-community">
                    @forelse($communityServices as $cs)
                    <tr class="hover:bg-slate-50 js-row-community" data-title="{{ strtolower($cs->title) }}" data-location="{{ strtolower($cs->location) }}">
                        <td class="px-4 py-2.5 text-center text-slate-400 text-xs js-row-number"></td>
                        <td class="px-4 py-2.5 text-slate-400 text-xs">{{ $cs->year }}</td>
                        <td class="px-4 py-2.5 text-slate-800">{{ $cs->title }}</td>
                        <td class="px-4 py-2.5 text-slate-500 text-xs">{{ $cs->location }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-4 py-10 text-center text-slate-400 text-sm">Belum ada data pengabdian.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div id="pag-community" class="px-5 py-3 border-t border-slate-100 flex justify-end flex-wrap gap-2"></div>
    </div>

</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('search-input');

    function initTable(pagId, rowClass) {
        const rows = Array.from(document.querySelectorAll(rowClass));
        const pagContainer = document.getElementById(pagId);
        let currentPage = 1;
        const perPage = 8;

        function update() {
            const query = searchInput.value.toLowerCase();
            const filtered = rows.filter(r => r.textContent.toLowerCase().includes(query));

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

            renderPag(pagContainer, totalPages, currentPage, (p) => {
                currentPage = p;
                update();
            });
        }

        searchInput.addEventListener('input', () => {
            currentPage = 1;
            update();
        });

        // PENTING: render pertama kali saat halaman dibuka
        update();
    }

    function renderPag(container, total, current, callback) {
        if (total <= 1) { container.innerHTML = ''; return; }
        container.innerHTML = `<div class="flex gap-2">
            ${Array.from({length: total}, (_, i) => i + 1).map(p => `
                <button type="button" class="px-3 py-1 border ${p === current ? 'bg-slate-600 text-white' : 'bg-white'} rounded" data-p="${p}">${p}</button>
            `).join('')}
        </div>`;

        container.querySelectorAll('button').forEach(btn => {
            btn.onclick = (e) => {
                e.preventDefault();
                callback(parseInt(btn.dataset.p));
            };
        });
    }

    initTable('pag-research', '.js-row-research');
    initTable('pag-community', '.js-row-community');
});
</script>
@endpush

@endsection