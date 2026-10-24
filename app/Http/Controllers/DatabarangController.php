<?php

namespace App\Http\Controllers;

use App\Models\DataBarang;
use App\Models\Satuan;
use App\Models\JenisBarang;
use App\Support\TenantContext;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DatabarangController extends Controller
{
    /**
     * Tenant yang sedang dikelola:
     * - user biasa: tenant miliknya
     * - admin (tenant.bypass): tenant yang dipilih di sidebar, atau null
     */
    protected function getCurrentTenant()
    {
        return TenantContext::current();
    }

    /**
     * Aturan validasi: satuan & jenis barang harus milik tenant yang sama.
     */
    private function aturanBarang($tenant): array
    {
        return [
            'id_barang' => 'required|string',
            'nama_barang' => 'required|string',
            'stok' => 'required|integer',
            'id_satuan' => ['required', Rule::exists('satuans', 'id')->where('id_tenant', $tenant->id)],
            'id_jenisbarang' => ['required', Rule::exists('jenisbarangs', 'id')->where('id_tenant', $tenant->id)],
            'lokasi' => 'required|string|max:255',
        ];
    }

    public function index()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $tenant = $this->getCurrentTenant();

        if (!$tenant && !TenantContext::bisaBypass()) {
            abort(404, 'Tenant tidak ditemukan untuk user ini.');
        }

        $query = DataBarang::with(['satuan', 'jenisbarang']);

        // Admin tanpa tenant terpilih melihat semua tenant
        if ($tenant) {
            $query->where('id_tenant', $tenant->id);
        }

        $databarangs = $query->get();

        // Dropdown hanya berisi data tenant aktif (kosong kalau admin belum memilih tenant)
        $daftarSatuan = $tenant ? Satuan::where('id_tenant', $tenant->id)->get() : collect();
        $daftarJenis = $tenant ? JenisBarang::where('id_tenant', $tenant->id)->get() : collect();

        return view('databarang', compact('databarangs', 'daftarSatuan', 'daftarJenis'));
    }

    public function store(Request $request)
    {
        try {
            $tenant = $this->getCurrentTenant();
            if (!$tenant) {
                return redirect()->back()->with(
                    'crud_error',
                    TenantContext::bisaBypass()
                        ? 'Pilih tenant terlebih dahulu di sidebar.'
                        : 'Tenant tidak ditemukan untuk user ini.'
                );
            }

            $request->validate($this->aturanBarang($tenant));

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
                'lokasi' => $request->lokasi,
            ]);

            return redirect()->route('databarangs.index')->with('crud_success', 'Data barang berhasil ditambahkan.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->with('crud_error', 'Gagal menambahkan data.')->withErrors($e->validator)->withInput();
        } catch (\Exception $e) {
            return redirect()->back()->with('crud_error', 'Terjadi kesalahan saat menambahkan data.');
        }
    }

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

        $satuans = Satuan::where('id_tenant', $tenant->id)->get();
        $jenisbarangs = JenisBarang::where('id_tenant', $tenant->id)->get();

        return view('databarangs.edit', compact('databarang', 'satuans', 'jenisbarangs'));
    }

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
            return redirect()->route('databarangs.index')->with('crud_error', 'Data tidak ditemukan atau bukan milik tenant ini.');
        }

        try {
            $request->validate($this->aturanBarang($tenant));

            // Unik dalam tenant, kecuali record ini sendiri
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
                'lokasi' => $request->lokasi,
            ]);

            return redirect()->route('databarangs.index')->with('crud_success', 'Data barang berhasil diperbarui.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->with('crud_error', 'Gagal memperbarui data.')->withErrors($e->validator)->withInput();
        } catch (\Exception $e) {
            return redirect()->back()->with('crud_error', 'Terjadi kesalahan saat memperbarui data.');
        }
    }

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
            return redirect()->route('databarangs.index')->with('crud_error', 'Data tidak ditemukan atau bukan milik tenant ini.');
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