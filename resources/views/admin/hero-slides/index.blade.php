@extends('layouts.admin')

@section('title', 'Kelola Hero Section (Banner)')

@section('content')
<div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="text-2xl font-bold text-slate-800">Kelola Hero Section</h1>
        <p class="text-sm text-slate-500">Atur gambar hero slide yang muncul di halaman utama.</p>
    </div>
    <div class="flex gap-2">
        <button type="button" onclick="openCreateModal()" class="inline-flex items-center gap-2 rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-orange-700">
            <i class="fa-solid fa-plus"></i>
            <span>+ Tambah Banner</span>
        </button>
    </div>
</div>

@if (session('success'))
<div class="mb-6 rounded-lg bg-emerald-50 p-4 text-emerald-700 border border-emerald-200">
    {{ session('success') }}
</div>
@endif

@if ($errors->any())
<div class="mb-6 rounded-lg bg-rose-50 p-4 text-rose-700 border border-rose-200">
    <ul class="list-disc pl-5">
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
    @if ($heroSlides->isEmpty())
        <div class="py-12 text-center">
            <div class="mx-auto mb-4 inline-flex h-16 w-16 items-center justify-center rounded-full bg-slate-100 text-slate-400">
                <i class="fa-regular fa-image text-2xl"></i>
            </div>
            <h3 class="text-lg font-semibold text-slate-800">Belum Ada Banner</h3>
            <p class="mt-1 text-sm text-slate-500">Klik tombol "Tambah Banner" untuk mengunggah gambar pertama Anda.</p>
        </div>
    @else
        <div class="mb-4 text-sm text-slate-500">
            <i class="fa-solid fa-info-circle mr-1 text-blue-500"></i>
            drag and drop gambar di bawah untuk mengubah urutan posisi banner.
        </div>
        
        <div id="banner-grid" class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
    @foreach ($heroSlides as $banner)
        <div class="banner-card group relative overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm transition hover:shadow-lg hover:border-slate-300 cursor-move"
             data-id="{{ $banner->id }}"
             draggable="true">
            
            {{-- Image Container --}}
            <div class="aspect-video w-full bg-slate-100 relative overflow-hidden">
                <img src="{{ asset('storage/' . $banner->image_path) }}" alt="Banner" class="h-full w-full object-cover group-hover:scale-105 transition-transform duration-300">
                
                {{-- Overlay on Hover with Action Buttons --}}
                <div class="absolute inset-0 bg-black/30 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex flex-col items-center justify-center gap-3">
                    <div class="flex gap-2">
                        <button type="button" 
                                onclick="openEditModal({{ $banner->id }}, '{{ asset('storage/' . $banner->image_path) }}')" 
                                class="inline-flex items-center gap-1 rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700 transition shadow-lg"
                                title="Edit/Ganti Gambar">
                            <i class="fa-solid fa-pen-to-square"></i>
                            <span>Edit</span>
                        </button>
                        
                        <button type="button"
                                onclick="deleteWithConfirm({{ $banner->id }})"
                                class="inline-flex items-center gap-1 rounded-lg bg-rose-600 px-4 py-2 text-sm font-semibold text-white hover:bg-rose-700 transition shadow-lg"
                                title="Hapus Banner">
                            <i class="fa-solid fa-trash"></i>
                            <span>Hapus</span>
                        </button>
                    </div>
                    <p class="text-xs text-white/80 flex items-center gap-1">
                        <i class="fa-solid fa-grip-vertical"></i>
                        Drag to reorder
                    </p>
                </div>
            </div>
            
            {{-- Footer with Position Number --}}
            <!-- <div class="p-4 border-t border-slate-100 flex items-center justify-between bg-slate-50 group-hover:bg-slate-100 transition">
                <div class="flex items-center gap-2">
                    <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-gradient-to-br from-orange-400 to-orange-600 text-xs font-bold text-white shadow-sm">
                        {{ $loop->iteration }}
                    </span>
                    <span class="text-xs text-slate-600 font-semibold">Posisi {{ $loop->iteration }}</span>
                </div>
            </div> -->
        </div>
    @endforeach
</div>

    @endif
</div>

