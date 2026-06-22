@extends('layouts.admin')

@section('title', 'Kelola Kurikulum')

@section('content')
    <div class="mb-4 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Kurikulum</h1>
            <p class="text-sm text-slate-600">Kelola data kurikulum dan matakuliah melalui spreadsheet.</p>
        </div>
    </div>

    <div class="rounded-xl border border-slate-200 bg-white">
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

                                <form method="POST" action="{{ route('admin.curricula.upload') }}" enctype="multipart/form-data" id="uploadForm">
                                    @csrf
                                    <input type="file" name="file" id="uploadInput" class="hidden" accept=".csv,.xlsx,.xls" />
                                    <button type="button" class="rounded-md bg-indigo-600 px-3 py-2 text-xs font-semibold text-white" id="uploadButton">Upload</button>
                                </form>

                                <form method="POST" action="{{ route('admin.curricula.sync') }}" id="syncForm">
                                    @csrf
                                    <button type="button" class="rounded-md bg-slate-700 px-3 py-2 text-xs font-semibold text-white" id="syncButton">Sync Now</button>
                                </form>

                                <a href="{{ route('admin.curricula.download') }}" class="rounded-md bg-slate-500 px-3 py-2 text-xs font-semibold text-white">Download</a>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    {{-- Curriculum & Course Data --}}
    @if ($allCurricula->isNotEmpty())
        <div class="mt-6 flex flex-wrap justify-center gap-3">
            @foreach ($curricula as $curriculum)
                <button type="button"
                        class="js-admin-curriculum-filter rounded-lg px-4 py-2 text-sm font-semibold transition-all {{ $loop->first ? 'bg-indigo-600 text-white shadow-md' : 'bg-slate-100 text-slate-700 hover:bg-indigo-600 hover:text-white' }}"
                        data-curriculum-name="{{ $curriculum->name }}">
                    {{ $curriculum->name }}
                </button>
            @endforeach
        </div>

        @foreach ($allCurricula as $curriculum)
            @php
                $panelMajorOptions = $allCurricula->where('name', $curriculum->name);
            @endphp
            <div class="js-admin-curriculum-panel mt-6 rounded-xl border border-slate-200 bg-white {{ $loop->first ? '' : 'hidden' }}"
                 data-curriculum-id="{{ $curriculum->id }}"
                 data-curriculum-name="{{ $curriculum->name }}">
                <div class="flex flex-wrap items-end justify-between gap-3 px-5 pt-5 pb-3">
                    <div>
                        <h2 class="text-xl font-bold text-slate-900">{{ $curriculum->name }}</h2>
                        @if($curriculum->description)
                            <p class="mt-1 text-sm text-slate-500">{{ $curriculum->description }}</p>
                        @endif
                    </div>
                    <span class="rounded-full bg-indigo-50 px-3 py-1 text-xs font-semibold text-indigo-700">
                        {{ $curriculum->courses->count() }} Matakuliah
                    </span>
                </div>

                @if($panelMajorOptions->count() > 1)
                    <div class="flex flex-wrap gap-2 border-b border-slate-100 px-5 pb-4">
                        @foreach($panelMajorOptions as $option)
                            <button type="button"
                                    class="js-admin-curriculum-major rounded-lg px-4 py-2 text-sm font-medium transition-all hover:bg-indigo-600 hover:text-white {{ $loop->first ? 'bg-indigo-600 text-white shadow-md' : 'bg-slate-50 text-slate-600 border border-slate-200' }}"
                                    data-curriculum-id="{{ $option->id }}">
                                {{ $option->major_selection ?: 'Umum' }}
                            </button>
                        @endforeach
                    </div>
                @endif

                <div class="flex flex-wrap items-center gap-3 border-b border-slate-100 px-5 pb-4">
                    <input type="text" placeholder="Cari matakuliah…" class="js-admin-course-search rounded-md border border-slate-300 px-3 py-1.5 text-sm" data-curriculum-id="{{ $curriculum->id }}" />
                    <span class="ml-auto text-xs text-slate-500"><span class="js-admin-course-total" data-curriculum-id="{{ $curriculum->id }}">{{ $curriculum->courses->count() }}</span> matakuliah</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-slate-50 text-left text-slate-600">
                            <tr>
                                <th class="px-4 py-3">No</th>
                                <th class="px-4 py-3">Kode</th>
                                <th class="px-4 py-3">Nama Matakuliah</th>
                                <th class="px-4 py-3 text-center">SKS Teori</th>
                                <th class="px-4 py-3 text-center">SKS Praktek</th>
                            </tr>
                        </thead>
                        <tbody class="js-admin-course-tbody" data-curriculum-id="{{ $curriculum->id }}">
                            @forelse ($curriculum->courses as $iteration => $course)
                                <tr class="border-t border-slate-100 hover:bg-slate-50">
                                    <td class="px-4 py-3">{{ $iteration + 1 }}</td>
                                    <td class="px-4 py-3 font-mono text-xs">{{ $course->code }}</td>
                                    <td class="px-4 py-3">{{ $course->name }}</td>
                                    <td class="px-4 py-3 text-center">{{ $course->credits_theory }}</td>
                                    <td class="px-4 py-3 text-center">{{ $course->credits_practice }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-8 text-center text-slate-400">Belum ada matakuliah.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="js-admin-course-pagination flex flex-wrap items-center justify-center gap-1 px-5 py-3" data-curriculum-id="{{ $curriculum->id }}"></div>
            </div>
        @endforeach
    @else
        <div class="mt-6 rounded-xl border border-dashed border-slate-300 bg-white p-8 text-center text-slate-400">
            Belum ada data kurikulum. Silakan upload atau sync dari spreadsheet.
        </div>
    @endif
@endsection

@push('scripts')
<script>
    (function(){
        const openBtn = document.getElementById('openEditLink');
        const body = document.body;
        const uploadButton = document.getElementById('uploadButton');
        const uploadInput = document.getElementById('uploadInput');
        const uploadForm = document.getElementById('uploadForm');

        const modalHtml = `
        <div id="linkModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.4);z-index:60;align-items:center;justify-content:center;">
            <div style="background:#fff;border-radius:8px;max-width:720px;margin:48px auto;padding:20px;">
                <h3 style="margin-top:0">Link Google Sheets (public)</h3>
                <form method="post" action="{{ route('admin.curricula.link.update') }}" id="linkForm">
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

        openBtn.addEventListener('click', function(){ modal.style.display = 'flex'; });
        cancel.addEventListener('click', function(){ modal.style.display = 'none'; });

        linkForm.addEventListener('submit', function(e){
            const ok = confirm('Simpan link spreadsheet baru?');
            if (!ok) { e.preventDefault(); }
        });

        uploadButton.addEventListener('click', function(){ uploadInput.click(); });
        uploadInput.addEventListener('change', function(){
            if (uploadInput.files.length > 0) {
                const ok = confirm('Upload ini akan menghapus semua data kurikulum dan matakuliah lalu menggantinya dari file. Lanjutkan?');
                if (ok) { uploadForm.submit(); } else { uploadInput.value = ''; }
            }
        });

        syncButton.addEventListener('click', function(){
            const ok = confirm('Sync Now akan menghapus semua data kurikulum dan matakuliah lalu mengambil dari spreadsheet. Lanjutkan?');
            if (ok) { syncForm.submit(); }
        });

        // --- Client-side Pagination for Course Tables ---
        const PER_PAGE = 5;

        // Build course data per curriculum panel from server JSON
        var courseDataMap = {!! json_encode($allCurricula->mapWithKeys(function($cur){ return [(string)$cur->id => $cur->courses->map(function($c){ return ['code' => $c->code, 'name' => $c->name, 'credits_theory' => $c->credits_theory, 'credits_practice' => $c->credits_practice]; })]; })) !!};
        var coursePageMap = {};
        Object.keys(courseDataMap).forEach(function(cid) {
            coursePageMap[cid] = 1;
        });

        function filterCourses(data, searchText) {
            if (!searchText) return data;
            var lower = searchText.toLowerCase();
            return data.filter(function(item) {
                return (item.code && item.code.toLowerCase().indexOf(lower) !== -1) ||
                       (item.name && item.name.toLowerCase().indexOf(lower) !== -1);
            });
        }

        function renderCourseTable(cid) {
            var tbody = document.querySelector('.js-admin-course-tbody[data-curriculum-id="' + cid + '"]');
            var totalSpan = document.querySelector('.js-admin-course-total[data-curriculum-id="' + cid + '"]');
            var searchInput = document.querySelector('.js-admin-course-search[data-curriculum-id="' + cid + '"]');
            var searchText = searchInput ? searchInput.value : '';
            var data = filterCourses(courseDataMap[cid] || [], searchText);
            var page = coursePageMap[cid] || 1;
            var totalPages = Math.ceil(data.length / PER_PAGE) || 1;
            if (page > totalPages) page = totalPages;
            if (page < 1) page = 1;
            coursePageMap[cid] = page;

            var start = (page - 1) * PER_PAGE;
            var pageItems = data.slice(start, start + PER_PAGE);
            var html = '';

            if (pageItems.length === 0) {
                html = '<tr><td colspan="5" class="px-4 py-8 text-center text-slate-400">Belum ada matakuliah.</td></tr>';
            } else {
                for (var i = 0; i < pageItems.length; i++) {
                    var item = pageItems[i];
                    var num = start + i + 1;
                    html += '<tr class="border-t border-slate-100 hover:bg-slate-50">' +
                        '<td class="px-4 py-3">' + num + '</td>' +
                        '<td class="px-4 py-3 font-mono text-xs">' + item.code + '</td>' +
                        '<td class="px-4 py-3">' + item.name + '</td>' +
                        '<td class="px-4 py-3 text-center">' + item.credits_theory + '</td>' +
                        '<td class="px-4 py-3 text-center">' + item.credits_practice + '</td>' +
                        '</tr>';
                }
            }
            tbody.innerHTML = html;
            if (totalSpan) totalSpan.textContent = data.length;

            // Render pagination
            var pagContainer = document.querySelector('.js-admin-course-pagination[data-curriculum-id="' + cid + '"]');
            if (totalPages <= 1) { pagContainer.innerHTML = ''; return; }

            var pagHtml = '';
            pagHtml += '<button class="px-2 py-1 rounded border border-slate-300 ' + (page === 1 ? 'text-slate-300 cursor-not-allowed' : 'text-slate-600 hover:bg-slate-100') + '"' + (page === 1 ? ' disabled' : '') + ' data-page="' + (page - 1) + '">&laquo;</button>';
            for (var p = 1; p <= totalPages; p++) {
                if (p === page) {
                    pagHtml += '<span class="px-3 py-1 rounded bg-indigo-600 text-white font-semibold">' + p + '</span>';
                } else {
                    pagHtml += '<button class="px-3 py-1 rounded border border-slate-300 text-slate-600 hover:bg-slate-100" data-page="' + p + '">' + p + '</button>';
                }
            }
            pagHtml += '<button class="px-2 py-1 rounded border border-slate-300 ' + (page === totalPages ? 'text-slate-300 cursor-not-allowed' : 'text-slate-600 hover:bg-slate-100') + '"' + (page === totalPages ? ' disabled' : '') + ' data-page="' + (page + 1) + '">&raquo;</button>';
            pagContainer.innerHTML = pagHtml;
        }

        // Search input handlers
        document.querySelectorAll('.js-admin-course-search').forEach(function(input) {
            input.addEventListener('input', function() {
                var cid = this.dataset.curriculumId;
                coursePageMap[cid] = 1;
                renderCourseTable(cid);
            });
        });

        // Pagination click delegation
        document.querySelectorAll('.js-admin-course-pagination').forEach(function(container) {
            container.addEventListener('click', function(e) {
                var btn = e.target.closest('button');
                if (btn && btn.dataset.page) {
                    var cid = this.dataset.curriculumId;
                    coursePageMap[cid] = parseInt(btn.dataset.page);
                    renderCourseTable(cid);
                }
            });
        });

        // Initial render for all panels
        Object.keys(courseDataMap).forEach(function(cid) {
            renderCourseTable(cid);
        });

        // --- Curriculum panel switching ---
        const filterBtns = Array.from(document.querySelectorAll('.js-admin-curriculum-filter'));
        const majorBtns = Array.from(document.querySelectorAll('.js-admin-curriculum-major'));
        const panels = Array.from(document.querySelectorAll('.js-admin-curriculum-panel'));

        function setFilterActive(name) {
            filterBtns.forEach(btn => {
                const isActive = btn.dataset.curriculumName === name;
                btn.classList.toggle('bg-indigo-600', isActive);
                btn.classList.toggle('text-white', isActive);
                btn.classList.toggle('shadow-md', isActive);
                btn.classList.toggle('bg-slate-100', !isActive);
                btn.classList.toggle('text-slate-700', !isActive);
            });
        }

        function setMajorActive(curriculumId) {
            majorBtns.forEach(btn => {
                const isActive = btn.dataset.curriculumId === curriculumId;
                btn.classList.toggle('bg-indigo-600', isActive);
                btn.classList.toggle('text-white', isActive);
                btn.classList.toggle('shadow-md', isActive);
                btn.classList.toggle('bg-slate-50', !isActive);
                btn.classList.toggle('text-slate-600', !isActive);
                btn.classList.toggle('border', !isActive);
                btn.classList.toggle('border-slate-200', !isActive);
            });
        }

        function showPanel(curriculumId) {
            const target = panels.find(p => p.dataset.curriculumId === curriculumId);
            if (!target) return;
            panels.forEach(p => p.classList.toggle('hidden', p !== target));
            setFilterActive(target.dataset.curriculumName);
            setMajorActive(curriculumId);
            // Reset search and re-render pagination for the shown panel
            var searchInput = target.querySelector('.js-admin-course-search');
            if (searchInput) searchInput.value = '';
            coursePageMap[curriculumId] = 1;
            renderCourseTable(curriculumId);
        }

        function showGroup(groupName) {
            const groupPanels = panels.filter(p => p.dataset.curriculumName === groupName);
            if (groupPanels.length === 0) return;
            const visible = groupPanels.find(p => !p.classList.contains('hidden')) || groupPanels[0];
            showPanel(visible.dataset.curriculumId);
            // Reset search and re-render for all panels in the group
            groupPanels.forEach(p => {
                var searchInput = p.querySelector('.js-admin-course-search');
                if (searchInput) searchInput.value = '';
                coursePageMap[p.dataset.curriculumId] = 1;
                renderCourseTable(p.dataset.curriculumId);
            });
        }

        filterBtns.forEach(btn => {
            btn.addEventListener('click', () => showGroup(btn.dataset.curriculumName));
        });

        majorBtns.forEach(btn => {
            btn.addEventListener('click', () => showPanel(btn.dataset.curriculumId));
        });
    })();
</script>
@endpush
