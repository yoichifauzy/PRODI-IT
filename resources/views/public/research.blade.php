@extends('layouts.public')

@section('title', __('public.research.page_title'))

@section('content')
    @include('public.partials.page-hero', [
        'title' => __('public.research.hero_title'),
        'subtitle' => __('public.research.hero_subtitle'),
    ])

    <section class="section-wrap public-page-shell">
        <div class="public-panel rounded-2xl border border-[var(--border-soft)] bg-white p-5 shadow-sm">
            <!-- Filter Section -->
            <div class="mb-6 grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                <!-- Year Filter -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Tahun</label>
                    <select id="yearFilter" class="w-full px-3 py-2 bg-white text-slate-700 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                        <option value="">Semua Tahun</option>
                        @foreach ($researchYears as $year)
                            <option value="{{ $year }}" {{ $selectedYear == $year ? 'selected' : '' }}>{{ $year }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Search with Autocomplete -->
                <div class="md:col-span-2 lg:col-span-1 relative">
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Cari</label>
                    <div class="relative">
                        <input type="text" id="searchInput"
                            placeholder="Cari judul, peneliti..."
                            value="{{ $search }}"
                            class="w-full px-3 py-2 bg-white text-slate-700 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                            autocomplete="off" />

                        <!-- Autocomplete Dropdown -->
                        <div id="suggestionsList" class="absolute top-full left-0 right-0 bg-white border border-slate-300 rounded-lg mt-1 shadow-lg z-50 hidden max-h-64 overflow-y-auto">
                            <!-- Suggestions will be populated by JS -->
                        </div>
                    </div>
                </div>
            </div>

            <!-- Data Table -->
            <div class="overflow-x-auto">
                <table class="curriculum-table min-w-full text-sm">
                    <thead class="curriculum-table-head text-left">
                        <tr>
                            <th class="px-4 py-3 w-1 whitespace-nowrap text-center">No</th>
                            <th class="px-4 py-3 w-1 whitespace-nowrap text-center">{{ __('public.research.table_year') }}</th>
                            <th class="px-4 py-3">{{ __('public.research.table_title') }}</th>
                            <th class="px-4 py-3 w-1/3 min-w-[200px]">{{ __('public.research.table_author') }}</th>
                        </tr>
                    </thead>
                    <tbody id="researchTbody">
                        @forelse ($researches as $index => $research)
                            <tr
                                class="border-t border-slate-100 hover:bg-slate-50/50 transition-colors"
                                data-research-row
                                data-year="{{ $research->year }}"
                                data-title="{{ $research->title }}"
                                data-researcher="{{ $research->researcher_name }}"
                                data-abstract="{{ $research->abstract ?? '' }}"
                            >
                                <td class="px-4 py-3 text-center text-slate-400" data-research-no>{{ $index + 1 }}</td>
                                <td class="px-4 py-3 text-center font-medium">{{ $research->year }}</td>
                                <td class="px-4 py-3">
                                    <div class="font-semibold text-slate-700">
                                        {{ $research->title }}
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-slate-600 italic">{{ $research->researcher_name }}</td>
                            </tr>
                        @empty
                            <tr class="border-t border-slate-100" data-research-empty>
                                <td class="px-4 py-3 text-center text-slate-500" colspan="4">{{ __('public.research.empty') }}</td>
                            </tr>
                        @endforelse
                        @if ($researches->isNotEmpty())
                            <tr class="border-t border-slate-100 hidden" data-research-empty>
                                <td class="px-4 py-3 text-center text-slate-500" colspan="4">{{ __('public.research.empty') }}</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if ($researches->isNotEmpty())
            <div id="researchPagination" class="mt-4 flex items-center justify-center gap-1"></div>
            @endif
        </div>
    </section>
@endsection

@push('scripts')
<script>
(function() {
    const PER_PAGE = 10;
    let currentPage = 1;

    const searchInput = document.getElementById('searchInput');
    const suggestionsList = document.getElementById('suggestionsList');
    const yearFilter = document.getElementById('yearFilter');
    const tbody = document.getElementById('researchTbody');
    const paginationEl = document.getElementById('researchPagination');
    const emptyRows = document.querySelectorAll('[data-research-empty]');
    let debounceTimer;

    // Build data array from server-rendered rows
    const allData = [];
    document.querySelectorAll('[data-research-row]').forEach(function(row, idx) {
        allData.push({
            year: row.getAttribute('data-year') || '',
            title: row.getAttribute('data-title') || '',
            researcher: row.getAttribute('data-researcher') || '',
            abstract: row.getAttribute('data-abstract') || ''
        });
    });

    // Remove server-rendered rows, we'll render via JS
    document.querySelectorAll('[data-research-row]').forEach(function(row) { row.remove(); });

    function getFilteredData() {
        const year = yearFilter.value;
        const search = searchInput.value.trim().toLowerCase();

        return allData.filter(function(item) {
            const matchesYear = !year || item.year === year;
            const matchesSearch = !search || (item.title.toLowerCase() + ' ' + item.researcher.toLowerCase() + ' ' + item.abstract.toLowerCase()).includes(search);
            return matchesYear && matchesSearch;
        });
    }

    function renderTable() {
        const filtered = getFilteredData();
        const totalPages = Math.max(1, Math.ceil(filtered.length / PER_PAGE));
        if (currentPage > totalPages) currentPage = totalPages;

        const start = (currentPage - 1) * PER_PAGE;
        const pageItems = filtered.slice(start, start + PER_PAGE);

        let html = '';
        if (pageItems.length === 0) {
            html = '<tr class="border-t border-slate-100" data-research-empty><td class="px-4 py-3 text-center text-slate-500" colspan="4">{{ __("public.research.empty") }}</td></tr>';
        } else {
            pageItems.forEach(function(item, idx) {
                const no = start + idx + 1;
                html += '<tr class="border-t border-slate-100 hover:bg-slate-50/50 transition-colors" data-research-row>'
                    + '<td class="px-4 py-3 text-center text-slate-400">' + no + '</td>'
                    + '<td class="px-4 py-3 text-center font-medium">' + item.year + '</td>'
                    + '<td class="px-4 py-3"><div class="font-semibold text-slate-700">' + item.title + '</div></td>'
                    + '<td class="px-4 py-3 text-slate-600 italic">' + item.researcher + '</td>'
                    + '</tr>';
            });
        }
        tbody.innerHTML = html;
        renderPagination(filtered.length, totalPages);
    }

    function renderPagination(total, totalPages) {
        if (!paginationEl) return;
        if (totalPages <= 1) { paginationEl.innerHTML = ''; return; }

        let html = '';
        // Prev button
        html += '<button data-page="prev" class="px-3 py-1.5 rounded-lg text-sm font-medium ' +
            (currentPage === 1 ? 'text-slate-300 cursor-not-allowed' : 'text-slate-600 hover:bg-slate-100') +
            '" ' + (currentPage === 1 ? 'disabled' : '') + '>&laquo;</button>';

        // Page numbers
        var startPage = Math.max(1, currentPage - 2);
        var endPage = Math.min(totalPages, startPage + 4);
        if (endPage - startPage < 4) startPage = Math.max(1, endPage - 4);

        for (var i = startPage; i <= endPage; i++) {
            html += '<button data-page="' + i + '" class="px-3 py-1.5 rounded-lg text-sm font-medium ' +
                (i === currentPage ? 'bg-orange-600 text-white' : 'text-slate-600 hover:bg-slate-100') +
                '">' + i + '</button>';
        }

        // Next button
        html += '<button data-page="next" class="px-3 py-1.5 rounded-lg text-sm font-medium ' +
            (currentPage === totalPages ? 'text-slate-300 cursor-not-allowed' : 'text-slate-600 hover:bg-slate-100') +
            '" ' + (currentPage === totalPages ? 'disabled' : '') + '>&raquo;</button>';

        // Info text
        html += '<span class="ml-3 text-sm text-slate-500">Baris ' + ((currentPage - 1) * PER_PAGE + 1) + '&ndash;' + Math.min(currentPage * PER_PAGE, total) + ' dari ' + total + '</span>';

        paginationEl.innerHTML = html;
    }

    function goToPage(page) {
        var filtered = getFilteredData();
        var totalPages = Math.max(1, Math.ceil(filtered.length / PER_PAGE));
        if (page === 'prev') page = Math.max(1, currentPage - 1);
        else if (page === 'next') page = Math.min(totalPages, currentPage + 1);
        else page = parseInt(page, 10);
        if (isNaN(page) || page < 1 || page > totalPages) return;
        currentPage = page;
        renderTable();
        // Scroll table into view
        var tableEl = document.querySelector('.curriculum-table');
        if (tableEl) tableEl.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    // Pagination click delegation
    if (paginationEl) {
        paginationEl.addEventListener('click', function(e) {
            var btn = e.target.closest('[data-page]');
            if (!btn || btn.disabled) return;
            goToPage(btn.dataset.page);
        });
    }

    // Search input with autocomplete
    searchInput.addEventListener('input', function() {
        clearTimeout(debounceTimer);
        const query = this.value.trim();

        if (query.length < 2) {
            suggestionsList.classList.add('hidden');
            currentPage = 1;
            renderTable();
            updateUrl();
            return;
        }

        debounceTimer = setTimeout(() => {
            fetch(`{{ route('public.research.suggestions') }}?q=${encodeURIComponent(query)}`)
                .then(res => res.json())
                .then(data => {
                    if (data.length === 0) {
                        suggestionsList.innerHTML = '<div class="px-4 py-2 text-sm text-slate-500">Tidak ada hasil</div>';
                        suggestionsList.classList.remove('hidden');
                        return;
                    }

                    suggestionsList.innerHTML = data.map((item, idx) => `
                        <div class="px-4 py-2 hover:bg-orange-50 cursor-pointer border-b border-slate-100 last:border-b-0 suggestion-item"
                            data-title="${item.title}" data-researcher="${item.researcher}" data-year="${item.year}">
                            <div class="font-semibold text-slate-700 text-sm">${item.title}</div>
                            <div class="text-xs text-slate-500">${item.researcher} (${item.year})</div>
                        </div>
                    `).join('');

                    suggestionsList.classList.remove('hidden');

                    // Add click handlers to suggestions
                    document.querySelectorAll('.suggestion-item').forEach(item => {
                        item.addEventListener('click', function() {
                            searchInput.value = this.getAttribute('data-title');
                            suggestionsList.classList.add('hidden');
                            currentPage = 1;
                            renderTable();
                            updateUrl();
                        });
                    });
                })
                .catch(err => console.error(err));
        }, 300);

        currentPage = 1;
        renderTable();
        updateUrl();
    });

    // Year filter change
    yearFilter.addEventListener('change', function() {
        currentPage = 1;
        renderTable();
        updateUrl();
    });

    searchInput.addEventListener('keydown', (event) => {
        if (event.key === 'Enter') {
            event.preventDefault();
            currentPage = 1;
            renderTable();
            updateUrl();
            suggestionsList.classList.add('hidden');
        }
    });

    // Click outside to close suggestions
    document.addEventListener('click', function(e) {
        if (!e.target.closest('#searchInput') && !e.target.closest('#suggestionsList')) {
            suggestionsList.classList.add('hidden');
        }
    });

    function updateUrl() {
        const year = yearFilter.value;
        const search = searchInput.value.trim();
        const params = new URLSearchParams();
        if (year) params.append('year', year);
        if (search) params.append('q', search);
        const url = `{{ route('public.research') }}${params.toString() ? '?' + params.toString() : ''}`;
        window.history.replaceState({}, '', url);
    }

    // Restore search value if exists
    if (searchInput.value.trim().length > 0) {
        searchInput.focus();
    }

    // Initial render
    renderTable();
})();
</script>
@endpush
