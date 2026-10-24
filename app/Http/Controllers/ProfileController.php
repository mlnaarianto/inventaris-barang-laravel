<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /**
     * Halaman profil (dipakai semua role).
     */
    public function edit()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        return view('profile', [
            'user' => $user,
            // User yang hanya masuk lewat Google tidak punya password
            'hasPassword' => !is_null($user->password),
            'backUrl' => $this->homeUrl($user),
        ]);
    }

    /**
     * Ubah data diri (nama). Email tidak bisa diubah karena dipakai login dan OTP.
     */
    public function update(Request $request)
    {
        $data = $request->validate([
            'nama' => 'required|string|max:255',
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $user->update(['nama' => $data['nama']]);

        return back()->with('crud_success', 'Profil berhasil diperbarui.');
    }

    /**
     * Unggah / ganti foto profil.
     */
    public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatar' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ], [
            'avatar.required' => 'Pilih foto terlebih dahulu.',
            'avatar.image' => 'File harus berupa gambar.',
            'avatar.mimes' => 'Format foto harus JPG, PNG, atau WebP.',
            'avatar.max' => 'Ukuran foto maksimal 2 MB.',
            'avatar.uploaded' => 'Foto gagal diunggah. Pastikan ukurannya maksimal 2 MB.',
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        $newPath = $request->file('avatar')->store('avatars', 'public');

        // Hapus foto lama setelah yang baru berhasil tersimpan
        $this->deleteAvatarFile($user->avatar);

        $user->update(['avatar' => $newPath]);

        return back()->with('crud_success', 'Foto profil berhasil diperbarui.');
    }

    /**
     * Hapus foto profil.
     */
    public function destroyAvatar()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $this->deleteAvatarFile($user->avatar);
        $user->update(['avatar' => null]);

        return back()->with('crud_success', 'Foto profil berhasil dihapus.');
    }

    /**
     * Ganti password. Hanya untuk akun yang punya password.
     */
    public function changePassword(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (is_null($user->password)) {
            return back()->with('crud_error', 'Akun Anda masuk lewat Google dan tidak memiliki password.');
        }

        $request->validate([
            'current_password' => 'required',
            'new_password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
                // Aturan sama dengan halaman register:
                // diawali huruf kapital, hanya huruf & angka, minimal ada satu angka.
                'regex:/^[A-Z][A-Za-z0-9]{7,}$/',
                'regex:/[0-9]/',
            ],
        ], [
            'current_password.required' => 'Password saat ini wajib diisi.',
            'new_password.required' => 'Password baru wajib diisi.',
            'new_password.min' => 'Password baru minimal 8 karakter.',
            'new_password.regex' => 'Password baru harus minimal 8 karakter, diawali huruf kapital, hanya berisi huruf dan angka, serta mengandung minimal satu angka.',
            'new_password.confirmed' => 'Konfirmasi password baru tidak cocok.',
        ]);

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Password lama tidak sesuai.']);
        }

        if (Hash::check($request->new_password, $user->password)) {
            return back()->withErrors(['new_password' => 'Password baru tidak boleh sama dengan password lama.']);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return back()->with('crud_success', 'Password berhasil diubah.');
    }

    // ================= HELPER =================

    private function deleteAvatarFile(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }

    /**
     * Tujuan tombol "Kembali" sesuai role, urutannya sama dengan AuthController::redirectByRole().
     */
    private function homeUrl($user): string
    {
        if ($user->can('user.view')) {
            return route('dashboard');
        }

        if ($user->can('tenant.view')) {
            return route('pengelola');
        }

        return route('user');
    }
}