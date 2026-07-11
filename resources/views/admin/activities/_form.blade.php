@php
    $eventDate  = old('event_date', isset($activity) && $activity->event_date ? $activity->event_date->format('Y-m-d') : now()->format('Y-m-d'));
    $startAt    = old('start_at', isset($activity) && $activity->start_at ? $activity->start_at->format('H:i') : '');
    $endAt      = old('end_at',   isset($activity) && $activity->end_at   ? $activity->end_at->format('H:i')   : '');
@endphp

@if(isset($activity) && $activity->google_event_url)
    <div class="mb-4 flex items-start gap-3 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
        <i class="fa-brands fa-google mt-0.5 text-base"></i>
        <div>
            <p class="font-semibold">Tersinkronisasi dengan Google Calendar</p>
            <!-- <a href="{{ $activity->google_event_url }}" target="_blank" class="mt-0.5 block truncate text-xs text-emerald-600 underline">{{ $activity->google_event_url }}</a> -->
        </div>
    </div>
@endif

<div class="grid gap-5">
    {{-- Judul --}}
    <div>
        <label for="title" class="mb-1.5 block text-sm font-medium text-slate-700">
            Judul Kegiatan <span class="text-rose-500">*</span>
        </label>
        <input
            id="title"
            name="title"
            required
            value="{{ old('title', $activity->title ?? '') }}"
            class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500"
            placeholder="Contoh: E-LINK Competition 2026"
        />
    </div>

    {{-- Baris 1: Kategori & Tanggal --}}
    <div class="grid gap-4 md:grid-cols-2">
        <div>
            <label for="category" class="mb-1.5 block text-sm font-medium text-slate-700">
                Kategori <span class="text-rose-500">*</span>
            </label>
            <input
                id="category"
                name="category"
                list="category-suggestions"
                required
                autocomplete="off"
                value="{{ old('category', $activity->category ?? '') }}"
                class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500"
                placeholder="Contoh: Lomba, Seminar, Pelatihan…"
            />
            <datalist id="category-suggestions">
                @php
                    $defaults = ['Lomba', 'Pelatihan', 'Workshop', 'Kuliah Tamu', 'Nasional'];
                    $mergedCategories = collect($defaults)->merge($existingCategories ?? [])->unique()->sort();
                @endphp
                @foreach ($mergedCategories as $cat)
                    <option value="{{ $cat }}">{{ $cat }}</option>
                @endforeach
            </datalist>
            <p class="mt-1 text-xs text-slate-400">Klik dua kali pada kolom atau ketik untuk melihat saran kategori.</p>
        </div>

        <div>
            <label for="event_date" class="mb-1.5 block text-sm font-medium text-slate-700">
                Tanggal Kegiatan <span class="text-rose-500">*</span>
            </label>
            <input
                id="event_date"
                type="date"
                name="event_date"
                required
                value="{{ $eventDate }}"
                onclick="this.showPicker()"
                class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500"
            />
        </div>
    </div>

    {{-- Baris 2: Waktu Mulai & Selesai --}}
    <div class="grid gap-4 md:grid-cols-2">
        <div>
            <label for="start_at" class="mb-1.5 block text-sm font-medium text-slate-700">Waktu Mulai</label>
            <input
                id="start_at"
                type="time"
                name="start_at"
                value="{{ $startAt }}"
                onclick="this.showPicker()"
                class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500"
            />
            <p class="mt-1 text-xs text-slate-400">Opsional — digunakan untuk Google Calendar.</p>
        </div>
        <div>
            <label for="end_at" class="mb-1.5 block text-sm font-medium text-slate-700">Waktu Selesai</label>
            <input
                id="end_at"
                type="time"
                name="end_at"
                value="{{ $endAt }}"
                onclick="this.showPicker()"
                class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500"
            />
            <p class="mt-1 text-xs text-slate-400">Opsional — kosongkan jika tidak ada waktu selesai pasti.</p>
        </div>
    </div>

    {{-- Deskripsi --}}
    <div>
        <label for="description" class="mb-1.5 block text-sm font-medium text-slate-700">Deskripsi</label>
        <textarea
            id="description"
            name="description"
            rows="4"
            class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500"
            placeholder="Jelaskan kegiatan secara singkat…"
        >{{ old('description', $activity->description ?? '') }}</textarea>
    </div>

    {{-- Lokasi --}}
    <div>
        <label for="location" class="mb-1.5 block text-sm font-medium text-slate-700">Lokasi</label>
        <input
            id="location"
            name="location"
            value="{{ old('location', $activity->location ?? '') }}"
            class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500"
            placeholder="Contoh: Aula Gedung A, Lantai 3"
        />
    </div>

    {{-- Upload Gambar --}}
    <div class="mb-5">
        <label class="mb-2 block text-sm font-semibold text-slate-700">Gambar Cover</label>

        <label for="image-upload" class="group relative flex h-64 w-full cursor-pointer flex-col items-center justify-center overflow-hidden rounded-xl border-2 border-dashed border-slate-300 bg-slate-50 transition-all hover:border-slate-400">

            @php
                $hasImage = isset($activity) && $activity->image_path;
            @endphp
            <img id="image-preview"
                 src="{{ $hasImage ? asset('storage/' . $activity->image_path) : '' }}"
                 class="absolute inset-0 h-full w-full object-cover {{ $hasImage ? '' : 'hidden' }}"
                 alt="Preview">

            <div id="upload-overlay"
                class="absolute inset-0 flex flex-col items-center justify-center transition-all duration-300
                {{ $hasImage ? 'bg-black/50 opacity-100' : 'bg-transparent opacity-100' }}">

                <i id="upload-icon" class="fa-solid fa-cloud-arrow-up text-3xl mb-2 {{ $hasImage ? 'text-white' : 'text-slate-400' }}"></i>

                <p id="upload-text" class="text-sm font-medium {{ $hasImage ? 'text-white' : 'text-slate-500' }}">
                    <span class="font-bold">Klik</span> untuk {{ $hasImage ? 'upload gambar pengganti' : 'upload gambar' }}
                </p>

                <p id="upload-subtext" class="mt-1 text-xs {{ $hasImage ? 'text-white/80' : 'text-slate-400' }}">
                    JPG, PNG, atau WEBP (Max 2MB)
                </p>
            </div>

            <input id="image-upload" type="file" name="image" accept="image/*" class="hidden" onchange="previewImage(this)">
        </label>
    </div>
</div>

@push('scripts')
<script>
    function previewImage(input) {
        const preview = document.getElementById('image-preview');
        const overlay = document.getElementById('upload-overlay');
        const icon = document.getElementById('upload-icon');
        const text = document.getElementById('upload-text');
        const subtext = document.getElementById('upload-subtext');

        if (input.files && input.files[0]) {
            const reader = new FileReader();

            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.classList.remove('hidden');

                overlay.className = 'absolute inset-0 flex flex-col items-center justify-center bg-black/50 opacity-0 group-hover:opacity-100 transition-all duration-300';

                icon.className = 'fa-solid fa-cloud-arrow-up text-3xl mb-2 text-white';
                text.className = 'text-sm font-medium text-white';
                subtext.className = 'mt-1 text-xs text-white/80';
            }

            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endpush
