@extends('layouts.admin')

@section('title', 'Edit Event Akademik')

@section('content')
    <div class="mb-4">
        <h1 class="text-2xl font-bold text-slate-900">Edit Event Akademik</h1>
        <p class="text-sm text-slate-600">Perbarui detail event kalender akademik.</p>
    </div>

    <div class="rounded-xl border border-slate-200 bg-white p-6">
        <form method="POST" action="{{ route('admin.academic-events.update', $academicEvent) }}" class="space-y-5">
            @csrf
            @method('PUT')
            @include('admin.academic-events._form', ['academicEvent' => $academicEvent, 'eventTypes' => $eventTypes])
            <div class="flex items-center gap-3">
                <button type="submit" class="rounded-md bg-slate-900 px-4 py-2 font-semibold text-white">Update</button>
                <a href="{{ route('admin.academic-events.index') }}" class="rounded-md border border-slate-300 px-4 py-2 text-slate-700">Batal</a>
            </div>
        </form>
    </div>
@endsection
