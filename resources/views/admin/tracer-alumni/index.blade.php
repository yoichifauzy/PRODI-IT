@extends('layouts.admin')

@section('title', 'Tracer Alumni')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="text-xl font-bold text-slate-900">Tracer Alumni</h1>
        <p class="text-sm text-slate-400">Sync dari sheet tracer-alumni.</p>
    </div>
    <div>
        <form action="{{ route('admin.tracer-alumni.sync') }}" method="POST" class="flex flex-col gap-3 sm:flex-row sm:items-center">
            @csrf
            {{-- Dropdown (Style disamakan dengan input search Kegiatan) --}}
            <select name="document_id" id="document_id" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700 focus:border-slate-900 focus:outline-none focus:ring-1 focus:ring-slate-900 sm:w-64">
                <option value="">-- Pilih Dokumen --</option>
                @foreach($docs as $doc)
                    <option value="{{ $doc->id }}" {{ ($defaultDoc && $defaultDoc->id === $doc->id) ? 'selected' : '' }}>
                        {{ $doc->name }}
                    </option>
                @endforeach
            </select>

            {{-- Action Button (Style disamakan dengan tombol Tambah Kegiatan) --}}
            <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white shadow-sm transition-all hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-slate-900 focus:ring-offset-2">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                </svg>
                Sync Sekarang
            </button>
        </form>
    </div>
</div>

    
{{-- Tracer Alumni Banner Section --}}
<div class="mb-6 rounded-xl border border-slate-200 bg-white p-6 shadow-sm relative overflow-hidden">
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-base font-bold text-slate-800">Foto / Banner Alumni (Marquee)</h2>
        <button type="button" onclick="document.getElementById('banner-upload-input').click()" class="inline-flex items-center justify-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700 transition">
            <i class="fa-solid fa-plus"></i> Tambah Foto
        </button>
    </div>

    @if($banners->count() > 0)
        <div id="banner-grid" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
            @foreach($banners as $b)
                <div data-id="{{ $b->id }}" class="relative group aspect-[4/3] rounded-xl overflow-hidden border border-slate-200 bg-slate-100 cursor-move">
                    <img src="{{ asset('storage/' . $b->image_path) }}" alt="Banner Alumni" class="w-full h-full object-cover">
                    <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity flex flex-col items-center justify-center">
                        <form action="{{ route('admin.tracer-alumni.banner.destroy', $b) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded-lg font-semibold shadow-lg hover:bg-red-600 transition text-sm">
                                <i class="fa-solid fa-trash"></i> Hapus
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-10 bg-slate-50 border-2 border-dashed border-slate-300 rounded-xl">
            <i class="fa-solid fa-image text-4xl mb-3 text-slate-300"></i>
            <p class="text-slate-500 font-medium">Belum ada foto banner yang diunggah.</p>
            <p class="text-sm text-slate-400 mt-1">Anda bisa memilih banyak file sekaligus saat mengunggah.</p>
        </div>
    @endif
    
    <form action="{{ route('admin.tracer-alumni.banner.update') }}" method="POST" enctype="multipart/form-data" id="banner-form" class="hidden">
        @csrf
        <input type="file" name="images[]" id="banner-upload-input" accept="image/*" multiple onchange="document.getElementById('banner-form').submit()">
    </form>
</div>
{{-- Filter Bar --}}
<div class="mb-4">
    <div class="flex flex-wrap items-center gap-2">
        <input type="text" id="search-input" placeholder="Cari NIM / Nama / Perusahaan..."
               class="w-full sm:w-64 rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700 placeholder-slate-400 focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500">

</div>
</div>

