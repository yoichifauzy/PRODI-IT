@extends('layouts.admin')

@section('title', 'Penelitian & Pengabdian')

@section('content')
    <div class="mb-4 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Penelitian & Pengabdian</h1>
            <p class="text-sm text-slate-600">Kelola data penelitian dan pengabdian melalui spreadsheet.</p>
        </div>
    </div>

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

                                <form method="POST" action="{{ route('admin.research-community.sync') }}" id="syncForm">
                                    @csrf
                                    <button type="button" class="rounded-md bg-slate-700 px-3 py-2 text-xs font-semibold text-white" id="syncButton">Sync Now</button>
                                </form>

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
        <div class="border-b border-slate-200 px-4 py-3">
            <h2 class="text-lg font-semibold text-slate-900">Data Penelitian</h2>
            <p class="text-xs text-slate-500">{{ $researches->count() }} data</p>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 text-left text-slate-600">
                    <tr>
                        <th class="px-4 py-3 w-10">#</th>
                        <th class="px-4 py-3 w-20">Tahun</th>
                        <th class="px-4 py-3">Judul Penelitian</th>
                        <th class="px-4 py-3 w-56">Peneliti</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($researches as $i => $research)
                        <tr class="border-t border-slate-100 hover:bg-slate-50">
                            <td class="px-4 py-3 text-slate-500">{{ $loop->iteration }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-block rounded-full bg-indigo-100 px-2.5 py-0.5 text-xs font-medium text-indigo-800">{{ $research->year }}</span>
                            </td>
                            <td class="px-4 py-3 font-medium text-slate-900 max-w-md">{{ $research->title }}</td>
                            <td class="px-4 py-3 text-slate-700 whitespace-nowrap">{{ $research->researcher_name }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-8 text-center text-slate-400">Belum ada data penelitian. Lakukan sync atau upload terlebih dahulu.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pengabdian Masyarakat Data --}}
    <div id="section-community" class="rounded-xl border border-slate-200 bg-white">
        <div class="border-b border-slate-200 px-4 py-3">
            <h2 class="text-lg font-semibold text-slate-900">Data Pengabdian Masyarakat</h2>
            <p class="text-xs text-slate-500">{{ $communityServices->count() }} data</p>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 text-left text-slate-600">
                    <tr>
                        <th class="px-4 py-3 w-10">#</th>
                        <th class="px-4 py-3">Tahun</th>
                        <th class="px-4 py-3">Nama Program</th>
                        <th class="px-4 py-3">Lokasi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($communityServices as $i => $service)
                        <tr class="border-t border-slate-100 hover:bg-slate-50">
                            <td class="px-4 py-3 text-slate-500">{{ $loop->iteration }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-block rounded-full bg-indigo-100 px-2.5 py-0.5 text-xs font-medium text-indigo-800">{{ $service->activity_date ? $service->activity_date->format('Y') : '-' }}</span>
                            </td>
                            <td class="px-4 py-3 font-medium text-slate-900">{{ $service->title }}</td>
                            <td class="px-4 py-3 text-slate-700">{{ $service->location ?: '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-8 text-center text-slate-400">Belum ada data pengabdian. Lakukan sync atau upload terlebih dahulu.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
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

        openBtn.addEventListener('click', function(){ modal.style.display = 'flex'; });
        cancel.addEventListener('click', function(){ modal.style.display = 'none'; });

        linkForm.addEventListener('submit', function(e){
            const ok = confirm('Simpan link spreadsheet baru?');
            if (!ok) { e.preventDefault(); }
        });

        uploadButton.addEventListener('click', function(){ uploadInput.click(); });
        uploadInput.addEventListener('change', function(){
            if (uploadInput.files.length > 0) {
                const ok = confirm('Upload ini akan menghapus semua data penelitian dan pengabdian lalu menggantinya dari file. Lanjutkan?');
                if (ok) { uploadForm.submit(); } else { uploadInput.value = ''; }
            }
        });

        syncButton.addEventListener('click', function(){
            const ok = confirm('Sync Now akan menghapus semua data penelitian dan pengabdian lalu mengambil dari spreadsheet. Lanjutkan?');
            if (ok) { syncForm.submit(); }
        });

        // Filter buttons
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
    })();
</script>
@endpush
