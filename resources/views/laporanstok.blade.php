@extends('layouts.app')

@section('content')
@php
    $tampilTenant = $tenants->count() > 1;
    $namaTenant = $tenants->keyBy('id');
@endphp

<div class="max-w-7xl mx-auto py-6 space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-slate-900 dark:text-white flex items-center gap-3">
                <span class="p-2 bg-brand-50 dark:bg-brand-500/10 rounded-lg text-brand-600 dark:text-brand-400 border border-brand-100 dark:border-brand-500/20">
                    <i class="fa-solid fa-chart-bar"></i>
                </span>
                Laporan Stok Barang
            </h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-2">Ringkasan ketersediaan dan jumlah stok barang saat ini.</p>
        </div>
    </div>

    <!-- Table Card -->
    <div class="bg-white dark:bg-[#0b1220] border border-slate-200 dark:border-slate-800 rounded-xl shadow-sm overflow-hidden flex flex-col">
        
        <!-- Table Header & Controls -->
        <div class="p-5 border-b border-slate-200 dark:border-slate-800 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-slate-100 dark:bg-slate-800 rounded-lg text-slate-600 dark:text-slate-300">
                    <i class="fa-solid fa-boxes-stacked"></i>
                </div>
                <h2 class="text-lg font-bold text-slate-900 dark:text-white uppercase tracking-wider">Tabel Stok Barang</h2>
            </div>
            
            <!-- Custom Search & Length Controls -->
            <div class="flex flex-col sm:flex-row items-center gap-3 w-full md:w-auto">
                <div class="relative w-full sm:w-64 group">
                    <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-sm text-slate-400 group-focus-within:text-brand-500 transition-colors"></i>
                    <input type="text" id="customSearch" 
                           class="w-full pl-9 pr-3 py-2 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-lg text-sm focus:ring-2 focus:ring-brand-500/50 focus:border-brand-500 outline-none text-slate-900 dark:text-white transition-all placeholder:text-slate-400" 
                           placeholder="Cari barang...">
                </div>
                <div class="flex items-center gap-2 w-full sm:w-auto">
                    <span class="text-xs text-slate-500 dark:text-slate-400 whitespace-nowrap hidden sm:block">Tampilkan:</span>
                    <select id="customLength" class="w-full sm:w-auto bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-lg text-sm py-2 px-3 focus:ring-2 focus:ring-brand-500/50 focus:border-brand-500 outline-none text-slate-900 dark:text-white cursor-pointer transition-all">
                        <option value="5">5 Baris</option>
                        <option value="10" selected>10 Baris</option>
                        <option value="25">25 Baris</option>
                        <option value="50">50 Baris</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Table Wrapper -->
        <div class="overflow-x-auto">
            <table id="tabelStokBarang" class="w-full text-sm text-left text-slate-600 dark:text-slate-300 whitespace-nowrap">
                <thead class="text-xs uppercase bg-slate-50 dark:bg-slate-800/50 text-slate-700 dark:text-slate-300 border-b border-slate-200 dark:border-slate-700 font-semibold tracking-wider">
                    <tr>
                        <th class="py-3 px-5 font-bold w-16">No</th>
                        @if ($tampilTenant)
                        <th class="py-3 px-5 font-bold">Tenant</th>
                        @endif
                        <th class="py-3 px-5 font-bold">ID Barang</th>
                        <th class="py-3 px-5 font-bold">Nama Barang</th>
                        <th class="py-3 px-5 font-bold">Satuan</th>
                        <th class="py-3 px-5 font-bold">Jenis Barang</th>
                        <th class="py-3 px-5 font-bold text-center">Stok Tersedia</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/50">
                    @foreach ($stokBarangs as $index => $barang)
                    <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/30 transition-colors align-middle">
                        <td class="py-3 px-5">{{ $index + 1 }}</td>
                        
                        @if ($tampilTenant)
                        <td class="py-3 px-5">
                            <span class="inline-flex items-center gap-1.5 py-1 px-2.5 rounded-md text-xs font-semibold bg-brand-50 text-brand-700 dark:bg-brand-500/10 dark:text-brand-400 border border-brand-100 dark:border-brand-500/20">
                                {{ $namaTenant[$barang->id_tenant]->nama_perusahaan ?? '-' }}
                            </span>
                        </td>
                        @endif
                        
                        <td class="py-3 px-5">
                            <span class="font-mono text-xs font-semibold text-brand-600 dark:text-brand-400 bg-brand-50/50 dark:bg-brand-500/5 px-2 py-1 rounded border border-brand-100/50 dark:border-brand-500/10">
                                {{ $barang->id_barang }}
                            </span>
                        </td>
                        <td class="py-3 px-5 font-medium text-slate-900 dark:text-white">{{ $barang->nama_barang }}</td>
                        <td class="py-3 px-5">{{ $barang->satuan->nama ?? '-' }}</td>
                        <td class="py-3 px-5">{{ $barang->jenisbarang->jenis_barang ?? '-' }}</td>
                        <td class="py-3 px-5 text-center">
                            <!-- Menonjolkan angka stok dengan font mono dan warna khusus -->
                            <span class="inline-flex items-center justify-center min-w-[3rem] px-3 py-1 font-mono text-sm font-bold rounded-lg 
                                {{ $barang->stok > 10 ? 'text-emerald-700 bg-emerald-50 dark:text-emerald-400 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/20' : '' }}
                                {{ $barang->stok > 0 && $barang->stok <= 10 ? 'text-amber-700 bg-amber-50 dark:text-amber-400 dark:bg-amber-500/10 border border-amber-200 dark:border-amber-500/20' : '' }}
                                {{ $barang->stok == 0 ? 'text-rose-700 bg-rose-50 dark:text-rose-400 dark:bg-rose-500/10 border border-rose-200 dark:border-rose-500/20' : '' }}">
                                {{ $barang->stok }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Custom Footer: Info & Pagination -->
        <div class="p-5 border-t border-slate-200 dark:border-slate-800 flex flex-col sm:flex-row justify-between items-center gap-4 bg-slate-50/50 dark:bg-slate-800/20">
            <div id="table-info" class="text-xs text-slate-500 dark:text-slate-400 font-medium font-mono tracking-wider">
                <!-- Info text akan dimasukkan oleh JS -->
            </div>
            <div id="table-pagination" class="flex items-center gap-1">
                <!-- Tombol pagination akan dimasukkan oleh JS -->
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<!-- DataTables CSS (Hanya butuh base CSS) -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
<style>
    /* Sembunyikan TOTAL semua elemen bawaan DataTables */
    .dataTables_wrapper .dataTables_length, 
    .dataTables_wrapper .dataTables_filter, 
    .dataTables_wrapper .dataTables_info, 
    .dataTables_wrapper .dataTables_paginate {
        display: none !important;
    }
    /* Hilangkan border bawaan */
    table.dataTable.no-footer {
        border-bottom: none;
    }
</style>
@endpush

@push('scripts')
<!-- jQuery & DataTables JS -->
<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>

<script>
    $(document).ready(function() {
        // 1. Inisialisasi DataTables dengan DOM minimal
        let table = $('#tabelStokBarang').DataTable({
            dom: 't', // Hanya render tabel, tanpa kontrol default
            order: [[0, 'asc']], // Urutkan berdasarkan No
            pageLength: 10,
            language: {
                emptyTable: "Tidak ada data stok barang",
                zeroRecords: "Pencarian tidak ditemukan"
            }
        });

        // 2. Bind Custom Search Input
        $('#customSearch').on('keyup', function() {
            table.search(this.value).draw();
        });

        // 3. Bind Custom Length Select
        $('#customLength').on('change', function() {
            table.page.len(this.value).draw();
        });

        // 4. Render Custom Info & Pagination
        table.on('draw', function() {
            let info = table.page.info();
            
            // A. Render Teks Info
            let infoText = '';
            if (info.total === 0) {
                infoText = 'Tidak ada data untuk ditampilkan';
            } else {
                infoText = `Menampilkan ${info.start + 1} - ${info.end} dari ${info.total} barang`;
            }
            $('#table-info').html(infoText);

            // B. Render Tombol Pagination
            let paginationHtml = '';
            let prevDisabled = info.page === 0 ? 'disabled' : '';
            
            paginationHtml += `
                <button type="button" class="btn-prev px-3 py-1.5 text-xs font-semibold text-slate-600 dark:text-slate-300 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-md hover:bg-slate-50 dark:hover:bg-slate-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors ${prevDisabled}">
                    <i class="fa-solid fa-chevron-left text-[10px] mr-1"></i> Kembali
                </button>
            `;

            if (info.pages > 0) {
                paginationHtml += `
                    <span class="px-3 py-1.5 text-xs font-semibold bg-brand-50 text-brand-700 dark:bg-brand-500/10 dark:text-brand-400 border border-brand-100 dark:border-brand-500/20 rounded-md">
                        Halaman ${info.page + 1} dari ${info.pages}
                    </span>
                `;
            }

            let nextDisabled = (info.page + 1) === info.pages || info.pages === 0 ? 'disabled' : '';
            paginationHtml += `
                <button type="button" class="btn-next px-3 py-1.5 text-xs font-semibold text-slate-600 dark:text-slate-300 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-md hover:bg-slate-50 dark:hover:bg-slate-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors ${nextDisabled}">
                    Lanjut <i class="fa-solid fa-chevron-right text-[10px] ml-1"></i>
                </button>
            `;

            $('#table-pagination').html(paginationHtml);

            // C. Bind Events
            $('.btn-prev').off('click').on('click', function() {
                if (!$(this).prop('disabled')) table.page('previous').draw('page');
            });
            
            $('.btn-next').off('click').on('click', function() {
                if (!$(this).prop('disabled')) table.page('next').draw('page');
            });
        });

        // Trigger initial draw
        table.draw();
    });
</script>
@endpush