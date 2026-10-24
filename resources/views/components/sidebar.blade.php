@php
    $authUser = Auth::user();
    $bisaBypass = $authUser->can('tenant.bypass');

    // Tenant aktif milik user itu sendiri: didaftarkan dengan email-nya, atau tertaut lewat users.id_tenant.
    $punyaTenantAktifSendiri = \App\Models\Tenant::where('status', 'aktif')
        ->where(function ($q) use ($authUser) {
            $q->where('email', $authUser->email);
            if ($authUser->id_tenant) {
                $q->orWhere('id', $authUser->id_tenant);
            }
        })
        ->exists();

    // Role dengan permission tenant.bypass (admin) selalu dianggap lolos.
    $punyaTenantAktif = $bisaBypass || $punyaTenantAktifSendiri;
    $punyaTenant = $bisaBypass || $authUser->id_tenant !== null;

    // Konteks tenant untuk admin (dipilih lewat dropdown, disimpan di session)
    $tenantTerpilih = \App\Support\TenantContext::current();
    $daftarTenant = $bisaBypass
        ? \App\Models\Tenant::where('status', 'aktif')->orderBy('nama')->get()
        : collect();
@endphp

<aside :class="sidebarOpen ? 'w-64' : 'w-0 overflow-hidden'"
    class="bg-purple-700 text-white fixed top-16 left-0 bottom-0 p-4 transition-all duration-300 overflow-y-auto">

    <div class="flex items-center space-x-2 mb-6" x-show="sidebarOpen">
        <img src="{{ $authUser->avatar_url ?: asset('images/1.jpg') }}"
            onerror="this.src='{{ asset('images/1.jpg') }}'"
            class="rounded-full w-10 h-10 object-cover" alt="User">
        <div>
            <p class="font-bold">{{ $authUser->nama }}</p>
            <p class="text-sm capitalize">{{ $authUser->getRoleNames()->first() }}</p>
        </div>
    </div>

    {{-- ===== Pilih tenant yang dikelola (khusus akun bypass / admin) ===== --}}
    @if ($bisaBypass)
        <form action="{{ route('tenant.context') }}" method="POST" class="mb-4" x-show="sidebarOpen">
            @csrf
            <label class="text-xs uppercase tracking-wide text-purple-200">Kelola tenant</label>
            <select name="tenant_id" onchange="this.form.submit()"
                class="w-full mt-1 p-2 rounded text-gray-800 text-sm">
                <option value="">Semua tenant (hanya lihat)</option>
                @foreach ($daftarTenant as $t)
                    <option value="{{ $t->id }}" @selected($tenantTerpilih && $tenantTerpilih->id == $t->id)>
                        {{ $t->nama }}
                    </option>
                @endforeach
            </select>
        </form>
    @endif

    <nav x-show="sidebarOpen">

        {{-- ===== Dashboard (urutan sama dengan redirectByRole) ===== --}}
        @can('user.view')
            <a href="{{ route('dashboard') }}"
                class="p-2 rounded mb-2 flex items-center space-x-2 {{ request()->routeIs('dashboard') ? 'bg-purple-900' : 'hover:bg-purple-800' }}">
                <i class="fa-solid fa-house"></i> <span>Dashboard Admin</span>
            </a>
        @elsecan('tenant.view')
            <a href="{{ route('pengelola') }}"
                class="p-2 rounded mb-2 flex items-center space-x-2 {{ request()->routeIs('pengelola*') ? 'bg-purple-900' : 'hover:bg-purple-800' }}">
                <i class="fa-solid fa-building"></i> <span>Tambah Tenant</span>
            </a>
        @else
            <a href="{{ route('user') }}"
                class="p-2 rounded mb-2 flex items-center space-x-2 {{ request()->routeIs('user') ? 'bg-purple-900' : 'hover:bg-purple-800' }}">
                <i class="fa-solid fa-house"></i> <span>Dashboard User</span>
            </a>
        @endcan

        {{-- ===== Administrasi ===== --}}
        @can('tenant.approve')
            <a href="{{ route('tenantuser.index') }}"
                class="p-2 rounded flex items-center space-x-2 {{ request()->routeIs('tenantuser.*') ? 'bg-purple-900' : 'hover:bg-purple-800' }}">
                <i class="fa-solid fa-users"></i> <span>Manajemen Pengguna Tenant</span>
            </a>
        @endcan

        @can('role.view')
            <a href="{{ route('roles.index') }}"
                class="p-2 rounded flex items-center space-x-2 {{ request()->routeIs('roles.*') ? 'bg-purple-900' : 'hover:bg-purple-800' }}">
                <i class="fa-solid fa-user-shield"></i> <span>Manajemen Role</span>
            </a>
        @endcan

        {{-- ===== Anggota tenant (tenant harus aktif, atau role bypass) ===== --}}
        @if ($punyaTenantAktif)
            @can('member.view')
                <a href="{{ route('tenantmembers.index') }}"
                    class="p-2 rounded flex items-center space-x-2 {{ request()->routeIs('tenantmembers.*') ? 'bg-purple-900' : 'hover:bg-purple-800' }}">
                    <i class="fa-solid fa-user-gear"></i> <span>Tambah User Tenant</span>
                </a>
            @endcan
        @endif

        {{-- ===== Laporan (tenant harus aktif, atau role bypass) ===== --}}
        @if ($punyaTenantAktif)
            @can('laporan.view')
                <a href="{{ route('laporanstok') }}"
                    class="p-2 rounded flex items-center space-x-2 {{ request()->routeIs('laporanstok') ? 'bg-purple-900' : 'hover:bg-purple-800' }}">
                    <i class="fa-solid fa-chart-bar"></i> <span>Laporan Stok</span>
                </a>
                <a href="{{ route('laporanbarangmasuk') }}"
                    class="p-2 rounded flex items-center space-x-2 {{ request()->routeIs('laporanbarangmasuk') ? 'bg-purple-900' : 'hover:bg-purple-800' }}">
                    <i class="fa-solid fa-file-import"></i> <span>Laporan Barang Masuk</span>
                </a>
                <a href="{{ route('laporanbarangkeluar') }}"
                    class="p-2 rounded flex items-center space-x-2 {{ request()->routeIs('laporanbarangkeluar') ? 'bg-purple-900' : 'hover:bg-purple-800' }}">
                    <i class="fa-solid fa-file-export"></i> <span>Laporan Barang Keluar</span>
                </a>
            @endcan
        @endif

        {{-- ===== Data barang & transaksi (wajib punya tenant, kecuali role bypass) ===== --}}
        @if ($punyaTenant)
            @can('databarang.view')
                <a href="{{ route('namatenant') }}"
                    class="p-2 rounded flex items-center space-x-2 {{ request()->routeIs('namatenant') ? 'bg-purple-900' : 'hover:bg-purple-800' }}">
                    <i class="fa-solid fa-building"></i> <span>Nama Tenant</span>
                </a>
            @endcan

            {{-- Submenu Barang --}}
            @canany(['satuan.view', 'jenisbarang.view', 'databarang.view'])
                <div x-data="{ open: {{ request()->routeIs('satuan.*', 'jenisbarangs.*', 'databarangs.*') ? 'true' : 'false' }} }">
                    <a @click="open = !open"
                        class="p-2 rounded flex items-center space-x-2 cursor-pointer hover:bg-purple-800">
                        <i class="fa-solid fa-box"></i> <span>Barang</span>
                        <i class="fa-solid fa-chevron-down ml-auto" x-show="!open"></i>
                        <i class="fa-solid fa-chevron-up ml-auto" x-show="open"></i>
                    </a>
                    <div x-show="open" x-cloak class="ml-6">
                        @can('satuan.view')
                            <a href="{{ route('satuan.index') }}"
                                class="p-2 rounded flex items-center space-x-2 {{ request()->routeIs('satuan.*') ? 'bg-purple-900' : 'hover:bg-purple-700' }}">
                                <i class="fa-solid fa-ruler"></i> <span>Satuan</span>
                            </a>
                        @endcan
                        @can('jenisbarang.view')
                            <a href="{{ route('jenisbarangs.index') }}"
                                class="p-2 rounded flex items-center space-x-2 {{ request()->routeIs('jenisbarangs.*') ? 'bg-purple-900' : 'hover:bg-purple-700' }}">
                                <i class="fa-solid fa-tags"></i> <span>Jenis Barang</span>
                            </a>
                        @endcan
                        @can('databarang.view')
                            <a href="{{ route('databarangs.index') }}"
                                class="p-2 rounded flex items-center space-x-2 {{ request()->routeIs('databarangs.*') ? 'bg-purple-900' : 'hover:bg-purple-700' }}">
                                <i class="fa-solid fa-box-open"></i> <span>Data Barang</span>
                            </a>
                        @endcan
                    </div>
                </div>
            @endcanany

            @can('barangmasuk.view')
                <a href="{{ route('barangmasuk.index') }}"
                    class="p-2 rounded flex items-center space-x-2 {{ request()->routeIs('barangmasuk.*') ? 'bg-purple-900' : 'hover:bg-purple-800' }}">
                    <i class="fa-solid fa-download"></i> <span>Barang Masuk</span>
                </a>
            @endcan

            @can('barangkeluar.view')
                <a href="{{ route('barangkeluar.index') }}"
                    class="p-2 rounded flex items-center space-x-2 {{ request()->routeIs('barangkeluar.*') ? 'bg-purple-900' : 'hover:bg-purple-800' }}">
                    <i class="fa-solid fa-upload"></i> <span>Barang Keluar</span>
                </a>
            @endcan
        @endif

        {{-- ===== Akun (semua role yang sudah login) ===== --}}
        <div class="mt-4 pt-4 border-t border-purple-500">
            <a href="{{ route('profile.edit') }}"
                class="p-2 rounded flex items-center space-x-2 {{ request()->routeIs('profile.*') ? 'bg-purple-900' : 'hover:bg-purple-800' }}">
                <i class="fa-solid fa-user"></i> <span>Profil</span>
            </a>
        </div>
    </nav>
</aside>