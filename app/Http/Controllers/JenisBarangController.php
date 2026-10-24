<?php

namespace App\Http\Controllers;

use App\Models\JenisBarang;
use App\Models\DataBarang;
use App\Support\TenantContext;
use Illuminate\Http\Request;

class JenisBarangController extends Controller
{
    private function tolakJikaTanpaTenant()
    {
        if (!TenantContext::current()) {
            return redirect()->route('jenisbarangs.index')->with(
                'crud_error',
                TenantContext::bisaBypass()
                    ? 'Pilih tenant terlebih dahulu di sidebar untuk mengubah data.'
                    : 'Akun ini tidak terhubung ke tenant, sehingga tidak dapat mengubah data.'
            );
        }

        return null;
    }

    private function queryTenant()
    {
        $tenant = TenantContext::current();
        $query = JenisBarang::query();

        if ($tenant) {
            $query->where('id_tenant', $tenant->id);
        }

        return $query;
    }

    public function index()
    {
        $jenisbarangs = $this->queryTenant()->get();

        return view('jenisbarang', compact('jenisbarangs'));
    }

    public function store(Request $request)
    {
        if ($tolak = $this->tolakJikaTanpaTenant()) return $tolak;
        $tenant = TenantContext::current();

        $request->validate([
            'jenis_barang' => 'required|string|max:255',
        ]);

        $jenis = new JenisBarang(['jenis_barang' => $request->jenis_barang]);
        $jenis->id_tenant = $tenant->id;
        $jenis->save();

        return redirect()->route('jenisbarangs.index')->with('crud_success', 'Data berhasil disimpan.');
    }

    public function edit($id)
    {
        if ($tolak = $this->tolakJikaTanpaTenant()) return $tolak;

        $jenisbarang = $this->queryTenant()->findOrFail($id);

        return view('jenisbarangs.edit', compact('jenisbarang'));
    }

    public function update(Request $request, $id)
    {
        if ($tolak = $this->tolakJikaTanpaTenant()) return $tolak;

        $request->validate([
            'jenis_barang' => 'required|string|max:255',
        ]);

        $jenisbarang = $this->queryTenant()->findOrFail($id);
        $jenisbarang->update([
            'jenis_barang' => $request->jenis_barang,
        ]);

        return redirect()->route('jenisbarangs.index')->with('crud_success', 'Data berhasil diperbarui.');
    }

    public function destroy($id)
    {
        if ($tolak = $this->tolakJikaTanpaTenant()) return $tolak;

        $jenisbarang = $this->queryTenant()->findOrFail($id);

        if (DataBarang::where('id_jenisbarang', $id)->exists()) {
            return redirect()->route('jenisbarangs.index')->with('crud_error', 'Data tidak dapat dihapus karena masih digunakan pada data barang.');
        }

        $jenisbarang->delete();

        return redirect()->route('jenisbarangs.index')->with('crud_success', 'Data berhasil dihapus.');
    }
}