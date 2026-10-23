<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TenantMemberController extends Controller
{
    private function tenantMilik()
    {
        $tenant = Auth::user()->tenant;
        abort_unless($tenant, 403, 'Akun Anda belum memiliki tenant.');

        return $tenant;
    }

    public function index()
    {
        $tenant = $this->tenantMilik();

        // User yang sudah masuk tenant ini
        $members = $tenant->users()->role('user')->orderBy('nama')->get();

        // Kandidat: role user, aktif, dan belum masuk tenant mana pun
        $candidates = User::role('user')
            ->whereNull('id_tenant')
            ->where('status', 'aktif')
            ->orderBy('nama')
            ->get();

        return view('tenant.tenantmembers', compact('tenant', 'members', 'candidates'));
    }

    public function store(Request $request)
    {
        $tenant = $this->tenantMilik();

        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        // Satu query memaksa semua aturan: role user, aktif, belum punya tenant
        $user = User::role('user')
            ->whereNull('id_tenant')
            ->where('status', 'aktif')
            ->find($request->user_id);

        if (!$user) {
            return redirect()->route('tenantmembers.index')
                ->with('crud_error', 'User tidak tersedia (sudah terdaftar di tenant lain atau tidak aktif).');
        }

        $user->update(['id_tenant' => $tenant->id]);

        return redirect()->route('tenantmembers.index')
            ->with('crud_success', 'User berhasil ditambahkan ke tenant.');
    }

    public function destroy(User $user)
    {
        $tenant = $this->tenantMilik();

        // Isolasi: hanya boleh mengeluarkan user milik tenant sendiri
        abort_unless($user->id_tenant === $tenant->id, 403);

        $user->update(['id_tenant' => null]);

        return redirect()->route('tenantmembers.index')
            ->with('crud_success', 'User dikeluarkan dari tenant.');
    }
}