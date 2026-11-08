# 📦 Inventaris Barang Laravel

Sistem Manajemen Inventaris Barang (*Inventory Management System*) berbasis web yang dibangun menggunakan **Laravel**, **Tailwind CSS**, dan **Docker**. Aplikasi ini dirancang untuk memudahkan pengelolaan stok barang, transaksi masuk/keluar, serta manajemen multi-tenant dengan pembatasan hak akses yang ketat.

## ✨ Fitur Utama

- 🔐 **Role-Based Access Control (RBAC)**: Menggunakan `spatie/laravel-permission` untuk membatasi akses antara Admin, Pengelola, dan User.
- 🌐 **Google OAuth 2.0 Login**: Integrasi login menggunakan akun Google via `laravel/socialite`.
- 📧 **Email OTP Verification**: Verifikasi pendaftaran akun menggunakan kode OTP yang dikirim ke email.
- 🧭 **Dynamic Breadcrumbs**: Navigasi breadcrumb yang responsif menggunakan `diglactic/laravel-breadcrumbs`, dikustomisasi dengan Tailwind CSS (mendukung Light & Dark Mode).
- 🎨 **Modern UI/UX**: Antarmuka yang bersih dan interaktif menggunakan **Tailwind CSS**, **Alpine.js**, dan **Font Awesome**.
- 🐳 **Fully Dockerized**: Lingkungan pengembangan siap pakai menggunakan Docker (Nginx, PHP-FPM 8.4, MySQL 8.0, dan Node.js 20).

## 🛠️ Teknologi yang Digunakan

| Kategori | Teknologi |
|----------|-----------|
| Backend | Laravel 11/12, PHP 8.4 |
| Frontend | Tailwind CSS, Vite, Alpine.js |
| Database | MySQL 8.0 |
| Autentikasi & Otorisasi | Spatie Laravel Permission, Laravel Socialite |
| Navigasi | Diglactic Laravel Breadcrumbs |
| Infrastruktur | Docker & Docker Compose |

---

## 🚀 Panduan Instalasi (Menggunakan Docker)

Pastikan **Docker** dan **Docker Compose** sudah terinstal di komputer Anda.

### 1. Clone Repository

```bash
git clone https://github.com/mlnaarianto/inventaris-barang-laravel.git
cd inventaris-barang-laravel
```

### 2. Siapkan Environment File

Duplikat file `.env.example` menjadi `.env`:

```bash
cp .env.example .env
```

Sesuaikan konfigurasi database dan email di dalam file `.env`. Untuk Docker, pastikan konfigurasi database seperti ini:

```env
DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=philo
DB_USERNAME=root
DB_PASSWORD=root
```

Untuk fitur login Google dan OTP email, isi juga kredensial berikut (sesuaikan dengan akun Anda):

```env
GOOGLE_CLIENT_ID=
GOOGLE_CLIENT_SECRET=
GOOGLE_REDIRECT_URI=http://localhost:8000/auth/google/callback

MAIL_MAILER=smtp
MAIL_HOST=
MAIL_PORT=587
MAIL_USERNAME=
MAIL_PASSWORD=
```

### 3. Jalankan Container Docker

```bash
docker compose up -d
```

Tunggu hingga semua container (`philo_app`, `philo_web`, `philo_db`, `philo_vite`) berstatus *running* atau *healthy*.

### 4. Setup Aplikasi

Jalankan perintah berikut secara berurutan:

```bash
# Install dependensi PHP
docker compose exec app composer install

# Generate Application Key
docker compose exec app php artisan key:generate

# Jalankan migrasi database dan seeder
docker compose exec app php artisan migrate --seed

# Buat symbolic link untuk storage (opsional, untuk upload gambar)
docker compose exec app php artisan storage:link
```

### 5. Akses Aplikasi

Buka browser dan kunjungi 👉 **http://localhost:8000**

> **Catatan:** Jika Anda mengubah port di `docker-compose.yml`, sesuaikan URL di atas.

---

## 🐳 Perintah Docker yang Sering Digunakan

| Keperluan | Perintah |
|-----------|----------|
| Menjalankan aplikasi | `docker compose up -d` |
| Mematikan aplikasi | `docker compose down` |
| Melihat log real-time | `docker compose logs -f` |
| Masuk ke terminal container PHP | `docker compose exec app bash` |
| Menjalankan Artisan | `docker compose exec app php artisan [perintah]` |
| Menjalankan Composer | `docker compose exec app composer [perintah]` |

---

## 📄 Lisensi

Proyek ini dilisensikan di bawah [MIT License](LICENSE). Info lebih lanjut tentang lisensi ini: [opensource.org/license/MIT](https://opensource.org/license/MIT).

Anda bebas menggunakan, memodifikasi, dan mendistribusikan perangkat lunak ini, baik untuk keperluan pribadi maupun komersial, dengan tetap menyertakan atribusi hak cipta asli.

Copyright © 2026 Maulana Arianto