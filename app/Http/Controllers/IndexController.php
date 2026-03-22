<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Models\DataBarang;
use App\Models\BarangMasuk;
use App\Models\BarangKeluar;
use App\Models\Satuan;

use Illuminate\Support\Facades\Auth;

use Illuminate\Http\Request;

class IndexController extends Controller
{
 

public function index()
{
    $user = Auth::user();

    $directTenants = Tenant::where('email', $user->email)->get();

    $relatedTenants = Tenant::whereHas('tenantRoleUsers', function ($query) use ($user) {
        $query->whereHas('role', function ($subQuery) use ($user) {
            $subQuery->where('id_user', $user->id);
        });
    })->get();

    $tenants = $directTenants->merge($relatedTenants)->unique('id');

    // Hitung langsung tanpa load data
    $jumlahBarang = DataBarang::count();
    $jumlahBarangMasuk = BarangMasuk::sum('jumlah_masuk'); // asumsi ada kolom 'jumlah'
    $jumlahBarangKeluar = BarangKeluar::sum('jumlah_keluar'); // asumsi ada kolom 'jumlah'
    $jumlahSatuan = Satuan::count();

    // Ambil data barang untuk ditampilkan di tabel
    $databarangs = DataBarang::with(['satuan', 'jenisbarang'])->get();

    return view('index', compact(
        'tenants',
        'databarangs',
        'jumlahBarang',
        'jumlahBarangMasuk',
        'jumlahBarangKeluar',
        'jumlahSatuan'
    ));
}

    
}
