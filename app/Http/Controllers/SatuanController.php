<?php

namespace App\Http\Controllers;

use App\Models\Satuan;
use App\Support\TenantContext;
use Illuminate\Http\Request;

class SatuanController extends Controller
{
    /**
     * Operasi tulis wajib punya tenant aktif (milik sendiri / dipilih admin).
     */
    private function tolakJikaTanpaTenant()
    {
        if (!TenantContext::current()) {
            return redirect()->route('satuan.index')->with(
                'crud_error',
                TenantContext::bisaBypass()
                    ? 'Pilih tenant terlebih dahulu di sidebar untuk mengubah data.'
                    : 'Akun ini tidak terhubung ke tenant, sehingga tidak dapat mengubah data.'
            );
        }

        return null;
    }

    /**
     * Query satuan yang dibatasi ke tenant aktif.
     * (Admin bypass tidak difilter oleh global scope, jadi difilter manual di sini.)
     */
    private function queryTenant()
    {
        $tenant = TenantContext::current();
        $query = Satuan::query();

        if ($tenant) {
            $query->where('id_tenant', $tenant->id);
        }

        return $query;
    }

    public function index()
    {
        // Admin tanpa tenant terpilih melihat semua tenant
        $satuans = $this->queryTenant()->get();

        return view('satuan', compact('satuans'));
    }

    public function store(Request $request)
    {
        if ($tolak = $this->tolakJikaTanpaTenant()) return $tolak;
        $tenant = TenantContext::current();

        $request->validate([
            'nama' => 'required|string|max:255',
        ]);

        // id_tenant diisi eksplisit karena admin tidak punya id_tenant sendiri
        $satuan = new Satuan(['nama' => $request->nama]);
        $satuan->id_tenant = $tenant->id;
        $satuan->save();

        return redirect()->back()->with('crud_success', 'Data satuan berhasil ditambahkan.');
    }

    public function edit($id)
    {
        if ($tolak = $this->tolakJikaTanpaTenant()) return $tolak;

        $satuan = $this->queryTenant()->findOrFail($id);

        return view('satuan.edit', compact('satuan'));
    }

    public function update(Request $request, $id)
    {
        if ($tolak = $this->tolakJikaTanpaTenant()) return $tolak;

        $request->validate([
            'nama' => 'required|string|max:255',
        ]);

        $satuan = $this->queryTenant()->findOrFail($id);
        $satuan->update([
            'nama' => $request->nama,
        ]);

        return redirect()->route('satuan.index')->with('crud_success', 'Data satuan berhasil diperbarui.');
    }

    public function destroy($id)
    {
        if ($tolak = $this->tolakJikaTanpaTenant()) return $tolak;

        $satuan = $this->queryTenant()->findOrFail($id);

        if ($satuan->databarangs()->exists()) {
            return redirect()->back()->with('crud_error', 'Tidak dapat menghapus satuan karena digunakan pada data barang.');
        }

        $satuan->delete();

        return redirect()->back()->with('crud_success', 'Data satuan berhasil dihapus.');
    }
}