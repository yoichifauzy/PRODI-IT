@extends('layouts.admin')

@section('title', 'Kelola Kalender Akademik')

@section('content')
    <div class="mb-4 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Kalender Akademik</h1>
            <p class="text-sm text-slate-600">Kelola event kalender ala Google Calendar untuk ditampilkan ke publik.</p>
        </div>
        <a href="{{ route('admin.academic-events.create') }}" class="rounded-md bg-slate-900 px-4 py-2 text-sm font-semibold text-white">+ Tambah Event</a>
    </div>

    <form method="GET" action="{{ route('admin.academic-events.index') }}" class="mb-4 grid gap-3 rounded-xl border border-slate-200 bg-white p-4 md:grid-cols-[1fr_180px_auto]">
        <input name="q" value="{{ $search }}" placeholder="Cari judul, deskripsi, lokasi..." class="rounded-md border border-slate-300 px-3 py-2 focus:border-slate-500 focus:outline-none" />
        <input type="month" name="month" value="{{ $month->format('Y-m') }}" class="rounded-md border border-slate-300 px-3 py-2 focus:border-slate-500 focus:outline-none" />
        <button type="submit" class="rounded-md bg-slate-900 px-4 py-2 text-sm font-semibold text-white">Filter</button>
    </form>

    <div class="rounded-xl border border-slate-200 bg-white">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 text-left text-slate-600">
                    <tr>
                        <th class="px-4 py-3">Judul</th>
                        <th class="px-4 py-3">Jenis</th>
                        <th class="px-4 py-3">Periode</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($events as $event)
                        <tr class="border-t border-slate-100">
                            <td class="px-4 py-3">
                                <p class="font-medium text-slate-900">{{ $event->title }}</p>
                                <p class="text-xs text-slate-500">/{{ $event->slug }}</p>
                            </td>
                            <td class="px-4 py-3">{{ strtoupper($event->event_type) }}</td>
                            <td class="px-4 py-3">
                                {{ $event->start_date->format('d M Y') }}
                                @if ($event->end_date)
                                    - {{ $event->end_date->format('d M Y') }}
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <span class="rounded-full px-2 py-1 text-xs {{ $event->is_published ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-200 text-slate-700' }}">
                                    {{ $event->is_published ? 'PUBLISHED' : 'DRAFT' }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <a href="{{ route('admin.academic-events.edit', $event) }}" class="text-slate-900 underline">Edit</a>
                                    <form method="POST" action="{{ route('admin.academic-events.destroy', $event) }}" onsubmit="return confirm('Hapus event ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-rose-600 underline">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-slate-500">Belum ada event di bulan ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-slate-200 px-4 py-3">
            {{ $events->links() }}
        </div>
    </div>
@endsection
