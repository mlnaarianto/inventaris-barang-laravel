<?php

namespace App\Http\Controllers;

use App\Models\BarangKeluar;
use App\Models\DataBarang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
     * Mengembalikan redirect jika tidak, atau null jika boleh lanjut.
     */
    private function tolakJikaTanpaTenant()
    {
        if (!Auth::user()?->id_tenant) {
            return redirect()->route('barangkeluar.index')
                ->with('crud_error', 'Akun ini tidak terhubung ke tenant, sehingga tidak dapat mengubah data.');
        }

        return null;
    }

    public function index()
    {
        $user = Auth::user();
        $bypass = $this->bisaBypassTenant();

        if (!$user->id_tenant && !$bypass) {
            abort(404, 'Tenant tidak ditemukan');
        }

        $barangKeluarQuery = BarangKeluar::with('dataBarang');
        $dataBarangQuery = DataBarang::query();

        if (!$bypass) {
            $barangKeluarQuery->where('id_tenant', $user->id_tenant);
            $dataBarangQuery->where('id_tenant', $user->id_tenant);
        }

        $barangkeluars = $barangKeluarQuery->get();
        $databarangs = $dataBarangQuery->get();

        // ID otomatis hanya untuk user yang punya tenant
        $newId = $user->id_tenant ? $this->generateNoBarangKeluar() : null;

        return view('barangkeluar', compact('barangkeluars', 'databarangs', 'newId'));
    }

    private function generateNoBarangKeluar()
    {
        $user = Auth::user();
        if (!$user || !$user->id_tenant) return null;

        $last = BarangKeluar::where('id_tenant', $user->id_tenant)
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

        $newId = $this->generateNoBarangKeluar();

        // id_tenant diisi otomatis oleh trait HasTenant
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
        if ($tolak = $this->tolakJikaTanpaTenant()) return $tolak;

        $barangkeluar = BarangKeluar::with('dataBarang')->findOrFail($id);
        $databarangs = DataBarang::where('id_tenant', Auth::user()->id_tenant)->get();

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
