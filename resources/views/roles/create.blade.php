@extends('layouts.app')

@section('title', 'Tambah Role')

@section('content')
    <div class="max-w-xl">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Tambah Role</h1>

        <form method="POST" action="{{ route('roles.store') }}" class="bg-white rounded-lg shadow p-6 space-y-4">
            @csrf

            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama Role</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required autofocus
                    placeholder="contoh: supervisor"
                    class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400
                    {{ $errors->has('name') ? 'border-red-500' : 'border-gray-300' }}">
                <p class="text-xs text-gray-500 mt-1">Huruf, angka, strip, dan garis bawah saja (tanpa spasi). Permission diatur setelah role dibuat.</p>
                @error('name')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex gap-2">
                <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm">Simpan</button>
                <a href="{{ route('roles.index') }}" class="px-4 py-2 rounded-lg text-sm bg-gray-200 hover:bg-gray-300 text-gray-700">Batal</a>
            </div>
        </form>
    </div>
@endsection