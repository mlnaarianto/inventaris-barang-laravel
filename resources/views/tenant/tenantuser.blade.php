@extends('layouts.app')

@section('title', 'Daftar Tenant - Inventaris Barang')

@section('content')
<div class="max-w-7xl mx-auto py-6 space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-slate-900 dark:text-white flex items-center gap-3">
                <span class="p-2 bg-brand-50 dark:bg-brand-500/10 rounded-lg text-brand-600 dark:text-brand-400 border border-brand-100 dark:border-brand-500/20">
                    <i class="fa-solid fa-building"></i>
                </span>
                Daftar Tenant
            </h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-2">Kelola data dan status pendaftaran tenant sistem.</p>
        </div>
    </div>

    <!-- Tenant Table Section -->
    <div class="bg-white dark:bg-[#0b1220] border border-slate-200 dark:border-slate-800 rounded-xl shadow-sm overflow-hidden flex flex-col">
        
        <!-- Table Header & Controls -->
        <div class="p-5 border-b border-slate-200 dark:border-slate-800 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-slate-100 dark:bg-slate-800 rounded-lg text-slate-600 dark:text-slate-300">
                    <i class="fa-solid fa-list-ul"></i>
                </div>
                <h2 class="text-lg font-bold text-slate-900 dark:text-white">Data Tenant</h2>
            </div>
            
            <!-- Custom Search & Length Controls -->
            <div class="flex flex-col sm:flex-row items-center gap-3 w-full md:w-auto">
                <div class="relative w-full sm:w-64 group">
                    <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-sm text-slate-400 group-focus-within:text-brand-500 transition-colors"></i>
                    <input type="text" id="customSearch" 
                           class="w-full pl-9 pr-3 py-2 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-lg text-sm focus:ring-2 focus:ring-brand-500/50 focus:border-brand-500 outline-none text-slate-900 dark:text-white transition-all placeholder:text-slate-400" 
                           placeholder="Cari tenant...">
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
            <table id="tabelTenant" class="w-full text-sm text-left text-slate-600 dark:text-slate-300 whitespace-nowrap">
                <thead class="text-xs uppercase bg-slate-50 dark:bg-slate-800/50 text-slate-700 dark:text-slate-300 border-b border-slate-200 dark:border-slate-700">
                    <tr>
                        <th class="py-3 px-5 font-bold">Nama Tenant</th>
                        <th class="py-3 px-5 font-bold">Email</th>
                        <th class="py-3 px-5 font-bold">Perusahaan</th>
                        <th class="py-3 px-5 font-bold">Kode</th>
                        <th class="py-3 px-5 font-bold">Telepon</th>
                        <th class="py-3 px-5 font-bold">Alamat</th>
                        <th class="py-3 px-5 font-bold text-center">Status</th>
                        <th class="py-3 px-5 font-bold text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/50">
                    @foreach ($tenants as $tenant)
                        <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/30 transition-colors align-middle">
                            <td class="py-3 px-5 font-medium text-slate-900 dark:text-white">
                                {{ $tenant->nama }}
                            </td>
                            <td class="py-3 px-5">
                                {{ $tenant->email }}
                            </td>
                            <td class="py-3 px-5 text-brand-600 dark:text-brand-400 font-medium">
                                {{ $tenant->nama_perusahaan }}
                            </td>
                            <td class="py-3 px-5 font-semibold">
                                {{ $tenant->kode }}
                            </td>
                            <td class="py-3 px-5">
                                {{ $tenant->telepon }}
                            </td>
                            <td class="py-3 px-5">
                                {{ $tenant->alamat }}
                            </td>
                            <td class="py-3 px-5">
                                <div class="flex justify-center">
                                    @if ($tenant->status == 'pending')
                                        <span class="inline-flex items-center gap-1.5 py-1 px-2.5 rounded-md text-xs font-semibold bg-amber-50 text-amber-700 dark:bg-amber-500/10 dark:text-amber-400 border border-amber-100 dark:border-amber-500/20">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Pending
                                        </span>
                                    @elseif ($tenant->status == 'aktif')
                                        <span class="inline-flex items-center gap-1.5 py-1 px-2.5 rounded-md text-xs font-semibold bg-emerald-50 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-500/20">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Aktif
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 py-1 px-2.5 rounded-md text-xs font-semibold bg-rose-50 text-rose-700 dark:bg-rose-500/10 dark:text-rose-400 border border-rose-100 dark:border-rose-500/20">
                                            <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> Nonaktif
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="py-3 px-5">
                                <div class="flex items-center justify-center gap-2">
                                    @if ($tenant->status == 'pending' || $tenant->status == 'nonaktif')
                                        <form action="{{ route('tenantuser.approve', $tenant->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" title="Aktifkan Tenant"
                                                class="flex items-center gap-1.5 px-2.5 py-1.5 text-xs font-semibold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 dark:text-emerald-400 dark:bg-emerald-500/10 dark:hover:bg-emerald-500/20 border border-emerald-200 dark:border-emerald-500/30 rounded-md transition-colors">
                                                <i class="fa-solid fa-check"></i> Aktifkan
                                            </button>
                                        </form>
                                    @endif
                                    
                                    @if ($tenant->status == 'pending' || $tenant->status == 'aktif')
                                        <form action="{{ route('tenantuser.reject', $tenant->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" title="Nonaktifkan Tenant"
                                                class="flex items-center gap-1.5 px-2.5 py-1.5 text-xs font-semibold text-rose-700 bg-rose-50 hover:bg-rose-100 dark:text-rose-400 dark:bg-rose-500/10 dark:hover:bg-rose-500/20 border border-rose-200 dark:border-rose-500/30 rounded-md transition-colors">
                                                <i class="fa-solid fa-ban"></i> Nonaktifkan
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Custom Footer: Info & Pagination -->
        <div class="p-5 border-t border-slate-200 dark:border-slate-800 flex flex-col sm:flex-row justify-between items-center gap-4 bg-slate-50/50 dark:bg-slate-800/20">
            <div id="table-info" class="text-xs text-slate-500 dark:text-slate-400 font-medium">
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
        // 1. Inisialisasi DataTables dengan DOM minimal (hanya tabel)
        let table = $('#tabelTenant').DataTable({
            dom: 't', // Hanya render tabel, tanpa kontrol default
            order: [[0, 'asc']], // Urutkan berdasarkan Nama (Kolom ke-0) secara default
            pageLength: 10,
            columnDefs: [
                { orderable: false, targets: [6, 7] } // Matikan sort di kolom Status (6) dan Aksi (7)
            ],
            language: {
                emptyTable: "Tidak ada data tenant",
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

        // 4. Render Custom Info & Pagination setiap kali tabel digambar ulang (draw)
        table.on('draw', function() {
            let info = table.page.info();
            
            // --- A. Render Teks Info ---
            let infoText = '';
            if (info.total === 0) {
                infoText = 'Tidak ada data untuk ditampilkan';
            } else {
                infoText = `Menampilkan <span class="text-slate-900 dark:text-white font-bold">${info.start + 1}</span> sampai <span class="text-slate-900 dark:text-white font-bold">${info.end}</span> dari <span class="text-slate-900 dark:text-white font-bold">${info.total}</span> tenant`;
            }
            $('#table-info').html(infoText);

            // --- B. Render Tombol Pagination ---
            let paginationHtml = '';

            // Tombol Previous
            let prevDisabled = info.page === 0 ? 'disabled' : '';
            paginationHtml += `
                <button type="button" class="btn-prev px-3 py-1.5 text-xs font-semibold text-slate-600 dark:text-slate-300 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-md hover:bg-slate-50 dark:hover:bg-slate-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors ${prevDisabled}">
                    <i class="fa-solid fa-chevron-left text-[10px] mr-1"></i> Kembali
                </button>
            `;

            // Info Halaman
            if (info.pages > 0) {
                paginationHtml += `
                    <span class="px-3 py-1.5 text-xs font-semibold bg-brand-50 text-brand-700 dark:bg-brand-500/10 dark:text-brand-400 border border-brand-100 dark:border-brand-500/20 rounded-md">
                        Halaman ${info.page + 1} dari ${info.pages}
                    </span>
                `;
            }

            // Tombol Next
            let nextDisabled = (info.page + 1) === info.pages || info.pages === 0 ? 'disabled' : '';
            paginationHtml += `
                <button type="button" class="btn-next px-3 py-1.5 text-xs font-semibold text-slate-600 dark:text-slate-300 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-md hover:bg-slate-50 dark:hover:bg-slate-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors ${nextDisabled}">
                    Lanjut <i class="fa-solid fa-chevron-right text-[10px] ml-1"></i>
                </button>
            `;

            $('#table-pagination').html(paginationHtml);

            // --- C. Bind Event Listener Tombol Pagination ---
            $('.btn-prev').off('click').on('click', function() {
                if (!$(this).prop('disabled')) table.page('previous').draw('page');
            });
            
            $('.btn-next').off('click').on('click', function() {
                if (!$(this).prop('disabled')) table.page('next').draw('page');
            });
        });

        // Trigger draw pertama kali agar UI langsung muncul
        table.draw();
    });
</script>
@endpush