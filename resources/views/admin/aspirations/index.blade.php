@extends('layouts.admin')

@section('title', 'Inbox Aspirasi')

@section('content')
    <div class="mb-4">
        <h1 class="text-2xl font-bold text-slate-900">Inbox Aspirasi</h1>
        <p class="text-sm text-slate-600">Daftar pesan aspirasi yang masuk dari halaman publik.</p>
    </div>

    <form method="GET" action="{{ route('admin.aspirations.index') }}" class="mb-4 grid gap-3 rounded-xl border border-slate-200 bg-white p-4 md:grid-cols-[1fr_200px_auto]">
        <input name="q" value="{{ $search }}" placeholder="Cari nama, email, subjek, isi pesan..." class="rounded-md border border-slate-300 px-3 py-2 focus:border-slate-500 focus:outline-none" />
        <select name="status" class="rounded-md border border-slate-300 px-3 py-2 focus:border-slate-500 focus:outline-none">
            <option value="">Semua Status</option>
            <option value="unread" @selected($status === 'unread')>Unread</option>
            <option value="read" @selected($status === 'read')>Read</option>
            <option value="archived" @selected($status === 'archived')>Archived</option>
        </select>
        <button type="submit" class="rounded-md bg-slate-900 px-4 py-2 font-semibold text-white">Filter</button>
    </form>

    <div class="rounded-xl border border-slate-200 bg-white">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 text-left text-slate-600">
                    <tr>
                        <th class="px-4 py-3">Pengirim</th>
                        <th class="px-4 py-3">Subjek</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Waktu</th>
                        <th class="px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($aspirations as $aspiration)
                        <tr class="border-t border-slate-100">
                            <td class="px-4 py-3">
                                <p class="font-medium text-slate-900">{{ $aspiration->full_name }}</p>
                                <p class="text-xs text-slate-500">{{ $aspiration->email }}</p>
                            </td>
                            <td class="px-4 py-3">{{ \Illuminate\Support\Str::limit($aspiration->subject, 50) }}</td>
                            <td class="px-4 py-3">
                                <span class="rounded-full px-2 py-1 text-xs {{ $aspiration->status === 'unread' ? 'bg-amber-100 text-amber-700' : ($aspiration->status === 'read' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-200 text-slate-700') }}">
                                    {{ strtoupper($aspiration->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3">{{ $aspiration->created_at->format('d M Y H:i') }}</td>
                            <td class="px-4 py-3">
                                <a href="{{ route('admin.aspirations.show', $aspiration) }}" class="text-slate-900 underline">Buka</a>
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

        <div class="border-t border-slate-200 px-4 py-3">
            {{ $aspirations->links() }}
        </div>
    </div>
@endsection
