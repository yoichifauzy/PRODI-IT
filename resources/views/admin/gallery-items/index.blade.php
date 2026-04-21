@extends('layouts.admin')

@section('title', 'Kelola Item Galeri')

@section('content')
    <div class="mb-4 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Item Galeri</h1>
            <p class="text-sm text-slate-600">Kelola isi dokumentasi foto yang ditampilkan pada section galeri.</p>
        </div>
        <a href="{{ route('admin.gallery-items.create') }}" class="rounded-md bg-slate-900 px-4 py-2 text-sm font-semibold text-white">+ Tambah Item</a>
    </div>

    <form method="GET" action="{{ route('admin.gallery-items.index') }}" class="mb-4 grid gap-3 rounded-xl border border-slate-200 bg-white p-4 md:grid-cols-[1fr_auto]">
        <select name="gallery_id" class="rounded-md border border-slate-300 px-3 py-2">
            <option value="">Semua Filter</option>
            @foreach ($galleries as $gallery)
                <option value="{{ $gallery->id }}" @selected((string) $galleryId === (string) $gallery->id)>{{ $gallery->name }}</option>
            @endforeach
        </select>
        <button type="submit" class="rounded-md bg-slate-900 px-4 py-2 text-sm font-semibold text-white">Filter</button>
    </form>

    <div class="rounded-xl border border-slate-200 bg-white">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 text-left text-slate-600">
                    <tr>
                        <th class="px-4 py-3">Preview</th>
                        <th class="px-4 py-3">Filter</th>
                        <th class="px-4 py-3">Judul</th>
                        <th class="px-4 py-3">Tanggal</th>
                        <th class="px-4 py-3">Dibuat</th>
                        <th class="px-4 py-3">Publish At</th>
                        <th class="px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($items as $item)
                        <tr class="border-t border-slate-100">
                            <td class="px-4 py-3"><img src="{{ asset('storage/' . $item->image_path) }}" alt="{{ $item->title ?: 'Item Galeri' }}" class="h-20 w-32 rounded-md object-cover" /></td>
                            <td class="px-4 py-3">{{ $item->gallery?->name ?: '-' }}</td>
                            <td class="px-4 py-3">{{ $item->title ?: '-' }}</td>
                            <td class="px-4 py-3">{{ optional($item->taken_at)->format('d M Y') ?: '-' }}</td>
                            <td class="px-4 py-3">{{ $item->created_at?->format('d M Y H:i') ?? '-' }}</td>
                            <td class="px-4 py-3">{{ $item->published_at?->format('d M Y H:i') ?? '-' }}</td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <a href="{{ route('admin.gallery-items.edit', $item) }}" class="text-slate-900 underline">Edit</a>
                                    <form method="POST" action="{{ route('admin.gallery-items.destroy', $item) }}" onsubmit="return confirm('Hapus item galeri ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-rose-600 underline">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-slate-500">Belum ada item galeri.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-slate-200 px-4 py-3">
            {{ $items->links() }}
        </div>
    </div>
@endsection
