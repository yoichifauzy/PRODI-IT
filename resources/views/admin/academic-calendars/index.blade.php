@extends('layouts.admin')

@section('title', 'Kelola Kalender Akademik')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-2xl font-bold text-slate-800">Kalender Akademik</h1>
        <p class="text-sm text-slate-500">Kelola jadwal kegiatan akademik prodi dan otomatis tersinkronisasi ke Google Calendar.</p>
    </div>
    <a href="{{ route('admin.academic-calendars.create') }}" class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-700 transition">
        + Tambah Jadwal
    </a>
</div>

<div class="rounded-xl border border-slate-200 bg-white overflow-hidden shadow-sm">
    <table class="min-w-full divide-y divide-slate-200 text-sm">
        <thead class="bg-slate-50">
            <tr>
                <th class="px-5 py-3 text-left font-semibold text-slate-700">Kegiatan</th>
                <th class="px-5 py-3 text-left font-semibold text-slate-700">Kategori</th>
                <th class="px-5 py-3 text-left font-semibold text-slate-700">Tanggal</th>
                <th class="px-5 py-3 text-left font-semibold text-slate-700 w-48">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @forelse($calendars as $calendar)
            <tr class="hover:bg-slate-50 transition">
                <td class="px-5 py-3">
                    <span class="font-medium text-slate-800">{{ $calendar->title }}</span>
                </td>
                <td class="px-5 py-3">
                    <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-medium text-slate-800">
                        {{ $calendar->category }}
                    </span>
                </td>
                <td class="px-5 py-3">
                    <div class="text-slate-800">{{ $calendar->start_date->format('d M Y') }}</div>
                    @if($calendar->end_date && $calendar->end_date != $calendar->start_date)
                        <div class="text-xs text-slate-500">s/d {{ $calendar->end_date->format('d M Y') }}</div>
                    @endif
                </td>
                <td class="px-5 py-3">
                    <div class="flex items-center gap-3">
                        <a href="{{ route('admin.academic-calendars.edit', $calendar) }}" class="text-slate-500 hover:text-slate-600 transition">Edit</a>
                        <form action="{{ route('admin.academic-calendars.destroy', $calendar) }}" method="POST" onsubmit="return confirm('Hapus kegiatan ini? Kegiatan juga akan dihapus dari Google Calendar.')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-rose-500 hover:text-rose-700 transition">Hapus</button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="px-5 py-10 text-center text-slate-500">Belum ada kegiatan akademik.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
