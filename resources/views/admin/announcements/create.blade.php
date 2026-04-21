@extends('layouts.admin')

@section('title', 'Tambah Pengumuman')

@section('content')
    <div class="mb-4">
        <h1 class="text-2xl font-bold text-slate-900">Tambah Pengumuman</h1>
        <p class="text-sm text-slate-600">Buat pengumuman baru untuk ditampilkan di halaman publik.</p>
    </div>

    <div class="rounded-xl border border-slate-200 bg-white p-6">
        <form action="{{ route('admin.announcements.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf
            @include('admin.announcements._form', ['announcement' => new \App\Models\Announcement()])
            <div class="flex items-center gap-3">
                <button type="submit" class="rounded-md bg-slate-900 px-4 py-2 font-semibold text-white">Simpan</button>
                <a href="{{ route('admin.announcements.index') }}" class="rounded-md border border-slate-300 px-4 py-2 text-slate-700">Batal</a>
            </div>
        </form>
    </div>
@endsection
