@extends('layouts.admin')

@section('title', 'Kelola Filter Galeri')

@section('content')
    <div class="mb-4 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Filter Galeri</h1>
            <p class="text-sm text-slate-600">Kelola tombol filter kategori galeri kegiatan.</p>
        </div>
        <a href="{{ route('admin.galleries.create') }}" class="rounded-md bg-slate-900 px-4 py-2 text-sm font-semibold text-white">+ Tambah Filter</a>
    </div>

    <div class="rounded-xl border border-slate-200 bg-white">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 text-left text-slate-600">
                    <tr>
                        <th class="px-4 py-3">Nama</th>
                        <th class="px-4 py-3">Slug</th>
                        <th class="px-4 py-3">Dibuat</th>
                        <th class="px-4 py-3">Publish At</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Item</th>
                        <th class="px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($galleries as $gallery)
                        <tr class="border-t border-slate-100">
                            <td class="px-4 py-3 font-semibold text-slate-900">{{ $gallery->name }}</td>
                            <td class="px-4 py-3 text-xs text-slate-600">{{ $gallery->slug }}</td>
                            <td class="px-4 py-3">{{ $gallery->created_at?->format('d M Y H:i') ?? '-' }}</td>
                            <td class="px-4 py-3">{{ $gallery->published_at?->format('d M Y H:i') ?? '-' }}</td>
                            <td class="px-4 py-3">
                                <span class="rounded-full px-2 py-1 text-xs {{ $gallery->status === 'published' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-200 text-slate-700' }}">
                                    {{ strtoupper($gallery->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3">{{ $gallery->items_count }}</td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <a href="{{ route('admin.galleries.edit', $gallery) }}" class="text-slate-900 underline">Edit</a>
                                    <a href="{{ route('admin.gallery-items.create', ['gallery_id' => $gallery->id]) }}" class="text-indigo-700 underline">Tambah Item</a>
                                    <form method="POST" action="{{ route('admin.galleries.destroy', $gallery) }}" onsubmit="return confirm('Hapus filter ini beserta item terkait?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-rose-600 underline">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-slate-500">Belum ada filter galeri.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-slate-200 px-4 py-3">
            {{ $galleries->links() }}
        </div>
    </div>
@endsection
