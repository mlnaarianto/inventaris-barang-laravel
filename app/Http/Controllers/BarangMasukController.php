<?php

namespace App\Http\Controllers;

use App\Models\BarangMasuk;
use App\Models\DataBarang;
use App\Models\Tenant;
use App\Support\TenantContext;
use Illuminate\Http\Request;

class BarangMasukController extends Controller
{
    protected function getCurrentTenant()
    {
        return TenantContext::current();
    }

    /**
     * Barang harus milik tenant yang sedang aktif.
     */
    private function barangMilikTenant(Tenant $tenant, $id): DataBarang
    {
        return DataBarang::where('id_tenant', $tenant->id)->findOrFail($id);
    }

    private function generateId(Tenant $tenant): string
    {
        $last = BarangMasuk::where('id_tenant', $tenant->id)
            ->orderByDesc('id_barangmasuk')
            ->first();

        $lastNumber = $last ? (int) substr($last->id_barangmasuk, 2) : 0;

        return 'BM' . str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
    }

    public function index()
    {
        $tenant = $this->getCurrentTenant();

        if (!$tenant && !TenantContext::bisaBypass()) {
            abort(404, 'Tenant tidak ditemukan');
        }

        $barangMasukQuery = BarangMasuk::with('dataBarang');
        $dataBarangQuery = DataBarang::with('satuan');

        // Admin tanpa tenant terpilih melihat semua tenant
        if ($tenant) {
            $barangMasukQuery->where('id_tenant', $tenant->id);
            $dataBarangQuery->where('id_tenant', $tenant->id);
        }

        $barangMasuks = $barangMasukQuery->get();
        $dataBarangs = $dataBarangQuery->get();

        $newId = $tenant ? $this->generateId($tenant) : null;

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
            return redirect()->back()->with(
                'crud_error',
                TenantContext::bisaBypass()
                    ? 'Pilih tenant terlebih dahulu di sidebar.'
                    : 'Tenant tidak ditemukan.'
            );
        }

        $dataBarang = $this->barangMilikTenant($tenant, $request->id_databarang);

        $newId = $this->generateId($tenant);

        if (BarangMasuk::where('id_tenant', $tenant->id)->where('id_barangmasuk', $newId)->exists()) {
            return back()->with('crud_error', 'ID Barang Masuk sudah digunakan di tenant ini.');
        }

        BarangMasuk::create([
            'id_barangmasuk' => $newId,
            'tanggal' => $request->tanggal,
            'id_databarang' => $dataBarang->id,
            'jumlah_masuk' => $request->jumlah_masuk,
            'id_tenant' => $tenant->id,
        ]);

        $dataBarang->stok += $request->jumlah_masuk;
        $dataBarang->save();

        return redirect()->route('barangmasuk.index')->with('crud_success', 'Barang Masuk berhasil disimpan dan stok diperbarui.');
    }

    public function edit(BarangMasuk $barangMasuk)
    {
        $tenant = $this->getCurrentTenant();
        if (!$tenant || (int) $barangMasuk->id_tenant !== (int) $tenant->id) {
            return redirect()->route('barangmasuk.index')->with('crud_error', 'Data tidak ditemukan atau bukan milik tenant ini.');
        }

        $dataBarangs = DataBarang::where('id_tenant', $tenant->id)->get();

        return view('barangmasuk.edit', compact('barangMasuk', 'dataBarangs'));
    }

    public function update(Request $request, BarangMasuk $barangMasuk)
    {
        $tenant = $this->getCurrentTenant();
        if (!$tenant || (int) $barangMasuk->id_tenant !== (int) $tenant->id) {
            return redirect()->route('barangmasuk.index')->with('crud_error', 'Data tidak ditemukan atau bukan milik tenant ini.');
        }

        $request->validate([
            'tanggal' => 'required|date',
            'id_databarang' => 'required|exists:databarangs,id',
            'jumlah_masuk' => 'required|integer|min:1',
        ]);

        // Barang baru harus milik tenant yang sama
        $newBarang = $this->barangMilikTenant($tenant, $request->id_databarang);

        $oldJumlahMasuk = $barangMasuk->jumlah_masuk;
        $oldIdDataBarang = $barangMasuk->id_databarang;

        $barangMasuk->update([
            'tanggal' => $request->tanggal,
            'id_databarang' => $request->id_databarang,
            'jumlah_masuk' => $request->jumlah_masuk,
        ]);

        if ($oldIdDataBarang == $request->id_databarang) {
            $selisih = $request->jumlah_masuk - $oldJumlahMasuk;
            $newBarang->stok += $selisih;
            if ($newBarang->stok < 0) $newBarang->stok = 0;
            $newBarang->save();
        } else {
            $oldBarang = DataBarang::where('id_tenant', $tenant->id)->find($oldIdDataBarang);

            if ($oldBarang) {
                $oldBarang->stok -= $oldJumlahMasuk;
                if ($oldBarang->stok < 0) $oldBarang->stok = 0;
                $oldBarang->save();
            }

            $newBarang->stok += $request->jumlah_masuk;
            $newBarang->save();
        }

        return redirect()->route('barangmasuk.index')->with('crud_success', 'Barang Masuk berhasil diperbarui dan stok disesuaikan.');
    }

    public function destroy(BarangMasuk $barangMasuk)
    {
        $tenant = $this->getCurrentTenant();
        if (!$tenant || (int) $barangMasuk->id_tenant !== (int) $tenant->id) {
            return redirect()->route('barangmasuk.index')->with('crud_error', 'Data tidak ditemukan atau bukan milik tenant ini.');
        }

        $dataBarang = DataBarang::where('id_tenant', $tenant->id)->find($barangMasuk->id_databarang);
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