@extends('layouts.admin')

@section('title', 'Edit Kegiatan')

@section('content')
    <div class="mb-6 flex items-center gap-3">
        <a href="{{ route('admin.activities.index') }}"
           class="flex h-9 w-9 items-center justify-center rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-100 transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Edit Kegiatan</h1>
            <p class="text-sm text-slate-500 truncate max-w-md">{{ $activity->title }}</p>
        </div>
    </div>

    @if($errors->any())
    <div class="mb-4 rounded-lg bg-rose-50 border border-rose-200 p-4 text-sm text-rose-700">
        <ul class="list-disc list-inside space-y-1">
            @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
        </ul>
    </div>
    @endif

    <form id="edit-form" method="POST" action="{{ route('admin.activities.update', $activity) }}" enctype="multipart/form-data" 
          class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
        @csrf
        @method('PUT')
        @include('admin.activities._form', ['activity' => $activity])

        <div class="mt-6 flex items-center gap-3 border-t border-slate-100 pt-5">
            <button type="submit" form="edit-form"
                    class="rounded-lg bg-slate-900 px-5 py-2 text-sm font-semibold text-white hover:bg-slate-700 transition">
                Perbarui Kegiatan
            </button>
            <a href="{{ route('admin.activities.index') }}"
               class="rounded-lg border border-slate-200 px-5 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-50 transition">
                Batal
            </a>

            {{-- Quick delete --}}
            <button type="submit" form="delete-form"
                    class="ml-auto rounded-lg border border-rose-200 px-4 py-2 text-sm font-semibold text-rose-600 hover:bg-rose-50 transition">
                Hapus Kegiatan
            </button>
        </div>
    </form>

    <form id="delete-form" method="POST" action="{{ route('admin.activities.destroy', $activity) }}" class="hidden"
          onsubmit="return confirm('Hapus kegiatan ini secara permanen?')">
        @csrf
        @method('DELETE')
    </form>
@endsection