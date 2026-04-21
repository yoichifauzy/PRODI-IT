@php
    $startAt = old('start_at', isset($heroSlide) && $heroSlide->start_at ? $heroSlide->start_at->format('Y-m-d\TH:i') : '');
    $endAt = old('end_at', isset($heroSlide) && $heroSlide->end_at ? $heroSlide->end_at->format('Y-m-d\TH:i') : '');
    $isActive = old('is_active', $heroSlide->is_active ?? true);
@endphp

<div class="grid gap-4">
    <div>
        <label for="title" class="mb-2 block text-sm font-medium text-slate-700">Judul Slide (Opsional)</label>
        <input id="title" name="title" value="{{ old('title', $heroSlide->title ?? '') }}" class="w-full rounded-md border border-slate-300 px-3 py-2 focus:border-slate-500 focus:outline-none" />
    </div>

    <div>
        <label for="subtitle" class="mb-2 block text-sm font-medium text-slate-700">Subjudul Slide (Opsional)</label>
        <input id="subtitle" name="subtitle" value="{{ old('subtitle', $heroSlide->subtitle ?? '') }}" class="w-full rounded-md border border-slate-300 px-3 py-2 focus:border-slate-500 focus:outline-none" />
    </div>

    <div>
        <label for="image" class="mb-2 block text-sm font-medium text-slate-700">Gambar Hero {{ isset($heroSlide) ? '(Opsional jika tidak diubah)' : '' }}</label>
        <input id="image" type="file" name="image" accept="image/*" {{ isset($heroSlide) ? '' : 'required' }} class="w-full rounded-md border border-slate-300 px-3 py-2" />

        @if (isset($heroSlide) && $heroSlide->image_path)
            <img src="{{ asset('storage/' . $heroSlide->image_path) }}" alt="Preview Hero" class="mt-3 h-36 w-full rounded-lg object-cover" />
        @endif
    </div>

    <div class="grid gap-4 md:grid-cols-3">
        <div>
            <label for="sort_order" class="mb-2 block text-sm font-medium text-slate-700">Urutan</label>
            <input id="sort_order" type="number" min="0" name="sort_order" value="{{ old('sort_order', $heroSlide->sort_order ?? 0) }}" class="w-full rounded-md border border-slate-300 px-3 py-2" />
        </div>
        <div>
            <label for="start_at" class="mb-2 block text-sm font-medium text-slate-700">Mulai Tayang (Opsional)</label>
            <input id="start_at" type="datetime-local" name="start_at" value="{{ $startAt }}" class="w-full rounded-md border border-slate-300 px-3 py-2" />
        </div>
        <div>
            <label for="end_at" class="mb-2 block text-sm font-medium text-slate-700">Selesai Tayang (Opsional)</label>
            <input id="end_at" type="datetime-local" name="end_at" value="{{ $endAt }}" class="w-full rounded-md border border-slate-300 px-3 py-2" />
        </div>
    </div>

    <label class="inline-flex items-center gap-2 rounded-md border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-700">
        <input type="hidden" name="is_active" value="0" />
        <input type="checkbox" name="is_active" value="1" @checked((string) $isActive === '1' || $isActive === true || $isActive === 1) />
        Aktifkan slide ini
    </label>
</div>