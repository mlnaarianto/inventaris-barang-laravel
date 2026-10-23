@extends('layouts.app')

@section('title', 'Manajemen Role')

@section('content')
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Manajemen Role</h1>

        @can('role.create')
            <a href="{{ route('roles.create') }}"
                class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm">
                <i class="fa-solid fa-plus"></i> Tambah Role
            </a>
        @endcan
    </div>

    @if ($errors->any())
        <div class="mb-4 p-3 rounded-lg bg-red-100 text-red-700 text-sm">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="GET" action="{{ route('roles.index') }}" class="mb-4 flex gap-2">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama role..."
            class="w-full md:w-72 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
        <button class="bg-gray-700 hover:bg-gray-800 text-white px-4 py-2 rounded-lg text-sm">
            <i class="fa-solid fa-magnifying-glass"></i>
        </button>
    </form>

    <div class="bg-white rounded-lg shadow overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50 text-gray-600 text-left">
                <tr>
                    <th class="px-4 py-3">Nama Role</th>
                    <th class="px-4 py-3 text-center">Permission</th>
                    <th class="px-4 py-3 text-center">User</th>
                    <th class="px-4 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse ($roles as $role)
                    @php($inti = in_array($role->name, $protectedRoles))
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-medium text-gray-800">
                            {{ $role->name }}
                            @if ($inti)
                                <span class="ml-2 text-xs bg-gray-200 text-gray-600 px-2 py-0.5 rounded-full">inti</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center">{{ $role->permissions_count }}</td>
                        <td class="px-4 py-3 text-center">{{ $role->users_count }}</td>
                        <td class="px-4 py-3">
                            <div class="flex justify-end items-center gap-2">
                                @can('role.update')
                                    @if ($role->name === 'admin')
                                        <span class="text-xs text-gray-400" title="Admin selalu full access">
                                            <i class="fa-solid fa-lock"></i> Terkunci
                                        </span>
                                    @else
                                        <a href="{{ route('roles.permissions.edit', $role) }}"
                                            class="px-3 py-1 rounded bg-indigo-100 text-indigo-700 hover:bg-indigo-200">
                                            <i class="fa-solid fa-key"></i> Permission
                                        </a>
                                    @endif

                                    @unless ($inti)
                                        <a href="{{ route('roles.edit', $role) }}"
                                            class="px-3 py-1 rounded bg-yellow-100 text-yellow-700 hover:bg-yellow-200">
                                            <i class="fa-solid fa-pen"></i> Edit
                                        </a>
                                    @endunless
                                @endcan

                                @can('role.delete')
                                    @unless ($inti)
                                        <form method="POST" action="{{ route('roles.destroy', $role) }}"
                                            onsubmit="return konfirmasiHapus(event, this, '{{ $role->name }}')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="px-3 py-1 rounded bg-red-100 text-red-700 hover:bg-red-200">
                                                <i class="fa-solid fa-trash"></i> Hapus
                                            </button>
                                        </form>
                                    @endunless
                                @endcan
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-8 text-center text-gray-500">Belum ada role.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $roles->links() }}</div>
@endsection

@push('scripts')
    <script>
        function konfirmasiHapus(e, form, nama) {
            e.preventDefault();
            Swal.fire({
                title: 'Hapus role "' + nama + '"?',
                text: 'Tindakan ini tidak bisa dibatalkan.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, hapus',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#dc2626'
            }).then(r => { if (r.isConfirmed) form.submit(); });
            return false;
        }
    </script>
@endpush