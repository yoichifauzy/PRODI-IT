@extends('layouts.admin')

@section('title', 'Blog Dosen & Staff')

@section('content')
    <div class="mb-4 flex items-center justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Blog Dosen/Staff</h1>
            <p class="text-sm text-slate-600">Kelola kegiatan mengajar untuk {{ $lecturerStaff->name }}.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.lecturer-staff.index') }}" class="rounded-md border border-slate-300 px-4 py-2 text-sm text-slate-700">Kembali</a>
            <a href="{{ route('admin.lecturer-staff.blogs.create', $lecturerStaff) }}" class="rounded-md bg-slate-900 px-4 py-2 text-sm font-semibold text-white">+ Tambah Blog</a>
        </div>
    </div>

    <div class="rounded-xl border border-slate-200 bg-white">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 text-left text-slate-600">
                    <tr>
                        <th class="px-4 py-3">Judul</th>
                        <th class="px-4 py-3">Tanggal</th>
                        <th class="px-4 py-3">Slug</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($blogs as $blog)
                        <tr class="border-t border-slate-100">
                            <td class="px-4 py-3 font-semibold text-slate-900">{{ $blog->title }}</td>
                            <td class="px-4 py-3">{{ optional($blog->activity_date)->format('d M Y') ?: '-' }}</td>
                            <td class="px-4 py-3 text-xs text-slate-600">/{{ $blog->slug }}</td>
                            <td class="px-4 py-3">
                                <span class="rounded-full px-2 py-1 text-xs {{ $blog->is_published ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-200 text-slate-700' }}">
                                    {{ $blog->is_published ? 'PUBLISHED' : 'DRAFT' }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <a href="{{ route('admin.lecturer-staff.blogs.edit', [$lecturerStaff, $blog]) }}" class="text-slate-900 underline">Edit</a>
                                    <form method="POST" action="{{ route('admin.lecturer-staff.blogs.destroy', [$lecturerStaff, $blog]) }}" onsubmit="return confirm('Hapus blog ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-rose-600 underline">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-slate-500">Belum ada blog untuk dosen/staff ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-slate-200 px-4 py-3">
            {{ $blogs->links() }}
        </div>
    </div>
@endsection
