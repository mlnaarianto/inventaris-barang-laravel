<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionController extends Controller
{
    /**
     * Permission role admin tidak boleh diubah lewat UI
     * (admin harus selalu full access).
     */
    protected array $protectedRoles = ['admin'];

    /**
     * Label modul (bagian sebelum titik pada nama permission).
     */
    protected array $moduleLabels = [
        'databarang'   => 'Data Barang',
        'jenisbarang'  => 'Jenis Barang',
        'satuan'       => 'Satuan',
        'barangmasuk'  => 'Barang Masuk',
        'barangkeluar' => 'Barang Keluar',
        'laporan'      => 'Laporan',
        'tenant'       => 'Tenant',
        'member'       => 'Anggota Tenant',
        'user'         => 'Manajemen User',
        'role'         => 'Manajemen Role',
    ];

    /**
     * Label tiap permission. Kalau menambah permission baru di seeder,
     * tambahkan juga di sini (kalau lupa, tetap tampil lewat fallback).
     */
    protected array $permissionLabels = [
        'databarang.view'   => 'Lihat Data Barang',
        'databarang.create' => 'Tambah Data Barang',
        'databarang.update' => 'Ubah Data Barang',
        'databarang.delete' => 'Hapus Data Barang',

        'jenisbarang.view'   => 'Lihat Jenis Barang',
        'jenisbarang.create' => 'Tambah Jenis Barang',
        'jenisbarang.update' => 'Ubah Jenis Barang',
        'jenisbarang.delete' => 'Hapus Jenis Barang',

        'satuan.view'   => 'Lihat Satuan',
        'satuan.create' => 'Tambah Satuan',
        'satuan.update' => 'Ubah Satuan',
        'satuan.delete' => 'Hapus Satuan',

        'barangmasuk.view'   => 'Lihat Barang Masuk',
        'barangmasuk.create' => 'Catat Barang Masuk',
        'barangmasuk.update' => 'Ubah Barang Masuk',
        'barangmasuk.delete' => 'Hapus Barang Masuk',

        'barangkeluar.view'   => 'Lihat Barang Keluar',
        'barangkeluar.create' => 'Catat Barang Keluar',
        'barangkeluar.update' => 'Ubah Barang Keluar',
        'barangkeluar.delete' => 'Hapus Barang Keluar',

        'laporan.view'   => 'Lihat Laporan',
        'laporan.export' => 'Cetak / Ekspor Laporan (PDF)',

        'tenant.view'    => 'Lihat Tenant Sendiri',
        'tenant.create'  => 'Daftarkan Tenant',
        'tenant.update'  => 'Ubah Tenant Sendiri',
        'tenant.delete'  => 'Hapus Tenant Sendiri',
        'tenant.approve' => 'Setujui / Tolak Tenant (Semua Tenant)',
        'tenant.bypass'  => 'Lihat Data Lintas Tenant (Semua Tenant)',

        'member.view'   => 'Lihat Anggota Tenant',
        'member.create' => 'Tambah Anggota Tenant',
        'member.delete' => 'Keluarkan Anggota Tenant',

        'user.view'   => 'Lihat Semua User',
        'user.create' => 'Tambah User',
        'user.update' => 'Ubah Status User',
        'user.delete' => 'Hapus User',

        'role.view'   => 'Lihat Role',
        'role.create' => 'Tambah Role',
        'role.update' => 'Ubah Role dan Permission',
        'role.delete' => 'Hapus Role',
    ];

    protected array $actionWords = [
        'view'    => 'Lihat',
        'create'  => 'Tambah',
        'update'  => 'Ubah',
        'delete'  => 'Hapus',
        'export'  => 'Ekspor',
        'approve' => 'Setujui',
        'bypass'  => 'Lintas Tenant',
    ];

    /**
     * Permission berdampak luas: ditandai di view (mis. badge peringatan),
     * karena melewati batas tenant atau memberi kuasa administrasi.
     */
    protected array $broadPermissions = ['tenant.bypass', 'tenant.approve'];

    public function edit(Role $role)
    {
        if (in_array($role->name, $this->protectedRoles)) {
            return redirect()
                ->route('roles.index')
                ->with('crud_error', 'Permission role ini tidak dapat diubah.');
        }

        $permissions = $this->groupPermissions(Permission::orderBy('name')->get());
        $rolePermissions = $role->permissions->pluck('name')->toArray();

        return view('roles.permissions', compact('role', 'permissions', 'rolePermissions'));
    }

    public function update(Request $request, Role $role): RedirectResponse
    {
        if (in_array($role->name, $this->protectedRoles)) {
            return back()->withErrors(['permissions' => 'Permission role ini tidak dapat diubah.']);
        }

        $validated = $request->validate([
            'permissions'   => ['array'],
            'permissions.*' => ['string', 'exists:permissions,name'],
        ]);

        // Permission yang tidak dicentang otomatis dilepas dari role.
        // syncPermissions juga membersihkan cache permission Spatie.
        $role->syncPermissions($validated['permissions'] ?? []);

        return redirect()
            ->route('roles.index')
            ->with('crud_success', "Permission role \"{$role->name}\" berhasil diperbarui.");
    }

    private function groupPermissions(Collection $permissions): array
    {
        return $permissions
            ->map(function (Permission $permission) {
                $permission->label = $this->permissionLabels[$permission->name]
                    ?? $this->humanizePermissionName($permission->name);

                $permission->isBroadAccess = in_array($permission->name, $this->broadPermissions);

                return $permission;
            })
            ->groupBy(fn (Permission $permission) => explode('.', $permission->name)[0])
            ->sortKeys()
            ->mapWithKeys(function (Collection $modulePermissions, string $module) {
                $label = $this->moduleLabels[$module] ?? $this->fallbackModuleLabel($module);

                return [$label => $modulePermissions->values()];
            })
            ->all(); // all(), bukan toArray(): toArray() akan mengubah tiap
                     // Permission jadi array dan merusak $permission->id di view.
    }

    private function humanizePermissionName(string $name): string
    {
        [$module, $action] = array_pad(explode('.', $name, 2), 2, '');

        $actionLabel = $this->actionWords[$action] ?? Str::title(str_replace('-', ' ', $action));
        $moduleLabel = $this->moduleLabels[$module] ?? $this->fallbackModuleLabel($module);

        return trim("{$actionLabel} {$moduleLabel}");
    }

    private function fallbackModuleLabel(string $module): string
    {
        return Str::title(str_replace('-', ' ', $module));
    }
}