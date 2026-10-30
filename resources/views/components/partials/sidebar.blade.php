<!-- resources/views/components/partials/sidebar.blade.php -->
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

// Eager load users agar bisa ambil avatar pengelola tanpa N+1
$daftarTenant = $bisaBypass
? \App\Models\Tenant::with('users')->where('status', 'aktif')->orderBy('nama')->get()
: collect();

// Map data untuk Alpine.js
$daftarTenantJson = $daftarTenant->map(function($t) {
    $userTenant = $t->users->first();
    return [
        'id' => $t->id,
        'nama' => $t->nama,
        'nama_perusahaan' => $t->nama_perusahaan,
        'avatar_url' => $userTenant?->avatar_url ?: asset('images/1.jpg')
    ];
})->values();
@endphp

<aside
    class="fixed top-16 left-0 bottom-0 z-40 bg-white dark:bg-[#0b1220] border-r border-slate-200 dark:border-slate-800 transition-all duration-300 flex flex-col shadow-xl dark:shadow-brand-950/20"
    :class="sidebarOpen ? 'w-64 translate-x-0' : 'w-64 -translate-x-full md:w-0 md:translate-x-0 md:overflow-hidden'">

    {{-- Scrollable Area --}}
    <div class="flex-1 overflow-y-auto p-4 custom-scrollbar">

        {{-- User Info Mobile (Tampil jika di mobile) --}}
        <div class="flex items-center gap-3 mb-6 md:hidden pb-4 border-b border-slate-100 dark:border-slate-800">
            <img src="{{ $authUser->avatar_url ?: asset('images/1.jpg') }}"
                onerror="this.src='{{ asset('images/1.jpg') }}'"
                style="width: 40px; height: 40px; min-width: 40px; object-fit: cover;"
                class="rounded-full border border-slate-200 dark:border-slate-700" alt="User">
            <div>
                <p class="font-bold text-sm text-slate-900 dark:text-white">{{ $authUser->nama }}</p>
                <p class="text-xs text-brand-600 dark:text-brand-400 capitalize">{{ $authUser->getRoleNames()->first() }}</p>
            </div>
        </div>

        {{-- Tenant Selector (Khusus Admin) --}}
        @if ($bisaBypass)
        <form action="{{ route('tenant.context') }}" method="POST" class="mb-6" x-data="tenantDropdown({{ $tenantTerpilih ? $tenantTerpilih->id : 'null' }})">
            @csrf
            <label class="text-[10px] uppercase tracking-wider text-slate-400 dark:text-slate-500 font-bold">Kelola tenant</label>
            
            <div class="relative mt-1" @click.away="open = false">
                <!-- Hidden Input untuk dikirim ke backend -->
                <input type="hidden" name="tenant_id" x-model="selectedId">

                <!-- Tombol Trigger -->
                <button type="button" @click="open = !open; if(open) $nextTick(() => $refs.searchInput.focus())"
                    class="w-full flex justify-between items-center bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 text-sm rounded-lg pl-3 pr-8 py-2 focus:ring-2 focus:ring-brand-500/50 outline-none transition-all text-left">
                    <div class="flex items-center gap-2 min-w-0">
                        <!-- Avatar Terpilih -->
                        <template x-if="selectedTenant">
                            <img :src="selectedTenant.avatar_url" 
                                 onerror="this.src='{{ asset('images/1.jpg') }}'" 
                                 style="width: 20px; height: 20px; min-width: 20px; object-fit: cover;" 
                                 class="rounded-full border border-slate-200 dark:border-slate-700">
                        </template>
                        <span x-text="selectedTenant ? selectedTenant.nama : 'Semua tenant (lihat)'" class="truncate"></span>
                    </div>
                    <i class="fa-solid fa-chevron-down absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs transition-transform duration-200" :class="open ? 'rotate-180' : ''"></i>
                </button>

                <!-- Dropdown Menu -->
                <div x-show="open" x-transition.opacity style="display: none;"
                    class="absolute z-50 w-full mt-1 bg-white dark:bg-[#0b1220] border border-slate-200 dark:border-slate-700 rounded-lg shadow-xl max-h-60 overflow-y-auto">
                    
                    <!-- Input Pencarian -->
                    <div class="sticky top-0 bg-white dark:bg-[#0b1220] p-2 border-b border-slate-100 dark:border-slate-800 z-10">
                        <input type="text" x-model="search" x-ref="searchInput" placeholder="Cari tenant..."
                            class="w-full bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white text-xs rounded-md focus:ring-1 focus:ring-brand-500/50 outline-none p-2 placeholder:text-slate-400">
                    </div>

                    <!-- List Tenant -->
                    <ul class="py-1">
                        <!-- Opsi Semua Tenant (hanya muncul jika tidak sedang mencari) -->
                        <template x-if="search === ''">
                            <li @click="selectTenant(null)"
                                class="px-3 py-2 text-sm cursor-pointer hover:bg-brand-50 dark:hover:bg-brand-500/10 hover:text-brand-700 dark:hover:text-brand-400 transition-colors text-slate-700 dark:text-slate-300"
                                :class="selectedId === '' ? 'bg-brand-50 dark:bg-brand-500/10 font-medium' : ''">
                                Semua tenant (lihat)
                            </li>
                        </template>
                        
                        <!-- Looping Tenant -->
                        <template x-for="tenant in filteredTenants" :key="tenant.id">
                            <li @click="selectTenant(tenant)"
                                class="px-3 py-2 text-sm cursor-pointer hover:bg-brand-50 dark:hover:bg-brand-500/10 hover:text-brand-700 dark:hover:text-brand-400 transition-colors flex items-center gap-2.5"
                                :class="selectedId == tenant.id ? 'bg-brand-50 dark:bg-brand-500/10 text-brand-700 dark:text-brand-400 font-medium' : 'text-slate-700 dark:text-slate-300'">
                                <!-- Avatar di List -->
                                <img :src="tenant.avatar_url" 
                                     onerror="this.src='{{ asset('images/1.jpg') }}'" 
                                     style="width: 24px; height: 24px; min-width: 24px; object-fit: cover;" 
                                     class="rounded-full border border-slate-200 dark:border-slate-700">
                                <div class="min-w-0 flex-1">
                                    <div class="truncate text-xs" x-text="tenant.nama"></div>
                                    <div class="text-[10px] text-slate-400 truncate" x-text="tenant.nama_perusahaan"></div>
                                </div>
                            </li>
                        </template>

                        <!-- Jika tidak ada hasil -->
                        <li x-show="filteredTenants.length === 0 && search !== ''" class="px-3 py-3 text-xs text-center text-slate-500">
                            Tenant tidak ditemukan.
                        </li>
                    </ul>
                </div>
            </div>
        </form>
        @endif

        <nav class="space-y-1">
            {{-- Dashboard --}}
            @can('user.view')
            <a href="{{ route('dashboard') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors {{ request()->routeIs('dashboard') ? 'bg-brand-50 text-brand-600 dark:bg-brand-500/10 dark:text-brand-400 font-semibold border-l-2 border-brand-500' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800/50 hover:text-slate-900 dark:hover:text-white' }}">
                <i class="fa-solid fa-house w-5 text-center"></i> <span class="text-sm">Dashboard Admin</span>
            </a>
            @elsecan('tenant.view')
            <a href="{{ route('pengelola') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors {{ request()->routeIs('pengelola*') ? 'bg-brand-50 text-brand-600 dark:bg-brand-500/10 dark:text-brand-400 font-semibold border-l-2 border-brand-500' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800/50 hover:text-slate-900 dark:hover:text-white' }}">
                <i class="fa-solid fa-building w-5 text-center"></i> <span class="text-sm">Tambah Tenant</span>
            </a>
            @else
            <a href="{{ route('user') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors {{ request()->routeIs('user') ? 'bg-brand-50 text-brand-600 dark:bg-brand-500/10 dark:text-brand-400 font-semibold border-l-2 border-brand-500' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800/50 hover:text-slate-900 dark:hover:text-white' }}">
                <i class="fa-solid fa-house w-5 text-center"></i> <span class="text-sm">Dashboard User</span>
            </a>
            @endcan

            {{-- Administrasi --}}
            @can('tenant.approve')
            <a href="{{ route('tenantuser.index') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors {{ request()->routeIs('tenantuser.*') ? 'bg-brand-50 text-brand-600 dark:bg-brand-500/10 dark:text-brand-400 font-semibold border-l-2 border-brand-500' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800/50 hover:text-slate-900 dark:hover:text-white' }}">
                <i class="fa-solid fa-users w-5 text-center"></i> <span class="text-sm">Manajemen Pengguna Tenant</span>
            </a>
            @endcan

           

            {{-- Anggota Tenant --}}
            @if ($punyaTenantAktif)
            @can('member.view')
            <a href="{{ route('tenantmembers.index') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors {{ request()->routeIs('tenantmembers.*') ? 'bg-brand-50 text-brand-600 dark:bg-brand-500/10 dark:text-brand-400 font-semibold border-l-2 border-brand-500' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800/50 hover:text-slate-900 dark:hover:text-white' }}">
                <i class="fa-solid fa-user-gear w-5 text-center"></i> <span class="text-sm">Tambah User Tenant</span>
            </a>
            @endcan
            @endif

            {{-- Laporan --}}
            @if ($punyaTenantAktif)
            @can('laporan.view')
            <div x-data="{ open: {{ request()->routeIs('laporanstok', 'laporanbarangmasuk', 'laporanbarangkeluar') ? 'true' : 'false' }} }">
                <button @click="open = !open"
                    class="w-full flex items-center justify-between px-3 py-2.5 rounded-lg text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800/50 hover:text-slate-900 dark:hover:text-white transition-colors
                {{ request()->routeIs('laporanstok', 'laporanbarangmasuk', 'laporanbarangkeluar') ? 'bg-brand-50 text-brand-600 dark:bg-brand-500/10 dark:text-brand-400 font-semibold border-l-2 border-brand-500' : '' }}">
                    <div class="flex items-center gap-3">
                        <i class="fa-solid fa-chart-line w-5 text-center"></i> <span class="text-sm">Laporan</span>
                    </div>
                    <i class="fa-solid fa-chevron-down text-xs transition-transform duration-200" :class="open ? 'rotate-180' : ''"></i>
                </button>

                <div x-show="open" x-collapse x-cloak class="mt-1 ml-4 pl-4 border-l border-slate-200 dark:border-slate-700 space-y-1">
                    <a href="{{ route('laporanstok') }}"
                        class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('laporanstok') ? 'text-brand-600 dark:text-brand-400 font-semibold' : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
                        <i class="fa-solid fa-chart-bar w-4 text-center text-xs"></i> <span class="text-sm">Laporan Stok</span>
                    </a>
                    <a href="{{ route('laporanbarangmasuk') }}"
                        class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('laporanbarangmasuk') ? 'text-brand-600 dark:text-brand-400 font-semibold' : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
                        <i class="fa-solid fa-file-import w-4 text-center text-xs"></i> <span class="text-sm">Laporan Barang Masuk</span>
                    </a>
                    <a href="{{ route('laporanbarangkeluar') }}"
                        class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('laporanbarangkeluar') ? 'text-brand-600 dark:text-brand-400 font-semibold' : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
                        <i class="fa-solid fa-file-export w-4 text-center text-xs"></i> <span class="text-sm">Laporan Barang Keluar</span>
                    </a>
                </div>
            </div>
            @endcan
            @endif

            {{-- Data Barang & Transaksi --}}
            @if ($punyaTenant)
            @can('databarang.view')
            <a href="{{ route('namatenant') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors {{ request()->routeIs('namatenant') ? 'bg-brand-50 text-brand-600 dark:bg-brand-500/10 dark:text-brand-400 font-semibold border-l-2 border-brand-500' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800/50 hover:text-slate-900 dark:hover:text-white' }}">
                <i class="fa-solid fa-building w-5 text-center"></i> <span class="text-sm">Nama Tenant</span>
            </a>
            @endcan

            {{-- Submenu Barang --}}
            @canany(['satuan.view', 'jenisbarang.view', 'databarang.view'])
            <div x-data="{ open: {{ request()->routeIs('satuan.*', 'jenisbarangs.*', 'databarangs.*') ? 'true' : 'false' }} }">
                <button @click="open = !open" class="w-full flex items-center justify-between px-3 py-2.5 rounded-lg text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800/50 hover:text-slate-900 dark:hover:text-white transition-colors
                {{ request()->routeIs('satuan.*', 'jenisbarangs.*', 'databarangs.*') ? 'bg-brand-50 text-brand-600 dark:bg-brand-500/10 dark:text-brand-400 font-semibold border-l-2 border-brand-500' : '' }}">
                    <div class="flex items-center gap-3">
                        <i class="fa-solid fa-box w-5 text-center"></i> <span class="text-sm">Barang</span>
                    </div>
                    <i class="fa-solid fa-chevron-down text-xs transition-transform duration-200" :class="open ? 'rotate-180' : ''"></i>
                </button>

                <div x-show="open" x-collapse x-cloak class="mt-1 ml-4 pl-4 border-l border-slate-200 dark:border-slate-700 space-y-1">
                    @can('satuan.view')
                    <a href="{{ route('satuan.index') }}"
                        class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('satuan.*') ? 'text-brand-600 dark:text-brand-400 font-semibold' : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
                        <i class="fa-solid fa-ruler w-4 text-center text-xs"></i> <span class="text-sm">Satuan</span>
                    </a>
                    @endcan
                    @can('jenisbarang.view')
                    <a href="{{ route('jenisbarangs.index') }}"
                        class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('jenisbarangs.*') ? 'text-brand-600 dark:text-brand-400 font-semibold' : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
                        <i class="fa-solid fa-tags w-4 text-center text-xs"></i> <span class="text-sm">Jenis Barang</span>
                    </a>
                    @endcan
                    @can('databarang.view')
                    <a href="{{ route('databarangs.index') }}"
                        class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('databarangs.*') ? 'text-brand-600 dark:text-brand-400 font-semibold' : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
                        <i class="fa-solid fa-box-open w-4 text-center text-xs"></i> <span class="text-sm">Data Barang</span>
                    </a>
                    @endcan
                </div>
            </div>
            @endcanany

            {{-- Submenu Transaksi (BARANG MASUK & BARANG KELUAR) --}}
            @canany(['barangmasuk.view', 'barangkeluar.view'])
            <div x-data="{ open: {{ request()->routeIs('barangmasuk.*', 'barangkeluar.*') ? 'true' : 'false' }} }">
                <button @click="open = !open" class="w-full flex items-center justify-between px-3 py-2.5 rounded-lg text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800/50 hover:text-slate-900 dark:hover:text-white transition-colors
                {{ request()->routeIs('barangmasuk.*', 'barangkeluar.*') ? 'bg-brand-50 text-brand-600 dark:bg-brand-500/10 dark:text-brand-400 font-semibold border-l-2 border-brand-500' : '' }}">
                    <div class="flex items-center gap-3">
                        <i class="fa-solid fa-arrow-right-arrow-left w-5 text-center"></i> <span class="text-sm">Transaksi</span>
                    </div>
                    <i class="fa-solid fa-chevron-down text-xs transition-transform duration-200" :class="open ? 'rotate-180' : ''"></i>
                </button>

                <div x-show="open" x-collapse x-cloak class="mt-1 ml-4 pl-4 border-l border-slate-200 dark:border-slate-700 space-y-1">
                    @can('barangmasuk.view')
                    <a href="{{ route('barangmasuk.index') }}"
                        class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('barangmasuk.*') ? 'text-brand-600 dark:text-brand-400 font-semibold' : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
                        <i class="fa-solid fa-download w-4 text-center text-xs"></i> <span class="text-sm">Barang Masuk</span>
                    </a>
                    @endcan

                    @can('barangkeluar.view')
                    <a href="{{ route('barangkeluar.index') }}"
                        class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('barangkeluar.*') ? 'text-brand-600 dark:text-brand-400 font-semibold' : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
                        <i class="fa-solid fa-upload w-4 text-center text-xs"></i> <span class="text-sm">Barang Keluar</span>
                    </a>
                    @endcan
                </div>
            </div>
            @endcanany
            @endif

             @can('role.view')
            <a href="{{ route('roles.index') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors {{ request()->routeIs('roles.*') ? 'bg-brand-50 text-brand-600 dark:bg-brand-500/10 dark:text-brand-400 font-semibold border-l-2 border-brand-500' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800/50 hover:text-slate-900 dark:hover:text-white' }}">
                <i class="fa-solid fa-user-shield w-5 text-center"></i> <span class="text-sm">Manajemen Role</span>
            </a>
            @endcan
        </nav>
    </div>

    {{-- Bottom Profile Link --}}
    <div class="p-4 border-t border-slate-200 dark:border-slate-800">
        <a href="{{ route('profile.edit') }}"
            class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors {{ request()->routeIs('profile.*') ? 'bg-brand-50 text-brand-600 dark:bg-brand-500/10 dark:text-brand-400 font-semibold' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800/50 hover:text-slate-900 dark:hover:text-white' }}">
            <i class="fa-solid fa-user w-5 text-center"></i> <span class="text-sm">Profil Akun</span>
        </a>
    </div>
</aside>

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('tenantDropdown', (initialId) => ({
            open: false,
            search: '',
            selectedId: initialId || '',
            tenants: @json($daftarTenantJson),
            
            // Ambil data tenant yang sedang terpilih
            get selectedTenant() {
                if (!this.selectedId) return null;
                return this.tenants.find(t => t.id == this.selectedId);
            },
            
            // Filter pencarian
            get filteredTenants() {
                if (this.search === '') return this.tenants;
                return this.tenants.filter(tenant => 
                    tenant.nama.toLowerCase().includes(this.search.toLowerCase()) || 
                    (tenant.nama_perusahaan && tenant.nama_perusahaan.toLowerCase().includes(this.search.toLowerCase()))
                );
            },
            
            // Aksi saat tenant dipilih
            selectTenant(tenant) {
                this.selectedId = tenant ? tenant.id : '';
                this.open = false;
                this.search = '';
                
                // Submit form secara otomatis setelah render
                this.$nextTick(() => {
                    this.$el.closest('form').submit();
                });
            }
        }));
    });
</script>
@endpush