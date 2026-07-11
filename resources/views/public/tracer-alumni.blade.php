@extends('layouts.public')

@section('title', __('public.tracer.page_title'))

@section('content')
    @include('public.partials.page-hero', [
        'title' => __('public.tracer.hero_title'),
        'subtitle' => __('public.tracer.hero_subtitle'),
    ])

    <section class="section-wrap public-page-shell">
        <!-- <header class="public-page-intro">
            <h2 class="public-page-title">{{ __('public.tracer.intro_title') }}</h2>
            <p class="public-page-copy">{{ __('public.tracer.intro_copy') }}</p>
        </header> -->

        <div class="tracer-hero-panel public-panel rounded-2xl border border-[var(--border-soft)] bg-white p-5 shadow-sm"
            data-tracer-panel
            data-selected-year="{{ $selectedYear }}"
            data-base-url="{{ route('public.tracer-alumni') }}"
            data-meta-selected-id="{{ trans('public.tracer.meta_selected_year', [], 'id') }}"
            data-meta-selected-en="{{ trans('public.tracer.meta_selected_year', [], 'en') }}"
            data-meta-total-id="{{ trans('public.tracer.meta_total_data', [], 'id') }}"
            data-meta-total-en="{{ trans('public.tracer.meta_total_data', [], 'en') }}"
            data-all-id="{{ trans('public.tracer.all_graduates', [], 'id') }}"
            data-all-en="{{ trans('public.tracer.all_graduates', [], 'en') }}"
            data-hero-label-id="{{ trans('public.tracer.hero_label', ['year' => ':year'], 'id') }}"
            data-hero-label-en="{{ trans('public.tracer.hero_label', ['year' => ':year'], 'en') }}"

            data-summary-all-id="{{ trans('public.tracer.summary_all', [], 'id') }}"
            data-summary-all-en="{{ trans('public.tracer.summary_all', [], 'en') }}"
        >
            <style>
                @keyframes marquee {
                    0% { transform: translateX(0); }
                    100% { transform: translateX(-50%); }
                }
                .animate-marquee {
                    animation: marquee 40s linear infinite;
                    display: flex;
                    width: max-content;
                }
                .animate-marquee:hover {
                    animation-play-state: paused;
                }
                .marquee-item {
                    height: 100%;
                    aspect-ratio: 16/9;
                    object-fit: cover;
                }
            </style>
            <div class="tracer-hero-image-wrap relative overflow-hidden rounded-xl h-80 isolate bg-slate-900 flex">
                @if(isset($alumniBanners) && $alumniBanners->isNotEmpty())
                    <div class="animate-marquee h-full">
                        {{-- First Set --}}
                        @foreach ($alumniBanners as $banner)
                            <img src="{{ asset('storage/' . $banner->image_path) }}" class="marquee-item" alt="Banner Alumni">
                        @endforeach
                        {{-- Second Set for seamless looping --}}
                        @foreach ($alumniBanners as $banner)
                            <img src="{{ asset('storage/' . $banner->image_path) }}" class="marquee-item" alt="Banner Alumni">
                        @endforeach
                    </div>
                @else
                    <!-- Fallback ke gambar default jika database kosong/belum diupload -->
                    <img src="{{ asset('image/galeri/image3.jpeg') }}" 
                        alt="{{ __('public.tracer.hero_title') }}" 
                        class="h-full w-full object-cover" />
                @endif
            </div>
        </div>

        {{-- AREA FILTER: Ditambahin mt-8 biar gak nempel panel atas --}}
        <div class="tracer-filter-wrap mt-6 mb-6 flex flex-col sm:flex-row items-center justify-center gap-4">
            <div class="relative w-full sm:w-96">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                    <svg class="h-4 w-4 text-slate-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <input type="text" id="tracer-search" placeholder="Cari nama, NIM, perusahaan..." 
                       class="w-full rounded-lg border border-slate-200 bg-white py-2.5 pl-10 pr-4 text-sm text-slate-700 focus:border-orange-500 focus:outline-none focus:ring-1 focus:ring-orange-500 shadow-sm transition-shadow">
            </div>
            
            <div class="w-full sm:w-auto">
                <select id="tracer-year-select" class="w-full sm:w-48 rounded-lg border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-700 focus:border-orange-500 focus:outline-none focus:ring-1 focus:ring-orange-500 shadow-sm cursor-pointer">
                    <option value="">{{ __('public.tracer.all_graduates') }}</option>
                    @foreach ($graduationYears as $year)
                        <option value="{{ $year }}" {{ (string)$selectedYear === (string)$year ? 'selected' : '' }}>
                            {{ __('public.tracer.filter_year', ['year' => $year]) }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div id="tracer-table-wrap" class="overflow-x-auto rounded-xl border border-[var(--border-soft)] bg-white shadow-sm">
            <table class="tracer-data-table min-w-full text-sm">
                <thead class="tracer-table-head text-left bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="px-4 py-3">No</th>
                        <th class="px-4 py-3">{{ __('public.tracer.table_graduation') }}</th>
                        <th class="px-4 py-3">{{ __('public.tracer.table_nim') }}</th>
                        <th class="px-4 py-3">{{ __('public.tracer.table_name') }}</th>
                        <th class="px-4 py-3">{{ __('public.tracer.table_company') }}</th>
                        <th class="px-4 py-3">{{ __('public.tracer.table_department') }}</th>
                        <th class="px-4 py-3 text-center">{{ __('public.tracer.table_relevance') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($rows as $row)
                        <tr class="border-t border-slate-100 hidden"
                            data-tracer-row
                            data-year="{{ $row->graduation_year }}"
                        >
                            <td class="px-4 py-3 text-slate-400 js-row-number"></td>
                            <td class="px-4 py-3 text-slate-500">{{ $row->graduation_year ?: '-' }}</td>
                            <td class="px-4 py-3 font-mono text-xs">{{ $row->nim }}</td>
                            <td class="px-4 py-3 font-medium text-slate-800">{{ $row->name }}</td>
                            <td class="px-4 py-3 font-medium text-slate-800">{{ $row->company_name }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $row->department ?: '-' }}</td>
                            <td class="px-4 py-3 text-slate-500 text-center">{{ $row->relevance ?: '-' }}</td>
                        </tr>
                    @empty
                        {{-- Kosong dari database --}}
                    @endforelse

                    <tr id="js-tracer-empty-row" class="hidden">
                        <td colspan="7" class="px-4 py-12 text-center text-[var(--text-soft)] bg-white">
                            Tidak ditemukan data yang cocok.
                        </td>
                    </tr>
                </tbody>
            </table>
            
            {{-- AREA PAGINATION CLIENT-SIDE --}}
            <div id="tracer-pagination" class="flex items-center justify-between border-t border-slate-100 bg-slate-50/50 px-4 py-3 sm:px-6">
                </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const tableWrap = document.getElementById('tracer-table-wrap');
            const tracerPanel = document.querySelector('[data-tracer-panel]');
            const tracerRows = Array.from(document.querySelectorAll('[data-tracer-row]'));
            const emptyRow = document.getElementById('js-tracer-empty-row');
            const pagContainer = document.getElementById('tracer-pagination');
            const heroLabel = document.querySelector('[data-tracer-hero-label]');
            const summary = document.querySelector('[data-tracer-summary]');
            
            const searchInput = document.getElementById('tracer-search');
            const yearSelect = document.getElementById('tracer-year-select');

            if (!tableWrap || !tracerPanel || !searchInput || !yearSelect) return;

            // Konfigurasi Pagination
            const itemsPerPage = 15;
            let currentPage = 1;
            let filteredRows = [];

            const getCurrentLang = () => (localStorage.getItem('site-lang') === 'en' ? 'en' : 'id');
            const getPanelText = (name) => tracerPanel.getAttribute(`data-${name}-${getCurrentLang()}`)
                || tracerPanel.getAttribute(`data-${name}-id`)
                || '';

            const renderSummary = (year) => {
                if (!summary) return;
                if (!year) {
                    summary.textContent = getPanelText('summary-all');
                    return;
                }
                const template = getPanelText('summary-selected');
                const before = template.split(':year')[0] || '';
                const after = template.split(':year').slice(1).join(':year') || '';
                
                summary.textContent = '';
                summary.append(document.createTextNode(before));
                const strong = document.createElement('strong');
                strong.textContent = year;
                summary.append(strong);
                summary.append(document.createTextNode(after));
            };

            const renderPagination = (totalItems, totalPages) => {
                if (!pagContainer) return;

                if (totalItems === 0 || totalPages <= 1) {
                    pagContainer.innerHTML = '';
                    return;
                }

                let startP = Math.max(1, currentPage - 2);
                let endP = Math.min(totalPages, startP + 4);
                if (endP - startP < 4) startP = Math.max(1, endP - 4);

                let pagesHtml = '';
                for (let p = startP; p <= endP; p++) {
                    const isActive = p === currentPage;
                    pagesHtml += `
                        <button type="button" class="js-page-btn relative inline-flex items-center px-4 py-2 text-sm font-semibold transition-colors ${isActive ? 'z-10 bg-orange-600 text-white focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-orange-600' : 'text-slate-700 ring-1 ring-inset ring-slate-300 hover:bg-slate-100 focus:z-20 focus:outline-offset-0'}" data-page="${p}">
                            ${p}
                        </button>
                    `;
                }

                pagContainer.innerHTML = `
                    <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
                        <div>
                            <p class="text-sm text-slate-500">
                                Menampilkan <span class="font-medium text-slate-800">${(currentPage - 1) * itemsPerPage + 1}</span> 
                                hingga <span class="font-medium text-slate-800">${Math.min(currentPage * itemsPerPage, totalItems)}</span> 
                                dari <span class="font-medium text-slate-800">${totalItems}</span> data
                            </p>
                        </div>
                        <div>
                            <nav class="isolate inline-flex -space-x-px rounded-md shadow-sm" aria-label="Pagination">
                                <button type="button" class="js-page-btn relative inline-flex items-center rounded-l-md px-2 py-2 text-slate-400 ring-1 ring-inset ring-slate-300 hover:bg-slate-100 disabled:opacity-50 disabled:cursor-not-allowed" data-page="${currentPage - 1}" ${currentPage === 1 ? 'disabled' : ''}>
                                    <span class="sr-only">Previous</span>
                                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M12.79 5.23a.75.75 0 01-.02 1.06L8.832 10l3.938 3.71a.75.75 0 11-1.04 1.08l-4.5-4.25a.75.75 0 010-1.08l4.5-4.25a.75.75 0 011.06.02z" clip-rule="evenodd" /></svg>
                                </button>
                                ${pagesHtml}
                                <button type="button" class="js-page-btn relative inline-flex items-center rounded-r-md px-2 py-2 text-slate-400 ring-1 ring-inset ring-slate-300 hover:bg-slate-100 disabled:opacity-50 disabled:cursor-not-allowed" data-page="${currentPage + 1}" ${currentPage === totalPages ? 'disabled' : ''}>
                                    <span class="sr-only">Next</span>
                                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd" /></svg>
                                </button>
                            </nav>
                        </div>
                    </div>
                    <div class="flex flex-1 justify-between sm:hidden">
                        <button type="button" class="js-page-btn relative inline-flex items-center rounded-md border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 disabled:opacity-50" data-page="${currentPage - 1}" ${currentPage === 1 ? 'disabled' : ''}>Previous</button>
                        <button type="button" class="js-page-btn relative ml-3 inline-flex items-center rounded-md border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 disabled:opacity-50" data-page="${currentPage + 1}" ${currentPage === totalPages ? 'disabled' : ''}>Next</button>
                    </div>
                `;
            };

            const applyTracerFilter = (year, searchQuery = '', page = 1, shouldPushState = true) => {
                const selectedYear = year ? String(year) : '';
                const query = searchQuery.toLowerCase().trim();
                currentPage = page;
                filteredRows = [];

                // 1. Simpan baris yang lolos filter ke array temporary
                tracerRows.forEach((row) => {
                    const rowYear = String(row.getAttribute('data-year') || '');
                    const rowText = row.textContent.toLowerCase(); 

                    const matchesYear = selectedYear === '' || rowYear === selectedYear;
                    const matchesSearch = query === '' || rowText.includes(query);

                    if (matchesYear && matchesSearch) {
                        filteredRows.push(row);
                    } else {
                        row.classList.add('hidden'); // Sembunyikan yang tidak lolos
                    }
                });

                // 2. Hitung batasan Pagination
                const totalItems = filteredRows.length;
                const totalPages = Math.ceil(totalItems / itemsPerPage) || 1;

                if (currentPage > totalPages) currentPage = totalPages;
                if (currentPage < 1) currentPage = 1;

                const startIndex = (currentPage - 1) * itemsPerPage;
                const endIndex = startIndex + itemsPerPage;

                // 3. Tampilkan HANYA 15 baris yang sesuai dengan currentPage
                filteredRows.forEach((row, index) => {
                    if (index >= startIndex && index < endIndex) {
                        row.classList.remove('hidden');
                        const numberCell = row.querySelector('.js-row-number');
                        if (numberCell) numberCell.textContent = index + 1; // Update nomor urut
                    } else {
                        row.classList.add('hidden');
                    }
                });

                if (emptyRow) emptyRow.classList.toggle('hidden', totalItems > 0);

                const allLabel = getPanelText('all');
                const selectedLabel = selectedYear || allLabel;
                if (heroLabel) heroLabel.textContent = getPanelText('hero-label').replace(':year', selectedLabel);

                renderSummary(selectedYear);
                tracerPanel.setAttribute('data-selected-year', selectedYear);
                renderPagination(totalItems, totalPages);

                if (shouldPushState) {
                    const url = new URL(tracerPanel.getAttribute('data-base-url') || window.location.href, window.location.origin);
                    if (selectedYear) url.searchParams.set('year', selectedYear);
                    else url.searchParams.delete('year');
                    window.history.pushState({ tracerYear: selectedYear }, '', url);
                }
            };

            // Listener Ketik & Ganti Filter (Reset page kembali ke 1)
            searchInput.addEventListener('input', (e) => applyTracerFilter(yearSelect.value, e.target.value, 1, false));
            yearSelect.addEventListener('change', (e) => applyTracerFilter(e.target.value, searchInput.value, 1, true));

            // Listener Klik Tombol Pagination
            document.addEventListener('click', (e) => {
                const pageBtn = e.target.closest('.js-page-btn');
                if (pageBtn && !pageBtn.disabled) {
                    const newPage = parseInt(pageBtn.dataset.page);
                    applyTracerFilter(yearSelect.value, searchInput.value, newPage, false);
                    tableWrap.scrollIntoView({ behavior: 'smooth', block: 'start' }); // Auto scroll ke atas tabel
                }
            });

            window.addEventListener('popstate', () => {
                const year = new URLSearchParams(window.location.search).get('year') || '';
                yearSelect.value = year;
                applyTracerFilter(yearSelect.value, searchInput.value, 1, false);
            });

            // Init awal
            const initialYear = new URLSearchParams(window.location.search).get('year') || '';
            if (initialYear) yearSelect.value = initialYear;
            applyTracerFilter(yearSelect.value, searchInput.value, 1, false);
        });
    </script>
@endpush