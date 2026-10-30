<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tenant;

class TenantUserController extends Controller
{
    // app/Http/Controllers/TenantUserController.php
    public function index()
    {
        $tenants = Tenant::with('users')->get();   // ← eager load users
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
