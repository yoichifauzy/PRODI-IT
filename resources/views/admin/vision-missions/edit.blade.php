@extends('layouts.admin')

@section('title', 'Edit Visi & Misi')

@section('content')
    <div class="mb-4">
        <h1 class="text-2xl font-bold text-slate-900">Edit Visi & Misi</h1>
        <p class="text-sm text-slate-600">Perbarui data visi dan misi yang ditampilkan.</p>
    </div>

    <div class="rounded-xl border border-slate-200 bg-white p-6">
        <form method="POST" action="{{ route('admin.vision-missions.update', $visionMission) }}" class="space-y-5">
            @csrf
            @method('PUT')
            @include('admin.vision-missions._form', ['visionMission' => $visionMission])
            <div class="flex items-center gap-3">
                <button type="submit" class="rounded-md bg-slate-900 px-4 py-2 font-semibold text-white">Update</button>
                <a href="{{ route('admin.vision-missions.index') }}" class="rounded-md border border-slate-300 px-4 py-2 text-slate-700">Batal</a>
            </div>
        </form>
    </div>
@endsection
