@php
$authUser = Auth::user();

// Tenant aktif milik user: didaftarkan dengan email-nya, atau tertaut lewat users.id_tenant.
// Dihitung di sini supaya sidebar tidak bergantung pada variabel $tenants dari controller.
$punyaTenantAktif = \App\Models\Tenant::where('status', 'aktif')
->where(function ($q) use ($authUser) {
$q->where('email', $authUser->email);
if ($authUser->id_tenant) {
$q->orWhere('id', $authUser->id_tenant);
}
})
->exists();

$punyaTenant = $authUser->id_tenant !== null;
@endphp

<aside :class="sidebarOpen ? 'w-64' : 'w-0 overflow-hidden'"
    class="bg-purple-700 text-white fixed top-16 left-0 bottom-0 p-4 transition-all duration-300 overflow-y-auto">

    <div class="flex items-center space-x-2 mb-6" x-show="sidebarOpen">
        <img src="{{ asset('images/1.jpg') }}" class="rounded-full w-10 h-10" alt="User">
        <div>
            <p class="font-bold">{{ $authUser->nama }}</p>
            <p class="text-sm capitalize">{{ $authUser->getRoleNames()->first() }}</p>
        </div>
    </div>

    <nav x-show="sidebarOpen">
        <!-- Dashboard sesuai role -->
        @if ($authUser->hasRole('admin'))
        <a href="{{ route('dashboard') }}"
            class="p-2 rounded mb-2 flex items-center space-x-2
                    {{ Request::is('dashboard') ? 'bg-purple-900' : 'hover:bg-purple-800' }}">
            <i class="fa-solid fa-house"></i> <span>Dashboard Admin</span>
        </a>
        @elseif ($authUser->hasRole('pengelola'))
        <a href="{{ route('pengelola') }}"
            class="p-2 rounded mb-2 flex items-center space-x-2
                    {{ Request::is('pengelola') ? 'bg-purple-900' : 'hover:bg-purple-800' }}">
            <i class="fa-solid fa-building"></i> <span>Tambah Tenant</span>
        </a>
        @else
        <a href="{{ route('user') }}"
            class="p-2 rounded mb-2 flex items-center space-x-2
                    {{ Request::is('user') ? 'bg-purple-900' : 'hover:bg-purple-800' }}">
            <i class="fa-solid fa-house"></i> <span>Dashboard User</span>
        </a>
        @endif

        <!-- Menu khusus Admin -->
        @if ($authUser->hasRole('admin'))
        <a href="/tenantuser"
            class="p-2 rounded flex items-center space-x-2
                    {{ Request::is('tenantuser') ? 'bg-purple-900' : 'hover:bg-purple-800' }}">
            <i class="fa-solid fa-users"></i> <span>Manajemen Pengguna Tenant</span>
        </a>
        @endif

        <!-- Menu untuk Pengelola (tenant sudah aktif) -->
        @if ($authUser->hasRole('pengelola') && $punyaTenantAktif)
        <a href="{{ route('tenantmembers.index') }}"
            class="p-2 rounded flex items-center space-x-2 text-white
        {{ Request::is('tenantmembers') ? 'bg-purple-900' : 'hover:bg-purple-800' }}">
            <i class="fa-solid fa-user-gear"></i> <span>Tambah User Tenant</span>
        </a>
        <a href="/laporanstok"
            class="p-2 rounded flex items-center space-x-2
                    {{ Request::is('laporanstok') ? 'bg-purple-900' : 'hover:bg-purple-800' }}">
            <i class="fa-solid fa-chart-bar"></i> <span>Laporan Stok</span>
        </a>
        <a href="/laporanbarangmasuk"
            class="p-2 rounded flex items-center space-x-2
                    {{ Request::is('laporanbarangmasuk') ? 'bg-purple-900' : 'hover:bg-purple-800' }}">
            <i class="fa-solid fa-file-import"></i> <span>Laporan Barang Masuk</span>
        </a>
        <a href="/laporanbarangkeluar"
            class="p-2 rounded flex items-center space-x-2
                    {{ Request::is('laporanbarangkeluar') ? 'bg-purple-900' : 'hover:bg-purple-800' }}">
            <i class="fa-solid fa-file-export"></i> <span>Laporan Barang Keluar</span>
        </a>
        @endif

        <!-- Menu untuk User yang sudah punya tenant -->
        @if ($authUser->hasRole('user') && $punyaTenant)
        <a href="/namatenant"
            class="p-2 rounded flex items-center space-x-2
                    {{ Request::is('namatenant') ? 'bg-purple-900' : 'hover:bg-purple-800' }}">
            <i class="fa-solid fa-building"></i> <span>Nama Tenant</span>
        </a>

        <!-- Submenu Barang -->
        <div x-data="{ open: false }"
            x-init="open = {{ Request::is('satuan') || Request::is('jenisbarang') || Request::is('databarang') ? 'true' : 'false' }}"
            @click.away="open = false">
            <a @click="open = !open"
                class="p-2 rounded flex items-center space-x-2 cursor-pointer hover:bg-purple-800">
                <i class="fa-solid fa-box"></i> <span>Barang</span>
                <i class="fa-solid fa-chevron-down ml-auto" x-show="!open"></i>
                <i class="fa-solid fa-chevron-up ml-auto" x-show="open"></i>
            </a>
            <div x-show="open" class="ml-6">
                <a href="/satuan"
                    class="p-2 rounded flex items-center space-x-2
                            {{ Request::is('satuan') ? 'bg-purple-900' : 'hover:bg-purple-700' }}">
                    <i class="fa-solid fa-ruler"></i> <span>Satuan</span>
                </a>
                <a href="/jenisbarang"
                    class="p-2 rounded flex items-center space-x-2
                            {{ Request::is('jenisbarang') ? 'bg-purple-900' : 'hover:bg-purple-700' }}">
                    <i class="fa-solid fa-tags"></i> <span>Jenis Barang</span>
                </a>
                <a href="/databarang"
                    class="p-2 rounded flex items-center space-x-2
                            {{ Request::is('databarang') ? 'bg-purple-900' : 'hover:bg-purple-700' }}">
                    <i class="fa-solid fa-box-open"></i> <span>Data Barang</span>
                </a>
            </div>
        </div>

        <a href="/barangmasuk"
            class="p-2 rounded flex items-center space-x-2
                    {{ Request::is('barangmasuk') ? 'bg-purple-900' : 'hover:bg-purple-800' }}">
            <i class="fa-solid fa-download"></i> <span>Barang Masuk</span>
        </a>
        <a href="/barangkeluar"
            class="p-2 rounded flex items-center space-x-2
                    {{ Request::is('barangkeluar') ? 'bg-purple-900' : 'hover:bg-purple-800' }}">
            <i class="fa-solid fa-upload"></i> <span>Barang Keluar</span>
        </a>
        @endif
    </nav>
</aside>