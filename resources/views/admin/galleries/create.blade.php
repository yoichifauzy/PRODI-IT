@extends('layouts.admin')

@section('title', 'Tambah Filter Galeri')

@section('content')
    <div class="mb-4">
        <h1 class="text-2xl font-bold text-slate-900">Tambah Filter Galeri</h1>
        <p class="text-sm text-slate-600">Filter ini akan tampil sebagai tombol kategori di section galeri.</p>
    </div>

    <form method="POST" action="{{ route('admin.galleries.store') }}" class="rounded-xl border border-slate-200 bg-white p-4">
        @csrf
        @include('admin.galleries._form')

        <div class="mt-5 flex items-center gap-3">
            <button type="submit" class="rounded-md bg-slate-900 px-4 py-2 text-sm font-semibold text-white">Simpan</button>
            <a href="{{ route('admin.galleries.index') }}" class="rounded-md border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700">Batal</a>
        </div>
    </form>
@endsection