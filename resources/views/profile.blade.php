@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto py-6 space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-slate-900 dark:text-white flex items-center gap-3">
                <span class="p-2 bg-brand-50 dark:bg-brand-500/10 rounded-lg text-brand-600 dark:text-brand-400 border border-brand-100 dark:border-brand-500/20">
                    <i class="fa-solid fa-user-gear"></i>
                </span>
                Profil Saya
            </h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-2">Kelola informasi akun, foto profil, dan keamanan Anda.</p>
        </div>
        
        <a href="{{ $backUrl }}"
            class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors font-semibold text-sm border border-slate-200 dark:border-slate-700">
            <i class="fa-solid fa-arrow-left"></i> Kembali
        </a>
    </div>

    <!-- ================= FOTO PROFIL ================= -->
    <section class="bg-white dark:bg-[#0b1220] border border-slate-200 dark:border-slate-800 rounded-xl shadow-sm p-6 sm:p-8">
        <div class="flex items-center gap-3 border-b border-slate-200 dark:border-slate-800 pb-4 mb-6">
            <div class="p-2 bg-slate-100 dark:bg-slate-800 rounded-lg text-slate-600 dark:text-slate-300">
                <i class="fa-solid fa-image"></i>
            </div>
            <h2 class="text-lg font-bold text-slate-900 dark:text-white uppercase tracking-wider">Foto Profil</h2>
        </div>

        <div x-data="{ preview: null }" class="flex flex-col sm:flex-row items-center gap-8">
            <!-- Avatar Preview -->
            <div class="relative group shrink-0">
                <div class="absolute -inset-1 bg-gradient-to-r from-brand-400 to-blue-500 rounded-full blur opacity-25 group-hover:opacity-50 transition duration-300"></div>
                <img :src="preview ?? '{{ $user->avatar_url ?: asset('images/1.jpg') }}'"
                    onerror="this.src='{{ asset('images/1.jpg') }}'"
                    class="relative w-32 h-32 rounded-full object-cover border-4 border-white dark:border-[#0b1220] shadow-lg" alt="Foto profil">
            </div>

            <div class="flex-1 w-full space-y-4">
                <form method="POST" action="{{ route('profile.avatar') }}" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    
                    <div>
                        <input type="file" name="avatar" accept="image/png,image/jpeg,image/webp" required
                            @change="preview = $event.target.files[0] ? URL.createObjectURL($event.target.files[0]) : null"
                            class="block w-full text-sm text-slate-500 dark:text-slate-400 file:mr-4 file:py-2.5 file:px-4 file:rounded-lg file:border-0 file:bg-brand-50 file:text-brand-700 dark:file:bg-brand-500/10 dark:file:text-brand-400 hover:file:bg-brand-100 dark:hover:file:bg-brand-500/20 file:transition-colors file:font-semibold file:cursor-pointer">
                        <p class="text-[11px] text-slate-500 mt-2">Format JPG, PNG, atau WebP. Maksimal 2 MB.</p>
                    </div>
                    
                    <button type="submit"
                        class="inline-flex items-center gap-2 px-5 py-2.5 bg-brand-600 text-white rounded-lg shadow-[0_4px_14px_0_rgba(6,182,212,0.39)] hover:bg-brand-500 hover:shadow-[0_6px_20px_rgba(6,182,212,0.23)] transition-all font-semibold text-sm">
                        <i class="fa-solid fa-upload"></i> Simpan Foto
                    </button>
                </form>

                @if ($user->avatar)
                    <form id="form-hapus-foto" method="POST" action="{{ route('profile.avatar.destroy') }}" class="pt-2 border-t border-slate-100 dark:border-slate-800/50">
                        @csrf
                        @method('DELETE')
                        <button type="button" onclick="konfirmasiHapusFoto()"
                            class="text-sm font-semibold text-rose-500 hover:text-rose-600 transition-colors flex items-center gap-1.5">
                            <i class="fa-solid fa-trash"></i> Hapus foto profil
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </section>

    <!-- ================= DATA DIRI ================= -->
    <section class="bg-white dark:bg-[#0b1220] border border-slate-200 dark:border-slate-800 rounded-xl shadow-sm p-6 sm:p-8">
        <div class="flex items-center gap-3 border-b border-slate-200 dark:border-slate-800 pb-4 mb-6">
            <div class="p-2 bg-slate-100 dark:bg-slate-800 rounded-lg text-slate-600 dark:text-slate-300">
                <i class="fa-solid fa-address-card"></i>
            </div>
            <h2 class="text-lg font-bold text-slate-900 dark:text-white uppercase tracking-wider">Data Diri</h2>
        </div>

        <form method="POST" action="{{ route('profile.update') }}" class="space-y-5">
            @csrf
            @method('PUT')

            <div>
                <label for="nama" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Nama Lengkap</label>
                <input type="text" id="nama" name="nama" value="{{ old('nama', $user->nama) }}" required
                    class="w-full bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white text-sm rounded-lg focus:ring-2 focus:ring-brand-500/50 focus:border-brand-500 p-2.5 outline-none transition-all">
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Email</label>
                <input type="email" value="{{ $user->email }}" disabled
                    class="w-full bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-500 dark:text-slate-400 text-sm rounded-lg p-2.5 outline-none cursor-not-allowed">
                <p class="text-[11px] text-slate-500 mt-1.5"><i class="fa-solid fa-circle-info mr-1"></i> Email tidak dapat diubah karena dipakai untuk login.</p>
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Role</label>
                <input type="text" value="{{ ucfirst($user->getRoleNames()->first()) }}" disabled
                    class="w-full bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-500 dark:text-slate-400 text-sm rounded-lg p-2.5 outline-none cursor-not-allowed capitalize">
            </div>

            <div class="pt-2">
                <button type="submit"
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-brand-600 text-white rounded-lg shadow-[0_4px_14px_0_rgba(6,182,212,0.39)] hover:bg-brand-500 hover:shadow-[0_6px_20px_rgba(6,182,212,0.23)] transition-all font-semibold text-sm">
                    <i class="fa-solid fa-floppy-disk"></i> Simpan Perubahan
                </button>
            </div>
        </form>
    </section>

    <!-- ================= GANTI PASSWORD ================= -->
    <section class="bg-white dark:bg-[#0b1220] border border-slate-200 dark:border-slate-800 rounded-xl shadow-sm p-6 sm:p-8">
        <div class="flex items-center gap-3 border-b border-slate-200 dark:border-slate-800 pb-4 mb-6">
            <div class="p-2 bg-slate-100 dark:bg-slate-800 rounded-lg text-slate-600 dark:text-slate-300">
                <i class="fa-solid fa-shield-halved"></i>
            </div>
            <h2 class="text-lg font-bold text-slate-900 dark:text-white uppercase tracking-wider">Ganti Password</h2>
        </div>

        @if ($hasPassword)
            <form method="POST" action="{{ route('password.change') }}" class="space-y-5"
                x-data="{
                    show: false,
                    password: '',
                    get rLen() { return this.password.length >= 8 },
                    get rCap() { return /^[A-Z]/.test(this.password) },
                    get rNum() { return /\d/.test(this.password) },
                    get rAlnum() { return this.password.length > 0 && /^[A-Za-z0-9]+$/.test(this.password) },
                    get score() { return [this.rLen, this.rCap, this.rNum, this.rAlnum].filter(Boolean).length }
                }">
                @csrf

                <div>
                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Password Saat Ini</label>
                    <input :type="show ? 'text' : 'password'" name="current_password" required
                        class="w-full bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white text-sm rounded-lg focus:ring-2 focus:ring-brand-500/50 focus:border-brand-500 p-2.5 outline-none transition-all">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Password Baru</label>
                    <input :type="show ? 'text' : 'password'" name="new_password" x-model="password" required
                        class="w-full bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white text-sm rounded-lg focus:ring-2 focus:ring-brand-500/50 focus:border-brand-500 p-2.5 outline-none transition-all">

                    <!-- Strength bar -->
                    <div class="w-full h-1.5 mt-3 rounded-full bg-slate-200 dark:bg-slate-700 overflow-hidden">
                        <div class="h-full transition-all duration-300 ease-out" :style="`width: ${score * 25}%`"
                            :class="{
                                'bg-rose-500': score > 0 && score <= 2,
                                'bg-amber-400': score === 3,
                                'bg-emerald-500': score === 4
                            }">
                        </div>
                    </div>
                    <p class="text-xs mt-1.5 font-medium" :class="score === 0 ? 'text-slate-400' : (score <= 2 ? 'text-rose-500' : (score === 3 ? 'text-amber-500' : 'text-emerald-500'))"
                        x-text="score === 0 ? '' : score <= 2 ? 'Password lemah' : score === 3 ? 'Password cukup' : 'Password kuat'">
                    </p>

                    <!-- Syarat password -->
                    <div class="mt-4 p-4 rounded-lg bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 text-xs">
                        <p class="font-bold text-slate-700 dark:text-slate-300 mb-2 uppercase tracking-wider">Syarat password:</p>
                        <ul class="space-y-1.5">
                            <li :class="rLen ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-500 dark:text-slate-400'" class="flex items-center gap-2 transition-colors">
                                <i :class="rLen ? 'fa-solid fa-circle-check' : 'fa-regular fa-circle'"></i> Minimal 8 karakter
                            </li>
                            <li :class="rCap ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-500 dark:text-slate-400'" class="flex items-center gap-2 transition-colors">
                                <i :class="rCap ? 'fa-solid fa-circle-check' : 'fa-regular fa-circle'"></i> Diawali huruf kapital
                            </li>
                            <li :class="rNum ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-500 dark:text-slate-400'" class="flex items-center gap-2 transition-colors">
                                <i :class="rNum ? 'fa-solid fa-circle-check' : 'fa-regular fa-circle'"></i> Mengandung minimal satu angka
                            </li>
                            <li :class="rAlnum ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-500 dark:text-slate-400'" class="flex items-center gap-2 transition-colors">
                                <i :class="rAlnum ? 'fa-solid fa-circle-check' : 'fa-regular fa-circle'"></i> Hanya huruf dan angka (tanpa spasi/simbol)
                            </li>
                        </ul>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Konfirmasi Password Baru</label>
                    <input :type="show ? 'text' : 'password'" name="new_password_confirmation" required
                        class="w-full bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white text-sm rounded-lg focus:ring-2 focus:ring-brand-500/50 focus:border-brand-500 p-2.5 outline-none transition-all">
                </div>

                <label class="flex items-center gap-2.5 text-sm font-medium text-slate-600 dark:text-slate-400 cursor-pointer w-max">
                    <input type="checkbox" x-model="show" class="rounded border-slate-300 dark:border-slate-600 text-brand-600 focus:ring-brand-500 bg-slate-50 dark:bg-slate-800 cursor-pointer">
                    Tampilkan password
                </label>

                <div class="pt-2">
                    <button type="submit"
                        class="inline-flex items-center gap-2 px-5 py-2.5 bg-brand-600 text-white rounded-lg shadow-[0_4px_14px_0_rgba(6,182,212,0.39)] hover:bg-brand-500 hover:shadow-[0_6px_20px_rgba(6,182,212,0.23)] transition-all font-semibold text-sm">
                        <i class="fa-solid fa-key"></i> Ganti Password
                    </button>
                </div>
            </form>
        @else
            <div class="flex items-start gap-4 p-5 rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 text-sm text-slate-700 dark:text-slate-300">
                <div class="p-2 bg-white dark:bg-slate-800 rounded-full shadow-sm">
                    <i class="fa-brands fa-google text-xl text-red-500"></i>
                </div>
                <div>
                    <p class="font-bold mb-1">Akun Google Terhubung</p>
                    <p class="leading-relaxed">Akun Anda masuk menggunakan Google, sehingga tidak memiliki password di aplikasi ini. Pengaturan password dikelola langsung melalui akun Google Anda.</p>
                </div>
            </div>
        @endif
    </section>
