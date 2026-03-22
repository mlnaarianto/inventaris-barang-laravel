<?php

namespace App\Http\Controllers;

use App\Models\BarangKeluar;
use App\Models\DataBarang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BarangKeluarController extends Controller
{
    public function index()
    {
        $barangkeluars = BarangKeluar::with('dataBarang')->get();
        $databarangs = DataBarang::all(); // diasumsikan juga pakai tenant filtering

        // Generate ID otomatis untuk no_barangkeluar
        $newId = $this->generateNoBarangKeluar();

        return view('barangkeluar', compact('barangkeluars', 'databarangs', 'newId'));
    }

    private function generateNoBarangKeluar()
    {
        $user = Auth::user();
        if (!$user) return null;

        // Ambil id_tenant dari relasi user
        $tenantId = $this->getTenantIdFromUser($user->id);

        $last = BarangKeluar::where('id_tenant', $tenantId)
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
        $request->validate([
            'tanggal' => 'required|date',
            'id_databarang' => 'required|exists:databarangs,id',
            'jumlah_keluar' => 'required|integer|min:1',
        ]);

        $dataBarang = Databarang::findOrFail($request->id_databarang);

        if ($request->jumlah_keluar > $dataBarang->stok) {
            return redirect()->back()
                ->with('crud_error', 'Stok tidak mencukupi untuk barang keluar.')
                ->withInput();
        }

        $newId = $this->generateNoBarangKeluar();

        // Pembuatan otomatis akan mengisi id_tenant via HasTenant trait
        BarangKeluar::create([
            'no_barangkeluar' => $newId,
            'tanggal' => $request->tanggal,
            'id_databarang' => $request->id_databarang,
            'jumlah_keluar' => $request->jumlah_keluar,
        ]);

        $dataBarang->stok -= $request->jumlah_keluar;
        $dataBarang->save();

        return redirect()->route('barangkeluar.index')->with('crud_success', 'Barang keluar berhasil ditambahkan');
    }

    public function edit($id)
    {
        $barangkeluar = BarangKeluar::with('dataBarang')->findOrFail($id);
        $databarangs = Databarang::all();

        return view('barangkeluar.edit', compact('barangkeluar', 'databarangs'));
    }

    public function update(Request $request, $id)
    {
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
            $dataBarang = Databarang::find($request->id_databarang);

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
            $oldBarang = Databarang::find($oldIdDataBarang);
            $newBarang = Databarang::find($request->id_databarang);

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
        $barangkeluar = BarangKeluar::findOrFail($id);

        $dataBarang = Databarang::find($barangkeluar->id_databarang);
        if ($dataBarang) {
            $dataBarang->stok += $barangkeluar->jumlah_keluar;
            $dataBarang->save();
        }

        $barangkeluar->delete();

        return redirect()->route('barangkeluar.index')->with('crud_success', 'Barang keluar berhasil dihapus');
    }

    /**
     * Helper: ambil tenant dari user login
     */
    private function getTenantIdFromUser($userId)
    {
        $role = \App\Models\Role::where('id_user', $userId)->first();
        if ($role) {
            $tenantRoleUser = \App\Models\TenantRoleUser::where('id_role', $role->id)->first();
            return $tenantRoleUser?->id_tenant;
        }
        return null;
    }
}
