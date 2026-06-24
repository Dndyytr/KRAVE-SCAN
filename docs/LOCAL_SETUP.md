# Panduan Setup Lokal KraveScan

Dokumen ini memandu Anda dalam melakukan instalasi, konfigurasi, dan menjalankan KraveScan di lingkungan lokal (Local Environment).

---

## 1. Prasyarat Sistem (Prerequisites)

Sebelum memulai instalasi, pastikan komputer Anda telah terinstal modul-modul berikut:
- **PHP 8.3** atau versi terbaru. Ekstensi PHP berikut harus aktif: `openssl`, `pdo`, `mbstring`, `tokenizer`, `xml`, `ctype`, `json`, `bcmath`.
- **Composer** (Dependency manager PHP).
- **Node.js (LTS)** & **NPM** (Untuk build frontend).
- **MySQL / MariaDB** (Disarankan menggunakan XAMPP atau Laragon).
- Web browser modern (Chrome, Edge, Firefox, Safari).

---

## 2. Langkah Instalasi

Ikuti langkah-langkah berikut secara berurutan:

### Langkah A: Ekstrak atau Clone Repositori
Ekstrak file source code KraveScan ke direktori web server Anda (misal `C:\xampp\htdocs\krave-scan` atau direktori kerja pilihan Anda).

### Langkah B: Instalasi Dependensi PHP & JavaScript
Buka terminal (Command Prompt, PowerShell, atau Git Bash) di dalam folder proyek, lalu jalankan perintah:

```bash
# Menginstal dependensi PHP
composer install

# Menginstal dependensi JavaScript/CSS
npm install
```

### Langkah C: Konfigurasi Environment File
Salin file `.env.example` menjadi `.env` dengan menjalankan perintah terminal:

```bash
copy .env.example .env
```
*(Atau lakukan copy-paste secara manual melalui File Explorer).*

Buka file `.env` menggunakan editor teks (VS Code, Notepad, dll.) untuk melakukan konfigurasi kunci di bawah ini.

---

## 3. Konfigurasi Database

1. Pastikan server MySQL Anda (misal dari XAMPP Control Panel) sudah berjalan.
2. Buat database baru bernama `krave_scan` melalui phpMyAdmin (`http://localhost/phpmyadmin`) atau melalui query SQL:
   ```sql
   CREATE DATABASE krave_scan;
   ```
3. Sesuaikan konfigurasi database pada file `.env` Anda:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=krave_scan
   DB_USERNAME=root
   DB_PASSWORD=
   ```
   *(Kosongkan `DB_PASSWORD` atau sesuaikan dengan password MySQL lokal Anda).*

---

## 4. Konfigurasi Pengiriman Email (SMTP)

Untuk menguji fitur pengiriman notifikasi via email secara lokal, Anda dapat menggunakan salah satu dari opsi berikut pada `.env` Anda:

### Opsi 1: Menggunakan Mailpit (Direkomendasikan untuk lokal)
Jika Anda menggunakan **Laragon**, Mailpit biasanya sudah terintegrasi. Atau Anda dapat menginstalnya secara mandiri.
Konfigurasi `.env`:
```env
MAIL_MAILER=smtp
MAIL_HOST=127.0.0.1
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="no-reply@kravescan.test"
MAIL_FROM_NAME="KraveScan Local"
```
Anda dapat melihat email masuk dengan membuka browser pada alamat `http://localhost:8025`.

### Opsi 2: Menggunakan Mailtrap (Layanan Cloud Free)
Daftar di [Mailtrap.io](https://mailtrap.io/), buat Inbox baru, lalu gunakan kredensial SMTP yang disediakan:
```env
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_mailtrap_username
MAIL_PASSWORD=your_mailtrap_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="no-reply@kravescan.test"
MAIL_FROM_NAME="KraveScan Local"
```

---

## 5. Inisialisasi Aplikasi (Key & Seed)

Setelah konfigurasi `.env` dan database siap, jalankan perintah inisialisasi berikut di terminal:

```bash
# Generate application key
php artisan key:generate

# Jalankan migrasi database dan seed data dummy/default
php artisan migrate --seed
```

Perintah `migrate --seed` akan secara otomatis membuat tabel-tabel yang diperlukan, mengisi data cabang awal, kategori menu, menu, aturan otomatisasi, serta akun staf pengujian.

### Kredensial Default Staf (Seeder)
Anda dapat menggunakan akun-akun berikut untuk masuk ke sistem:

| Role | Cabang | Email | Password | Hak Akses |
|------|--------|-------|----------|-----------|
| **Super Admin** | Semua Cabang | `superadmin@kravescan.com` | `password` | Akses penuh, mengelola cabang, ganti cabang aktif. |
| **Admin Jakarta** | Jakarta | `admin.jkt@kravescan.com` | `password` | Mengelola menu, stok, staf, & melihat laporan cabang Jakarta. |
| **Cashier Jakarta** | Jakarta | `cashier.jkt@kravescan.com` | `password` | Memproses pesanan, memantau pesanan di cabang Jakarta. |
| **Admin Bandung** | Bandung | `admin.bdg@kravescan.com` | `password` | Mengelola menu, stok, staf, & melihat laporan cabang Bandung. |
| **Cashier Bandung** | Bandung | `cashier.bdg@kravescan.com` | `password` | Memproses pesanan, memantau pesanan di cabang Bandung. |

---

## 6. Menjalankan Server Lokal (Development)

KraveScan menggunakan Vite untuk kompilasi frontend dan antrian database (Queue) untuk tugas latar belakang. Jalankan server pembangunan secara bersamaan menggunakan command pintasan:

```bash
composer dev
```

Command di atas secara otomatis menjalankan tiga proses secara paralel:
1. **Web Server**: `php artisan serve` (Meng-host aplikasi pada `http://127.0.0.1:8000`).
2. **Queue Listener**: `php artisan queue:listen --tries=1` (Memproses antrian otomatisasi RPA dan pengiriman notifikasi email di latar belakang).
3. **Vite Server**: `npm run dev` (Melakukan Hot Module Replacement untuk file CSS/JS agar perubahan UI langsung terlihat).

Akses aplikasi di browser pada alamat **[http://127.0.0.1:8000](http://127.0.0.1:8000)**.

---

## 7. Menjalankan Task Scheduler (Opsional namun Disarankan)

Beberapa tugas pemeliharaan berkala (seperti pembersihan otomatis log aktivitas berusia > 30 hari dan notifikasi lama) dijalankan oleh scheduler Laravel. Di lingkungan lokal, jalankan perintah scheduler scheduler di tab terminal terpisah:

```bash
php artisan schedule:work
```

Perintah ini akan menyimulasikan cron job yang memicu scheduler setiap menit.
