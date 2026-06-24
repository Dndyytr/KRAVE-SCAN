# Panduan Pengguna (User Guide) KraveScan

Dokumen ini berisi panduan operasional sistem KraveScan bagi **Super Admin (Owner)**, **Admin Cabang**, dan **Kasir (Cashier)**.

---

## 1. Akses Masuk Sistem (Login)

1. Buka browser dan arahkan ke alamat: **[http://127.0.0.1:8000/login](http://127.0.0.1:8000/login)**.
2. Masukkan email dan password sesuai dengan peran Anda (silakan lihat tabel kredensial di [Panduan Setup Lokal](LOCAL_SETUP.md)).
3. Halaman login menampilkan visualisasi khas lokal (*Bakso Cinta Ciamis*) dan mendukung perpindahan bahasa (Bahasa Indonesia / English) melalui switcher di pojok kanan atas.

---

## 2. Panduan Peran: Super Admin (Owner)

Super Admin memiliki hak akses tanpa batas untuk memantau seluruh ekosistem KraveScan lintas cabang.

### A. Fitur Pemindah Cabang (Branch Switcher)
- Di pojok kanan atas navbar (di sebelah nama profil), terdapat dropdown pilihan cabang.
- Super Admin dapat mengubah cabang aktif kapan saja. Seluruh data pada dashboard, daftar menu, daftar pesanan, stok, laporan, dan otomatisasi akan langsung tersaring secara otomatis sesuai cabang yang dipilih.
- Pilih *Semua Cabang* untuk melihat total akumulasi metrik keseluruhan.

### B. Manajemen Pengguna (Staf & Kasir)
- Masuk ke menu **Kelola Staf** di sidebar.
- Anda dapat mendaftarkan Admin Cabang baru atau Kasir baru, serta menetapkan cabang kerja mereka.
- Anda dapat mengaktifkan atau menonaktifkan akun staf melalui tombol toggle **Status Aktif**. Akun yang dinonaktifkan tidak akan bisa masuk ke dalam sistem.

### C. Log Audit & Aktivitas Staf
- Masuk ke menu **Log Aktivitas** di sidebar.
- Sistem mencatat seluruh tindakan sensitif seperti login, logout, pembuatan menu baru, pembaruan stok, pembatalan pesanan, dan perubahan status otomatisasi.
- Halaman ini membantu Anda melacak *siapa melakukan apa, kapan, dan di cabang mana*.

---

## 3. Panduan Peran: Admin Cabang

Admin Cabang bertanggung jawab atas operasional menu, ketersediaan bahan/stok, dan laporan keuangan di cabang spesifik tempat mereka ditugaskan.

### A. Manajemen Menu & Kategori
- **Kategori**: Buat kategori menu (misalnya: *Makanan Utama*, *Minuman*, *Camilan*).
- **Menu**: Tambahkan menu baru. Saat mengunggah gambar menu, Anda dapat menggunakan fitur **Drag and Drop Uploader** bertenaga Alpine.js untuk mempermudah pemrosesan gambar.

### B. Manajemen Stok Bahan & Hubungan Menu
- Masuk ke menu **Kelola Stok**.
- Buat item stok baru (misalnya: *Biji Kopi (Gram)*, *Cup Plastik (Pcs)*) dan tentukan batas stok minimum (*Minimum Threshold*).
- Saat membuat atau mengedit **Menu**, Anda dapat menghubungkan menu tersebut dengan item stok (misalnya: Menu *Americano* dihubungkan dengan item stok *Biji Kopi* sebanyak *15 Gram*).
- Setiap kali pesanan dibayar, stok bahan yang terhubung dengan menu tersebut akan dipotong secara otomatis di latar belakang.

### C. Analisis Laporan Keuangan
- Masuk ke menu **Laporan Penjualan** / **Laporan Menu** / **Metode Pembayaran**.
- Sistem menyediakan diagram visual interaktif yang dibangun menggunakan **Apache ECharts** untuk memudahkan analisis tren.
- Anda dapat mengunduh laporan dalam format file spreadsheet **Excel (.xlsx)** atau **CSV** dengan menekan tombol **Ekspor**.

---

## 4. Panduan Peran: Kasir (Cashier)

Kasir adalah ujung tombak pelayanan transaksi harian di meja makan pelanggan.

### A. Sistem Notifikasi Real-time
- Di navbar bagian atas, terdapat ikon **Lonceng Notifikasi** dengan polling otomatis (Alpine.js).
- Setiap kali ada pelanggan baru yang melakukan pemesanan (Checkout), lonceng akan menampilkan badge merah berisi jumlah pesanan baru dan mengeluarkan suara notifikasi kecil.
- Klik ikon lonceng untuk melihat detail pesanan baru secara instan tanpa perlu memuat ulang (refresh) halaman.

### B. Memproses Pesanan & Konfirmasi Pembayaran
- Masuk ke menu **Daftar Pesanan**.
- Pesanan baru masuk akan berstatus **Pending**. Kasir dapat melihat daftar item belanja pelanggan beserta nomor meja mereka.
- **Proses Pembayaran**: Klik pesanan, pilih metode pembayaran yang digunakan pelanggan (Tunai, QRIS, Transfer Bank), lalu klik **Konfirmasi Pembayaran**.
- Setelah pembayaran dikonfirmasi:
  1. Status pesanan berubah menjadi **Paid**.
  2. Sistem otomatis memicu antrian RPA di latar belakang untuk membuat file **PDF Struk Pembayaran**.
  3. Stok bahan terkait menu yang dipesan akan berkurang otomatis.
  4. Sistem memeriksa apakah ada bahan yang di bawah batas minimum dan segera memicu notifikasi stok rendah ke Admin jika terdeteksi.

---

## 5. Panduan Pelanggan (Customer Ordering Flow)

1. Pelanggan memindai QR Code di meja makan mereka. QR Code tersebut mengarah ke tautan lokal:
   `http://127.0.0.1:8000/branches/{branch_code}/table/{table_number}`
   *(Contoh: [http://127.0.0.1:8000/branches/JKT-01/table/5](http://127.0.0.1:8000/branches/JKT-01/table/5))*
2. Pelanggan dapat menjelajahi menu pastel yang responsif dan menambahkan menu ke keranjang belanja mereka.
3. **Pencarian Cepat Bertenaga AI**:
   - Pelanggan dapat menekan ikon **Kamera/AI** di kolom pencarian menu.
   - Unggah foto makanan/minuman yang diinginkan, dan sistem AI akan otomatis mencarikan menu yang sesuai.
4. Pelanggan melakukan **Checkout** untuk mengirimkan pesanan langsung ke sistem kasir di cabang tersebut. Status pesanan dapat dipantau secara real-time dari halaman status pesanan pelanggan.
