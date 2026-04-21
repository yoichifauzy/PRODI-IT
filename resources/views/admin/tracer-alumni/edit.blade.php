@extends('layouts.admin')

@section('title', 'Edit Tracer Alumni')

@section('content')
    <div class="mb-4">
        <h1 class="text-2xl font-bold text-slate-900">Edit Data Tracer Alumni</h1>
        <p class="text-sm text-slate-600">Perbarui data tracer alumni.</p>
    </div>

    <form method="POST" action="{{ route('admin.tracer-alumni.update', $tracerAlumni) }}" class="rounded-xl border border-slate-200 bg-white p-4">
        @csrf
        @method('PUT')
        @include('admin.tracer-alumni._form', ['tracerAlumni' => $tracerAlumni])

        <div class="mt-5 flex items-center gap-3">
            <button type="submit" class="rounded-md bg-slate-900 px-4 py-2 text-sm font-semibold text-white">Update</button>
            <a href="{{ route('admin.tracer-alumni.index') }}" class="rounded-md border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700">Batal</a>
        </div>
    </form>
@endsection