</div>
@endsection

@push('scripts')
<script>
    // --- Konfirmasi Hapus Foto (SweetAlert Cyberpunk Style) ---
    function konfirmasiHapusFoto() {
        const isDark = document.documentElement.classList.contains('dark');
        Swal.fire({
            title: 'Hapus foto profil?',
            text: 'Foto Anda akan dihapus dan diganti gambar bawaan.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e11d48', // Rose-600
            cancelButtonColor: isDark ? '#334155' : '#cbd5e1', // Slate
            confirmButtonText: 'Ya, hapus',
            cancelButtonText: 'Batal',
            background: isDark ? '#0b1220' : '#ffffff',
            color: isDark ? '#f8fafc' : '#0f172a'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('form-hapus-foto').submit();
            }
        });
    }

    // --- Notifikasi Session Bawaan Anda ---
    document.addEventListener('DOMContentLoaded', function() {
        const isDark = document.documentElement.classList.contains('dark');
        const bg = isDark ? '#0b1220' : '#ffffff';
        const color = isDark ? '#f8fafc' : '#0f172a';

        @if (session('crud_success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: @json(session('crud_success')),
                confirmButtonColor: '#06b6d4',
                background: bg,
                color: color
            });
        @endif

        @if (session('crud_error'))
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: @json(session('crud_error')),
                confirmButtonColor: '#e11d48',
                background: bg,
                color: color
            });
        @endif

        @if ($errors->any())
            Swal.fire({
                icon: 'error',
                title: 'Validasi Gagal',
                html: @json(collect($errors->all())->map(fn($m) => e($m))->implode('<br>')),
                confirmButtonColor: '#e11d48',
                background: bg,
                color: color
            });
        @endif
    });
</script>
@endpush