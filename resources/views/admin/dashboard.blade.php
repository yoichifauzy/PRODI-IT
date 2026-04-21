@extends('layouts.admin')

@section('title', 'Dashboard Admin')

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-slate-900">Dashboard</h1>
        <p class="text-sm text-slate-600">Ringkasan data konten website dan aspirasi mahasiswa.</p>
    </div>

    <div class="mb-6 grid gap-4 md:grid-cols-2 xl:grid-cols-3">
        <div class="rounded-xl border border-slate-200 bg-white p-4">
            <p class="text-sm text-slate-500">Total Pengumuman</p>
            <p class="mt-2 text-2xl font-bold">{{ $stats['announcement_total'] }}</p>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-4">
            <p class="text-sm text-slate-500">Pengumuman Publish</p>
            <p class="mt-2 text-2xl font-bold">{{ $stats['announcement_published'] }}</p>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-4">
            <p class="text-sm text-slate-500">Total Aspirasi</p>
            <p class="mt-2 text-2xl font-bold">{{ $stats['aspiration_total'] }}</p>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-4">
            <p class="text-sm text-slate-500">Aspirasi Belum Dibaca</p>
            <p class="mt-2 text-2xl font-bold">{{ $stats['aspiration_unread'] }}</p>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-4">
            <p class="text-sm text-slate-500">Total Event Akademik</p>
            <p class="mt-2 text-2xl font-bold">{{ $stats['event_total'] }}</p>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-4">
            <p class="text-sm text-slate-500">Event Publish</p>
            <p class="mt-2 text-2xl font-bold">{{ $stats['event_published'] }}</p>
        </div>
    </div>

    <div class="rounded-xl border border-slate-200 bg-white">
        <div class="border-b border-slate-200 px-4 py-3">
            <h2 class="font-semibold text-slate-900">Aspirasi Terbaru</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 text-left text-slate-600">
                    <tr>
                        <th class="px-4 py-3">Nama</th>
                        <th class="px-4 py-3">Subjek</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Waktu</th>
                        <th class="px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($latestAspirations as $aspiration)
                        <tr class="border-t border-slate-100">
                            <td class="px-4 py-3">{{ $aspiration->full_name }}</td>
                            <td class="px-4 py-3">{{ $aspiration->subject }}</td>
                            <td class="px-4 py-3">
                                <span class="rounded-full px-2 py-1 text-xs {{ $aspiration->status === 'unread' ? 'bg-amber-100 text-amber-700' : ($aspiration->status === 'read' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-200 text-slate-700') }}">
                                    {{ strtoupper($aspiration->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3">{{ $aspiration->created_at->format('d M Y H:i') }}</td>
                            <td class="px-4 py-3">
                                <a href="{{ route('admin.aspirations.show', $aspiration) }}" class="text-slate-900 underline">Lihat</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-slate-500">Belum ada aspirasi masuk.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
