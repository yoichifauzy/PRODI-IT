@extends('layouts.admin')

@section('title', 'Kurikulum')

@section('content')
{{-- Header & Page Actions --}}
<div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
    {{-- Left Side: Title & Description --}}
    <div>
        <h1 class="text-2xl font-bold text-slate-900">Kurikulum</h1>
        <p class="mt-1 text-sm text-slate-500">Sync dari sheet kurikulum.</p>
    </div>

    {{-- Right Side: Actions (Select Dropdown & Sync Button) --}}
    <div>
        <form action="{{ route('admin.courses.sync') }}" method="POST" class="flex flex-col gap-3 sm:flex-row sm:items-center">
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
</div>

{{-- Data Table Section --}}
<div class="rounded-xl border border-slate-200 bg-white overflow-hidden">
    {{-- Filter Table (Local JS Search) --}}
    <div class="flex items-center justify-between border-b border-slate-100 bg-slate-50 px-5 py-4">
        <div class="relative w-full sm:w-80">
            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                <svg class="h-4 w-4 text-slate-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                </svg>
            </div>
            <input type="text" id="search-input" placeholder="Cari kode / nama mata kuliah..."
                   class="w-full rounded-xl border border-slate-200 bg-white py-2 pl-9 pr-4 text-sm text-slate-700 placeholder-slate-400 focus:border-slate-900 focus:outline-none focus:ring-1 focus:ring-slate-900">
        </div>
    </div>
{{-- Data table --}}
<div class="rounded-xl border border-slate-200 bg-white overflow-hidden">
    <div class="flex items-center justify-between border-b border-slate-100 bg-slate-50 px-5 py-3">
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-100 text-sm">
            <thead class="bg-white">
                <tr>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-slate-500 uppercase">Semester</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-slate-500 uppercase">Jurusan</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-slate-500 uppercase">Kode</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-slate-500 uppercase">Mata Kuliah</th>
                    <th class="px-4 py-2 text-center text-xs font-semibold text-slate-500 uppercase">SKS T</th>
                    <th class="px-4 py-2 text-center text-xs font-semibold text-slate-500 uppercase">SKS P</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100" id="table-courses">
                @forelse($courses as $course)
                <tr class="hover:bg-slate-50 js-row-course"
                    data-code="{{ strtolower($course->code) }}"
                    data-name="{{ strtolower($course->name) }}">
                    <td class="px-4 py-2 text-slate-600 font-medium">{{ $course->semester }}</td>
                    <td class="px-4 py-2 text-slate-400 text-xs">{{ $course->major_selection ?? '—' }}</td>
                    <td class="px-4 py-2 font-mono text-slate-700 text-xs">{{ $course->code }}</td>
                    <td class="px-4 py-2 text-slate-800">{{ $course->name }}</td>
                    <td class="px-4 py-2 text-center text-slate-500">{{ $course->credits_theory }}</td>
                    <td class="px-4 py-2 text-center text-slate-500">{{ $course->credits_practice }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-4 py-10 text-center text-slate-400 text-sm">
                        Belum ada data. Sinkronisasi dokumen sheet.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div id="pag-courses" class="px-5 py-3 border-t border-slate-100 flex justify-end flex-wrap gap-2"></div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('search-input');
    const countLabel = document.getElementById('course-count');

    function initTable(pagId, rowClass) {
        const rows = Array.from(document.querySelectorAll(rowClass));
        const pagContainer = document.getElementById(pagId);
        let currentPage = 1;
        const perPage = 12;

        function update() {
            const query = searchInput.value.toLowerCase();
            const filtered = rows.filter(r => r.textContent.toLowerCase().includes(query));

            if (countLabel) countLabel.textContent = filtered.length;

            rows.forEach(r => r.classList.add('hidden'));

            const totalPages = Math.max(1, Math.ceil(filtered.length / perPage));
            if (currentPage > totalPages) currentPage = totalPages;

            const start = (currentPage - 1) * perPage;
            const visible = filtered.slice(start, start + perPage);

            visible.forEach(r => r.classList.remove('hidden'));

            renderPag(pagContainer, totalPages, currentPage, (p) => {
                currentPage = p;
                update();
            });
        }

        searchInput.addEventListener('input', () => {
            currentPage = 1;
            update();
        });

        update();
    }

    function renderPag(container, total, current, callback) {
        if (total <= 1) { container.innerHTML = ''; return; }

        const buttons = Array.from({length: total}, (_, i) => i + 1).map(p => `
            <button type="button" class="px-3 py-1 border ${p === current ? 'bg-slate-600 text-white border-slate-600' : 'bg-white'} rounded" data-p="${p}">${p}</button>
        `).join('');

        container.innerHTML = buttons;

        container.querySelectorAll('button').forEach(btn => {
            btn.onclick = (e) => {
                e.preventDefault();
                callback(parseInt(btn.dataset.p));
            };
        });
    }

    initTable('pag-courses', '.js-row-course');
});
</script>
@endpush
@endsection