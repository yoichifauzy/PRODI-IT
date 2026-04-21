@extends('layouts.admin')

@section('title', 'Edit Kegiatan')

@section('content')
    <div class="mb-4">
        <h1 class="text-2xl font-bold text-slate-900">Edit Dokumentasi Kegiatan</h1>
        <p class="text-sm text-slate-600">Perbarui konten card kegiatan.</p>
    </div>

    <form method="POST" action="{{ route('admin.activities.update', $activity) }}" enctype="multipart/form-data" class="rounded-xl border border-slate-200 bg-white p-4">
        @csrf
        @method('PUT')
        @include('admin.activities._form', ['activity' => $activity])

        <div class="mt-5 flex items-center gap-3">
            <button type="submit" class="rounded-md bg-slate-900 px-4 py-2 text-sm font-semibold text-white">Update</button>
            <a href="{{ route('admin.activities.index') }}" class="rounded-md border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700">Batal</a>
        </div>
    </form>
@endsection