# Panduan Deploy Hostinger — BDA Shooting Championship 2026

Website ini dibangun menggunakan **Pure PHP + HTML + Tailwind CSS (CDN) + Alpine.js (CDN)** tanpa ketergantungan framework Node.js atau bundler. Arsitektur ini **100% kompatibel langsung dengan Shared Hosting Hostinger (LiteSpeed / Apache)**.

---

## 📦 File Deployment

File arsip siap upload:
👉 **`bda-shooting-hostinger-v2.zip`**

Arsip ini berisi seluruh file di folder `site/`:
```
public_html/
├── index.php                 (Landing page utama)
├── daftar.php                (Formulir pendaftaran peserta)
├── live-score.php            (Papan Live Score realtime)
├── e-ticket.php              (E-Ticket resmi & QR Code)
├── schema.sql                (Skema database MySQL)
├── .htaccess                 (Security & HTTPS redirect)
├── admin/
│   ├── index.php             (Dashboard admin: verifikasi pendaftaran & kirim WA)
│   └── scores.php            (Kelola skor Presisi 20M & bagan Dueling Plat)
├── api/
│   ├── register.php          (API Pendaftaran)
│   ├── registrations.php     (API List Pendaftar - Admin)
│   ├── status.php            (API Update Verifikasi & Auto No. Peserta)
│   ├── eticket.php           (API E-Ticket Publik)
│   ├── scores-presisi.php    (API CRUD Skor Presisi)
│   ├── scores-dueling.php    (API CRUD Bagan Dueling Plat)
│   └── public-scores.php     (API Skor Publik Realtime)
├── includes/
│   ├── config.php            (Konfigurasi DB, Admin, & Event)
│   ├── db.php                (Koneksi PDO MySQL)
│   ├── header.php            (Navbar, Dark mode, CDN)
│   └── footer.php            (Footer & Scripts)
├── assets/
│   ├── logo-championship.jpeg
│   └── logo-bda.jpeg
└── uploads/
    ├── kta/                  (Folder foto KTA peserta)
    └── bukti/                (Folder foto bukti transfer)
```

---

## 🚀 Langkah Deploy di Hostinger (hPanel)

### Langkah 1: Buat Database MySQL di Hostinger
1. Buka **hPanel Hostinger** → Domain `bda-shooting-championship.sbs`.
2. Klik menu **Databases** → **MySQL Databases**.
3. Buat database baru, misalnya:
   - **Database Name**: `u123456789_bda`
   - **Username**: `u123456789_bda`
   - **Password**: `PasswordKuatAnda2026!`
4. Catat nama database, username, dan password tersebut.

### Langkah 2: Import Tabel Database (schema.sql)
1. Di halaman MySQL Databases, klik tombol **Enter phpMyAdmin** di sebelah database yang baru dibuat.
2. Di phpMyAdmin, klik tab **Import** (di menu atas).
3. Klik **Choose File** → pilih file `schema.sql` (bisa diekstrak dari zip atau disalin isinya).
4. Klik **Go / Kirim**. Ketiga tabel (`registrations`, `scores_presisi`, `dueling_matches`) akan terbentuk otomatis.

### Langkah 3: Upload & Ekstrak File ke File Manager
1. Di hPanel, buka **Files** → **File Manager**.
2. Masuk ke direktori **`public_html`**.
3. (Opsional) Hapus file bawaan Hostinger seperti `default.php` jika ada.
4. Klik tombol **Upload** (ikon panah ke atas) → pilih file **`bda-shooting-hostinger-v2.zip`**.
5. Setelah upload selesai, klik kanan file zip → pilih **Extract**.
6. Pilih tujuan ekstrak ke folder saat ini (`public_html/`).
7. Pastikan file seperti `index.php`, `daftar.php`, dan folder `admin/`, `api/`, `includes/` berada langsung di dalam `public_html/`.

### Langkah 4: Sesuaikan includes/config.php
1. Di File Manager, buka folder `includes/` → klik kanan `config.php` → pilih **Edit**.
2. Ubah konfigurasi database sesuai data di Langkah 1:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'u123456789_bda');      // Ganti nama DB Anda
   define('DB_USER', 'u123456789_bda');      // Ganti username DB Anda
   define('DB_PASS', 'PasswordKuatAnda2026!'); // Ganti password DB Anda
   ```
3. (Opsional) Sesuaikan password admin jika ingin mengubah dari default:
   ```php
   define('ADMIN_USER', 'admin');
   define('ADMIN_PASS', 'bsc2026admin'); // Ganti jika diinginkan
   ```
4. Klik **Save**.

### Langkah 5: Pastikan Izin Folder Upload (Permissions)
1. Di File Manager, klik kanan folder `uploads/` → pilih **Permissions**.
2. Pastikan permission diset ke **755** (Read, Write, Execute untuk Owner).
3. Lakukan hal yang sama untuk subfolder `uploads/kta/` dan `uploads/bukti/`.

---

## 🎯 Panduan Fitur & Penggunaan

### 1. Pendaftaran & E-Ticket
- Formulir pendaftaran dapat diakses di: `https://bda-shooting-championship.sbs/daftar.php`
- Peserta mengisi data lengkap, memilih kategori (Presisi 20M / Dueling Plat / Keduanya), upload KTA dan bukti transfer.
- Setelah submit, peserta menerima ID Registrasi unik (contoh: `BDA-A1B2C3D4`).
- Tautan E-Ticket peserta: `https://bda-shooting-championship.sbs/e-ticket.php?id=BDA-A1B2C3D4`

### 2. Dashboard Admin
- Akses: `https://bda-shooting-championship.sbs/admin/index.php`
- **Password Default**: `bsc2026admin`
- Fitur Dashboard:
  - Cek detail pendaftar, foto KTA, dan bukti transfer.
  - Klik **Verifikasi**: Sistem otomatis membuatkan Nomor Peserta resmi sequential (`BSC-001`, `BSC-002`, dst).
  - Klik **Kirim E-Ticket (WhatsApp)**: Otomatis membuka chat WhatsApp peserta dengan format resmi berisi nomor peserta, tautan unik E-Ticket, dan QR Code.
  - Export data ke CSV.

### 3. Live Score Pertandingan
- Papan Live Score publik: `https://bda-shooting-championship.sbs/live-score.php`
  - Dilengkapi fitur **update otomatis setiap 10 detik** (bisa dipause).
  - **Tab Presisi 20M**: Tabel perankingan otomatis diurutkan berdasarkan **Total Skor Tertinggi**, dan jika ada nilai seri/imbang otomatis diurutkan berdasarkan **Jumlah Tembakan X (Inner-10)** terbanyak.
  - **Tab Dueling Plat**: Bagan turnamen gugur (Penyisihan → Perempat Final → Semifinal → Perebutan Juara 3 → Final) lengkap dengan catatan waktu dan pemenang.
- **Halaman Editor Skor Admin**: `https://bda-shooting-championship.sbs/admin/scores.php`
  - Klik tombol **Sinkron Peserta Verified** untuk langsung memasukkan peserta terverifikasi ke daftar skor presisi.
  - Admin dapat mengetik skor seri 1 - 10 dan jumlah tembakan X secara langsung, lalu klik **Simpan**. Total nilai dan urutan ranking terhitung otomatis!
  - Admin dapat membuat bagan pertandingan dueling plat baru, memasukkan waktu peserta, dan memilih pemenang pertandingan.
