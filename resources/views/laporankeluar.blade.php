@extends('layouts.app')

@section('content')
    @php
        $tampilTenant = $tenants->count() > 1;
        $namaTenant = $tenants->keyBy('id');
        $jumlahKolom = $tampilTenant ? 7 : 6;
    @endphp

    <div class="max-w-7xl mx-auto py-6 space-y-6">
        
        <!-- Header Section -->
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-slate-900 dark:text-white flex items-center gap-3">
                    <span class="p-2 bg-brand-50 dark:bg-brand-500/10 rounded-lg text-brand-600 dark:text-brand-400 border border-brand-100 dark:border-brand-500/20">
                        <i class="fa-solid fa-file-export"></i>
                    </span>
                    Laporan Barang Keluar
                </h1>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-2">Pantau dan cetak riwayat pengeluaran barang berdasarkan periode tertentu.</p>
            </div>
        </div>

        <!-- Filter Card -->
        <div class="bg-white dark:bg-[#0b1220] border border-slate-200 dark:border-slate-800 rounded-xl shadow-sm p-5 sm:p-6">
            <form id="form-filter" method="GET" action="{{ route('laporanbarangkeluar') }}"
                class="grid md:grid-cols-4 sm:grid-cols-2 gap-4 items-start">
                
                <!-- Tanggal Awal -->
                <div>
                    <label for="tanggal_awal" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Tanggal Awal</label>
                    <input type="date" name="tanggal_awal" id="tanggal_awal" value="{{ request('tanggal_awal') }}"
                        class="w-full bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white text-sm rounded-lg focus:ring-2 focus:ring-brand-500/50 focus:border-brand-500 p-2.5 outline-none transition-all [color-scheme:light] dark:[color-scheme:dark]">
                </div>

                <!-- Tanggal Akhir -->
                <div>
                    <label for="tanggal_akhir" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Tanggal Akhir</label>
                    <input type="date" name="tanggal_akhir" id="tanggal_akhir" value="{{ request('tanggal_akhir') }}"
                        class="w-full bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white text-sm rounded-lg focus:ring-2 focus:ring-brand-500/50 focus:border-brand-500 p-2.5 outline-none transition-all [color-scheme:light] dark:[color-scheme:dark]">
                    <p id="tanggal-error" class="hidden text-[11px] font-medium text-rose-500 mt-1.5 flex items-center gap-1">
                        <i class="fa-solid fa-circle-exclamation"></i> Tanggal akhir tidak boleh lebih awal.
                    </p>
                </div>

                <!-- Search Barang -->
                <div>
                    <label for="search" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Cari Barang</label>
                    <div class="relative group">
                        <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-sm text-slate-400 group-focus-within:text-brand-500 transition-colors"></i>
                        <input type="text" name="search" id="search" placeholder="ID / Nama / Satuan..." value="{{ request('search') }}"
                            class="w-full pl-9 pr-3 py-2.5 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-lg text-sm focus:ring-2 focus:ring-brand-500/50 focus:border-brand-500 outline-none text-slate-900 dark:text-white transition-all placeholder:text-slate-400">
                    </div>
                </div>

                <!-- Buttons -->
                <div class="grid @can('laporan.export') grid-cols-2 @else grid-cols-1 @endcan gap-2 w-full mt-1 md:mt-7">
                    <button type="submit"
                        class="inline-flex items-center justify-center gap-2 bg-brand-600 text-white font-semibold py-2.5 px-4 rounded-lg shadow-[0_4px_14px_0_rgba(6,182,212,0.39)] hover:bg-brand-500 transition-all text-sm">
                        <i class="fa-solid fa-filter"></i> Tampilkan
                    </button>

                    @can('laporan.export')
                        <a href="{{ route('laporanbarangkeluar.pdf', [
                            'tanggal_awal' => request('tanggal_awal'),
                            'tanggal_akhir' => request('tanggal_akhir'),
                            'search' => request('search'),
                        ]) }}"
                            target="_blank"
                            class="inline-flex items-center justify-center gap-2 bg-emerald-600 text-white font-semibold py-2.5 px-4 rounded-lg shadow-[0_4px_14px_0_rgba(16,185,129,0.39)] hover:bg-emerald-500 transition-all text-sm">
                            <i class="fa-solid fa-print"></i> Cetak PDF
                        </a>
                    @endcan
                </div>
            </form>
        </div>

        <!-- Table Card -->
        <div class="bg-white dark:bg-[#0b1220] border border-slate-200 dark:border-slate-800 rounded-xl shadow-sm overflow-hidden flex flex-col">
            
            <!-- Table Header Info -->
            <div class="p-5 border-b border-slate-200 dark:border-slate-800 flex items-center gap-3">
                <div class="p-2 bg-slate-100 dark:bg-slate-800 rounded-lg text-slate-600 dark:text-slate-300">
                    <i class="fa-solid fa-list-ul"></i>
                </div>
                <h2 class="text-lg font-bold text-slate-900 dark:text-white uppercase tracking-wider">Data Barang Keluar</h2>
            </div>

            <!-- Table Wrapper -->
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-slate-600 dark:text-slate-300 whitespace-nowrap">
                    <thead class="text-xs uppercase bg-slate-50 dark:bg-slate-800/50 text-slate-700 dark:text-slate-300 border-b border-slate-200 dark:border-slate-700 font-semibold tracking-wider">
                        <tr>
                            <th class="px-5 py-4 font-bold w-16 text-center">No</th>
                            @if ($tampilTenant)
                                <th class="px-5 py-4 font-bold">Tenant</th>
                            @endif
                            <th class="px-5 py-4 font-bold">No Barang Keluar</th>
                            <th class="px-5 py-4 font-bold">Tanggal</th>
                            <th class="px-5 py-4 font-bold">Nama Barang</th>
                            <th class="px-5 py-4 font-bold">Satuan</th>
                            <th class="px-5 py-4 font-bold text-center">Jumlah Keluar</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800/50">
                        @forelse ($barangKeluars as $index => $item)
                            <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/30 transition-colors align-middle">
                                <td class="px-5 py-3 text-center">{{ $index + 1 }}</td>
                                
                                @if ($tampilTenant)
                                    <td class="px-5 py-3">
                                        <span class="inline-flex items-center gap-1.5 py-1 px-2.5 rounded-md text-xs font-semibold bg-brand-50 text-brand-700 dark:bg-brand-500/10 dark:text-brand-400 border border-brand-100 dark:border-brand-500/20">
                                            {{ $namaTenant[$item->id_tenant]->nama_perusahaan ?? '-' }}
                                        </span>
                                    </td>
                                @endif
                                
                                <td class="px-5 py-3">
                                    <span class="font-mono text-xs font-semibold text-brand-600 dark:text-brand-400 bg-brand-50/50 dark:bg-brand-500/5 px-2 py-1 rounded border border-brand-100/50 dark:border-brand-500/10">
                                        {{ $item->no_barangkeluar }}
                                    </span>
                                </td>
                                <td class="px-5 py-3">
                                    <div class="flex items-center gap-2">
                                        <i class="fa-regular fa-calendar text-slate-400"></i>
                                        {{ $item->tanggal }}
                                    </div>
                                </td>
                                <td class="px-5 py-3 font-medium text-slate-900 dark:text-white">{{ $item->dataBarang->nama_barang ?? '-' }}</td>
                                <td class="px-5 py-3">{{ $item->dataBarang->satuan->nama ?? '-' }}</td>
                                <td class="px-5 py-3 text-center">
                                    <!-- Menonjolkan jumlah keluar dengan warna Rose/Merah (karena barang berkurang) -->
                                    <span class="inline-flex items-center justify-center min-w-[3rem] px-3 py-1 font-mono text-sm font-bold rounded-lg text-rose-700 bg-rose-50 dark:text-rose-400 dark:bg-rose-500/10 border border-rose-200 dark:border-rose-500/20">
                                        -{{ $item->jumlah_keluar }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ $jumlahKolom }}" class="px-6 py-12 text-center text-slate-500 dark:text-slate-400">
                                    <div class="flex flex-col items-center justify-center gap-3">
                                        <div class="w-16 h-16 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center mb-2">
                                            <i class="fa-solid fa-box-open text-2xl text-slate-400 dark:text-slate-500"></i>
                                        </div>
                                        <p class="text-sm font-medium">Tidak ada data barang keluar.</p>
                                        <p class="text-xs text-slate-400">Silakan sesuaikan filter tanggal atau kata kunci pencarian Anda.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
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