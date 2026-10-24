<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Tenant;
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

    // Catatan: changePassword() sudah dipindah ke ProfileController.

    /**
     * Display the dashboard with user data.
     */
    public function index()
    {
        $pengelolaCount = User::role('pengelola')->count();
        $userCount = User::role('user')->count();

        // Semua user selain admin
        $users = User::with('roles')->withoutRole('admin')->get();

        $tenantAktif = Tenant::where('status', 'aktif')->count();
        $tenantNonaktif = Tenant::where('status', 'nonaktif')->count();

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

        $user = User::create([
            'nama' => $request->nama,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'status' => $request->status,
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