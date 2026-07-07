@extends('layouts.admin')

@section('title', 'Tambah Project Mahasiswa')

@section('content')
<div class="mb-6 flex items-center gap-3">
    <a href="{{ route('admin.projects.index') }}"
       class="flex h-9 w-9 items-center justify-center rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-100 transition">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
        </svg>
    </a>
    <div>
        <h1 class="text-2xl font-bold text-slate-800">Tambah Project</h1>
        <p class="text-sm text-slate-500">Isi informasi project mahasiswa baru.</p>
    </div>
</div>

@if($errors->any())
<div class="mb-4 rounded-lg bg-rose-50 border border-rose-200 p-4 text-sm text-rose-700">
    <ul class="list-disc list-inside space-y-1">
        @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
    </ul>
</div>
@endif

<form method="POST" action="{{ route('admin.projects.store') }}" enctype="multipart/form-data"
      class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
    @csrf
    @include('admin.projects._form')

    <div class="mt-6 flex items-center gap-3 border-t border-slate-100 pt-5">
        <button type="submit"
                class="rounded-lg bg-slate-900 px-5 py-2 text-sm font-semibold text-white hover:bg-slate-700 transition">
            Simpan Project
        </button>
        <a href="{{ route('admin.projects.index') }}"
           class="rounded-lg border border-slate-200 px-5 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-50 transition">
            Batal
        </a>
    </div>
</form>
@endsection