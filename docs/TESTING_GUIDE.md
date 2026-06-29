# 📋 Panduan Testing Visual KraveScan
### Panduan Lengkap untuk Pengguna Awam — Cara Menggunakan & Menguji Aplikasi

> **Dokumen ini ditujukan bagi siapa saja** yang ingin mencoba dan menguji tampilan serta fungsionalitas aplikasi KraveScan dari awal, tanpa perlu memahami kode program.

---

## 🗂️ Daftar Isi

1. [Persiapan Awal — Jalankan Aplikasi](#1-persiapan-awal--jalankan-aplikasi)
2. [Halaman Utama & Login](#2-halaman-utama--login)
3. [Skenario A — Sebagai Pelanggan (Customer)](#3-skenario-a--sebagai-pelanggan-customer)
4. [Skenario B — Sebagai Kasir (Cashier)](#4-skenario-b--sebagai-kasir-cashier)
5. [Skenario C — Sebagai Admin Cabang](#5-skenario-c--sebagai-admin-cabang)
6. [Skenario D — Sebagai Super Admin (Pemilik)](#6-skenario-d--sebagai-super-admin-pemilik)
7. [Skenario Lengkap — Simulasi Transaksi Penuh](#7-skenario-lengkap--simulasi-transaksi-penuh)
8. [Daftar Akun & Kata Sandi](#8-daftar-akun--kata-sandi)
9. [URL Penting](#9-url-penting)
10. [Troubleshooting — Masalah Umum](#10-troubleshooting--masalah-umum)

---

## 1. Persiapan Awal — Jalankan Aplikasi

Sebelum memulai pengujian, pastikan aplikasi sudah **berjalan di komputer Anda**. Jika belum, ikuti langkah berikut:

### Langkah 1: Buka Terminal / Command Prompt
Buka **Command Prompt** atau **PowerShell** di Windows (tekan `Windows + R`, ketik `cmd`, tekan Enter).

Arahkan terminal ke folder proyek KraveScan. Misalnya:
```
cd D:\Projek Laravel\krave-scan
```

### Langkah 2: Jalankan Server Aplikasi
Ketik perintah berikut dan tekan Enter:
```
composer dev
```

Tunggu hingga muncul teks seperti ini di terminal (tanda aplikasi sudah berjalan):
```
INFO  Server running on [http://127.0.0.1:8000].
```

> ⚠️ **Jangan tutup terminal ini!** Biarkan terminal tetap terbuka selama Anda melakukan pengujian.

### Langkah 3: Buka Browser
Buka browser favorit Anda (Chrome, Firefox, Edge) dan kunjungi alamat:

**👉 http://127.0.0.1:8000**

Anda akan melihat halaman selamat datang KraveScan.

---

## 2. Halaman Utama & Login

### Halaman Selamat Datang
Ketika Anda membuka `http://127.0.0.1:8000`, Anda akan melihat halaman default Laravel. Ini normal — halaman ini bukan antarmuka utama aplikasi.

### Cara Login ke Dashboard Staf
Untuk masuk ke panel Admin atau Kasir, buka URL berikut:

**👉 http://127.0.0.1:8000/login**

Halaman login akan menampilkan formulir dengan desain dua panel:
- **Panel kiri**: Branding dan ilustrasi KraveScan.
- **Panel kanan**: Formulir email, kata sandi, dan tombol **Login**.

Gunakan salah satu akun dari [Daftar Akun](#8-daftar-akun--kata-sandi) di bawah ini.

---

## 3. Skenario A — Sebagai Pelanggan (Customer)

Pelanggan **tidak perlu login** ke sistem. Pelanggan mengakses menu melalui URL yang mewakili QR Code dari meja tertentu.

### Langkah A.1: Akses Halaman Menu Pelanggan
Buka URL berikut di browser (simulasi scan QR Code di Meja No. 1 Cabang Jakarta):

**👉 http://127.0.0.1:8000/c/JKT-01/table/1**

Anda akan langsung melihat **halaman daftar menu** dengan tampilan kartu produk yang menarik.

> **Penjelasan format URL:**
> - `c/` = Customer area (area pelanggan)
> - `JKT-01` = Kode cabang Jakarta (Bisa diganti `BDG-01` untuk Bandung)
> - `table/1` = Nomor meja (Bisa diganti angka lain, misal `table/5`)

---

### Langkah A.2: Jelajahi Menu
Di halaman menu Anda dapat:

**a) Melihat Semua Menu**
Semua menu yang aktif akan ditampilkan dalam bentuk kartu bergambar.

**b) Filter Berdasarkan Kategori**
Klik tombol kategori di bagian atas daftar menu untuk memfilter:
- ☕ **Coffee** — Espresso, Americano, Caffe Latte, Caramel Macchiato
- 🍵 **Non-Coffee** — Matcha Latte, Chocolate Milk, Iced Lemon Tea
- 🍛 **Food** — Nasi Goreng Kampung, Chicken Katsu Curry, Spaghetti Bolognese
- 🍰 **Dessert** — Croissant Plain, Chocolate Fudge Cake

**c) Cari Menu**
Gunakan kotak pencarian untuk mencari menu berdasarkan nama atau deskripsi.

---

### Langkah A.3: Tambah Menu ke Keranjang
1. Temukan menu yang Anda inginkan (misalnya **Caffe Latte** dengan harga Rp 28.000).
2. Klik tombol **tambah (+)** di pojok kanan bawah kartu menu.
3. Sebuah notifikasi kecil (`toast`) akan muncul di layar menandakan item berhasil ditambahkan.
4. Badge jumlah item di ikon keranjang (🛒) di navigasi bawah akan bertambah secara otomatis tanpa reload halaman.

Ulangi untuk beberapa menu lain jika diinginkan.

---

### Langkah A.4: Lihat & Ubah Keranjang
1. Klik ikon **Keranjang (🛒)** di navigasi bawah, atau buka URL:
   **👉 http://127.0.0.1:8000/c/JKT-01/cart**
2. Halaman keranjang menampilkan semua item beserta subtotal dan total harga.
3. Klik tombol **( + )** atau **( − )** di samping setiap item untuk mengubah jumlah pesanan.
4. Klik tombol **( × )** atau kurangi jumlah hingga 0 untuk menghapus item dari keranjang.
5. Total harga akan diperbarui secara otomatis.

---

### Langkah A.5: Checkout / Konfirmasi Pesanan
1. Dari halaman keranjang, klik tombol **"Pesan Sekarang"** (atau tombol konfirmasi pesanan yang tersedia).
2. Sistem akan membuat pesanan dan mengarahkan Anda ke **halaman status pesanan**.
3. Halaman status pesanan menampilkan:
   - Nomor pesanan
   - Daftar item yang dipesan
   - Total harga
   - Status pesanan saat ini (awalnya: **"Menunggu Pembayaran"**)
4. Halaman ini akan **otomatis menyegarkan dirinya setiap 15 detik** untuk memantau perubahan status pesanan.

> 📝 **Catat nomor pesanan Anda!** Anda akan membutuhkannya untuk memverifikasi dari sisi kasir.

---

## 4. Skenario B — Sebagai Kasir (Cashier)

Kasir bertugas menerima pesanan masuk, memproses pembayaran, dan mencetak struk.

### Langkah B.1: Login sebagai Kasir Jakarta
1. Buka **http://127.0.0.1:8000/login**
2. Masukkan email: `cashier.jkt@kravescan.com`
3. Masukkan kata sandi: `password`
4. Klik tombol **Login**

Anda akan diarahkan ke halaman **Dashboard** khusus Kasir.

---

### Langkah B.2: Lihat Daftar Pesanan Masuk
1. Dari dashboard, klik menu **"Daftar Pesanan"** di sidebar kiri, atau kunjungi:
   **👉 http://127.0.0.1:8000/cashier/orders**
2. Pesanan yang baru dibuat oleh pelanggan (dari Skenario A) akan muncul di bagian atas dengan status **"Menunggu Pembayaran"** berwarna kuning.
3. Halaman ini otomatis menyegarkan tampilan secara berkala.
4. Anda dapat memfilter pesanan berdasarkan status atau tanggal.

---

### Langkah B.3: Lihat Detail & Proses Pembayaran
1. Klik tombol **"Detail"** atau klik baris pesanan yang ingin diproses.
2. Halaman detail pesanan menampilkan ringkasan semua item, jumlah, dan total harga.
3. Di bagian bawah, pilih **metode pembayaran**:

**Opsi 1 — Pembayaran Tunai (Cash):**
- Pilih tab/opsi **"Tunai"**.
- Masukkan jumlah uang yang diterima dari pelanggan (contoh: `50000` untuk Rp 50.000).
- Klik tombol denominasi cepat seperti **"Uang Pas"**, **"+10k"**, **"+50k"** untuk mempercepat input.
- Sistem otomatis menghitung **kembalian** di bawah kolom input.
- Klik **"Konfirmasi Pembayaran"**.

**Opsi 2 — Pembayaran QRIS:**
- Pilih tab/opsi **"QRIS"**.
- Sistem akan menampilkan simulasi pembayaran QRIS.
- Klik **"Tandai Sudah Dibayar"** untuk mengonfirmasi pembayaran.

4. Setelah konfirmasi, sistem akan:
   - Mengubah status pesanan menjadi **"Dikonfirmasi"**.
   - Otomatis mengurangi stok bahan baku yang terhubung.
   - Mengarahkan ke halaman **struk digital**.

---

### Langkah B.4: Lihat & Cetak Struk
1. Halaman struk digital menampilkan informasi:
   - Nomor struk (format: `REC-JKT-01-TANGGAL-ID`)
   - Nama cabang dan tanggal/jam transaksi
   - Daftar item pesanan beserta harga satuan
   - Subtotal, pajak (jika ada), dan grand total
   - Jumlah bayar dan kembalian (khusus tunai)
2. Halaman struk dirancang mirip kertas **thermal printer** (hitam-putih minimalis).
3. Untuk mencetak, klik **"Cetak Struk"** atau gunakan shortcut browser `Ctrl+P`.
   - Elemen navigasi web akan otomatis tersembunyi saat dicetak.

---

### Langkah B.5: Kelola Status Pesanan (Opsional)
Kasir juga dapat memperbarui status pesanan secara manual:
1. Dari halaman detail pesanan, gunakan tombol **"Update Status"**.
2. Alur status pesanan yang tersedia:
   ```
   Menunggu Pembayaran → Dikonfirmasi → Sedang Disiapkan → Selesai
                                                           ↓
                                                       Dibatalkan
   ```

---

## 5. Skenario C — Sebagai Admin Cabang

Admin Cabang mengelola menu, stok, kategori, laporan, dan data staf untuk cabang mereka.

### Langkah C.1: Login sebagai Admin Jakarta
1. Buka **http://127.0.0.1:8000/login**
2. Email: `admin.jkt@kravescan.com` | Kata sandi: `password`
3. Klik **Login**

---

### Langkah C.2: Eksplorasi Dashboard Admin
Dashboard admin menampilkan:
- **Pendapatan Hari Ini** — Total transaksi sukses hari ini.
- **Pesanan Hari Ini** — Jumlah order masuk hari ini.
- **Pesanan Pending** — Order yang belum dibayar.
- **Stok Menipis** — Bahan baku di bawah batas minimum.
- **Mini Chart (Sparkline)** — Grafik visualisasi kecil di setiap kartu metrik.
- **Tabel Pesanan Terbaru** — 5 pesanan terakhir.
- **Tabel Stok Kritis** — Bahan baku yang perlu diisi ulang.

---

### Langkah C.3: Kelola Kategori Menu

**Lihat Daftar Kategori:**
1. Klik **"Kategori Menu"** di sidebar kiri (di bawah "Daftar Menu").
2. Semua kategori cabang Jakarta ditampilkan beserta jumlah menu yang terhubung.

**Tambah Kategori Baru:**
1. Klik tombol **"+ Tambah Kategori"** di pojok kanan atas.
2. Isi nama kategori baru, contoh: `Minuman Segar`.
3. Klik **"Simpan Kategori"**.
4. Notifikasi hijau "Kategori berhasil ditambahkan" akan muncul di bagian atas halaman.

**Edit Kategori:**
1. Klik tombol **"Edit"** di baris kategori yang ingin diubah.
2. Ubah namanya, lalu klik **"Simpan Perubahan"**.

**Hapus Kategori:**
1. Klik tombol **"Hapus"** di baris kategori.
2. Modal konfirmasi akan muncul.
3. Klik **"Hapus"** untuk mengonfirmasi.

> ⚠️ **Catatan Penting:** Kategori yang **masih memiliki menu terhubung** **tidak dapat dihapus**. Sistem akan menampilkan pesan error berwarna merah. Pindahkan atau hapus menu di kategori tersebut terlebih dahulu sebelum menghapus kategorinya.

---

### Langkah C.4: Kelola Menu Makanan

**Lihat Daftar Menu:**
1. Klik **"Daftar Menu"** di sidebar.
2. Tabel menampilkan semua menu dengan gambar thumbnail, nama, kategori, harga, dan status ketersediaan.

**Tambah Menu Baru:**
1. Klik tombol **"+ Tambah Menu"**.
2. Isi formulir:
   - **Nama Menu**: Contoh `Es Kopi Susu`
   - **Kategori**: Pilih dari dropdown (misal `Coffee`)
   - **Harga**: Contoh `28000` (tanpa titik/koma)
   - **Status**: Aktifkan toggle "Aktif" agar menu langsung tersedia untuk pelanggan
   - **Stok Barang**: (Opsional) Hubungkan ke bahan baku stok jika ingin penghitungan otomatis
   - **Deskripsi**: Tulis deskripsi singkat menu
   - **Foto Produk**: Klik area upload atau seret file gambar (format JPG/PNG/WEBP, maks 2MB)
3. Klik **"Simpan Menu"**.

**Toggle Aktif/Nonaktif Menu:**
1. Di tabel daftar menu, klik tombol **"Aktif"** atau **"Nonaktif"** di kolom Status.
2. Status menu berubah **secara langsung tanpa reload halaman** (AJAX).
3. Menu yang dinonaktifkan tidak akan muncul di halaman pelanggan.

**Edit Menu:**
1. Klik tombol **"Edit"** di baris menu yang ingin diubah.
2. Halaman edit menampilkan data lama sekaligus preview gambar yang sudah terunggah.
3. Perbarui data yang diinginkan, lalu klik **"Simpan Menu"**.

**Hapus Menu:**
1. Klik tombol **"Hapus"** di baris menu.
2. Modal konfirmasi akan muncul — klik **"Hapus"** untuk mengonfirmasi.
3. File gambar menu di server juga akan terhapus otomatis.

---

### Langkah C.5: Kelola Stok Barang

1. Klik **"Stok Barang"** di sidebar.
2. Daftar stok menampilkan nama bahan, kuantitas saat ini, batas minimum, dan satuan.
3. Bahan dengan stok di bawah minimum ditandai badge **"Menipis"** berwarna merah.

**Tambah Stok Baru:**
1. Klik **"+ Tambah Stok"**.
2. Isi: Nama (misal `Biji Kopi`), Kuantitas awal (misal `50`), Batas minimum (misal `10`), Satuan (misal `kg`).
3. Klik **"Simpan"**.

**Edit/Restock Stok:**
1. Klik **"Edit"** di baris stok yang ingin diubah.
2. Perbarui nilai kuantitas untuk mensimulasikan penambahan stok.
3. Klik **"Simpan"**.

---

### Langkah C.6: Lihat Laporan

**Laporan Penjualan:**
1. Di sidebar, klik **"Laporan"** → **"Laporan Penjualan"**.
2. Pilih rentang tanggal (misal: hari ini) dan klik **"Filter"**.
3. Grafik batang (bar chart) Apache ECharts menampilkan total transaksi per hari.
4. Tabel di bawah menampilkan detail transaksi yang dapat diekspor ke Excel.

**Laporan Performa Menu:**
1. Klik **"Laporan"** → **"Laporan Performa Menu"**.
2. Grafik lingkaran (pie chart) menampilkan distribusi penjualan per menu.
3. Tabel menampilkan menu terlaris beserta total terjual.

**Laporan Metode Pembayaran:**
1. Klik **"Laporan"** → **"Laporan Metode Pembayaran"**.
2. Grafik menampilkan perbandingan penggunaan metode pembayaran (Tunai vs QRIS vs Transfer).

---

### Langkah C.7: Kelola Data Staf

1. Klik **"Kelola Staf"** di sidebar.
2. Daftar semua staf cabang Jakarta ditampilkan.

**Tambah Staf Baru:**
1. Klik **"+ Tambah Staf"**.
2. Isi nama, email, kata sandi, dan pilih peran (**Admin** atau **Kasir**).
3. Klik **"Simpan"**.

**Nonaktifkan Staf:**
1. Klik tombol toggle **"Aktif/Nonaktif"** di baris staf.
2. Staf yang dinonaktifkan tidak akan bisa login ke sistem.

> 💡 **Catatan:** Admin Cabang hanya dapat melihat dan mengelola staf yang berada di cabang yang sama.

---

### Langkah C.8: Monitor Log Aktivitas

1. Di sidebar, klik **"Log Aktivitas"**.
2. Halaman ini menampilkan rekam jejak aktivitas seluruh pengguna di cabang:
   - Siapa yang melakukan aksi
   - Aksi apa yang dilakukan (login, ubah menu, hapus stok, dsb.)
   - Kapan aksi tersebut terjadi
3. Gunakan filter untuk menyaring log berdasarkan jenis aksi atau tanggal.

---

## 6. Skenario D — Sebagai Super Admin (Pemilik)

Super Admin memiliki akses ke **semua cabang** sekaligus dan dapat berpindah-pindah antar cabang.

### Langkah D.1: Login sebagai Super Admin
1. Buka **http://127.0.0.1:8000/login**
2. Email: `superadmin@kravescan.com` | Kata sandi: `password`
3. Klik **Login**

---

### Langkah D.2: Fitur Branch Switcher
Di bagian atas sidebar terdapat **dropdown pemilih cabang** yang hanya muncul untuk Super Admin.

1. Klik dropdown tersebut (defaultnya menampilkan semua data gabungan).
2. Pilih **"Krave Scan Jakarta"** — dashboard, laporan, dan semua data akan berganti menampilkan data khusus Jakarta.
3. Pilih **"Krave Scan Bandung"** — tampilan beralih ke data Bandung.
4. Pilih kembali opsi default untuk melihat data gabungan semua cabang.

---

### Langkah D.3: Kelola Semua Staf Lintas Cabang
1. Klik **"Kelola Staf"** di sidebar.
2. Super Admin dapat melihat **seluruh staf dari semua cabang**.
3. Saat membuat staf baru, Super Admin dapat memilih cabang mana yang akan ditugaskan kepada staf tersebut.

---

### Langkah D.4: Monitor Log Aktivitas Semua Cabang
1. Klik **"Log Aktivitas"** di sidebar.
2. Tampilan menampilkan aktivitas dari **semua cabang sekaligus**.
3. Filter berdasarkan cabang untuk mempersempit hasil.

---

## 7. Skenario Lengkap — Simulasi Transaksi Penuh

Berikut adalah panduan langkah demi langkah untuk mensimulasikan satu siklus transaksi penuh dari awal hingga akhir. Disarankan membuka **2 tab browser** sekaligus.

---

### 🪑 TAB 1 — Sisi Pelanggan
Buka tab browser pertama dan kunjungi:
```
http://127.0.0.1:8000/c/JKT-01/table/3
```

1. Tambahkan **Caffe Latte** (Rp 28.000) ke keranjang → klik **+**.
2. Tambahkan **Croissant Plain** (Rp 20.000) ke keranjang → klik **+**.
3. Klik ikon keranjang (🛒), pastikan total = **Rp 48.000**.
4. Klik tombol **"Pesan Sekarang"**.
5. Anda diarahkan ke halaman **Status Pesanan** dengan status: `Menunggu Pembayaran`.
6. Biarkan tab ini terbuka — Anda akan melihat status berubah setelah kasir memproses.

---

### 💼 TAB 2 — Sisi Kasir
Buka tab browser kedua dan login sebagai kasir:
```
http://127.0.0.1:8000/login
Email: cashier.jkt@kravescan.com
Password: password
```

1. Klik **"Daftar Pesanan"** di sidebar.
2. Pesanan dari Meja 3 akan muncul di bagian atas.
3. Klik **"Detail"** pada pesanan tersebut.
4. Lihat ringkasan: Caffe Latte + Croissant Plain = **Rp 48.000**.
5. Pilih metode pembayaran **"Tunai"**.
6. Masukkan nominal: `50000` (Rp 50.000).
7. Sistem menampilkan kembalian: **Rp 2.000**.
8. Klik **"Konfirmasi Pembayaran"**.
9. Anda diarahkan ke halaman **Struk Digital** dengan nomor struk (contoh: `REC-JKT-01-20260624-0001`).

---

### 🔄 Kembali ke TAB 1 — Verifikasi Status
1. Kembali ke tab pelanggan.
2. Status pesanan sekarang berubah menjadi **"Dikonfirmasi"** (warna hijau).
3. Jika kasir memperbarui status ke "Sedang Disiapkan" dan kemudian "Selesai", perubahan tersebut juga akan terlihat di sini dalam 15 detik.

✅ **Satu siklus transaksi penuh telah berhasil disimulasikan!**

---

## 8. Daftar Akun & Kata Sandi

| Peran | Cabang | Email Login | Kata Sandi | Akses |
|-------|--------|------------|------------|-------|
| **Super Admin** | Semua Cabang | `superadmin@kravescan.com` | `password` | Dashboard global, ganti cabang, kelola semua staf |
| **Admin Jakarta** | Jakarta | `admin.jkt@kravescan.com` | `password` | Menu, stok, kategori, laporan, staf cabang Jakarta |
| **Kasir Jakarta** | Jakarta | `cashier.jkt@kravescan.com` | `password` | Pesanan & pembayaran cabang Jakarta |
| **Admin Bandung** | Bandung | `admin.bdg@kravescan.com` | `password` | Menu, stok, kategori, laporan, staf cabang Bandung |
| **Kasir Bandung** | Bandung | `cashier.bdg@kravescan.com` | `password` | Pesanan & pembayaran cabang Bandung |

---

## 9. URL Penting

| Halaman | URL | Akses |
|---------|-----|-------|
| Halaman Utama | `http://127.0.0.1:8000` | Semua |
| Login | `http://127.0.0.1:8000/login` | Semua |
| Dashboard Staf | `http://127.0.0.1:8000/dashboard` | Admin & Kasir |
| Menu Pelanggan — Jakarta Meja 1 | `http://127.0.0.1:8000/c/JKT-01/table/1` | Semua (tanpa login) |
| Menu Pelanggan — Jakarta Meja 5 | `http://127.0.0.1:8000/c/JKT-01/table/5` | Semua (tanpa login) |
| Menu Pelanggan — Bandung Meja 1 | `http://127.0.0.1:8000/c/BDG-01/table/1` | Semua (tanpa login) |
| Keranjang Pelanggan Jakarta | `http://127.0.0.1:8000/c/JKT-01/cart` | Semua (tanpa login) |
| Daftar Pesanan Kasir | `http://127.0.0.1:8000/cashier/orders` | Kasir |
| Kelola Menu Admin | `http://127.0.0.1:8000/admin/menus` | Admin |
| Kelola Kategori Admin | `http://127.0.0.1:8000/admin/categories` | Admin |
| Kelola Stok Admin | `http://127.0.0.1:8000/admin/stocks` | Admin |
| Kelola Staf Admin | `http://127.0.0.1:8000/admin/users` | Admin |
| Laporan Penjualan | `http://127.0.0.1:8000/admin/reports/sales` | Admin |
| Log Aktivitas | `http://127.0.0.1:8000/admin/activity-logs` | Admin |

---

## 10. Troubleshooting — Masalah Umum

### ❌ Halaman tidak dapat diakses / Error 500
**Penyebab:** Server belum berjalan atau ada kesalahan konfigurasi.
**Solusi:**
1. Pastikan terminal dengan perintah `composer dev` masih terbuka dan berjalan.
2. Coba akses ulang setelah beberapa detik.
3. Jika masih error, coba jalankan `php artisan config:clear` di terminal baru.

---

### ❌ Login gagal meskipun email & kata sandi benar
**Penyebab:** Database belum di-seed dengan data akun default.
**Solusi:** Jalankan perintah berikut di terminal:
```
php artisan migrate:fresh --seed
```
> ⚠️ **Perhatian:** Perintah ini akan **menghapus seluruh data** dan mengisinya ulang dari awal.

---

### ❌ Pesanan pelanggan tidak muncul di daftar kasir
**Penyebab:** Pelanggan dan kasir berada di cabang yang berbeda.
**Solusi:**
- Pastikan URL pelanggan menggunakan kode cabang `JKT-01` saat login dengan akun kasir Jakarta.
- Atau gunakan `BDG-01` untuk cabang Bandung dengan akun kasir Bandung.

---

### ❌ Notifikasi Bell (🔔) tidak memunculkan pesanan baru
**Penyebab:** Queue listener (antrian latar belakang) tidak berjalan.
**Solusi:**
- Gunakan perintah `composer dev` (bukan `php artisan serve` saja) karena `composer dev` juga menjalankan Queue listener secara otomatis.

---

### ❌ Laporan menampilkan data kosong
**Penyebab:** Belum ada transaksi yang selesai pada rentang tanggal yang dipilih.
**Solusi:**
1. Lakukan simulasi transaksi penuh terlebih dahulu (lihat [Skenario 7](#7-skenario-lengkap--simulasi-transaksi-penuh)).
2. Kemudian buka laporan dan filter berdasarkan tanggal hari ini.

---

### ❌ Tombol "Hapus Kategori" menampilkan pesan error merah
**Penyebab:** Ini adalah perilaku yang **disengaja**. Kategori tidak dapat dihapus jika masih ada menu yang menggunakan kategori tersebut.
**Solusi:** Hapus atau pindahkan semua menu di kategori tersebut ke kategori lain terlebih dahulu, baru kemudian hapus kategorinya.

---

### ❌ Grafik (Chart) tidak muncul di dashboard
**Penyebab:** Vite dev server tidak berjalan, sehingga file JavaScript tidak terkompilasi.
**Solusi:**
- Gunakan perintah `composer dev` (yang juga menjalankan `npm run dev`).
- Atau jalankan `npm run build` untuk kompilasi produksi, lalu restart server.

---

> 📞 **Butuh bantuan lebih lanjut?** Rujuk ke dokumen:
> - [LOCAL_SETUP.md](./LOCAL_SETUP.md) — Panduan instalasi dan konfigurasi awal.
> - [USER_GUIDE.md](./USER_GUIDE.md) — Panduan operasional lengkap per peran.
> - [AI_RPA_GUIDE.md](./AI_RPA_GUIDE.md) — Panduan penggunaan fitur AI & otomatisasi.
