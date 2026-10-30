@extends('layouts.app')

@section('title', 'Anggota Tenant - Inventaris Barang')

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
                        <td class="py-3 px-5 font-medium text-slate-900 dark:text-white">
                            <div class="flex items-center gap-3">
                                @php
                                $avatarMember = $member->avatar_url ?: asset('images/1.jpg');
                                @endphp
                                <img src="{{ $avatarMember }}"
                                    onerror="this.src='{{ asset('images/1.jpg') }}'"
                                    alt="{{ $member->nama }}"
                                    style="width: 32px; height: 32px; min-width: 32px; object-fit: cover;"
                                    class="rounded-full border border-slate-200 dark:border-slate-700 shrink-0">
                                <span>{{ $member->nama }}</span>
                            </div>
                        </td>
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
        class="fixed inset-0 z-[100] bg-slate-900/60 dark:bg-black/70 backdrop-blur-sm hidden items-center justify-center p-4">

        <div class="bg-white dark:bg-[#0b1220] border border-slate-200 dark:border-slate-800 w-full max-w-md rounded-2xl shadow-2xl relative">

            <!-- Aksen atas -->
            <div class="h-1 w-full bg-gradient-to-r from-transparent via-brand-500 to-transparent rounded-t-2xl"></div>

            <div class="p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-slate-900 dark:text-white">Tambah User ke Tenant</h3>
                    <button type="button" onclick="closeModalTambah()" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors">
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>

                <form method="POST" action="{{ route('tenantmembers.store') }}" class="space-y-4">
                    @csrf

                    <!-- CUSTOM SEARCHABLE DROPDOWN MENGGUNAKAN ALPINE.JS -->
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Pilih User</label>

                        <!-- PERBAIKAN: @click.away diletakkan di parent div, bukan di tombol -->
                        <div x-data="userDropdown()" class="relative" @click.away="open = false">
                            <!-- Input hidden untuk dikirim ke backend -->
                            <input type="hidden" name="user_id" x-model="selectedId" required>

                            <!-- Tombol Trigger -->
                            <!-- PERBAIKAN: Menambahkan $nextTick untuk auto-focus ke input search -->
                            <button type="button"
                                @click="open = !open; if(open) $nextTick(() => $refs.searchInput.focus())"
                                class="w-full flex justify-between items-center bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white text-sm rounded-lg focus:ring-2 focus:ring-brand-500/50 focus:border-brand-500 p-2.5 outline-none transition-all text-left">
                                <div class="flex items-center gap-2 min-w-0">
                                    <template x-if="selectedUser">
                                        <img :src="selectedUser.avatar_url || '{{ asset('images/1.jpg') }}'"
                                            onerror="this.src='{{ asset('images/1.jpg') }}'"
                                            :alt="selectedUser.nama"
                                            style="width: 24px; height: 24px; min-width: 24px; object-fit: cover;"
                                            class="rounded-full border border-slate-200 dark:border-slate-700 shrink-0">
                                    </template>
                                    <span x-text="selectedUser ? selectedUser.nama + ' (' + selectedUser.email + ')' : '-- Pilih User --'"
                                        :class="!selectedUser ? 'text-slate-400 dark:text-slate-500' : ''"
                                        class="truncate"></span>
                                </div>
                                <i class="fa-solid fa-chevron-down text-xs text-slate-400 transition-transform duration-200" :class="open ? 'rotate-180' : ''"></i>
                            </button>

                            <!-- Dropdown Menu -->
                            <div x-show="open" x-transition.opacity
                                class="absolute z-50 w-full mt-1 bg-white dark:bg-[#0b1220] border border-slate-200 dark:border-slate-700 rounded-lg shadow-xl max-h-60 overflow-y-auto"
                                style="display: none;">

                                <!-- Input Pencarian -->
                                <div class="sticky top-0 bg-white dark:bg-[#0b1220] p-2 border-b border-slate-100 dark:border-slate-800 z-10">
                                    <!-- PERBAIKAN: Menambahkan x-ref="searchInput" -->
                                    <input type="text" x-model="search" x-ref="searchInput" placeholder="Ketik untuk mencari..."
                                        class="w-full bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white text-sm rounded-md focus:ring-1 focus:ring-brand-500/50 outline-none p-2 placeholder:text-slate-400">
                                </div>

                                <!-- List User -->
                                <ul class="py-1">
                                    <!-- Template untuk setiap user -->
                                    <template x-for="user in filteredUsers" :key="user.id">
                                        <li @click="selectUser(user)"
                                            class="px-4 py-2 text-sm cursor-pointer hover:bg-brand-50 dark:hover:bg-brand-500/10 hover:text-brand-700 dark:hover:text-brand-400 transition-colors flex items-center gap-2.5"
                                            :class="selectedId == user.id ? 'bg-brand-50 dark:bg-brand-500/10 text-brand-700 dark:text-brand-400 font-medium' : 'text-slate-700 dark:text-slate-300'">
                                            <img :src="user.avatar_url || '{{ asset('images/1.jpg') }}'"
                                                onerror="this.src='{{ asset('images/1.jpg') }}'"
                                                :alt="user.nama"
                                                style="width: 28px; height: 28px; min-width: 28px; object-fit: cover;"
                                                class="rounded-full border border-slate-200 dark:border-slate-700 shrink-0">
                                            <div class="min-w-0 flex-1">
                                                <div class="truncate" x-text="user.nama"></div>
                                                <div class="text-xs text-slate-400 truncate" x-text="user.email"></div>
                                            </div>
                                        </li>
                                    </template>

                                    <!-- Jika tidak ada hasil -->
                                    <li x-show="filteredUsers.length === 0" class="px-4 py-3 text-sm text-center text-slate-500">
                                        User tidak ditemukan.
                                    </li>
                                </ul>
                            </div>
                        </div>
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
</style>
@endpush

