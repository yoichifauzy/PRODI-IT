@extends('layouts.public')

@section('title', __('public.research.page_title'))

@section('content')
    @include('public.partials.page-hero', [
        'title' => __('public.research.hero_title'),
        'subtitle' => __('public.research.hero_subtitle'),
    ])

    <section class="section-wrap public-page-shell">
        <!-- <header class="public-page-intro">
            <h2 class="public-page-title">{{ __('public.research.intro_title') }}</h2>
            <p class="public-page-copy">{{ __('public.research.intro_copy') }}</p>
        </header> -->

        <div class="public-panel rounded-2xl border border-[var(--border-soft)] bg-white p-5 shadow-sm">
            <div class="overflow-x-auto">
                <table class="curriculum-table min-w-full text-sm">
                    <thead class="curriculum-table-head text-left">
                        <tr>
                            {{-- No: Paksa sekecil mungkin --}}
                            <th class="px-4 py-3 w-1 whitespace-nowrap text-center">No</th>
                            
                            {{-- Tahun: Paksa sekecil mungkin biar rapet ke No --}}
                            <th class="px-4 py-3 w-1 whitespace-nowrap text-center">{{ __('public.research.table_year') }}</th>
                            
                            {{-- Judul: Biarkan fleksibel --}}
                            <th class="px-4 py-3">{{ __('public.research.table_title') }}</th>
                            
                            {{-- Researcher: Kasih lebar lebih besar (misal 35% atau 40% layar) --}}
                            <th class="px-4 py-3 w-1/3 min-w-[200px]">{{ __('public.research.table_author') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="border-t border-slate-100 hover:bg-slate-50/50 transition-colors">
                            <td class="px-4 py-3 text-center text-slate-400">1</td>
                            <td class="px-4 py-3 text-center font-medium">2026</td>
                            <td class="px-4 py-3">
                                <div class="font-semibold text-slate-700">
                                    Pengembangan AI untuk Deteksi Hama Padi di Lahan Gambat
                                </div>
                            </td>
                            {{-- Researcher jadi lebih panjang ruangnya --}}
                            <td class="px-4 py-3 text-slate-600 italic">
                                Tama, M.T., Dr. Eng. Heru Susanto, dkk.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
@endsection