{{-- Modal Tambah --}}
<div id="createModal" class="fixed inset-0 z-50 flex hidden items-center justify-center bg-slate-900/50 backdrop-blur-sm transition-opacity">
    <div class="w-full max-w-lg rounded-2xl bg-white p-6 shadow-xl" onclick="event.stopPropagation()">
        <div class="mb-5 flex items-center justify-between border-b border-slate-100 pb-4">
            <h3 class="text-lg font-bold text-slate-800">Tambah Banner Hero</h3>
            <button type="button" onclick="closeModal('createModal')" class="text-slate-400 hover:text-slate-600">
                <i class="fa-solid fa-xmark text-xl"></i>
            </button>
        </div>
        
        <form action="{{ route('admin.hero-slides.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="mb-5">
                <label class="mb-2 block text-sm font-semibold text-slate-700">Unggah Gambar</label>
                <div class="flex w-full items-center justify-center">
                    <label for="dropzone-file-create" class="dark:hover:bg-bray-800 flex h-48 w-full cursor-pointer flex-col items-center justify-center rounded-lg border-2 border-dashed border-slate-300 bg-slate-50 hover:bg-slate-100">
                        <div class="flex flex-col items-center justify-center pb-6 pt-5">
                            <i class="fa-solid fa-cloud-arrow-up text-3xl text-slate-400 mb-3"></i>
                            <p class="mb-2 text-sm text-slate-500"><span class="font-semibold">Klik untuk upload</span></p>
                            <p class="text-xs text-slate-500">JPG, PNG atau WEBP (Max. 10MB)</p>
                            <p id="file-name-create" class="mt-2 text-sm font-medium text-orange-600 hidden"></p>
                        </div>
                        <input id="dropzone-file-create" type="file" name="image" class="hidden" accept="image/*" required onchange="updateFileName(this, 'file-name-create')" />
                    </label>
                </div>
            </div>
            
            <div class="flex justify-end gap-3 pt-4 border-t border-slate-100">
                <button type="button" onclick="closeModal('createModal')" class="rounded-lg px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-100">Batal</button>
                <button type="submit" class="rounded-lg bg-orange-600 px-4 py-2 text-sm font-medium text-white hover:bg-orange-700">Simpan Banner</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Edit --}}