{{-- Data Table --}}
<div class="rounded-xl border border-slate-200 bg-white overflow-hidden shadow-sm">
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="border-b border-slate-200 bg-slate-50 text-slate-600">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Tahun</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">NIM</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Nama</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Penempatan</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Departemen</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Kesesuaian</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Contact</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 bg-white" id="table-alumni">
                @forelse($alumni as $a)
                <tr class="hover:bg-slate-50 transition-colors js-row-alumni" data-year="{{ $a->graduation_year }}">
                    <td class="px-4 py-3 text-slate-500 text-xs font-medium">{{ $a->graduation_year }}</td>
                    <td class="px-4 py-3 font-mono text-slate-700 text-xs">{{ $a->nim }}</td>
                    <td class="px-4 py-3 font-medium text-slate-900 text-xs">{{ $a->name }}</td>
                    <td class="px-4 py-3 text-slate-600 text-xs">{{ $a->company_name }}</td>
                    <td class="px-4 py-3 text-slate-500 text-xs">{{ $a->department }}</td>
                    <td class="px-4 py-3 text-slate-500 text-xs">
                        <span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-xs font-medium text-slate-600">
                            {{ $a->relevance }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-slate-400 text-xs">{{ $a->contact }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-4 py-16 text-center text-slate-400 text-sm">
                        <div class="flex flex-col items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-8 w-8 text-slate-300 mb-2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                            </svg>
                            <span>Belum ada data alumni. Silakan lakukan sinkronisasi dokumen terlebih dahulu.</span>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div id="pag-alumni" class="border-t border-slate-200 bg-slate-50/50 px-4 py-3 flex justify-end flex-wrap gap-2"></div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    // Inisialisasi SortableJS untuk Drag and Drop Banner
    const bannerGrid = document.getElementById('banner-grid');
    if (bannerGrid) {
        new Sortable(bannerGrid, {
            animation: 150,
            ghostClass: 'opacity-50',
            onEnd: function (evt) {
                const order = Array.from(bannerGrid.children).map(item => item.dataset.id);
                fetch("{{ route('admin.tracer-alumni.banner.reorder') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ order: order })
                }).then(response => {
                    if(!response.ok) alert('Gagal mengurutkan banner');
                });
            }
        });
    }

    const searchInput = document.getElementById('search-input');
    // Tambahkan pengecekan null untuk filter agar tidak crash kalau ID belum ada
    const yearFilter = document.getElementById('year-filter');
    const pagContainer = document.getElementById('pag-alumni');
    const rows = Array.from(document.querySelectorAll('.js-row-alumni'));

    let currentPage = 1;
    const perPage = 10; // Sesuai permintaan Anda

    function update() {
        const query = searchInput ? searchInput.value.toLowerCase() : '';
        const year = yearFilter ? yearFilter.value : '';

        // Filter baris
        const filtered = rows.filter(r => {
            const matchesQuery = query === '' || r.textContent.toLowerCase().includes(query);
            const matchesYear = year === '' || r.dataset.year === year;
            return matchesQuery && matchesYear;
        });

        // Sembunyikan semua dulu
        rows.forEach(r => r.classList.add('hidden'));

        // Hitung total halaman
        const totalPages = Math.max(1, Math.ceil(filtered.length / perPage));
        if (currentPage > totalPages) currentPage = totalPages;

        // Tampilkan baris untuk halaman ini
        const start = (currentPage - 1) * perPage;
        const visible = filtered.slice(start, start + perPage);

        visible.forEach((r, i) => {
            r.classList.remove('hidden');
            // Update nomor urut di tabel
            const numCell = r.querySelector('.js-row-number');
            if (numCell) numCell.textContent = start + i + 1;
        });

        renderPag(totalPages, currentPage, (p) => {
            currentPage = p;
            update();
        });
    }

    function renderPag(total, current, callback) {
        if (total <= 1) { pagContainer.innerHTML = ''; return; }

        // Styling disesuaikan dengan contoh kedua (warna orange untuk aktif)
        const buttons = Array.from({length: total}, (_, i) => i + 1).map(p => `
            <button type="button" 
                class="px-3 py-1 text-sm font-medium border rounded transition-colors ${p === current ? 'bg-slate-600 text-white border-slate-600' : 'bg-white text-slate-700 border-slate-200 hover:bg-slate-50'}" 
                data-p="${p}">
                ${p}
            </button>
        `).join('');

        pagContainer.innerHTML = `<div class="flex gap-2">${buttons}</div>`;

        pagContainer.querySelectorAll('button').forEach(btn => {
            btn.onclick = (e) => {
                e.preventDefault();
                callback(parseInt(btn.dataset.p));
            };
        });
    }

    if (searchInput) {
        searchInput.addEventListener('input', () => {
            currentPage = 1;
            update();
        });
    }

    if (yearFilter) {
        yearFilter.addEventListener('change', () => {
            currentPage = 1;
            update();
        });
    }

    // Init
    update();
});
</script>
@endpush
@endsection