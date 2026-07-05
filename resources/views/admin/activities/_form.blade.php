@php
    $eventDate  = old('event_date', isset($activity) && $activity->event_date ? $activity->event_date->format('Y-m-d') : now()->format('Y-m-d'));
    $startAt    = old('start_at', isset($activity) && $activity->start_at ? $activity->start_at->format('H:i') : '');
    $endAt      = old('end_at',   isset($activity) && $activity->end_at   ? $activity->end_at->format('H:i')   : '');
@endphp

{{-- Info: google_event_url diisi otomatis oleh sistem --}}
@if(isset($activity) && $activity->google_event_url)
    <div class="mb-4 flex items-start gap-3 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
        <i class="fa-brands fa-google mt-0.5 text-base"></i>
        <div>
            <p class="font-semibold">Tersinkronisasi dengan Google Calendar</p>
            <a href="{{ $activity->google_event_url }}" target="_blank" class="mt-0.5 block truncate text-xs text-emerald-600 underline">{{ $activity->google_event_url }}</a>
        </div>
    </div>
@else
    <!-- <div class="mb-4 flex items-start gap-3 rounded-lg border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-600">
        <i class="fa-regular fa-calendar mt-0.5 text-base"></i>
        <p>Link Google Calendar akan diisi <strong>otomatis</strong> oleh sistem setelah kegiatan disimpan.</p>
    </div> -->
@endif

{{-- Judul --}}
<div class="grid gap-5">
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


<div class="grid gap-5">
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
                value="{{ old('category', $activity->category ?? '') }}"
                class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500"
                placeholder="Contoh: Lomba, Seminar, Pelatihan…"
            />
            {{-- Datalist: opsi kategori dari DB yang sudah pernah dipakai --}}
            <datalist id="category-suggestions">
                @foreach ($existingCategories ?? [] as $cat)
                    <option value="{{ $cat }}"></option>
                @endforeach
            </datalist>
            <p class="mt-1 text-xs text-slate-400">Ketik atau pilih dari saran kategori yang sudah ada.</p>
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
    <div>
        <label class="mb-1.5 block text-sm font-medium text-slate-700">
            Gambar Cover {{ isset($activity) ? '' : '*' }}
        </label>

        {{-- Area klik untuk upload --}}
        <label
            for="image-upload"
            id="image-drop-zone"
            class="relative flex cursor-pointer flex-col items-center justify-center gap-3 rounded-xl border-2 border-dashed border-slate-300 bg-slate-50 px-6 py-10 text-center transition hover:border-slate-400 hover:bg-slate-100"
        >
            {{-- Preview (tersembunyi dulu) --}}
            <img id="image-preview" src="" alt="Preview" class="hidden h-40 w-full rounded-lg object-cover" />

            {{-- Ikon cloud upload (akan hilang ketika ada preview) --}}
            <div id="image-upload-placeholder">
                <svg class="mx-auto h-12 w-12 text-slate-300" viewBox="0 0 24 24" fill="currentColor">
                    <path fill-rule="evenodd" d="M11.47 2.47a.75.75 0 011.06 0l4.5 4.5a.75.75 0 01-1.06 1.06l-3.22-3.22V16.5a.75.75 0 01-1.5 0V4.81L8.03 8.03a.75.75 0 01-1.06-1.06l4.5-4.5zM3 15.75a.75.75 0 01.75.75v2.25a1.5 1.5 0 001.5 1.5h13.5a1.5 1.5 0 001.5-1.5V16.5a.75.75 0 011.5 0v2.25a3 3 0 01-3 3H5.25a3 3 0 01-3-3V16.5a.75.75 0 01.75-.75z" clip-rule="evenodd" />
                </svg>
                <p class="mt-3 text-sm font-semibold text-slate-600">
                    Klik untuk upload gambar
                </p>
                <p class="mt-1 text-xs text-slate-400">atau seret & lepas file ke sini</p>
                <p class="mt-1 text-xs text-slate-400">PNG, JPG hingga 5 MB</p>
            </div>

            <input id="image-upload" name="image" type="file" accept="image/*" class="sr-only" {{ isset($activity) ? '' : 'required' }} />
        </label>

        {{-- Gambar aktif (edit mode) --}}
        @if (isset($activity) && $activity->image_path)
            <div class="mt-3">
                <p class="mb-1 text-xs text-slate-500">Gambar saat ini:</p>
                <img
                    id="current-image"
                    src="{{ asset('storage/' . $activity->image_path) }}"
                    alt="{{ $activity->title }}"
                    class="h-40 w-full rounded-lg object-cover"
                />
            </div>
        @endif
    </div>

</div>

{{-- JavaScript: preview gambar setelah dipilih --}}
<script>
    (function () {
        const input       = document.getElementById('image-upload');
        const preview     = document.getElementById('image-preview');
        const placeholder = document.getElementById('image-upload-placeholder');
        const currentImg  = document.getElementById('current-image');

        if (!input || !preview || !placeholder) return;

        input.addEventListener('change', function () {
            const file = this.files?.[0];
            if (!file) return;

            const reader = new FileReader();
            reader.onload = function (e) {
                preview.src = e.target.result;
                preview.classList.remove('hidden');
                placeholder.classList.add('hidden');
                if (currentImg) currentImg.classList.add('hidden');
            };
            reader.readAsDataURL(file);
        });
    })();
</script>
