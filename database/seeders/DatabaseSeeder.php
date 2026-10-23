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

        // 2. Admin (tanpa tenant, melihat semua)
        $this->buatUser('Maulanaganteng123', 'admin@gmail.com', 'admin');

        // 3. Tenant A + pengelola A + user A
        //    Email tenant HARUS sama dengan email pengelola, karena
        //    PengelolaController::index mencari tenant lewat tenants.email
        $tenantA = Tenant::create([
            'nama' => 'Maulanaganteng123',
            'nama_perusahaan' => 'PT Demo Philo',
            'kode' => 'DEMO001',
            'email' => 'pengguna3@gmail.com',
            'telepon' => '081234567890',
            'alamat' => 'Batam',
            'status' => 'aktif',
        ]);

        $this->buatUser('Maulanaganteng123', 'pengguna3@gmail.com', 'pengelola', $tenantA->id);
        $this->buatUser('Maulanaganteng123', 'pengguna2@gmail.com', 'user', $tenantA->id);

        // 4. Tenant B + pengelola B + user B (untuk uji isolasi tenant)
        $tenantB = Tenant::create([
            'nama' => 'Pengelola B',
            'nama_perusahaan' => 'PT Contoh Dua',
            'kode' => 'DEMO002',
            'email' => 'pengelolab@gmail.com',
            'telepon' => '081200000000',
            'alamat' => 'Jakarta',
            'status' => 'aktif',
        ]);

        $this->buatUser('Pengelola B', 'pengelolab@gmail.com', 'pengelola', $tenantB->id);
        $this->buatUser('User B', 'userb@gmail.com', 'user', $tenantB->id);

        // 5. User bebas (belum punya tenant) untuk uji "Tambah User" di menu User Tenant
        $this->buatUser('User Bebas', 'bebas@gmail.com', 'user');
    }

    private function buatUser(string $nama, string $email, string $role, ?int $idTenant = null): User
    {
        $user = User::create([
            'nama' => $nama,
            'email' => $email,
            'password' => Hash::make('12345678'),
            'status' => 'aktif',
            'email_verified_at' => now(),
            'id_tenant' => $idTenant,
        ]);

        $user->assignRole($role);

        return $user;
    }
}