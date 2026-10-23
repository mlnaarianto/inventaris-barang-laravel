@extends('layouts.app')

@section('content')
@php
$bisaAksi = auth()->user()->canany(['user.update', 'user.delete']);
@endphp

<div x-data="{ isOpen: false }" class="container mx-auto py-6">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-3xl font-semibold text-gray-800 flex items-center space-x-2">
            <i class="fa-solid fa-house text-purple-600"></i>
            <span>Dashboard</span>
        </h1>

        @can('user.create')
        <button @click="isOpen = true"
            class="bg-blue-600 text-white px-6 py-3 rounded-lg shadow-lg hover:bg-blue-700 transition duration-200">
            <i class="fas fa-plus"></i> Tambah User
        </button>
        @endcan
    </div>

    <!-- Statistik Cards -->
    <div class="bg-white shadow-md rounded-lg p-6 mb-6 overflow-x-auto">
        <div class="flex space-x-4 flex-nowrap min-w-full">
            <!-- Data Tenant Aktif -->
            <div class="bg-white p-6 rounded-lg shadow-md flex items-center border-l-8 border-blue-500 min-w-[250px]">
                <div class="text-blue-500 text-4xl mr-4">📂</div>
                <div>
                    <p class="text-gray-600">Data Tenant Aktif</p>
                    <p class="text-2xl font-bold">{{ $tenantAktif }}</p>
                </div>
            </div>

            <!-- Data Tenant Nonaktif -->
            <div class="bg-white p-6 rounded-lg shadow-md flex items-center border-l-8 border-red-500 min-w-[250px]">
                <div class="text-red-500 text-4xl mr-4">🚫</div>
                <div>
                    <p class="text-gray-600">Data Tenant Nonaktif</p>
                    <p class="text-2xl font-bold">{{ $tenantNonaktif }}</p>
                </div>
            </div>

            <!-- Data User -->
            <div class="bg-white p-6 rounded-lg shadow-md flex items-center border-l-8 border-orange-500 min-w-[250px]">
                <div class="text-orange-500 text-4xl mr-4">👤</div>
                <div>
                    <p class="text-gray-600">Total User</p>
                    <p class="text-2xl font-bold">{{ $userCount }}</p>
                </div>
            </div>

            <!-- Data Pengelola -->
            <div class="bg-white p-6 rounded-lg shadow-md flex items-center border-l-8 border-green-500 min-w-[250px]">
                <div class="text-green-500 text-4xl mr-4">👨‍💼</div>
                <div>
                    <p class="text-gray-600">Total Pengelola</p>
                    <p class="text-2xl font-bold">{{ $pengelolaCount }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Users Table -->
    <div class="bg-white shadow-md rounded-lg p-6 mx-auto">
        <div class="flex items-center space-x-2 mb-4">
            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
            <span class="text-lg font-semibold text-gray-700">Daftar Pengguna</span>
        </div>
        <table id="tabelPengguna" class="display w-full text-sm text-gray-700">
            <thead class="bg-purple-700 text-white uppercase">
                <tr>
                    <th class="py-3 px-6 text-left">No</th>
                    <th class="py-3 px-6 text-left">Nama</th>
                    <th class="py-3 px-6 text-left">Email</th>
                    <th class="py-3 px-6 text-left">Role</th>
                    <th class="py-3 px-6 text-left">Status</th>
                    @if ($bisaAksi)
                    <th class="py-3 px-6 text-left">Aksi</th>
                    @endif
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach ($users as $index => $user)
                <tr class="hover:bg-gray-50 transition">
                    <td class="py-3 px-6">{{ $index + 1 }}</td>
                    <td class="py-3 px-6">{{ $user->nama }}</td>
                    <td class="py-3 px-6">{{ $user->email }}</td>
                    <td class="py-3 px-6">{{ ucfirst($user->getRoleNames()->first() ?? '-') }}</td>
                    <td class="py-3 px-6">
                        <span class="px-2 py-1 rounded text-white {{ $user->status === 'tidak_aktif' ? 'bg-red-500' : ($user->status === 'pending' ? 'bg-yellow-500' : 'bg-green-500') }}">
                            {{ ucfirst(str_replace('_', ' ', $user->status)) }}
                        </span>
                    </td>
                    @if ($bisaAksi)
                    <td class="py-3 px-6">
                        <div class="flex space-x-2">
                            @can('user.update')
                            <form action="{{ route('user.updateStatus', $user->id) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="aktif">
                                <button type="submit" class="bg-green-500 text-white px-3 py-1 rounded hover:bg-green-600">Aktif</button>
                            </form>
                            <form action="{{ route('user.updateStatus', $user->id) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="tidak_aktif">
                                <button type="submit" class="bg-yellow-500 text-white px-3 py-1 rounded hover:bg-yellow-600">Nonaktif</button>
                            </form>
                            @endcan

                            @can('user.delete')
                            <form action="{{ route('users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Hapus user ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600">Hapus</button>
                            </form>
                            @endcan
                        </div>
                    </td>
                    @endif
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @can('user.create')
    <!-- Modal Tambah User -->
    <div x-show="isOpen" x-cloak class="fixed inset-0 z-50 bg-gray-600 bg-opacity-50 flex justify-center items-center">
        <div class="bg-white p-6 rounded-lg shadow-lg w-1/3">
            <h2 class="text-xl font-bold mb-4 text-gray-800">Tambah User</h2>
            <form action="{{ route('users.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700">Nama</label>
                    <input type="text" name="nama" class="w-full border p-2 rounded focus:ring-2 focus:ring-blue-500" required>
                </div>
                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" name="email" class="w-full border p-2 rounded focus:ring-2 focus:ring-blue-500" required>
                </div>
                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700">Password</label>
                    <input type="password" name="password" class="w-full border p-2 rounded focus:ring-2 focus:ring-blue-500" required>
                </div>
                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700">Role</label>
                    <select name="role" class="w-full border p-2 rounded focus:ring-2 focus:ring-blue-500" required>
                        @foreach ($roleOptions as $roleName)
                        <option value="{{ $roleName }}">{{ ucfirst($roleName) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700">Status</label>
                    <select name="status" class="w-full border p-2 rounded focus:ring-2 focus:ring-blue-500" required>
                        <option value="aktif">Aktif</option>
                        <option value="tidak_aktif">Tidak Aktif</option>
                        <option value="pending">Pending</option>
                    </select>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" @click="isOpen = false" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">Batal</button>
                    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Simpan</button>
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
        $('#tabelPengguna').DataTable({
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
    });
</script>
@endpush