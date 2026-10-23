<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\IndexController;
use App\Http\Controllers\PengelolaController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TenantUserController;
use App\Http\Controllers\TenantMemberController;
use App\Http\Controllers\SatuanController;
use App\Http\Controllers\DatabarangController;
use App\Http\Controllers\JenisBarangController;
use App\Http\Controllers\BarangMasukController;
use App\Http\Controllers\BarangKeluarController;

// Route Home
Route::view('/', 'home')->name('home');

// Route Login dan Register (hanya untuk guest)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::get('/verify-otp', [AuthController::class, 'showOtpForm'])->name('verify.otp.form');
Route::post('/verify-otp', [AuthController::class, 'verifyOtp'])->name('verify.otp');
Route::post('/resend-otp', [AuthController::class, 'resendOtpSession'])->name('resend.otp.session');

// Route untuk Google OAuth
Route::get('/auth/google', [AuthController::class, 'redirectToGoogle'])->name('google.login');
Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback']);

// Route Logout & Routes yang memerlukan autentikasi
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::post('/change-password', [UserController::class, 'changePassword'])->name('password.change');

    // Manajemen Pengguna (hanya untuk admin)
    Route::middleware('role:admin')->group(function () {
        Route::get('/dashboard', [UserController::class, 'index'])->name('dashboard');
        Route::patch('/users/{id}/update-status', [UserController::class, 'updateStatus'])->name('user.updateStatus');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('users.destroy');

        // Manajemen tenant (aktifkan / nonaktifkan oleh admin)
        Route::get('/tenantuser', [TenantUserController::class, 'index'])->name('tenantuser.index');
        Route::post('/tenantuser/{tenant}/approve', [TenantUserController::class, 'approve'])->name('tenantuser.approve');
        Route::post('/tenantuser/{tenant}/reject', [TenantUserController::class, 'reject'])->name('tenantuser.reject');
    });

    // Dashboard Pengelola
    Route::middleware('role:pengelola')->group(function () {
        Route::get('/pengelola', [PengelolaController::class, 'index'])->name('pengelola');
        Route::post('/pengelola/store', [PengelolaController::class, 'store'])->name('pengelola.store');
        Route::get('/pengelola/{tenant}/edit', [PengelolaController::class, 'edit'])->name('pengelola.edit');
        Route::put('/pengelola/{tenant}/update', [PengelolaController::class, 'update'])->name('pengelola.update');
        Route::delete('/pengelola/{tenant}/delete', [PengelolaController::class, 'destroy'])->name('pengelola.destroy');

        Route::get('/tenantmembers', [TenantMemberController::class, 'index'])->name('tenantmembers.index');
        Route::post('/tenantmembers', [TenantMemberController::class, 'store'])->name('tenantmembers.store');
        Route::delete('/tenantmembers/{user}', [TenantMemberController::class, 'destroy'])->name('tenantmembers.destroy');

        Route::get('/laporanbarangmasuk/pdf', [PengelolaController::class, 'cetakLaporanBarangMasuk'])->name('laporanbarangmasuk.pdf');
        Route::get('/laporanbarangkeluar/pdf', [PengelolaController::class, 'cetakLaporanBarangKeluar'])->name('laporanbarangkeluar.pdf');

        Route::get('/laporanbarangmasuk', [PengelolaController::class, 'indexLaporanBarangMasuk'])->name('laporanbarangmasuk');
        Route::get('/laporanbarangkeluar', [PengelolaController::class, 'indexLaporanBarangKeluar'])->name('laporanbarangkeluar');
        Route::get('/laporanstok', [PengelolaController::class, 'indexLaporanStok'])->name('laporanstok');
    });

    // Dashboard User
    Route::middleware('role:user')->group(function () {
        Route::get('/user', [IndexController::class, 'index'])->name('user');
        Route::get('/namatenant', [PengelolaController::class, 'showNamatenant'])->name('namatenant');

        Route::get('/satuan', [SatuanController::class, 'index'])->name('satuan.index');
        Route::post('/satuan', [SatuanController::class, 'store'])->name('satuan.store');
        Route::get('/satuan/{id}/edit', [SatuanController::class, 'edit'])->name('satuan.edit');
        Route::put('/satuan/{id}', [SatuanController::class, 'update'])->name('satuan.update');
        Route::delete('/satuan/{id}', [SatuanController::class, 'destroy'])->name('satuan.destroy');

        Route::prefix('databarang')->name('databarangs.')->group(function () {
            Route::get('/', [DatabarangController::class, 'index'])->name('index');
            Route::post('/', [DatabarangController::class, 'store'])->name('store');
            Route::get('{databarang}/edit', [DatabarangController::class, 'edit'])->name('edit');
            Route::put('{databarang}', [DatabarangController::class, 'update'])->name('update');
            Route::delete('{databarang}', [DatabarangController::class, 'destroy'])->name('destroy');
        });

        Route::get('/jenisbarang', [JenisBarangController::class, 'index'])->name('jenisbarangs.index');
        Route::post('/jenisbarang', [JenisBarangController::class, 'store'])->name('jenisbarangs.store');
        Route::get('/jenisbarang/{id}/edit', [JenisBarangController::class, 'edit'])->name('jenisbarangs.edit');
        Route::put('/jenisbarang/{id}', [JenisBarangController::class, 'update'])->name('jenisbarangs.update');
        Route::delete('/jenisbarang/{id}', [JenisBarangController::class, 'destroy'])->name('jenisbarangs.destroy');

        Route::prefix('barangmasuk')->group(function () {
            Route::get('/', [BarangMasukController::class, 'index'])->name('barangmasuk.index');
            Route::post('/', [BarangMasukController::class, 'store'])->name('barangmasuk.store');
            Route::get('{barangMasuk}/edit', [BarangMasukController::class, 'edit'])->name('barangmasuk.edit');
            Route::put('{barangMasuk}', [BarangMasukController::class, 'update'])->name('barangmasuk.update');
            Route::delete('{barangMasuk}', [BarangMasukController::class, 'destroy'])->name('barangmasuk.destroy');
        });

        Route::get('/barangkeluar', [BarangKeluarController::class, 'index'])->name('barangkeluar.index');
        Route::post('/barangkeluar', [BarangKeluarController::class, 'store'])->name('barangkeluar.store');
        Route::get('/barangkeluar/{id}/edit', [BarangKeluarController::class, 'edit'])->name('barangkeluar.edit');
        Route::put('/barangkeluar/{id}', [BarangKeluarController::class, 'update'])->name('barangkeluar.update');
        Route::delete('/barangkeluar/{id}', [BarangKeluarController::class, 'destroy'])->name('barangkeluar.destroy');
    });
});