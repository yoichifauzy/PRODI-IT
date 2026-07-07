@extends('layouts.admin')

@section('title', 'Preview Data Kurikulum (Courses)')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-6 flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-800">Preview Data Kurikulum</h1>
        <a href="{{ route('admin.courses.index') }}" class="text-amber-600 hover:text-amber-800 font-medium">Batal & Kembali</a>
    </div>

    @if(session('success'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
            {{ session('success') }}
        </div>
    @endif
    
    @if(session('error'))
        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <h2 class="text-xl font-semibold mb-2 text-gray-700">Validasi Data (File: {{ $originalFilename }})</h2>
        <p class="text-gray-600 mb-6 text-sm">Harap cek dengan teliti data di bawah ini. Jika sudah sesuai, klik tombol simpan untuk memasukkan data ke database.</p>
        
        <div class="overflow-x-auto border border-gray-200 rounded-lg mb-6 max-h-[60vh]">
            <table class="min-w-full divide-y divide-gray-200 relative">
                <thead class="bg-gray-50 sticky top-0 shadow-sm z-10">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Semester</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jurusan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kode MK</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama MK</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">SKS Teori</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">SKS Praktik</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($previewData as $index => $row)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-500">{{ $index + 1 }}</td>
                        <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-900">{{ $row['semester'] }}</td>
                        <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-500">{{ $row['major_selection'] ?? '-' }}</td>
                        <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-900">{{ $row['code'] }}</td>
                        <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-900">{{ $row['name'] }}</td>
                        <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-500">{{ $row['credits_theory'] }}</td>
                        <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-500">{{ $row['credits_practice'] }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">Data kosong.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <form action="{{ route('admin.courses.store') }}" method="POST">
            @csrf
            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-6 rounded transition shadow-md">
                Simpan Data
            </button>
        </form>
    </div>
</div>
@endsection
