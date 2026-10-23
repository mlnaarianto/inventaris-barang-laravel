@extends('layouts.app')

@section('title', 'Edit Role')

@section('content')
    <div class="max-w-xl">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Edit Role</h1>

        @if ($errors->has('role'))
            <div class="mb-4 p-3 rounded-lg bg-red-100 text-red-700 text-sm">{{ $errors->first('role') }}</div>
        @endif

        <form method="POST" action="{{ route('roles.update', $role) }}" class="bg-white rounded-lg shadow p-6 space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama Role</label>
                <input type="text" id="name" name="name" value="{{ old('name', $role->name) }}" required autofocus
                    class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400
                    {{ $errors->has('name') ? 'border-red-500' : 'border-gray-300' }}">
                @error('name')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex gap-2">
                <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm">Perbarui</button>
                <a href="{{ route('roles.index') }}" class="px-4 py-2 rounded-lg text-sm bg-gray-200 hover:bg-gray-300 text-gray-700">Batal</a>
            </div>
        </form>
    </div>
@endsection