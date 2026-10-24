@php
    $tampilTenant = $tenants->count() > 1;
    $namaTenant = $tenants->keyBy('id');
    $jumlahKolom = $tampilTenant ? 7 : 6;
@endphp
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Barang Keluar</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 6px; text-align: left; }
        th { background-color: #f0f0f0; }
    </style>
</head>
<body>
    <h2 style="text-align: center;">Laporan Barang Keluar</h2>

    <table>
        <thead>
            <tr>
                <th>No</th>
                @if ($tampilTenant)
                    <th>Tenant</th>
                @endif
                <th>No Barang Keluar</th>
                <th>Tanggal</th>
                <th>Nama Barang</th>
                <th>Satuan</th>
                <th>Jumlah Keluar</th>
            </tr>
        </thead>
        <tbody>
            @forelse($barangKeluars as $index => $barang)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    @if ($tampilTenant)
                        <td>{{ $namaTenant[$barang->id_tenant]->nama_perusahaan ?? '-' }}</td>
                    @endif
                    <td>{{ $barang->no_barangkeluar }}</td>
                    <td>{{ $barang->tanggal }}</td>
                    <td>{{ $barang->dataBarang->nama_barang ?? '-' }}</td>
                    <td>{{ $barang->dataBarang->satuan->nama ?? '-' }}</td>
                    <td>{{ $barang->jumlah_keluar }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="{{ $jumlahKolom }}" style="text-align: center;">Tidak ada data</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    <p style="text-align: right;">..............., {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</p>
</body>
</html>