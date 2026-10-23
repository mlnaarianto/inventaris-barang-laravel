@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-semibold text-gray-800 mb-6 flex items-center gap-2">
            <i class="fa-solid fa-file-export"></i> <span>Laporan Barang Keluar</span>
        </h1>
        <hr class="mb-4 border-gray-300">

        {{-- Form Filter Tanggal --}}
        <form id="form-filter" method="GET" action="{{ route('laporanbarangkeluar') }}"
            class="grid md:grid-cols-4 sm:grid-cols-2 gap-4 mb-6 items-end">

            {{-- Tanggal awal --}}
            <div>
                <label for="tanggal_awal" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Awal</label>
                <input type="date" name="tanggal_awal" id="tanggal_awal" value="{{ request('tanggal_awal') }}"
                    class="w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
            </div>

            {{-- Tanggal akhir --}}
            <div>
                <label for="tanggal_akhir" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Akhir</label>
                <input type="date" name="tanggal_akhir" id="tanggal_akhir" value="{{ request('tanggal_akhir') }}"
                    class="w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                <p id="tanggal-error" class="hidden text-xs text-red-600 mt-1">
                    Tanggal akhir tidak boleh lebih awal dari tanggal awal.
                </p>
            </div>

            {{-- Search --}}
            <div>
                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Cari</label>
                <input type="text" name="search" id="search" value="{{ request('search') }}"
                    placeholder="ID / Nama barang / Satuan…"
                    class="w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
            </div>

            {{-- Tombol --}}
            <div class="grid @can('laporan.export') grid-cols-2 @else grid-cols-1 @endcan gap-2">
                <button type="submit"
                    class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-4 rounded-lg shadow-md transition duration-200">
                    Tampilkan
                </button>

                @can('laporan.export')
                    <a href="{{ route('laporanbarangkeluar.pdf', [
                        'tanggal_awal' => request('tanggal_awal'),
                        'tanggal_akhir' => request('tanggal_akhir'),
                        'search' => request('search'),
                    ]) }}"
                        target="_blank"
                        class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg shadow-md transition duration-200 text-center">
                        Cetak PDF
                    </a>
                @endcan
            </div>
        </form>

        {{-- Table --}}
        <div class="overflow-x-auto bg-white rounded-xl shadow-md ring-1 ring-gray-200">
            <table class="min-w-full text-sm text-left">
                <thead class="bg-gray-100 text-gray-700 uppercase text-xs tracking-wider">
                    <tr>
                        <th class="px-6 py-4">No</th>
                        <th class="px-6 py-4">No Barang Keluar</th>
                        <th class="px-6 py-4">Tanggal</th>
                        <th class="px-6 py-4">Nama Barang</th>
                        <th class="px-6 py-4">Satuan</th>
                        <th class="px-6 py-4">Jumlah Keluar</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse ($barangKeluars as $index => $item)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4">{{ $index + 1 }}</td>
                            <td class="px-6 py-4 font-medium text-gray-800">{{ $item->no_barangkeluar }}</td>
                            <td class="px-6 py-4">{{ $item->tanggal }}</td>
                            <td class="px-6 py-4">{{ $item->dataBarang->nama_barang ?? '-' }}</td>
                            <td class="px-6 py-4">{{ $item->dataBarang->satuan->nama ?? '-' }}</td>
                            <td class="px-6 py-4">{{ $item->jumlah_keluar }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-6 text-center text-gray-500 italic">
                                Tidak ada data barang keluar.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Validasi tanggal dengan JavaScript --}}
    <script>
        document.getElementById('form-filter').addEventListener('submit', function(e) {
            const awal = document.getElementById('tanggal_awal').value;
            const akhir = document.getElementById('tanggal_akhir').value;
            const errorEl = document.getElementById('tanggal-error');

            if (awal && akhir && akhir < awal) {
                e.preventDefault();
                errorEl.classList.remove('hidden');
            } else {
                errorEl.classList.add('hidden');
            }
        });
    </script>
@endsection