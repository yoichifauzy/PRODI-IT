@php
    $isActive = old('is_active', $tracerAlumni->is_active ?? true);
@endphp

<div class="grid gap-4">
    <div class="grid gap-4 md:grid-cols-2">
        <div>
            <label for="nim" class="mb-2 block text-sm font-medium text-slate-700">NIM</label>
            <input id="nim" name="nim" required value="{{ old('nim', $tracerAlumni->nim ?? '') }}" class="w-full rounded-md border border-slate-300 px-3 py-2" />
        </div>
        <div>
            <label for="graduation_year" class="mb-2 block text-sm font-medium text-slate-700">Tahun Lulusan</label>
            <input id="graduation_year" type="number" min="2000" max="2100" name="graduation_year" value="{{ old('graduation_year', $tracerAlumni->graduation_year ?? now()->year) }}" class="w-full rounded-md border border-slate-300 px-3 py-2" />
        </div>
    </div>

    <div class="grid gap-4 md:grid-cols-1">
        <div>
            <label for="company_name" class="mb-2 block text-sm font-medium text-slate-700">Perusahaan</label>
            <input id="company_name" name="company_name" required value="{{ old('company_name', $tracerAlumni->company_name ?? '') }}" class="w-full rounded-md border border-slate-300 px-3 py-2" />
        </div>
    </div>

    <div class="grid gap-4 md:grid-cols-3">
        <div>
            <label for="company_level" class="mb-2 block text-sm font-medium text-slate-700">Tingkat / Ukuran</label>
            <input id="company_level" name="company_level" value="{{ old('company_level', $tracerAlumni->company_level ?? '') }}" class="w-full rounded-md border border-slate-300 px-3 py-2" placeholder="Nasional / Multinasional" />
        </div>
        <div>
            <label for="department" class="mb-2 block text-sm font-medium text-slate-700">Departemen</label>
            <input id="department" name="department" value="{{ old('department', $tracerAlumni->department ?? '') }}" class="w-full rounded-md border border-slate-300 px-3 py-2" />
        </div>
        <div>
            <label for="relevance" class="mb-2 block text-sm font-medium text-slate-700">Kesesuaian</label>
            <input id="relevance" name="relevance" value="{{ old('relevance', $tracerAlumni->relevance ?? '') }}" class="w-full rounded-md border border-slate-300 px-3 py-2" placeholder="Sesuai / Cukup Sesuai" />
        </div>
    </div>

    <div>
        <label for="notes" class="mb-2 block text-sm font-medium text-slate-700">Catatan (Opsional)</label>
        <textarea id="notes" name="notes" rows="4" class="w-full rounded-md border border-slate-300 px-3 py-2">{{ old('notes', $tracerAlumni->notes ?? '') }}</textarea>
    </div>

    <label class="inline-flex items-center gap-2 rounded-md border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-700">
        <input type="hidden" name="is_active" value="0" />
        <input type="checkbox" name="is_active" value="1" @checked((string) $isActive === '1' || $isActive === true || $isActive === 1) />
        Aktif ditampilkan
    </label>
</div>
