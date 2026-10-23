<?php

namespace App\Http\Controllers;

use App\Models\JenisBarang;
use App\Models\DataBarang;
use Illuminate\Http\Request;

class JenisBarangController extends Controller
{
    public function index()
    {
        $jenisbarangs = JenisBarang::all(); // global scope tenant aktif
        return view('jenisbarang', compact('jenisbarangs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'jenis_barang' => 'required|string|max:255',
        ]);

        JenisBarang::create([
            'jenis_barang' => $request->jenis_barang,
            // id_tenant diisi otomatis oleh trait
        ]);

        return redirect()->route('jenisbarangs.index')->with('crud_success', 'Data berhasil disimpan.');
    }

    public function edit($id)
    {
        $jenisbarang = JenisBarang::findOrFail($id); // hanya data tenant yang aktif
        return view('jenisbarangs.edit', compact('jenisbarang'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'jenis_barang' => 'required|string|max:255',
        ]);

        $jenisbarang = JenisBarang::findOrFail($id); // aman terhadap data dari tenant lain
        $jenisbarang->update([
            'jenis_barang' => $request->jenis_barang,
        ]);

        return redirect()->route('jenisbarangs.index')->with('crud_success', 'Data berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $jenisbarang = JenisBarang::findOrFail($id); // hanya milik tenant aktif

        $isUsed = DataBarang::where('id_jenisbarang', $id)->exists();
        if ($isUsed) {
            return redirect()->route('jenisbarangs.index')->with('crud_error', 'Data tidak dapat dihapus karena masih digunakan pada data barang.');
        }

        $jenisbarang->delete();

        return redirect()->route('jenisbarangs.index')->with('crud_success', 'Data berhasil dihapus.');
    }
}
