@extends('layouts.app')

@section('content')
<div class="container mx-auto py-6">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-3xl font-semibold text-gray-800">Daftar Barang Masuk</h1>

        @can('barangmasuk.create')
        <button class="bg-blue-600 text-white px-6 py-3 rounded-lg shadow-lg hover:bg-blue-700 transition duration-200"
            data-modal-target="add-modal">Tambah Barang Masuk</button>
        @endcan
    </div>
    <hr class="mb-4 border-gray-300">

    <div class="bg-white shadow-md rounded-lg p-6 mx-auto">
        <div class="flex items-center space-x-2 mb-4">
            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span class="text-lg font-semibold text-gray-700">Daftar Barang Masuk</span>
        </div>
        <table id="tabelBarangMasuk" class="display w-full text-sm text-gray-700">
            <thead class="bg-gray-100">
                <tr>
                    <th class="py-3 px-6 text-left">No.</th>
                    <th class="py-3 px-6 text-left">ID Barang Masuk</th>
                    <th class="py-3 px-6 text-left">Tanggal</th>
                    <th class="py-3 px-6 text-left">Nama Barang</th>
                    <th class="py-3 px-6 text-left">Satuan</th>
                    <th class="py-3 px-6 text-left">Jumlah Masuk</th>
                    @canany(['barangmasuk.update', 'barangmasuk.delete'])
                    <th class="py-3 px-6 text-left">Aksi</th>
                    @endcanany
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach ($barangMasuks as $index => $barangMasuk)
                <tr class="hover:bg-gray-100 transition duration-200">
                    <td class="py-3 px-6">{{ $index + 1 }}</td>
                    <td class="py-3 px-6">{{ $barangMasuk->id_barangmasuk }}</td>
                    <td class="py-3 px-6">{{ $barangMasuk->tanggal }}</td>
                    <td class="py-3 px-6">{{ $barangMasuk->dataBarang->nama_barang ?? '-' }}</td>
                    <td class="py-3 px-6">{{ $barangMasuk->dataBarang->satuan->nama ?? '-' }}</td>
                    <td class="py-3 px-6">{{ $barangMasuk->jumlah_masuk }}</td>
                    @canany(['barangmasuk.update', 'barangmasuk.delete'])
                    <td class="py-3 px-6">
                        <div class="flex space-x-4">
                            @can('barangmasuk.update')
                            <button type="button" class="text-yellow-500 hover:text-yellow-600"
                                data-modal-target="edit-modal-{{ $barangMasuk->id }}">Edit</button>
                            @endcan

                            @can('barangmasuk.delete')
                            <form action="{{ route('barangmasuk.destroy', $barangMasuk) }}" method="POST"
                                class="inline-block" onsubmit="return confirm('Yakin ingin hapus?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-600">Hapus</button>
                            </form>
                            @endcan
                        </div>
                    </td>
                    @endcanany
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Modal Edit (di luar tabel, hanya untuk yang boleh update) -->
    @can('barangmasuk.update')
    @foreach ($barangMasuks as $barangMasuk)
    <div id="edit-modal-{{ $barangMasuk->id }}"
        class="modal hidden fixed inset-0 z-50 bg-gray-600 bg-opacity-50 flex justify-center items-center">
        <div class="bg-white p-4 rounded-lg w-1/3 max-w-md">
            <form action="{{ route('barangmasuk.update', $barangMasuk) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">ID Barang Masuk</label>
                    <input type="text" name="id_barangmasuk" class="w-full p-2 border rounded bg-gray-100"
                        value="{{ $barangMasuk->id_barangmasuk }}" readonly>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Tanggal</label>
                    <input type="date" name="tanggal" class="w-full p-2 border rounded"
                        value="{{ $barangMasuk->tanggal }}" required>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Nama Barang</label>
                    <select name="id_databarang" class="w-full p-2 border rounded" required>
                        @foreach ($dataBarangs as $dataBarang)
                        <option value="{{ $dataBarang->id }}" data-stok="{{ $dataBarang->stok }}"
                            data-satuan="{{ $dataBarang->satuan->nama ?? '' }}"
                            @selected($dataBarang->id == $barangMasuk->id_databarang)>
                            {{ $dataBarang->nama_barang }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Satuan</label>
                    <input type="text" id="satuan-edit-{{ $barangMasuk->id }}"
                        class="w-full p-2 border rounded bg-gray-100"
                        value="{{ $barangMasuk->dataBarang->satuan->nama ?? '' }}" readonly>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Stok Tersedia</label>
                    <input type="number" id="stok-edit-{{ $barangMasuk->id }}"
                        class="w-full p-2 border rounded bg-gray-100"
                        value="{{ $barangMasuk->dataBarang->stok ?? 0 }}" readonly>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Jumlah Masuk</label>
                    <input type="number" name="jumlah_masuk" id="jumlah_masuk-edit-{{ $barangMasuk->id }}"
                        class="w-full p-2 border rounded" value="{{ $barangMasuk->jumlah_masuk }}"
                        min="1" required>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Total Stok</label>
                    <input type="number" id="total_stok-edit-{{ $barangMasuk->id }}"
                        class="w-full p-2 border rounded bg-gray-100"
                        value="{{ ($barangMasuk->dataBarang->stok ?? 0) + $barangMasuk->jumlah_masuk }}"
                        readonly>
                </div>

                <div class="flex justify-between">
                    <button type="submit"
                        class="bg-blue-500 text-white px-6 py-2 rounded-lg hover:bg-blue-600">Update</button>
                    <button type="button"
                        class="bg-gray-500 text-white px-6 py-2 rounded-lg hover:bg-gray-600"
                        data-modal-target="edit-modal-{{ $barangMasuk->id }}">Batal</button>
                </div>
            </form>
        </div>
    </div>
    @endforeach
    @endcan

    <!-- Modal Tambah -->
    @can('barangmasuk.create')
    <div id="add-modal"
        class="modal hidden fixed inset-0 z-50 bg-gray-600 bg-opacity-50 flex justify-center items-center">
        <div class="bg-white p-4 rounded-lg w-1/3 max-w-md">
            <form action="{{ route('barangmasuk.store') }}" method="POST">
                @csrf

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">ID Barang Masuk</label>
                    <input type="text" name="id_barangmasuk" class="w-full p-2 border rounded bg-gray-100"
                        value="{{ $newId }}" readonly>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Tanggal</label>
                    <input type="date" name="tanggal" class="w-full p-2 border rounded" required>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Nama Barang</label>
                    <select name="id_databarang" id="id_databarang" class="w-full p-2 border rounded" required>
                        <option value="">Pilih Barang</option>
                        @foreach ($dataBarangs as $dataBarang)
                        <option value="{{ $dataBarang->id }}" data-stok="{{ $dataBarang->stok }}"
                            data-satuan="{{ $dataBarang->satuan->nama ?? '' }}">
                            {{ $dataBarang->nama_barang }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Satuan</label>
                    <input type="text" id="satuan" class="w-full p-2 border rounded bg-gray-100" readonly>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Stok Tersedia</label>
                    <input type="number" id="stok" class="w-full p-2 border rounded bg-gray-100" readonly>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Jumlah Masuk</label>
                    <input type="number" name="jumlah_masuk" id="jumlah_masuk"
                        class="w-full p-2 border rounded" min="1" required>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Total Stok</label>
                    <input type="number" id="total_stok" class="w-full p-2 border rounded bg-gray-100" readonly>
                </div>

                <div class="flex justify-between">
                    <button type="submit"
                        class="bg-blue-500 text-white px-6 py-2 rounded-lg hover:bg-blue-600">Simpan</button>
                    <button type="button" class="bg-gray-500 text-white px-6 py-2 rounded-lg hover:bg-gray-600"
                        data-modal-target="add-modal">Batal</button>
                </div>
            </form>
        </div>
    </div>
    @endcan
</div>
@endsection

@push('scripts')
<!-- DataTables CDN -->
<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">

<script>
    $(document).ready(function() {
        $('#tabelBarangMasuk').DataTable({
            language: {
                search: "Cari:",
                lengthMenu: "Tampilkan _MENU_ entri",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
                paginate: {
                    first: "Awal",
                    last: "Akhir",
                    next: "Berikutnya",
                    previous: "Sebelumnya"
                },
                zeroRecords: "Tidak ada data ditemukan",
                emptyTable: "Tidak ada data tersedia"
            }
        });

        // Modal toggle
        $('[data-modal-target]').on('click', function() {
            let modalId = $(this).data('modal-target');
            $('#' + modalId).toggleClass('hidden');
        });

        // Tambah Modal (jQuery aman kalau elemennya tidak ada)
        $('#id_databarang').on('change', function() {
            const stok = $('option:selected', this).data('stok');
            const satuan = $('option:selected', this).data('satuan');
            $('#stok').val(stok);
            $('#satuan').val(satuan);
            updateTotalStok('stok', 'jumlah_masuk', 'total_stok');
        });

        $('#jumlah_masuk').on('input', function() {
            updateTotalStok('stok', 'jumlah_masuk', 'total_stok');
        });

        // Edit Modal (hanya dirender untuk yang boleh update)
        @can('barangmasuk.update')
        @foreach($barangMasuks as $barangMasuk)
        $('#edit-modal-{{ $barangMasuk->id }} select[name="id_databarang"]').on('change', function() {
            const stok = $('option:selected', this).data('stok');
            const satuan = $('option:selected', this).data('satuan');
            $('#stok-edit-{{ $barangMasuk->id }}').val(stok);
            $('#satuan-edit-{{ $barangMasuk->id }}').val(satuan);
            updateTotalStok('stok-edit-{{ $barangMasuk->id }}',
                'jumlah_masuk-edit-{{ $barangMasuk->id }}',
                'total_stok-edit-{{ $barangMasuk->id }}');
        });

        $('#jumlah_masuk-edit-{{ $barangMasuk->id }}').on('input', function() {
            updateTotalStok('stok-edit-{{ $barangMasuk->id }}',
                'jumlah_masuk-edit-{{ $barangMasuk->id }}',
                'total_stok-edit-{{ $barangMasuk->id }}');
        });
        @endforeach
        @endcan
    });

    function updateTotalStok(stokId, jumlahId, totalId) {
        const stok = parseInt($('#' + stokId).val()) || 0;
        const jumlah = parseInt($('#' + jumlahId).val()) || 0;
        $('#' + totalId).val(stok + jumlah);
    }
</script>
@endpush