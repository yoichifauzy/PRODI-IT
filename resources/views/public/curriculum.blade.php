@extends('layouts.public')

@section('title', __('public.curriculum.page_title'))

@section('content')
    @include('public.partials.page-hero', [
        'title' => __('public.curriculum.hero_title'),
        'subtitle' => __('public.curriculum.hero_subtitle'),
    ])

    <section class="section-wrap public-page-shell">
        <header class="public-page-intro">
            <!-- <h2 class="public-page-title">{{ __('public.curriculum.intro_title') }}</h2>
            <p class="public-page-copy">{{ __('public.curriculum.intro_copy') }}</p> -->
        </header>

        <div class="curriculum-filter-wrap mb-6 flex flex-wrap justify-center gap-3">
            @foreach ($curricula as $curriculum)
                <a href="{{ route('public.curriculum', ['curriculum' => $curriculum->id]) }}"
                   class="curriculum-filter-btn js-curriculum-filter {{ optional($selectedCurriculum)->name === $curriculum->name ? 'is-active' : '' }}"
                   data-curriculum-name="{{ $curriculum->name }}"
                   data-default-curriculum="{{ $curriculum->id }}">
                    {{ $curriculum->name }}
                </a>
            @endforeach
        </div>

        @if ($allCurricula->isNotEmpty())
            @foreach ($allCurricula as $curriculum)
                @php
                    $panelMajorOptions = $allCurricula->where('name', $curriculum->name);
                @endphp
                <div class="public-panel curriculum-panel rounded-2xl border border-[var(--border-soft)] bg-white p-5 shadow-sm {{ optional($selectedCurriculum)->id !== $curriculum->id ? 'hidden' : '' }}"
                     data-curriculum-panel="{{ $curriculum->id }}"
                     data-curriculum-name="{{ $curriculum->name }}">
                    <div class="mb-4 flex flex-wrap items-end justify-between gap-3">
                        <div>
                            <h2 class="mb-1 text-2xl font-bold">{{ $curriculum->name }}</h2>
                            <!-- <p class="text-sm text-[var(--text-soft)]">Penjurusan: {{ $curriculum->major_selection ?: '-' }}</p> -->
                        </div>
                        <span class="meta-pill">{{ __('public.curriculum.meta_total_courses') }}: {{ $curriculum->courses->count() }}</span>
                    </div>

                    @if($panelMajorOptions->count() > 1)
                        <div class="mb-6 flex flex-wrap gap-2 border-b border-slate-100 pb-4">
                            @foreach($panelMajorOptions as $option)
                                <a href="{{ route('public.curriculum', ['curriculum' => $option->id]) }}"
                                   class="js-curriculum-major px-4 py-2 rounded-lg text-sm font-medium transition-all hover:bg-orange-600 hover:text-white {{ optional($selectedCurriculum)->id === $option->id ? 'bg-orange-600 text-white shadow-md active' : 'bg-slate-50 text-slate-600 border border-slate-200' }}"
                                   data-curriculum="{{ $option->id }}">
                                    {{ $option->major_selection ?: 'Umum' }}
                                </a>
                            @endforeach
                        </div>
                    @endif

                    <div class="overflow-x-auto">
                        <table class="curriculum-table min-w-full text-sm">
                            <thead class="curriculum-table-head text-left">
                                <tr>
                                    <th class="px-4 py-3">No</th>
                                    <th class="px-4 py-3">{{ __('public.curriculum.table_code') }}</th>
                                    <th class="px-4 py-3">{{ __('public.curriculum.table_course') }}</th>
                                    <th class="px-4 py-3">{{ __('public.curriculum.table_credits_theory') }}</th>
                                    <th class="px-4 py-3">{{ __('public.curriculum.table_credits_practice') }}</th>
                                    <!-- <th class="px-4 py-3">{{ __('public.curriculum.table_syllabus') }}</th> -->
                                </tr>
                            </thead>
                            <tbody class="js-public-course-tbody" data-curriculum-id="{{ $curriculum->id }}">
                                @forelse ($curriculum->courses as $iteration => $course)
                                    <tr class="border-t border-slate-100 hover:bg-slate-50/50 transition-colors">
                                        <td class="px-4 py-3">{{ $iteration + 1 }}</td>
                                        <td class="px-4 py-3">{{ $course->code }}</td>
                                        <td class="px-4 py-3">{{ $course->name }}</td>
                                        <td class="px-4 py-3">{{ $course->credits_theory }}</td>
                                        <td class="px-4 py-3">{{ $course->credits_practice }}</td>
                                        <!-- <td class="px-4 py-3">{{ $course->short_syllabus ?: '-' }}</td> -->
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-4 py-8 text-center text-[var(--text-soft)]">{{ __('public.curriculum.empty_courses') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="js-public-course-pagination mt-4 flex items-center justify-center gap-1" data-curriculum-id="{{ $curriculum->id }}"></div>
                </div>
            @endforeach
        @else
            <div class="public-empty-state rounded-2xl border border-dashed border-[var(--border-soft)] bg-white p-8 text-center text-lg font-semibold text-[var(--text-soft)]">
                {{ __('public.curriculum.empty_not_selected') }}
            </div>
        @endif

        @push('scripts')
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    const filterButtons = Array.from(document.querySelectorAll('.js-curriculum-filter'));
                    const majorButtons = Array.from(document.querySelectorAll('.js-curriculum-major'));
                    const panels = Array.from(document.querySelectorAll('[data-curriculum-panel]'));

                    function setFilterActiveByName(name) {
                        filterButtons.forEach(button => {
                            button.classList.toggle('is-active', button.dataset.curriculumName === name);
                        });
                    }

                    function setMajorActive(curriculumId) {
                        majorButtons.forEach(button => {
                            const isSelected = button.dataset.curriculum === curriculumId;
                            button.classList.toggle('bg-orange-600', isSelected);
                            button.classList.toggle('text-white', isSelected);
                            button.classList.toggle('shadow-md', isSelected);
                            button.classList.toggle('active', isSelected);
                            button.classList.toggle('bg-slate-50', !isSelected);
                            button.classList.toggle('text-slate-600', !isSelected);
                            button.classList.toggle('border', !isSelected);
                            button.classList.toggle('border-slate-200', !isSelected);
                        });
                    }

                    function showCurriculum(curriculumId) {
                        const selectedPanel = panels.find(panel => panel.dataset.curriculumPanel === curriculumId);
                        if (!selectedPanel) {
                            return;
                        }

                        const selectedGroup = selectedPanel.dataset.curriculumName;
                        panels.forEach(panel => {
                            panel.classList.toggle('hidden', panel !== selectedPanel);
                        });

                        setFilterActiveByName(selectedGroup);
                        setMajorActive(curriculumId);

                        if (history.replaceState) {
                            const url = new URL(window.location);
                            url.searchParams.set('curriculum', curriculumId);
                            history.replaceState(null, '', url);
                        }
                    }

                    function showCurriculumGroup(groupName, defaultCurriculumId) {
                        const groupPanels = panels.filter(panel => panel.dataset.curriculumName === groupName);
                        if (groupPanels.length === 0) {
                            return;
                        }

                        const activePanel = groupPanels.find(panel => !panel.classList.contains('hidden')) || groupPanels[0];
                        showCurriculum(activePanel.dataset.curriculumPanel || defaultCurriculumId);
                    }

                    filterButtons.forEach(button => {
                        button.addEventListener('click', function (event) {
                            event.preventDefault();
                            showCurriculumGroup(this.dataset.curriculumName, this.dataset.defaultCurriculum);
                        });
                    });

                    majorButtons.forEach(button => {
                        button.addEventListener('click', function (event) {
                            const curriculumId = this.dataset.curriculum;
                            if (!curriculumId) {
                                return;
                            }
                            event.preventDefault();
                            showCurriculum(curriculumId);
                        });
                    });

                    // --- Client-side Pagination ---
                    const PER_PAGE = 10;
                    var courseDataMap = {!! json_encode($allCurricula->mapWithKeys(function($cur){ return [(string)$cur->id => $cur->courses->map(function($c){ return ['code' => $c->code, 'name' => $c->name, 'credits_theory' => $c->credits_theory, 'credits_practice' => $c->credits_practice]; })]; })) !!};
                    var coursePageMap = {};
                    Object.keys(courseDataMap).forEach(function(cid) { coursePageMap[cid] = 1; });

                    function renderCourseTable(cid) {
                        var tbody = document.querySelector('.js-public-course-tbody[data-curriculum-id="' + cid + '"]');
                        var data = courseDataMap[cid] || [];
                        var page = coursePageMap[cid] || 1;
                        var totalPages = Math.ceil(data.length / PER_PAGE) || 1;
                        if (page > totalPages) page = totalPages;
                        if (page < 1) page = 1;
                        coursePageMap[cid] = page;

                        var start = (page - 1) * PER_PAGE;
                        var pageItems = data.slice(start, start + PER_PAGE);
                        var html = '';

                        if (pageItems.length === 0) {
                            html = '<tr><td colspan="5" class="px-4 py-8 text-center text-[var(--text-soft)]">{{ __("public.curriculum.empty_courses") }}</td></tr>';
                        } else {
                            for (var i = 0; i < pageItems.length; i++) {
                                var item = pageItems[i];
                                var num = start + i + 1;
                                html += '<tr class="border-t border-slate-100 hover:bg-slate-50/50 transition-colors">' +
                                    '<td class="px-4 py-3">' + num + '</td>' +
                                    '<td class="px-4 py-3">' + item.code + '</td>' +
                                    '<td class="px-4 py-3">' + item.name + '</td>' +
                                    '<td class="px-4 py-3">' + item.credits_theory + '</td>' +
                                    '<td class="px-4 py-3">' + item.credits_practice + '</td>' +
                                    '</tr>';
                            }
                        }
                        if (tbody) tbody.innerHTML = html;

                        var pagContainer = document.querySelector('.js-public-course-pagination[data-curriculum-id="' + cid + '"]');
                        if (!pagContainer) return;
                        if (totalPages <= 1) { pagContainer.innerHTML = ''; return; }

                        var pagHtml = '';
                        pagHtml += '<button class="px-3 py-1.5 rounded-lg text-sm font-medium ' + (page === 1 ? 'text-slate-300 cursor-not-allowed' : 'text-slate-600 hover:bg-slate-100') + '"' + (page === 1 ? ' disabled' : '') + ' data-page="' + (page - 1) + '">&laquo;</button>';
                        
                        var startPage = Math.max(1, page - 2);
                        var endPage = Math.min(totalPages, startPage + 4);
                        if (endPage - startPage < 4) startPage = Math.max(1, endPage - 4);

                        for (var p = startPage; p <= endPage; p++) {
                            pagHtml += '<button class="px-3 py-1.5 rounded-lg text-sm font-medium ' + (p === page ? 'bg-orange-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100') + '" data-page="' + p + '">' + p + '</button>';
                        }
                        pagHtml += '<button class="px-3 py-1.5 rounded-lg text-sm font-medium ' + (page === totalPages ? 'text-slate-300 cursor-not-allowed' : 'text-slate-600 hover:bg-slate-100') + '"' + (page === totalPages ? ' disabled' : '') + ' data-page="' + (page + 1) + '">&raquo;</button>';
                        
                        pagHtml += '<span class="ml-3 text-sm text-slate-500">Baris ' + (start + 1) + '&ndash;' + Math.min(page * PER_PAGE, data.length) + ' dari ' + data.length + '</span>';

                        pagContainer.innerHTML = pagHtml;
                    }

                    document.querySelectorAll('.js-public-course-pagination').forEach(function(container) {
                        container.addEventListener('click', function(e) {
                            var btn = e.target.closest('button[data-page]');
                            if (btn && !btn.disabled) {
                                var cid = this.dataset.curriculumId;
                                coursePageMap[cid] = parseInt(btn.dataset.page);
                                renderCourseTable(cid);
                            }
                        });
                    });

                    Object.keys(courseDataMap).forEach(function(cid) { renderCourseTable(cid); });

                });
            </script>
        @endpush
    </section>
@endsection
