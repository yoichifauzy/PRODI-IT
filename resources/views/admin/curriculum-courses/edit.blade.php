@extends('layouts.admin')

@section('title', 'Edit Mata Kuliah')

@section('content')
    <div class="mb-4">
        <h1 class="text-2xl font-bold text-slate-900">Edit Mata Kuliah Kurikulum</h1>
        <p class="text-sm text-slate-600">Perbarui data mata kuliah.</p>
    </div>

    <form method="POST" action="{{ route('admin.curriculum-courses.update', $curriculumCourse) }}" class="rounded-xl border border-slate-200 bg-white p-4">
        @csrf
        @method('PUT')
        @include('admin.curriculum-courses._form', ['curriculumCourse' => $curriculumCourse])

        <div class="mt-5 flex items-center gap-3">
            <button type="submit" class="rounded-md bg-slate-900 px-4 py-2 text-sm font-semibold text-white">Update</button>
            <a href="{{ route('admin.curriculum-courses.index') }}" class="rounded-md border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700">Batal</a>
        </div>
    </form>
@endsection