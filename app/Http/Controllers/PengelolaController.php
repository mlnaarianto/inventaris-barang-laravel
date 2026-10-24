<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tenant;
use App\Models\User;
use App\Support\TenantContext;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\BarangMasuk;
use App\Models\BarangKeluar;
use App\Models\DataBarang;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\QueryException;

class PengelolaController extends Controller
{
    /**
     * Daftar id tenant milik user login (dipakai untuk isolasi kepemilikan):
     * - tenant yang didaftarkan dengan email user (pengelola), dan
     * - tenant yang tertaut lewat users.id_tenant
     */
    protected function tenantIds()
    {
        $user = Auth::user();

        return Tenant::where('email', $user->email)->pluck('id')
            ->when($user->id_tenant, fn($c) => $c->push($user->id_tenant))
            ->unique()
            ->values();
    }

    /**
     * Daftar id tenant untuk LAPORAN dan halaman Nama Tenant (hanya baca):
     * - admin (tenant.bypass) dengan tenant terpilih: tenant itu saja
     * - admin tanpa tenant terpilih: semua tenant
     * - lainnya: tenant milik user
     */
    protected function tenantIdsLaporan()
    {
        if (TenantContext::bisaBypass()) {
            $tenant = TenantContext::current();

            return $tenant ? collect([$tenant->id]) : Tenant::pluck('id');
        }

        return $this->tenantIds();
    }

    /**
     * Pastikan tenant ini milik user yang login (isolasi antar pengelola).
     */
    protected function pastikanMilik(Tenant $tenant): void
    {
        abort_unless($this->tenantIds()->contains($tenant->id), 403);
    }

