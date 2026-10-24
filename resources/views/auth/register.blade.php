<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>

    <!-- TailwindCSS & SweetAlert2 -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>


    <!-- Font Awesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body class="flex items-center justify-center min-h-screen bg-gradient-to-r from-indigo-500 to-purple-600">

    <div class="bg-white p-8 rounded-xl shadow-xl w-full max-w-md text-center animate-fadeIn">
        <!-- Ikon Register -->
        <div class="flex justify-center mb-4 text-indigo-600 text-5xl">
            <i class="fas fa-user-plus"></i>
        </div>

        <!-- Judul -->
        <h2 class="text-2xl font-bold text-gray-800 mb-1">Buat Akun Baru</h2>
        <p class="text-sm text-gray-500 mb-6">Isi data Anda untuk mendaftar</p>

        <form action="{{ route('register') }}" method="POST" class="space-y-4 text-left">
            @csrf

            <!-- Info Role -->
            <div class="flex items-start gap-3 p-3 rounded-lg bg-indigo-50 border border-indigo-100 text-sm text-indigo-800">
                <i class="fas fa-circle-info mt-0.5"></i>
                <p>
                    Akun yang dibuat lewat halaman ini otomatis berperan sebagai <b>User</b>.
                    Untuk menjadi <b>Pengelola</b>, hubungi admin setelah Anda berhasil mendaftar.
                </p>
            </div>

            <!-- Nama -->
            <div>
                <label for="nama" class="block text-sm font-semibold text-gray-700 mb-1">Nama Lengkap</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                        <i class="fas fa-user"></i>
                    </span>
                    <input type="text" id="nama" name="nama" value="{{ old('nama') }}" required
                        class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-400 transition">
                </div>
            </div>

            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-semibold text-gray-700 mb-1">Email</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                        <i class="fas fa-envelope"></i>
                    </span>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required
                        class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-400 transition">
                </div>
            </div>

            <!-- Password -->
            <div>
                <label for="password" class="block text-sm font-semibold text-gray-700 mb-1">Password</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                        <i class="fas fa-lock"></i>
                    </span>
                    <input type="password" id="password" name="password" required
                        class="w-full pl-10 pr-10 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-400 transition"
                        oninput="checkPasswordStrength(this.value)">
                    <span class="absolute inset-y-0 right-0 flex items-center pr-3 cursor-pointer"
                        onclick="togglePassword('password', this)">
                        <i class="fas fa-eye text-gray-400"></i>
                    </span>
                </div>
                <!-- Password Strength Bar -->
                <div class="w-full h-2 mt-2 rounded bg-gray-200 overflow-hidden">
                    <div id="password-strength" class="h-full transition-all duration-300 ease-in-out"></div>
                </div>
                <!-- Keterangan Kekuatan Password -->
                <div id="password-strength-text" class="mt-1 text-sm font-medium text-gray-600"></div>

            </div>


            <!-- Konfirmasi Password -->
            <div>
                <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-1">Konfirmasi
                    Password</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                        <i class="fas fa-check-circle"></i>
                    </span>
                    <input type="password" id="password_confirmation" name="password_confirmation" required
                        class="w-full pl-10 pr-10 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-400 transition">
                    <span class="absolute inset-y-0 right-0 flex items-center pr-3 cursor-pointer"
                        onclick="togglePassword('password_confirmation', this)">
                        <i class="fas fa-eye text-gray-400"></i>
                    </span>
                </div>
            </div>


            <!-- Tombol Daftar -->
            <button type="submit"
                class="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-2 rounded-lg font-semibold transition duration-200">
                <i class="fas fa-user-plus mr-2"></i> Daftar
            </button>
        </form>

        <!-- Divider -->
        <div class="flex items-center my-4">
            <div class="flex-grow border-t"></div>
            <span class="mx-2 text-gray-400 text-sm">atau</span>
            <div class="flex-grow border-t"></div>
        </div>

        <!-- Tombol Google -->
        <a href="{{ route('google.login') }}"
            class="w-full flex items-center justify-center gap-2 border border-gray-300 py-2 rounded-lg hover:bg-gray-50 transition font-semibold text-gray-700">
            <i class="fab fa-google text-red-500"></i>
            Daftar / Masuk dengan Google
        </a>


        <!-- Link ke Login -->
        <div class="mt-4 text-sm">
            <a href="/login" class="text-indigo-500 hover:underline">Sudah punya akun? Login</a>
        </div>
    </div>

    <!-- SweetAlert2 Notifikasi -->
    @if (session('success'))
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Registrasi Berhasil!',
            text: "{{ session('success') }}",
            confirmButtonColor: '#5563c1'
        });
    </script>
    @endif

    @if ($errors->any())
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Registrasi Gagal!',
            html: `{!! implode('<br>', array_map('e', $errors->all())) !!}`,
            confirmButtonColor: '#e14b56'
        });
    </script>
    @endif


    <style>
        /* Fade-in animation */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fadeIn {
            animation: fadeIn 0.6s ease-out;
        }
    </style>

</body>

<script>
    function togglePassword(id, el) {
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
</script>
<script>
    function checkPasswordStrength(password) {
        const bar = document.getElementById('password-strength');
        const text = document.getElementById('password-strength-text');
        let strength = 0;

        // Cek panjang minimal 8 karakter
        if (password.length >= 8) strength += 1;

        // Cek diawali huruf kapital
        if (/^[A-Z]/.test(password)) strength += 1;

        // Cek kombinasi huruf dan angka
        if (/[0-9]/.test(password) && /[A-Za-z]/.test(password)) strength += 1;

        // Atur tampilan indikator & teks
        if (strength === 0) {
            bar.style.width = '0%';
            bar.style.backgroundColor = '';
            text.textContent = '';
        } else if (strength === 1) {
            bar.style.width = '33%';
            bar.style.backgroundColor = '#f56565'; // merah
            text.textContent = 'Lemah';
            text.style.color = '#f56565';
        } else if (strength === 2) {
            bar.style.width = '66%';
            bar.style.backgroundColor = '#ecc94b'; // kuning
            text.textContent = 'Sedang';
            text.style.color = '#ecc94b';
        } else if (strength === 3) {
            bar.style.width = '100%';
            bar.style.backgroundColor = '#48bb78'; // hijau
            text.textContent = 'Kuat';
            text.style.color = '#48bb78';
        }
    }
</script>




</html>