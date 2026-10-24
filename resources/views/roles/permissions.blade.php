@extends('layouts.app')

@section('title', 'Permission Role')

@section('content')
<div class="max-w-7xl mx-auto py-6 space-y-6">

    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-slate-900 dark:text-white flex items-center gap-3">
                <span class="p-2 bg-brand-50 dark:bg-brand-500/10 rounded-lg text-brand-600 dark:text-brand-400 border border-brand-100 dark:border-brand-500/20">
                    <i class="fa-solid fa-key"></i>
                </span>
                Permission Role: <span class="text-brand-600 dark:text-brand-400">{{ $role->name }}</span>
            </h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-2">Centang hak akses yang diberikan. Yang tidak dicentang akan dicabut.</p>
        </div>
    </div>

    @if ($errors->any())
        <div class="p-4 rounded-xl bg-rose-50 dark:bg-rose-500/10 border border-rose-200 dark:border-rose-500/20 text-rose-700 dark:text-rose-400 text-sm flex items-start gap-3 shadow-sm">
            <i class="fa-solid fa-circle-exclamation mt-0.5"></i>
            <div>{{ $errors->first() }}</div>
        </div>
    @endif

    @php($terpilih = old('permissions', $rolePermissions))

    <form method="POST" action="{{ route('roles.permissions.update', $role) }}">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
            @foreach ($permissions as $modul => $items)
                <div class="bg-white dark:bg-[#0b1220] border border-slate-200 dark:border-slate-800 rounded-xl shadow-sm p-5"
                    x-data="{
                        toggle() {
                            const cb = this.$root.querySelectorAll('input[type=checkbox]');
                            const semua = [...cb].every(c => c.checked);
                            cb.forEach(c => c.checked = !semua);
                        }
                    }">
                    
                    <!-- Module Header -->
                    <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 pb-3 mb-4">
                        <h2 class="font-bold text-slate-900 dark:text-white uppercase tracking-wider">{{ $modul }}</h2>
                        <button type="button" @click="toggle()" class="text-[11px] font-semibold text-brand-600 dark:text-brand-400 hover:underline transition-colors">
                            Pilih / batalkan semua
                        </button>
                    </div>

                    <!-- Checkbox List -->
                    <div class="space-y-3">
                        @foreach ($items as $permission)
                            <label class="flex items-start gap-3 cursor-pointer group">
                                <!-- Checkbox -->
                                <div class="relative flex items-center pt-0.5">
                                    <input type="checkbox" name="permissions[]" value="{{ $permission->name }}"
                                        class="peer h-4 w-4 shrink-0 appearance-none rounded border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 checked:border-brand-500 checked:bg-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/30 focus:ring-offset-1 dark:focus:ring-offset-[#0b1220] transition-all cursor-pointer"
                                        @checked(in_array($permission->name, $terpilih))>
                                    <!-- Custom Check Icon -->
                                    <svg class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 w-3 h-3 text-white pointer-events-none opacity-0 peer-checked:opacity-100 transition-opacity" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                                
                                <!-- Label -->
                                <span class="text-sm text-slate-700 dark:text-slate-300 group-hover:text-slate-900 dark:group-hover:text-white transition-colors leading-tight">
                                    {{ $permission->label }}
                                    @if ($permission->isBroadAccess)
                                        <span class="inline-flex items-center gap-1 mt-1 text-[10px] font-bold bg-rose-50 text-rose-700 dark:bg-rose-500/10 dark:text-rose-400 border border-rose-200 dark:border-rose-500/20 px-1.5 py-0.5 rounded-md">
                                            <i class="fa-solid fa-triangle-exclamation"></i> Akses Luas
                                        </span>
                                    @endif
                                </span>
                            </label>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center gap-3 mt-8 pt-6 border-t border-slate-200 dark:border-slate-800">
            <button class="inline-flex items-center justify-center gap-2 bg-brand-600 text-white px-6 py-2.5 rounded-lg shadow-[0_4px_14px_0_rgba(6,182,212,0.39)] hover:bg-brand-500 transition-all font-semibold text-sm">
                <i class="fa-solid fa-floppy-disk"></i> Simpan Permission
            </button>
            <a href="{{ route('roles.index') }}" class="inline-flex items-center justify-center px-6 py-2.5 rounded-lg text-sm font-semibold bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700 transition-colors">
                Kembali
            </a>
        </div>
    </form>
</div>
@endsection