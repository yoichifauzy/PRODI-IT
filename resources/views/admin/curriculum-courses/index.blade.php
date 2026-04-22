@extends('layouts.admin')

@section('title', 'Kelola Mata Kuliah Kurikulum')

@section('content')
    <div class="mb-4 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Mata Kuliah Kurikulum</h1>
            <p class="text-sm text-slate-600">CRUD detail mata kuliah untuk setiap kurikulum.</p>
        </div>
        <a href="{{ route('admin.curriculum-courses.create') }}" class="rounded-md bg-slate-900 px-4 py-2 text-sm font-semibold text-white">+ Tambah MK</a>
    </div>

    <form method="GET" action="{{ route('admin.curriculum-courses.index') }}" class="mb-4 grid gap-3 rounded-xl border border-slate-200 bg-white p-4 md:grid-cols-[1fr_auto]">
        <select name="curriculum_id" class="rounded-md border border-slate-300 px-3 py-2">
            <option value="">Semua Kurikulum</option>
            @foreach ($curricula as $curriculum)
                <option value="{{ $curriculum->id }}" @selected((string) $curriculumId === (string) $curriculum->id)>
                    {{ $curriculum->name }}{{ $curriculum->major_selection ? ' - ' . $curriculum->major_selection : '' }}
                </option>
            @endforeach
        </select>
        <button type="submit" class="rounded-md bg-slate-900 px-4 py-2 text-sm font-semibold text-white">Filter</button>
    </form>

    <div class="rounded-xl border border-slate-200 bg-white">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 text-left text-slate-600">
                    <tr>
                        <th class="px-4 py-3">Kurikulum</th>
                        <!-- <th class="px-4 py-3">Semester</th> -->
                        <th class="px-4 py-3">Kode</th>
                        <th class="px-4 py-3">Nama</th>
                        <th class="px-4 py-3">SKS Teori</th>
                        <th class="px-4 py-3">SKS Praktik</th>
                        <th class="px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($courses as $course)
                        <tr class="border-t border-slate-100">
                            <td class="px-4 py-3">{{ $course->curriculum?->name ?: '-' }}</td>
                            <!-- <td class="px-4 py-3">{{ $course->semester }}</td> -->
                            <td class="px-4 py-3">{{ $course->code }}</td>
                            <td class="px-4 py-3">{{ $course->name }}</td>
                            <td class="px-4 py-3">{{ $course->credits_theory }}</td>
                            <td class="px-4 py-3">{{ $course->credits_practice }}</td>
                            <td class="px-4 py-3\">
                                <div class="flex items-center gap-3">
                                    <a href="{{ route('admin.curriculum-courses.edit', $course) }}" class="text-slate-900 underline">Edit</a>
                                    <form method="POST" action="{{ route('admin.curriculum-courses.destroy', $course) }}" onsubmit="return confirm('Hapus mata kuliah ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-rose-600 underline">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-slate-500">Belum ada data mata kuliah.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-slate-200 px-4 py-3">
            {{ $courses->links() }}
        </div>
    </div>
@endsection