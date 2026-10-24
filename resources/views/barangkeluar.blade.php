@extends('layouts.app')

@section('content')
@php
    // Tombol hanya muncul kalau ada tenant aktif (milik sendiri atau dipilih admin)
    $bisaKelola = \App\Support\TenantContext::current() !== null;
    $user = auth()->user();
    $bisaTambah = $bisaKelola && $user->can('barangkeluar.create');
    $bisaUbah   = $bisaKelola && $user->can('barangkeluar.update');
    $bisaHapus  = $bisaKelola && $user->can('barangkeluar.delete');
@endphp

<div class="container mx-auto py-6">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-3xl font-semibold text-gray-800">Daftar Barang Keluar</h1>

        @if ($bisaTambah)
        <button class="bg-blue-600 text-white px-6 py-3 rounded-lg shadow-lg hover:bg-blue-700 transition duration-200"
            data-modal-target="add-modal">Tambah Barang Keluar</button>
        @endif
    </div>
    <hr class="mb-4 border-gray-300">

    <div class="bg-white shadow-md rounded-lg p-6 mx-auto">
        <div class="flex items-center space-x-2 mb-4">
            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span class="text-lg font-semibold text-gray-700">Daftar Barang Keluar</span>
        </div>
        <table id="tabelBarangKeluar" class="display w-full text-sm text-gray-700">
            <thead class="bg-gray-100">
                <tr>
                    <th class="py-3 px-6 text-left">No.</th>
                    <th class="py-3 px-6 text-left">No Barang Keluar</th>
                    <th class="py-3 px-6 text-left">Tanggal</th>
                    <th class="py-3 px-6 text-left">Nama Barang</th>
                    <th class="py-3 px-6 text-left">Satuan</th>
                    <th class="py-3 px-6 text-left">Jumlah Keluar</th>
                    @if ($bisaUbah || $bisaHapus)
                    <th class="py-3 px-6 text-left">Aksi</th>
                    @endif
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach ($barangkeluars as $index => $barangkeluar)
                <tr class="hover:bg-gray-100 transition duration-200">
                    <td class="py-3 px-6">{{ $index + 1 }}</td>
                    <td class="py-3 px-6">{{ $barangkeluar->no_barangkeluar }}</td>
                    <td class="py-3 px-6">{{ $barangkeluar->tanggal }}</td>
                    <td class="py-3 px-6">{{ $barangkeluar->dataBarang->nama_barang ?? '-' }}</td>
                    <td class="py-3 px-6">{{ $barangkeluar->dataBarang->satuan->nama ?? '-' }}</td>
                    <td class="py-3 px-6">{{ $barangkeluar->jumlah_keluar }}</td>
                    @if ($bisaUbah || $bisaHapus)
                    <td class="py-3 px-6">
                        <div class="flex space-x-4">
                            @if ($bisaUbah)
                            <button type="button" class="text-yellow-500 hover:text-yellow-600"
                                data-modal-target="edit-modal-{{ $barangkeluar->id }}">Edit</button>
                            @endif

                            @if ($bisaHapus)
                            <form action="{{ route('barangkeluar.destroy', $barangkeluar->id) }}" method="POST"
                                class="inline-block" onsubmit="return confirm('Yakin ingin hapus?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-600">Hapus</button>
                            </form>
                            @endif
                        </div>
                    </td>
                    @endif
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Modal Edit -->
    @if ($bisaUbah)
    @foreach ($barangkeluars as $barangkeluar)
    @php
        $stokSekarang = $barangkeluar->dataBarang->stok ?? 0;
        // Stok sebelum transaksi ini = stok sekarang + jumlah keluar lama
        $stokTersedia = $stokSekarang + $barangkeluar->jumlah_keluar;
    @endphp
    <div id="edit-modal-{{ $barangkeluar->id }}"
        class="modal edit-modal hidden fixed inset-0 z-50 bg-gray-600 bg-opacity-50 flex justify-center items-center"
        data-old-barang="{{ $barangkeluar->id_databarang }}"
        data-old-jumlah="{{ $barangkeluar->jumlah_keluar }}">
        <div class="bg-white p-4 rounded-lg w-1/3 max-w-md">
            <form action="{{ route('barangkeluar.update', $barangkeluar->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">No Barang Keluar</label>
                    <input type="text" class="w-full p-2 border rounded bg-gray-100"
                        value="{{ $barangkeluar->no_barangkeluar }}" readonly>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Tanggal</label>
                    <input type="date" name="tanggal" class="w-full p-2 border rounded"
                        value="{{ $barangkeluar->tanggal }}" required>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Nama Barang</label>
                    <select name="id_databarang" class="w-full p-2 border rounded js-barang" required>
                        @foreach ($databarangs as $databarang)
                        <option value="{{ $databarang->id }}" data-stok="{{ $databarang->stok }}"
                            data-satuan="{{ $databarang->satuan->nama ?? '' }}"
                            @selected($databarang->id == $barangkeluar->id_databarang)>
                            {{ $databarang->nama_barang }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Satuan</label>
                    <input type="text" class="w-full p-2 border rounded bg-gray-100 js-satuan"
                        value="{{ $barangkeluar->dataBarang->satuan->nama ?? '' }}" readonly>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Stok Tersedia</label>
                    <input type="number" class="w-full p-2 border rounded bg-gray-100 js-stok"
                        value="{{ $stokTersedia }}" readonly>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Jumlah Keluar</label>
                    <input type="number" name="jumlah_keluar" class="w-full p-2 border rounded js-jumlah"
                        value="{{ $barangkeluar->jumlah_keluar }}" min="1" required>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Sisa Stok</label>
                    <input type="number" class="w-full p-2 border rounded bg-gray-100 js-sisa"
                        value="{{ $stokSekarang }}" readonly>
                </div>

                <div class="flex justify-between">
                    <button type="submit"
                        class="bg-blue-500 text-white px-6 py-2 rounded-lg hover:bg-blue-600">Update</button>
                    <button type="button"
                        class="bg-gray-500 text-white px-6 py-2 rounded-lg hover:bg-gray-600"
                        data-modal-target="edit-modal-{{ $barangkeluar->id }}">Batal</button>
                </div>
            </form>
        </div>
    </div>
    @endforeach
    @endif

    <!-- Modal Tambah -->
    @if ($bisaTambah)
    <div id="add-modal"
        class="modal hidden fixed inset-0 z-50 bg-gray-600 bg-opacity-50 flex justify-center items-center">
        <div class="bg-white p-4 rounded-lg w-1/3 max-w-md">
            <form action="{{ route('barangkeluar.store') }}" method="POST">
                @csrf

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">No Barang Keluar</label>
                    <input type="text" class="w-full p-2 border rounded bg-gray-100"
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
                        @foreach ($databarangs as $databarang)
                        <option value="{{ $databarang->id }}" data-stok="{{ $databarang->stok }}"
                            data-satuan="{{ $databarang->satuan->nama ?? '' }}">
                            {{ $databarang->nama_barang }}
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
                    <label class="block text-sm font-medium text-gray-700">Jumlah Keluar</label>
                    <input type="number" name="jumlah_keluar" id="jumlah_keluar"
                        class="w-full p-2 border rounded" min="1" required>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Sisa Stok</label>
                    <input type="number" id="sisa_stok" class="w-full p-2 border rounded bg-gray-100" readonly>
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
    @endif
</div>
@endsection

@push('scripts')
<!-- DataTables CDN -->
<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">

<script>
    $(document).ready(function() {
        $('#tabelBarangKeluar').DataTable({
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

        // Modal toggle (delegasi, supaya tombol di dalam DataTables tetap berfungsi)
        $(document).on('click', '[data-modal-target]', function() {
            $('#' + $(this).data('modal-target')).toggleClass('hidden');
        });

        // ===== Modal Tambah =====
        function hitungSisaTambah() {
            const stok = parseInt($('#stok').val()) || 0;
            const jumlah = parseInt($('#jumlah_keluar').val()) || 0;
            $('#sisa_stok').val(stok - jumlah);
        }

        $('#id_databarang').on('change', function() {
            const opt = $('option:selected', this);
            $('#stok').val(opt.data('stok'));
            $('#satuan').val(opt.data('satuan'));
            hitungSisaTambah();
        });

        $('#jumlah_keluar').on('input', hitungSisaTambah);

        // ===== Modal Edit (satu handler untuk semua modal) =====
        function hitungEdit(modal) {
            const stok = parseInt(modal.find('.js-stok').val()) || 0;
            const jumlah = parseInt(modal.find('.js-jumlah').val()) || 0;
            modal.find('.js-sisa').val(stok - jumlah);
        }

        $(document).on('change', '.edit-modal .js-barang', function() {
            const modal = $(this).closest('.edit-modal');
            const opt = $('option:selected', this);
            const oldBarang = String(modal.data('old-barang'));
            const oldJumlah = parseInt(modal.data('old-jumlah')) || 0;

            // Kalau kembali ke barang yang sama, stok lama dikembalikan dulu
            let tersedia = parseInt(opt.data('stok')) || 0;
            if (String(opt.val()) === oldBarang) {
                tersedia += oldJumlah;
            }

            modal.find('.js-stok').val(tersedia);
            modal.find('.js-satuan').val(opt.data('satuan'));
            hitungEdit(modal);
        });

        $(document).on('input', '.edit-modal .js-jumlah', function() {
            hitungEdit($(this).closest('.edit-modal'));
        });
    });
</script>
@endpush