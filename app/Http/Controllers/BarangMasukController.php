<?php

namespace App\Http\Controllers;

use App\Models\BarangMasuk;
use App\Models\DataBarang;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BarangMasukController extends Controller
{
    /**
     * Ambil tenant berdasarkan user login
     */
    protected function getCurrentTenant()
    {
        return Auth::user()?->tenant;
    }


    public function index()
    {
        $tenant = $this->getCurrentTenant();
        if (!$tenant) {
            abort(404, 'Tenant tidak ditemukan');
        }

        $barangMasuks = BarangMasuk::with('dataBarang')
            ->where('id_tenant', $tenant->id)
            ->get();

        $dataBarangs = DataBarang::where('id_tenant', $tenant->id)->get();

        // Generate ID otomatis per tenant
        $last = BarangMasuk::where('id_tenant', $tenant->id)
            ->orderByDesc('id_barangmasuk')
            ->first();

        $lastNumber = 0;
        if ($last) {
            $lastNumber = (int) substr($last->id_barangmasuk, 2);
        }
        $newId = 'BM' . str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);

        return view('barangmasuk', compact('barangMasuks', 'dataBarangs', 'newId'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'id_databarang' => 'required|exists:databarangs,id',
            'jumlah_masuk' => 'required|integer|min:1',
        ]);

        $tenant = $this->getCurrentTenant();
        if (!$tenant) {
            return redirect()->back()->with('crud_error', 'Tenant tidak ditemukan.');
        }

        $last = BarangMasuk::where('id_tenant', $tenant->id)
            ->orderByDesc('id_barangmasuk')
            ->first();

        $lastNumber = 0;
        if ($last) {
            $lastNumber = (int) substr($last->id_barangmasuk, 2);
        }

        $newId = 'BM' . str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);

        // Tambahan validasi manual (opsional)
        if (BarangMasuk::where('id_tenant', $tenant->id)->where('id_barangmasuk', $newId)->exists()) {
            return back()->with('crud_error', 'ID Barang Masuk sudah digunakan di tenant ini.');
        }

        $barangMasuk = BarangMasuk::create([
            'id_barangmasuk' => $newId,
            'tanggal' => $request->tanggal,
            'id_databarang' => $request->id_databarang,
            'jumlah_masuk' => $request->jumlah_masuk,
            'id_tenant' => $tenant->id,
        ]);

        $dataBarang = DataBarang::find($request->id_databarang);
        $dataBarang->stok += $request->jumlah_masuk;
        $dataBarang->save();

        return redirect()->route('barangmasuk.index')->with('crud_success', 'Barang Masuk berhasil disimpan dan stok diperbarui.');
    }

    public function edit(BarangMasuk $barangMasuk)
    {
        $tenant = $this->getCurrentTenant();
        if (!$tenant || $barangMasuk->id_tenant !== $tenant->id) {
            return redirect()->route('barangmasuk.index')->with('crud_error', 'Data tidak ditemukan atau bukan milik tenant Anda.');
        }

        $dataBarangs = DataBarang::where('id_tenant', $tenant->id)->get();

        return view('barangmasuk.edit', compact('barangMasuk', 'dataBarangs'));
    }

    public function update(Request $request, BarangMasuk $barangMasuk)
    {
        $tenant = $this->getCurrentTenant();
        if (!$tenant || $barangMasuk->id_tenant !== $tenant->id) {
            return redirect()->route('barangmasuk.index')->with('crud_error', 'Data tidak ditemukan atau bukan milik tenant Anda.');
        }

        $request->validate([
            'tanggal' => 'required|date',
            'id_databarang' => 'required|exists:databarangs,id',
            'jumlah_masuk' => 'required|integer|min:1',
        ]);

        $oldJumlahMasuk = $barangMasuk->jumlah_masuk;
        $oldIdDataBarang = $barangMasuk->id_databarang;

        $barangMasuk->update([
            'tanggal' => $request->tanggal,
            'id_databarang' => $request->id_databarang,
            'jumlah_masuk' => $request->jumlah_masuk,
        ]);

        if ($oldIdDataBarang == $request->id_databarang) {
            $selisih = $request->jumlah_masuk - $oldJumlahMasuk;
            $dataBarang = DataBarang::find($request->id_databarang);
            $dataBarang->stok += $selisih;
            if ($dataBarang->stok < 0) $dataBarang->stok = 0;
            $dataBarang->save();
        } else {
            $oldBarang = DataBarang::find($oldIdDataBarang);
            $newBarang = DataBarang::find($request->id_databarang);

            if ($oldBarang) {
                $oldBarang->stok -= $oldJumlahMasuk;
                if ($oldBarang->stok < 0) $oldBarang->stok = 0;
                $oldBarang->save();
            }

            if ($newBarang) {
                $newBarang->stok += $request->jumlah_masuk;
                $newBarang->save();
            }
        }

        return redirect()->route('barangmasuk.index')->with('crud_success', 'Barang Masuk berhasil diperbarui dan stok disesuaikan.');
    }

    public function destroy(BarangMasuk $barangMasuk)
    {
        $tenant = $this->getCurrentTenant();
        if (!$tenant || $barangMasuk->id_tenant !== $tenant->id) {
            return redirect()->route('barangmasuk.index')->with('crud_error', 'Data tidak ditemukan atau bukan milik tenant Anda.');
        }

        $dataBarang = DataBarang::find($barangMasuk->id_databarang);
        if ($dataBarang) {
            $dataBarang->stok -= $barangMasuk->jumlah_masuk;
            if ($dataBarang->stok < 0) {
                $dataBarang->stok = 0;
            }
            $dataBarang->save();
        }

        $barangMasuk->delete();

        return redirect()->route('barangmasuk.index')->with('crud_success', 'Barang Masuk berhasil dihapus dan stok diperbarui.');
    }
}
