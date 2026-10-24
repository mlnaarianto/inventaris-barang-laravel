@extends('layouts.app')

@section('content')
@php
    // Admin/akun sistem: $tenant null -> tampilan semua tenant, hanya lihat
    $semuaTenant = $tenant === null;
@endphp

<div class="max-w-7xl mx-auto py-6 space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-slate-900 dark:text-white flex items-center gap-3">
                <span class="p-2 bg-brand-50 dark:bg-brand-500/10 rounded-lg text-brand-600 dark:text-brand-400 border border-brand-100 dark:border-brand-500/20">
                    <i class="fa-solid fa-users-gear"></i>
                </span>
                User Tenant: <span class="text-brand-600 dark:text-brand-400">{{ $semuaTenant ? 'Semua Tenant' : $tenant->nama }}</span>
            </h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-2">
                {{ $semuaTenant ? 'Daftar seluruh user yang terdaftar di semua tenant.' : 'Kelola anggota yang memiliki akses ke tenant ini.' }}
            </p>
        </div>

        @if (!$semuaTenant)
            @can('member.create')
            <button onclick="openModalTambah()"
                class="inline-flex items-center justify-center gap-2 bg-brand-600 text-white px-5 py-2.5 rounded-lg shadow-[0_4px_14px_0_rgba(6,182,212,0.39)] hover:bg-brand-500 hover:shadow-[0_6px_20px_rgba(6,182,212,0.23)] transition-all duration-300 font-semibold text-sm">
                <i class="fa-solid fa-plus"></i> Tambah User
            </button>
            @endcan
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
                <h2 class="text-lg font-bold text-slate-900 dark:text-white uppercase tracking-wider">Data Anggota</h2>
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
            <table id="tabelUserTenant" class="w-full text-sm text-left text-slate-600 dark:text-slate-300 whitespace-nowrap">
                <thead class="text-xs uppercase bg-slate-50 dark:bg-slate-800/50 text-slate-700 dark:text-slate-300 border-b border-slate-200 dark:border-slate-700 font-semibold tracking-wider">
                    <tr>
                        <th class="py-3 px-5 font-bold w-16">No.</th>
                        <th class="py-3 px-5 font-bold">Nama</th>
                        <th class="py-3 px-5 font-bold">Email</th>
                        @if ($semuaTenant)
                        <th class="py-3 px-5 font-bold">Tenant</th>
                        @endif
                        @if (!$semuaTenant)
                        @can('member.delete')
                        <th class="py-3 px-5 font-bold text-center">Aksi</th>
                        @endcan
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/50">
                    @foreach ($members as $index => $member)
                    <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/30 transition-colors align-middle">
                        <td class="py-3 px-5">{{ $index + 1 }}</td>
                        <td class="py-3 px-5 font-medium text-slate-900 dark:text-white">{{ $member->nama }}</td>
                        <td class="py-3 px-5">{{ $member->email }}</td>
                        @if ($semuaTenant)
                        <td class="py-3 px-5">
                            <span class="inline-flex items-center gap-1.5 py-1 px-2.5 rounded-md text-xs font-semibold bg-brand-50 text-brand-700 dark:bg-brand-500/10 dark:text-brand-400 border border-brand-100 dark:border-brand-500/20">
                                {{ $member->tenant->nama ?? '-' }}
                            </span>
                        </td>
                        @endif
                        @if (!$semuaTenant)
                        @can('member.delete')
                        <td class="py-3 px-5">
                            <div class="flex items-center justify-center">
                                <form action="{{ route('tenantmembers.destroy', $member->id) }}" method="POST"
                                    onsubmit="return confirm('Keluarkan user ini dari tenant?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="flex items-center gap-1.5 px-2.5 py-1.5 text-xs font-semibold text-rose-700 bg-rose-50 hover:bg-rose-100 dark:text-rose-400 dark:bg-rose-500/10 dark:hover:bg-rose-500/20 border border-rose-200 dark:border-rose-500/30 rounded-md transition-colors">
                                        <i class="fa-solid fa-user-minus"></i> Keluarkan
                                    </button>
                                </form>
                            </div>
                        </td>
                        @endcan
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

    @if (!$semuaTenant)
    @can('member.create')
    <!-- Modal Tambah User -->
    <div id="modal-tambah"
        class="fixed inset-0 z-[100] bg-slate-900/60 dark:bg-black/70 backdrop-blur-sm flex items-center justify-center opacity-0 pointer-events-none transition-opacity duration-300">
        
        <div class="bg-white dark:bg-[#0b1220] border border-slate-200 dark:border-slate-800 w-full max-w-md rounded-2xl shadow-2xl overflow-hidden relative transform scale-95 transition-transform duration-300" id="modal-content">
            
            <!-- Aksen atas -->
            <div class="h-1 w-full bg-gradient-to-r from-transparent via-brand-500 to-transparent"></div>

            <div class="p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-slate-900 dark:text-white">Tambah User ke Tenant</h3>
                    <button type="button" onclick="closeModalTambah()" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors">
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>

                <form method="POST" action="{{ route('tenantmembers.store') }}" class="space-y-4">
                    @csrf
                    
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Pilih User</label>
                        <select name="user_id" required class="select-user w-full">
                            <option value="">Cari dan pilih user...</option>
                            @foreach ($candidates as $candidate)
                            <option value="{{ $candidate->id }}">{{ $candidate->nama }} ({{ $candidate->email }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex justify-end gap-3 mt-8 pt-4 border-t border-slate-100 dark:border-slate-800">
                        <button type="button" onclick="closeModalTambah()"
                            class="px-4 py-2 text-sm font-semibold text-slate-600 dark:text-slate-300 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 rounded-lg transition-colors">
                            Batal
                        </button>
                        <button type="submit"
                            class="px-4 py-2 text-sm font-semibold text-white bg-brand-600 hover:bg-brand-500 rounded-lg shadow-[0_4px_14px_0_rgba(6,182,212,0.39)] transition-all">
                            <i class="fa-solid fa-check mr-1"></i> Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endcan
    @endif
</div>
@endsection

@push('styles')
<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css" />
<!-- Select2 CSS (Hanya dimuat jika diperlukan) -->
@if (!$semuaTenant)
@can('member.create')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endif
@endcan
<style>
    /* Sembunyikan elemen bawaan DataTables */
    .dataTables_wrapper .dataTables_length, 
    .dataTables_wrapper .dataTables_filter, 
    .dataTables_wrapper .dataTables_info, 
    .dataTables_wrapper .dataTables_paginate {
        display: none !important;
    }
    table.dataTable.no-footer {
        border-bottom: none;
    }

    /* --- CUSTOM SELECT2 (Cyberpunk Style) --- */
    .select2-container--default .select2-selection--single {
        background-color: #f8fafc; /* slate-50 */
        border: 1px solid #e2e8f0; /* slate-200 */
        height: 42px;
        border-radius: 0.5rem;
        display: flex;
        align-items: center;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        color: #0f172a;
        padding-left: 12px;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 40px;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow b {
        border-color: #64748b transparent transparent transparent;
    }

    /* Dark Mode Select2 */
    .dark .select2-container--default .select2-selection--single {
        background-color: #1e293b; /* slate-800 */
        border-color: #334155; /* slate-700 */
    }
    .dark .select2-container--default .select2-selection--single .select2-selection__rendered {
        color: #f8fafc;
    }
    .dark .select2-container--default .select2-selection--single .select2-selection__arrow b {
        border-color: #94a3b8 transparent transparent transparent;
    }
    
    /* Select2 Dropdown */
    .select2-dropdown {
        background-color: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 0.5rem;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    }
    .dark .select2-dropdown {
        background-color: #0b1220;
        border-color: #1e293b;
    }
    .select2-container--default .select2-results__option--selected {
        background-color: #cffafe;
        color: #0891b2;
    }
    .dark .select2-container--default .select2-results__option--selected {
        background-color: rgba(6, 182, 212, 0.2);
        color: #22d3ee;
    }
    .select2-container--default .select2-results__option--highlighted.select2-results__option--selectable {
        background-color: #06b6d4;
        color: white;
    }
    .dark .select2-search--dropdown .select2-search__field {
        background-color: #1e293b;
        border-color: #334155;
        color: white;
    }
</style>
@endpush

@push('scripts')
<!-- jQuery & DataTables -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

<!-- Select2 JS -->
@if (!$semuaTenant)
@can('member.create')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
@endif
@endcan

<script>
    // --- Modal Functions ---
    @if (!$semuaTenant)
    @can('member.create')
    function openModalTambah() {
        const modal = document.getElementById('modal-tambah');
        const content = document.getElementById('modal-content');
        
        modal.classList.remove('opacity-0', 'pointer-events-none');
        content.classList.remove('scale-95');
        content.classList.add('scale-100');

        // Inisialisasi Select2
        $('#modal-tambah .select-user').select2({
            dropdownParent: $('#modal-tambah'),
            width: '100%',
            placeholder: "Cari dan pilih user...",
            allowClear: true
        });
    }

    function closeModalTambah() {
        const modal = document.getElementById('modal-tambah');
        const content = document.getElementById('modal-content');
        
        modal.classList.add('opacity-0', 'pointer-events-none');
        content.classList.remove('scale-100');
        content.classList.add('scale-95');
    }
    @endcan
    @endif

    // --- DataTables Custom UI ---
    $(document).ready(function() {
        let table = $('#tabelUserTenant').DataTable({
            dom: 't', // Hanya render tabel
            order: [[0, 'asc']],
            pageLength: 10,
            columnDefs: [
                // Matikan sorting di kolom aksi (jika ada)
                @if (!$semuaTenant)
                @can('member.delete')
                { orderable: false, targets: -1 }
                @endcan
                @endif
            ],
            language: {
                emptyTable: "Tidak ada data anggota",
                zeroRecords: "Pencarian tidak ditemukan"
            }
        });

        // Bind Custom Search
        $('#customSearch').on('keyup', function() {
            table.search(this.value).draw();
        });

        // Bind Custom Length
        $('#customLength').on('change', function() {
            table.page.len(this.value).draw();
        });

        // Render Custom Info & Pagination
        table.on('draw', function() {
            let info = table.page.info();
            
            // A. Render Teks Info
            let infoText = '';
            if (info.total === 0) {
                infoText = 'Tidak ada data untuk ditampilkan';
            } else {
                infoText = `Menampilkan ${info.start + 1} - ${info.end} dari ${info.total} anggota`;
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