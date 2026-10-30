<?php

namespace App\Http\Controllers;

use App\Models\BarangKeluar;
use App\Models\DataBarang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Support\TenantContext; // TAMBAHKAN INI

class BarangKeluarController extends Controller
{
    /**
     * Role seperti admin (tenant.bypass) boleh melihat data lintas tenant
     */
    private function bisaBypassTenant(): bool
    {
        return Auth::user()?->can('tenant.bypass') ?? false;
    }

    /**
     * Penjaga untuk operasi tulis: user wajib terhubung ke tenant.
     */
    private function tolakJikaTanpaTenant()
    {
        // PERBAIKAN: Cek id_tenant dari user ATAU dari TenantContext (jika admin sedang memilih tenant)
        $idTenant = TenantContext::current()?->id ?? Auth::user()?->id_tenant;

        if (!$idTenant) {
            return redirect()->route('barangkeluar.index')
                ->with('crud_error', 'Akun ini tidak terhubung ke tenant, sehingga tidak dapat mengubah data.');
        }

        return null;
    }

    public function index()
    {
        $user = Auth::user();
        $bypass = $this->bisaBypassTenant();
        
        // PERBAIKAN: Ambil ID tenant aktif (baik dari context admin maupun user biasa)
        $idTenantAktif = TenantContext::current()?->id ?? $user->id_tenant;

        if (!$idTenantAktif && !$bypass) {
            abort(404, 'Tenant tidak ditemukan');
        }

        $barangKeluarQuery = BarangKeluar::with('dataBarang');
        $dataBarangQuery = DataBarang::query();

        // PERBAIKAN: Gunakan $idTenantAktif untuk filter
        if (!$bypass && $idTenantAktif) {
            $barangKeluarQuery->where('id_tenant', $idTenantAktif);
            $dataBarangQuery->where('id_tenant', $idTenantAktif);
        }

        $barangkeluars = $barangKeluarQuery->get();
        $databarangs = $dataBarangQuery->get();

        // PERBAIKAN: Generate ID berdasarkan tenant aktif
        $newId = $idTenantAktif ? $this->generateNoBarangKeluar($idTenantAktif) : null;

        return view('barangkeluar', compact('barangkeluars', 'databarangs', 'newId'));
    }

    // PERBAIKAN: Tambahkan parameter $idTenant
    private function generateNoBarangKeluar($idTenant = null)
    {
        // Jika tidak dikirim dari parameter, coba ambil dari context atau user
        $idTenant = $idTenant ?? TenantContext::current()?->id ?? Auth::user()?->id_tenant;
        
        if (!$idTenant) return null;

        $last = BarangKeluar::where('id_tenant', $idTenant)
            ->orderBy('created_at', 'desc')
            ->first();

        $lastNumber = 0;
        if ($last) {
            $lastNumber = (int) substr($last->no_barangkeluar, 2);
        }

        return 'BK' . str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
    }

    public function store(Request $request)
    {
        if ($tolak = $this->tolakJikaTanpaTenant()) return $tolak;

        $request->validate([
            'tanggal' => 'required|date',
            'id_databarang' => 'required|exists:databarangs,id',
            'jumlah_keluar' => 'required|integer|min:1',
        ]);

        $dataBarang = DataBarang::findOrFail($request->id_databarang);

        if ($request->jumlah_keluar > $dataBarang->stok) {
            return redirect()->back()
                ->with('crud_error', 'Stok tidak mencukupi untuk barang keluar.')
                ->withInput();
        }

        // generateNoBarangKeluar() sekarang otomatis mendeteksi tenant aktif
        $newId = $this->generateNoBarangKeluar();

        BarangKeluar::create([
            'no_barangkeluar' => $newId,
            'tanggal' => $request->tanggal,
            'id_databarang' => $request->id_databarang,
            'jumlah_keluar' => $request->jumlah_keluar,
            // 'id_tenant' => TenantContext::current()?->id ?? Auth::user()?->id_tenant,
            // ^ Hapus komentar di atas JIKA trait HasTenant Anda tidak otomatis mengisi id_tenant
        ]);

        $dataBarang->stok -= $request->jumlah_keluar;
        $dataBarang->save();

        return redirect()->route('barangkeluar.index')->with('crud_success', 'Barang keluar berhasil ditambahkan');
    }

    public function edit($id)
    {
        if ($tolak = $this->tolakJikaTanpaTenant()) return $tolak;

        $barangkeluar = BarangKeluar::with('dataBarang')->findOrFail($id);
        
        // PERBAIKAN: Gunakan tenant aktif
        $idTenantAktif = TenantContext::current()?->id ?? Auth::user()->id_tenant;
        $databarangs = DataBarang::where('id_tenant', $idTenantAktif)->get();

        return view('barangkeluar.edit', compact('barangkeluar', 'databarangs'));
    }

    public function update(Request $request, $id)
    {
        if ($tolak = $this->tolakJikaTanpaTenant()) return $tolak;

        $barangkeluar = BarangKeluar::findOrFail($id);

        $request->validate([
            'tanggal' => 'required|date',
            'id_databarang' => 'required|exists:databarangs,id',
            'jumlah_keluar' => 'required|integer|min:1',
        ]);

        $oldJumlahKeluar = $barangkeluar->jumlah_keluar;
        $oldIdDataBarang = $barangkeluar->id_databarang;

        if ($oldIdDataBarang == $request->id_databarang) {
            $selisih = $request->jumlah_keluar - $oldJumlahKeluar;
            $dataBarang = DataBarang::find($request->id_databarang);

            if ($selisih > 0 && $selisih > $dataBarang->stok) {
                return redirect()->back()->with('crud_error', 'Stok tidak mencukupi untuk perubahan jumlah keluar.')->withInput();
            }

            $barangkeluar->update([
                'tanggal' => $request->tanggal,
                'id_databarang' => $request->id_databarang,
                'jumlah_keluar' => $request->jumlah_keluar,
            ]);

            $dataBarang->stok -= $selisih;
            $dataBarang->save();
        } else {
            $oldBarang = DataBarang::find($oldIdDataBarang);
            $newBarang = DataBarang::find($request->id_databarang);

            if ($request->jumlah_keluar > $newBarang->stok) {
                return redirect()->back()->with('crud_error', 'Stok tidak mencukupi untuk barang baru yang dipilih.')->withInput();
            }

            $barangkeluar->update([
                'tanggal' => $request->tanggal,
                'id_databarang' => $request->id_databarang,
                'jumlah_keluar' => $request->jumlah_keluar,
            ]);

            $oldBarang->stok += $oldJumlahKeluar;
            $oldBarang->save();

            $newBarang->stok -= $request->jumlah_keluar;
            $newBarang->save();
        }

        return redirect()->route('barangkeluar.index')->with('crud_success', 'Barang keluar berhasil diupdate');
    }

    public function destroy($id)
    {
        if ($tolak = $this->tolakJikaTanpaTenant()) return $tolak;

        $barangkeluar = BarangKeluar::findOrFail($id);

        $dataBarang = DataBarang::find($barangkeluar->id_databarang);
        if ($dataBarang) {
            $dataBarang->stok += $barangkeluar->jumlah_keluar;
            $dataBarang->save();
        }

        $barangkeluar->delete();

        return redirect()->route('barangkeluar.index')->with('crud_success', 'Barang keluar berhasil dihapus');
    }
}