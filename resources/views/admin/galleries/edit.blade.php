@extends('layouts.admin')

@section('title', 'Edit Filter Galeri')

@section('content')
    <div class="mb-4">
        <h1 class="text-2xl font-bold text-slate-900">Edit Filter Galeri</h1>
        <p class="text-sm text-slate-600">Perbarui filter dan lihat item galeri terkait.</p>
    </div>

    <form method="POST" action="{{ route('admin.galleries.update', $gallery) }}" class="rounded-xl border border-slate-200 bg-white p-4">
        @csrf
        @method('PUT')
        @include('admin.galleries._form', ['gallery' => $gallery])

        <div class="mt-5 flex items-center gap-3">
            <button type="submit" class="rounded-md bg-slate-900 px-4 py-2 text-sm font-semibold text-white">Update</button>
            <a href="{{ route('admin.galleries.index') }}" class="rounded-md border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700">Batal</a>
        </div>
    </form>

    <div class="mt-6 rounded-xl border border-slate-200 bg-white p-4">
        <div class="mb-3 flex items-center justify-between">
            <h2 class="text-lg font-semibold text-slate-900">Item Pada Filter Ini</h2>
            <a href="{{ route('admin.gallery-items.create', ['gallery_id' => $gallery->id]) }}" class="rounded-md bg-slate-900 px-3 py-2 text-xs font-semibold text-white">+ Tambah Item</a>
        </div>

        <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
            @forelse ($gallery->items as $item)
                <div class="rounded-lg border border-slate-200 p-2">
                    <img src="{{ asset('storage/' . $item->image_path) }}" alt="{{ $item->title ?: $gallery->name }}" class="h-32 w-full rounded object-cover" />
                    <p class="mt-2 text-sm font-semibold text-slate-900">{{ $item->title ?: '-' }}</p>
                    <p class="text-xs text-slate-500">{{ optional($item->taken_at)->format('d M Y') ?: '-' }}</p>
                    <a href="{{ route('admin.gallery-items.edit', $item) }}" class="mt-1 inline-block text-xs text-slate-900 underline">Edit</a>
                </div>
            @empty
                <p class="text-sm text-slate-500">Belum ada item untuk filter ini.</p>
            @endforelse
        </div>
    </div>
@endsection