@extends('layouts.admin')

@section('title', 'Edit Kurikulum')

@section('content')
    <div class="mb-4">
        <h1 class="text-2xl font-bold text-slate-900">Edit Kurikulum</h1>
        <p class="text-sm text-slate-600">Perbarui data kurikulum serta lihat daftar mata kuliah terhubung.</p>
    </div>

    <form method="POST" action="{{ route('admin.curricula.update', $curriculum) }}" class="rounded-xl border border-slate-200 bg-white p-4">
        @csrf
        @method('PUT')
        @include('admin.curricula._form', ['curriculum' => $curriculum])

        <div class="mt-5 flex items-center gap-3">
            <button type="submit" class="rounded-md bg-slate-900 px-4 py-2 text-sm font-semibold text-white">Update</button>
            <a href="{{ route('admin.curricula.index') }}" class="rounded-md border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700">Batal</a>
        </div>
    </form>

    <div class="mt-6 rounded-xl border border-slate-200 bg-white p-4">
        <div class="mb-3 flex items-center justify-between">
            <h2 class="text-lg font-semibold text-slate-900">Mata Kuliah Kurikulum Ini</h2>
            <a href="{{ route('admin.curriculum-courses.create', ['curriculum_id' => $curriculum->id]) }}" class="rounded-md bg-slate-900 px-3 py-2 text-xs font-semibold text-white">+ Tambah Mata Kuliah</a>
        </div>

        <div class="space-y-2">
            @forelse ($curriculum->courses as $course)
                <div class="rounded-md border border-slate-200 px-3 py-2 text-sm">
                    <p class="font-semibold text-slate-900">S{{ $course->semester }} - {{ $course->code }} - {{ $course->name }} ({{ $course->credits }} SKS)</p>
                    <a href="{{ route('admin.curriculum-courses.edit', $course) }}" class="text-xs text-slate-900 underline">Edit Mata Kuliah</a>
                </div>
            @empty
                <p class="text-sm text-slate-500">Belum ada mata kuliah untuk kurikulum ini.</p>
            @endforelse
        </div>
    </div>
@endsection