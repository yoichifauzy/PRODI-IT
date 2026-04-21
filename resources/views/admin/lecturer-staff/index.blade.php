@extends('layouts.admin')

@section('title', 'Kelola Dosen & Staff')

@section('content')
    <div class="mb-4 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Dosen & Staff</h1>
            <p class="text-sm text-slate-600">Kelola data dosen dan staff prodi.</p>
        </div>
        <a href="{{ route('admin.lecturer-staff.create') }}" class="rounded-md bg-slate-900 px-4 py-2 text-sm font-semibold text-white">+ Tambah Data</a>
    </div>

    <form method="GET" action="{{ route('admin.lecturer-staff.index') }}" class="mb-4 grid gap-3 rounded-xl border border-slate-200 bg-white p-4 md:grid-cols-[1fr_auto]">
        <select name="type" class="rounded-md border border-slate-300 px-3 py-2">
            <option value="">Semua Tipe</option>
            @foreach ($types as $itemType)
                <option value="{{ $itemType }}" @selected($type === $itemType)>{{ strtoupper($itemType) }}</option>
            @endforeach
        </select>
        <button type="submit" class="rounded-md bg-slate-900 px-4 py-2 text-sm font-semibold text-white">Filter</button>
    </form>

    <div class="rounded-xl border border-slate-200 bg-white">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 text-left text-slate-600">
                    <tr>
                        <th class="px-4 py-3">Nama</th>
                        <th class="px-4 py-3">Tipe</th>
                        <th class="px-4 py-3">Posisi</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($members as $member)
                        <tr class="border-t border-slate-100">
                            <td class="px-4 py-3">
                                <p class="font-semibold text-slate-900">{{ $member->name }}</p>
                                <p class="text-xs text-slate-500">{{ $member->email ?: '-' }}</p>
                            </td>
                            <td class="px-4 py-3 uppercase">{{ $member->type }}</td>
                            <td class="px-4 py-3">{{ $member->position }}</td>
                            <td class="px-4 py-3">
                                <span class="rounded-full px-2 py-1 text-xs {{ $member->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-200 text-slate-700' }}">{{ $member->is_active ? 'AKTIF' : 'NONAKTIF' }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <a href="{{ route('admin.lecturer-staff.edit', $member) }}" class="text-slate-900 underline">Edit</a>
                                    <a href="{{ route('admin.lecturer-staff.blogs.index', $member) }}" class="text-blue-700 underline">Blog</a>
                                    <form method="POST" action="{{ route('admin.lecturer-staff.destroy', $member) }}" onsubmit="return confirm('Hapus data ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-rose-600 underline">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-slate-500">Belum ada data dosen/staff.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-slate-200 px-4 py-3">
            {{ $members->links() }}
        </div>
    </div>
@endsection
