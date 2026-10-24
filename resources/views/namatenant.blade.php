@extends('layouts.app')

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
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-2">Informasi detail mengenai tenant yang terdaftar di sistem.</p>
        </div>
    </div>

    <!-- Tenant Table Section -->
    <div class="bg-white dark:bg-[#0b1220] border border-slate-200 dark:border-slate-800 rounded-xl shadow-sm overflow-hidden flex flex-col">
        
        <!-- Table Header Info -->
        <div class="p-5 border-b border-slate-200 dark:border-slate-800 flex items-center gap-3">
            <div class="p-2 bg-slate-100 dark:bg-slate-800 rounded-lg text-slate-600 dark:text-slate-300">
                <i class="fa-solid fa-list-ul"></i>
            </div>
            <h2 class="text-lg font-bold text-slate-900 dark:text-white uppercase tracking-wider">Data Tenant</h2>
        </div>

        <!-- Table Wrapper -->
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-slate-600 dark:text-slate-300 whitespace-nowrap">
                <thead class="text-xs uppercase bg-slate-50 dark:bg-slate-800/50 text-slate-700 dark:text-slate-300 border-b border-slate-200 dark:border-slate-700 font-semibold tracking-wider">
                    <tr>
                        <th class="py-3 px-5 font-bold w-16 text-center">No</th>
                        <th class="py-3 px-5 font-bold">Nama (Pengelola)</th>
                        <th class="py-3 px-5 font-bold">Perusahaan</th>
                        <th class="py-3 px-5 font-bold text-center">Kode</th>
                        <th class="py-3 px-5 font-bold">Email (Pengelola)</th>
                        <th class="py-3 px-5 font-bold text-center">Telepon</th>
                        <th class="py-3 px-5 font-bold">Alamat</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/50">
                    @forelse ($tenants as $index => $tenant)
                        <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/30 transition-colors align-middle">
                            <td class="py-3 px-5 text-center">{{ $index + 1 }}</td>
                            
                            <td class="py-3 px-5 font-medium text-slate-900 dark:text-white">
                                <div class="flex items-center gap-2">
                                    <i class="fa-solid fa-user text-brand-500 text-xs"></i> 
                                    {{ $tenant->nama }}
                                </div>
                            </td>
                            
                            <td class="py-3 px-5 text-brand-600 dark:text-brand-400 font-medium">
                                <div class="flex items-center gap-2">
                                    <i class="fa-solid fa-building text-blue-500 text-xs"></i> 
                                    {{ $tenant->nama_perusahaan }}
                                </div>
                            </td>
                            
                            <td class="py-3 px-5 text-center">
                                <!-- Kode Tenant dibuat seperti barcode -->
                                <span class="inline-block font-mono text-xs font-semibold text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-slate-800 px-2.5 py-1 rounded border border-slate-200 dark:border-slate-700">
                                    {{ $tenant->kode }}
                                </span>
                            </td>
                            
                            <td class="py-3 px-5">
                                <div class="flex items-center gap-2">
                                    <i class="fa-solid fa-envelope text-amber-500 text-xs"></i> 
                                    {{ $tenant->email }}
                                </div>
                            </td>
                            
                            <td class="py-3 px-5 text-center">
                                <div class="flex items-center justify-center gap-2 font-mono">
                                    <i class="fa-solid fa-phone text-emerald-500 text-xs"></i> 
                                    {{ $tenant->telepon }}
                                </div>
                            </td>
                            
                            <td class="py-3 px-5">
                                <div class="flex items-center gap-2">
                                    <i class="fa-solid fa-map-marker-alt text-rose-500 text-xs"></i> 
                                    {{ $tenant->alamat }}
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-slate-500 dark:text-slate-400">
                                <div class="flex flex-col items-center justify-center gap-3">
                                    <div class="w-16 h-16 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center mb-2">
                                        <i class="fa-solid fa-folder-open text-2xl text-slate-400 dark:text-slate-500"></i>
                                    </div>
                                    <p class="text-sm font-medium">Belum ada tenant yang ditambahkan.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection