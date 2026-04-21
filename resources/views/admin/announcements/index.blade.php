@extends('layouts.admin')

@section('title', 'Kelola Pengumuman')

@section('content')
    <div class="mb-4 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Pengumuman</h1>
            <p class="text-sm text-slate-600">Kelola pengumuman yang tampil di halaman publik.</p>
        </div>
        <a href="{{ route('admin.announcements.create') }}" class="rounded-md bg-slate-900 px-4 py-2 text-sm font-semibold text-white">+ Tambah Pengumuman</a>
    </div>

    <div class="rounded-xl border border-slate-200 bg-white">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 text-left text-slate-600">
                    <tr>
                        <th class="px-4 py-3">Judul</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Dibuat</th>
                        <th class="px-4 py-3">Publish At</th>
                        <th class="px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($announcements as $announcement)
                        <tr class="border-t border-slate-100">
                            <td class="px-4 py-3">
                                <p class="font-medium text-slate-900">{{ $announcement->title }}</p>
                                <p class="text-xs text-slate-500">/{{ $announcement->slug }}</p>
                            </td>
                            <td class="px-4 py-3">
                                <span class="rounded-full px-2 py-1 text-xs {{ $announcement->status === 'published' ? 'bg-emerald-100 text-emerald-700' : ($announcement->status === 'draft' ? 'bg-amber-100 text-amber-700' : 'bg-slate-200 text-slate-700') }}">
                                    {{ strtoupper($announcement->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3">{{ optional($announcement->created_at)->format('d M Y H:i') ?: '-' }}</td>
                            <td class="px-4 py-3">{{ optional($announcement->published_at)->format('d M Y H:i') ?: '-' }}</td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <a href="{{ route('admin.announcements.edit', $announcement) }}" class="text-slate-900 underline">Edit</a>
                                    <form action="{{ route('admin.announcements.destroy', $announcement) }}" method="POST" onsubmit="return confirm('Hapus pengumuman ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-rose-600 underline">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-slate-500">Belum ada data pengumuman.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-slate-200 px-4 py-3">
            {{ $announcements->links() }}
        </div>
    </div>
@endsection