@push('scripts')
<!-- jQuery & DataTables -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

<script>
    // --- Modal Functions (Vanilla JS) ---
    @if(!$semuaTenant)
    @can('member.create')

    function openModalTambah() {
        const modal = document.getElementById('modal-tambah');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeModalTambah() {
        const modal = document.getElementById('modal-tambah');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
    @endcan
    @endif

    // --- Alpine.js Component untuk Searchable Dropdown ---
    document.addEventListener('alpine:init', () => {
        Alpine.data('userDropdown', () => ({
            open: false,
            search: '',
            selectedId: '',
            selectedUser: null,
            // Ambil data kandidat user dari Laravel
            users: @json($candidates),

            // Fungsi filter pencarian
            get filteredUsers() {
                if (this.search === '') return this.users;
                return this.users.filter(user =>
                    user.nama.toLowerCase().includes(this.search.toLowerCase()) ||
                    user.email.toLowerCase().includes(this.search.toLowerCase())
                );
            },

            // Fungsi saat user dipilih
            selectUser(user) {
                this.selectedId = user.id;
                this.selectedUser = user;
                this.open = false;
                this.search = ''; // Reset pencarian
            }
        }))
    });

    // --- DataTables Custom UI ---
    $(document).ready(function() {
        // Inisialisasi DataTables
        let table = $('#tabelUserTenant').DataTable({
            dom: 't', // Hanya render tabel
            order: [
                [0, 'asc']
            ],
            pageLength: 10,
            columnDefs: [
                @if(!$semuaTenant)
                @can('member.delete') {
                    orderable: false,
                    targets: -1
                }
                @endcan
                @endif
            ],
            language: {
                emptyTable: "Tidak ada data anggota",
                zeroRecords: "Pencarian tidak ditemukan"
            }
        });

        // Bind Custom Search & Length
        $('#customSearch').on('keyup', function() {
            table.search(this.value).draw();
        });

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