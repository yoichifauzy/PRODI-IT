@extends('layouts.admin')

@section('title', 'Edit Kalender Akademik')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-slate-800">Edit Kegiatan Akademik</h1>
    <p class="text-sm text-slate-500">Perubahan pada jadwal ini akan otomatis tersinkronisasi di Google Calendar publik.</p>
</div>

<form method="POST" action="{{ route('admin.academic-calendars.update', $academicCalendar) }}" class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
    @csrf
    @method('PUT')
    
    <div class="space-y-5">
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Judul Kegiatan <span class="text-rose-500">*</span></label>
            <input type="text" name="title" required value="{{ old('title', $academicCalendar->title) }}" class="w-full rounded-lg border border-slate-200 px-4 py-2 text-sm focus:border-slate-800 focus:outline-none focus:ring-1 focus:ring-slate-800">
            @error('title') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Kategori <span class="text-rose-500">*</span></label>
            <select name="category" required class="w-full rounded-lg border border-slate-200 px-4 py-2 text-sm focus:border-slate-800 focus:outline-none focus:ring-1 focus:ring-slate-800">
                <option value="">-- Pilih Kategori --</option>
                <option value="UAS" {{ old('category', $academicCalendar->category) == 'UAS' ? 'selected' : '' }}>UAS</option>
                <option value="UTS" {{ old('category', $academicCalendar->category) == 'UTS' ? 'selected' : '' }}>UTS</option>
                <option value="Lainnya" {{ old('category', $academicCalendar->category) == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
            </select>
            @error('category') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Tanggal Mulai <span class="text-rose-500">*</span></label>
                <input type="date" name="start_date" required value="{{ old('start_date', $academicCalendar->start_date->format('Y-m-d')) }}" class="w-full rounded-lg border border-slate-200 px-4 py-2 text-sm focus:border-slate-800 focus:outline-none focus:ring-1 focus:ring-slate-800">
                @error('start_date') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
            </div>
            
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Tanggal Selesai <span class="text-slate-400 font-normal">(Opsional)</span></label>
                <input type="date" name="end_date" value="{{ old('end_date', $academicCalendar->end_date ? $academicCalendar->end_date->format('Y-m-d') : '') }}" class="w-full rounded-lg border border-slate-200 px-4 py-2 text-sm focus:border-slate-800 focus:outline-none focus:ring-1 focus:ring-slate-800">
                <p class="text-xs text-slate-500 mt-1">Kosongkan jika kegiatan hanya berlangsung 1 hari.</p>
                @error('end_date') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Deskripsi <span class="text-slate-400 font-normal">(Opsional)</span></label>
            <textarea name="description" rows="3" class="w-full rounded-lg border border-slate-200 px-4 py-2 text-sm focus:border-slate-800 focus:outline-none focus:ring-1 focus:ring-slate-800">{{ old('description', $academicCalendar->description) }}</textarea>
            @error('description') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
        </div>
    </div>

    <div class="mt-8 flex items-center gap-3 border-t border-slate-100 pt-5">
        <button type="submit" class="rounded-lg bg-slate-900 px-5 py-2 text-sm font-semibold text-white hover:bg-slate-800 transition">Simpan Perubahan</button>
        <a href="{{ route('admin.academic-calendars.index') }}" class="rounded-lg border border-slate-300 px-5 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition">Batal</a>
    </div>
</form>
@endsection
