@extends('layouts.admin')

@section('title', 'Kelola Hero Section')

@section('content')
    <div class="mb-4 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Hero Section</h1>
            <p class="text-sm text-slate-600">Kelola gambar slider hero yang tampil di halaman utama.</p>
        </div>
        <a href="{{ route('admin.hero-slides.create') }}" class="rounded-md bg-slate-900 px-4 py-2 text-sm font-semibold text-white">+ Tambah Slide</a>
    </div>

    <div class="rounded-xl border border-slate-200 bg-white">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 text-left text-slate-600">
                    <tr>
                        <th class="px-4 py-3">Preview</th>
                        <th class="px-4 py-3">Judul</th>
                        <th class="px-4 py-3">Urutan</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($heroSlides as $slide)
                        <tr class="border-t border-slate-100">
                            <td class="px-4 py-3">
                                <img src="{{ asset('storage/' . $slide->image_path) }}" alt="{{ $slide->title ?: 'Slide Hero' }}" class="h-20 w-36 rounded-md object-cover" />
                            </td>
                            <td class="px-4 py-3">
                                <p class="font-semibold text-slate-900">{{ $slide->title ?: '-' }}</p>
                                <p class="text-xs text-slate-500">{{ $slide->subtitle ?: '-' }}</p>
                            </td>
                            <td class="px-4 py-3">{{ $slide->sort_order }}</td>
                            <td class="px-4 py-3">
                                <span class="rounded-full px-2 py-1 text-xs {{ $slide->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-200 text-slate-700' }}">
                                    {{ $slide->is_active ? 'AKTIF' : 'NONAKTIF' }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <a href="{{ route('admin.hero-slides.edit', $slide) }}" class="text-slate-900 underline">Edit</a>
                                    <form method="POST" action="{{ route('admin.hero-slides.destroy', $slide) }}" onsubmit="return confirm('Hapus slide hero ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-rose-600 underline">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-slate-500">Belum ada slide hero.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-slate-200 px-4 py-3">
            {{ $heroSlides->links() }}
        </div>
    </div>
@endsection