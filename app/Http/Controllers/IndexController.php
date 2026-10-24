<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Models\DataBarang;
use App\Models\BarangMasuk;
use App\Models\BarangKeluar;
use App\Models\Satuan;
use Illuminate\Support\Facades\Auth;

class IndexController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        

        // Tenant yang didaftarkan dengan email user, atau tertaut lewat users.id_tenant
        $tenants = Tenant::where('email', $user->email)
            ->when($user->id_tenant, fn($q) => $q->orWhere('id', $user->id_tenant))
            ->get();

        $jumlahBarang = DataBarang::count();
        $jumlahBarangMasuk = BarangMasuk::sum('jumlah_masuk');
        $jumlahBarangKeluar = BarangKeluar::sum('jumlah_keluar');
        $jumlahSatuan = Satuan::count();

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