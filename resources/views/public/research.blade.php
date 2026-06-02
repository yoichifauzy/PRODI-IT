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
                    <select id="yearFilter" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
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
                            class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
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
                    <tbody>
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
        </div>
    </section>
@endsection

@push('scripts')
<script>
(function() {
    const searchInput = document.getElementById('searchInput');
    const suggestionsList = document.getElementById('suggestionsList');
    const yearFilter = document.getElementById('yearFilter');
    const researchRows = Array.from(document.querySelectorAll('[data-research-row]'));
    const emptyRow = document.querySelector('[data-research-empty]');
    let debounceTimer;

    // Search input with autocomplete
    searchInput.addEventListener('input', function() {
        clearTimeout(debounceTimer);
        const query = this.value.trim();

        if (query.length < 2) {
            suggestionsList.classList.add('hidden');
            applyFilters(false);
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
                        <div class="px-4 py-2 hover:bg-indigo-50 cursor-pointer border-b border-slate-100 last:border-b-0 suggestion-item"
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
                            applyFilters(true);
                        });
                    });
                })
                .catch(err => console.error(err));
        }, 300);

        applyFilters(false);
    });

    // Year filter change
    yearFilter.addEventListener('change', () => applyFilters(true));

    searchInput.addEventListener('keydown', (event) => {
        if (event.key === 'Enter') {
            event.preventDefault();
            applyFilters(true);
            suggestionsList.classList.add('hidden');
        }
    });

    // Click outside to close suggestions
    document.addEventListener('click', function(e) {
        if (!e.target.closest('#searchInput') && !e.target.closest('#suggestionsList')) {
            suggestionsList.classList.add('hidden');
        }
    });

    function applyFilters(updateUrl) {
        const year = yearFilter.value;
        const search = searchInput.value.trim().toLowerCase();

        let visibleIndex = 0;
        researchRows.forEach((row) => {
            const rowYear = row.getAttribute('data-year') || '';
            const title = (row.getAttribute('data-title') || '').toLowerCase();
            const researcher = (row.getAttribute('data-researcher') || '').toLowerCase();
            const abstractText = (row.getAttribute('data-abstract') || '').toLowerCase();

            const matchesYear = !year || rowYear === year;
            const matchesSearch = !search || `${title} ${researcher} ${abstractText}`.includes(search);
            const isVisible = matchesYear && matchesSearch;

            row.classList.toggle('hidden', !isVisible);

            if (isVisible) {
                visibleIndex += 1;
                const noCell = row.querySelector('[data-research-no]');
                if (noCell) {
                    noCell.textContent = String(visibleIndex);
                }
            }
        });

        if (emptyRow) {
            emptyRow.classList.toggle('hidden', visibleIndex !== 0);
        }

        if (updateUrl) {
            const params = new URLSearchParams();
            if (year) params.append('year', year);
            if (search) params.append('q', search);

            const url = `{{ route('public.research') }}${params.toString() ? '?' + params.toString() : ''}`;
            window.history.replaceState({}, '', url);
        }
    }

    // Restore search value if exists (untuk page reload)
    if (searchInput.value.trim().length > 0) {
        searchInput.focus();
    }

    applyFilters(false);
})();
</script>
@endpush
