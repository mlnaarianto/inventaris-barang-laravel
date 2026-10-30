<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tenant;
use App\Support\TenantContext; // <-- Tambahkan ini

class TenantUserController extends Controller
{
    public function index()
    {
        // 1. Ambil tenant yang sedang dipilih di sidebar
        $tenantAktif = TenantContext::current();

        // 2. Siapkan query dengan eager load users (untuk avatar)
        $query = Tenant::with('users');

        // 3. Jika admin sedang memfilter 1 tenant, batasi query ke tenant tersebut
        if ($tenantAktif) {
            $query->where('id', $tenantAktif->id);
        }

        // 4. Eksekusi query
        $tenants = $query->get();
        
        return view('tenant.tenantuser', compact('tenants'));
    }

    // Menyetujui tenant
    public function approve(Tenant $tenant)
    {
        $tenant->update(['status' => 'aktif']); // Sesuaikan dengan enum baru
        return redirect()->route('tenantuser.index')->with('crud_success', 'Tenant berhasil diaktifkan.');
    }

    public function reject(Tenant $tenant)
    {
        $tenant->update(['status' => 'nonaktif']); // Sesuaikan dengan enum baru
        return redirect()->route('tenantuser.index')->with('crud_success', 'Tenant berhasil dinonaktifkan.');
    }
}