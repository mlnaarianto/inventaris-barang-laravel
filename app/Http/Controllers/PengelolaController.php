<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tenant;
use Illuminate\Support\Facades\Auth;
use App\Models\BarangMasuk;
use App\Models\BarangKeluar;
use App\Models\Databarang;
use Barryvdh\DomPDF\Facade\Pdf;

class PengelolaController extends Controller
{
    /**
     * Daftar id tenant milik user login:
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

    public function indexLaporanStok(Request $request)
    {
        $tenantIds = $this->tenantIds();

        // Tenant untuk dropdown filter
        $tenants = Tenant::whereIn('id', $tenantIds)->get();

        $query = Databarang::with('satuan')
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

        $tenantIds = $this->tenantIds();

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

        $pdf = Pdf::loadView('laporankeluar_pdf', compact('barangKeluars', 'awal', 'akhir'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('laporan_barang_keluar.pdf');
    }

    public function cetakLaporanBarangMasuk(Request $request)
    {
        \Carbon\Carbon::setLocale('id');

        $tenantIds = $this->tenantIds();

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

        $pdf = Pdf::loadView('laporanmasuk_pdf', compact('barangMasuks', 'tanggalAwal', 'tanggalAkhir'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('laporan_barang_masuk.pdf');
    }

    public function indexLaporanBarangMasuk(Request $request)
    {
        $tenantIds = $this->tenantIds();

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
        $tenantIds = $this->tenantIds();

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
        $tenants = Tenant::whereIn('id', $this->tenantIds())->get();

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

        // Cek apakah tenant dengan email user sudah ada
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
        return response()->json($tenant);
    }

    // Memperbarui data tenant di database
    public function update(Request $request, Tenant $tenant)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'nama_perusahaan' => 'required|string|max:255',
            'kode' => 'required|string|max:255|unique:tenants,kode,' . $tenant->id,
            'email' => 'required|email|unique:tenants,email,' . $tenant->id,
            'telepon' => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
        ]);

        // Simpan semua data kecuali status agar tetap pending
        $updateData = $request->except(['status']);
        $tenant->update($updateData);

        return redirect()->route('pengelola')->with('crud_success', 'Tenant berhasil diperbarui.');
    }

    // Menghapus tenant dari database
    public function destroy(Tenant $tenant)
    {
        $tenant->delete();
        return redirect()->route('pengelola')->with('crud_success', 'Tenant berhasil dihapus.');
    }
}