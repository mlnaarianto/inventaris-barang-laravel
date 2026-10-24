@extends('layouts.app')

@section('content')
@php
    $bisaKelola = \App\Support\TenantContext::current() !== null;
    $user = auth()->user();
    $bisaTambah = $bisaKelola && $user->can('jenisbarang.create');
    $bisaUbah   = $bisaKelola && $user->can('jenisbarang.update');
    $bisaHapus  = $bisaKelola && $user->can('jenisbarang.delete');
@endphp

<div class="container mx-auto py-6">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-3xl font-semibold text-gray-800">Jenis Barang</h1>

        @if ($bisaTambah)
        <button class="bg-blue-600 text-white px-6 py-3 rounded-lg shadow-lg hover:bg-blue-700 transition duration-200"
            data-modal-target="modal-tambah">
            Tambah Jenis Barang
        </button>
        @endif
    </div>
    <hr class="mb-4 border-gray-300">

    <div class="bg-white shadow-md rounded-lg p-6">
        <div class="flex items-center space-x-2 mb-4">
            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span class="text-lg font-semibold text-gray-700">Daftar Jenis Barang</span>
        </div>
        <table id="tabelJenis" class="display w-full text-sm text-gray-700">
            <thead class="bg-gray-100">
                <tr>
                    <th>No.</th>
                    <th>Jenis Barang</th>
                    @if ($bisaUbah || $bisaHapus)
                    <th>Aksi</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @foreach ($jenisbarangs as $index => $jenis)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $jenis->jenis_barang }}</td>
                    @if ($bisaUbah || $bisaHapus)
                    <td>
                        <div class="flex space-x-4">
                            @if ($bisaUbah)
                            <button type="button" class="text-yellow-500 hover:text-yellow-600"
                                onclick='openEditModal({{ $jenis->id }}, @json($jenis->jenis_barang))'>Edit</button>
                            @endif

                            @if ($bisaHapus)
                            <form action="{{ route('jenisbarangs.destroy', $jenis->id) }}" method="POST"
                                onsubmit="return confirm('Yakin ingin hapus?')">
                                @csrf @method('DELETE')
                                <button class="text-red-500 hover:text-red-600">Hapus</button>
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

    {{-- Modal Tambah --}}
    @if ($bisaTambah)
    <div id="modal-tambah"
        class="modal hidden fixed inset-0 z-50 bg-gray-600 bg-opacity-50 flex justify-center items-center">
        <div class="bg-white p-8 rounded-lg w-1/3 max-w-md">
            <form method="POST" action="{{ route('jenisbarangs.store') }}">
                @csrf
                <h2 class="text-xl font-semibold mb-4">Tambah Jenis Barang</h2>
                <input type="text" name="jenis_barang" class="w-full mb-3 p-2 border rounded"
                    placeholder="Nama jenis barang" required>
                <div class="flex justify-between">
                    <button type="submit"
                        class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Tambah</button>
                    <button type="button" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600"
                        data-modal-target="modal-tambah">Batal</button>
                </div>
            </form>
        </div>
    </div>
    @endif

    {{-- Modal Edit --}}
    @if ($bisaUbah)
    <div id="modal-edit"
        class="modal hidden fixed inset-0 z-50 bg-gray-600 bg-opacity-50 flex justify-center items-center">
        <div class="bg-white p-8 rounded-lg w-1/3 max-w-md">
            <form id="edit-form" method="POST">
                @csrf @method('PUT')
                <h2 class="text-xl font-semibold mb-4">Edit Jenis Barang</h2>
                <input type="text" name="jenis_barang" id="edit-jenis-barang"
                    class="w-full mb-3 p-2 border rounded" required>
                <div class="flex justify-between">
                    <button type="submit"
                        class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Simpan</button>
                    <button type="button" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600"
                        data-modal-target="modal-edit">Batal</button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<!-- DataTables -->
<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">

<script>
    $(document).ready(function() {
        $('#tabelJenis').DataTable({
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

        // Modal toggle (delegasi)
        $(document).on('click', '[data-modal-target]', function() {
            $('#' + $(this).data('modal-target')).toggleClass('hidden');
        });
    });

    function openEditModal(id, value) {
        const form = document.getElementById('edit-form');
        if (!form) return;
        form.action = `/jenisbarang/${id}`;
        $('#edit-jenis-barang').val(value);
        $('#modal-edit').removeClass('hidden');
    }
</script>
@endpush