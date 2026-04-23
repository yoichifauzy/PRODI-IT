@php
    $selectedType = old('event_type', $academicEvent->event_type ?? 'other');
    $startDate = old('start_date', optional($academicEvent->start_date ?? null)->format('Y-m-d'));
    $endDate = old('end_date', optional($academicEvent->end_date ?? null)->format('Y-m-d'));
    $isPublished = old('is_published', $academicEvent->is_published ?? true);
@endphp

<div class="grid gap-4">
    <div>
        <label for="title" class="mb-2 block text-sm font-medium text-slate-700">Judul Event</label>
        <input id="title" name="title" value="{{ old('title', $academicEvent->title ?? '') }}" required class="w-full rounded-md border border-slate-300 px-3 py-2 focus:border-slate-500 focus:outline-none" />
    </div>

    <div>
        <label for="slug" class="mb-2 block text-sm font-medium text-slate-700">Slug (Opsional)</label>
        <input id="slug" name="slug" value="{{ old('slug', $academicEvent->slug ?? '') }}" class="w-full rounded-md border border-slate-300 px-3 py-2 focus:border-slate-500 focus:outline-none" />
    </div>

    <div class="grid gap-4 md:grid-cols-3">
        <div>
            <label for="event_type" class="mb-2 block text-sm font-medium text-slate-700">Jenis Event</label>
            <select id="event_type" name="event_type" required class="w-full rounded-md border border-slate-300 px-3 py-2 focus:border-slate-500 focus:outline-none">
                @foreach ($eventTypes as $type)
                    <option value="{{ $type }}" @selected($selectedType === $type)>{{ strtoupper($type) }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="start_date" class="mb-2 block text-sm font-medium text-slate-700">Tanggal Mulai</label>
            <input id="start_date" type="date" name="start_date" value="{{ $startDate }}" required class="w-full rounded-md border border-slate-300 px-3 py-2 focus:border-slate-500 focus:outline-none" />
        </div>
        <div>
            <label for="end_date" class="mb-2 block text-sm font-medium text-slate-700">Tanggal Selesai (Opsional)</label>
            <input id="end_date" type="date" name="end_date" value="{{ $endDate }}" class="w-full rounded-md border border-slate-300 px-3 py-2 focus:border-slate-500 focus:outline-none" />
        </div>
    </div>

    <div>
        <label for="location" class="mb-2 block text-sm font-medium text-slate-700">Lokasi (Opsional)</label>
        <input id="location" name="location" value="{{ old('location', $academicEvent->location ?? '') }}" class="w-full rounded-md border border-slate-300 px-3 py-2 focus:border-slate-500 focus:outline-none" />
    </div>

    <div>
        <label for="google_event_url" class="mb-2 block text-sm font-medium text-slate-700">URL Google Calendar (Opsional)</label>
        <input id="google_event_url" name="google_event_url" value="{{ old('google_event_url', $academicEvent->google_event_url ?? '') }}" class="w-full rounded-md border border-slate-300 px-3 py-2 focus:border-slate-500 focus:outline-none" placeholder="https://calendar.google.com/..." />
        <p class="mt-2 text-xs text-slate-500">Jika dikosongkan, URL akan diisi otomatis setelah sinkronisasi Google Calendar aktif.</p>
    </div>

    <div>
        <label for="description" class="mb-2 block text-sm font-medium text-slate-700">Deskripsi</label>
        <textarea id="description" name="description" rows="5" class="w-full rounded-md border border-slate-300 px-3 py-2 focus:border-slate-500 focus:outline-none">{{ old('description', $academicEvent->description ?? '') }}</textarea>
    </div>

    <label class="inline-flex items-center gap-2 rounded-md border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-700">
        <input type="checkbox" name="is_published" value="1" @checked((string) $isPublished === '1' || $isPublished === true || $isPublished === 1) />
        Publish event ke halaman publik
    </label>
</div>
