@extends('layouts.admin')

@section('title', 'Penelitian & Pengabdian')

@section('content')
    <div class="mb-4 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Penelitian & Pengabdian</h1>
            <p class="text-sm text-slate-600">Kelola data penelitian dan pengabdian melalui spreadsheet.</p>
        </div>
    </div>

    @if ($isDraftMode ?? false)
        <div class="mb-6 rounded-lg bg-yellow-50 border border-yellow-200 p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 5zm0 9a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-yellow-800">Mode Preview (Data Draft)</h3>
                    <div class="mt-2 text-sm text-yellow-700">
                        <p>Anda sedang melihat data draft hasil sinkronisasi dari spreadsheet. Data ini <strong>belum</strong> dipublikasikan ke halaman publik.</p>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Spreadsheet Link Section --}}
    <div class="rounded-xl border border-slate-200 bg-white mb-6">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 text-left text-slate-600">
                    <tr>
                        <th class="px-4 py-3">Links Spreadsheet</th>
                        <th class="px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="border-t border-slate-100">
                        <td class="px-4 py-3">
                            @if(!empty($sheetUrl))
                                <a href="{{ $sheetUrl }}" target="_blank" class="text-indigo-700 underline">{{ $sheetUrl }}</a>
                            @else
                                <span class="text-slate-500">Belum diatur</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex flex-wrap items-center gap-3">
                                <button class="rounded-md bg-emerald-600 px-3 py-2 text-xs font-semibold text-white" id="openEditLink">Edit</button>

                                <form method="POST" action="{{ route('admin.research-community.upload') }}" enctype="multipart/form-data" id="uploadForm">
                                    @csrf
                                    <input type="file" name="file" id="uploadInput" class="hidden" accept=".csv,.xlsx,.xls" />
                                    <button type="button" class="rounded-md bg-indigo-600 px-3 py-2 text-xs font-semibold text-white" id="uploadButton">Upload</button>
                                </form>

                                @if (!($isDraftMode ?? false))
                                    <form method="POST" action="{{ route('admin.research-community.sync') }}" id="syncForm">
                                        @csrf
                                        <button type="button" class="rounded-md bg-slate-700 px-3 py-2 text-xs font-semibold text-white" id="syncButton">Sync Now</button>
                                    </form>
                                @else
                                    <form method="POST" action="{{ route('admin.research-community.sync.validate') }}" id="syncValidateForm">
                                        @csrf
                                        <button type="button" class="rounded-md bg-emerald-600 px-3 py-2 text-xs font-semibold text-white" id="syncValidateButton">Sync Validate</button>
                                    </form>

                                    <form method="POST" action="{{ route('admin.research-community.sync.discard') }}" id="syncDiscardForm">
                                        @csrf
                                        <button type="button" class="rounded-md bg-red-600 px-3 py-2 text-xs font-semibold text-white" id="syncDiscardButton">Batalkan Draft</button>
                                    </form>
                                @endif

                                <a href="{{ route('admin.research-community.download') }}" class="rounded-md bg-slate-500 px-3 py-2 text-xs font-semibold text-white">Download</a>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    {{-- Filter Buttons --}}
    <div class="mb-6 flex flex-wrap justify-center gap-3">
        <button type="button" class="filter-btn rounded-lg px-4 py-2 text-sm font-semibold transition-all bg-indigo-600 text-white shadow-md" data-filter="all">Semua</button>
        <button type="button" class="filter-btn rounded-lg px-4 py-2 text-sm font-semibold transition-all bg-slate-100 text-slate-700 hover:bg-indigo-600 hover:text-white" data-filter="research">Penelitian</button>
        <button type="button" class="filter-btn rounded-lg px-4 py-2 text-sm font-semibold transition-all bg-slate-100 text-slate-700 hover:bg-indigo-600 hover:text-white" data-filter="community">Pengabdian Masyarakat</button>
    </div>

    {{-- Penelitian Data --}}
    <div id="section-research" class="rounded-xl border border-slate-200 bg-white mb-6">
        <div class="border-b border-slate-200 px-4 py-3 flex items-center justify-between flex-wrap gap-3">
            <div>
                <h2 class="text-lg font-semibold text-slate-900">Data Penelitian</h2>
                <p class="text-xs text-slate-500"><span id="research-total">{{ $researches->count() }}</span> data</p>
            </div>
            <div class="flex items-center gap-2">
                <input type="text" id="research-search" placeholder="Cari judul/peneliti..." class="text-xs border border-slate-300 rounded-md px-3 py-1.5 w-48 focus:outline-none focus:border-indigo-400 focus:ring-1 focus:ring-indigo-400" />
                <select id="research-year-filter" class="text-xs border border-slate-300 rounded-md px-2 py-1.5 focus:outline-none focus:border-indigo-400 focus:ring-1 focus:ring-indigo-400">
                    <option value="">Semua Tahun</option>
                </select>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 text-left text-slate-600">
                    <tr>
                        <th class="px-4 py-3 w-10">#</th>
                        <th class="px-4 py-3 w-20">Tahun</th>
                        <th class="px-4 py-3">Judul Penelitian</th>
                        <th class="px-4 py-3 w-56">Peneliti</th>
                        <th class="px-4 py-3 text-center w-28">Status</th>
                    </tr>
                </thead>
                <tbody id="research-tbody"></tbody>
            </table>
        </div>
        <div id="research-pagination-bottom" class="border-t border-slate-200 px-4 py-3 flex justify-center items-center gap-1 text-xs"></div>
    </div>

    {{-- Pengabdian Masyarakat Data --}}
    <div id="section-community" class="rounded-xl border border-slate-200 bg-white">
        <div class="border-b border-slate-200 px-4 py-3 flex items-center justify-between flex-wrap gap-3">
            <div>
                <h2 class="text-lg font-semibold text-slate-900">Data Pengabdian Masyarakat</h2>
                <p class="text-xs text-slate-500"><span id="community-total">{{ $communityServices->count() }}</span> data</p>
            </div>
            <div class="flex items-center gap-2">
                <input type="text" id="community-search" placeholder="Cari program/lokasi..." class="text-xs border border-slate-300 rounded-md px-3 py-1.5 w-48 focus:outline-none focus:border-indigo-400 focus:ring-1 focus:ring-indigo-400" />
                <select id="community-year-filter" class="text-xs border border-slate-300 rounded-md px-2 py-1.5 focus:outline-none focus:border-indigo-400 focus:ring-1 focus:ring-indigo-400">
                    <option value="">Semua Tahun</option>
                </select>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 text-left text-slate-600">
                    <tr>
                        <th class="px-4 py-3 w-10">#</th>
                        <th class="px-4 py-3">Tahun</th>
                        <th class="px-4 py-3">Nama Program</th>
                        <th class="px-4 py-3">Lokasi</th>
                        <th class="px-4 py-3 text-center w-28">Status</th>
                    </tr>
                </thead>
                <tbody id="community-tbody"></tbody>
            </table>
        </div>
        <div id="community-pagination-bottom" class="border-t border-slate-200 px-4 py-3 flex justify-center items-center gap-1 text-xs"></div>
    </div>
