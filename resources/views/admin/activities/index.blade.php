@extends('layouts.admin')

@section('title', 'Kelola Kegiatan Kami')

@section('content')
    <div class="mb-4 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Kegiatan Kami</h1>
            <p class="text-sm text-slate-600">Kelola dokumentasi card kegiatan: label, judul, deskripsi, lokasi, dan tanggal.</p>
        </div>
        <a href="{{ route('admin.activities.create') }}" class="rounded-md bg-slate-900 px-4 py-2 text-sm font-semibold text-white">+ Tambah Kegiatan</a>
    </div>

    <form method="GET" action="{{ route('admin.activities.index') }}" class="mb-4 grid gap-3 rounded-xl border border-slate-200 bg-white p-4 md:grid-cols-[1fr_auto]">
        <input name="q" value="{{ $search }}" placeholder="Cari judul, kategori, deskripsi, atau lokasi..." class="rounded-md border border-slate-300 px-3 py-2" />
        <button type="submit" class="rounded-md bg-slate-900 px-4 py-2 text-sm font-semibold text-white">Cari</button>
    </form>

    <div class="rounded-xl border border-slate-200 bg-white">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 text-left text-slate-600">
                    <tr>
                        <th class="px-4 py-3">Preview</th>
                        <th class="px-4 py-3">Konten</th>
                        <th class="px-4 py-3">Tanggal Kegiatan</th>
                        <th class="px-4 py-3">Dibuat</th>
                        <th class="px-4 py-3">Publish At</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($activities as $activity)
                        <tr class="border-t border-slate-100 align-top">
                            <td class="px-4 py-3">
                                @if ($activity->image_path)
                                    <img src="{{ asset('storage/' . $activity->image_path) }}" alt="{{ $activity->title }}" class="h-20 w-32 rounded-md object-cover" />
                                @else
                                    <span class="text-xs text-slate-400">Tanpa gambar</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <p class="mb-1 inline-block rounded-full bg-orange-100 px-2 py-0.5 text-xs font-medium text-orange-700">{{ $activity->category }}</p>
                                <p class="font-semibold text-slate-900">{{ $activity->title }}</p>
                                <p class="mt-1 line-clamp-2 text-xs text-slate-500">{{ $activity->description ?: '-' }}</p>
                                <p class="mt-1 text-xs text-slate-500">{{ $activity->location ?: '-' }}</p>
                            </td>
                            <td class="px-4 py-3">{{ $activity->event_date->format('d M Y') }}</td>
                            <td class="px-4 py-3">{{ $activity->created_at?->format('d M Y H:i') ?? '-' }}</td>
                            <td class="px-4 py-3">{{ $activity->published_at?->format('d M Y H:i') ?? '-' }}</td>
                            <td class="px-4 py-3">
                                <span class="rounded-full px-2 py-1 text-xs {{ $activity->is_published ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-200 text-slate-700' }}">
                                    {{ $activity->is_published ? 'PUBLISHED' : 'DRAFT' }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <a href="{{ route('admin.activities.edit', $activity) }}" class="text-slate-900 underline">Edit</a>
                                    <form method="POST" action="{{ route('admin.activities.destroy', $activity) }}" onsubmit="return confirm('Hapus kegiatan ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-rose-600 underline">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-slate-500">Belum ada dokumentasi kegiatan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-slate-200 px-4 py-3">
            {{ $activities->links() }}
        </div>
    </div>
@endsection
