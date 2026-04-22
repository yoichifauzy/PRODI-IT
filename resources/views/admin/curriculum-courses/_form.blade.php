@php
    $selectedCurriculum = (string) old('curriculum_id', request('curriculum_id', $curriculumCourse->curriculum_id ?? ''));
@endphp

<div class="grid gap-4">
    <div>
        <label for="curriculum_id" class="mb-2 block text-sm font-medium text-slate-700">Kurikulum</label>
        <select id="curriculum_id" name="curriculum_id" required class="w-full rounded-md border border-slate-300 px-3 py-2">
            <option value="">-- Pilih Kurikulum --</option>
            @foreach ($curricula as $curriculum)
                <option value="{{ $curriculum->id }}" {{ (old('curriculum_id', $selectedCurriculum ?? '') == $curriculum->id) ? 'selected' : '' }}>
                    {{ $curriculum->name }} 
                    @if(!empty($curriculum->major_selection) && $curriculum->major_selection !== '-')
                        ({{ $curriculum->major_selection }})
                    @endif
                </option>
            @endforeach
        </select>
    </div>

    <div class="grid gap-4 md:grid-cols-2">
        <!-- <div>
            <label for="semester" class="mb-2 block text-sm font-medium text-slate-700">Semester</label>
            <input id="semester" type="number" min="1" max="14" name="semester" required value="{{ old('semester', $curriculumCourse->semester ?? 1) }}" class="w-full rounded-md border border-slate-300 px-3 py-2" />
        </div> -->
        <div>
            <label for="code" class="mb-2 block text-sm font-medium text-slate-700">Kode MK</label>
            <input id="code" name="code" required value="{{ old('code', $curriculumCourse->code ?? '') }}" class="w-full rounded-md border border-slate-300 px-3 py-2" />
        </div>
        <div>
            <label for="name" class="mb-2 block text-sm font-medium text-slate-700">Nama Mata Kuliah</label>
            <input id="name" name="name" required value="{{ old('name', $curriculumCourse->name ?? '') }}" class="w-full rounded-md border border-slate-300 px-3 py-2" />
        </div>
    </div>

    <div class="grid gap-4 md:grid-cols-2">
        <div>
            <label for="credits_theory" class="mb-2 block text-sm font-medium text-slate-700">SKS Teori</label>
            <input id="credits_theory" type="number" min="0" max="9" name="credits_theory" value="{{ old('credits_theory', $curriculumCourse->credits_theory ?? 2) }}" class="w-full rounded-md border border-slate-300 px-3 py-2" />
        </div>
        <div>
            <label for="credits_practice" class="mb-2 block text-sm font-medium text-slate-700">SKS Praktikum</label>
            <input id="credits_practice" type="number" min="0" max="9" name="credits_practice" value="{{ old('credits_practice', $curriculumCourse->credits_practice ?? 0) }}" class="w-full rounded-md border border-slate-300 px-3 py-2" />
        </div>
        <!-- <div>
            <label for="sort_order" class="mb-2 block text-sm font-medium text-slate-700">Urutan Tampil</label>
            <input id="sort_order" type="number" min="0" name="sort_order" value="{{ old('sort_order', $curriculumCourse->sort_order ?? 0) }}" class="w-full rounded-md border border-slate-300 px-3 py-2" />
        </div> -->
    </div>
</div>