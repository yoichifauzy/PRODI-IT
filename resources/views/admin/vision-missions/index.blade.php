@extends('layouts.admin')

@section('title', 'Kelola Visi & Misi')

@section('content')
    <div class="mb-4 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Visi & Misi</h1>
            <p class="text-sm text-slate-600">Data pada status aktif akan ditampilkan di section Visi dan Misi pada homepage.</p>
        </div>
        <a href="{{ route('admin.vision-missions.create') }}" class="rounded-md bg-slate-900 px-4 py-2 text-sm font-semibold text-white">+ Tambah Data</a>
    </div>

    <div class="rounded-xl border border-slate-200 bg-white">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 text-left text-slate-600">
                    <tr>
                        <th class="px-4 py-3">Judul Visi</th>
                        <th class="px-4 py-3">Judul Misi</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($visionMissions as $item)
                        <tr class="border-t border-slate-100">
                            <td class="px-4 py-3">{{ $item->vision_title }}</td>
                            <td class="px-4 py-3">{{ $item->mission_title }}</td>
                            <td class="px-4 py-3">
                                <span class="rounded-full px-2 py-1 text-xs {{ $item->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-200 text-slate-700' }}">
                                    {{ $item->is_active ? 'AKTIF' : 'NONAKTIF' }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <a href="{{ route('admin.vision-missions.edit', $item) }}" class="text-slate-900 underline">Edit</a>
                                    <form method="POST" action="{{ route('admin.vision-missions.destroy', $item) }}" onsubmit="return confirm('Hapus data ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-rose-600 underline">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-8 text-center text-slate-500">Belum ada data visi dan misi.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-slate-200 px-4 py-3">
            {{ $visionMissions->links() }}
        </div>
    </div>
@endsection