<div id="editModal" class="fixed inset-0 z-50 flex hidden items-center justify-center bg-slate-900/50 backdrop-blur-sm transition-opacity">
    <div class="w-full max-w-lg rounded-2xl bg-white p-6 shadow-xl" onclick="event.stopPropagation()">
        <div class="mb-5 flex items-center justify-between border-b border-slate-100 pb-4">
            <h3 class="text-lg font-bold text-slate-800">Edit / Ganti Gambar</h3>
            <button type="button" onclick="closeModal('editModal')" class="text-slate-400 hover:text-slate-600">
                <i class="fa-solid fa-xmark text-xl"></i>
            </button>
        </div>
        
        <form id="editForm" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="mb-4">
                <p class="text-sm font-semibold text-slate-700 mb-2">Gambar Saat Ini:</p>
                <div class="aspect-video w-full rounded-lg overflow-hidden border border-slate-200">
                    <img id="current-image-preview" src="" class="w-full h-full object-cover">
                </div>
            </div>

            <div class="mb-5">
                <label class="mb-2 block text-sm font-semibold text-slate-700">Unggah Gambar Baru</label>
                <div class="flex w-full items-center justify-center">
                    <label for="dropzone-file-edit" class="dark:hover:bg-bray-800 flex h-32 w-full cursor-pointer flex-col items-center justify-center rounded-lg border-2 border-dashed border-slate-300 bg-slate-50 hover:bg-slate-100">
                        <div class="flex flex-col items-center justify-center">
                            <i class="fa-solid fa-cloud-arrow-up text-2xl text-slate-400 mb-2"></i>
                            <p class="text-sm text-slate-500"><span class="font-semibold">Pilih gambar pengganti</span></p>
                            <p id="file-name-edit" class="mt-2 text-sm font-medium text-orange-600 hidden"></p>
                        </div>
                        <input id="dropzone-file-edit" type="file" name="image" class="hidden" accept="image/*" required onchange="updateFileName(this, 'file-name-edit')" />
                    </label>
                </div>
            </div>
            
            <div class="flex justify-end gap-3 pt-4 border-t border-slate-100">
                <button type="button" onclick="closeModal('editModal')" class="rounded-lg px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-100">Batal</button>
                <button type="submit" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function updateFileName(input, textId) {
        const textEl = document.getElementById(textId);
        if (input.files && input.files.length > 0) {
            textEl.textContent = input.files[0].name;
            textEl.classList.remove('hidden');
        } else {
            textEl.classList.add('hidden');
        }
    }

    function openModal(id) {
        const modal = document.getElementById(id);
        modal.classList.remove('hidden');
        // Prevent scrolling on body
        document.body.style.overflow = 'hidden';
    }
    
    function closeModal(id) {
        const modal = document.getElementById(id);
        modal.classList.add('hidden');
        document.body.style.overflow = '';
        
        // Reset file inputs
        if(id === 'createModal') {
            document.getElementById('dropzone-file-create').value = '';
            document.getElementById('file-name-create').classList.add('hidden');
        } else if(id === 'editModal') {
            document.getElementById('dropzone-file-edit').value = '';
            document.getElementById('file-name-edit').classList.add('hidden');
        }
    }

    function openCreateModal() {
        openModal('createModal');
    }

    function openEditModal(id, imageUrl) {
        document.getElementById('editForm').action = `/adminit/hero-slides/${id}`;
        document.getElementById('current-image-preview').src = imageUrl;
        openModal('editModal');
    }

    // Close modal when clicking outside
    document.querySelectorAll('.fixed.inset-0').forEach(modal => {
        modal.addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal(this.id);
            }
        });
    });

    // Drag and Drop Logic
    document.addEventListener('DOMContentLoaded', () => {
        const grid = document.getElementById('banner-grid');
        if (!grid) return;

        let draggedItem = null;

        grid.addEventListener('dragstart', function (e) {
            draggedItem = e.target.closest('.banner-card');
            if(!draggedItem) return;
            e.dataTransfer.effectAllowed = 'move';
            e.dataTransfer.setData('text/html', draggedItem.innerHTML);
            setTimeout(() => {
                draggedItem.classList.add('opacity-50');
            }, 0);
        });

        grid.addEventListener('dragover', function (e) {
            e.preventDefault();
            const afterElement = getDragAfterElement(grid, e.clientX, e.clientY);
            const currentHover = e.target.closest('.banner-card');
            
            if (currentHover && currentHover !== draggedItem) {
                if (afterElement == null) {
                    grid.appendChild(draggedItem);
                } else {
                    grid.insertBefore(draggedItem, afterElement);
                }
            }
        });

        grid.addEventListener('dragenter', function (e) {
            e.preventDefault();
        });

        grid.addEventListener('dragend', function (e) {
            if(draggedItem) {
                draggedItem.classList.remove('opacity-50');
                draggedItem = null;
                saveNewOrder();
            }
        });

        function getDragAfterElement(container, x, y) {
            const draggableElements = [...container.querySelectorAll('.banner-card:not(.opacity-50)')];

            return draggableElements.reduce((closest, child) => {
                const box = child.getBoundingClientRect();
                const offset = y - box.top - box.height / 2;
                
                // For a grid, we also consider X axis, but simple Y/X combined offset works well enough
                if (offset < 0 && offset > closest.offset) {
                    return { offset: offset, element: child };
                } else {
                    return closest;
                }
            }, { offset: Number.NEGATIVE_INFINITY }).element;
        }

        function saveNewOrder() {
            const items = [...grid.querySelectorAll('.banner-card')];
            const orderedIds = items.map(item => item.getAttribute('data-id'));

            // Update visible numbers
            items.forEach((item, index) => {
                item.querySelector('.rounded-full').textContent = index + 1;
            });

            // Send to server
            fetch('{{ route("admin.hero-slides.reorder") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ ordered_ids: orderedIds })
            })
            .then(res => res.json())
            .then(data => {
                if(!data.success) {
                    alert('Gagal memperbarui posisi');
                }
            })
            .catch(err => {
                console.error(err);
                alert('Terjadi kesalahan jaringan saat menyimpan posisi.');
            });
        }
    });

    function deleteWithConfirm(bannerId) {
    if (confirm('Apakah Anda yakin ingin menghapus banner ini? Tindakan ini tidak dapat dibatalkan.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/adminit/hero-slides/${bannerId}`;
        form.innerHTML = `
            @csrf
            @method('DELETE')
        `;
        document.body.appendChild(form);
        form.submit();
    }
}

</script>
@endpush