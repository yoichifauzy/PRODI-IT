@extends('layouts.admin')

@section('title', 'Tambah Blog Dosen/Staff')

@section('content')
    <div class="mb-4">
        <h1 class="text-2xl font-bold text-slate-900">Tambah Blog Dosen/Staff</h1>
        <p class="text-sm text-slate-600">Tambahkan dokumentasi kegiatan mengajar untuk {{ $lecturerStaff->name }}.</p>
    </div>

    <div class="rounded-xl border border-slate-200 bg-white p-6">
        <form action="{{ route('admin.lecturer-staff.blogs.store', $lecturerStaff) }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf
            @include('admin.lecturer-staff-blogs._form', ['blog' => $blog])
            <div class="flex items-center gap-3">
                <button type="submit" class="rounded-md bg-slate-900 px-4 py-2 font-semibold text-white">Simpan</button>
                <a href="{{ route('admin.lecturer-staff.blogs.index', $lecturerStaff) }}" class="rounded-md border border-slate-300 px-4 py-2 text-slate-700">Batal</a>
            </div>
        </form>
    </div>
@endsection
