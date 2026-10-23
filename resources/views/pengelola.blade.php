<!-- resources/views/pengelola.blade.php -->
@extends('layouts.app')

@section('content')
<div x-data="{ isOpen: false }" class="container mx-auto p-6">

    <div class="flex justify-between items-center mb-4">
        <h1 class="text-3xl font-bold mb-6 flex items-center space-x-2 text-gray-800">
            <i class="fa-solid fa-building text-purple-600"></i>
            <span>Pendaftaran Tenant</span>
        </h1>

        @if ($tenants->isEmpty())
        <button class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 transition duration-300"
            onclick="openModal('addTenantModal')"> <i class="fas fa-plus"></i> Tambah Tenant</button>
        @endif

    </div>




    <!-- Tabel Tenant -->
    <div class="bg-white shadow-md rounded-lg overflow-hidden mt-6">
        <div class="overflow-x-auto">
            <table class="w-full min-w-max border-collapse">
                <thead>
                    <tr class="bg-purple-700 text-white text-sm uppercase text-center">
                        <th class="p-4">No</th>
                        <th class="p-4 text-left">Nama</th>
                        <th class="p-4 text-left">Perusahaan</th>
                        <th class="p-4">Kode</th>
                        <th class="p-4">Email</th>
                        <th class="p-4">Telepon</th>
                        <th class="p-4 text-left">Alamat</th>
                        <th class="p-4">Status</th>
                        <th class="p-4">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse ($tenants as $index => $tenant)
                    <tr class="hover:bg-gray-50 text-center">
                        <td class="p-4 font-semibold">{{ $index + 1 }}</td>
                        <td class="p-4 text-left whitespace-nowrap">
                            <i class="fa-solid fa-user text-purple-600 mr-2"></i> {{ $tenant->nama }}
                        </td>
                        <td class="p-4 text-left text-purple-600 font-medium">
                            <i class="fa-solid fa-building text-blue-500 mr-2"></i> {{ $tenant->nama_perusahaan }}
                        </td>
                        <td class="p-4 font-semibold">
                            <i class="fa-solid fa-key text-gray-600 mr-2"></i> {{ $tenant->kode }}
                        </td>
                        <td class="p-4 text-left">
                            <i class="fa-solid fa-envelope text-blue-600 mr-2"></i> {{ $tenant->email }}
                        </td>
                        <td class="p-4">
                            <i class="fa-solid fa-phone text-green-600"></i> {{ $tenant->telepon }}
                        </td>
                        <td class="p-4 text-left">
                            <i class="fa-solid fa-map-marker-alt text-red-600 mr-2"></i> {{ $tenant->alamat }}
                        </td>
                        <td class="p-4">
                            @if ($tenant->status == 'pending')
                            <span
                                class="px-3 py-1 text-xs font-medium text-yellow-800 bg-yellow-100 rounded-full">
                                <i class="fa-solid fa-clock mr-1"></i> Pending
                            </span>
                            @elseif ($tenant->status == 'aktif')
                            <span
                                class="px-3 py-1 text-xs font-medium text-green-800 bg-green-100 rounded-full">
                                <i class="fa-solid fa-circle-check mr-1"></i> Aktif
                            </span>
                            @else
                            <span class="px-3 py-1 text-xs font-medium text-red-800 bg-red-100 rounded-full">
                                <i class="fa-solid fa-circle-xmark mr-1"></i> Nonaktif
                            </span>
                            @endif
                        </td>
                        <td class="p-4">
                            <div class="flex justify-center gap-2">
                                @if ($tenant->status === 'pending')
                                <button onclick="editTenant({{ $tenant->id }})"
                                    class="px-3 py-1 text-sm font-semibold text-white bg-yellow-500 hover:bg-yellow-600 rounded-md shadow flex items-center space-x-1">
                                    <i class="fa-solid fa-pen"></i>
                                    <span>Edit</span>
                                </button>
                                @endif
                                <form action="{{ route('pengelola.destroy', $tenant->id) }}" method="POST"
                                    onsubmit="return confirm('Hapus tenant ini? Semua user akan dilepas dari tenant.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="px-3 py-1 text-sm font-semibold text-white bg-red-500 hover:bg-red-600 rounded-md shadow flex items-center space-x-1">
                                        <i class="fa-solid fa-trash"></i>
                                        <span>Hapus</span>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="p-4 text-center text-gray-500">
                            <i class="fa-solid fa-folder-open mr-2"></i> Belum ada tenant yang ditambahkan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>


    <!-- Modal Tambah Tenant -->
    <div id="addTenantModal"
        class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center opacity-0 pointer-events-none transition-opacity duration-300 z-50">
        <div class="bg-white p-6 rounded shadow-lg w-1/3">
            <h2 class="text-xl font-bold mb-4">Pendaftaran Tenant</h2>
            <form action="{{ route('pengelola.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="block text-sm font-medium">Nama</label>
                    <input type="text" name="nama" class="w-full border rounded p-2 bg-gray-200"
                        value="{{ auth()->user()->nama }}" readonly required>
                </div>

                <div class="mb-3">
                    <label class="block text-sm font-medium">Nama Perusahaan</label>
                    <input type="text" name="nama_perusahaan" class="w-full border rounded p-2" required>
                </div>
                <div class="mb-3">
                    <label class="block text-sm font-medium">Kode</label>
                    <input type="text" name="kode" class="w-full border rounded p-2" required>
                </div>
                <div class="mb-3">
                    <label class="block text-sm font-medium">Email</label>
                    <input type="email" name="email" class="w-full border rounded p-2 bg-gray-200"
                        value="{{ auth()->user()->email }}" readonly required>
                </div>
                <div class="mb-3">
                    <label class="block text-sm font-medium">Telepon</label>
                    <input type="text" name="telepon" class="w-full border rounded p-2">
                </div>
                <div class="mb-3">
                    <label class="block text-sm font-medium">Alamat</label>
                    <textarea name="alamat" class="w-full border rounded p-2"></textarea>
                </div>
                <button type="submit"
                    class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 transition">Simpan</button>
                <button type="button"
                    class="ml-2 bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 transition"
                    onclick="closeModal('addTenantModal')">Batal</button>
            </form>
        </div>
    </div>

    <!-- Modal Edit Tenant -->
    <div id="editTenantModal"
        class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center opacity-0 pointer-events-none transition-opacity duration-300 z-50">
        <div class="bg-white p-6 rounded shadow-lg w-1/3">
            <h2 class="text-xl font-bold mb-4">Edit Tenant</h2>
            <form id="editTenantForm" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" id="editTenantId" name="id">
                <div class="mb-3">
                    <label class="block text-sm font-medium">Nama</label>
                    <input type="text" id="editNama" name="nama"
                        class="w-full border rounded p-2 bg-gray-200" readonly required>
                </div>
                <div class="mb-3">
                    <label class="block text-sm font-medium">Nama Perusahaan</label>
                    <input type="text" id="editNamaPerusahaan" name="nama_perusahaan"
                        class="w-full border rounded p-2" required>
                </div>
                <div class="mb-3">
                    <label class="block text-sm font-medium">Kode</label>
                    <input type="text" id="editKode" name="kode" class="w-full border rounded p-2" required>
                </div>
                <div class="mb-3">
                    <label class="block text-sm font-medium">Email</label>
                    <input type="email" id="editEmail" name="email"
                        class="w-full border rounded p-2 bg-gray-200" readonly required>
                </div>
                <div class="mb-3">
                    <label class="block text-sm font-medium">Telepon</label>
                    <input type="text" id="editTelepon" name="telepon" class="w-full border rounded p-2">
                </div>
                <div class="mb-3">
                    <label class="block text-sm font-medium">Alamat</label>
                    <textarea id="editAlamat" name="alamat" class="w-full border rounded p-2"></textarea>
                </div>
                <button type="submit"
                    class="bg-yellow-500 text-white px-4 py-2 rounded hover:bg-yellow-600 transition">Update</button>
                <button type="button"
                    class="ml-2 bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 transition"
                    onclick="closeModal('editTenantModal')">Batal</button>
            </form>

        </div>
    </div>

    <script>
        function openModal(id) {
            let modal = document.getElementById(id);
            modal.classList.remove('opacity-0', 'pointer-events-none');
            modal.classList.add('opacity-100');
        }

        function closeModal(id) {
            let modal = document.getElementById(id);
            modal.classList.remove('opacity-100');
            modal.classList.add('opacity-0', 'pointer-events-none');
        }

        function editTenant(id) {
            fetch(`/pengelola/${id}/edit`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('editTenantId').value = data.id;
                    document.getElementById('editNama').value = data.nama;
                    document.getElementById('editNamaPerusahaan').value = data.nama_perusahaan;
                    document.getElementById('editKode').value = data.kode;
                    document.getElementById('editEmail').value = data.email;
                    document.getElementById('editTelepon').value = data.telepon;
                    document.getElementById('editAlamat').value = data.alamat;
                    document.getElementById('editTenantForm').action = `/pengelola/${id}/update`;
                    openModal('editTenantModal'); // Pastikan ini ID modal edit
                });
        }
    </script>
</div>
@endsection