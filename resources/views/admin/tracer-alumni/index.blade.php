@extends('layouts.admin')

@section('title', 'Kelola Tracer Alumni')

@section('content')
    <div class="mb-4 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Tracer Alumni</h1>
            <p class="text-sm text-slate-600">Kelola data tabel tracer alumni (No, NIM, Perusahaan, Tingkat, Departemen, Kesesuaian).</p>
        </div>
        <a href="{{ route('admin.tracer-alumni.create') }}" class="rounded-md bg-slate-900 px-4 py-2 text-sm font-semibold text-white">+ Tambah Data</a>
    </div>

    <form method="GET" action="{{ route('admin.tracer-alumni.index') }}" class="mb-4 grid gap-3 rounded-xl border border-slate-200 bg-white p-4 md:grid-cols-[1fr_auto]">
        <input name="q" value="{{ $search }}" placeholder="Cari NIM, perusahaan, departemen..." class="rounded-md border border-slate-300 px-3 py-2" />
        <button type="submit" class="rounded-md bg-slate-900 px-4 py-2 text-sm font-semibold text-white">Cari</button>
    </form>

    <div class="rounded-xl border border-slate-200 bg-white">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 text-left text-slate-600">
                    <tr>
                        <th class="px-4 py-3">No</th>
                        <th class="px-4 py-3">Lulusan</th>
                        <th class="px-4 py-3">NIM</th>
                        <th class="px-4 py-3">Perusahaan</th>
                        <th class="px-4 py-3">Tingkat/Ukuran</th>
                        <th class="px-4 py-3">Departemen</th>
                        <th class="px-4 py-3">Kesesuaian</th>
                        <th class="px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($alumni as $index => $row)
                        <tr class="border-t border-slate-100">
                            <td class="px-4 py-3">{{ $alumni->firstItem() + $index }}</td>
                            <td class="px-4 py-3">{{ $row->graduation_year ?: '-' }}</td>
                            <td class="px-4 py-3">{{ $row->nim }}</td>
                            <td class="px-4 py-3">{{ $row->company_name }}</td>
                            <td class="px-4 py-3">{{ $row->company_level ?: '-' }}</td>
                            <td class="px-4 py-3">{{ $row->department ?: '-' }}</td>
                            <td class="px-4 py-3">{{ $row->relevance ?: '-' }}</td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <a href="{{ route('admin.tracer-alumni.edit', $row) }}" class="text-slate-900 underline">Edit</a>
                                    <form method="POST" action="{{ route('admin.tracer-alumni.destroy', $row) }}" onsubmit="return confirm('Hapus data tracer alumni ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-rose-600 underline">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-8 text-center text-slate-500">Belum ada data tracer alumni.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-slate-200 px-4 py-3">
            {{ $alumni->links() }}
        </div>
    </div>
@endsection
