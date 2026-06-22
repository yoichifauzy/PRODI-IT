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

    <form method="GET" action="{{ route('admin.curriculum-courses.index') }}" class="mb-4 grid gap-3 rounded-xl border border-slate-200 bg-white p-4 md:grid-cols-[1fr_auto_auto]">
        <select name="curriculum_id" class="rounded-md border border-slate-300 px-3 py-2">
            <option value="">Semua Kurikulum</option>
            @foreach ($curricula as $curriculum)
                <option value="{{ $curriculum->id }}" @selected((string) $curriculumId === (string) $curriculum->id)>
                    {{ $curriculum->name }}{{ $curriculum->major_selection ? ' - ' . $curriculum->major_selection : '' }}
                </option>
            @endforeach
        </select>
        <button type="submit" class="rounded-md bg-slate-900 px-4 py-2 text-sm font-semibold text-white">Filter</button>
        <a href="{{ route('admin.curriculum-courses.index') }}" class="rounded-md border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 text-center">Reset</a>
    </form>

    <div class="rounded-xl border border-slate-200 bg-white">
        <div class="flex flex-wrap items-center gap-3 border-b border-slate-100 px-5 py-3">
            <input type="text" id="courseSearch" placeholder="Cari matakuliah…" class="rounded-md border border-slate-300 px-3 py-1.5 text-sm" />
            <span class="ml-auto text-xs text-slate-500"><span id="courseTotal">{{ $courses->count() }}</span> matakuliah</span>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 text-left text-slate-600">
                    <tr>
                        <th class="px-4 py-3">No</th>
                        <th class="px-4 py-3">Kurikulum</th>
                        <th class="px-4 py-3">Kode</th>
                        <th class="px-4 py-3">Nama</th>
                        <th class="px-4 py-3">SKS Teori</th>
                        <th class="px-4 py-3">SKS Praktik</th>
                        <th class="px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody id="courseTbody">
                </tbody>
            </table>
        </div>

        <div id="coursePagination" class="flex flex-wrap items-center justify-center gap-1 border-t border-slate-200 px-5 py-3"></div>
    </div>
@endsection

@push('scripts')
<script>
    (function() {
        const PER_PAGE = 5;
        var courseData = {!! json_encode($courses->map(function($c) {
            return [
                'id' => $c->id,
                'curriculum_name' => $c->curriculum?->name ?: '-',
                'code' => $c->code,
                'name' => $c->name,
                'credits_theory' => $c->credits_theory,
                'credits_practice' => $c->credits_practice,
                'edit_url' => route('admin.curriculum-courses.edit', $c),
                'delete_url' => route('admin.curriculum-courses.destroy', $c),
            ];
        })) !!};
        var currentPage = 1;

        function filterData(data, searchText) {
            if (!searchText) return data;
            var lower = searchText.toLowerCase();
            return data.filter(function(item) {
                return (item.code && item.code.toLowerCase().indexOf(lower) !== -1) ||
                       (item.name && item.name.toLowerCase().indexOf(lower) !== -1) ||
                       (item.curriculum_name && item.curriculum_name.toLowerCase().indexOf(lower) !== -1);
            });
        }

        function renderTable() {
            var searchText = document.getElementById('courseSearch').value;
            var data = filterData(courseData, searchText);
            var totalPages = Math.ceil(data.length / PER_PAGE) || 1;
            if (currentPage > totalPages) currentPage = totalPages;
            if (currentPage < 1) currentPage = 1;

            var start = (currentPage - 1) * PER_PAGE;
            var pageItems = data.slice(start, start + PER_PAGE);
            var tbody = document.getElementById('courseTbody');
            var html = '';

            if (pageItems.length === 0) {
                html = '<tr><td colspan="7" class="px-4 py-8 text-center text-slate-500">Belum ada data mata kuliah.</td></tr>';
            } else {
                for (var i = 0; i < pageItems.length; i++) {
                    var item = pageItems[i];
                    var num = start + i + 1;
                    html += '<tr class="border-t border-slate-100 hover:bg-slate-50">' +
                        '<td class="px-4 py-3">' + num + '</td>' +
                        '<td class="px-4 py-3">' + item.curriculum_name + '</td>' +
                        '<td class="px-4 py-3 font-mono text-xs">' + item.code + '</td>' +
                        '<td class="px-4 py-3">' + item.name + '</td>' +
                        '<td class="px-4 py-3 text-center">' + item.credits_theory + '</td>' +
                        '<td class="px-4 py-3 text-center">' + item.credits_practice + '</td>' +
                        '<td class="px-4 py-3"><div class="flex items-center gap-3">' +
                        '<a href="' + item.edit_url + '" class="text-slate-900 underline">Edit</a>' +
                        '<form method="POST" action="' + item.delete_url + '" onsubmit="return confirm(\'Hapus mata kuliah ini?\')" style="display:inline">' +
                        '<input type="hidden" name="_token" value="' + document.querySelector('meta[name="csrf-token"]').content + '">' +
                        '<input type="hidden" name="_method" value="DELETE">' +
                        '<button type="submit" class="text-rose-600 underline">Hapus</button>' +
                        '</form></div></td></tr>';
                }
            }
            tbody.innerHTML = html;
            document.getElementById('courseTotal').textContent = data.length;

            // Pagination
            var pagContainer = document.getElementById('coursePagination');
            if (totalPages <= 1) { pagContainer.innerHTML = ''; return; }

            var pagHtml = '';
            pagHtml += '<button class="px-2 py-1 rounded border border-slate-300 ' + (currentPage === 1 ? 'text-slate-300 cursor-not-allowed' : 'text-slate-600 hover:bg-slate-100') + '"' + (currentPage === 1 ? ' disabled' : '') + ' data-page="' + (currentPage - 1) + '">&laquo;</button>';
            for (var p = 1; p <= totalPages; p++) {
                if (p === currentPage) {
                    pagHtml += '<span class="px-3 py-1 rounded bg-indigo-600 text-white font-semibold">' + p + '</span>';
                } else {
                    pagHtml += '<button class="px-3 py-1 rounded border border-slate-300 text-slate-600 hover:bg-slate-100" data-page="' + p + '">' + p + '</button>';
                }
            }
            pagHtml += '<button class="px-2 py-1 rounded border border-slate-300 ' + (currentPage === totalPages ? 'text-slate-300 cursor-not-allowed' : 'text-slate-600 hover:bg-slate-100') + '"' + (currentPage === totalPages ? ' disabled' : '') + ' data-page="' + (currentPage + 1) + '">&raquo;</button>';
            pagContainer.innerHTML = pagHtml;
        }

        document.getElementById('courseSearch').addEventListener('input', function() {
            currentPage = 1;
            renderTable();
        });

        document.getElementById('coursePagination').addEventListener('click', function(e) {
            var btn = e.target.closest('button');
            if (btn && btn.dataset.page) {
                currentPage = parseInt(btn.dataset.page);
                renderTable();
            }
        });

        renderTable();
    })();
</script>
@endpush
