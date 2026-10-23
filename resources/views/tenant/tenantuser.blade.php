@extends('layouts.app')

@section('title', 'Daftar Tenant - Inventaris Barang')

@section('content')
    <div class="container mx-auto py-6">
        <div class="flex justify-between items-center mb-4">
            <h1 class="text-3xl font-semibold text-gray-800 flex items-center space-x-2">
                <i class="fa-solid fa-building text-purple-600"></i>
                <span>Daftar Tenant</span>
            </h1>
        </div>
        <hr class="mb-4 border-gray-300">

        <div class="bg-white shadow-md rounded-lg p-6 mx-auto">
            <div class="flex items-center space-x-2 mb-4">
                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
                <span class="text-lg font-semibold text-gray-700">Daftar Tenant</span>
            </div>
            <div class="overflow-x-auto">
                <table id="tabelTenant" class="display w-full text-sm text-gray-700 whitespace-nowrap">
                    <thead class="bg-purple-700 text-white uppercase">
                        <tr>
                            <th class="py-3 px-6 text-left">Nama Tenant</th>
                            <th class="py-3 px-6 text-left">Email</th>
                            <th class="py-3 px-6 text-left">Perusahaan</th>
                            <th class="py-3 px-6">Kode</th>
                            <th class="py-3 px-6">Telepon</th>
                            <th class="py-3 px-6 text-left">Alamat</th>
                            <th class="py-3 px-6">Status</th>
                            <th class="py-3 px-6">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach ($tenants as $tenant)
                            <tr class="hover:bg-gray-50">
                                <td class="py-3 px-6 text-left">
                                    <div class="flex items-center">
                                        <i class="fa-solid fa-user text-purple-600 mr-2"></i>
                                        {{ $tenant->nama }}
                                    </div>
                                </td>
                                <td class="py-3 px-6 text-left">
                                    <div class="flex items-center">
                                        <i class="fa-solid fa-envelope text-blue-600 mr-2"></i>
                                        {{ $tenant->email }}
                                    </div>
                                </td>
                                <td class="py-3 px-6 text-left text-purple-600 font-medium">
                                    <div class="flex items-center">
                                        <i class="fa-solid fa-building text-blue-500 mr-2"></i>
                                        {{ $tenant->nama_perusahaan }}
                                    </div>
                                </td>
                                <td class="py-3 px-6 font-semibold">
                                    <div class="flex items-center justify-center">
                                        <i class="fa-solid fa-key text-gray-600 mr-2"></i>
                                        {{ $tenant->kode }}
                                    </div>
                                </td>
                                <td class="py-3 px-6">
                                    <div class="flex items-center justify-center">
                                        <i class="fa-solid fa-phone text-green-600 mr-2"></i>
                                        {{ $tenant->telepon }}
                                    </div>
                                </td>
                                <td class="py-3 px-6 text-left">
                                    <div class="flex items-center">
                                        <i class="fa-solid fa-map-marker-alt text-red-500 mr-2"></i>
                                        {{ $tenant->alamat }}
                                    </div>
                                </td>
                                <td class="py-3 px-6">
                                    @if ($tenant->status == 'pending')
                                        <span
                                            class="px-3 py-1 text-xs font-medium text-yellow-800 bg-yellow-100 rounded-full flex items-center justify-center w-max mx-auto">
                                            <i class="fa-solid fa-clock mr-1"></i> Pending
                                        </span>
                                    @elseif ($tenant->status == 'aktif')
                                        <span
                                            class="px-3 py-1 text-xs font-medium text-green-800 bg-green-100 rounded-full flex items-center justify-center w-max mx-auto">
                                            <i class="fa-solid fa-circle-check mr-1"></i> Aktif
                                        </span>
                                    @else
                                        <span
                                            class="px-3 py-1 text-xs font-medium text-red-800 bg-red-100 rounded-full flex items-center justify-center w-max mx-auto">
                                            <i class="fa-solid fa-circle-xmark mr-1"></i> Nonaktif
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3 px-6">
                                    <div class="flex justify-center gap-2">
                                        @if ($tenant->status == 'pending' || $tenant->status == 'nonaktif')
                                            <form action="{{ route('tenantuser.approve', $tenant->id) }}" method="POST">
                                                @csrf
                                                <button type="submit"
                                                    class="px-3 py-1 text-sm font-semibold text-white bg-green-500 hover:bg-green-600 rounded-md shadow flex items-center space-x-1">
                                                    <i class="fa-solid fa-check"></i>
                                                    <span>Aktifkan</span>
                                                </button>
                                            </form>
                                        @endif
                                        @if ($tenant->status == 'pending' || $tenant->status == 'aktif')
                                            <form action="{{ route('tenantuser.reject', $tenant->id) }}" method="POST">
                                                @csrf
                                                <button type="submit"
                                                    class="px-3 py-1 text-sm font-semibold text-white bg-red-500 hover:bg-red-600 rounded-md shadow flex items-center space-x-1">
                                                    <i class="fa-solid fa-ban"></i>
                                                    <span>Nonaktifkan</span>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>

                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <!-- jQuery lalu DataTables, baru plugin Responsive -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.4.1/js/dataTables.responsive.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.4.1/css/responsive.dataTables.min.css">

    <script>
        $(document).ready(function() {
            $('#tabelTenant').DataTable({
                responsive: true,
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
                },
                columnDefs: [{
                    orderable: false,
                    targets: [7]
                }]
            });
        });
    </script>
@endpush
