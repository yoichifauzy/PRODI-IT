@extends('layouts.public')

@section('title', __('public.community_service.page_title'))

@section('content')
    @include('public.partials.page-hero', [
        'title' => __('public.community_service.hero_title'),
        'subtitle' => __('public.community_service.hero_subtitle'),
    ])

    <section class="section-wrap public-page-shell">

        <div class="public-panel rounded-2xl border border-[var(--border-soft)] bg-white p-5 shadow-sm">
            <div class="overflow-x-auto">
                <table class="curriculum-table min-w-full text-sm">
                    <thead class="curriculum-table-head text-left">
                        <tr>
                            <th class="px-4 py-3 w-1 whitespace-nowrap text-center">No</th>
                            <th class="px-4 py-3 w-1 whitespace-nowrap text-center">{{ __('public.community_service.table_year') }}</th>
                            <th class="px-4 py-3">{{ __('public.community_service.table_title') }}</th>
                            <th class="px-4 py-3 w-1/3 min-w-[200px]">{{ __('public.community_service.table_location') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="border-t border-slate-100">
                            <td class="px-4 py-3">1</td>
                            <td class="px-4 py-3 text-center">2026</td>
                            <td class="px-4 py-3 font-semibold">Workshop Literasi Digital untuk UMKM Tangerang</td>
                            <td class="px-4 py-3 text-slate-600">Jatiuwung, Tangerang</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
@endsection