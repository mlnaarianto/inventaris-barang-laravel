@extends('layouts.app')

@section('content')
    <div class="container mx-auto py-6">
        <div class="flex justify-between items-center mb-4">
            <h1 class="text-3xl font-semibold text-gray-800">Data Barang</h1>
            <button class="bg-blue-600 text-white px-6 py-3 rounded-lg shadow hover:bg-blue-700 transition"
                data-modal-target="add-modal">Tambah Barang</button>
        </div>
        <hr class="mb-4 border-gray-300">

        <div class="bg-white shadow-md rounded-lg p-6 mx-auto">
            <div class="flex items-center space-x-2 mb-4">
                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="text-lg font-semibold text-gray-700">Daftar Data Barang</span>
            </div>
            <table id="tabelBarang" class="display w-full text-sm text-gray-700">
                <thead class="bg-gray-100">
                    <tr>
                        <th>No.</th>
                        <th>ID Barang</th>
                        <th>Nama Barang</th>
                        <th>Stok</th>
                        <th>Satuan</th>
                        <th>Jenis Barang</th>
                        <th>Lokasi</th> {{-- Tambahkan ini --}}
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($databarangs as $index => $barang)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $barang->id_barang }}</td>
                            <td>{{ $barang->nama_barang }}</td>
                            <td>{{ $barang->stok }}</td>
                            <td>{{ $barang->satuan->nama }}</td>
                            <td>{{ $barang->jenisbarang->jenis_barang }}</td>
                            <td>{{ $barang->lokasi }}</td>
                            <td>
                                <div class="flex space-x-4">
                                    <button class="text-yellow-500 hover:text-yellow-600"
                                        data-modal-target="edit-modal-{{ $barang->id }}">Edit</button>
                                    <form action="{{ route('databarangs.destroy', $barang->id) }}" method="POST"
                                        onsubmit="return confirm('Yakin ingin hapus?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:text-red-600">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>

                        <!-- Modal Edit -->
                        <div id="edit-modal-{{ $barang->id }}"
                            class="modal hidden fixed inset-0 z-50 bg-gray-600 bg-opacity-50 flex justify-center items-center">
                            <div class="bg-white p-8 rounded-lg w-1/3 max-w-md">
                                <form method="POST" action="{{ route('databarangs.update', $barang->id) }}">
                                    @csrf @method('PUT')
                                    <h2 class="text-xl font-semibold mb-4">Edit Barang</h2>
                                    <input type="text" name="id_barang" value="{{ $barang->id_barang }}"
                                        class="w-full mb-3 p-2 border rounded" required>
                                    <input type="text" name="nama_barang" value="{{ $barang->nama_barang }}"
                                        class="w-full mb-3 p-2 border rounded" required>
                                    <input type="number" name="stok" value="{{ $barang->stok }}"
                                        class="w-full mb-3 p-2 border rounded" required>
                                        <input type="text" name="lokasi" value="{{ $barang->lokasi }}" class="w-full mb-3 p-2 border rounded" required>


                                    <select name="id_satuan" class="w-full mb-3 p-2 border rounded" required>
                                        @foreach (\App\Models\Satuan::all() as $satuan)
                                            <option value="{{ $satuan->id }}"
                                                @if ($satuan->id == $barang->id_satuan) selected @endif>{{ $satuan->nama }}
                                            </option>
                                        @endforeach
                                    </select>

                                    <select name="id_jenisbarang" class="w-full mb-3 p-2 border rounded" required>
                                        @foreach (\App\Models\Jenisbarang::all() as $jenis)
                                            <option value="{{ $jenis->id }}"
                                                @if ($jenis->id == $barang->id_jenisbarang) selected @endif>
                                                {{ $jenis->jenis_barang }}</option>
                                        @endforeach
                                    </select>

                                    <div class="flex justify-between">
                                        <button type="submit"
                                            class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Simpan</button>
                                        <button type="button"
                                            class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600"
                                            data-modal-target="edit-modal-{{ $barang->id }}">Batal</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </tbody>

            </table>
        </div>

        <!-- Modal Tambah -->
        <div id="add-modal"
            class="modal hidden fixed inset-0 z-50 bg-gray-600 bg-opacity-50 flex justify-center items-center">
            <div class="bg-white p-8 rounded-lg w-1/3 max-w-md">
                <form method="POST" action="{{ route('databarangs.store') }}">
                    @csrf
                    <h2 class="text-xl font-semibold mb-4">Tambah Barang</h2>
                    <input type="text" name="id_barang" class="w-full mb-3 p-2 border rounded" placeholder="ID Barang"
                        required>
                    <input type="text" name="nama_barang" class="w-full mb-3 p-2 border rounded"
                        placeholder="Nama Barang" required>
                    <input type="number" name="stok" class="w-full mb-3 p-2 border rounded" placeholder="Stok" required>
                    <input type="text" name="lokasi" class="w-full mb-3 p-2 border rounded" placeholder="Lokasi" required>

                    <select name="id_satuan" class="w-full mb-3 p-2 border rounded" required>
                        <option value="">-- Pilih Satuan --</option>
                        @foreach (\App\Models\Satuan::all() as $satuan)
                            <option value="{{ $satuan->id }}">{{ $satuan->nama }}</option>
                        @endforeach
                    </select>

                    <select name="id_jenisbarang" class="w-full mb-3 p-2 border rounded" required>
                        <option value="">-- Pilih Jenis Barang --</option>
                        @foreach (\App\Models\JenisBarang::all() as $jenis)
                            <option value="{{ $jenis->id }}">{{ $jenis->jenis_barang }}</option>
                        @endforeach
                    </select>

                    <div class="flex justify-between">
                        <button type="submit"
                            class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Tambah</button>
                        <button type="button" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600"
                            data-modal-target="add-modal">Batal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <!-- DataTables CDN -->
        <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
        <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
        <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">

        <script>
            $(document).ready(function() {
                $('#tabelBarang').DataTable({
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
            });
        </script>
    @endpush
@endsection
