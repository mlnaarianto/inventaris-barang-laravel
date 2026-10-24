@extends('layouts.app')

@section('title', 'Tambah Role')

@section('content')
<div class="max-w-xl mx-auto py-6">
    
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl sm:text-3xl font-bold text-slate-900 dark:text-white flex items-center gap-3">
            <span class="p-2 bg-brand-50 dark:bg-brand-500/10 rounded-lg text-brand-600 dark:text-brand-400 border border-brand-100 dark:border-brand-500/20">
                <i class="fa-solid fa-plus"></i>
            </span>
            Tambah Role
        </h1>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-2">Buat role baru untuk mengelompokkan hak akses pengguna.</p>
    </div>

    <!-- Form Card -->
    <form method="POST" action="{{ route('roles.store') }}" class="bg-white dark:bg-[#0b1220] border border-slate-200 dark:border-slate-800 rounded-xl shadow-sm p-6 space-y-5">
        @csrf

        <div>
            <label for="name" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Nama Role</label>
            <input type="text" id="name" name="name" value="{{ old('name') }}" required autofocus
                placeholder="contoh: supervisor"
                class="w-full bg-slate-50 dark:bg-slate-800/50 border text-slate-900 dark:text-white text-sm rounded-lg focus:ring-2 focus:ring-brand-500/50 focus:border-brand-500 p-2.5 outline-none transition-all placeholder:text-slate-400 dark:placeholder:text-slate-500
                {{ $errors->has('name') ? 'border-rose-500 focus:border-rose-500 focus:ring-rose-500/50' : 'border-slate-200 dark:border-slate-700' }}">
            
            <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-2 flex items-center gap-1.5">
                <i class="fa-solid fa-circle-info text-brand-500"></i>
                Huruf, angka, strip, dan garis bawah saja (tanpa spasi). Permission diatur setelah role dibuat.
            </p>
            
            @error('name')
                <p class="text-xs text-rose-500 mt-1.5 font-medium">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center gap-3 pt-4 border-t border-slate-100 dark:border-slate-800">
            <button class="inline-flex items-center justify-center gap-2 bg-brand-600 text-white px-5 py-2.5 rounded-lg shadow-[0_4px_14px_0_rgba(6,182,212,0.39)] hover:bg-brand-500 transition-all font-semibold text-sm">
                <i class="fa-solid fa-floppy-disk"></i> Simpan
            </button>
            <a href="{{ route('roles.index') }}" class="inline-flex items-center justify-center px-5 py-2.5 rounded-lg text-sm font-semibold bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700 transition-colors">
                Batal
            </a>
        </div>
    </form>
</div>
@endsection