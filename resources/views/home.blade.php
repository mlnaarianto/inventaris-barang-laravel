<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Home | Sistem Inventaris Barang</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Vite (Tailwind) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Script Anti-Flicker: Cek tema sebelum render --}}
    <script>
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>

    <style>
        /* =========================================
           NEON GRID - LIGHT MODE (Bersih & Modern)
           ========================================= */
        body {
            background-color: #f8fafc;
            background-image: 
                linear-gradient(to right, rgba(6, 182, 212, 0.05) 1px, transparent 1px),
                linear-gradient(to bottom, rgba(6, 182, 212, 0.05) 1px, transparent 1px);
            background-size: 40px 40px;
            background-attachment: fixed;
            transition: background-color 0.3s ease, color 0.3s ease;
        }
        
        /* =========================================
           NEON GRID - DARK MODE (Subtle Cyberpunk)
           ========================================= */
        .dark body {
            background-color: #020617; /* Slate-950 */
            background-image: 
                /* Grid Cyan Tipis */
                linear-gradient(to right, rgba(6, 182, 212, 0.1) 1px, transparent 1px),
                linear-gradient(to bottom, rgba(6, 182, 212, 0.1) 1px, transparent 1px),
                /* Glow Cyan Kiri Atas */
                radial-gradient(circle at 0% 0%, rgba(6, 182, 212, 0.15) 0%, transparent 50%),
                /* Glow Biru Kanan Bawah */
                radial-gradient(circle at 100% 100%, rgba(59, 130, 246, 0.1) 0%, transparent 50%);
            background-size: 40px 40px, 40px 40px, 100vw 100vh, 100vw 100vh;
            background-attachment: fixed;
        }

        /* Animasi Pulse untuk visual */
        @keyframes soft-pulse {
            0%, 100% { transform: scale(1); opacity: 0.6; }
            50%      { transform: scale(1.05); opacity: 0.2; }
        }
        .soft-pulse { animation: soft-pulse 6s ease-in-out infinite; }
        
        /* Animasi Rotasi Radar */
        @keyframes spin-slow {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        .spin-slow { animation: spin-slow 15s linear infinite; }

        ::selection { background: #cffafe; color: #164e63; }
        .dark ::selection { background: #0891b2; color: #ffffff; }
    </style>
</head>
<body class="font-sans antialiased text-slate-900 dark:text-slate-100 min-h-screen flex flex-col transition-colors duration-300">

    {{-- ================= NAVBAR ================= --}}
    <nav class="sticky top-0 z-50 bg-white/80 dark:bg-slate-900/80 backdrop-blur-lg border-b border-slate-200/70 dark:border-slate-800 transition-colors duration-300">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">

                {{-- Logo --}}
                <a href="/" class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-slate-900 dark:bg-brand-600 flex items-center justify-center text-white font-bold text-xs tracking-tight transition-colors duration-300 shadow-[0_0_10px_rgba(6,182,212,0.3)] dark:shadow-[0_0_15px_rgba(6,182,212,0.5)]">
                        <i class="fas fa-boxes-stacked"></i>
                    </div>
                    <div class="flex flex-col leading-none">
                        <span class="font-semibold text-base text-slate-900 dark:text-white tracking-tight transition-colors duration-300">
                            INV<span class="text-brand-600 dark:text-brand-400">ENTARIS</span>
                        </span>
                        <span class="text-[10px] text-slate-500 dark:text-slate-400 mt-1 font-medium tracking-wide uppercase transition-colors duration-300">
                            Manajemen Barang
                        </span>
                    </div>
                </a>

                {{-- Kanan Navbar --}}
                <div class="flex items-center gap-5">
                    {{-- Status Indikator --}}
                    <span class="hidden sm:flex items-center gap-2 text-[11px] text-slate-500 dark:text-slate-400 font-medium uppercase tracking-wider transition-colors duration-300">
                        <span class="w-1.5 h-1.5 rounded-full bg-brand-500 animate-pulse"></span>
                        System Active
                    </span>

                    {{-- Tombol Switch Tema --}}
                    <button id="theme-toggle" type="button" class="w-9 h-9 flex items-center justify-center rounded-full bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 transition-colors" title="Ganti Tema">
                        <i id="theme-toggle-icon" class="fas fa-moon text-sm"></i>
                    </button>

                    {{-- Tombol Auth --}}
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 bg-slate-900 dark:bg-brand-600 text-white font-medium text-sm px-4 py-2 rounded-lg hover:bg-slate-800 dark:hover:bg-brand-500 transition-all duration-300 hover:shadow-[0_0_15px_rgba(6,182,212,0.4)]">
                                Dashboard <i class="fas fa-arrow-right text-[10px]"></i>
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="inline-flex items-center gap-2 bg-slate-900 dark:bg-brand-600 text-white font-medium text-sm px-4 py-2 rounded-lg hover:bg-slate-800 dark:hover:bg-brand-500 transition-all duration-300 hover:shadow-[0_0_15px_rgba(6,182,212,0.4)]">
                                Masuk <i class="fas fa-sign-in-alt text-[10px]"></i>
                            </a>
                        @endauth
                    @endif
                </div>

            </div>
        </div>
    </nav>

    {{-- ================= HERO ================= --}}
    <main class="flex-grow">
        <div class="max-w-7xl mx-auto px-6 lg:px-8 py-20 lg:py-28">
            <div class="grid lg:grid-cols-12 gap-12 lg:gap-8 items-center">

                {{-- KIRI (Teks) --}}
                <div class="lg:col-span-7 space-y-8">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full border border-brand-100 dark:border-brand-900 bg-brand-50 dark:bg-brand-950/50 text-[11px] text-brand-700 dark:text-brand-400 font-semibold uppercase tracking-wider transition-colors duration-300">
                        <span class="w-1.5 h-1.5 rounded-full bg-brand-500"></span>
                        Sistem Inventaris Terpadu · v2.0
                    </div>

                    <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold leading-[1.05] tracking-tight text-slate-900 dark:text-white transition-colors duration-300">
                        Kendali Penuh<br>
                        Atas Setiap<br>
                        <span class="text-brand-600 dark:text-brand-400 drop-shadow-sm dark:drop-shadow-[0_0_15px_rgba(6,182,212,0.4)]">Barang Anda</span>
                    </h1>

                    <p class="text-lg text-slate-600 dark:text-slate-400 max-w-xl leading-relaxed transition-colors duration-300">
                        Lacak stok, kelola mutasi barang masuk dan keluar, serta hasilkan laporan akurat dalam satu platform yang aman dan *real-time*.
                    </p>

                    <div class="flex flex-col sm:flex-row gap-3 pt-2">
                        @auth
                            <a href="{{ route('dashboard') }}" class="inline-flex items-center justify-center gap-2 bg-brand-600 text-white font-semibold px-6 py-3 rounded-lg hover:bg-brand-500 transition-all duration-300 text-sm shadow-[0_4px_14px_0_rgba(6,182,212,0.39)] hover:shadow-[0_6px_20px_rgba(6,182,212,0.23)]">
                                <i class="fas fa-cubes"></i> Buka Gudang
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="inline-flex items-center justify-center gap-2 bg-brand-600 text-white font-semibold px-6 py-3 rounded-lg hover:bg-brand-500 transition-all duration-300 text-sm shadow-[0_4px_14px_0_rgba(6,182,212,0.39)] hover:shadow-[0_6px_20px_rgba(6,182,212,0.23)]">
                                <i class="fas fa-sign-in-alt"></i> Masuk ke Sistem
                            </a>
                        @endauth

                        <a href="#fitur" class="inline-flex items-center justify-center gap-2 border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-300 font-semibold px-6 py-3 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-800 hover:border-brand-400 dark:hover:border-brand-500 transition-colors text-sm">
                            <i class="fas fa-circle-info text-slate-400 dark:text-slate-500"></i> Pelajari Fitur
                        </a>
                    </div>

                    {{-- Mini stats --}}
                    <div class="grid grid-cols-3 gap-6 pt-8 border-t border-slate-200 dark:border-slate-800 max-w-md transition-colors duration-300">
                        <div>
                            <div class="text-2xl font-bold text-slate-900 dark:text-white leading-none transition-colors duration-300">100%</div>
                            <div class="text-[11px] text-slate-500 dark:text-slate-400 uppercase tracking-wider mt-2 font-medium transition-colors duration-300">Akurasi Stok</div>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-slate-900 dark:text-white leading-none transition-colors duration-300">24/7</div>
                            <div class="text-[11px] text-slate-500 dark:text-slate-400 uppercase tracking-wider mt-2 font-medium transition-colors duration-300">Sinkronisasi</div>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-slate-900 dark:text-white leading-none transition-colors duration-300">Barcode</div>
                            <div class="text-[11px] text-slate-500 dark:text-slate-400 uppercase tracking-wider mt-2 font-medium transition-colors duration-300">Scanning</div>
                        </div>
                    </div>
                </div>

                {{-- KANAN (Visual Cyberpunk) --}}
                <div class="lg:col-span-5 flex justify-center relative">
                    <div class="relative w-full max-w-md aspect-square">
                        
                        {{-- Efek Grid & Radar --}}
                        <div class="absolute inset-0 rounded-full border border-slate-200 dark:border-brand-900/30 transition-colors duration-300"></div>
                        <div class="absolute inset-8 rounded-full border border-slate-200 dark:border-brand-900/40 transition-colors duration-300"></div>
                        <div class="absolute inset-16 rounded-full border border-dashed border-brand-300 dark:border-brand-700/50 spin-slow"></div>
                        <div class="absolute inset-24 rounded-full border border-brand-200 dark:border-brand-600/30 soft-pulse"></div>

                        {{-- Crosshair / Penanda --}}
                        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-px h-4 bg-brand-400 dark:bg-brand-500 shadow-[0_0_8px_rgba(6,182,212,0.8)]"></div>
                        <div class="absolute bottom-0 left-1/2 -translate-x-1/2 w-px h-4 bg-brand-400 dark:bg-brand-500 shadow-[0_0_8px_rgba(6,182,212,0.8)]"></div>
                        <div class="absolute left-0 top-1/2 -translate-y-1/2 h-px w-4 bg-brand-400 dark:bg-brand-500 shadow-[0_0_8px_rgba(6,182,212,0.8)]"></div>
                        <div class="absolute right-0 top-1/2 -translate-y-1/2 h-px w-4 bg-brand-400 dark:bg-brand-500 shadow-[0_0_8px_rgba(6,182,212,0.8)]"></div>

                        {{-- Ikon di Tengah --}}
                        <div class="absolute inset-0 flex items-center justify-center">
                            <div class="relative">
                                <div class="absolute inset-0 bg-brand-400/20 dark:bg-brand-500/20 blur-3xl rounded-full transition-colors duration-300"></div>
                                <div class="relative w-32 h-32 sm:w-40 sm:h-40 rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-brand-500/50 shadow-2xl dark:shadow-[0_0_30px_rgba(6,182,212,0.3)] flex items-center justify-center transform rotate-12 hover:rotate-0 transition-all duration-500">
                                    <i class="fas fa-server text-5xl sm:text-6xl text-brand-500 dark:text-brand-400"></i>
                                </div>
                            </div>
                        </div>

                        {{-- Label Mengambang --}}
                        <div class="hidden sm:block absolute top-4 right-4 text-[10px] font-bold text-brand-700 dark:text-brand-300 bg-white/90 dark:bg-slate-900/90 backdrop-blur-sm border border-brand-200 dark:border-brand-500/50 px-3 py-1.5 rounded-md shadow-sm dark:shadow-[0_0_10px_rgba(6,182,212,0.2)] transition-colors duration-300">
                            <i class="fas fa-wifi mr-1"></i> ONLINE
                        </div>
                        <div class="hidden sm:block absolute bottom-4 left-4 text-[10px] font-bold text-slate-600 dark:text-slate-300 bg-white/90 dark:bg-slate-900/90 backdrop-blur-sm border border-slate-200 dark:border-slate-700 px-3 py-1.5 rounded-md shadow-sm transition-colors duration-300">
                            <i class="fas fa-database mr-1 text-brand-500"></i> DATABASE SYNC
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </main>

    {{-- ================= FITUR ================= --}}
    <section id="fitur" class="border-t border-slate-200 dark:border-slate-800 bg-slate-50/60 dark:bg-slate-950/60 py-24 transition-colors duration-300">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">

            <div class="max-w-3xl mb-16">
                <div class="text-xs text-brand-600 dark:text-brand-400 font-bold uppercase tracking-wider mb-4 transition-colors duration-300">Fitur Unggulan</div>
                <h2 class="text-3xl sm:text-4xl font-bold text-slate-900 dark:text-white leading-tight mb-5 transition-colors duration-300">
                    Semua yang Anda butuhkan,<br>
                    dalam satu genggaman.
                </h2>
                <p class="text-slate-600 dark:text-slate-400 text-lg leading-relaxed transition-colors duration-300">
                    Dirancang untuk efisiensi maksimal. Mulai dari pencatatan barang masuk, pemantauan stok kritis, hingga ekspor laporan.
                </p>
            </div>

            <div class="grid md:grid-cols-3 gap-6">

                {{-- 01 --}}
                <div class="group bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-8 hover:border-brand-400 dark:hover:border-brand-500 hover:shadow-lg dark:hover:shadow-[0_0_20px_rgba(6,182,212,0.15)] transition-all duration-300 hover:-translate-y-1">
                    <div class="flex items-start justify-between mb-8">
                        <div class="text-xs text-slate-400 dark:text-slate-500 font-bold tracking-wider transition-colors duration-300">01</div>
                        <div class="w-12 h-12 rounded-xl bg-brand-50 dark:bg-brand-950/30 border border-brand-100 dark:border-brand-900/50 flex items-center justify-center text-brand-600 dark:text-brand-400 transition-colors duration-300 group-hover:scale-110">
                            <i class="fas fa-boxes-packing text-xl"></i>
                        </div>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-3 transition-colors duration-300">Manajemen Stok</h3>
                    <p class="text-slate-600 dark:text-slate-400 text-sm leading-relaxed transition-colors duration-300">
                        Pantau kuantitas setiap barang secara akurat. Sistem otomatis memberi peringatan jika stok menipis atau habis.
                    </p>
                </div>

                {{-- 02 --}}
                <div class="group bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-8 hover:border-brand-400 dark:hover:border-brand-500 hover:shadow-lg dark:hover:shadow-[0_0_20px_rgba(6,182,212,0.15)] transition-all duration-300 hover:-translate-y-1">
                    <div class="flex items-start justify-between mb-8">
                        <div class="text-xs text-slate-400 dark:text-slate-500 font-bold tracking-wider transition-colors duration-300">02</div>
                        <div class="w-12 h-12 rounded-xl bg-brand-50 dark:bg-brand-950/30 border border-brand-100 dark:border-brand-900/50 flex items-center justify-center text-brand-600 dark:text-brand-400 transition-colors duration-300 group-hover:scale-110">
                            <i class="fas fa-right-left text-xl"></i>
                        </div>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-3 transition-colors duration-300">Mutasi Barang</h3>
                    <p class="text-slate-600 dark:text-slate-400 text-sm leading-relaxed transition-colors duration-300">
                        Catat riwayat barang masuk dan keluar dengan detail. Dilengkapi dengan timestamp dan user pencatat.
                    </p>
                </div>

                {{-- 03 --}}
                <div class="group bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-8 hover:border-brand-400 dark:hover:border-brand-500 hover:shadow-lg dark:hover:shadow-[0_0_20px_rgba(6,182,212,0.15)] transition-all duration-300 hover:-translate-y-1">
                    <div class="flex items-start justify-between mb-8">
                        <div class="text-xs text-slate-400 dark:text-slate-500 font-bold tracking-wider transition-colors duration-300">03</div>
                        <div class="w-12 h-12 rounded-xl bg-brand-50 dark:bg-brand-950/30 border border-brand-100 dark:border-brand-900/50 flex items-center justify-center text-brand-600 dark:text-brand-400 transition-colors duration-300 group-hover:scale-110">
                            <i class="fas fa-chart-pie text-xl"></i>
                        </div>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-3 transition-colors duration-300">Laporan Analitik</h3>
                    <p class="text-slate-600 dark:text-slate-400 text-sm leading-relaxed transition-colors duration-300">
                        Dapatkan wawasan dari data inventaris. Ekspor laporan ke PDF atau Excel untuk keperluan audit bulanan.
                    </p>
                </div>

            </div>
        </div>
    </section>

    {{-- ================= FOOTER ================= --}}
    <footer class="border-t border-slate-200 dark:border-slate-800 py-10 bg-white dark:bg-slate-900 transition-colors duration-300">
        <div class="max-w-7xl mx-auto px-6 lg:px-8 flex flex-col sm:flex-row justify-between items-center gap-4">
            <div class="flex items-center gap-3">
                <div class="w-7 h-7 rounded-md bg-slate-900 dark:bg-brand-600 flex items-center justify-center text-white font-bold text-[10px] tracking-tight transition-colors duration-300">
                    INV
                </div>
                <span class="text-[11px] text-slate-500 dark:text-slate-400 uppercase tracking-wider font-medium transition-colors duration-300">
                    Sistem Inventaris Barang
                </span>
            </div>
            <p class="text-[11px] text-slate-400 dark:text-slate-500 transition-colors duration-300">
                © {{ date('Y') }} · All systems operational
            </p>
        </div>
    </footer>

    {{-- ================= SCRIPT TOGGLE TEMA ================= --}}
    <script>
        const themeToggleBtn = document.getElementById('theme-toggle');
        const themeToggleIcon = document.getElementById('theme-toggle-icon');
        const html = document.documentElement;

        // Fungsi untuk update ikon
        function updateIcon() {
            if (html.classList.contains('dark')) {
                themeToggleIcon.classList.remove('fa-moon');
                themeToggleIcon.classList.add('fa-sun');
            } else {
                themeToggleIcon.classList.remove('fa-sun');
                themeToggleIcon.classList.add('fa-moon');
            }
        }

        // Set ikon saat halaman pertama kali dimuat
        updateIcon();

        // Event listener tombol
        themeToggleBtn.addEventListener('click', function() {
            html.classList.toggle('dark');
            
            if (html.classList.contains('dark')) {
                localStorage.setItem('theme', 'dark');
            } else {
                localStorage.setItem('theme', 'light');
            }
            updateIcon();
        });
    </script>

</body>
</html>