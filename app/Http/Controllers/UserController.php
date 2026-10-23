<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    public function changePassword(Request $request)
    {
        try {
            $request->validate([
                'current_password' => 'required',
                'new_password' => [
                    'required',
                    'string',
                    'min:8',
                    'confirmed',
                    'regex:/^(?=.*[A-Z])(?=.*\d).{8,}$/'
                ],
            ], [
                'new_password.regex' => 'Password baru harus memiliki minimal 8 karakter, mengandung setidaknya satu huruf kapital dan satu angka.',
                'new_password.confirmed' => 'Konfirmasi password baru tidak cocok.',
            ]);
        } catch (ValidationException $e) {
            $firstError = collect($e->errors())->flatten()->first();
            return back()->with('crud_error', $firstError)->withInput();
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->with('crud_error', 'Password lama tidak sesuai.')->withInput();
        }

        if (Hash::check($request->new_password, $user->password)) {
            return back()->with('crud_error', 'Password baru tidak boleh sama dengan password lama.')->withInput();
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return back()->with('crud_success', 'Password berhasil diubah.');
    }

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

        return view('dashboard', compact('pengelolaCount', 'userCount', 'users', 'tenantAktif', 'tenantNonaktif'));
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
            'role' => ['required', Rule::in(['pengelola', 'user'])], // admin tidak boleh dibuat dari form
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