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
                            <tbody>
                                @forelse ($curriculum->courses as $iteration => $course)
                                    <tr class="border-t border-slate-100">
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
                });
            </script>
        @endpush
    </section>
@endsection
