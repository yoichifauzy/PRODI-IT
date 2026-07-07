@extends('layouts.admin')

@section('title', 'Kelola Dokumen')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-2xl font-bold text-slate-800">Kelola Dokumen</h1>
        <p class="text-sm text-slate-500">Pusat Integrasi Data dan Arsip Dokumen.</p>
    </div>
    <button onclick="openModal('addModal')" class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-700 transition">
        + Tambah Dokumen
    </button>
</div>


<div class="rounded-xl border border-slate-200 bg-white overflow-hidden shadow-sm">
    <table class="min-w-full divide-y divide-slate-200 text-sm">
        <thead class="bg-slate-50">
            <tr>
                <th class="px-5 py-3 text-left font-semibold text-slate-700">Nama Dokumen</th>
                <th class="px-5 py-3 text-left font-semibold text-slate-700">Link URL</th>
                <th class="px-5 py-3 text-left font-semibold text-slate-700 w-48">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @forelse($documents as $doc)
            <tr class="hover:bg-slate-50 transition">
                <td class="px-5 py-3">
                    <span class="font-mono text-slate-800">{{ $doc->name }}</span>
                </td>
                <td class="px-5 py-3">
                    <a href="{{ $doc->url }}" target="_blank" class="text-slate-800 hover:underline max-w-md truncate block" title="{{ $doc->url }}">
                        {{ $doc->url }}
                    </a>
                </td>
                <td class="px-5 py-3">
                    <div class="flex items-center gap-3">
                        <button onclick="editDoc({{ $doc->id }}, '{{ $doc->name }}', '{{ $doc->url }}')" class="text-slate-500 hover:text-slate-600 transition">Edit</button>
                        <form action="{{ route('admin.documents.destroy', $doc) }}" method="POST" onsubmit="return confirm('Hapus dokumen ini?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-rose-500 hover:text-rose-700 transition">Hapus</button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="3" class="px-5 py-10 text-center text-slate-500">Belum ada dokumen.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Add Modal --}}
<div id="addModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/50 backdrop-blur-sm">
    <div class="w-full max-w-md rounded-2xl bg-white shadow-xl overflow-hidden">
        <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4">
            <h3 class="text-lg font-bold text-slate-800">Tambah Dokumen</h3>
            <button onclick="closeModal('addModal')" class="text-slate-400 hover:text-slate-600">✕</button>
        </div>
        <form action="{{ route('admin.documents.store') }}" method="POST" class="p-6">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Nama Dokumen</label>
                    <input type="text" name="name" required class="w-full rounded-lg border border-slate-200 px-4 py-2 text-sm focus:border-slate-800 focus:outline-none focus:ring-1 focus:ring-slate-800" placeholder="Misal: kurikulum">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">URL (Link Google Sheets)</label>
                    <input type="url" name="url" required class="w-full rounded-lg border border-slate-200 px-4 py-2 text-sm focus:border-slate-800 focus:outline-none focus:ring-1 focus:ring-slate-800" placeholder="https://docs.google.com/spreadsheets/d/...">
                </div>
            </div>
            <div class="mt-6 flex justify-end gap-3">
                <button type="button" onclick="closeModal('addModal')" class="rounded-lg px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-100 transition">Batal</button>
                <button type="submit" class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-700 transition">Simpan</button>
            </div>
        </form>
    </div>
</div>

{{-- Edit Modal --}}
<div id="editModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/50 backdrop-blur-sm">
    <div class="w-full max-w-md rounded-2xl bg-white shadow-xl overflow-hidden">
        <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4">
            <h3 class="text-lg font-bold text-slate-800">Edit Dokumen</h3>
            <button onclick="closeModal('editModal')" class="text-slate-400 hover:text-slate-600">✕</button>
        </div>
        <form id="editForm" method="POST" class="p-6">
            @csrf @method('PUT')
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Nama Dokumen</label>
                    <input type="text" name="name" id="editName" required class="w-full rounded-lg border border-slate-200 px-4 py-2 text-sm focus:border-slate-800 focus:outline-none focus:ring-1 focus:ring-slate-800">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">URL (Link Google Sheets)</label>
                    <input type="url" name="url" id="editUrl" required class="w-full rounded-lg border border-slate-200 px-4 py-2 text-sm focus:border-slate-800 focus:outline-none focus:ring-1 focus:ring-slate-800">
                </div>
            </div>
            <div class="mt-6 flex justify-end gap-3">
                <button type="button" onclick="closeModal('editModal')" class="rounded-lg px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-100 transition">Batal</button>
                <button type="submit" class="rounded-lg bg-slate-600 px-4 py-2 text-sm font-medium text-white hover:bg-slate-700 transition">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openModal(id) {
        const modal = document.getElementById(id);
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }
    
    function closeModal(id) {
        const modal = document.getElementById(id);
        modal.classList.remove('flex');
        modal.classList.add('hidden');
    }

    function editDoc(id, name, url) {
        const form = document.getElementById('editForm');
        form.action = `/adminit/documents/${id}`;
        document.getElementById('editName').value = name;
        document.getElementById('editUrl').value = url;
        openModal('editModal');
    }
</script>
@endsection
