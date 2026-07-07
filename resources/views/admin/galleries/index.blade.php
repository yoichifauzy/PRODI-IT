@extends('layouts.admin')

@section('title', 'Kelola Galeri')

@section('content')
<style>
    /* Drag handle cursor */
    .gallery-card[draggable="true"] { cursor: grab; }
    .gallery-card[draggable="true"]:active { cursor: grabbing; }
    /* Dragging ghost */
    .gallery-card.is-dragging { opacity: 0.4; }
    /* Drop zone active */
    .gallery-card.drag-over { outline: 2px dashed #708090 ; outline-offset: 2px; }
</style>

<div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="text-2xl font-bold text-slate-800">Kelola Galeri</h1>
        <p class="text-sm text-slate-500">Upload dan kelola foto kegiatan. Drag card untuk ubah urutan. Edit langsung di card.</p>
    </div>
    <button type="button" onclick="openUploadModal()"
            class="inline-flex items-center gap-2 rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-700 transition">
        <i class="fa-solid fa-cloud-arrow-up"></i>
        <span>Upload Gambar</span>
    </button>
</div>

{{-- Alert --}}
<div id="js-alert" class="hidden mb-4"></div>

{{-- Filter Bar --}}
<div class="mb-5 flex flex-wrap items-center gap-3">
    <input type="text" id="filter-search" placeholder="Cari judul / kategori…"
           class="w-full sm:w-64 rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm placeholder-slate-400 focus:outline-none focus:ring-1 focus:ring-slate-400">

    <select id="filter-category"
            class="rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700 focus:outline-none focus:ring-1 focus:ring-slate-400">
        <option value="">Semua Kategori</option>
        @foreach($categories as $cat)
            <option value="{{ strtolower($cat) }}">{{ $cat }}</option>
        @endforeach
    </select>

    <select id="filter-year"
            class="rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700 focus:outline-none focus:ring-1 focus:ring-slate-400">
        <option value="">Semua Tahun</option>
        @foreach($years as $y)
            <option value="{{ $y }}">{{ $y }}</option>
        @endforeach
    </select>

    <span class="ml-auto text-xs text-slate-400"><span id="count-label">{{ $galleries->total() }}</span> foto</span>
</div>

{{-- Card Grid --}}
<div id="gallery-grid" class="grid gap-5 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5">
    @forelse($galleries as $item)
    <div class="gallery-card group relative flex flex-col overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm hover:shadow-md transition-shadow select-none"
         draggable="true"
         data-id="{{ $item->id }}"
         data-pos="{{ $item->position }}"
         data-title="{{ strtolower($item->title) }}"
         data-category="{{ strtolower($item->category) }}"
         data-year="{{ $item->year }}">

        {{-- Gambar --}}
        <div class="aspect-square overflow-hidden bg-slate-100 flex-shrink-0">
            <img src="{{ asset('storage/' . $item->image_path) }}" alt="{{ $item->title }}"
                 draggable="false"
                 class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-110">
        </div>

        {{-- Tombol hapus — selalu tampil saat hover, jelas dengan label --}}
        <button type="button"
                onclick="deleteGallery({{ $item->id }}, this); event.stopPropagation();"
                title="Hapus gambar"
                class="delete-btn absolute right-2 top-2 z-20 flex items-center gap-1.5 rounded-md bg-rose-600 px-2 py-1 text-xs font-semibold text-white shadow-md opacity-0 group-hover:opacity-100 transition-opacity hover:bg-rose-700">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
            </svg>
            <span>Hapus</span>
        </button>

        {{-- Drag handle indicator --}}
        <div class="absolute left-2 top-2 z-10 flex h-6 w-6 items-center justify-center rounded-md bg-black/30 text-white opacity-0 group-hover:opacity-100 transition-opacity" title="Drag untuk pindah posisi">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="currentColor" viewBox="0 0 24 24">
                <path d="M11 5a2 2 0 1 1-4 0 2 2 0 0 1 4 0zm6 0a2 2 0 1 1-4 0 2 2 0 0 1 4 0zM5 11a2 2 0 1 1-4 0 2 2 0 0 1 4 0zm6 0a2 2 0 1 1-4 0 2 2 0 0 1 4 0zm6 0a2 2 0 1 1-4 0 2 2 0 0 1 4 0zM5 17a2 2 0 1 1-4 0 2 2 0 0 1 4 0zm6 0a2 2 0 1 1-4 0 2 2 0 0 1 4 0zm6 0a2 2 0 1 1-4 0 2 2 0 0 1 4 0z"/>
            </svg>
        </div>

        {{-- Meta editable --}}
        <div class="p-3 space-y-1 flex-1">
            <input type="text" value="{{ $item->title }}"
                   class="card-field w-full rounded border-0 border-b border-transparent bg-transparent text-sm font-medium text-slate-800 focus:border-slate-400 focus:outline-none hover:border-slate-200 transition"
                   data-field="title"
                   placeholder="Judul"
                   onfocus="this.closest('.gallery-card').setAttribute('draggable','false')"
                   onblur="this.closest('.gallery-card').setAttribute('draggable','true'); updateGallery({{ $item->id }}, this.dataset.field, this.value)">

            <div class="flex gap-1">
                <input type="text" value="{{ $item->category }}"
                       class="card-field w-full rounded border-0 border-b border-transparent bg-transparent text-xs text-slate-500 focus:border-slate-400 focus:outline-none hover:border-slate-200 transition"
                       data-field="category"
                       placeholder="Kategori"
                       onfocus="this.closest('.gallery-card').setAttribute('draggable','false')"
                       onblur="this.closest('.gallery-card').setAttribute('draggable','true'); updateGallery({{ $item->id }}, this.dataset.field, this.value)">

                <input type="number" value="{{ $item->year }}"
                       class="card-field w-20 rounded border-0 border-b border-transparent bg-transparent text-xs text-slate-500 text-right focus:border-slate-400 focus:outline-none hover:border-slate-200 transition"
                       data-field="year" min="2000" max="2099"
                       placeholder="Tahun"
                       onfocus="this.closest('.gallery-card').setAttribute('draggable','false')"
                       onblur="this.closest('.gallery-card').setAttribute('draggable','true'); updateGallery({{ $item->id }}, this.dataset.field, this.value)">
            </div>
        </div>
    </div>
    @empty
    <div id="empty-state" class="col-span-full py-20 text-center text-slate-400">
        <i class="fa-regular fa-image text-4xl mb-3 block"></i>
        <p class="text-sm">Belum ada gambar. Klik <strong>Upload Gambar</strong> untuk menambahkan.</p>
    </div>
    @endforelse
</div>

{{-- Pagination --}}
@if($galleries->hasPages())
<div class="mt-8 flex justify-center">
    {{ $galleries->links() }}
</div>
@endif

{{-- ===== MODAL UPLOAD ===== --}}
<div id="uploadModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/60 backdrop-blur-sm">
    <div class="relative mx-4 w-full max-w-lg rounded-2xl bg-white p-6 shadow-2xl" onclick="event.stopPropagation()">
        <div class="mb-5 flex items-center justify-between border-b border-slate-100 pb-4">
            <h3 class="text-lg font-bold text-slate-800">Upload Gambar Baru</h3>
            <button type="button" onclick="closeUploadModal()" class="text-slate-400 hover:text-slate-600">
                <i class="fa-solid fa-xmark text-xl"></i>
            </button>
        </div>

        {{-- Form fields --}}
        <div class="mb-4 flex gap-3">
            <div class="flex-1">
                <label class="mb-1 block text-xs font-semibold text-slate-600">Judul <span class="font-normal text-slate-400">(opsional)</span></label>
                <input type="text" id="upload-title" placeholder="e.g. Wisuda 2026"
                       class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-slate-400">
            </div>
            <div class="w-36">
                <label class="mb-1 block text-xs font-semibold text-slate-600">Kategori <span class="text-rose-500">*</span></label>
                <input type="text" id="upload-category" placeholder="e.g. wisuda"
                       list="category-suggestions"
                       class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-slate-400">
                <datalist id="category-suggestions">
                    @foreach($categories as $cat)
                        <option value="{{ $cat }}">
                    @endforeach
                </datalist>
            </div>
        </div>

        {{-- Drop zone --}}
        <div id="drop-zone"
             ondragover="event.preventDefault(); this.classList.add('border-slate-400','bg-slate-50')"
             ondragleave="this.classList.remove('border-slate-400','bg-slate-50')"
             ondrop="handleDrop(event)"
             onclick="document.getElementById('file-input').click()"
             class="flex h-44 cursor-pointer flex-col items-center justify-center rounded-xl border-2 border-dashed border-slate-300 bg-slate-50 transition hover:border-slate-400 hover:bg-slate-50">
            <i class="fa-solid fa-cloud-arrow-up text-3xl text-slate-400 mb-2"></i>
            <p class="text-sm font-medium text-slate-700">Drag & drop atau <span class="text-slate-600 underline">pilih file</span></p>
            <p class="mt-1 text-xs text-slate-400" id="selected-label">JPG, PNG, WEBP — max 10MB per file</p>
        </div>
        <input type="file" id="file-input" multiple accept="image/*" class="hidden" onchange="handleFileSelect(this.files)">

        {{-- Progress --}}
        <div id="upload-progress-wrap" class="mt-4 hidden">
            <div class="mb-1 flex justify-between text-xs text-slate-500">
                <span>Mengunggah…</span>
                <span id="progress-pct">0%</span>
            </div>
            <div class="h-2 w-full overflow-hidden rounded-full bg-slate-200">
                <div id="progress-bar" class="h-full rounded-full bg-slate-500 transition-all" style="width:0%"></div>
            </div>
        </div>

        <div class="mt-5 flex justify-end gap-3 border-t border-slate-100 pt-4">
            <button type="button" onclick="closeUploadModal()" class="rounded-lg px-4 py-2 text-sm text-slate-600 hover:bg-slate-100">Batal</button>
            <button type="button" id="btn-upload" onclick="doUpload()"
                    class="rounded-lg bg-slate-600 px-5 py-2 text-sm font-semibold text-white hover:bg-slate-700 disabled:opacity-50 transition">
                Upload Semua
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const CSRF = '{{ csrf_token() }}';
let selectedFiles = [];

// ── Upload modal ───────────────────────────────────────────────────────
function openUploadModal() {
    document.getElementById('uploadModal').classList.remove('hidden');
    document.getElementById('uploadModal').classList.add('flex');
    document.body.style.overflow = 'hidden';
}
function closeUploadModal() {
    document.getElementById('uploadModal').classList.add('hidden');
    document.getElementById('uploadModal').classList.remove('flex');
    document.body.style.overflow = '';
    selectedFiles = [];
    document.getElementById('file-input').value = '';
    document.getElementById('selected-label').textContent = 'JPG, PNG, WEBP — max 10MB per file';
    document.getElementById('upload-progress-wrap').classList.add('hidden');
    document.getElementById('progress-bar').style.width = '0%';
}
document.getElementById('uploadModal').addEventListener('click', function(e) {
    if (e.target === this) closeUploadModal();
});

function handleDrop(e) {
    e.preventDefault();
    document.getElementById('drop-zone').classList.remove('border-slate-400','bg-slate-50');
    handleFileSelect(e.dataTransfer.files);
}
function handleFileSelect(files) {
    selectedFiles = Array.from(files);
    document.getElementById('selected-label').textContent = selectedFiles.length + ' file dipilih';
}

async function doUpload() {
    const category = document.getElementById('upload-category').value.trim();
    if (!category) { showAlert('Kategori wajib diisi.', 'error'); return; }
    if (!selectedFiles.length) { showAlert('Pilih setidaknya 1 gambar.', 'error'); return; }

    const btn = document.getElementById('btn-upload');
    btn.disabled = true;

    const wrap = document.getElementById('upload-progress-wrap');
    const bar  = document.getElementById('progress-bar');
    const pct  = document.getElementById('progress-pct');
    wrap.classList.remove('hidden');

    const fd = new FormData();
    selectedFiles.forEach(f => fd.append('images[]', f));
    fd.append('category', category);
    const title = document.getElementById('upload-title').value.trim();
    if (title) fd.append('title', title);
    fd.append('_token', CSRF);

    const xhr = new XMLHttpRequest();
    xhr.upload.onprogress = (e) => {
        if (e.lengthComputable) {
            const p = Math.round(e.loaded / e.total * 100);
            bar.style.width = p + '%';
            pct.textContent = p + '%';
        }
    };
    xhr.onload = function() {
        btn.disabled = false;
        try {
            const data = JSON.parse(xhr.responseText);
            if (xhr.status === 200 && data.success) {
                showAlert(data.message, 'success');
                prependCards(data.images);
                closeUploadModal();
                updateCount(data.images.length, 'add');
            } else if (xhr.status === 422) {
                let msgs = Object.values(data.errors || {}).flat();
                let alertMsg = msgs.length > 0 ? msgs.join(', ') : (data.message || 'Data tidak valid.');
                showAlert(alertMsg, 'error');
            } else {
                showAlert(data.message || 'Upload gagal.', 'error');
            }
        } catch(e) {
            showAlert('Terjadi kesalahan saat upload.', 'error');
        }
    };
    xhr.onerror = () => { btn.disabled = false; showAlert('Gagal terhubung ke server.', 'error'); };
    xhr.open('POST', '{{ route("admin.galleries.store") }}');
    xhr.setRequestHeader('Accept', 'application/json');
    xhr.send(fd);
}

// ── Buat HTML card (dipakai oleh prependCards dan initDragDrop) ────────────
function makeCardHTML(img) {
    return `
        <div class="aspect-square overflow-hidden bg-slate-100 flex-shrink-0">
            <img src="${img.image_url}" alt="${escHtml(img.title)}" draggable="false"
                 class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-110">
        </div>
        <button type="button" onclick="deleteGallery(${img.id}, this); event.stopPropagation();" title="Hapus gambar"
                class="delete-btn absolute right-2 top-2 z-20 flex items-center gap-1.5 rounded-md bg-rose-600 px-2 py-1 text-xs font-semibold text-white shadow-md opacity-0 group-hover:opacity-100 transition-opacity hover:bg-rose-700">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
            </svg>
            <span>Hapus</span>
        </button>
        <div class="absolute left-2 top-2 z-10 flex h-6 w-6 items-center justify-center rounded-md bg-black/30 text-white opacity-0 group-hover:opacity-100 transition-opacity">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="currentColor" viewBox="0 0 24 24">
                <path d="M11 5a2 2 0 1 1-4 0 2 2 0 0 1 4 0zm6 0a2 2 0 1 1-4 0 2 2 0 0 1 4 0zM5 11a2 2 0 1 1-4 0 2 2 0 0 1 4 0zm6 0a2 2 0 1 1-4 0 2 2 0 0 1 4 0zm6 0a2 2 0 1 1-4 0 2 2 0 0 1 4 0zM5 17a2 2 0 1 1-4 0 2 2 0 0 1 4 0zm6 0a2 2 0 1 1-4 0 2 2 0 0 1 4 0zm6 0a2 2 0 1 1-4 0 2 2 0 0 1 4 0z"/>
            </svg>
        </div>
        <div class="p-3 space-y-1 flex-1">
            <input type="text" value="${escHtml(img.title)}"
                   class="card-field w-full rounded border-0 border-b border-transparent bg-transparent text-sm font-medium text-slate-800 focus:border-slate-400 focus:outline-none hover:border-slate-200 transition"
                   data-field="title" placeholder="Judul"
                   onfocus="this.closest('.gallery-card').setAttribute('draggable','false')"
                   onblur="this.closest('.gallery-card').setAttribute('draggable','true'); updateGallery(${img.id}, this.dataset.field, this.value)">
            <div class="flex gap-1">
                <input type="text" value="${escHtml(img.category)}"
                       class="card-field w-full rounded border-0 border-b border-transparent bg-transparent text-xs text-slate-500 focus:border-slate-400 focus:outline-none hover:border-slate-200 transition"
                       data-field="category" placeholder="Kategori"
                       onfocus="this.closest('.gallery-card').setAttribute('draggable','false')"
                       onblur="this.closest('.gallery-card').setAttribute('draggable','true'); updateGallery(${img.id}, this.dataset.field, this.value)">
                <input type="number" value="${img.year}"
                       class="card-field w-20 rounded border-0 border-b border-transparent bg-transparent text-xs text-slate-500 text-right focus:border-slate-400 focus:outline-none hover:border-slate-200 transition"
                       data-field="year" min="2000" max="2099" placeholder="Tahun"
                       onfocus="this.closest('.gallery-card').setAttribute('draggable','false')"
                       onblur="this.closest('.gallery-card').setAttribute('draggable','true'); updateGallery(${img.id}, this.dataset.field, this.value)">
            </div>
        </div>`;
}

function escHtml(str) {
    return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

function prependCards(images) {
    const grid = document.getElementById('gallery-grid');
    const empty = document.getElementById('empty-state');
    if (empty) empty.remove();

    images.slice().reverse().forEach(img => {
        const div = document.createElement('div');
        div.className = 'gallery-card group relative flex flex-col overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm hover:shadow-md transition-shadow select-none';
        div.setAttribute('draggable', 'true');
        div.dataset.id       = img.id;
        div.dataset.pos      = img.position ?? 0;
        div.dataset.title    = img.title.toLowerCase();
        div.dataset.category = img.category.toLowerCase();
        div.dataset.year     = img.year;
        div.innerHTML = makeCardHTML(img);
        grid.prepend(div);
        initCardDrag(div);
    });

    applyFilters();
}

// ── Inline update ─────────────────────────────────────────────────────────
async function updateGallery(id, field, value) {
    await fetch(`/adminit/galleries/${id}`, {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
        body: JSON.stringify({ [field]: value })
    });
    const card = document.querySelector(`.gallery-card[data-id="${id}"]`);
    if (card) {
        if (field === 'title')    card.dataset.title    = value.toLowerCase();
        if (field === 'category') card.dataset.category = value.toLowerCase();
        if (field === 'year')     card.dataset.year     = value;
    }
}

// ── Delete ────────────────────────────────────────────────────────────────
async function deleteGallery(id, btn) {
    if (!confirm('Hapus gambar ini?')) return;
    const card = btn.closest('.gallery-card');
    const res  = await fetch(`/adminit/galleries/${id}`, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': CSRF }
    });
    const data = await res.json();
    if (data.success) {
        card.style.transition = 'opacity 0.3s';
        card.style.opacity    = '0';
        setTimeout(() => { card.remove(); updateCount(-1, 'remove'); }, 300);
        showAlert(data.message, 'success');
    } else {
        showAlert('Gagal menghapus gambar.', 'error');
    }
}

// ── Drag & Drop Reorder ───────────────────────────────────────────────────
let dragSrc = null;

function initCardDrag(card) {
    card.addEventListener('dragstart', function(e) {
        dragSrc = this;
        // Delay so the grab effect shows
        setTimeout(() => this.classList.add('is-dragging'), 0);
        e.dataTransfer.effectAllowed = 'move';
        e.dataTransfer.setData('text/plain', this.dataset.id);
    });
    card.addEventListener('dragend', function() {
        this.classList.remove('is-dragging');
        document.querySelectorAll('.gallery-card.drag-over').forEach(c => c.classList.remove('drag-over'));
        dragSrc = null;
        saveOrder();
    });
    card.addEventListener('dragover', function(e) {
        e.preventDefault();
        e.dataTransfer.dropEffect = 'move';
        if (dragSrc && dragSrc !== this) {
            document.querySelectorAll('.gallery-card.drag-over').forEach(c => c.classList.remove('drag-over'));
            this.classList.add('drag-over');
        }
    });
    card.addEventListener('dragleave', function() {
        this.classList.remove('drag-over');
    });
    card.addEventListener('drop', function(e) {
        e.preventDefault();
        this.classList.remove('drag-over');
        if (!dragSrc || dragSrc === this) return;

        const grid = document.getElementById('gallery-grid');
        const cards = Array.from(grid.querySelectorAll('.gallery-card'));
        const srcIdx  = cards.indexOf(dragSrc);
        const destIdx = cards.indexOf(this);

        if (srcIdx < destIdx) {
            grid.insertBefore(dragSrc, this.nextSibling);
        } else {
            grid.insertBefore(dragSrc, this);
        }
    });
}

async function saveOrder() {
    const cards = Array.from(document.querySelectorAll('.gallery-card'));
    const orderedIds = cards.map(c => parseInt(c.dataset.id));

    try {
        const res = await fetch('{{ route("admin.galleries.reorder") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
            body: JSON.stringify({ ordered_ids: orderedIds })
        });
        const data = await res.json();
        if (!data.success) showAlert('Gagal menyimpan urutan.', 'error');
    } catch(e) {
        showAlert('Gagal menyimpan urutan.', 'error');
    }
}

