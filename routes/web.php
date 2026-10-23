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
use App\Http\Controllers\RoleController;
use App\Http\Controllers\RolePermissionController;

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

    // ===== Administrasi user (admin) =====
    Route::get('/dashboard', [UserController::class, 'index'])->name('dashboard')->middleware('permission:user.view');
    Route::patch('/users/{id}/update-status', [UserController::class, 'updateStatus'])->name('user.updateStatus')->middleware('permission:user.update');
    Route::post('/users', [UserController::class, 'store'])->name('users.store')->middleware('permission:user.create');
    Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('users.destroy')->middleware('permission:user.delete');

    // ===== Persetujuan tenant (admin) =====
    Route::middleware('permission:tenant.approve')->group(function () {
        Route::get('/tenantuser', [TenantUserController::class, 'index'])->name('tenantuser.index');
        Route::post('/tenantuser/{tenant}/approve', [TenantUserController::class, 'approve'])->name('tenantuser.approve');
        Route::post('/tenantuser/{tenant}/reject', [TenantUserController::class, 'reject'])->name('tenantuser.reject');
    });

    // ===== Manajemen role dan permission =====
    Route::controller(RoleController::class)->prefix('roles')->name('roles.')->group(function () {
        Route::get('/', 'index')->name('index')->middleware('permission:role.view');
        Route::get('/create', 'create')->name('create')->middleware('permission:role.create');
        Route::post('/', 'store')->name('store')->middleware('permission:role.create');
        Route::get('/{role}/edit', 'edit')->name('edit')->middleware('permission:role.update');
        Route::put('/{role}', 'update')->name('update')->middleware('permission:role.update');
        Route::delete('/{role}', 'destroy')->name('destroy')->middleware('permission:role.delete');

        Route::get('/{role}/permissions', [RolePermissionController::class, 'edit'])
            ->name('permissions.edit')->middleware('permission:role.update');
        Route::put('/{role}/permissions', [RolePermissionController::class, 'update'])
            ->name('permissions.update')->middleware('permission:role.update');
    });

    // ===== Tenant milik pengelola =====
    Route::get('/pengelola', [PengelolaController::class, 'index'])->name('pengelola')->middleware('permission:tenant.view');
    Route::post('/pengelola/store', [PengelolaController::class, 'store'])->name('pengelola.store')->middleware('permission:tenant.create');
    Route::get('/pengelola/{tenant}/edit', [PengelolaController::class, 'edit'])->name('pengelola.edit')->middleware('permission:tenant.update');
    Route::put('/pengelola/{tenant}/update', [PengelolaController::class, 'update'])->name('pengelola.update')->middleware('permission:tenant.update');
    Route::delete('/pengelola/{tenant}/delete', [PengelolaController::class, 'destroy'])->name('pengelola.destroy')->middleware('permission:tenant.delete');

    // ===== Anggota tenant =====
    Route::get('/tenantmembers', [TenantMemberController::class, 'index'])->name('tenantmembers.index')->middleware('permission:member.view');
    Route::post('/tenantmembers', [TenantMemberController::class, 'store'])->name('tenantmembers.store')->middleware('permission:member.create');
    Route::delete('/tenantmembers/{user}', [TenantMemberController::class, 'destroy'])->name('tenantmembers.destroy')->middleware('permission:member.delete');

    // ===== Laporan =====
    Route::middleware('permission:laporan.view')->group(function () {
        Route::get('/laporanbarangmasuk', [PengelolaController::class, 'indexLaporanBarangMasuk'])->name('laporanbarangmasuk');
        Route::get('/laporanbarangkeluar', [PengelolaController::class, 'indexLaporanBarangKeluar'])->name('laporanbarangkeluar');
        Route::get('/laporanstok', [PengelolaController::class, 'indexLaporanStok'])->name('laporanstok');
    });

    Route::middleware('permission:laporan.export')->group(function () {
        Route::get('/laporanbarangmasuk/pdf', [PengelolaController::class, 'cetakLaporanBarangMasuk'])->name('laporanbarangmasuk.pdf');
        Route::get('/laporanbarangkeluar/pdf', [PengelolaController::class, 'cetakLaporanBarangKeluar'])->name('laporanbarangkeluar.pdf');
    });

    // ===== Dashboard user =====
    Route::get('/user', [IndexController::class, 'index'])->name('user')->middleware('permission:databarang.view');
    Route::get('/namatenant', [PengelolaController::class, 'showNamatenant'])->name('namatenant')->middleware('permission:databarang.view');

    // ===== Master data =====
    Route::controller(SatuanController::class)->prefix('satuan')->name('satuan.')->group(function () {
        Route::get('/', 'index')->name('index')->middleware('permission:satuan.view');
        Route::post('/', 'store')->name('store')->middleware('permission:satuan.create');
        Route::get('/{id}/edit', 'edit')->name('edit')->middleware('permission:satuan.update');
        Route::put('/{id}', 'update')->name('update')->middleware('permission:satuan.update');
        Route::delete('/{id}', 'destroy')->name('destroy')->middleware('permission:satuan.delete');
    });

    Route::controller(DatabarangController::class)->prefix('databarang')->name('databarangs.')->group(function () {
        Route::get('/', 'index')->name('index')->middleware('permission:databarang.view');
        Route::post('/', 'store')->name('store')->middleware('permission:databarang.create');
        Route::get('{databarang}/edit', 'edit')->name('edit')->middleware('permission:databarang.update');
        Route::put('{databarang}', 'update')->name('update')->middleware('permission:databarang.update');
        Route::delete('{databarang}', 'destroy')->name('destroy')->middleware('permission:databarang.delete');
    });

    Route::controller(JenisBarangController::class)->prefix('jenisbarang')->name('jenisbarangs.')->group(function () {
        Route::get('/', 'index')->name('index')->middleware('permission:jenisbarang.view');
        Route::post('/', 'store')->name('store')->middleware('permission:jenisbarang.create');
        Route::get('/{id}/edit', 'edit')->name('edit')->middleware('permission:jenisbarang.update');
        Route::put('/{id}', 'update')->name('update')->middleware('permission:jenisbarang.update');
        Route::delete('/{id}', 'destroy')->name('destroy')->middleware('permission:jenisbarang.delete');
    });

    // ===== Transaksi =====
    Route::controller(BarangMasukController::class)->prefix('barangmasuk')->name('barangmasuk.')->group(function () {
        Route::get('/', 'index')->name('index')->middleware('permission:barangmasuk.view');
        Route::post('/', 'store')->name('store')->middleware('permission:barangmasuk.create');
        Route::get('{barangMasuk}/edit', 'edit')->name('edit')->middleware('permission:barangmasuk.update');
        Route::put('{barangMasuk}', 'update')->name('update')->middleware('permission:barangmasuk.update');
        Route::delete('{barangMasuk}', 'destroy')->name('destroy')->middleware('permission:barangmasuk.delete');
    });

    Route::controller(BarangKeluarController::class)->prefix('barangkeluar')->name('barangkeluar.')->group(function () {
        Route::get('/', 'index')->name('index')->middleware('permission:barangkeluar.view');
        Route::post('/', 'store')->name('store')->middleware('permission:barangkeluar.create');
        Route::get('/{id}/edit', 'edit')->name('edit')->middleware('permission:barangkeluar.update');
        Route::put('/{id}', 'update')->name('update')->middleware('permission:barangkeluar.update');
        Route::delete('/{id}', 'destroy')->name('destroy')->middleware('permission:barangkeluar.delete');
    });
});
