@extends('layouts.app')

@section('title', 'Barang Keluar - Inventaris Barang')


@section('content')
@php
    // Tombol hanya muncul kalau ada tenant aktif (milik sendiri atau dipilih admin)
    $bisaKelola = \App\Support\TenantContext::current() !== null;
    $user = auth()->user();
    $bisaTambah = $bisaKelola && $user->can('barangkeluar.create');
    $bisaUbah   = $bisaKelola && $user->can('barangkeluar.update');
    $bisaHapus  = $bisaKelola && $user->can('barangkeluar.delete');
@endphp

<div class="max-w-7xl mx-auto py-6 space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-slate-900 dark:text-white flex items-center gap-3">
                <span class="p-2 bg-rose-50 dark:bg-rose-500/10 rounded-lg text-rose-600 dark:text-rose-400 border border-rose-100 dark:border-rose-500/20">
                    <i class="fa-solid fa-upload"></i>
                </span>
                Barang Keluar
            </h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-2">Kelola data transaksi pengeluaran barang dari gudang.</p>
        </div>

        @if ($bisaTambah)
        <button data-modal-target="add-modal"
            class="inline-flex items-center justify-center gap-2 bg-brand-600 text-white px-5 py-2.5 rounded-lg shadow-[0_4px_14px_0_rgba(6,182,212,0.39)] hover:bg-brand-500 hover:shadow-[0_6px_20px_rgba(6,182,212,0.23)] transition-all duration-300 font-semibold text-sm">
            <i class="fa-solid fa-plus"></i> Tambah Barang Keluar
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
                <h2 class="text-lg font-bold text-slate-900 dark:text-white uppercase tracking-wider">Daftar Barang Keluar</h2>
            </div>
            
            <!-- Custom Search & Length Controls -->
            <div class="flex flex-col sm:flex-row items-center gap-3 w-full md:w-auto">
                <div class="relative w-full sm:w-64 group">
                    <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-sm text-slate-400 group-focus-within:text-brand-500 transition-colors"></i>
                    <input type="text" id="customSearch" 
                           class="w-full pl-9 pr-3 py-2 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-lg text-sm focus:ring-2 focus:ring-brand-500/50 focus:border-brand-500 outline-none text-slate-900 dark:text-white transition-all placeholder:text-slate-400" 
                           placeholder="Cari barang keluar...">
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
            <table id="tabelBarangKeluar" class="w-full text-sm text-left text-slate-600 dark:text-slate-300 whitespace-nowrap">
                <thead class="text-xs uppercase bg-slate-50 dark:bg-slate-800/50 text-slate-700 dark:text-slate-300 border-b border-slate-200 dark:border-slate-700 font-semibold tracking-wider">
                    <tr>
                        <th class="py-3 px-5 font-bold w-16">No.</th>
                        <th class="py-3 px-5 font-bold">No Barang Keluar</th>
                        <th class="py-3 px-5 font-bold">Tanggal</th>
                        <th class="py-3 px-5 font-bold">Nama Barang</th>
                        <th class="py-3 px-5 font-bold">Satuan</th>
                        <th class="py-3 px-5 font-bold text-center">Jumlah Keluar</th>
                        @if ($bisaUbah || $bisaHapus)
                        <th class="py-3 px-5 font-bold text-center w-48">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/50">
                    @foreach ($barangkeluars as $index => $barangkeluar)
                    <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/30 transition-colors align-middle">
                        <td class="py-3 px-5">{{ $index + 1 }}</td>
                        
                        <td class="py-3 px-5">
                            <span class="font-mono text-xs font-semibold text-brand-600 dark:text-brand-400 bg-brand-50/50 dark:bg-brand-500/5 px-2 py-1 rounded border border-brand-100/50 dark:border-brand-500/10">
                                {{ $barangkeluar->no_barangkeluar }}
                            </span>
                        </td>
                        
                        <td class="py-3 px-5">
                            <div class="flex items-center gap-2">
                                <i class="fa-regular fa-calendar text-slate-400"></i>
                                {{ $barangkeluar->tanggal }}
                            </div>
                        </td>
                        
                        <td class="py-3 px-5 font-medium text-slate-900 dark:text-white">{{ $barangkeluar->dataBarang->nama_barang ?? '-' }}</td>
                        <td class="py-3 px-5">{{ $barangkeluar->dataBarang->satuan->nama ?? '-' }}</td>
                        
                        <td class="py-3 px-5 text-center">
                            <!-- Warna Rose untuk menandakan barang keluar (stok berkurang) -->
                            <span class="inline-flex items-center justify-center min-w-[3rem] px-3 py-1 font-mono text-sm font-bold rounded-lg text-rose-700 bg-rose-50 dark:text-rose-400 dark:bg-rose-500/10 border border-rose-200 dark:border-rose-500/20">
                                -{{ $barangkeluar->jumlah_keluar }}
                            </span>
                        </td>
                        
                        @if ($bisaUbah || $bisaHapus)
                        <td class="py-3 px-5">
                            <div class="flex items-center justify-center gap-2">
                                @if ($bisaUbah)
                                <button type="button" data-modal-target="edit-modal-{{ $barangkeluar->id }}"
                                    class="flex items-center gap-1.5 px-2.5 py-1.5 text-xs font-semibold text-amber-700 bg-amber-50 hover:bg-amber-100 dark:text-amber-400 dark:bg-amber-500/10 dark:hover:bg-amber-500/20 border border-amber-200 dark:border-amber-500/30 rounded-md transition-colors">
                                    <i class="fa-solid fa-pen"></i> Edit
                                </button>
                                @endif

                                @if ($bisaHapus)
                                <form action="{{ route('barangkeluar.destroy', $barangkeluar->id) }}" method="POST" onsubmit="return confirm('Yakin ingin hapus?')">
                                    @csrf
                                    @method('DELETE')
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
            <div id="table-info" class="text-xs text-slate-500 dark:text-slate-400 font-medium font-mono tracking-wider"></div>
            <div id="table-pagination" class="flex items-center gap-1"></div>
        </div>
    </div>

    <!-- ================= MODAL EDIT ================= -->
    @if ($bisaUbah)
    @foreach ($barangkeluars as $barangkeluar)
    @php
        $stokSekarang = $barangkeluar->dataBarang->stok ?? 0;
        // Stok sebelum transaksi ini = stok sekarang + jumlah keluar lama
        $stokTersedia = $stokSekarang + $barangkeluar->jumlah_keluar;
    @endphp
    <div id="edit-modal-{{ $barangkeluar->id }}"
        class="modal edit-modal hidden fixed inset-0 z-[100] bg-slate-900/60 dark:bg-black/70 backdrop-blur-sm flex justify-center items-center p-4"
        data-old-barang="{{ $barangkeluar->id_databarang }}"
        data-old-jumlah="{{ $barangkeluar->jumlah_keluar }}">
        
        <!-- PERUBAHAN: max-w-md diganti menjadi max-w-2xl -->
        <div class="bg-white dark:bg-[#0b1220] border border-slate-200 dark:border-slate-800 w-full max-w-2xl rounded-2xl shadow-2xl overflow-hidden relative">
            <!-- Aksen atas -->
            <div class="h-1 w-full bg-gradient-to-r from-transparent via-amber-500 to-transparent"></div>

            <div class="p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-bold text-slate-900 dark:text-white">Edit Barang Keluar</h2>
                    <button type="button" data-modal-target="edit-modal-{{ $barangkeluar->id }}" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors">
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>

                <form action="{{ route('barangkeluar.update', $barangkeluar->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <!-- PERUBAHAN: Menggunakan Grid 2 Kolom -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">No Barang Keluar</label>
                            <input type="text" value="{{ $barangkeluar->no_barangkeluar }}" readonly
                                class="w-full bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-500 dark:text-slate-400 font-mono text-sm rounded-lg p-2.5 outline-none cursor-not-allowed">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Tanggal</label>
                            <input type="date" name="tanggal" value="{{ $barangkeluar->tanggal }}" required
                                class="w-full bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white text-sm rounded-lg focus:ring-2 focus:ring-brand-500/50 focus:border-brand-500 p-2.5 outline-none transition-all [color-scheme:light] dark:[color-scheme:dark]">
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Nama Barang</label>
                            <select name="id_databarang" class="js-barang w-full bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white text-sm rounded-lg focus:ring-2 focus:ring-brand-500/50 focus:border-brand-500 p-2.5 outline-none transition-all" required>
                                @foreach ($databarangs as $databarang)
                                <option value="{{ $databarang->id }}" data-stok="{{ $databarang->stok }}" data-satuan="{{ $databarang->satuan->nama ?? '' }}" @selected($databarang->id == $barangkeluar->id_databarang)>
                                    {{ $databarang->nama_barang }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Satuan</label>
                            <input type="text" class="js-satuan w-full bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-500 dark:text-slate-400 text-sm rounded-lg p-2.5 outline-none cursor-not-allowed" value="{{ $barangkeluar->dataBarang->satuan->nama ?? '' }}" readonly>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Stok Tersedia</label>
                            <input type="number" class="js-stok w-full bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-500 dark:text-slate-400 font-mono text-sm rounded-lg p-2.5 outline-none cursor-not-allowed" value="{{ $stokTersedia }}" readonly>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Jumlah Keluar</label>
                            <input type="number" name="jumlah_keluar" class="js-jumlah w-full bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white font-mono text-sm rounded-lg focus:ring-2 focus:ring-brand-500/50 focus:border-brand-500 p-2.5 outline-none transition-all" value="{{ $barangkeluar->jumlah_keluar }}" min="1" required>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Sisa Stok</label>
                            <input type="number" class="js-sisa w-full bg-brand-50 dark:bg-brand-500/10 border border-brand-200 dark:border-brand-500/30 text-brand-700 dark:text-brand-400 font-mono font-bold text-sm rounded-lg p-2.5 outline-none cursor-not-allowed" value="{{ $stokSekarang }}" readonly>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 mt-8 pt-4 border-t border-slate-100 dark:border-slate-800">
                        <button type="button" data-modal-target="edit-modal-{{ $barangkeluar->id }}" class="px-4 py-2 text-sm font-semibold text-slate-600 dark:text-slate-300 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 rounded-lg transition-colors">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 text-sm font-semibold text-white bg-brand-600 hover:bg-brand-500 rounded-lg shadow-[0_4px_14px_0_rgba(6,182,212,0.39)] transition-all">
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
        
        <!-- PERUBAHAN: max-w-md diganti menjadi max-w-2xl -->
        <div class="bg-white dark:bg-[#0b1220] border border-slate-200 dark:border-slate-800 w-full max-w-2xl rounded-2xl shadow-2xl overflow-hidden relative">
            <!-- Aksen atas -->
            <div class="h-1 w-full bg-gradient-to-r from-transparent via-brand-500 to-transparent"></div>

            <div class="p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-bold text-slate-900 dark:text-white">Tambah Barang Keluar</h2>
                    <button type="button" data-modal-target="add-modal" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors">
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>

                <form action="{{ route('barangkeluar.store') }}" method="POST">
                    @csrf

                    <!-- PERUBAHAN: Menggunakan Grid 2 Kolom -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">No Barang Keluar</label>
                            <input type="text" value="{{ $newId }}" readonly
                                class="w-full bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-500 dark:text-slate-400 font-mono text-sm rounded-lg p-2.5 outline-none cursor-not-allowed">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Tanggal</label>
                            <input type="date" name="tanggal" required
                                class="w-full bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white text-sm rounded-lg focus:ring-2 focus:ring-brand-500/50 focus:border-brand-500 p-2.5 outline-none transition-all [color-scheme:light] dark:[color-scheme:dark]">
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Nama Barang</label>
                            <select name="id_databarang" id="id_databarang" required
                                class="w-full bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white text-sm rounded-lg focus:ring-2 focus:ring-brand-500/50 focus:border-brand-500 p-2.5 outline-none transition-all">
                                <option value="">-- Pilih Barang --</option>
                                @foreach ($databarangs as $databarang)
                                <option value="{{ $databarang->id }}" data-stok="{{ $databarang->stok }}" data-satuan="{{ $databarang->satuan->nama ?? '' }}">
                                    {{ $databarang->nama_barang }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Satuan</label>
                            <input type="text" id="satuan" readonly
                                class="w-full bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-500 dark:text-slate-400 text-sm rounded-lg p-2.5 outline-none cursor-not-allowed">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Stok Tersedia</label>
                            <input type="number" id="stok" readonly
                                class="w-full bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-500 dark:text-slate-400 font-mono text-sm rounded-lg p-2.5 outline-none cursor-not-allowed">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Jumlah Keluar</label>
                            <input type="number" name="jumlah_keluar" id="jumlah_keluar" min="1" required
                                class="w-full bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white font-mono text-sm rounded-lg focus:ring-2 focus:ring-brand-500/50 focus:border-brand-500 p-2.5 outline-none transition-all">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Sisa Stok</label>
                            <input type="number" id="sisa_stok" readonly
                                class="w-full bg-brand-50 dark:bg-brand-500/10 border border-brand-200 dark:border-brand-500/30 text-brand-700 dark:text-brand-400 font-mono font-bold text-sm rounded-lg p-2.5 outline-none cursor-not-allowed">
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 mt-8 pt-4 border-t border-slate-100 dark:border-slate-800">
                        <button type="button" data-modal-target="add-modal" class="px-4 py-2 text-sm font-semibold text-slate-600 dark:text-slate-300 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 rounded-lg transition-colors">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 text-sm font-semibold text-white bg-brand-600 hover:bg-brand-500 rounded-lg shadow-[0_4px_14px_0_rgba(6,182,212,0.39)] transition-all">
                            Simpan Data
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
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
<style>
    .dataTables_wrapper .dataTables_length, .dataTables_wrapper .dataTables_filter, .dataTables_wrapper .dataTables_info, .dataTables_wrapper .dataTables_paginate { display: none !important; }
    table.dataTable.no-footer { border-bottom: none; }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script>
    $(document).ready(function() {
        let table = $('#tabelBarangKeluar').DataTable({
            dom: 't', order: [[0, 'asc']], pageLength: 10,
            columnDefs: [{ orderable: false, targets: -1 }],
            language: { emptyTable: "Tidak ada data barang keluar", zeroRecords: "Pencarian tidak ditemukan" }
        });

        $('#customSearch').on('keyup', function() { table.search(this.value).draw(); });
        $('#customLength').on('change', function() { table.page.len(this.value).draw(); });

        table.on('draw', function() {
            let info = table.page.info();
            let infoText = info.total === 0 ? 'Tidak ada data untuk ditampilkan' : `Menampilkan ${info.start + 1} - ${info.end} dari ${info.total} data`;
            $('#table-info').html(infoText);

            let paginationHtml = '';
            paginationHtml += `<button type="button" class="btn-prev px-3 py-1.5 text-xs font-semibold text-slate-600 dark:text-slate-300 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-md hover:bg-slate-50 dark:hover:bg-slate-700 disabled:opacity-50 transition-colors ${info.page === 0 ? 'disabled' : ''}"><i class="fa-solid fa-chevron-left text-[10px] mr-1"></i> Kembali</button>`;
            if (info.pages > 0) paginationHtml += `<span class="px-3 py-1.5 text-xs font-semibold bg-brand-50 text-brand-700 dark:bg-brand-500/10 dark:text-brand-400 border border-brand-100 dark:border-brand-500/20 rounded-md">Hal ${info.page + 1} / ${info.pages}</span>`;
            paginationHtml += `<button type="button" class="btn-next px-3 py-1.5 text-xs font-semibold text-slate-600 dark:text-slate-300 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-md hover:bg-slate-50 dark:hover:bg-slate-700 disabled:opacity-50 transition-colors ${(info.page + 1) === info.pages || info.pages === 0 ? 'disabled' : ''}">Lanjut <i class="fa-solid fa-chevron-right text-[10px] ml-1"></i></button>`;
            $('#table-pagination').html(paginationHtml);

            $('.btn-prev').off('click').on('click', function() { if (!$(this).prop('disabled')) table.page('previous').draw('page'); });
            $('.btn-next').off('click').on('click', function() { if (!$(this).prop('disabled')) table.page('next').draw('page'); });
        });
        table.draw();

        $(document).on('click', '[data-modal-target]', function() { $('#' + $(this).data('modal-target')).toggleClass('hidden'); });

        // ===== LOGIKA STOK BAWAAN ANDA (100% UTUH) =====
        function hitungSisaTambah() {
            const stok = parseInt($('#stok').val()) || 0;
            const jumlah = parseInt($('#jumlah_keluar').val()) || 0;
            $('#sisa_stok').val(stok - jumlah);
        }

        $('#id_databarang').on('change', function() {
            const opt = $('option:selected', this);
            $('#stok').val(opt.data('stok'));
            $('#satuan').val(opt.data('satuan'));
            hitungSisaTambah();
        });

        $('#jumlah_keluar').on('input', hitungSisaTambah);

        function hitungEdit(modal) {
            const stok = parseInt(modal.find('.js-stok').val()) || 0;
            const jumlah = parseInt(modal.find('.js-jumlah').val()) || 0;
            modal.find('.js-sisa').val(stok - jumlah);
        }

        $(document).on('change', '.edit-modal .js-barang', function() {
            const modal = $(this).closest('.edit-modal');
            const opt = $('option:selected', this);
            const oldBarang = String(modal.data('old-barang'));
            const oldJumlah = parseInt(modal.data('old-jumlah')) || 0;

            // Kalau kembali ke barang yang sama, stok lama dikembalikan dulu
            let tersedia = parseInt(opt.data('stok')) || 0;
            if (String(opt.val()) === oldBarang) {
                tersedia += oldJumlah;
            }

            modal.find('.js-stok').val(tersedia);
            modal.find('.js-satuan').val(opt.data('satuan'));
            hitungEdit(modal);
        });

        $(document).on('input', '.edit-modal .js-jumlah', function() {
            hitungEdit($(this).closest('.edit-modal'));
        });
    });
</script>
@endpush