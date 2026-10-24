@extends('layouts.app')

@section('content')
@php
    $bisaKelola = \App\Support\TenantContext::current() !== null;
    $user = auth()->user();
    $bisaTambah = $bisaKelola && $user->can('satuan.create');
    $bisaUbah   = $bisaKelola && $user->can('satuan.update');
    $bisaHapus  = $bisaKelola && $user->can('satuan.delete');
@endphp

<div class="container mx-auto py-6">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-3xl font-semibold text-gray-800">Satuan</h1>

        @if ($bisaTambah)
        <button class="bg-blue-600 text-white px-6 py-3 rounded-lg shadow hover:bg-blue-700 transition"
            data-modal-target="modalTambah">Tambah Satuan</button>
        @endif
    </div>
    <hr class="mb-4 border-gray-300">

    <div class="bg-white shadow-md rounded-lg p-6 mx-auto">
        <div class="flex items-center space-x-2 mb-4">
            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span class="text-lg font-semibold text-gray-700">Daftar Satuan</span>
        </div>

        <table id="tabelSatuan" class="display w-full text-sm text-gray-700">
            <thead class="bg-gray-100">
                <tr>
                    <th>No.</th>
                    <th>Nama Satuan</th>
                    @if ($bisaUbah || $bisaHapus)
                    <th>Aksi</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @foreach ($satuans as $index => $satuan)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $satuan->nama }}</td>
                    @if ($bisaUbah || $bisaHapus)
                    <td>
                        <div class="flex space-x-4">
                            @if ($bisaUbah)
                            <button class="text-yellow-500 hover:text-yellow-600 font-medium"
                                data-modal-target="edit-modal-{{ $satuan->id }}">Edit</button>
                            @endif

                            @if ($bisaHapus)
                            <form action="{{ route('satuan.destroy', $satuan->id) }}" method="POST"
                                onsubmit="return confirm('Yakin ingin hapus?')">
                                @csrf @method('DELETE')
                                <button type="submit"
                                    class="text-red-500 hover:text-red-600 font-medium">Hapus</button>
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
    @foreach ($satuans as $satuan)
    <div id="edit-modal-{{ $satuan->id }}"
        class="modal hidden fixed inset-0 z-50 bg-gray-600 bg-opacity-50 flex justify-center items-center">
        <div class="bg-white p-8 rounded-lg w-1/3 max-w-md">
            <form method="POST" action="{{ route('satuan.update', $satuan->id) }}">
                @csrf @method('PUT')
                <h2 class="text-xl font-semibold mb-4">Edit Satuan</h2>
                <input type="text" name="nama" value="{{ $satuan->nama }}"
                    class="w-full mb-3 p-2 border rounded" required>

                <div class="flex justify-between">
                    <button type="submit"
                        class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Simpan</button>
                    <button type="button"
                        class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600"
                        data-modal-target="edit-modal-{{ $satuan->id }}">Batal</button>
                </div>
            </form>
        </div>
    </div>
    @endforeach
    @endif

    <!-- Modal Tambah -->
    @if ($bisaTambah)
    <div id="modalTambah"
        class="modal hidden fixed inset-0 z-50 bg-gray-600 bg-opacity-50 flex justify-center items-center">
        <div class="bg-white p-8 rounded-lg w-1/3 max-w-md">
            <form method="POST" action="{{ route('satuan.store') }}">
                @csrf
                <h2 class="text-xl font-semibold mb-4">Tambah Satuan</h2>
                <input type="text" name="nama" class="w-full mb-3 p-2 border rounded"
                    placeholder="Nama Satuan" required>

                <div class="flex justify-between">
                    <button type="submit"
                        class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Tambah</button>
                    <button type="button" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600"
                        data-modal-target="modalTambah">Batal</button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>

@push('scripts')
<!-- DataTables CDN -->
<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">

<script>
    $(document).ready(function() {
        $('#tabelSatuan').DataTable({
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

        // Modal toggle (delegasi, supaya tombol di halaman DataTables lain tetap berfungsi)
        $(document).on('click', '[data-modal-target]', function() {
            $('#' + $(this).data('modal-target')).toggleClass('hidden');
        });
    });
</script>
@endpush
@endsection