@extends('layouts.admin')

@section('title', 'Tambah Kegiatan')

@section('content')
    <div class="mb-4">
        <h1 class="text-2xl font-bold text-slate-900">Tambah Dokumentasi Kegiatan</h1>
        <p class="text-sm text-slate-600">Isi label, judul, deskripsi, lokasi, waktu, dan gambar kegiatan.</p>
    </div>

    <form method="POST" action="{{ route('admin.activities.store') }}" enctype="multipart/form-data" class="rounded-xl border border-slate-200 bg-white p-4">
        @csrf
        @include('admin.activities._form')

        <div class="mt-5 flex items-center gap-3">
            <button type="submit" class="rounded-md bg-slate-900 px-4 py-2 text-sm font-semibold text-white">Simpan</button>
            <a href="{{ route('admin.activities.index') }}" class="rounded-md border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700">Batal</a>
        </div>
    </form>
@endsection