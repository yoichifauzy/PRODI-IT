@extends('layouts.admin')

@section('title', 'Detail Aspirasi')

@section('content')
    <div class="mb-4 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Detail Aspirasi</h1>
            <p class="text-sm text-slate-600">Tinjau isi pesan dan perbarui status penanganan.</p>
        </div>
        <a href="{{ route('admin.aspirations.index') }}" class="rounded-md border border-slate-300 px-4 py-2 text-slate-700">Kembali</a>
    </div>

    <div class="grid gap-4 lg:grid-cols-[1fr_300px]">
        <div class="rounded-xl border border-slate-200 bg-white p-6">
            <h2 class="mb-2 text-xl font-semibold text-slate-900">{{ $aspiration->subject }}</h2>
            <p class="mb-4 text-sm text-slate-500">Dikirim pada {{ $aspiration->created_at->format('d M Y H:i') }}</p>

            <div class="prose max-w-none whitespace-pre-line text-slate-700">
                {{ $aspiration->message }}
            </div>
        </div>

        <div class="space-y-4">
            <div class="rounded-xl border border-slate-200 bg-white p-4 text-sm">
                <p><span class="font-medium text-slate-700">Nama:</span> {{ $aspiration->full_name }}</p>
                <p class="mt-2"><span class="font-medium text-slate-700">Email:</span> {{ $aspiration->email }}</p>
                <p class="mt-2"><span class="font-medium text-slate-700">NIM:</span> {{ $aspiration->nim ?: '-' }}</p>
                <p class="mt-2"><span class="font-medium text-slate-700">Status:</span> {{ strtoupper($aspiration->status) }}</p>
                <p class="mt-2"><span class="font-medium text-slate-700">Dibaca pada:</span> {{ optional($aspiration->read_at)->format('d M Y H:i') ?: '-' }}</p>
            </div>

            <div class="rounded-xl border border-slate-200 bg-white p-4">
                <form method="POST" action="{{ route('admin.aspirations.update', $aspiration) }}" class="space-y-3">
                    @csrf
                    @method('PATCH')
                    <label for="status" class="block text-sm font-medium text-slate-700">Ubah Status</label>
                    <select id="status" name="status" class="w-full rounded-md border border-slate-300 px-3 py-2 focus:border-slate-500 focus:outline-none">
                        <option value="unread" @selected($aspiration->status === 'unread')>Unread</option>
                        <option value="read" @selected($aspiration->status === 'read')>Read</option>
                        <option value="archived" @selected($aspiration->status === 'archived')>Archived</option>
                    </select>
                    <button type="submit" class="w-full rounded-md bg-slate-900 px-4 py-2 text-sm font-semibold text-white">Simpan Status</button>
                </form>
            </div>

            <div class="rounded-xl border border-rose-200 bg-white p-4">
                <form method="POST" action="{{ route('admin.aspirations.destroy', $aspiration) }}" onsubmit="return confirm('Hapus aspirasi ini?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full rounded-md bg-rose-600 px-4 py-2 text-sm font-semibold text-white">Hapus Aspirasi</button>
                </form>
            </div>
        </div>
    </div>
@endsection
