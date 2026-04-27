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

    <form method="GET" action="{{ route('admin.tracer-alumni.index') }}" class="mb-4 rounded-xl border border-slate-200 bg-white p-4">
        <div class="mb-3 grid gap-3 md:grid-cols-2">
            <div>
                <label for="q" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Pencarian Umum</label>
                <input
                    id="q"
                    name="q"
                    value="{{ $filters['q'] }}"
                    placeholder="Cari semua kolom data tracer alumni..."
                    class="w-full rounded-md border border-slate-300 px-3 py-2"
                />
            </div>
            <div>
                <label for="is_active" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Status Tampil</label>
                <select id="is_active" name="is_active" class="w-full rounded-md border border-slate-300 px-3 py-2">
                    <option value="" @selected($filters['is_active'] === '')>Semua Status</option>
                    <option value="1" @selected($filters['is_active'] === '1')>Aktif</option>
                    <option value="0" @selected($filters['is_active'] === '0')>Nonaktif</option>
                </select>
            </div>
        </div>

        <div class="grid gap-3 md:grid-cols-2 lg:grid-cols-4">
            <div>
                <label for="nim" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">NIM</label>
                <input id="nim" name="nim" value="{{ $filters['nim'] }}" class="w-full rounded-md border border-slate-300 px-3 py-2" />
            </div>
            <div>
                <label for="graduation_year" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Tahun Lulusan</label>
                <input id="graduation_year" name="graduation_year" type="number" min="2000" max="2100" value="{{ $filters['graduation_year'] }}" class="w-full rounded-md border border-slate-300 px-3 py-2" />
            </div>
            <div>
                <label for="company_name" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Perusahaan</label>
                <input id="company_name" name="company_name" value="{{ $filters['company_name'] }}" class="w-full rounded-md border border-slate-300 px-3 py-2" />
            </div>
            <div>
                <label for="company_level" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Tingkat/Ukuran</label>
                <input id="company_level" name="company_level" value="{{ $filters['company_level'] }}" class="w-full rounded-md border border-slate-300 px-3 py-2" />
            </div>
            <div>
                <label for="department" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Departemen</label>
                <input id="department" name="department" value="{{ $filters['department'] }}" class="w-full rounded-md border border-slate-300 px-3 py-2" />
            </div>
            <div>
                <label for="relevance" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Kesesuaian</label>
                <input id="relevance" name="relevance" value="{{ $filters['relevance'] }}" class="w-full rounded-md border border-slate-300 px-3 py-2" />
            </div>
            <div class="md:col-span-2 lg:col-span-2">
                <label for="notes" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Catatan</label>
                <input id="notes" name="notes" value="{{ $filters['notes'] }}" class="w-full rounded-md border border-slate-300 px-3 py-2" />
            </div>
        </div>

        <div class="mt-4 flex flex-wrap items-center gap-2">
            <button type="submit" class="rounded-md bg-slate-900 px-4 py-2 text-sm font-semibold text-white">Cari</button>
            <a href="{{ route('admin.tracer-alumni.index') }}" class="rounded-md border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700">Reset</a>
        </div>
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
