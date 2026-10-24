<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Register | Sistem Inventaris Barang</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Vite (Tailwind CSS) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Script Anti-Flicker --}}
    <script>
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>

    <style>
        ::selection { background: #cffafe; color: #164e63; }
        .dark ::selection { background: #0891b2; color: #ffffff; }
        
        /* NEON GRID - LIGHT MODE */
        .neon-grid-bg {
            background-color: #f8fafc;
            background-image: 
                linear-gradient(to right, rgba(6, 182, 212, 0.05) 1px, transparent 1px),
                linear-gradient(to bottom, rgba(6, 182, 212, 0.05) 1px, transparent 1px);
            background-size: 40px 40px;
            background-attachment: fixed;
        }

        /* NEON GRID - DARK MODE */
        .dark .neon-grid-bg {
            background-color: #020617;
            background-image: 
                linear-gradient(to right, rgba(6, 182, 212, 0.1) 1px, transparent 1px),
                linear-gradient(to bottom, rgba(6, 182, 212, 0.1) 1px, transparent 1px),
                radial-gradient(circle at 0% 0%, rgba(6, 182, 212, 0.15) 0%, transparent 50%),
                radial-gradient(circle at 100% 100%, rgba(59, 130, 246, 0.1) 0%, transparent 50%);
            background-size: 40px 40px, 40px 40px, 100vw 100vh, 100vh 100vh;
            background-attachment: fixed;
        }

        @keyframes soft-pulse {
            0%, 100% { transform: scale(1); opacity: 0.5; }
            50%      { transform: scale(1.02); opacity: 0.8; }
        }
        .soft-pulse { animation: soft-pulse 5s ease-in-out infinite; }

        body, aside, main, input, button, a, div {
            transition: background-color 0.3s ease, border-color 0.3s ease, color 0.3s ease;
        }
    </style>
</head>
<body class="font-sans antialiased text-slate-900 dark:text-slate-100 bg-white dark:bg-slate-900">

    <div class="flex min-h-screen">

        {{-- ================= KIRI: Branding ================= --}}
        <div class="hidden lg:flex lg:w-1/2 relative neon-grid-bg border-r border-slate-200/70 dark:border-slate-800 items-center justify-center overflow-hidden">
            <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[700px] h-[700px] bg-brand-100/40 dark:bg-brand-900/10 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[520px] h-[520px] rounded-full border border-slate-200/80 dark:border-slate-800/80"></div>
            <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[400px] h-[400px] rounded-full border border-slate-200/80 dark:border-slate-800/80"></div>
            <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[280px] h-[280px] rounded-full border border-brand-100 dark:border-brand-900/50 soft-pulse"></div>

            <div class="relative z-10 px-12 xl:px-20 py-16 flex flex-col justify-between w-full min-h-screen">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-slate-900 dark:bg-brand-600 flex items-center justify-center text-white font-bold text-xs tracking-tight shadow-[0_0_10px_rgba(6,182,212,0.3)]">
                        INV
                    </div>
                    <div class="flex flex-col leading-none">
                        <span class="font-semibold text-base text-slate-900 dark:text-white tracking-tight">
                            INV<span class="text-brand-600 dark:text-brand-400">ENTARIS</span>
                        </span>
                        <span class="text-[10px] text-slate-500 dark:text-slate-400 mt-1 font-medium tracking-wide uppercase">
                            Manajemen Barang
                        </span>
                    </div>
                </div>

                <div class="text-center my-8">
                    <div class="relative inline-block mb-8">
                        <div class="absolute inset-0 bg-brand-400/20 dark:bg-brand-500/20 blur-3xl rounded-full"></div>
                        <div class="relative w-32 h-32 xl:w-40 xl:h-40 mx-auto rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-brand-500/50 shadow-2xl dark:shadow-[0_0_30px_rgba(6,182,212,0.3)] flex items-center justify-center transform rotate-12 hover:rotate-0 transition-all duration-500">
                            <i class="fas fa-user-plus text-5xl xl:text-6xl text-brand-500 dark:text-brand-400"></i>
                        </div>
                    </div>

                    <h1 class="text-3xl xl:text-4xl font-bold tracking-tight mb-4 leading-tight text-slate-900 dark:text-white">
                        Bergabunglah dengan<br>
                        <span class="text-brand-600 dark:text-brand-400">Ekosistem</span> Kami
                    </h1>
                    <p class="text-slate-600 dark:text-slate-400 text-base max-w-sm mx-auto leading-relaxed">
                        Daftarkan diri Anda untuk mulai mengelola, melacak, dan menganalisis inventaris barang secara real-time.
                    </p>

                    <div class="mt-10 inline-flex items-center gap-2 text-xs font-semibold text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-900 px-4 py-2.5 rounded-full border border-slate-200 dark:border-slate-700 shadow-sm">
                        <i class="fas fa-shield-halved text-brand-600 dark:text-brand-500"></i>
                        Akses Aman & Terenkripsi
                    </div>
                </div>

                <div class="flex items-center gap-3 text-[11px] text-slate-500 dark:text-slate-400 uppercase tracking-wider font-medium">
                    <span class="flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-brand-500 dark:bg-brand-400 animate-pulse"></span>
                        System Online
                    </span>
                    <span class="text-slate-300 dark:text-slate-600">·</span>
                    <span>v.2.0</span>
                </div>
            </div>
        </div>

        {{-- ================= KANAN: Form Register ================= --}}
        <div class="w-full lg:w-1/2 flex flex-col bg-white dark:bg-slate-900 relative">

            {{-- Top Actions --}}
            <div class="absolute top-8 right-8 sm:right-12 xl:right-20 flex items-center gap-4 z-20">
                <button id="theme-toggle" type="button" class="w-9 h-9 flex items-center justify-center rounded-full bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 transition-colors" title="Ganti Tema">
                    <i id="theme-toggle-icon" class="fas fa-moon text-sm"></i>
                </button>
                <a href="{{ url('/') }}" class="inline-flex items-center gap-2 text-xs font-semibold text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white transition-colors">
                    <i class="fas fa-arrow-left text-[10px]"></i> Kembali
                </a>
            </div>

            {{-- Wrapper Form --}}
            <div class="flex-grow flex items-center justify-center px-6 sm:px-12 xl:px-20 py-20">
                <div class="max-w-md w-full">

                    {{-- Mobile Logo --}}
                    <div class="flex lg:hidden items-center gap-3 mb-8">
                        <div class="w-9 h-9 rounded-lg bg-slate-900 dark:bg-brand-600 flex items-center justify-center text-white font-bold text-xs tracking-tight shadow-[0_0_10px_rgba(6,182,212,0.3)]">INV</div>
                        <div class="flex flex-col leading-none">
                            <span class="font-semibold text-base text-slate-900 dark:text-white tracking-tight">INV<span class="text-brand-600 dark:text-brand-400">ENTARIS</span></span>
                            <span class="text-[10px] text-slate-500 dark:text-slate-400 mt-1 font-medium tracking-wide uppercase">Manajemen Barang</span>
                        </div>
                    </div>

                    <div class="mb-6">
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full border border-brand-100 dark:border-brand-900 bg-brand-50 dark:bg-brand-950/50 text-[10px] font-bold uppercase tracking-wider text-brand-700 dark:text-brand-400 mb-4">
                            <span class="w-1.5 h-1.5 rounded-full bg-brand-500"></span>
                            Pendaftaran Akun
                        </div>
                        <h2 class="text-3xl font-bold text-slate-900 dark:text-white mb-2 tracking-tight">Buat Akun Baru</h2>
                        <p class="text-slate-500 dark:text-slate-400 text-sm leading-relaxed">Lengkapi data di bawah ini untuk mendaftar.</p>
                    </div>

                    {{-- Info Box Role --}}
                    <div class="flex items-start gap-3 p-4 mb-6 rounded-lg bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 text-sm text-slate-700 dark:text-slate-300">
                        <i class="fas fa-circle-info mt-0.5 text-brand-500"></i>
                        <p class="leading-relaxed">
                            Akun ini otomatis berperan sebagai <b>User</b>. Untuk menjadi <b>Pengelola</b>, hubungi admin setelah mendaftar.
                        </p>
                    </div>

                    {{-- Form --}}
                    <form action="{{ route('register') }}" method="POST" class="space-y-4">
                        @csrf
                        
                        <!-- Nama -->
                        <div>
                            <label for="nama" class="block mb-2 text-sm font-semibold text-slate-700 dark:text-slate-300">Nama Lengkap</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 dark:text-slate-500">
                                    <i class="fas fa-user text-sm"></i>
                                </div>
                                <input id="nama" type="text" name="nama" value="{{ old('nama') }}" required autofocus
                                    placeholder="Nama Lengkap Anda"
                                    class="bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white text-sm rounded-lg focus:ring-4 focus:ring-brand-500/10 dark:focus:ring-brand-500/20 focus:border-brand-500 dark:focus:border-brand-500 focus:bg-white dark:focus:bg-slate-800/80 block w-full pl-11 p-3 outline-none transition-all placeholder:text-slate-400 dark:placeholder:text-slate-500" />
                            </div>
                        </div>

                        <!-- Email -->
                        <div>
                            <label for="email" class="block mb-2 text-sm font-semibold text-slate-700 dark:text-slate-300">Email</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 dark:text-slate-500">
                                    <i class="fas fa-envelope text-sm"></i>
                                </div>
                                <input id="email" type="email" name="email" value="{{ old('email') }}" required
                                    placeholder="nama@email.com"
                                    class="bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white text-sm rounded-lg focus:ring-4 focus:ring-brand-500/10 dark:focus:ring-brand-500/20 focus:border-brand-500 dark:focus:border-brand-500 focus:bg-white dark:focus:bg-slate-800/80 block w-full pl-11 p-3 outline-none transition-all placeholder:text-slate-400 dark:placeholder:text-slate-500" />
                            </div>
                        </div>

                        <!-- Password -->
                        <div>
                            <label for="password" class="block mb-2 text-sm font-semibold text-slate-700 dark:text-slate-300">Password</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 dark:text-slate-500">
                                    <i class="fas fa-lock text-sm"></i>
                                </div>
                                <input id="password" type="password" name="password" required
                                    placeholder="••••••••"
                                    oninput="checkPasswordStrength(this.value)"
                                    class="bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white text-sm rounded-lg focus:ring-4 focus:ring-brand-500/10 dark:focus:ring-brand-500/20 focus:border-brand-500 dark:focus:border-brand-500 focus:bg-white dark:focus:bg-slate-800/80 block w-full pl-11 pr-11 p-3 outline-none transition-all placeholder:text-slate-400 dark:placeholder:text-slate-500" />
                                <div class="absolute inset-y-0 right-0 pr-4 flex items-center cursor-pointer text-slate-400 dark:text-slate-500 hover:text-brand-600 dark:hover:text-brand-400 transition-colors" onclick="togglePassword('password', this)">
                                    <i class="fas fa-eye text-sm"></i>
                                </div>
                            </div>
                            
                            <!-- Password Strength Bar -->
                            <div class="w-full h-1.5 mt-2 rounded-full bg-slate-200 dark:bg-slate-700 overflow-hidden">
                                <div id="password-strength" class="h-full transition-all duration-300 ease-in-out w-0"></div>
                            </div>
                            <div id="password-strength-text" class="mt-1 text-xs font-medium text-slate-500"></div>
                        </div>

                        <!-- Konfirmasi Password -->
                        <div>
                            <label for="password_confirmation" class="block mb-2 text-sm font-semibold text-slate-700 dark:text-slate-300">Konfirmasi Password</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 dark:text-slate-500">
                                    <i class="fas fa-check-circle text-sm"></i>
                                </div>
                                <input id="password_confirmation" type="password" name="password_confirmation" required
                                    placeholder="••••••••"
                                    class="bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white text-sm rounded-lg focus:ring-4 focus:ring-brand-500/10 dark:focus:ring-brand-500/20 focus:border-brand-500 dark:focus:border-brand-500 focus:bg-white dark:focus:bg-slate-800/80 block w-full pl-11 pr-11 p-3 outline-none transition-all placeholder:text-slate-400 dark:placeholder:text-slate-500" />
                                <div class="absolute inset-y-0 right-0 pr-4 flex items-center cursor-pointer text-slate-400 dark:text-slate-500 hover:text-brand-600 dark:hover:text-brand-400 transition-colors" onclick="togglePassword('password_confirmation', this)">
                                    <i class="fas fa-eye text-sm"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Tombol Submit -->
                        <button type="submit"
                            class="w-full flex justify-center items-center gap-2 py-3 px-4 mt-2 rounded-lg text-sm font-semibold text-white bg-slate-900 dark:bg-brand-600 hover:bg-slate-800 dark:hover:bg-brand-500 focus:outline-none focus:ring-4 focus:ring-slate-900/10 dark:focus:ring-brand-500/30 transition-all duration-300 hover:shadow-[0_4px_14px_0_rgba(6,182,212,0.39)]">
                            Daftar Sekarang <i class="fas fa-user-plus text-xs"></i>
                        </button>
                    </form>

                    <!-- Divider -->
                    <div class="flex items-center my-6">
                        <div class="flex-grow border-t border-slate-200 dark:border-slate-700"></div>
                        <span class="mx-3 text-slate-400 dark:text-slate-500 text-xs font-medium uppercase tracking-wider">atau</span>
                        <div class="flex-grow border-t border-slate-200 dark:border-slate-700"></div>
                    </div>

                    <!-- Google Login -->
                    <a href="{{ route('google.login') }}" 
                       class="w-full flex items-center justify-center gap-2 border border-slate-300 dark:border-slate-700 py-3 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors font-semibold text-sm text-slate-700 dark:text-slate-300">
                        <i class="fab fa-google text-red-500"></i>
                        Daftar dengan Google
                    </a>

                    <!-- Link Login -->
                    <p class="text-center text-sm text-slate-500 dark:text-slate-400 mt-6">
                        Sudah punya akun? 
                        <a href="{{ route('login') }}" class="font-semibold text-brand-600 dark:text-brand-400 hover:underline">Masuk di sini</a>
                    </p>
                </div>
            </div>

            {{-- Footer --}}
            <div class="py-6 text-center lg:text-right px-6 sm:px-12 xl:px-20 border-t border-slate-100 dark:border-slate-800/50 mt-auto">
                <p class="text-[11px] text-slate-400 dark:text-slate-500">
                    &copy; {{ date('Y') }} Sistem Inventaris Barang · All rights reserved
                </p>
            </div>

        </div>
    </div>

    <!-- Scripts -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            
            // --- Toggle Password ---
            window.togglePassword = function(id, el) {
                const input = document.getElementById(id);
                const icon = el.querySelector('i');
                if (input.type === "password") {
                    input.type = "text";
                    icon.classList.remove("fa-eye");
                    icon.classList.add("fa-eye-slash");
                } else {
                    input.type = "password";
                    icon.classList.remove("fa-eye-slash");
                    icon.classList.add("fa-eye");
                }
            }

            // --- Check Password Strength ---
            window.checkPasswordStrength = function(password) {
                const bar = document.getElementById('password-strength');
                const text = document.getElementById('password-strength-text');
                let strength = 0;

                if (password.length >= 8) strength += 1;
                if (/^[A-Z]/.test(password)) strength += 1;
                if (/[0-9]/.test(password) && /[A-Za-z]/.test(password)) strength += 1;

                // Reset class width & warna agar transisi mulus
                bar.className = 'h-full transition-all duration-300 ease-in-out ';

                if (strength === 0) {
                    bar.style.width = '0%';
                    bar.style.backgroundColor = '';
                    text.textContent = '';
                } else if (strength === 1) {
                    bar.style.width = '33%';
                    bar.style.backgroundColor = '#ef4444'; // Tailwind red-500
                    text.textContent = 'Lemah';
                    text.style.color = '#ef4444';
                } else if (strength === 2) {
                    bar.style.width = '66%';
                    bar.style.backgroundColor = '#eab308'; // Tailwind yellow-500
                    text.textContent = 'Sedang';
                    text.style.color = '#eab308';
                } else if (strength === 3) {
                    bar.style.width = '100%';
                    bar.style.backgroundColor = '#22c55e'; // Tailwind green-500
                    text.textContent = 'Kuat';
                    text.style.color = '#22c55e';
                }
            }

            // --- Toggle Tema (Dark/Light) ---
            const themeToggleBtn = document.getElementById('theme-toggle');
            const themeToggleIcon = document.getElementById('theme-toggle-icon');
            const html = document.documentElement;

            function updateIcon() {
                if (html.classList.contains('dark')) {
                    themeToggleIcon.classList.remove('fa-moon');
                    themeToggleIcon.classList.add('fa-sun');
                } else {
                    themeToggleIcon.classList.remove('fa-sun');
                    themeToggleIcon.classList.add('fa-moon');
                }
            }

            if (themeToggleBtn && themeToggleIcon) {
                updateIcon();
                themeToggleBtn.addEventListener('click', function() {
                    html.classList.toggle('dark');
                    if (html.classList.contains('dark')) {
                        localStorage.setItem('theme', 'dark');
                    } else {
                        localStorage.setItem('theme', 'light');
                    }
                    updateIcon();
                });
            }

            // --- SweetAlert untuk Notifikasi (Aman & Terpusat) ---
            const isDark = document.documentElement.classList.contains('dark');
            const swalBg = isDark ? '#0b1220' : '#ffffff';
            const swalColor = isDark ? '#f8fafc' : '#0f172a';

            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: @json(session('success')),
                    confirmButtonColor: '#06b6d4',
                    background: swalBg,
                    color: swalColor
                });
            @endif

            @if ($errors->any())
                Swal.fire({
                    icon: 'error',
                    title: 'Registrasi Gagal!',
                    // Menggunakan 'e' untuk mencegah XSS
                    html: `{!! implode('<br>', array_map('e', $errors->all())) !!}`,
                    confirmButtonColor: '#e11d48', // Merah
                    background: swalBg,
                    color: swalColor
                });
            @endif
        });
    </script>
</body>
</html>