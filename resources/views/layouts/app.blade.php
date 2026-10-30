<!-- resources/views/layouts/app.blade.php -->
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard - Inventaris Barang')</title>

    <link rel="icon" type="image/png" href="{{ asset('images/13.png') }}">

    <!-- Vite (Tailwind CSS) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Font Awesome & SweetAlert2 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- AlpineJS -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x/dist/cdn.min.js"></script>

    {{-- Script Anti-Flicker: Cek tema sebelum render --}}
    <script>
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>

    <style>
        [x-cloak] { display: none !important; }
        
        /* Grid halus di belakang area konten */
        .cyber-grid {
            background-image:
                linear-gradient(to right, rgba(15, 23, 42, .03) 1px, transparent 1px),
                linear-gradient(to bottom, rgba(15, 23, 42, .03) 1px, transparent 1px);
            background-size: 40px 40px;
            -webkit-mask-image: radial-gradient(ellipse 100% 80% at 50% 0%, #000 30%, transparent 85%);
            mask-image: radial-gradient(ellipse 100% 80% at 50% 0%, #000 30%, transparent 85%);
        }
        .dark .cyber-grid {
            background-image:
                linear-gradient(to right, rgba(6, 182, 212, .05) 1px, transparent 1px),
                linear-gradient(to bottom, rgba(6, 182, 212, .05) 1px, transparent 1px);
        }

        /* Scrollbar ramping */
        #content-area::-webkit-scrollbar { width: 8px; }
        #content-area::-webkit-scrollbar-track { background: transparent; }
        #content-area::-webkit-scrollbar-thumb {
            background: rgba(148, 163, 184, .3);
            border-radius: 9999px;
        }
        #content-area::-webkit-scrollbar-thumb:hover { background: rgba(6, 182, 212, .5); }
    </style>
</head>

<body class="h-screen overflow-hidden bg-slate-50 font-sans text-slate-700 transition-colors duration-300 dark:bg-[#020617] dark:text-slate-300" x-data="{ sidebarOpen: window.innerWidth >= 768 }">

    <!-- Header / Navbar -->
    @include('components.partials.navbar')

    <!-- Body Wrapper -->
    <div class="relative flex h-[calc(100vh-64px)]">
        
        <!-- Grid Dekoratif -->
        <div aria-hidden="true" class="cyber-grid pointer-events-none absolute inset-0 z-0"></div>

        <!-- Sidebar -->
        @include('components.partials.sidebar')

                <!-- Main Content Area -->
        <main id="content-area" 
              class="relative z-10 flex-1 flex flex-col overflow-hidden transition-all duration-300"
              :class="sidebarOpen ? 'md:ml-64' : 'md:ml-0'">
            
            <!-- Area Konten yang bisa di-scroll -->
            <div class="flex-1 overflow-y-auto p-4 sm:p-6">
                @yield('content')
            </div>
            
            <!-- Footer (Menempel di bawah, tidak ikut scroll) -->
            <div class="shrink-0 w-full">
                @include('components.partials.footer')
            </div>

        </main>

    </div>

    <!-- Scripts -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Deteksi Tema
            const isDark = document.documentElement.classList.contains('dark');
            const swalBg = isDark ? '#0b1220' : '#ffffff';
            const swalColor = isDark ? '#f8fafc' : '#0f172a';

            // --- SweetAlert Notifications ---
            setTimeout(() => {
                
                // 1. Login Berhasil
                @if (session('login_success'))
                    Swal.fire({
                        title: "Login Berhasil!",
                        text: @json(session('login_success')),
                        icon: "success",
                        confirmButtonColor: '#06b6d4',
                        background: swalBg,
                        color: swalColor
                    });
                @endif
    
                // 2. CRUD Berhasil
                @if (session('crud_success'))
                    Swal.fire({
                        title: "Sukses!",
                        text: @json(session('crud_success')),
                        icon: "success",
                        confirmButtonColor: '#06b6d4',
                        background: swalBg,
                        color: swalColor
                    });
                @endif

                // 3. CRUD Gagal
                @if (session('crud_error'))
                    Swal.fire({
                        title: "Gagal!",
                        text: @json(session('crud_error')),
                        icon: "error",
                        confirmButtonColor: '#e11d48',
                        background: swalBg,
                        color: swalColor
                    });
                @endif

                // 4. Error Validasi (Login Gagal, Register Gagal, Input Salah, dll)
                @if ($errors->any())
                    Swal.fire({
                        title: "Validasi Gagal!",
                        html: `{!! implode('<br>', array_map('e', $errors->all())) !!}`,
                        icon: "error",
                        confirmButtonColor: '#e11d48',
                        background: swalBg,
                        color: swalColor
                    });
                @endif

            }, 300);
        });
    </script>
    @stack('scripts')

</body>
</html>