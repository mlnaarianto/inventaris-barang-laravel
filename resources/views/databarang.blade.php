@extends('layouts.app')

@section('content')
    @php
        $bisaKelola = \App\Support\TenantContext::current() !== null;
        $user = auth()->user();
        $bisaTambah = $bisaKelola && $user->can('databarang.create');
        $bisaUbah   = $bisaKelola && $user->can('databarang.update');
        $bisaHapus  = $bisaKelola && $user->can('databarang.delete');
    @endphp

    <div class="max-w-7xl mx-auto py-6 space-y-6">

        <!-- Header Section -->
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-slate-900 dark:text-white flex items-center gap-3">
                    <span class="p-2 bg-brand-50 dark:bg-brand-500/10 rounded-lg text-brand-600 dark:text-brand-400 border border-brand-100 dark:border-brand-500/20">
                        <i class="fa-solid fa-boxes-stacked"></i>
                    </span>
                    Data Barang
                </h1>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-2">Kelola informasi master data inventaris barang Anda.</p>
            </div>

            @if ($bisaTambah)
                <button data-modal-target="add-modal"
                    class="inline-flex items-center justify-center gap-2 bg-brand-600 text-white px-5 py-2.5 rounded-lg shadow-[0_4px_14px_0_rgba(6,182,212,0.39)] hover:bg-brand-500 hover:shadow-[0_6px_20px_rgba(6,182,212,0.23)] transition-all duration-300 font-semibold text-sm">
                    <i class="fa-solid fa-plus"></i> Tambah Barang
                </button>
            @endif
        </div>

        <!-- Table Card -->
        <div class="bg-white dark:bg-[#0b1220] border border-slate-200 dark:border-slate-800 rounded-xl shadow-sm overflow-hidden flex flex-col">
            
            <!-- Table Header & Controls -->
            <div class="p-5 border-b border-slate-200 dark:border-slate-800 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-slate-100 dark:bg-slate-800 rounded-lg text-slate-600 dark:text-slate-300">
                        <i class="fa-solid fa-list-ul"></i>
                    </div>
                    <h2 class="text-lg font-bold text-slate-900 dark:text-white uppercase tracking-wider">Daftar Data Barang</h2>
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
                <table id="tabelBarang" class="w-full text-sm text-left text-slate-600 dark:text-slate-300 whitespace-nowrap">
                    <thead class="text-xs uppercase bg-slate-50 dark:bg-slate-800/50 text-slate-700 dark:text-slate-300 border-b border-slate-200 dark:border-slate-700 font-semibold tracking-wider">
                        <tr>
                            <th class="py-3 px-5 font-bold w-16">No.</th>
                            <th class="py-3 px-5 font-bold">ID Barang</th>
                            <th class="py-3 px-5 font-bold">Nama Barang</th>
                            <th class="py-3 px-5 font-bold text-center">Stok</th>
                            <th class="py-3 px-5 font-bold">Satuan</th>
                            <th class="py-3 px-5 font-bold">Jenis Barang</th>
                            <th class="py-3 px-5 font-bold">Lokasi</th>
                            @if ($bisaUbah || $bisaHapus)
                                <th class="py-3 px-5 font-bold text-center w-48">Aksi</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800/50">
                        @foreach ($databarangs as $index => $barang)
                            <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/30 transition-colors align-middle">
                                <td class="py-3 px-5">{{ $index + 1 }}</td>
                                
                                <td class="py-3 px-5">
                                    <span class="font-mono text-xs font-semibold text-brand-600 dark:text-brand-400 bg-brand-50/50 dark:bg-brand-500/5 px-2 py-1 rounded border border-brand-100/50 dark:border-brand-500/10">
                                        {{ $barang->id_barang }}
                                    </span>
                                </td>
                                
                                <td class="py-3 px-5 font-medium text-slate-900 dark:text-white">{{ $barang->nama_barang }}</td>
                                
                                <td class="py-3 px-5 text-center">
                                    <span class="inline-flex items-center justify-center min-w-[3rem] px-3 py-1 font-mono text-sm font-bold rounded-lg text-slate-700 bg-slate-100 dark:text-slate-300 dark:bg-slate-800 border border-slate-200 dark:border-slate-700">
                                        {{ $barang->stok }}
                                    </span>
                                </td>
                                
                                <td class="py-3 px-5">{{ $barang->satuan->nama ?? '-' }}</td>
                                <td class="py-3 px-5">{{ $barang->jenisbarang->jenis_barang ?? '-' }}</td>
                                
                                <td class="py-3 px-5">
                                    <div class="flex items-center gap-2">
                                        <i class="fa-solid fa-location-dot text-rose-500 text-xs"></i>
                                        {{ $barang->lokasi }}
                                    </div>
                                </td>
                                
                                @if ($bisaUbah || $bisaHapus)
                                    <td class="py-3 px-5">
                                        <div class="flex items-center justify-center gap-2">
                                            @if ($bisaUbah)
                                                <button type="button" data-modal-target="edit-modal-{{ $barang->id }}"
                                                    class="flex items-center gap-1.5 px-2.5 py-1.5 text-xs font-semibold text-amber-700 bg-amber-50 hover:bg-amber-100 dark:text-amber-400 dark:bg-amber-500/10 dark:hover:bg-amber-500/20 border border-amber-200 dark:border-amber-500/30 rounded-md transition-colors">
                                                    <i class="fa-solid fa-pen"></i> Edit
                                                </button>
                                            @endif

                                            @if ($bisaHapus)
                                                <form action="{{ route('databarangs.destroy', $barang->id) }}" method="POST"
                                                    onsubmit="return confirm('Yakin ingin hapus barang ini?')">
                                                    @csrf @method('DELETE')
                                                    <button type="submit"
                                                        class="flex items-center gap-1.5 px-2.5 py-1.5 text-xs font-semibold text-rose-700 bg-rose-50 hover:bg-rose-100 dark:text-rose-400 dark:bg-rose-500/10 dark:hover:bg-rose-500/20 border border-rose-200 dark:border-rose-500/30 rounded-md transition-colors">
                                                        <i class="fa-solid fa-trash"></i> Hapus
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                @endif
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

        <!-- ================= MODAL EDIT ================= -->
        @if ($bisaUbah)
            @foreach ($databarangs as $barang)
                <div id="edit-modal-{{ $barang->id }}"
                    class="modal hidden fixed inset-0 z-[100] bg-slate-900/60 dark:bg-black/70 backdrop-blur-sm flex justify-center items-center p-4">
                    
                    <!-- PERUBAHAN: max-w-md diubah menjadi max-w-2xl -->
                    <div class="bg-white dark:bg-[#0b1220] border border-slate-200 dark:border-slate-800 w-full max-w-2xl rounded-2xl shadow-2xl overflow-hidden relative">
                        <!-- Aksen atas -->
                        <div class="h-1 w-full bg-gradient-to-r from-transparent via-amber-500 to-transparent"></div>

                        <div class="p-6">
                            <div class="flex items-center justify-between mb-6">
                                <h2 class="text-xl font-bold text-slate-900 dark:text-white">Edit Barang</h2>
                                <button type="button" data-modal-target="edit-modal-{{ $barang->id }}" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors">
                                    <i class="fa-solid fa-xmark text-lg"></i>
                                </button>
                            </div>

                            <form method="POST" action="{{ route('databarangs.update', $barang->id) }}" class="space-y-4">
                                @csrf @method('PUT')
                                
                                <!-- PERUBAHAN: Membuat Grid 2 Kolom untuk Form -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">ID Barang</label>
                                        <input type="text" name="id_barang" value="{{ $barang->id_barang }}" required
                                            class="w-full bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white text-sm rounded-lg focus:ring-2 focus:ring-brand-500/50 focus:border-brand-500 p-2.5 outline-none transition-all placeholder:text-slate-400 dark:placeholder:text-slate-500">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Nama Barang</label>
                                        <input type="text" name="nama_barang" value="{{ $barang->nama_barang }}" required
                                            class="w-full bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white text-sm rounded-lg focus:ring-2 focus:ring-brand-500/50 focus:border-brand-500 p-2.5 outline-none transition-all placeholder:text-slate-400 dark:placeholder:text-slate-500">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Stok</label>
                                        <input type="number" name="stok" value="{{ $barang->stok }}" required
                                            class="w-full bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white text-sm rounded-lg focus:ring-2 focus:ring-brand-500/50 focus:border-brand-500 p-2.5 outline-none transition-all placeholder:text-slate-400 dark:placeholder:text-slate-500">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Lokasi</label>
                                        <input type="text" name="lokasi" value="{{ $barang->lokasi }}" required
                                            class="w-full bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white text-sm rounded-lg focus:ring-2 focus:ring-brand-500/50 focus:border-brand-500 p-2.5 outline-none transition-all placeholder:text-slate-400 dark:placeholder:text-slate-500">
                                    </div>

                                    <div>
                                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Satuan</label>
                                        <select name="id_satuan" required
                                            class="w-full bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white text-sm rounded-lg focus:ring-2 focus:ring-brand-500/50 focus:border-brand-500 p-2.5 outline-none transition-all">
                                            @foreach ($daftarSatuan as $satuan)
                                                <option value="{{ $satuan->id }}" @selected($satuan->id == $barang->id_satuan)>
                                                    {{ $satuan->nama }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Jenis Barang</label>
                                        <select name="id_jenisbarang" required
                                            class="w-full bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white text-sm rounded-lg focus:ring-2 focus:ring-brand-500/50 focus:border-brand-500 p-2.5 outline-none transition-all">
                                            @foreach ($daftarJenis as $jenis)
                                                <option value="{{ $jenis->id }}" @selected($jenis->id == $barang->id_jenisbarang)>
                                                    {{ $jenis->jenis_barang }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <!-- Tombol diletakkan di luar grid agar rata kanan penuh -->
                                <div class="flex justify-end gap-3 mt-8 pt-4 border-t border-slate-100 dark:border-slate-800">
                                    <button type="button" data-modal-target="edit-modal-{{ $barang->id }}"
                                        class="px-4 py-2 text-sm font-semibold text-slate-600 dark:text-slate-300 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 rounded-lg transition-colors">
                                        Batal
                                    </button>
                                    <button type="submit"
                                        class="px-4 py-2 text-sm font-semibold text-white bg-brand-600 hover:bg-brand-500 rounded-lg shadow-[0_4px_14px_0_rgba(6,182,212,0.39)] transition-all">
                                        Simpan Perubahan
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        @endif

        <!-- ================= MODAL TAMBAH ================= -->
        @if ($bisaTambah)
            <div id="add-modal"
                class="modal hidden fixed inset-0 z-[100] bg-slate-900/60 dark:bg-black/70 backdrop-blur-sm flex justify-center items-center p-4">
                
                <!-- PERUBAHAN: max-w-md diubah menjadi max-w-2xl -->
                <div class="bg-white dark:bg-[#0b1220] border border-slate-200 dark:border-slate-800 w-full max-w-2xl rounded-2xl shadow-2xl overflow-hidden relative">
                    <!-- Aksen atas -->
                    <div class="h-1 w-full bg-gradient-to-r from-transparent via-brand-500 to-transparent"></div>

                    <div class="p-6">
                        <div class="flex items-center justify-between mb-6">
                            <h2 class="text-xl font-bold text-slate-900 dark:text-white">Tambah Barang</h2>
                            <button type="button" data-modal-target="add-modal" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors">
                                <i class="fa-solid fa-xmark text-lg"></i>
                            </button>
                        </div>

                        <form method="POST" action="{{ route('databarangs.store') }}" class="space-y-4">
                            @csrf
                            
                            <!-- PERUBAHAN: Membuat Grid 2 Kolom untuk Form -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">ID Barang</label>
                                    <input type="text" name="id_barang" placeholder="Contoh: BRG-001" required
                                        class="w-full bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white text-sm rounded-lg focus:ring-2 focus:ring-brand-500/50 focus:border-brand-500 p-2.5 outline-none transition-all placeholder:text-slate-400 dark:placeholder:text-slate-500">
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Nama Barang</label>
                                    <input type="text" name="nama_barang" placeholder="Masukkan nama barang" required
                                        class="w-full bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white text-sm rounded-lg focus:ring-2 focus:ring-brand-500/50 focus:border-brand-500 p-2.5 outline-none transition-all placeholder:text-slate-400 dark:placeholder:text-slate-500">
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Stok Awal</label>
                                    <input type="number" name="stok" placeholder="0" required
                                        class="w-full bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white text-sm rounded-lg focus:ring-2 focus:ring-brand-500/50 focus:border-brand-500 p-2.5 outline-none transition-all placeholder:text-slate-400 dark:placeholder:text-slate-500">
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Lokasi Penyimpanan</label>
                                    <input type="text" name="lokasi" placeholder="Contoh: Rak A-1" required
                                        class="w-full bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white text-sm rounded-lg focus:ring-2 focus:ring-brand-500/50 focus:border-brand-500 p-2.5 outline-none transition-all placeholder:text-slate-400 dark:placeholder:text-slate-500">
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Satuan</label>
                                    <select name="id_satuan" required
                                        class="w-full bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white text-sm rounded-lg focus:ring-2 focus:ring-brand-500/50 focus:border-brand-500 p-2.5 outline-none transition-all">
                                        <option value="">-- Pilih Satuan --</option>
                                        @foreach ($daftarSatuan as $satuan)
                                            <option value="{{ $satuan->id }}">{{ $satuan->nama }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Jenis Barang</label>
                                    <select name="id_jenisbarang" required
                                        class="w-full bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white text-sm rounded-lg focus:ring-2 focus:ring-brand-500/50 focus:border-brand-500 p-2.5 outline-none transition-all">
                                        <option value="">-- Pilih Jenis --</option>
                                        @foreach ($daftarJenis as $jenis)
                                            <option value="{{ $jenis->id }}">{{ $jenis->jenis_barang }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- Tombol diletakkan di luar grid agar rata kanan penuh -->
                            <div class="flex justify-end gap-3 mt-8 pt-4 border-t border-slate-100 dark:border-slate-800">
                                <button type="button" data-modal-target="add-modal"
                                    class="px-4 py-2 text-sm font-semibold text-slate-600 dark:text-slate-300 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 rounded-lg transition-colors">
                                    Batal
                                </button>
                                <button type="submit"
                                    class="px-4 py-2 text-sm font-semibold text-white bg-brand-600 hover:bg-brand-500 rounded-lg shadow-[0_4px_14px_0_rgba(6,182,212,0.39)] transition-all">
                                    Tambah Barang
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif
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
            let table = $('#tabelBarang').DataTable({
                dom: 't',
                order: [[0, 'asc']],
                pageLength: 10,
                columnDefs: [
                    // Matikan sorting di kolom aksi
                    @if ($bisaUbah || $bisaHapus)
                    { orderable: false, targets: -1 }
                    @endif
                ],
                language: {
                    emptyTable: "Tidak ada data barang",
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

            // --- Modal Toggle Logic Bawaan Anda (Tetap Dipertahankan) ---
            $(document).on('click', '[data-modal-target]', function() {
                $('#' + $(this).data('modal-target')).toggleClass('hidden');
            });
        });
    </script>
@endpush