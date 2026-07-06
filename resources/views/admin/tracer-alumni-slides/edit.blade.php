@extends('layouts.admin')

@section('title', 'Edit Slide Tracer Alumni')

@section('content')
    <div class="mb-4">
        <h1 class="text-2xl font-bold text-slate-900">Edit Slide</h1>
        <p class="text-sm text-slate-600">Perbarui informasi atau gambar slide.</p>
    </div>

    @if ($errors->any())
        <div class="mb-4 rounded-md bg-rose-50 p-4 text-sm text-rose-700">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="mb-4 rounded-md bg-blue-50 p-4 text-sm text-blue-700">
        <strong>Informasi Dimensi Gambar:</strong> Untuk hasil terbaik, gunakan gambar dengan rasio aspek layar lebar (misalnya <strong>1200 x 675 pixel</strong> atau rasio 16:9).
    </div>

    <form method="POST" action="{{ route('admin.tracer-alumni-slides.update', $tracerAlumniSlide) }}" enctype="multipart/form-data" class="rounded-xl border border-slate-200 bg-white p-6">
        @csrf
        @method('PUT')

        <div class="mb-4">
            <label class="mb-1 block text-sm font-semibold text-slate-700">Gambar Saat Ini</label>
            @if($tracerAlumniSlide->image_path)
                <img src="{{ asset('storage/' . $tracerAlumniSlide->image_path) }}" alt="Current Slide" class="h-32 w-auto object-cover rounded border border-slate-200 mb-2">
            @endif
        </div>

        <div class="mb-6">
            <label for="image" class="mb-1 block text-sm font-semibold text-slate-700">Ganti Gambar (Opsional)</label>
            <input type="file" id="image" name="image" accept="image/png, image/jpeg, image/webp" class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm">
            <p class="mt-1 text-xs text-slate-500">Pilih gambar baru jika ingin mengganti. Kosongkan jika tidak ingin mengubah gambar.</p>
        </div>

        <div class="grid gap-4 md:grid-cols-2 mb-6">
            <div>
                <label for="order" class="mb-1 block text-sm font-semibold text-slate-700">Urutan (Order)</label>
                <input type="number" id="order" name="order" value="{{ old('order', $tracerAlumniSlide->order) }}" class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm">
            </div>
            <div>
                <label class="mb-1 block text-sm font-semibold text-slate-700">Status</label>
                <label class="inline-flex items-center mt-2">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $tracerAlumniSlide->is_active) ? 'checked' : '' }} class="form-checkbox h-5 w-5 text-slate-900">
                    <span class="ml-2 text-sm text-slate-700">Aktif (Tampilkan)</span>
                </label>
            </div>
        </div>

        <div class="flex gap-2">
            <button type="submit" class="rounded-md bg-slate-900 px-4 py-2 text-sm font-semibold text-white">Simpan Perubahan</button>
            <a href="{{ route('admin.tracer-alumni-slides.index') }}" class="rounded-md border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700">Batal</a>
        </div>
    </form>
@endsection
