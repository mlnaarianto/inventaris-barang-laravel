@extends('layouts.app')

@section('content')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <div class="container mx-auto p-6">
        <div class="flex justify-between items-center mb-4">
            <h1 class="text-3xl font-semibold text-gray-800">
                <span>User Tenant: {{ $tenant->nama }}</span>
            </h1>
            <button onclick="openModalTambah()" class="mt-4 px-6 py-3 bg-green-500 text-white rounded hover:bg-green-700">
                <i class="fas fa-plus"></i> Tambah User
            </button>
        </div>
        <hr class="mb-4 border-gray-300">

        <div class="overflow-x-auto bg-white shadow-lg rounded-lg p-4">
            <table id="tabelUserTenant" class="display w-full text-sm text-gray-700 border border-gray-300 rounded-lg">
                <thead class="bg-purple-500 text-white">
                    <tr>
                        <th class="px-4 py-2">No.</th>
                        <th class="px-4 py-2">Nama</th>
                        <th class="px-4 py-2">Email</th>
                        <th class="px-4 py-2">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($members as $index => $member)
                        <tr class="text-center hover:bg-gray-100 transition">
                            <td class="px-4 py-2">{{ $index + 1 }}</td>
                            <td class="px-4 py-2">{{ $member->nama }}</td>
                            <td class="px-4 py-2">{{ $member->email }}</td>
                            <td class="px-4 py-2">
                                <form action="{{ route('tenantmembers.destroy', $member->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-700"
                                        onclick="return confirm('Keluarkan user ini dari tenant?')">
                                        <i class="fas fa-user-minus"></i> Keluarkan
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Modal Tambah -->
        <div id="modal-tambah" class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center opacity-0 pointer-events-none transition-opacity duration-300">
            <div class="bg-white p-6 rounded-lg shadow-lg w-96">
                <h3 class="text-xl font-semibold mb-4">Tambah User ke Tenant</h3>
                <form method="POST" action="{{ route('tenantmembers.store') }}">
                    @csrf
                    <label class="block mb-2">User</label>
                    <select name="user_id" required class="select-user w-full p-2 border border-gray-300 rounded">
                        <option value="">Pilih User</option>
                        @foreach ($candidates as $candidate)
                            <option value="{{ $candidate->id }}">{{ $candidate->nama }} ({{ $candidate->email }})</option>
                        @endforeach
                    </select>

                    <div class="mt-4 flex justify-end space-x-2">
                        <button type="button" onclick="closeModalTambah()" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-700">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-700">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openModalTambah() {
            document.getElementById('modal-tambah').classList.remove('opacity-0', 'pointer-events-none');
            $('#modal-tambah .select-user').select2({
                dropdownParent: $('#modal-tambah'),
                width: '100%',
                placeholder: "Pilih User",
                allowClear: true
            });
        }

        function closeModalTambah() {
            document.getElementById('modal-tambah').classList.add('opacity-0', 'pointer-events-none');
        }

        $(document).ready(function() {
            $('#tabelUserTenant').DataTable({
                responsive: true,
                language: {
                    search: "Cari:",
                    lengthMenu: "Tampilkan _MENU_ entri",
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
                    infoEmpty: "Tidak ada data tersedia",
                    infoFiltered: "(difilter dari total _MAX_ entri)",
                    zeroRecords: "Tidak ditemukan data yang cocok",
                    paginate: { first: "Pertama", last: "Terakhir", next: "Berikutnya", previous: "Sebelumnya" }
                }
            });
        });
    </script>
@endsection