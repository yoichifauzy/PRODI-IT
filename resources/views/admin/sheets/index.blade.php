@extends('layouts.admin')
@section('title', 'Kelola Sheet Upload')

@section('content')
<div class="mb-6 grid gap-6 md:grid-cols-2">
    <div class="rounded-xl border bg-white p-5 shadow-sm">
        <h2 class="mb-4 text-lg font-bold">Tambah Data Sheet Baru</h2>
        <form action="{{ route('admin.sheets.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
                <label class="block text-sm font-medium text-slate-700">Nama Sheet</label>
                <input type="text" name="name" required class="mt-1 w-full rounded-md border p-2" placeholder="Misal: Kurikulum Update 2026">
            </div>
            <div class="mb-3">
                <label class="block text-sm font-medium text-slate-700">Tipe Sumber</label>
                <select name="type" class="mt-1 w-full rounded-md border p-2">
                    <option value="file">Upload File Excel (.xlsx/.csv)</option>
                    <option value="url">Link Public Google Sheets (CSV Format)</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="block text-sm font-medium text-slate-700">File / Link</label>
                <input type="file" name="file" class="mb-2 block w-full text-sm">
                <input type="url" name="url" placeholder="https://..." class="w-full rounded-md border p-2">
            </div>
            <button type="submit" class="rounded-md bg-indigo-600 px-4 py-2 text-white">Simpan</button>
        </form>
    </div>

    <div class="rounded-xl border bg-white p-5 shadow-sm">
        <h2 class="mb-4 text-lg font-bold">Daftar Sheet Tersedia</h2>
        <table class="w-full text-left text-sm">
            <thead class="border-b bg-slate-50">
                <tr>
                    <th class="p-2">Nama</th>
                    <th class="p-2">Tipe</th>
                    <th class="p-2">Status</th>
                    <th class="p-2">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sheets as $sheet)
                <tr class="border-b">
                    <td class="p-2">{{ $sheet->name }}</td>
                    <td class="p-2">{{ strtoupper($sheet->type) }}</td>
                    <td class="p-2">
                        @if($sheet->is_active)
                            <span class="rounded bg-emerald-100 px-2 text-emerald-700">Aktif</span>
                        @else
                            <span class="text-slate-400">Nonaktif</span>
                        @endif
                    </td>
                    <td class="p-2 flex gap-2">
                        @if(!$sheet->is_active)
                        <form action="{{ route('admin.sheets.set-active', $sheet->id) }}" method="POST">
                            @csrf
                            <button class="rounded bg-indigo-600 px-2 py-1 text-white">Jadikan Aktif</button>
                        </form>
                        @endif
                        <form action="{{ route('admin.sheets.destroy', $sheet->id) }}" method="POST" onsubmit="return confirm('Hapus?');">
                            @csrf @method('DELETE')
                            <button class="rounded bg-rose-600 px-2 py-1 text-white">Hapus</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection