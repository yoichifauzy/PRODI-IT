@extends('layouts.admin')

@section('title', 'Kelola Kurikulum')

@section('content')
    <div class="mb-4 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Kurikulum</h1>
            <p class="text-sm text-slate-600">Kelola data kurikulum dan status aktif kurikulum.</p>
        </div>
        <a href="{{ route('admin.curricula.create') }}" class="rounded-md bg-slate-900 px-4 py-2 text-sm font-semibold text-white">+ Tambah Kurikulum</a>
    </div>

    <div class="rounded-xl border border-slate-200 bg-white">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 text-left text-slate-600">
                    <tr>
                        <th class="px-4 py-3">Nama</th>
                        <th class="px-4 py-3">Tahun</th>
                        <th class="px-4 py-3">Mata Kuliah</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($curricula as $curriculum)
                        <tr class="border-t border-slate-100">
                            <td class="px-4 py-3 font-semibold text-slate-900">{{ $curriculum->name }}</td>
                            <td class="px-4 py-3">{{ $curriculum->academic_year ?: '-' }}</td>
                            <td class="px-4 py-3">{{ $curriculum->courses_count }}</td>
                            <td class="px-4 py-3">
                                <span class="rounded-full px-2 py-1 text-xs {{ $curriculum->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-200 text-slate-700' }}">{{ $curriculum->is_active ? 'AKTIF' : 'NONAKTIF' }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <a href="{{ route('admin.curricula.edit', $curriculum) }}" class="text-slate-900 underline">Edit</a>
                                    <a href="{{ route('admin.curriculum-courses.index', ['curriculum_id' => $curriculum->id]) }}" class="text-indigo-700 underline">Mata Kuliah</a>
                                    <form method="POST" action="{{ route('admin.curricula.destroy', $curriculum) }}" onsubmit="return confirm('Hapus kurikulum ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-rose-600 underline">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-slate-500">Belum ada data kurikulum.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-slate-200 px-4 py-3">
            {{ $curricula->links() }}
        </div>
    </div>
@endsection