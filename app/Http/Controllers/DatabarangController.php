<?php

namespace App\Http\Controllers;

use App\Models\DataBarang;
use App\Models\Tenant;
use App\Models\Satuan;
use App\Models\JenisBarang;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class DatabarangController extends Controller
{
    /**
     * Helper untuk ambil tenant berdasarkan user login
     */
    protected function getCurrentTenant()
    {
        return Auth::user()?->tenant;
    }


    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $tenant = $this->getCurrentTenant();
        if (!$tenant) {
            abort(404, 'Tenant tidak ditemukan untuk user ini.');
        }

        $databarangs = DataBarang::with(['satuan', 'jenisbarang'])
            ->where('id_tenant', $tenant->id)
            ->get();

        return view('databarang', compact('databarangs'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'id_barang' => 'required|string',
                'nama_barang' => 'required|string',
                'stok' => 'required|integer',
                'id_satuan' => 'required|exists:satuans,id',
                'id_jenisbarang' => 'required|exists:jenisbarangs,id',
                'lokasi' => 'required|string|max:255', // validasi lokasi
            ]);


            $tenant = $this->getCurrentTenant();
            if (!$tenant) {
                return redirect()->back()->with('crud_error', 'Tenant tidak ditemukan untuk user ini.');
            }

            // Cek unik dalam tenant
            $exists = DataBarang::where('id_tenant', $tenant->id)
                ->where('id_barang', $request->id_barang)
                ->exists();

            if ($exists) {
                return redirect()->back()
                    ->with('crud_error', 'ID barang sudah digunakan dalam tenant ini.')
                    ->withInput();
            }

            DataBarang::create([
                'id_barang' => $request->id_barang,
                'nama_barang' => $request->nama_barang,
                'stok' => $request->stok,
                'id_satuan' => $request->id_satuan,
                'id_jenisbarang' => $request->id_jenisbarang,
                'id_tenant' => $tenant->id,
                'lokasi' => $request->lokasi, // simpan lokasi
            ]);


            return redirect()->route('databarangs.index')->with('crud_success', 'Data barang berhasil ditambahkan.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->with('crud_error', 'Gagal menambahkan data.')->withErrors($e->validator)->withInput();
        } catch (\Exception $e) {
            return redirect()->back()->with('crud_error', 'Terjadi kesalahan saat menambahkan data.');
        }
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $tenant = $this->getCurrentTenant();
        if (!$tenant) {
            return redirect()->route('databarangs.index')->with('crud_error', 'Tenant tidak ditemukan.');
        }

        $databarang = DataBarang::where('id', $id)
            ->where('id_tenant', $tenant->id)
            ->first();

        if (!$databarang) {
            return redirect()->route('databarangs.index')->with('crud_error', 'Data tidak ditemukan.');
        }

        $satuans = Satuan::all();
        $jenisbarangs = JenisBarang::all();

        return view('databarangs.edit', compact('databarang', 'satuans', 'jenisbarangs'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $tenant = $this->getCurrentTenant();
        if (!$tenant) {
            return redirect()->route('databarangs.index')->with('crud_error', 'Tenant tidak ditemukan.');
        }

        $databarang = DataBarang::where('id', $id)
            ->where('id_tenant', $tenant->id)
            ->first();

        if (!$databarang) {
            return redirect()->route('databarangs.index')->with('crud_error', 'Data tidak ditemukan atau bukan milik tenant Anda.');
        }

        try {
            $request->validate([
                'id_barang' => 'required|string',
                'nama_barang' => 'required|string',
                'stok' => 'required|integer',
                'id_satuan' => 'required|exists:satuans,id',
                'id_jenisbarang' => 'required|exists:jenisbarangs,id',
                'lokasi' => 'required|string|max:255',
            ]);


            // Cek apakah id_barang unik dalam tenant, kecuali untuk record ini sendiri
            $exists = DataBarang::where('id_tenant', $tenant->id)
                ->where('id_barang', $request->id_barang)
                ->where('id', '!=', $databarang->id)
                ->exists();

            if ($exists) {
                return redirect()->back()
                    ->with('crud_error', 'ID barang sudah digunakan dalam tenant ini.')
                    ->withInput();
            }

            $databarang->update([
                'id_barang' => $request->id_barang,
                'nama_barang' => $request->nama_barang,
                'stok' => $request->stok,
                'id_satuan' => $request->id_satuan,
                'id_jenisbarang' => $request->id_jenisbarang,
                'lokasi' => $request->lokasi, // update lokasi
            ]);


            return redirect()->route('databarangs.index')->with('crud_success', 'Data barang berhasil diperbarui.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->with('crud_error', 'Gagal memperbarui data.')->withErrors($e->validator)->withInput();
        } catch (\Exception $e) {
            return redirect()->back()->with('crud_error', 'Terjadi kesalahan saat memperbarui data.');
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $tenant = $this->getCurrentTenant();
        if (!$tenant) {
            return redirect()->route('databarangs.index')->with('crud_error', 'Tenant tidak ditemukan.');
        }

        $databarang = DataBarang::where('id', $id)
            ->where('id_tenant', $tenant->id)
            ->first();

        if (!$databarang) {
            return redirect()->route('databarangs.index')->with('crud_error', 'Data tidak ditemukan atau bukan milik tenant Anda.');
        }

        try {
            if ($databarang->barangkeluars()->exists() || $databarang->barangmasuks()->exists()) {
                return redirect()->back()->with('crud_error', 'Tidak bisa menghapus karena data terhubung.');
            }

            $databarang->delete();
            return redirect()->route('databarangs.index')->with('crud_success', 'Data barang berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()->with('crud_error', 'Terjadi kesalahan saat menghapus data.');
        }
    }
}
