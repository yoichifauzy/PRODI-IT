@extends('layouts.public')

@section('title', __('public.community_service.page_title'))

@section('content')
    @include('public.partials.page-hero', [
        'title' => __('public.community_service.hero_title'),
        'subtitle' => __('public.community_service.hero_subtitle'),
    ])

    <section class="section-wrap public-page-shell">

        <div class="public-panel rounded-2xl border border-[var(--border-soft)] bg-white p-5 shadow-sm">
            <div class="overflow-x-auto">
                <table class="curriculum-table min-w-full text-sm">
                    <thead class="curriculum-table-head text-left">
                        <tr>
                            <th class="px-4 py-3 w-1 whitespace-nowrap text-center">No</th>
                            <th class="px-4 py-3 w-1 whitespace-nowrap text-center">{{ __('public.community_service.table_year') }}</th>
                            <th class="px-4 py-3">{{ __('public.community_service.table_title') }}</th>
                            <th class="px-4 py-3 w-1/3 min-w-[200px]">{{ __('public.community_service.table_location') }}</th>
                        </tr>
                    </thead>
                    <tbody id="communityTbody">
                        @forelse ($services as $index => $service)
                            <tr class="border-t border-slate-100 hover:bg-slate-50/50 transition-colors"
                                data-service-row
                                data-year="{{ $service->year }}"
                                data-title="{{ $service->title }}"
                                data-location="{{ $service->location }}">
                                <td class="px-4 py-3 text-center text-slate-400">{{ $index + 1 }}</td>
                                <td class="px-4 py-3 text-center font-medium">
                                    {{ $service->year }}
                                </td>
                                <td class="px-4 py-3 font-semibold text-slate-700">{{ $service->title }}</td>
                                <td class="px-4 py-3 text-slate-600">{{ $service->location }}</td>
                            </tr>
                        @empty
                            <tr class="border-t border-slate-100">
                                <td class="px-4 py-3 text-center text-slate-500" colspan="4">{{ __('public.community_service.empty') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        @if ($services->isNotEmpty())
        <div id="communityPagination" class="mt-4 flex items-center justify-center gap-1"></div>
        @endif
    </section>
@endsection

@push('scripts')
<script>
(function() {
    const PER_PAGE = 10;
    let currentPage = 1;

    const tbody = document.getElementById('communityTbody');
    const paginationEl = document.getElementById('communityPagination');

    const allData = [];
    document.querySelectorAll('[data-service-row]').forEach(function(row) {
        allData.push({
            year: row.getAttribute('data-year') || '',
            title: row.getAttribute('data-title') || '',
            location: row.getAttribute('data-location') || ''
        });
        row.remove();
    });

    function renderTable() {
        const totalPages = Math.max(1, Math.ceil(allData.length / PER_PAGE));
        if (currentPage > totalPages) currentPage = totalPages;

        const start = (currentPage - 1) * PER_PAGE;
        const pageItems = allData.slice(start, start + PER_PAGE);

        let html = '';
        if (pageItems.length === 0) {
            html = '<tr class="border-t border-slate-100"><td class="px-4 py-3 text-center text-slate-500" colspan="4">{{ __("public.community_service.empty") }}</td></tr>';
        } else {
            pageItems.forEach(function(item, idx) {
                const no = start + idx + 1;
                html += '<tr class="border-t border-slate-100 hover:bg-slate-50/50 transition-colors">'
                    + '<td class="px-4 py-3 text-center text-slate-400">' + no + '</td>'
                    + '<td class="px-4 py-3 text-center font-medium">' + item.year + '</td>'
                    + '<td class="px-4 py-3 font-semibold text-slate-700">' + item.title + '</td>'
                    + '<td class="px-4 py-3 text-slate-600">' + item.location + '</td>'
                    + '</tr>';
            });
        }
        tbody.innerHTML = html;
        renderPagination(allData.length, totalPages);
    }

    function renderPagination(total, totalPages) {
        if (!paginationEl) return;
        if (totalPages <= 1) { paginationEl.innerHTML = ''; return; }

        let html = '';
        html += '<button data-page="prev" class="px-3 py-1.5 rounded-lg text-sm font-medium ' +
            (currentPage === 1 ? 'text-slate-300 cursor-not-allowed' : 'text-slate-600 hover:bg-slate-100') +
            '" ' + (currentPage === 1 ? 'disabled' : '') + '>&laquo;</button>';

        var startPage = Math.max(1, currentPage - 2);
        var endPage = Math.min(totalPages, startPage + 4);
        if (endPage - startPage < 4) startPage = Math.max(1, endPage - 4);

        for (var i = startPage; i <= endPage; i++) {
            html += '<button data-page="' + i + '" class="px-3 py-1.5 rounded-lg text-sm font-medium ' +
                (i === currentPage ? 'bg-orange-600 text-white' : 'text-slate-600 hover:bg-slate-100') +
                '">' + i + '</button>';
        }

        html += '<button data-page="next" class="px-3 py-1.5 rounded-lg text-sm font-medium ' +
            (currentPage === totalPages ? 'text-slate-300 cursor-not-allowed' : 'text-slate-600 hover:bg-slate-100') +
            '" ' + (currentPage === totalPages ? 'disabled' : '') + '>&raquo;</button>';

        html += '<span class="ml-3 text-sm text-slate-500">Baris ' + ((currentPage - 1) * PER_PAGE + 1) + '&ndash;' + Math.min(currentPage * PER_PAGE, total) + ' dari ' + total + '</span>';

        paginationEl.innerHTML = html;
    }

    function goToPage(page) {
        const totalPages = Math.max(1, Math.ceil(allData.length / PER_PAGE));
        if (page === 'prev') page = Math.max(1, currentPage - 1);
        else if (page === 'next') page = Math.min(totalPages, currentPage + 1);
        else page = parseInt(page, 10);
        
        if (isNaN(page) || page < 1 || page > totalPages) return;
        
        currentPage = page;
        renderTable();
        
        var tableEl = document.querySelector('.curriculum-table');
        if (tableEl) tableEl.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    if (paginationEl) {
        paginationEl.addEventListener('click', function(e) {
            var btn = e.target.closest('[data-page]');
            if (!btn || btn.disabled) return;
            goToPage(btn.dataset.page);
        });
    }

    renderTable();
})();
</script>
@endpush
