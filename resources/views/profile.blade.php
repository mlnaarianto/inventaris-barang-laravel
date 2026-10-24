<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Saya</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>

<body class="min-h-screen bg-gray-100">

    <!-- Header -->
    <header class="bg-purple-700 text-white p-4 flex justify-between items-center">
        <h1 class="text-xl font-bold">Profil Saya</h1>
        <a href="{{ $backUrl }}"
            class="flex items-center gap-2 px-3 py-1.5 rounded-lg bg-purple-600 hover:bg-purple-500 transition text-sm">
            <i class="fa-solid fa-arrow-left"></i> Kembali
        </a>
    </header>

    <main class="max-w-2xl mx-auto p-4 space-y-6">

        <!-- ================= FOTO PROFIL ================= -->
        <section class="bg-white p-6 rounded-xl shadow">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Foto Profil</h2>

            <div x-data="{ preview: null }" class="flex flex-col sm:flex-row items-center gap-6">
                <img :src="preview ?? '{{ $user->avatar_url ?: asset('images/1.jpg') }}'"
                    onerror="this.src='{{ asset('images/1.jpg') }}'"
                    class="w-28 h-28 rounded-full object-cover border-4 border-purple-100" alt="Foto profil">

                <div class="flex-1 w-full space-y-3">
                    <form method="POST" action="{{ route('profile.avatar') }}" enctype="multipart/form-data"
                        class="space-y-3">
                        @csrf
                        <input type="file" name="avatar" accept="image/png,image/jpeg,image/webp" required
                            @change="preview = $event.target.files[0] ? URL.createObjectURL($event.target.files[0]) : null"
                            class="block w-full text-sm text-gray-600 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100">
                        <p class="text-xs text-gray-500">Format JPG, PNG, atau WebP. Maksimal 2 MB.</p>
                        <button type="submit"
                            class="px-4 py-2 bg-purple-700 text-white rounded-lg hover:bg-purple-800 transition text-sm">
                            <i class="fa-solid fa-upload mr-1"></i> Simpan Foto
                        </button>
                    </form>

                    @if ($user->avatar)
                        <form id="form-hapus-foto" method="POST" action="{{ route('profile.avatar.destroy') }}">
                            @csrf
                            @method('DELETE')
                            <button type="button" onclick="konfirmasiHapusFoto()"
                                class="text-sm text-red-600 hover:underline">
                                <i class="fa-solid fa-trash mr-1"></i> Hapus foto
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </section>

        <!-- ================= DATA DIRI ================= -->
        <section class="bg-white p-6 rounded-xl shadow">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Data Diri</h2>

            <form method="POST" action="{{ route('profile.update') }}" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label for="nama" class="block text-sm font-semibold text-gray-700 mb-1">Nama Lengkap</label>
                    <input type="text" id="nama" name="nama" value="{{ old('nama', $user->nama) }}" required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-gray-900 focus:outline-none focus:ring-2 focus:ring-purple-500">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Email</label>
                    <input type="email" value="{{ $user->email }}" disabled
                        class="w-full border border-gray-200 bg-gray-100 rounded-lg px-3 py-2 text-gray-500 cursor-not-allowed">
                    <p class="text-xs text-gray-500 mt-1">Email tidak dapat diubah karena dipakai untuk login.</p>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Role</label>
                    <input type="text" value="{{ ucfirst($user->getRoleNames()->first()) }}" disabled
                        class="w-full border border-gray-200 bg-gray-100 rounded-lg px-3 py-2 text-gray-500 cursor-not-allowed">
                </div>

                <button type="submit"
                    class="px-4 py-2 bg-purple-700 text-white rounded-lg hover:bg-purple-800 transition text-sm">
                    <i class="fa-solid fa-floppy-disk mr-1"></i> Simpan Perubahan
                </button>
            </form>
        </section>

        <!-- ================= GANTI PASSWORD ================= -->
        <section class="bg-white p-6 rounded-xl shadow">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Ganti Password</h2>

            @if ($hasPassword)
                <form method="POST" action="{{ route('password.change') }}" class="space-y-4"
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
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Password Saat Ini</label>
                        <input :type="show ? 'text' : 'password'" name="current_password" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-gray-900 focus:outline-none focus:ring-2 focus:ring-purple-500">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Password Baru</label>
                        <input :type="show ? 'text' : 'password'" name="new_password" x-model="password" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-gray-900 focus:outline-none focus:ring-2 focus:ring-purple-500">

                        <!-- Strength bar -->
                        <div class="w-full h-2 mt-2 rounded bg-gray-200 overflow-hidden">
                            <div class="h-full transition-all duration-300" :style="`width: ${score * 25}%`"
                                :class="{
                                    'bg-red-500': score > 0 && score <= 2,
                                    'bg-yellow-400': score === 3,
                                    'bg-green-500': score === 4
                                }">
                            </div>
                        </div>
                        <p class="text-xs mt-1 text-gray-600"
                            x-text="score === 0 ? '' : score <= 2 ? 'Password lemah' : score === 3 ? 'Password cukup' : 'Password kuat'">
                        </p>

                        <!-- Syarat password -->
                        <div class="mt-3 p-3 rounded-lg bg-purple-50 border border-purple-100 text-xs">
                            <p class="font-semibold text-purple-800 mb-1">Syarat password (sama seperti saat daftar):</p>
                            <ul class="space-y-1">
                                <li :class="rLen ? 'text-green-600' : 'text-gray-600'">
                                    <i :class="rLen ? 'fa-solid fa-circle-check' : 'fa-regular fa-circle'"></i>
                                    Minimal 8 karakter
                                </li>
                                <li :class="rCap ? 'text-green-600' : 'text-gray-600'">
                                    <i :class="rCap ? 'fa-solid fa-circle-check' : 'fa-regular fa-circle'"></i>
                                    Diawali huruf kapital
                                </li>
                                <li :class="rNum ? 'text-green-600' : 'text-gray-600'">
                                    <i :class="rNum ? 'fa-solid fa-circle-check' : 'fa-regular fa-circle'"></i>
                                    Mengandung minimal satu angka
                                </li>
                                <li :class="rAlnum ? 'text-green-600' : 'text-gray-600'">
                                    <i :class="rAlnum ? 'fa-solid fa-circle-check' : 'fa-regular fa-circle'"></i>
                                    Hanya huruf dan angka (tanpa spasi atau simbol)
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Konfirmasi Password Baru</label>
                        <input :type="show ? 'text' : 'password'" name="new_password_confirmation" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-gray-900 focus:outline-none focus:ring-2 focus:ring-purple-500">
                    </div>

                    <label class="flex items-center gap-2 text-sm text-gray-600 cursor-pointer">
                        <input type="checkbox" x-model="show" class="rounded border-gray-300">
                        Tampilkan password
                    </label>

                    <button type="submit"
                        class="px-4 py-2 bg-purple-700 text-white rounded-lg hover:bg-purple-800 transition text-sm">
                        <i class="fa-solid fa-key mr-1"></i> Ganti Password
                    </button>
                </form>
            @else
                <div class="flex items-start gap-3 p-3 rounded-lg bg-gray-50 border border-gray-200 text-sm text-gray-700">
                    <i class="fa-brands fa-google mt-0.5 text-red-500"></i>
                    <p>
                        Akun Anda masuk menggunakan Google, sehingga tidak memiliki password di aplikasi ini.
                        Pengaturan password dikelola lewat akun Google Anda.
                    </p>
                </div>
            @endif
        </section>
    </main>

    <!-- Notifikasi -->
    @if (session('crud_success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: @json(session('crud_success')),
                confirmButtonColor: '#6d28d9'
            });
        </script>
    @endif

    @if (session('crud_error'))
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: @json(session('crud_error')),
                confirmButtonColor: '#e14b56'
            });
        </script>
    @endif

    @if ($errors->any())
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                html: @json(collect($errors->all())->map(fn($m) => e($m))->implode('<br>')),
                confirmButtonColor: '#e14b56'
            });
        </script>
    @endif

    <script>
        function konfirmasiHapusFoto() {
            Swal.fire({
                title: 'Hapus foto profil?',
                text: 'Foto Anda akan dihapus dan diganti gambar bawaan.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e14b56',
                cancelButtonText: 'Batal',
                confirmButtonText: 'Ya, hapus'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('form-hapus-foto').submit();
                }
            });
        }
    </script>
</body>

</html>