{{-- resources/views/pengelola.blade.php --}}
@extends('layouts.app')

@section('title', 'Pengelola Tenant - Inventaris Barang')

@section('content')
@php
$bisaAksi = auth()->user()->canany(['tenant.update', 'tenant.delete']);
$jumlahKolom = $bisaAksi ? 9 : 8;
@endphp

<div x-data="{ isOpen: false }" class="max-w-7xl mx-auto py-6 space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-slate-900 dark:text-white flex items-center gap-3">
                <span class="p-2 bg-brand-50 dark:bg-brand-500/10 rounded-lg text-brand-600 dark:text-brand-400 border border-brand-100 dark:border-brand-500/20">
                    <i class="fa-solid fa-building"></i>
                </span>
                Pendaftaran Tenant
            </h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-2">Kelola informasi pendaftaran perusahaan (tenant) Anda.</p>
        </div>

        @can('tenant.create')
        @if ($tenants->isEmpty())
        <button class="inline-flex items-center justify-center gap-2 bg-brand-600 text-white px-5 py-2.5 rounded-lg shadow-[0_4px_14px_0_rgba(6,182,212,0.39)] hover:bg-brand-500 hover:shadow-[0_6px_20px_rgba(6,182,212,0.23)] transition-all duration-300 font-semibold text-sm"
            onclick="openModal('addTenantModal')">
            <i class="fas fa-plus"></i> Tambah Tenant
        </button>
        @endif
        @endcan
    </div>

    <!-- Tabel Tenant Card -->
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
                        <th class="py-3 px-5 font-bold">Email</th>
                        <th class="py-3 px-5 font-bold text-center">Telepon</th>
                        <th class="py-3 px-5 font-bold">Alamat</th>
                        <th class="py-3 px-5 font-bold text-center">Status</th>
                        @if ($bisaAksi)
                        <th class="py-3 px-5 font-bold text-center w-48">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/50">
                    @forelse ($tenants as $index => $tenant)
                    <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/30 transition-colors align-middle">
                        <td class="py-3 px-5 text-center">{{ $index + 1 }}</td>

                        <td class="py-3 px-5 font-medium text-slate-900 dark:text-white">
                            <div class="flex items-center gap-2">
                                @php
                                // Ambil user yang email-nya cocok dengan tenant (pengelolanya),
                                // fallback ke user pertama kalau tidak ketemu.
                                $userPengelola = $tenant->users->firstWhere('email', $tenant->email)
                                ?? $tenant->users->first();
                                $avatarTenant = $userPengelola?->avatar_url ?: asset('images/1.jpg');
                                @endphp
                                <img src="{{ $avatarTenant }}"
                                    onerror="this.src='{{ asset('images/1.jpg') }}'"
                                    alt="{{ $tenant->nama }}"
                                    class="h-7 w-7 rounded-full object-cover border border-slate-200 dark:border-slate-700 shrink-0">
                                <span>{{ $tenant->nama }}</span>
                            </div>
                        </td>

                        <td class="py-3 px-5 text-brand-600 dark:text-brand-400 font-medium">
                            <div class="flex items-center gap-2">
                                <i class="fa-solid fa-building text-blue-500 text-xs"></i>
                                {{ $tenant->nama_perusahaan }}
                            </div>
                        </td>

                        <td class="py-3 px-5 text-center">
                            <span class="inline-block font-mono text-xs font-semibold text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-slate-800 px-2.5 py-1 rounded border border-slate-200 dark:border-slate-700">
                                {{ $tenant->kode }}
                            </span>
                        </td>

                        <td class="py-3 px-5">
                            {{ $tenant->email }}
                        </td>

                        <td class="py-3 px-5 text-center font-mono">
                            {{ $tenant->telepon }}
                        </td>

                        <td class="py-3 px-5">
                            {{ $tenant->alamat }}
                        </td>

                        <td class="py-3 px-5 text-center">
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
                        </td>

                        @if ($bisaAksi)
                        <td class="py-3 px-5">
                            <div class="flex items-center justify-center gap-2">
                                @can('tenant.update')
                                @if ($tenant->status === 'pending')
                                <button type="button" onclick="editTenant({{ $tenant->id }})"
                                    class="flex items-center gap-1.5 px-2.5 py-1.5 text-xs font-semibold text-amber-700 bg-amber-50 hover:bg-amber-100 dark:text-amber-400 dark:bg-amber-500/10 dark:hover:bg-amber-500/20 border border-amber-200 dark:border-amber-500/30 rounded-md transition-colors">
                                    <i class="fa-solid fa-pen"></i> Edit
                                </button>
                                @endif
                                @endcan

                                @can('tenant.delete')
                                <form action="{{ route('pengelola.destroy', $tenant->id) }}" method="POST"
                                    onsubmit="return confirm('Hapus tenant ini? Semua user akan dilepas dari tenant.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="flex items-center gap-1.5 px-2.5 py-1.5 text-xs font-semibold text-rose-700 bg-rose-50 hover:bg-rose-100 dark:text-rose-400 dark:bg-rose-500/10 dark:hover:bg-rose-500/20 border border-rose-200 dark:border-rose-500/30 rounded-md transition-colors">
                                        <i class="fa-solid fa-trash"></i> Hapus
                                    </button>
                                </form>
                                @endcan
                            </div>
                        </td>
                        @endif
                    </tr>
                    @empty
                    <tr>
                        <td colspan="{{ $jumlahKolom }}" class="px-6 py-12 text-center text-slate-500 dark:text-slate-400">
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

    @can('tenant.create')
    <!-- ================= MODAL TAMBAH TENANT ================= -->
    <div id="addTenantModal"
        class="fixed inset-0 z-[100] bg-slate-900/60 dark:bg-black/70 backdrop-blur-sm flex items-center justify-center opacity-0 pointer-events-none transition-opacity duration-300 p-4">

        <div class="bg-white dark:bg-[#0b1220] border border-slate-200 dark:border-slate-800 w-full max-w-md rounded-2xl shadow-2xl overflow-hidden relative">
            <!-- Aksen atas -->
            <div class="h-1 w-full bg-gradient-to-r from-transparent via-brand-500 to-transparent"></div>

            <div class="p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-bold text-slate-900 dark:text-white">Pendaftaran Tenant</h2>
                    <button type="button" onclick="closeModal('addTenantModal')" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors">
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>

                <form action="{{ route('pengelola.store') }}" method="POST" class="space-y-4">
                    @csrf

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Nama Pengelola</label>
                        <input type="text" name="nama" value="{{ auth()->user()->nama }}" readonly required
                            class="w-full bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-500 dark:text-slate-400 text-sm rounded-lg p-2.5 outline-none cursor-not-allowed">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Nama Perusahaan</label>
                        <input type="text" name="nama_perusahaan" required placeholder="PT Maju Jaya"
                            class="w-full bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white text-sm rounded-lg focus:ring-2 focus:ring-brand-500/50 focus:border-brand-500 p-2.5 outline-none transition-all placeholder:text-slate-400 dark:placeholder:text-slate-500">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Kode Tenant</label>
                        <input type="text" name="kode" required placeholder="TNT-001"
                            class="w-full bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white text-sm rounded-lg focus:ring-2 focus:ring-brand-500/50 focus:border-brand-500 p-2.5 outline-none transition-all placeholder:text-slate-400 dark:placeholder:text-slate-500">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Email</label>
                        <input type="email" name="email" value="{{ auth()->user()->email }}" readonly required
                            class="w-full bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-500 dark:text-slate-400 text-sm rounded-lg p-2.5 outline-none cursor-not-allowed">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Telepon</label>
                        <input type="text" name="telepon" placeholder="08123456789"
                            class="w-full bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white text-sm rounded-lg focus:ring-2 focus:ring-brand-500/50 focus:border-brand-500 p-2.5 outline-none transition-all placeholder:text-slate-400 dark:placeholder:text-slate-500">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Alamat</label>
                        <textarea name="alamat" rows="3" placeholder="Alamat lengkap perusahaan..."
                            class="w-full bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white text-sm rounded-lg focus:ring-2 focus:ring-brand-500/50 focus:border-brand-500 p-2.5 outline-none transition-all placeholder:text-slate-400 dark:placeholder:text-slate-500"></textarea>
                    </div>

                    <div class="flex justify-end gap-3 mt-8 pt-4 border-t border-slate-100 dark:border-slate-800">
                        <button type="button" onclick="closeModal('addTenantModal')"
                            class="px-4 py-2 text-sm font-semibold text-slate-600 dark:text-slate-300 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 rounded-lg transition-colors">
                            Batal
                        </button>
                        <button type="submit"
                            class="px-4 py-2 text-sm font-semibold text-white bg-brand-600 hover:bg-brand-500 rounded-lg shadow-[0_4px_14px_0_rgba(6,182,212,0.39)] transition-all">
                            Simpan Data
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endcan

    @can('tenant.update')
    <!-- ================= MODAL EDIT TENANT ================= -->
    <div id="editTenantModal"
        class="fixed inset-0 z-[100] bg-slate-900/60 dark:bg-black/70 backdrop-blur-sm flex items-center justify-center opacity-0 pointer-events-none transition-opacity duration-300 p-4">

        <div class="bg-white dark:bg-[#0b1220] border border-slate-200 dark:border-slate-800 w-full max-w-md rounded-2xl shadow-2xl overflow-hidden relative">
            <!-- Aksen atas -->
            <div class="h-1 w-full bg-gradient-to-r from-transparent via-amber-500 to-transparent"></div>

            <div class="p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-bold text-slate-900 dark:text-white">Edit Tenant</h2>
                    <button type="button" onclick="closeModal('editTenantModal')" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors">
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>

                <!-- ID form dan input tetap sama -->
                <form id="editTenantForm" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="editTenantId" name="id">

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Nama Pengelola</label>
                        <input type="text" id="editNama" name="nama" readonly required
                            class="w-full bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-500 dark:text-slate-400 text-sm rounded-lg p-2.5 outline-none cursor-not-allowed">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Nama Perusahaan</label>
                        <input type="text" id="editNamaPerusahaan" name="nama_perusahaan" required
                            class="w-full bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white text-sm rounded-lg focus:ring-2 focus:ring-brand-500/50 focus:border-brand-500 p-2.5 outline-none transition-all placeholder:text-slate-400 dark:placeholder:text-slate-500">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Kode Tenant</label>
                        <input type="text" id="editKode" name="kode" required
                            class="w-full bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white text-sm rounded-lg focus:ring-2 focus:ring-brand-500/50 focus:border-brand-500 p-2.5 outline-none transition-all placeholder:text-slate-400 dark:placeholder:text-slate-500">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Email</label>
                        <input type="email" id="editEmail" name="email" readonly required
                            class="w-full bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-500 dark:text-slate-400 text-sm rounded-lg p-2.5 outline-none cursor-not-allowed">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Telepon</label>
                        <input type="text" id="editTelepon" name="telepon"
                            class="w-full bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white text-sm rounded-lg focus:ring-2 focus:ring-brand-500/50 focus:border-brand-500 p-2.5 outline-none transition-all placeholder:text-slate-400 dark:placeholder:text-slate-500">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Alamat</label>
                        <textarea id="editAlamat" name="alamat" rows="3"
                            class="w-full bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white text-sm rounded-lg focus:ring-2 focus:ring-brand-500/50 focus:border-brand-500 p-2.5 outline-none transition-all placeholder:text-slate-400 dark:placeholder:text-slate-500"></textarea>
                    </div>

                    <div class="flex justify-end gap-3 mt-8 pt-4 border-t border-slate-100 dark:border-slate-800">
                        <button type="button" onclick="closeModal('editTenantModal')"
                            class="px-4 py-2 text-sm font-semibold text-slate-600 dark:text-slate-300 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 rounded-lg transition-colors">
                            Batal
                        </button>
                        <button type="submit"
                            class="px-4 py-2 text-sm font-semibold text-white bg-amber-500 hover:bg-amber-400 rounded-lg shadow-[0_4px_14px_0_rgba(245,158,11,0.39)] transition-all">
                            Update Data
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endcan

    <!-- ================= JAVASCRIPT BAWAAN (TETAP UTUH) ================= -->
    <script>
        function openModal(id) {
            const modal = document.getElementById(id);
            if (!modal) return;
            modal.classList.remove('opacity-0', 'pointer-events-none');
            modal.classList.add('opacity-100');
        }

        function closeModal(id) {
            const modal = document.getElementById(id);
            if (!modal) return;
            modal.classList.remove('opacity-100');
            modal.classList.add('opacity-0', 'pointer-events-none');
        }

        @can('tenant.update')

        function editTenant(id) {
            fetch(`/pengelola/${id}/edit`, {
                    headers: {
                        'Accept': 'application/json'
                    }
                })
                .then(response => {
                    if (!response.ok) throw new Error('Gagal memuat data tenant');
                    return response.json();
                })
                .then(data => {
                    document.getElementById('editTenantId').value = data.id;
                    document.getElementById('editNama').value = data.nama;
                    document.getElementById('editNamaPerusahaan').value = data.nama_perusahaan;
                    document.getElementById('editKode').value = data.kode;
                    document.getElementById('editEmail').value = data.email;
                    document.getElementById('editTelepon').value = data.telepon ?? '';
                    document.getElementById('editAlamat').value = data.alamat ?? '';
                    document.getElementById('editTenantForm').action = `/pengelola/${id}/update`;
                    openModal('editTenantModal');
                })
                .catch(err => alert(err.message));
        }
        @endcan
    </script>
</div>
@endsection