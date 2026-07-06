@extends('layouts.admin')

@section('title', 'Kelola Slide Tracer Alumni')

@section('content')
    <div class="mb-4 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Slide Tracer Alumni</h1>
            <p class="text-sm text-slate-600">Kelola gambar slide untuk banner halaman Tracer Alumni.</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.tracer-alumni.index') }}" class="rounded-md border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700">Kembali</a>
            <a href="{{ route('admin.tracer-alumni-slides.create') }}" class="rounded-md bg-slate-900 px-4 py-2 text-sm font-semibold text-white">+ Tambah Slide</a>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-4 rounded-md bg-green-50 p-4 text-sm text-green-700">
            {{ session('success') }}
        </div>
    @endif

    <div class="rounded-xl border border-slate-200 bg-white">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 text-left text-slate-600">
                    <tr>
                        <th class="px-4 py-3">Urutan</th>
                        <th class="px-4 py-3">Gambar</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($slides as $slide)
                        <tr class="border-t border-slate-100">
                            <td class="px-4 py-3">{{ $slide->order }}</td>
                            <td class="px-4 py-3">
                                <img src="{{ asset('storage/' . $slide->image_path) }}" alt="Slide" class="h-16 w-32 object-cover rounded">
                            </td>
                            <td class="px-4 py-3">
                                @if($slide->is_active)
                                    <span class="rounded-full bg-green-100 px-2 py-1 text-xs font-semibold text-green-700">Aktif</span>
                                @else
                                    <span class="rounded-full bg-slate-100 px-2 py-1 text-xs font-semibold text-slate-700">Nonaktif</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <a href="{{ route('admin.tracer-alumni-slides.edit', $slide) }}" class="text-slate-900 underline">Edit</a>
                                    <form method="POST" action="{{ route('admin.tracer-alumni-slides.destroy', $slide) }}" onsubmit="return confirm('Hapus slide ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-rose-600 underline">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-8 text-center text-slate-500">Belum ada slide gambar.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
