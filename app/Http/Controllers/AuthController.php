<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;

use Carbon\Carbon;

class AuthController extends Controller
{
    // ================= LOGIN MANUAL =================

    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if (!$user || $user->status !== 'aktif') {
            return back()->withErrors(['email' => 'Akun belum diverifikasi atau tidak aktif.']);
        }

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            return match ($user->role) {
                'admin' => redirect()->route('dashboard'),
                'pengelola' => redirect()->route('pengelola'),
                'user' => redirect()->route('user'),
                default => redirect()->route('login')->withErrors(['email' => 'Role tidak valid.']),
            };
        }

        return back()->withErrors(['email' => 'Email atau password salah.']);
    }

    // ================= REGISTER MANUAL + OTP =================

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => [
                'required',
                'confirmed',
                'min:8',
                'regex:/^[A-Z][A-Za-z0-9]{7,}$/',
                'regex:/[0-9]/',
            ],
            'role' => ['required', Rule::in(['pengelola', 'user'])],
        ]);

        $otp = rand(100000, 999999);

        // 🔥 Simpan ke session, bukan database
        session([
            'register_data' => [
                'nama' => $data['nama'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'role' => $data['role'],
                'otp' => $otp,
                'otp_expires_at' => now()->addMinutes(5),
            ]
        ]);

        Mail::raw("Kode OTP Anda adalah: $otp (berlaku 5 menit)", function ($message) use ($data) {
            $message->to($data['email'])
                ->subject('Verifikasi Akun - OTP');
        });

        return redirect()->route('verify.otp.form');
    }


    // ================= VERIFIKASI OTP =================

    public function showOtpForm()
    {
        if (!session()->has('register_data')) {
            return redirect()->route('register');
        }

        return view('auth.verify-otp');
    }


    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|digits:6'
        ]);

        if (!session()->has('register_data')) {
            return redirect()->route('register');
        }

        $data = session('register_data');

        if ($request->otp != $data['otp']) {
            return back()->withErrors(['otp' => 'OTP salah']);
        }

        if (now()->gt($data['otp_expires_at'])) {
            return back()->withErrors(['otp' => 'OTP sudah kadaluarsa']);
        }

        // 🔥 Baru buat user setelah OTP benar
        $user = User::create([
            'nama' => $data['nama'],
            'email' => $data['email'],
            'password' => $data['password'],
            'role' => $data['role'],
            'status' => 'aktif',
            'email_verified_at' => now(),
        ]);

        Role::create([
            'id_user' => $user->id,
            'role' => $data['role'],
        ]);

        // Hapus session
        session()->forget('register_data');

        Auth::login($user);

        return match ($user->role) {
            'admin' => redirect()->route('dashboard'),
            'pengelola' => redirect()->route('pengelola'),
            'user' => redirect()->route('user'),
            default => redirect()->route('login'),
        };
    }



    public function resendOtpSession()
    {
        if (!session()->has('register_data')) {
            return redirect()->route('register');
        }

        $data = session('register_data');

        $otp = rand(100000, 999999);

        $data['otp'] = $otp;
        $data['otp_expires_at'] = now()->addMinutes(5);

        session(['register_data' => $data]);

        Mail::raw("Kode OTP baru Anda adalah: $otp (berlaku 5 menit)", function ($message) use ($data) {
            $message->to($data['email'])
                ->subject('Resend OTP');
        });

        return back()->with('success', 'OTP baru telah dikirim.');
    }



    // ================= LOGIN GOOGLE =================

    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

 public function handleGoogleCallback()
{
    try {
        $googleUser = Socialite::driver('google')->stateless()->user();

        $user = User::where('email', $googleUser->getEmail())->first();

        $avatarPath = null;

        try {
            $avatarUrl = $googleUser->getAvatar();

            if ($avatarUrl) {
                $avatarContents = Http::get($avatarUrl)->body();

                $filename = 'avatars/' . Str::uuid() . '.jpg';

                // 🔥 hapus avatar lama kalau ada
                if ($user && $user->avatar && Storage::disk('public')->exists($user->avatar)) {
                    Storage::disk('public')->delete($user->avatar);
                }

                // simpan avatar baru
                Storage::disk('public')->put($filename, $avatarContents);

                $avatarPath = $filename;
            }
        } catch (\Exception $e) {
            $avatarPath = null;
        }

        // ================= CREATE USER =================
        if (!$user) {
            $user = User::create([
                'nama' => $googleUser->getName(),
                'email' => $googleUser->getEmail(),
                'email_verified_at' => now(),
                'role' => 'user',
                'status' => 'aktif',
                'google_id' => $googleUser->getId(),
                'avatar' => $avatarPath,
            ]);

            Role::create([
                'id_user' => $user->id,
                'role' => 'user',
            ]);
        }

        // ================= UPDATE USER =================
        else {
            $updateData = [
                'google_id' => $googleUser->getId(),
                'email_verified_at' => now(),
            ];

            if ($avatarPath) {
                $updateData['avatar'] = $avatarPath;
            }

            $user->update($updateData);
        }

        // ================= CEK STATUS =================
        if ($user->status !== 'aktif') {
            return redirect()->route('login')
                ->withErrors(['email' => 'Akun belum aktif.']);
        }

        // ================= LOGIN =================
        Auth::login($user);

        return match ($user->role) {
            'admin' => redirect()->route('dashboard'),
            'pengelola' => redirect()->route('pengelola'),
            'user' => redirect()->route('user'),
            default => redirect()->route('login')->withErrors(['email' => 'Role tidak valid.']),
        };

    } catch (\Exception $e) {
        return redirect()->route('login')
            ->withErrors(['email' => 'Login Google gagal.']);
    }
}

    // ================= LOGOUT =================

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
