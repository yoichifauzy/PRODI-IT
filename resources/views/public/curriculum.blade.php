@extends('layouts.public')

@section('title', __('public.curriculum.page_title'))

@section('content')
    @include('public.partials.page-hero', [
        'title' => __('public.curriculum.hero_title'),
        'subtitle' => __('public.curriculum.hero_subtitle'),
    ])

    <section class="section-wrap public-page-shell">

        {{-- Filters (Semester) --}}
        <div class="curriculum-filter-wrap mb-6 flex flex-wrap justify-center gap-3" id="semester-filters">
            @foreach ($semesters as $semester)
                <button type="button" 
                   onclick="setSemester('{{ $semester }}')"
                   class="curriculum-filter-btn {{ $loop->first ? 'is-active' : '' }}"
                   data-semester-btn="{{ $semester }}">
                    {{ $semester }}
                </button>
            @endforeach
        </div>

        @if ($courses->isNotEmpty())
            <div class="public-panel curriculum-panel rounded-2xl border border-[var(--border-soft)] bg-white p-5 shadow-sm">
                
                {{-- Major Options (Will be populated by JS depending on semester) --}}
                <div id="major-filters-container" class="mb-6 flex flex-wrap gap-2 border-b border-slate-100 pb-4 hidden">
                    <!-- Major buttons will be injected here -->
                </div>

                <div class="overflow-x-auto">
                    <table class="curriculum-table min-w-full text-sm">
                        <thead class="curriculum-table-head text-left">
                            <tr>
                                <th class="px-4 py-3">No</th>
                                <th class="px-4 py-3">{{ __('public.curriculum.table_code') }}</th>
                                <th class="px-4 py-3">{{ __('public.curriculum.table_course') }}</th>
                                <th class="px-4 py-3 text-center">{{ __('public.curriculum.table_credits_theory') }}</th>
                                <th class="px-4 py-3 text-center">{{ __('public.curriculum.table_credits_practice') }}</th>
                            </tr>
                        </thead>
                        <tbody id="course-tbody">
                            @foreach ($courses as $course) 
                                <tr class="course-row border-t border-slate-100 hover:bg-slate-50/50 transition-colors hidden"
                                    data-semester="{{ $course->semester }}"
                                    data-major="{{ $course->major_selection ?? 'NULL' }}">
                                    <td class="px-4 py-3 row-number"></td>
                                    <td class="px-4 py-3">{{ $course->code }}</td>
                                    <td class="px-4 py-3">{{ $course->name }}</td>
                                    <td class="px-4 py-3 text-center">{{ $course->credits_theory }}</td>
                                    <td class="px-4 py-3 text-center">{{ $course->credits_practice }}</td>
                                </tr>
                            @endforeach
                            <tr id="empty-state" class="hidden">
                                <td colspan="5" class="px-4 py-8 text-center text-[var(--text-soft)]">{{ __('public.curriculum.empty_courses') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        @else
            <div class="public-empty-state rounded-2xl border border-dashed border-[var(--border-soft)] bg-white p-8 text-center text-lg font-semibold text-[var(--text-soft)]">
                Belum ada data kurikulum.
            </div>
        @endif
    </section>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const defaultSemester = '{{ $semesters->first() ?? '' }}';
    if (!defaultSemester) return;

    let currentSemester = defaultSemester;
    let currentMajor = null;

    // Get unique majors for a given semester
    function getMajorsForSemester(semester) {
        const rows = document.querySelectorAll(`.course-row[data-semester="${semester}"]`);
        const majors = new Set();
        rows.forEach(row => {
            const major = row.getAttribute('data-major');
            if (major && major !== 'NULL' && major.trim() !== '') {
                majors.add(major);
            }
        });
        return Array.from(majors).sort();
    }

    // Render major buttons
    function renderMajorButtons(semester) {
        const majors = getMajorsForSemester(semester);
        const container = document.getElementById('major-filters-container');
        container.innerHTML = '';
        
        if (majors.length === 0) {
            container.classList.add('hidden');
            currentMajor = null;
            return;
        }

        container.classList.remove('hidden');
        if (!majors.includes(currentMajor)) {
            currentMajor = majors[0];
        }

        majors.forEach(major => {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = `px-4 py-2 rounded-lg text-sm font-medium transition-all hover:bg-orange-600 hover:text-white border border-slate-200 major-filter-btn`;
            
            if (major === currentMajor) {
                btn.className = `px-4 py-2 rounded-lg text-sm font-medium transition-all bg-orange-600 text-white shadow-md active major-filter-btn`;
            } else {
                btn.className = `px-4 py-2 rounded-lg text-sm font-medium transition-all hover:bg-orange-600 hover:text-white bg-slate-50 text-slate-600 border border-slate-200 major-filter-btn`;
            }
            
            btn.textContent = major;
            btn.onclick = () => setMajor(major);
            container.appendChild(btn);
        });
    }

    window.setSemester = function(semester) {
        currentSemester = semester;
        
        // Update active class on semester buttons
        document.querySelectorAll('[data-semester-btn]').forEach(btn => {
            if (btn.getAttribute('data-semester-btn') === semester) {
                btn.classList.add('is-active');
            } else {
                btn.classList.remove('is-active');
            }
        });

        renderMajorButtons(semester);
        updateTable();
    };

    window.setMajor = function(major) {
        currentMajor = major;
        renderMajorButtons(currentSemester); // Re-render to update active state
        updateTable();
    };

    function updateTable() {
        const rows = document.querySelectorAll('.course-row');
        let visibleCount = 0;

        rows.forEach(row => {
            const rowSem = row.getAttribute('data-semester');
            const rowMaj = row.getAttribute('data-major');

            let shouldShow = false;
            
            if (rowSem === currentSemester) {
                // If there are no major options for this semester, show all
                const majorsInSemester = getMajorsForSemester(currentSemester);
                if (majorsInSemester.length === 0) {
                    shouldShow = true;
                } else {
                    // Show if major matches, or if row has no major (NULL) - meaning it belongs to all majors
                    if (rowMaj === currentMajor || rowMaj === 'NULL') {
                        shouldShow = true;
                    }
                }
            }

            if (shouldShow) {
                row.classList.remove('hidden');
                visibleCount++;
                row.querySelector('.row-number').textContent = visibleCount;
            } else {
                row.classList.add('hidden');
            }
        });

        const emptyState = document.getElementById('empty-state');
        if (visibleCount === 0) {
            emptyState.classList.remove('hidden');
        } else {
            emptyState.classList.add('hidden');
        }
    }

    // Init
    setSemester(defaultSemester);
});
</script>
@endpush
