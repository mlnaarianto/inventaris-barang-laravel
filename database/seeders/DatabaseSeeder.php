<?php

namespace Database\Seeders;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Role dan permission harus ada dulu
        $this->call(RolePermissionSeeder::class);

        // 2. Tenant contoh (pengelola dan user wajib punya tenant)
        $tenant = Tenant::create([
            'nama' => 'Tenant Demo',
            'nama_perusahaan' => 'PT Demo Philo',
            'kode' => 'DEMO001',
            'email' => 'tenant@gmail.com',
            'status' => 'aktif',
        ]);

        // 3. Admin (tanpa tenant, melihat semua)
        $admin = User::create([
            'nama' => 'Maulanaganteng123',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('12345678'),
            'status' => 'aktif',
            'email_verified_at' => now(),
        ]);
        $admin->assignRole('admin');

        // 4. Pengelola
        $pengelola = User::create([
            'nama' => 'Maulanaganteng123',
            'email' => 'pengguna3@gmail.com',
            'password' => Hash::make('12345678'),
            'status' => 'aktif',
            'email_verified_at' => now(),
            'id_tenant' => $tenant->id,
        ]);
        $pengelola->assignRole('pengelola');

        // 5. User biasa
        $user = User::create([
            'nama' => 'Maulanaganteng123',
            'email' => 'pengguna2@gmail.com',
            'password' => Hash::make('12345678'),
            'status' => 'aktif',
            'email_verified_at' => now(),
            'id_tenant' => $tenant->id,
        ]);
        $user->assignRole('user');
    }
}