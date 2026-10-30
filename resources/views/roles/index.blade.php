@extends('layouts.app')

@section('title', 'Manajemen Role - Inventaris Barang')

@section('content')
<div class="max-w-7xl mx-auto py-6 space-y-6">

    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-slate-900 dark:text-white flex items-center gap-3">
                <span class="p-2 bg-brand-50 dark:bg-brand-500/10 rounded-lg text-brand-600 dark:text-brand-400 border border-brand-100 dark:border-brand-500/20">
                    <i class="fa-solid fa-user-shield"></i>
                </span>
                Manajemen Role
            </h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-2">Kelola peran pengguna dan hak akses sistem.</p>
        </div>

        @can('role.create')
            <a href="{{ route('roles.create') }}"
                class="inline-flex items-center justify-center gap-2 bg-brand-600 text-white px-5 py-2.5 rounded-lg shadow-[0_4px_14px_0_rgba(6,182,212,0.39)] hover:bg-brand-500 hover:shadow-[0_6px_20px_rgba(6,182,212,0.23)] transition-all duration-300 font-semibold text-sm">
                <i class="fa-solid fa-plus"></i> Tambah Role
            </a>
        @endcan
    </div>

    @if ($errors->any())
        <div class="p-4 rounded-xl bg-rose-50 dark:bg-rose-500/10 border border-rose-200 dark:border-rose-500/20 text-rose-700 dark:text-rose-400 text-sm flex items-start gap-3 shadow-sm">
            <i class="fa-solid fa-circle-exclamation mt-0.5"></i>
            <div>{{ $errors->first() }}</div>
        </div>
    @endif

    <!-- Search & Table Card -->
    <div class="bg-white dark:bg-[#0b1220] border border-slate-200 dark:border-slate-800 rounded-xl shadow-sm overflow-hidden flex flex-col">
        
        <!-- Header Controls -->
        <div class="p-5 border-b border-slate-200 dark:border-slate-800 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-slate-100 dark:bg-slate-800 rounded-lg text-slate-600 dark:text-slate-300">
                    <i class="fa-solid fa-list-ul"></i>
                </div>
                <h2 class="text-lg font-bold text-slate-900 dark:text-white uppercase tracking-wider">Data Role</h2>
            </div>

            <!-- Search Form -->
            <form method="GET" action="{{ route('roles.index') }}" class="w-full sm:w-auto flex gap-2">
                <div class="relative w-full sm:w-64 group">
                    <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-sm text-slate-400 group-focus-within:text-brand-500 transition-colors"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama role..."
                        class="w-full pl-9 pr-3 py-2 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-lg text-sm focus:ring-2 focus:ring-brand-500/50 focus:border-brand-500 outline-none text-slate-900 dark:text-white transition-all placeholder:text-slate-400">
                </div>
                <button type="submit" class="bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700 px-4 py-2 rounded-lg text-sm transition-colors border border-slate-200 dark:border-slate-700">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </button>
            </form>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-slate-600 dark:text-slate-300 whitespace-nowrap">
                <thead class="text-xs uppercase bg-slate-50 dark:bg-slate-800/50 text-slate-700 dark:text-slate-300 border-b border-slate-200 dark:border-slate-700 font-semibold tracking-wider">
                    <tr>
                        <th class="px-5 py-3 font-bold">Nama Role</th>
                        <th class="px-5 py-3 font-bold text-center">Permission</th>
                        <th class="px-5 py-3 font-bold text-center">User</th>
                        <th class="px-5 py-3 font-bold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/50">
                    @forelse ($roles as $role)
                        @php($inti = in_array($role->name, $protectedRoles))
                        <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/30 transition-colors align-middle">
                            <td class="px-5 py-3 font-medium text-slate-900 dark:text-white">
                                {{ $role->name }}
                                @if ($inti)
                                    <span class="ml-2 inline-flex items-center gap-1 py-0.5 px-2 rounded-md text-[10px] font-bold bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400 border border-slate-200 dark:border-slate-700">
                                        <i class="fa-solid fa-lock text-[8px]"></i> Inti
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-3 text-center font-mono font-bold text-brand-600 dark:text-brand-400">
                                {{ $role->permissions_count }}
                            </td>
                            <td class="px-5 py-3 text-center font-mono font-bold text-slate-700 dark:text-slate-300">
                                {{ $role->users_count }}
                            </td>
                            <td class="px-5 py-3">
                                <div class="flex justify-end items-center gap-2">
                                    @can('role.update')
                                        @if ($role->name === 'admin')
                                            <span class="flex items-center gap-1.5 px-2.5 py-1.5 text-xs font-semibold text-slate-500 bg-slate-100 dark:text-slate-400 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-md cursor-not-allowed" title="Admin selalu full access">
                                                <i class="fa-solid fa-lock"></i> Terkunci
                                            </span>
                                        @else
                                            <a href="{{ route('roles.permissions.edit', $role) }}"
                                                class="flex items-center gap-1.5 px-2.5 py-1.5 text-xs font-semibold text-brand-700 bg-brand-50 hover:bg-brand-100 dark:text-brand-400 dark:bg-brand-500/10 dark:hover:bg-brand-500/20 border border-brand-200 dark:border-brand-500/30 rounded-md transition-colors">
                                                <i class="fa-solid fa-key"></i> Permission
                                            </a>
                                        @endif

                                        @unless ($inti)
                                            <a href="{{ route('roles.edit', $role) }}"
                                                class="flex items-center gap-1.5 px-2.5 py-1.5 text-xs font-semibold text-amber-700 bg-amber-50 hover:bg-amber-100 dark:text-amber-400 dark:bg-amber-500/10 dark:hover:bg-amber-500/20 border border-amber-200 dark:border-amber-500/30 rounded-md transition-colors">
                                                <i class="fa-solid fa-pen"></i> Edit
                                            </a>
                                        @endunless
                                    @endcan

                                    @can('role.delete')
                                        @unless ($inti)
                                            <form method="POST" action="{{ route('roles.destroy', $role) }}"
                                                onsubmit="return konfirmasiHapus(event, this, '{{ $role->name }}')">
                                                @csrf
                                                @method('DELETE')
                                                <button class="flex items-center gap-1.5 px-2.5 py-1.5 text-xs font-semibold text-rose-700 bg-rose-50 hover:bg-rose-100 dark:text-rose-400 dark:bg-rose-500/10 dark:hover:bg-rose-500/20 border border-rose-200 dark:border-rose-500/30 rounded-md transition-colors">
                                                    <i class="fa-solid fa-trash"></i> Hapus
                                                </button>
                                            </form>
                                        @endunless
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-5 py-12 text-center text-slate-500 dark:text-slate-400">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <i class="fa-solid fa-folder-open text-4xl text-slate-300 dark:text-slate-600 mb-2"></i>
                                    <p>Belum ada data role yang tersedia.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination Wrapper (Laravel Default) -->
        @if($roles->hasPages())
            <div class="p-5 border-t border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/20">
                {{ $roles->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
    <script>
        function konfirmasiHapus(e, form, nama) {
            e.preventDefault();
            Swal.fire({
                title: 'Hapus role "' + nama + '"?',
                text: 'Tindakan ini tidak bisa dibatalkan.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, hapus',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#e11d48', // Rose-600
                background: document.documentElement.classList.contains('dark') ? '#0b1220' : '#ffffff',
                color: document.documentElement.classList.contains('dark') ? '#f8fafc' : '#0f172a'
            }).then(r => { if (r.isConfirmed) form.submit(); });
            return false;
        }
    </script>
@endpush