@extends('layouts.admin')

@section('title', 'Tambah Tracer Alumni')

@section('content')
    <div class="mb-4">
        <h1 class="text-2xl font-bold text-slate-900">Tambah Data Tracer Alumni</h1>
        <p class="text-sm text-slate-600">Isi data alumni berdasarkan tabel tracer alumni.</p>
    </div>

    <form method="POST" action="{{ route('admin.tracer-alumni.store') }}" class="rounded-xl border border-slate-200 bg-white p-4">
        @csrf
        @include('admin.tracer-alumni._form', ['tracerAlumni' => new \App\Models\TracerAlumni()])

        <div class="mt-5 flex items-center gap-3">
            <button type="submit" class="rounded-md bg-slate-900 px-4 py-2 text-sm font-semibold text-white">Simpan</button>
            <a href="{{ route('admin.tracer-alumni.index') }}" class="rounded-md border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700">Batal</a>
        </div>
    </form>
@endsection