    public function indexLaporanStok(Request $request)
    {
        $tenantIds = $this->tenantIdsLaporan();

        // Tenant untuk dropdown filter
        $tenants = Tenant::whereIn('id', $tenantIds)->get();

        $query = DataBarang::with('satuan')
            ->whereIn('id_tenant', $tenantIds);

        if ($request->filled('id_tenant')) {
            $query->where('id_tenant', $request->id_tenant);
        }

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('id_barang', 'like', "%{$search}%")
                    ->orWhere('nama_barang', 'like', "%{$search}%")
                    ->orWhereHas('satuan', function ($q2) use ($search) {
                        $q2->where('nama', 'like', "%{$search}%");
                    });
            });
        }

        $stokBarangs = $query->get();

        return view('laporanstok', compact('stokBarangs', 'tenants'));
    }

    public function cetakLaporanBarangKeluar(Request $request)
    {
        \Carbon\Carbon::setLocale('id');

        $tenantIds = $this->tenantIdsLaporan();

        if (!$request->filled(['tanggal_awal', 'tanggal_akhir'])) {
            return back()->with('crud_error', 'Harap isi tanggal awal & akhir.');
        }

        $awal  = $request->tanggal_awal;
        $akhir = $request->tanggal_akhir;

        if ($akhir < $awal) {
            return back()->with('crud_error', 'Tanggal akhir tidak boleh sebelum tanggal awal.');
        }

        $query = BarangKeluar::with(['dataBarang.satuan'])
            ->whereIn('id_tenant', $tenantIds)
            ->whereBetween('tanggal', [$awal, $akhir]);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('no_barangkeluar', 'like', "%{$search}%")
                    ->orWhere('tanggal', 'like', "%{$search}%")
                    ->orWhere('jumlah_keluar', 'like', "%{$search}%")
                    ->orWhereHas('dataBarang', function ($q2) use ($search) {
                        $q2->where('nama_barang', 'like', "%{$search}%")
                            ->orWhereHas(
                                'satuan',
                                fn($q3) => $q3->where('nama', 'like', "%{$search}%")
                            );
                    });
            });
        }

        $barangKeluars = $query->get();
        $tenants = Tenant::whereIn('id', $tenantIds)->get();

        $pdf = Pdf::loadView('laporankeluar_pdf', compact('barangKeluars', 'awal', 'akhir', 'tenants'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('laporan_barang_keluar.pdf');
    }

    public function cetakLaporanBarangMasuk(Request $request)
    {
        \Carbon\Carbon::setLocale('id');

        $tenantIds = $this->tenantIdsLaporan();

        if (!$request->filled('tanggal_awal') || !$request->filled('tanggal_akhir')) {
            return redirect()->back()->with('crud_error', 'Harap isi tanggal awal dan akhir.');
        }

        $tanggalAwal = $request->tanggal_awal;
        $tanggalAkhir = $request->tanggal_akhir;

        if ($tanggalAkhir < $tanggalAwal) {
            return redirect()->back()->with('crud_error', 'Tanggal akhir tidak boleh sebelum tanggal awal.');
        }

        $query = BarangMasuk::with(['dataBarang.satuan'])
            ->whereIn('id_tenant', $tenantIds)
            ->whereBetween('tanggal', [$tanggalAwal, $tanggalAkhir]);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('id_barangmasuk', 'like', "%{$search}%")
                    ->orWhere('jumlah_masuk', 'like', "%{$search}%")
                    ->orWhereHas('dataBarang', function ($q2) use ($search) {
                        $q2->where('nama_barang', 'like', "%{$search}%")
                            ->orWhereHas('satuan', function ($q3) use ($search) {
                                $q3->where('nama', 'like', "%{$search}%");
                            });
                    });
            });
        }

        $barangMasuks = $query->get();
        $tenants = Tenant::whereIn('id', $tenantIds)->get();


        $pdf = Pdf::loadView('laporanmasuk_pdf', compact('barangMasuks', 'tanggalAwal', 'tanggalAkhir', 'tenants'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('laporan_barang_masuk.pdf');
    }

    public function indexLaporanBarangMasuk(Request $request)
    {
        $tenantIds = $this->tenantIdsLaporan();

        /* ---------- 1. Mulai query builder ---------- */
        $query = BarangMasuk::with(['dataBarang', 'dataBarang.satuan'])
            ->whereIn('id_tenant', $tenantIds);

        /* ---------- 2. Filter tanggal (jika ada) ----- */
        if ($request->filled('tanggal_awal') && $request->filled('tanggal_akhir')) {
            $awal  = $request->tanggal_awal;
            $akhir = $request->tanggal_akhir;

            if ($akhir < $awal) {
                return back()->with('crud_error', 'Tanggal akhir tidak boleh sebelum tanggal awal.');
            }

            $query->whereBetween('tanggal', [$awal, $akhir]);
        }

        /* ---------- 3. Filter search (jika ada) ------ */
        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('id_barangmasuk', 'like', "%{$search}%")
                    ->orWhere('jumlah_masuk', 'like', "%{$search}%")
                    ->orWhereHas('dataBarang', function ($q2) use ($search) {
                        $q2->where('nama_barang', 'like', "%{$search}%")
                            ->orWhereHas('satuan', function ($q3) use ($search) {
                                $q3->where('nama', 'like', "%{$search}%");
                            });
                    });
            });
        }

        /* ---------- 4. Eksekusi query --------------- */
        // Jika tidak ada filter sama sekali, kembalikan koleksi kosong
        $barangMasuks = ($request->filled('tanggal_awal') || $request->filled('search'))
            ? $query->get()
            : collect();

        $tenants = Tenant::whereIn('id', $tenantIds)->get();

        return view('laporanmasuk', compact('barangMasuks', 'tenants'));
    }

    public function indexLaporanBarangKeluar(Request $request)
    {
        $tenantIds = $this->tenantIdsLaporan();

        // ─── Mulai Query ─────────────────────────────────────────────────────
        $query = BarangKeluar::with(['dataBarang.satuan'])
            ->whereIn('id_tenant', $tenantIds);

        // ─── Filter tanggal (opsional) ───────────────────────────────────────
        if ($request->filled('tanggal_awal') && $request->filled('tanggal_akhir')) {
            $awal  = $request->tanggal_awal;
            $akhir = $request->tanggal_akhir;

            if ($akhir < $awal) {
                return back()->with('crud_error', 'Tanggal akhir tidak boleh sebelum tanggal awal.');
            }

            $query->whereBetween('tanggal', [$awal, $akhir]);
        }

        // ─── Filter search (opsional) ────────────────────────────────────────
        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('no_barangkeluar', 'like', "%{$search}%")
                    ->orWhere('tanggal', 'like', "%{$search}%")
                    ->orWhere('jumlah_keluar', 'like', "%{$search}%")
                    ->orWhereHas('dataBarang', function ($q2) use ($search) {
                        $q2->where('nama_barang', 'like', "%{$search}%")
                            ->orWhereHas(
                                'satuan',
                                fn($q3) => $q3->where('nama', 'like', "%{$search}%")
                            );
                    });
            });
        }

        // ─── Eksekusi / kosongkan jika tak ada filter ────────────────────────
        $barangKeluars = ($request->filled('tanggal_awal') || $request->filled('search'))
            ? $query->get()
            : collect();

        $tenants = Tenant::whereIn('id', $tenantIds)->get();

        return view('laporankeluar', compact('barangKeluars', 'tenants'));
    }

    // Menampilkan daftar tenant di halaman namatenant
    public function showNamatenant()
    {
        $tenants = Tenant::whereIn('id', $this->tenantIdsLaporan())->get();

        return view('namatenant', compact('tenants'));
    }

    // Menampilkan daftar tenant
    public function index()
    {
        $tenants = Tenant::where('email', Auth::user()->email)->get();
        return view('pengelola', compact('tenants'));
    }

    // Menyimpan tenant baru ke database
    public function store(Request $request)
    {
        $request->validate([
            'nama_perusahaan' => 'required|string|max:255',
            'kode' => 'required|string|max:255|unique:tenants,kode',
            'telepon' => 'nullable|string|max:20',
            'alamat' => 'nullable|string'
        ]);

        // Satu pengelola, satu tenant
        $existingTenant = Tenant::where('email', Auth::user()->email)->first();

        if ($existingTenant) {
            return redirect()->back()->with('crud_error', 'Anda sudah mendaftar sebagai tenant!');
        }

        $tenant = Tenant::create([
            'nama' => Auth::user()->nama,
            'email' => Auth::user()->email,
            'nama_perusahaan' => $request->nama_perusahaan,
            'kode' => $request->kode,
            'telepon' => $request->telepon,
            'alamat' => $request->alamat,
            'status' => 'pending'
        ]);

        // Hubungkan pengelola ke tenant barunya
        Auth::user()->update(['id_tenant' => $tenant->id]);

        return redirect()->route('pengelola')->with('crud_success', 'Tenant berhasil ditambahkan.');
    }

    // Menampilkan data tenant tertentu
    public function edit(Tenant $tenant)
    {
        $this->pastikanMilik($tenant);

        return response()->json($tenant);
    }

    // Memperbarui data tenant di database
    public function update(Request $request, Tenant $tenant)
    {
        $this->pastikanMilik($tenant);

        // Email, nama, dan status sengaja tidak bisa diubah di sini
        $data = $request->validate([
            'nama_perusahaan' => 'required|string|max:255',
            'kode' => 'required|string|max:255|unique:tenants,kode,' . $tenant->id,
            'telepon' => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
        ]);

        $tenant->update($data);

        return redirect()->route('pengelola')->with('crud_success', 'Tenant berhasil diperbarui.');
    }

    // Menghapus tenant dari database
    public function destroy(Tenant $tenant)
    {
        $this->pastikanMilik($tenant);

        // Lapis 1: blokir dengan pesan jelas kalau tenant sudah punya data
        foreach (['databarangs', 'barangmasuks', 'barangkeluars'] as $tabel) {
            if (DB::table($tabel)->where('id_tenant', $tenant->id)->exists()) {
                return redirect()->route('pengelola')->with(
                    'crud_error',
                    'Tenant tidak bisa dihapus karena masih memiliki data barang atau transaksi.'
                );
            }
        }

        // Lapis 2: pengaman kalau ada tabel lain yang masih terhubung (foreign key)
        try {
            DB::transaction(function () use ($tenant) {
                // Lepaskan semua user (termasuk pengelola) dari tenant ini
                User::where('id_tenant', $tenant->id)->update(['id_tenant' => null]);
                $tenant->delete();
            });
        } catch (QueryException $e) {
            if ((string) $e->getCode() === '23000') {
                return redirect()->route('pengelola')->with(
                    'crud_error',
                    'Tenant tidak bisa dihapus karena masih terhubung dengan data lain.'
                );
            }

            throw $e; // error database lain tetap dilaporkan, jangan disembunyikan
        }

        return redirect()->route('pengelola')->with('crud_success', 'Tenant berhasil dihapus.');
    }
}
