@extends('layouts.app')

@section('title', 'Permission Role')

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">
            Permission Role: <span class="text-indigo-600">{{ $role->name }}</span>
        </h1>
        <p class="text-sm text-gray-500 mt-1">Centang hak akses yang diberikan. Yang tidak dicentang akan dicabut.</p>
    </div>

    @if ($errors->any())
        <div class="mb-4 p-3 rounded-lg bg-red-100 text-red-700 text-sm">{{ $errors->first() }}</div>
    @endif

    @php($terpilih = old('permissions', $rolePermissions))

    <form method="POST" action="{{ route('roles.permissions.update', $role) }}">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            @foreach ($permissions as $modul => $items)
                <div class="bg-white rounded-lg shadow p-4"
                    x-data="{
                        toggle() {
                            const cb = this.$root.querySelectorAll('input[type=checkbox]');
                            const semua = [...cb].every(c => c.checked);
                            cb.forEach(c => c.checked = !semua);
                        }
                    }">
                    <div class="flex items-center justify-between border-b pb-2 mb-3">
                        <h2 class="font-semibold text-gray-800">{{ $modul }}</h2>
                        <button type="button" @click="toggle()" class="text-xs text-blue-600 hover:underline">
                            Pilih / batalkan semua
                        </button>
                    </div>

                    <div class="space-y-2">
                        @foreach ($items as $permission)
                            <label class="flex items-start gap-2 cursor-pointer text-sm">
                                <input type="checkbox" name="permissions[]" value="{{ $permission->name }}"
                                    class="mt-1 rounded border-gray-300"
                                    @checked(in_array($permission->name, $terpilih))>
                                <span class="text-gray-700">
                                    {{ $permission->label }}
                                    @if ($permission->isBroadAccess)
                                        <span class="ml-1 text-xs bg-red-100 text-red-700 px-2 py-0.5 rounded-full">
                                            <i class="fa-solid fa-triangle-exclamation"></i> Akses luas
                                        </span>
                                    @endif
                                </span>
                            </label>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>

        <div class="flex gap-2 mt-6">
            <button class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg text-sm">
                <i class="fa-solid fa-floppy-disk"></i> Simpan Permission
            </button>
            <a href="{{ route('roles.index') }}" class="px-5 py-2 rounded-lg text-sm bg-gray-200 hover:bg-gray-300 text-gray-700">Kembali</a>
        </div>
    </form>
@endsection