<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Support\TenantContext;
use Illuminate\Http\Request;

class TenantMemberController extends Controller
{
    private function tenantAktif()
    {
        $tenant = TenantContext::current();

        abort_unless(
            $tenant,
            403,
            TenantContext::bisaBypass()
                ? 'Pilih tenant terlebih dahulu di sidebar.'
                : 'Akun Anda belum memiliki tenant.'
        );

        return $tenant;
    }

    public function index()
    {
        $tenant = TenantContext::current();

        // Admin tanpa tenant terpilih: lihat anggota semua tenant (tanpa aksi tulis)
        if (!$tenant && TenantContext::bisaBypass()) {
            $members = User::role('user')
                ->whereNotNull('id_tenant')
                ->with('tenant')
                ->orderBy('nama')
                ->get();
            $candidates = collect();

            return view('tenant.tenantmembers', compact('tenant', 'members', 'candidates'));
        }

        $tenant = $this->tenantAktif();

        $members = $tenant->users()->role('user')->orderBy('nama')->get();

        $candidates = User::role('user')
            ->whereNull('id_tenant')
            ->where('status', 'aktif')
            ->orderBy('nama')
            ->get()
            ->map(fn($u) => [
                'id'         => $u->id,
                'nama'       => $u->nama,
                'email'      => $u->email,
                'avatar_url' => $u->avatar_url,   // ← accessor dipanggil manual
            ]);

        return view('tenant.tenantmembers', compact('tenant', 'members', 'candidates'));
    }

    public function store(Request $request)
    {
        $tenant = $this->tenantAktif();

        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

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
        $tenant = $this->tenantAktif();

        abort_unless($user->id_tenant === $tenant->id, 403);

        $user->update(['id_tenant' => null]);

        return redirect()->route('tenantmembers.index')
            ->with('crud_success', 'User dikeluarkan dari tenant.');
    }
}
