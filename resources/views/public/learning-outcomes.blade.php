@extends('layouts.public')

@section('title', 'Capaian Pembelajaran — ' . config('app.name'))

@section('content')
    @include('public.partials.page-hero', [
        'title'    => 'Capaian Pembelajaran Lulusan',
        'subtitle' => 'Standar kompetensi yang harus dicapai oleh setiap lulusan Program Studi Teknologi Informasi.',
    ])

    <section class="section-wrap public-page-shell">
        <div class="public-panel rounded-2xl border border-[var(--border-soft)] bg-white shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="curriculum-table min-w-full text-sm">
                    <thead class="curriculum-table-head text-left">
                        <tr>
                            <th class="px-4 py-3 w-10 text-center">No</th>
                            <th class="px-4 py-3 w-28">Kode</th>
                            <th class="px-4 py-3">Deskripsi</th>
                        </tr>
                    </thead>
                    <tbody id="cpl-tbody">
                        @forelse($outcomes as $i => $o)
                            <tr class="border-t border-slate-100 hover:bg-slate-50/50 transition-colors">
                                <td class="px-4 py-3 text-center text-slate-400 text-xs">{{ $i + 1 }}</td>
                                <td class="px-4 py-3 font-mono font-semibold text-slate-700 text-xs">{{ $o->code }}</td>
                                <td class="px-4 py-3 text-slate-700 leading-relaxed">{{ $o->description }}</td>
                            </tr>
                        @empty
                            <tr class="border-t border-slate-100">
                                <td class="px-4 py-12 text-center text-slate-500" colspan="3">
                                    Data capaian pembelajaran belum tersedia.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>
@endsection
