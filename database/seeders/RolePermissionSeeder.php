<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Wajib: bersihkan cache permission Spatie
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        // ===== Daftar permission per modul =====
        $permissions = [
            // master data
            'databarang.view',
            'databarang.create',
            'databarang.update',
            'databarang.delete',
            'jenisbarang.view',
            'jenisbarang.create',
            'jenisbarang.update',
            'jenisbarang.delete',
            'satuan.view',
            'satuan.create',
            'satuan.update',
            'satuan.delete',

            // transaksi
            'barangmasuk.view',
            'barangmasuk.create',
            'barangmasuk.update',
            'barangmasuk.delete',
            'barangkeluar.view',
            'barangkeluar.create',
            'barangkeluar.update',
            'barangkeluar.delete',

            // laporan
            'laporan.view',
            'laporan.export',

            // tenant milik pengelola
            'tenant.view',
            'tenant.create',
            'tenant.update',
            'tenant.delete',

            // anggota tenant (dikelola pengelola)
            'member.view',
            'member.create',
            'member.delete',

            // administrasi (khusus admin)
            'tenant.approve',
            'user.view',
            'user.create',
            'user.update',
            'user.delete',

            // lintas tenant (dipakai TenantScope)
            'tenant.bypass',

            // manajemen role (khusus admin)
            'role.view',
            'role.create',
            'role.update',
            'role.delete',
        ];

        foreach ($permissions as $name) {
            Permission::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
        }

        // ===== Role =====
        $admin     = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $pengelola = Role::firstOrCreate(['name' => 'pengelola', 'guard_name' => 'web']);
        $user      = Role::firstOrCreate(['name' => 'user', 'guard_name' => 'web']);

        // admin: semuanya
        $admin->syncPermissions(Permission::all());

        // pengelola: master data, transaksi, laporan, tenant sendiri, anggota tenant
        $pengelola->syncPermissions([
            'databarang.view',
            'databarang.create',
            'databarang.update',
            'databarang.delete',
            'jenisbarang.view',
            'jenisbarang.create',
            'jenisbarang.update',
            'jenisbarang.delete',
            'satuan.view',
            'satuan.create',
            'satuan.update',
            'satuan.delete',
            'barangmasuk.view',
            'barangmasuk.create',
            'barangmasuk.update',
            'barangmasuk.delete',
            'barangkeluar.view',
            'barangkeluar.create',
            'barangkeluar.update',
            'barangkeluar.delete',
            'laporan.view',
            'laporan.export',
            'tenant.view',
            'tenant.create',
            'tenant.update',
            'tenant.delete',
            'member.view',
            'member.create',
            'member.delete',
        ]);

        // user: melihat data dan mencatat transaksi dasar
        $user->syncPermissions([
            'databarang.view',
            'jenisbarang.view',
            'satuan.view',
            'barangmasuk.view',
            'barangmasuk.create',
            'barangkeluar.view',
            'barangkeluar.create',
            'laporan.view',
        ]);
    }
}
