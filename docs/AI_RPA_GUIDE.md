# Panduan Modul AI & Otomasi RPA

Dokumen ini menjelaskan arsitektur, cara kerja, instalasi, dan pemecahan masalah (troubleshooting) untuk modul **Kecerdasan Buatan (AI)** dan **Otomasi Proses Robotik (RPA)** yang terintegrasi di KraveScan.

---

## 1. Layanan Pengenalan Gambar AI (FastAPI Microservice)

Modul AI KraveScan digunakan untuk membantu pelanggan mengenali item menu secara otomatis dari gambar/foto yang diunggah melalui perangkat mereka.

### A. Arsitektur & Logika Deteksi
Layanan AI ini dibangun menggunakan Python dengan framework **FastAPI**. Mekanisme klasifikasinya memiliki tiga tingkatan fallback (kemunduran aman):
1. **Pencocokan Kata Kunci Nama File (Filename Keyword)**:
   Jika nama file gambar yang diunggah mengandung kata kunci menu (misalnya `americano_togo.jpg` atau `nasi_goreng.png`), sistem akan langsung mencocokkannya ke menu tersebut dengan tingkat keyakinan (confidence) 95%. Sangat berguna untuk pengujian cepat.
2. **Model MobileNetV2 (Real AI Inference)**:
   Jika pustaka `torch` dan `torchvision` terinstal, sistem akan memuat model pre-trained **MobileNetV2** (ImageNet). Gambar akan diproses dan 5 prediksi teratas akan dipetakan ke menu KraveScan terdekat (misal: kelas ImageNet `cappuccino` dipetakan ke `Caffe Latte`).
3. **Random Fallback**:
   Jika pustaka ML tidak terinstal atau tidak ada kecocokan klasifikasi, sistem akan mengembalikan satu item menu acak secara dinamis dari pool menu agar fungsionalitas aplikasi web tetap berjalan lancar.

### B. Cara Setup & Menjalankan AI Service di Lokal

1. Buka terminal baru dan masuk ke folder `ai_service`:
   ```bash
   cd ai_service
   ```
2. *(Disarankan)* Buat dan aktifkan Python Virtual Environment:
   ```bash
   # Windows
   python -m venv venv
   venv\Scripts\activate

   # macOS/Linux
   python3 -m venv venv
   source venv/bin/activate
   ```
3. Pasang dependensi yang diperlukan:
   ```bash
   pip install -r requirements.txt
   ```
   *Catatan: Jika Anda ingin menggunakan klasifikasi gambar MobileNetV2 yang sebenarnya, hapus tanda komentar `#` pada `torch` dan `torchvision` di file `requirements.txt` sebelum menjalankan perintah di atas (diperlukan koneksi internet stabil karena ukuran unduhannya cukup besar).*
4. **Jalankan Layanan**:
   Karena server Laravel lokal standar menggunakan port `8000`, Anda harus menjalankan layanan FastAPI ini pada port berbeda (misalnya **5000**):
   ```bash
   uvicorn main:app --host 127.0.0.1 --port 5000 --reload
   ```
5. **Konfigurasi Laravel**:
   Buka file `.env` proyek Laravel Anda, lalu tambahkan baris berikut agar Laravel tahu lokasi layanan AI:
   ```env
   AI_SERVICE_URL=http://127.0.0.1:5000
   ```

---

## 2. Otomatisasi RPA (Automation Engine)

KraveScan dilengkapi dengan *Event-driven Automation Engine* untuk mensimulasikan proses robotik yang biasanya dilakukan secara manual oleh staf.

### A. Alur Kerja Event-Driven
```
[Transaksi Dibayar] -> Trigger Event 'OrderPaid'
                           │
                           ▼
             [Listener: TriggerOrderAutomations]
                           │
                           ▼
             [App\Services\AutomationEngine]
        (Membaca database tabel 'automation_rules')
                           │
        ┌──────────────────┴──────────────────┐
        ▼                                     ▼
[GenerateReceiptJob]                [CheckStockLevelsJob]
(Membuat file PDF struk)          (Memeriksa stok rendah)
```

1. **Aturan Otomatisasi (Automation Rules)**:
   Aturan disimpan di tabel `automation_rules` dan dapat diaktifkan/dinonaktifkan melalui menu **Aturan Otomasi** di dashboard Super Admin / Admin Cabang.
2. **Pekerjaan Latar Belakang (Jobs)**:
   - `App\Jobs\GenerateReceiptJob`: Menghasilkan file struk pembayaran digital dan menyimpannya di folder disk lokal `storage/app/public/branches/{branch_id}/receipts/`.
   - `App\Jobs\CheckStockLevelsJob`: Menganalisis sisa stok bahan pasca-pembelian. Jika sisa stok berada di bawah ambang batas (threshold), sistem otomatis mengirimkan `LowStockNotification` ke Admin/Kasir.

### B. Proteksi Eksekusi Duplikat (Idempotency Key)
Untuk mencegah kesalahan sistem seperti pencetakan struk ganda atau spam notifikasi akibat kegagalan koneksi atau klik berulang:
- Setiap pemicuan otomatisasi akan menghasilkan `idempotency_key` unik (berupa kombinasi `rule_id` dan `order_id`).
- Kunci ini dicatat pada tabel `automation_logs` saat pekerjaan pertama kali dimasukkan ke antrian.
- Jika ada upaya memicu aturan yang sama pada pesanan yang sama, `AutomationEngine` akan mendeteksi log yang sudah ada dan langsung mengabaikan pemicuan berikutnya.

### C. Pemecahan Masalah (Troubleshooting) Otomasi

Jika tugas otomasi tidak berjalan atau notifikasi tidak muncul:
1. Pastikan **Queue Worker** Laravel Anda berjalan dengan baik. Jika Anda menggunakan `composer dev`, queue worker sudah otomatis berjalan. Namun, Anda juga dapat menjalankannya secara manual di terminal terpisah:
   ```bash
   php artisan queue:listen --tries=1
   ```
2. Periksa status kegagalan antrian pekerjaan di database pada tabel `failed_jobs`.
3. Periksa tabel `automation_logs` untuk memastikan apakah aturan sudah pernah dieksekusi sebelumnya (karena proteksi idempotensi menghalangi eksekusi ulang).
