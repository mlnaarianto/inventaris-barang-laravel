<?php

use Diglactic\Breadcrumbs\Breadcrumbs;

/*
|--------------------------------------------------------------------------
| Breadcrumb Definitions (Diglactic)
|--------------------------------------------------------------------------
*/

// ===== Root (Dynamic Dashboard based on Role) =====
Breadcrumbs::for('home', function ($trail) {
    $user = auth()->user();

    if ($user && $user->can('user.view')) {
        // Admin
        $trail->push('Dashboard', route('dashboard'));
    } elseif ($user && $user->can('tenant.view')) {
        // Pengelola
        $trail->push('Dashboard', route('pengelola'));
    } elseif ($user && $user->can('databarang.view')) {
        // User biasa
        $trail->push('Dashboard', route('user'));
    } else {
        // Fallback jika tidak punya akses apa-apa
        $trail->push('Home', url('/'));
    }
});

// ===== Dashboard Admin =====
// Cukup panggil parent('home') agar tidak terjadi "Dashboard / Dashboard"
Breadcrumbs::for('dashboard', function ($trail) {
    $trail->parent('home');
});

// ===== Profil =====
Breadcrumbs::for('profile.edit', function ($trail) {
    $trail->parent('home');
    $trail->push('Profil', route('profile.edit'));
});

// ===== Manajemen Role =====
Breadcrumbs::for('roles.index', function ($trail) {
    $trail->parent('home');
    $trail->push('Roles', route('roles.index'));
});

Breadcrumbs::for('roles.create', function ($trail) {
    $trail->parent('roles.index');
    $trail->push('Tambah', null); 
});

Breadcrumbs::for('roles.edit', function ($trail) {
    $trail->parent('roles.index');
    $trail->push('Edit', null);
});

Breadcrumbs::for('roles.permissions.edit', function ($trail) {
    $trail->parent('roles.index');
    $trail->push('Permissions', null);
});

// ===== Persetujuan Tenant =====
Breadcrumbs::for('tenantuser.index', function ($trail) {
    $trail->parent('home');
    $trail->push('Persetujuan Tenant', route('tenantuser.index'));
});

// ===== Tenant Pengelola =====
Breadcrumbs::for('pengelola', function ($trail) {
    $trail->parent('home');
    $trail->push('Tenant', route('pengelola'));
});

Breadcrumbs::for('pengelola.edit', function ($trail) {
    $trail->parent('pengelola');
    $trail->push('Edit', null);
});

// ===== Anggota Tenant =====
Breadcrumbs::for('tenantmembers.index', function ($trail) {
    $trail->parent('home');
    $trail->push('Anggota Tenant', route('tenantmembers.index'));
});

// ===== Laporan =====
Breadcrumbs::for('laporanbarangmasuk', function ($trail) {
    $trail->parent('home');
    $trail->push('Laporan', '#');
    $trail->push('Barang Masuk', null);
});

Breadcrumbs::for('laporanbarangkeluar', function ($trail) {
    $trail->parent('home');
    $trail->push('Laporan', '#');
    $trail->push('Barang Keluar', null);
});

Breadcrumbs::for('laporanstok', function ($trail) {
    $trail->parent('home');
    $trail->push('Laporan', '#');
    $trail->push('Stok', null);
});

// ===== Dashboard User (Inventaris) =====
Breadcrumbs::for('user', function ($trail) {
    $trail->parent('home');
    $trail->push('Inventaris', route('user'));
});

Breadcrumbs::for('namatenant', function ($trail) {
    $trail->parent('home');
    $trail->push('Nama Tenant', route('namatenant'));
});

// ===== Barang: Satuan =====
Breadcrumbs::for('satuan.index', function ($trail) {
    $trail->parent('home');
    $trail->push('Barang', '#');
    $trail->push('Satuan', route('satuan.index'));
});

Breadcrumbs::for('satuan.edit', function ($trail) {
    $trail->parent('satuan.index');
    $trail->push('Edit', null);
});

// ===== Barang: Barang =====
Breadcrumbs::for('databarangs.index', function ($trail) {
    $trail->parent('home');
    $trail->push('Barang', '#');
    $trail->push('Data Barang', route('databarangs.index'));
});

Breadcrumbs::for('databarangs.edit', function ($trail) {
    $trail->parent('databarangs.index');
    $trail->push('Edit', null);
});

// ===== Barang: Jenis Barang =====
Breadcrumbs::for('jenisbarangs.index', function ($trail) {
    $trail->parent('home');
    $trail->push('Barang', '#');
    $trail->push('Jenis Barang', route('jenisbarangs.index'));
});

Breadcrumbs::for('jenisbarangs.edit', function ($trail) {
    $trail->parent('jenisbarangs.index');
    $trail->push('Edit', null);
});

// ===== Transaksi: Barang Masuk =====
Breadcrumbs::for('barangmasuk.index', function ($trail) {
    $trail->parent('home');
    $trail->push('Transaksi', '#');
    $trail->push('Barang Masuk', route('barangmasuk.index'));
});

Breadcrumbs::for('barangmasuk.edit', function ($trail) {
    $trail->parent('barangmasuk.index');
    $trail->push('Edit', null);
});

// ===== Transaksi: Barang Keluar =====
Breadcrumbs::for('barangkeluar.index', function ($trail) {
    $trail->parent('home');
    $trail->push('Transaksi', '#');
    $trail->push('Barang Keluar', route('barangkeluar.index'));
});

Breadcrumbs::for('barangkeluar.edit', function ($trail) {
    $trail->parent('barangkeluar.index');
    $trail->push('Edit', null);
});