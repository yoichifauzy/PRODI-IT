@extends('layouts.admin')

@section('title', 'Kelola Project Mahasiswa')

@section('content')
    <div class="mb-4 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Project Mahasiswa</h1>
            <p class="text-sm text-slate-600">Kelola dokumentasi project mahasiswa (gambar, deskripsi, waktu).</p>
        </div>
        <a href="{{ route('admin.projects.create') }}" class="rounded-md bg-slate-900 px-4 py-2 text-sm font-semibold text-white">+ Tambah Project</a>
    </div>

    <form method="GET" action="{{ route('admin.projects.index') }}" class="mb-4 grid gap-3 rounded-xl border border-slate-200 bg-white p-4 md:grid-cols-[1fr_auto]">
        <input name="q" value="{{ $search }}" placeholder="Cari judul, nama, atau NIM..." class="rounded-md border border-slate-300 px-3 py-2" />
        <button type="submit" class="rounded-md bg-slate-900 px-4 py-2 text-sm font-semibold text-white">Cari</button>
    </form>

    <div class="rounded-xl border border-slate-200 bg-white">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 text-left text-slate-600">
                    <tr>
                        <th class="px-4 py-3">Judul</th>
                        <th class="px-4 py-3">Mahasiswa</th>
                        <th class="px-4 py-3">Dibuat</th>
                        <th class="px-4 py-3">Publish At</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($projects as $project)
                        <tr class="border-t border-slate-100">
                            <td class="px-4 py-3">
                                <p class="font-semibold text-slate-900">{{ $project->title }}</p>
                                <p class="text-xs text-slate-500">/{{ $project->slug }}</p>
                            </td>
                            <td class="px-4 py-3">
                                {{ $project->student_name }}
                                <p class="text-xs text-slate-500">{{ $project->student_nim ?: '-' }}</p>
                            </td>
                            <td class="px-4 py-3">{{ optional($project->created_at)->format('d M Y H:i') ?: '-' }}</td>
                            <td class="px-4 py-3">{{ optional($project->published_at)->format('d M Y H:i') ?: '-' }}</td>
                            <td class="px-4 py-3">
                                <span class="rounded-full px-2 py-1 text-xs {{ $project->status === 'published' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-200 text-slate-700' }}">{{ strtoupper($project->status) }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <a href="{{ route('admin.projects.edit', $project) }}" class="text-slate-900 underline">Edit</a>
                                    <form method="POST" action="{{ route('admin.projects.destroy', $project) }}" onsubmit="return confirm('Hapus project ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-rose-600 underline">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-slate-500">Belum ada project mahasiswa.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-slate-200 px-4 py-3">
            {{ $projects->links() }}
        </div>
    </div>
@endsection
