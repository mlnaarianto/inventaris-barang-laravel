@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-semibold text-gray-800 mb-6 flex items-center gap-2">
        <i class="fa-solid fa-chart-bar"></i>
        <span>Laporan Stok Barang</span>
    </h1>
    <hr class="mb-4 border-gray-300">

    <div class="bg-white shadow-md rounded-lg p-6 mx-auto">
        <div class="flex items-center space-x-2 mb-4">
            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span class="text-lg font-semibold text-gray-700">Tabel Stok Barang</span>
        </div>

        <table id="tabelStokBarang" class="display w-full text-sm text-gray-700">
            <thead class="bg-gray-100 text-gray-700 uppercase text-xs tracking-wider">
                <tr>
                    <th class="px-6 py-4">No</th>
                    <th class="px-6 py-4">ID Barang</th>
                    <th class="px-6 py-4">Nama Barang</th>
                    <th class="px-6 py-4">Satuan</th>
                    <th class="px-6 py-4">Jenis Barang</th>
                    <th class="px-6 py-4">Stok Tersedia</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach ($stokBarangs as $index => $barang)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4">{{ $index + 1 }}</td>
                    <td class="px-6 py-4 font-medium text-gray-800">{{ $barang->id_barang }}</td>
                    <td class="px-6 py-4">{{ $barang->nama_barang }}</td>
                    <td class="px-6 py-4">{{ $barang->satuan->nama ?? '-' }}</td>
                    <td class="px-6 py-4">{{ $barang->jenisbarang->jenis_barang ?? '-' }}</td>
                    <td class="px-6 py-4">{{ $barang->stok }}</td>
                </tr>
                @endforeach
            </tbody>

        </table>
    </div>
</div>
@endsection

@push('scripts')
<!-- DataTables CDN -->
<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">

<script>
    $(document).ready(function() {
        $('#tabelStokBarang').DataTable({
            language: {
                search: "Cari:",
                lengthMenu: "Tampilkan _MENU_ entri",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
                paginate: {
                    first: "Awal",
                    last: "Akhir",
                    next: "Berikutnya",
                    previous: "Sebelumnya"
                },
                zeroRecords: "Tidak ada data ditemukan",
                emptyTable: "Tidak ada data tersedia"
            }

        });
    });
</script>
@endpush