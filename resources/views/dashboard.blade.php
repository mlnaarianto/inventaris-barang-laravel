@extends('layouts.app')

@section('content')
@php
$bisaAksi = auth()->user()->canany(['user.update', 'user.delete']);
@endphp

<div x-data="{ isOpen: false }" class="max-w-7xl mx-auto py-6 space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-slate-900 dark:text-white flex items-center gap-3">
                <span class="p-2 bg-brand-50 dark:bg-brand-500/10 rounded-lg text-brand-600 dark:text-brand-400 border border-brand-100 dark:border-brand-500/20">
                    <i class="fa-solid fa-chart-line"></i>
                </span>
                Dashboard Admin
            </h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-2">Ringkasan data inventaris dan pengguna sistem.</p>
        </div>

        @can('user.create')
        <button @click="isOpen = true"
            class="inline-flex items-center justify-center gap-2 bg-brand-600 text-white px-5 py-2.5 rounded-lg shadow-[0_4px_14px_0_rgba(6,182,212,0.39)] hover:bg-brand-500 hover:shadow-[0_6px_20px_rgba(6,182,212,0.23)] transition-all duration-300 font-semibold text-sm">
            <i class="fas fa-plus"></i> Tambah User
        </button>
        @endcan
    </div>

    <!-- Statistik Cards (Grid Layout) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
        <!-- Data Tenant Aktif -->
        <div class="relative bg-white dark:bg-[#0b1220] p-5 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden group hover:border-blue-400 dark:hover:border-blue-500/50 transition-colors">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-blue-500/10 dark:bg-blue-500/5 rounded-full blur-2xl group-hover:bg-blue-500/20 transition-all"></div>
            <div class="flex items-center gap-4 relative z-10">
                <div class="w-12 h-12 rounded-lg bg-blue-50 dark:bg-blue-500/10 flex items-center justify-center text-blue-600 dark:text-blue-400 border border-blue-100 dark:border-blue-500/20">
                    <i class="fa-solid fa-folder-open text-xl"></i>
                </div>
                <div>
                    <p class="text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Tenant Aktif</p>
                    <p class="text-2xl font-bold text-slate-900 dark:text-white leading-none mt-1">{{ $tenantAktif }}</p>
                </div>
            </div>
        </div>

        <!-- Data Tenant Nonaktif -->
        <div class="relative bg-white dark:bg-[#0b1220] p-5 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden group hover:border-rose-400 dark:hover:border-rose-500/50 transition-colors">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-rose-500/10 dark:bg-rose-500/5 rounded-full blur-2xl group-hover:bg-rose-500/20 transition-all"></div>
            <div class="flex items-center gap-4 relative z-10">
                <div class="w-12 h-12 rounded-lg bg-rose-50 dark:bg-rose-500/10 flex items-center justify-center text-rose-600 dark:text-rose-400 border border-rose-100 dark:border-rose-500/20">
                    <i class="fa-solid fa-folder-minus text-xl"></i>
                </div>
                <div>
                    <p class="text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Tenant Nonaktif</p>
                    <p class="text-2xl font-bold text-slate-900 dark:text-white leading-none mt-1">{{ $tenantNonaktif }}</p>
                </div>
            </div>
        </div>

        <!-- Total User -->
        <div class="relative bg-white dark:bg-[#0b1220] p-5 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden group hover:border-amber-400 dark:hover:border-amber-500/50 transition-colors">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-amber-500/10 dark:bg-amber-500/5 rounded-full blur-2xl group-hover:bg-amber-500/20 transition-all"></div>
            <div class="flex items-center gap-4 relative z-10">
                <div class="w-12 h-12 rounded-lg bg-amber-50 dark:bg-amber-500/10 flex items-center justify-center text-amber-600 dark:text-amber-400 border border-amber-100 dark:border-amber-500/20">
                    <i class="fa-solid fa-users text-xl"></i>
                </div>
                <div>
                    <p class="text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Total User</p>
                    <p class="text-2xl font-bold text-slate-900 dark:text-white leading-none mt-1">{{ $userCount }}</p>
                </div>
            </div>
        </div>

        <!-- Total Pengelola -->
        <div class="relative bg-white dark:bg-[#0b1220] p-5 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden group hover:border-emerald-400 dark:hover:border-emerald-500/50 transition-colors">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-emerald-500/10 dark:bg-emerald-500/5 rounded-full blur-2xl group-hover:bg-emerald-500/20 transition-all"></div>
            <div class="flex items-center gap-4 relative z-10">
                <div class="w-12 h-12 rounded-lg bg-emerald-50 dark:bg-emerald-500/10 flex items-center justify-center text-emerald-600 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-500/20">
                    <i class="fa-solid fa-user-tie text-xl"></i>
                </div>
                <div>
                    <p class="text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Total Pengelola</p>
                    <p class="text-2xl font-bold text-slate-900 dark:text-white leading-none mt-1">{{ $pengelolaCount }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Users Table Section -->
    <div class="bg-white dark:bg-[#0b1220] border border-slate-200 dark:border-slate-800 rounded-xl shadow-sm overflow-hidden flex flex-col">
        
        <!-- Table Header & Controls -->
        <div class="p-5 border-b border-slate-200 dark:border-slate-800 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-slate-100 dark:bg-slate-800 rounded-lg text-slate-600 dark:text-slate-300">
                    <i class="fa-solid fa-list-ul"></i>
                </div>
                <h2 class="text-lg font-bold text-slate-900 dark:text-white">Daftar Pengguna</h2>
            </div>
            
            <!-- Custom Search & Length Controls -->
            <div class="flex flex-col sm:flex-row items-center gap-3 w-full md:w-auto">
                <div class="relative w-full sm:w-64 group">
                    <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-sm text-slate-400 group-focus-within:text-brand-500 transition-colors"></i>
                    <input type="text" id="customSearch" 
                           class="w-full pl-9 pr-3 py-2 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-lg text-sm focus:ring-2 focus:ring-brand-500/50 focus:border-brand-500 outline-none text-slate-900 dark:text-white transition-all placeholder:text-slate-400" 
                           placeholder="Cari nama atau email...">
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
            <table id="tabelPengguna" class="w-full text-sm text-left text-slate-600 dark:text-slate-300 whitespace-nowrap">
                <thead class="text-xs uppercase bg-slate-50 dark:bg-slate-800/50 text-slate-700 dark:text-slate-300 border-b border-slate-200 dark:border-slate-700">
                    <tr>
                        <th class="py-3 px-5 font-bold w-16">No</th>
                        <th class="py-3 px-5 font-bold">Nama</th>
                        <th class="py-3 px-5 font-bold">Email</th>
                        <th class="py-3 px-5 font-bold">Role</th>
                        <th class="py-3 px-5 font-bold">Status</th>
                        @if ($bisaAksi)
                        <th class="py-3 px-5 font-bold text-center">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/50">
                    @foreach ($users as $index => $user)
                    <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/30 transition-colors align-middle">
                        <td class="py-3 px-5">{{ $index + 1 }}</td>
                        <td class="py-3 px-5 font-medium text-slate-900 dark:text-white">
                            <div class="flex items-center gap-3">
                                @php
                                    $avatarUser = $user->avatar_url ?: asset('images/1.jpg');
                                @endphp
                                <img src="{{ $avatarUser }}"
                                     onerror="this.src='{{ asset('images/1.jpg') }}'"
                                     alt="{{ $user->nama }}"
                                     class="h-8 w-8 rounded-full object-cover border border-slate-200 dark:border-slate-700 shrink-0">
                                <span>{{ $user->nama }}</span>
                            </div>
                        </td>
                        <td class="py-3 px-5">{{ $user->email }}</td>
                        <td class="py-3 px-5">
                            <span class="inline-flex items-center gap-1.5 py-1 px-2.5 rounded-md text-xs font-semibold bg-brand-50 text-brand-700 dark:bg-brand-500/10 dark:text-brand-400 border border-brand-100 dark:border-brand-500/20">
                                {{ ucfirst($user->getRoleNames()->first() ?? '-') }}
                            </span>
                        </td>
                        <td class="py-3 px-5">
                            @if($user->status === 'tidak_aktif')
                                <span class="inline-flex items-center gap-1.5 py-1 px-2.5 rounded-md text-xs font-semibold bg-rose-50 text-rose-700 dark:bg-rose-500/10 dark:text-rose-400 border border-rose-100 dark:border-rose-500/20">
                                    <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> Nonaktif
                                </span>
                            @elseif($user->status === 'pending')
                                <span class="inline-flex items-center gap-1.5 py-1 px-2.5 rounded-md text-xs font-semibold bg-amber-50 text-amber-700 dark:bg-amber-500/10 dark:text-amber-400 border border-amber-100 dark:border-amber-500/20">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Pending
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 py-1 px-2.5 rounded-md text-xs font-semibold bg-emerald-50 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-500/20">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Aktif
                                </span>
                            @endif
                        </td>
                        @if ($bisaAksi)
                        <td class="py-3 px-5">
                            <div class="flex items-center justify-center gap-2">
                                @can('user.update')
                                @if($user->status !== 'aktif')
                                <form action="{{ route('user.updateStatus', $user->id) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="aktif">
                                    <button type="submit" title="Aktifkan User" 
                                            class="flex items-center gap-1.5 px-2.5 py-1.5 text-xs font-semibold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 dark:text-emerald-400 dark:bg-emerald-500/10 dark:hover:bg-emerald-500/20 border border-emerald-200 dark:border-emerald-500/30 rounded-md transition-colors">
                                        <i class="fa-solid fa-check"></i> Aktifkan
                                    </button>
                                </form>
                                @endif
                                @if($user->status !== 'tidak_aktif')
                                <form action="{{ route('user.updateStatus', $user->id) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="tidak_aktif">
                                    <button type="submit" title="Nonaktifkan User" 
                                            class="flex items-center gap-1.5 px-2.5 py-1.5 text-xs font-semibold text-amber-700 bg-amber-50 hover:bg-amber-100 dark:text-amber-400 dark:bg-amber-500/10 dark:hover:bg-amber-500/20 border border-amber-200 dark:border-amber-500/30 rounded-md transition-colors">
                                        <i class="fa-solid fa-ban"></i> Nonaktif
                                    </button>
                                </form>
                                @endif
                                @endcan

                                @can('user.delete')
                                <form action="{{ route('users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Hapus user ini secara permanen?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" title="Hapus User" 
                                            class="flex items-center gap-1.5 px-2.5 py-1.5 text-xs font-semibold text-rose-700 bg-rose-50 hover:bg-rose-100 dark:text-rose-400 dark:bg-rose-500/10 dark:hover:bg-rose-500/20 border border-rose-200 dark:border-rose-500/30 rounded-md transition-colors">
                                        <i class="fa-solid fa-trash"></i> Hapus
                                    </button>
                                </form>
                                @endcan
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
            <div id="table-info" class="text-xs text-slate-500 dark:text-slate-400 font-medium">
                <!-- Info text will be injected here by JS -->
            </div>
            <div id="table-pagination" class="flex items-center gap-1">
                <!-- Pagination buttons will be injected here by JS -->
            </div>
        </div>
    </div>

    @can('user.create')
    <!-- Modal Tambah User (Cyberpunk Style) -->
    <div x-show="isOpen" x-cloak 
         class="fixed inset-0 z-[100] bg-slate-900/60 dark:bg-black/70 backdrop-blur-sm flex justify-center items-center p-4">
        
        <div @click.away="isOpen = false" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 scale-95"
             class="bg-white dark:bg-[#0b1220] border border-slate-200 dark:border-slate-800 w-full max-w-md rounded-2xl shadow-2xl overflow-hidden relative">
            
            <!-- Aksen atas -->
            <div class="h-1 w-full bg-gradient-to-r from-transparent via-brand-500 to-transparent"></div>

            <div class="p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-bold text-slate-900 dark:text-white">Tambah User</h2>
                    <button @click="isOpen = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors">
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>

                <form action="{{ route('users.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Nama Lengkap</label>
                        <input type="text" name="nama" required
                               class="w-full bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white text-sm rounded-lg focus:ring-2 focus:ring-brand-500/50 focus:border-brand-500 p-2.5 outline-none transition-all placeholder:text-slate-400 dark:placeholder:text-slate-500" 
                               placeholder="Masukkan nama">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Email</label>
                        <input type="email" name="email" required
                               class="w-full bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white text-sm rounded-lg focus:ring-2 focus:ring-brand-500/50 focus:border-brand-500 p-2.5 outline-none transition-all placeholder:text-slate-400 dark:placeholder:text-slate-500" 
                               placeholder="nama@email.com">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Password</label>
                        <input type="password" name="password" required
                               class="w-full bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white text-sm rounded-lg focus:ring-2 focus:ring-brand-500/50 focus:border-brand-500 p-2.5 outline-none transition-all placeholder:text-slate-400 dark:placeholder:text-slate-500" 
                               placeholder="••••••••">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Role</label>
                            <select name="role" required
                                    class="w-full bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white text-sm rounded-lg focus:ring-2 focus:ring-brand-500/50 focus:border-brand-500 p-2.5 outline-none transition-all">
                                @foreach ($roleOptions as $roleName)
                                <option value="{{ $roleName }}">{{ ucfirst($roleName) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Status</label>
                            <select name="status" required
                                    class="w-full bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white text-sm rounded-lg focus:ring-2 focus:ring-brand-500/50 focus:border-brand-500 p-2.5 outline-none transition-all">
                                <option value="aktif">Aktif</option>
                                <option value="tidak_aktif">Tidak Aktif</option>
                                <option value="pending">Pending</option>
                            </select>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 mt-8 pt-4 border-t border-slate-100 dark:border-slate-800">
                        <button type="button" @click="isOpen = false" class="px-4 py-2 text-sm font-semibold text-slate-600 dark:text-slate-300 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 rounded-lg transition-colors">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 text-sm font-semibold text-white bg-brand-600 hover:bg-brand-500 rounded-lg shadow-[0_4px_14px_0_rgba(6,182,212,0.39)] transition-all">
                            Simpan User
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endcan
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
        let table = $('#tabelPengguna').DataTable({
            dom: 't', // Hanya render tabel (t), tanpa kontrol default
            order: [[0, 'asc']],
            pageLength: 10,
            columnDefs: [
                { orderable: false, targets: -1 } // Matikan sort di kolom Aksi
            ],
            language: {
                emptyTable: "Tidak ada data pengguna",
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
                infoText = `Menampilkan <span class="text-slate-900 dark:text-white font-bold">${info.start + 1}</span> sampai <span class="text-slate-900 dark:text-white font-bold">${info.end}</span> dari <span class="text-slate-900 dark:text-white font-bold">${info.total}</span> pengguna`;
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