@extends('layouts.admin')

@section('title', 'Tambah Event Akademik')

@section('content')
    <div class="mb-4">
        <h1 class="text-2xl font-bold text-slate-900">Tambah Event Akademik</h1>
        <p class="text-sm text-slate-600">Tambahkan agenda akademik baru ke kalender publik.</p>
    </div>

    <div class="rounded-xl border border-slate-200 bg-white p-6">
        <form method="POST" action="{{ route('admin.academic-events.store') }}" class="space-y-5">
            @csrf
            @include('admin.academic-events._form', ['academicEvent' => new \App\Models\AcademicEvent(['is_published' => true]), 'eventTypes' => $eventTypes])
            <div class="flex items-center gap-3">
                <button type="submit" class="rounded-md bg-slate-900 px-4 py-2 font-semibold text-white">Simpan</button>
                <a href="{{ route('admin.academic-events.index') }}" class="rounded-md border border-slate-300 px-4 py-2 text-slate-700">Batal</a>
            </div>
        </form>
    </div>
@endsection
