# KraveScan — QR-Code Based Ordering & Management System with AI and RPA

KraveScan adalah sistem manajemen restoran dan pemesanan makanan berbasis QR-Code modern yang dirancang untuk mendukung ekspansi multi-cabang secara seamless. Aplikasi ini dilengkapi dengan asisten pencarian menu berbasis kecerdasan buatan (AI) serta sistem otomatisasi proses (RPA) untuk efisiensi operasional.

Sistem ini dikembangkan menggunakan **Laravel 13** di bagian backend, dipadukan dengan **Alpine.js** dan **Tailwind CSS v4** untuk antarmuka pengguna (UI/UX) bertema pastel yang estetik dan premium.

---

## 🚀 Fitur Utama (Key Features)

### 1. Isolasi Multi-Cabang (Multi-Branch Scope)
- Isolasi data menu, stok, transaksi, dan laporan untuk tiap cabang melalui global scope `ScopedToBranch`.
- Fitur *Branch Switcher* instan bagi Super Admin/Owner untuk memantau data cabang mana pun dari navbar atas.
- Partisi file storage terisolasi untuk masing-masing cabang (`storage/app/public/branches/{branch_id}/`).

### 2. Pemesanan Pelanggan Responsif (QR-Code Customer Ordering)
- Pemindaian QR-Code meja pelanggan untuk langsung membuka menu cabang terkait secara instan.
- Keranjang belanja interaktif bertenaga Alpine.js yang responsif dan cepat tanpa *page-reload*.
- Halaman status pelacakan pesanan dinamis bagi pelanggan.

### 3. Pencarian Menu Berbasis AI (AI Image Recognition)
- Layanan pengenalan gambar makanan/minuman bertenaga **FastAPI** (Python).
- Mendukung klasifikasi gambar menggunakan model *pre-trained* **MobileNetV2** (ImageNet).
- Dilengkapi sistem *fallback* cerdas berupa pencocokan kata kunci nama file (*filename keywords*) dan *random fallback*.

### 4. Otomatisasi RPA Event-Driven (Automation Layer)
- *Automation Engine* yang membaca aturan dinamis (`automation_rules`) di database untuk memicu pekerjaan latar belakang.
- Pembuatan file PDF struk pembayaran otomatis saat transaksi dibayar (`GenerateReceiptJob`).
- Pemeriksaan berkala stok bahan kritis secara otomatis (`CheckStockLevelsJob`).
- Sistem proteksi eksekusi ganda menggunakan **Idempotency Key** pada log audit otomatisasi.

### 5. Dasbor Interaktif & Desain Premium
- Visualisasi metrik keuangan dan penjualan interaktif menggunakan library grafik **Apache ECharts**.
- Sparkline mini-charts pada kartu metrik dashboard utama.
- Komponen visual UI/UX pastel yang konsisten dengan efek transisi halus.

### 6. Notifikasi & Log Audit
- Lonceng notifikasi in-app pada navbar kasir bertenaga Alpine.js Polling.
- Pengiriman email paralel melalui driver **SMTP** untuk notifikasi stok kritis dan laporan harian.
- Log aktivitas staf yang lengkap untuk audit trail dengan retensi pembersihan otomatis selama 30 hari.

---

## 📂 Dokumentasi Teknis & Panduan Pengguna

Untuk mempermudah setup dan operasional di komputer lokal, kami telah menyediakan panduan mendalam secara terpisah:

1. ⚙️ **[Panduan Setup Lokal & Konfigurasi (docs/LOCAL_SETUP.md)](docs/LOCAL_SETUP.md)**  
   *Panduan instalasi dari nol, konfigurasi database MySQL, pengaturan SMTP email lokal (Mailpit/Mailtrap), daftar akun default seeder, dan cara menjalankan server lokal.*
   
2. 🤖 **[Dokumentasi AI & Otomatisasi RPA (docs/AI_RPA_GUIDE.md)](docs/AI_RPA_GUIDE.md)**  
   *Penjelasan teknis detail mengenai cara kerja FastAPI, model MobileNetV2, pemicuan event, background queue, dan troubleshooting idempotensi.*
   
3. 📖 **[Panduan Pengguna / User Guide (docs/USER_GUIDE.md)](docs/USER_GUIDE.md)**  
   *Manual singkat operasional sistem bagi Super Admin (Owner), Admin Cabang, Kasir, dan alur pelanggan.*

---

## 🛠️ Stack Teknologi

- **Backend**: Laravel 13 (PHP 8.3+)
- **Frontend**: Blade + Alpine.js, Tailwind CSS v4 (Vite plugin)
- **Database**: MySQL (Pengembangan Lokal), SQLite (Testing)
- **AI Microservice**: FastAPI, Python 3.10+, Uvicorn, MobileNetV2 (PyTorch)
- **Package Tambahan**: Laravel Breeze (Auth), Maatwebsite Excel (Ekspor Laporan)

---

## 📄 Lisensi

KraveScan dikembangkan sebagai proyek proprietary berlisensi tertutup. Seluruh aset grafis, desain, dan kode sumber adalah hak cipta terdaftar.
