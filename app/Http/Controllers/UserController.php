<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Tenant;
use App\Support\TenantContext; // <-- Tambahkan ini
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Role yang boleh dipilih saat membuat user dari form.
     * Admin sengaja tidak dimasukkan.
     */
    private const ROLE_FORM = ['pengelola', 'user'];

    /**
     * Display the dashboard with user data.
     */
    public function index()
    {
        // 1. Ambil tenant yang sedang dipilih di sidebar
        $tenant = TenantContext::current();

        // 2. Base Query untuk User (selalu kecualikan admin)
        $userQuery = User::withoutRole('admin');

        // Jika admin sedang memfilter 1 tenant, tambahkan kondisi where
        if ($tenant) {
            $userQuery->where('id_tenant', $tenant->id);
        }

        // 3. Hitung statistik user berdasarkan filter tenant
        $pengelolaCount = (clone $userQuery)->role('pengelola')->count();
        $userCount = (clone $userQuery)->role('user')->count();

        // 4. Ambil data user beserta role-nya
        $users = (clone $userQuery)->with('roles')->get();

        // 5. Statistik Tenant
        if ($tenant) {
            // Jika sedang melihat 1 tenant, statistik menyesuaikan tenant tersebut
            $tenantAktif = $tenant->status === 'aktif' ? 1 : 0;
            
            // Catatan: Di kodingan asli Anda menggunakan 'nonaktif'.
            // Jika di database Anda 'tidak_aktif', silakan sesuaikan string di bawah.
            $tenantNonaktif = $tenant->status === 'nonaktif' ? 1 : 0; 
        } else {
            // Jika tidak memilih tenant (Mode lihat Semua Tenant)
            $tenantAktif = Tenant::where('status', 'aktif')->count();
            $tenantNonaktif = Tenant::where('status', 'nonaktif')->count();
        }

        // Opsi role untuk dropdown di modal Tambah User
        $roleOptions = self::ROLE_FORM;

        return view('dashboard', compact(
            'pengelolaCount',
            'userCount',
            'users',
            'tenantAktif',
            'tenantNonaktif',
            'roleOptions'
        ));
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'role' => ['required', Rule::in(self::ROLE_FORM)],
            'status' => ['required', Rule::in(['aktif', 'tidak_aktif', 'pending'])],
        ]);

        // Cek apakah admin sedang memilih tenant tertentu di sidebar
        $tenantAktif = TenantContext::current();

        $user = User::create([
            'nama' => $request->nama,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'status' => $request->status,
            // Jika admin sedang di konteks tenant tertentu, otomatis daftarkan user ke tenant itu
            'id_tenant' => $tenantAktif ? $tenantAktif->id : null, 
        ]);

        $user->assignRole($request->role);

        return redirect()->route('dashboard')->with('crud_success', 'User berhasil ditambahkan!');
    }

    /**
     * Update the status of a user.
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => ['required', Rule::in(['aktif', 'tidak_aktif', 'pending'])],
        ]);

        $user = User::findOrFail($id);
        $user->update(['status' => $request->status]);

        return redirect()->route('dashboard')->with('crud_success', 'Status user berhasil diperbarui!');
    }

    public function destroy($id)
    {
        // Relasi role ikut dilepas otomatis oleh trait HasRoles saat user dihapus
        User::findOrFail($id)->delete();

        return redirect()->route('dashboard')->with('crud_success', 'User berhasil dihapus!');
    }
}