@endsection

@push('scripts')
<script>
(function(){
    // ========== Modal & Sync/Upload handlers ==========
    const openBtn = document.getElementById('openEditLink');
    const body = document.body;
    const uploadButton = document.getElementById('uploadButton');
    const uploadInput = document.getElementById('uploadInput');
    const uploadForm = document.getElementById('uploadForm');

    const modalHtml = `
    <div id="linkModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.4);z-index:60;align-items:center;justify-content:center;">
        <div style="background:#fff;border-radius:8px;max-width:720px;margin:48px auto;padding:20px;">
            <h3 style="margin-top:0">Link Google Sheets (public)</h3>
            <form method="post" action="{{ route('admin.research-community.link.update') }}" id="linkForm">
                @csrf
                <div style="margin-bottom:12px">
                    <label style="display:block;margin-bottom:6px">URL Spreadsheet</label>
                    <input name="sheet_url" type="url" value="{{ $sheetUrl }}" placeholder="https://docs.google.com/spreadsheets/d/..." style="width:100%;padding:8px;border:1px solid #ddd;border-radius:4px" required />
                </div>
                <div style="display:flex;gap:10px;justify-content:flex-end">
                    <button type="button" id="cancelLink" style="background:#e5e7eb;border-radius:6px;padding:8px 12px">Batal</button>
                    <button type="submit" style="background:#10b981;color:#fff;border-radius:6px;padding:8px 12px">Simpan</button>
                </div>
            </form>
        </div>
    </div>`;

    body.insertAdjacentHTML('beforeend', modalHtml);

    const modal = document.getElementById('linkModal');
    const cancel = document.getElementById('cancelLink');
    const linkForm = document.getElementById('linkForm');
    const syncButton = document.getElementById('syncButton');
    const syncForm = document.getElementById('syncForm');
    const syncValidateButton = document.getElementById('syncValidateButton');
    const syncValidateForm = document.getElementById('syncValidateForm');
    const syncDiscardButton = document.getElementById('syncDiscardButton');
    const syncDiscardForm = document.getElementById('syncDiscardForm');

    openBtn.addEventListener('click', function(){ modal.style.display = 'flex'; });
    cancel.addEventListener('click', function(){ modal.style.display = 'none'; });

    linkForm.addEventListener('submit', function(e){
        const ok = confirm('Simpan link spreadsheet baru?');
        if (!ok) { e.preventDefault(); }
    });

    if (uploadButton) {
        uploadButton.addEventListener('click', function(){ uploadInput.click(); });
    }
    if (uploadInput) {
        uploadInput.addEventListener('change', function(){
            if (uploadInput.files.length > 0) {
                const ok = confirm('Upload ini akan mengambil data dari file dan menyimpannya ke Draft. Lanjutkan?');
                if (ok) { uploadForm.submit(); } else { uploadInput.value = ''; }
            }
        });
    }

    if (syncButton) {
        syncButton.addEventListener('click', function(){
            const ok = confirm('Sync Now akan mengambil data dari spreadsheet dan menyimpannya ke Draft untuk direview. Lanjutkan?');
            if (ok) { syncForm.submit(); }
        });
    }

    if (syncValidateButton) {
        syncValidateButton.addEventListener('click', function(){
            const ok = confirm('Sync Validate akan mempublikasikan data draft ini ke publik. Data lama akan dihapus. Lanjutkan?');
            if (ok) { syncValidateForm.submit(); }
        });
    }

    if (syncDiscardButton) {
        syncDiscardButton.addEventListener('click', function(){
            const ok = confirm('Batalkan Draft akan menghapus semua preview dan kembali ke data publik. Lanjutkan?');
            if (ok) { syncDiscardForm.submit(); }
        });
    }

    // ========== Filter buttons ==========
    const filterBtns = document.querySelectorAll('.filter-btn');
    const sectionResearch = document.getElementById('section-research');
    const sectionCommunity = document.getElementById('section-community');

    filterBtns.forEach(function(btn){
        btn.addEventListener('click', function(){
            filterBtns.forEach(function(b){
                b.classList.remove('bg-indigo-600','text-white','shadow-md');
                b.classList.add('bg-slate-100','text-slate-700');
            });
            btn.classList.remove('bg-slate-100','text-slate-700');
            btn.classList.add('bg-indigo-600','text-white','shadow-md');

            var filter = btn.getAttribute('data-filter');
            if (filter === 'all') {
                sectionResearch.style.display = '';
                sectionCommunity.style.display = '';
            } else if (filter === 'research') {
                sectionResearch.style.display = '';
                sectionCommunity.style.display = 'none';
            } else if (filter === 'community') {
                sectionResearch.style.display = 'none';
                sectionCommunity.style.display = '';
            }
        });
    });

    // ========== Client-side Pagination with Search & Year Filter ==========
    const PER_PAGE = 5;

    // Year → color mapping (cycles through a palette)
    var yearColors = {};
    var colorPalette = [
        'bg-indigo-100 text-indigo-800',
        'bg-emerald-100 text-emerald-800',
        'bg-amber-100 text-amber-800',
        'bg-rose-100 text-rose-800',
        'bg-cyan-100 text-cyan-800',
        'bg-violet-100 text-violet-800',
        'bg-orange-100 text-orange-800',
        'bg-teal-100 text-teal-800',
        'bg-pink-100 text-pink-800',
        'bg-sky-100 text-sky-800'
    ];
    var colorIdx = 0;
    function getYearColor(year) {
        if (!yearColors[year]) {
            yearColors[year] = colorPalette[colorIdx % colorPalette.length];
            colorIdx++;
        }
        return yearColors[year];
    }

    // Research data
    var researchData = {!! json_encode($researches->map(function($r){ return ['year' => $r->year, 'title' => $r->title, 'researcher_name' => $r->researcher_name]; })) !!};
    var communityData = {!! json_encode($communityServices->map(function($c){ return ['year' => $c->activity_date ? $c->activity_date->format('Y') : '-', 'title' => $c->title, 'location' => $c->location ?: '-']; })) !!};

    // Populate year filter dropdowns
    function populateYearFilters() {
        // Research years
        var researchYears = [...new Set(researchData.map(function(r){ return r.year; }))].sort(function(a,b){ return b - a; });
        var rSelect = document.getElementById('research-year-filter');
        researchYears.forEach(function(y){
            var opt = document.createElement('option');
            opt.value = y;
            opt.textContent = y;
            rSelect.appendChild(opt);
        });

        // Community years
        var communityYears = [...new Set(communityData.map(function(c){ return c.year; }))].sort(function(a,b){ return b - a; });
        var cSelect = document.getElementById('community-year-filter');
        communityYears.forEach(function(y){
            var opt = document.createElement('option');
            opt.value = y;
            opt.textContent = y;
            cSelect.appendChild(opt);
        });
    }
    populateYearFilters();

    function filterData(rawData, searchText, yearFilter, searchFields) {
        return rawData.filter(function(item){
            // Year filter
            if (yearFilter && String(item.year) !== String(yearFilter)) return false;
            // Search filter
            if (searchText) {
                var lower = searchText.toLowerCase();
                var match = false;
                for (var f = 0; f < searchFields.length; f++) {
                    var val = item[searchFields[f]] || '';
                    if (val.toLowerCase().indexOf(lower) !== -1) { match = true; break; }
                }
                if (!match) return false;
            }
            return true;
        });
    }

    function renderTable(tbodyId, data, page, totalSpanId) {
        var tbody = document.getElementById(tbodyId);
        var start = (page - 1) * PER_PAGE;
        var pageItems = data.slice(start, start + PER_PAGE);
        var html = '';

        var statusHtml = {{ ($isDraftMode ?? false) ? 'true' : 'false' }} 
            ? '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Draft</span>'
            : '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">Published</span>';

        if (pageItems.length === 0) {
            var colspan = 5;
            var emptyMsg = tbodyId === 'research-tbody'
                ? 'Belum ada data penelitian. Lakukan sync atau upload terlebih dahulu.'
                : 'Belum ada data pengabdian. Lakukan sync atau upload terlebih dahulu.';
            html = '<tr><td colspan="' + colspan + '" class="px-4 py-8 text-center text-slate-400">' + emptyMsg + '</td></tr>';
        } else {
            for (var i = 0; i < pageItems.length; i++) {
                var item = pageItems[i];
                var num = start + i + 1;
                var yearClass = getYearColor(item.year);
                if (tbodyId === 'research-tbody') {
                    html += '<tr class="border-t border-slate-100 hover:bg-slate-50">' +
                        '<td class="px-4 py-3 text-slate-500">' + num + '</td>' +
                        '<td class="px-4 py-3"><span class="inline-block rounded-full px-2.5 py-0.5 text-xs font-medium ' + yearClass + '">' + item.year + '</span></td>' +
                        '<td class="px-4 py-3 font-medium text-slate-900 max-w-md">' + item.title + '</td>' +
                        '<td class="px-4 py-3 text-slate-700 whitespace-nowrap">' + item.researcher_name + '</td>' +
                        '<td class="px-4 py-3 text-center">' + statusHtml + '</td>' +
                        '</tr>';
                } else {
                    html += '<tr class="border-t border-slate-100 hover:bg-slate-50">' +
                        '<td class="px-4 py-3 text-slate-500">' + num + '</td>' +
                        '<td class="px-4 py-3"><span class="inline-block rounded-full px-2.5 py-0.5 text-xs font-medium ' + yearClass + '">' + item.year + '</span></td>' +
                        '<td class="px-4 py-3 font-medium text-slate-900">' + item.title + '</td>' +
                        '<td class="px-4 py-3 text-slate-700">' + item.location + '</td>' +
                        '<td class="px-4 py-3 text-center">' + statusHtml + '</td>' +
                        '</tr>';
                }
            }
        }
        tbody.innerHTML = html;
        document.getElementById(totalSpanId).textContent = data.length;
    }

    function renderPagination(containerId, data, currentPage) {
        var container = document.getElementById(containerId);
        var totalPages = Math.ceil(data.length / PER_PAGE);
        if (totalPages <= 1) { container.innerHTML = ''; return; }

        var html = '';
        html += '<button class="px-2 py-1 rounded border border-slate-300 ' + (currentPage === 1 ? 'text-slate-300 cursor-not-allowed' : 'text-slate-600 hover:bg-slate-100') + '"' + (currentPage === 1 ? ' disabled' : '') + ' data-page="' + (currentPage - 1) + '">&laquo;</button>';

        for (var p = 1; p <= totalPages; p++) {
            if (p === currentPage) {
                html += '<span class="px-3 py-1 rounded bg-indigo-600 text-white font-semibold">' + p + '</span>';
            } else {
                html += '<button class="px-3 py-1 rounded border border-slate-300 text-slate-600 hover:bg-slate-100" data-page="' + p + '">' + p + '</button>';
            }
        }

        html += '<button class="px-2 py-1 rounded border border-slate-300 ' + (currentPage === totalPages ? 'text-slate-300 cursor-not-allowed' : 'text-slate-600 hover:bg-slate-100') + '"' + (currentPage === totalPages ? ' disabled' : '') + ' data-page="' + (currentPage + 1) + '">&raquo;</button>';

        container.innerHTML = html;
    }

    // State
    var researchPage = 1;
    var communityPage = 1;

    function goToPage(type, page) {
        if (type === 'research') {
            var searchText = document.getElementById('research-search').value;
            var yearFilter = document.getElementById('research-year-filter').value;
            var filtered = filterData(researchData, searchText, yearFilter, ['title', 'researcher_name']);
            var totalPages = Math.ceil(filtered.length / PER_PAGE) || 1;
            if (page < 1) page = 1;
            if (page > totalPages) page = totalPages;
            researchPage = page;
            renderTable('research-tbody', filtered, researchPage, 'research-total');
            renderPagination('research-pagination-bottom', filtered, researchPage);
        } else {
            var searchText = document.getElementById('community-search').value;
            var yearFilter = document.getElementById('community-year-filter').value;
            var filtered = filterData(communityData, searchText, yearFilter, ['title', 'location']);
            var totalPages = Math.ceil(filtered.length / PER_PAGE) || 1;
            if (page < 1) page = 1;
            if (page > totalPages) page = totalPages;
            communityPage = page;
            renderTable('community-tbody', filtered, communityPage, 'community-total');
            renderPagination('community-pagination-bottom', filtered, communityPage);
        }
    }

    // Search input handlers
    document.getElementById('research-search').addEventListener('input', function(){ goToPage('research', 1); });
    document.getElementById('research-year-filter').addEventListener('change', function(){ goToPage('research', 1); });
    document.getElementById('community-search').addEventListener('input', function(){ goToPage('community', 1); });
    document.getElementById('community-year-filter').addEventListener('change', function(){ goToPage('community', 1); });

    // Delegate click on pagination containers
    document.getElementById('research-pagination-bottom').addEventListener('click', function(e){
        var btn = e.target.closest('button');
        if (btn && btn.dataset.page) { goToPage('research', parseInt(btn.dataset.page)); }
    });
    document.getElementById('community-pagination-bottom').addEventListener('click', function(e){
        var btn = e.target.closest('button');
        if (btn && btn.dataset.page) { goToPage('community', parseInt(btn.dataset.page)); }
    });

    // Initial render
    goToPage('research', 1);
    goToPage('community', 1);
})();
</script>
@endpush