// Init drag untuk semua card yang sudah ada di DOM
document.querySelectorAll('.gallery-card').forEach(card => initCardDrag(card));

// ── Filter (client-side, no reload) ─────────────────────────────────────
let filterTimeout = null;
function applyFilters() {
    const q    = document.getElementById('filter-search').value.toLowerCase();
    const cat  = document.getElementById('filter-category').value.toLowerCase();
    const year = document.getElementById('filter-year').value;
    const cards = document.querySelectorAll('.gallery-card');
    let shown = 0;
    cards.forEach(card => {
        const matchQ   = q    === '' || card.dataset.title.includes(q) || card.dataset.category.includes(q);
        const matchCat = cat  === '' || card.dataset.category === cat;
        const matchY   = year === '' || card.dataset.year     === year;
        const visible  = matchQ && matchCat && matchY;
        card.classList.toggle('hidden', !visible);
        if (visible) shown++;
    });
    document.getElementById('count-label').textContent = shown;
}

['filter-search','filter-category','filter-year'].forEach(id => {
    document.getElementById(id).addEventListener('input', () => {
        clearTimeout(filterTimeout);
        filterTimeout = setTimeout(applyFilters, 150);
    });
});

function updateCount(delta, op) {
    const el = document.getElementById('count-label');
    el.textContent = op === 'add'
        ? parseInt(el.textContent||0) + delta
        : Math.max(0, parseInt(el.textContent||0) + delta);
}

// ── Alert utility ─────────────────────────────────────────────────────────
function showAlert(msg, type) {
    const el = document.getElementById('js-alert');
    const cls = type === 'success'
        ? 'rounded-lg bg-emerald-50 border border-emerald-200 p-4 text-sm text-emerald-700'
        : 'rounded-lg bg-rose-50 border border-rose-200 p-4 text-sm text-rose-700';
    el.className = cls;
    el.textContent = msg;
    el.classList.remove('hidden');
    setTimeout(() => el.classList.add('hidden'), 4000);
}
</script>
@endpush
