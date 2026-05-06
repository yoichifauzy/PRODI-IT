@extends('layouts.admin')

@section('title', 'Penelitian & Pengabdian')

@section('content')
    <div class="mb-4 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Penelitian & Pengabdian</h1>
            <p class="text-sm text-slate-600">Kelola data penelitian dan pengabdian melalui spreadsheet.</p>
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
    })();
</script>
@endpush
