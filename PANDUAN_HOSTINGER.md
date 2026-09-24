# Panduan Deploy ke Hostinger (bda-shooting-championship.sbs)

Paket arsip siap deploy telah berhasil dibuat:
📁 **`bda-shooting-championship-hostinger.zip`** (berada di folder utama proyek ini: `D:\BDA-shooting-championship\bda-shooting-championship-hostinger.zip`).

Paket ini sudah menggabungkan:
- ✅ **Frontend Next.js**: Landing Page, Countdown, Jadwal, Aturan, Formulir Pendaftaran, Dashboard Admin, dan E-Ticket dengan QR Code.
- ✅ **Backend PHP REST API**: `api/register.php`, `api/registrations.php`, `api/status.php`, `api/eticket.php`.
- ✅ **Sistem File Upload**: Folder `uploads/kta/` dan `uploads/bukti/`.
- ✅ **Konfigurasi Web Server**: File `.htaccess` (Clean URLs, keamanan upload, dan proteksi konfigurasi).

---

## Langkah 1: Buat Database MySQL di hPanel Hostinger

1. Buka [hPanel Hostinger](https://hpanel.hostinger.com/).
2. Masuk ke menu **Databases** → **MySQL Databases**.
3. Buat database baru untuk domain `bda-shooting-championship.sbs`:
   - **Database Name**: contoh `u123456789_bda`
   - **Username**: contoh `u123456789_bda_user`
   - **Password**: (catat password ini)
4. Klik **Create**.
5. Klik **Enter phpMyAdmin** di samping database baru tersebut.
6. Klik tab **SQL** di phpMyAdmin, tempel (*paste*) query SQL berikut, lalu klik **Go**:

```sql
CREATE TABLE IF NOT EXISTS `registrations` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `registration_id` VARCHAR(20) NOT NULL UNIQUE,
  `no_peserta` VARCHAR(10) DEFAULT NULL,
  `nama` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) NOT NULL,
  `telepon` VARCHAR(20) NOT NULL,
  `pangkat` VARCHAR(50) NOT NULL,
  `nrp` VARCHAR(30) NOT NULL UNIQUE,
  `satuan` VARCHAR(100) NOT NULL,
  `kategori` VARCHAR(100) NOT NULL,
  `kta_filename` VARCHAR(255) DEFAULT NULL,
  `bukti_filename` VARCHAR(255) DEFAULT NULL,
  `status` ENUM('Pending', 'Verified', 'Rejected') DEFAULT 'Pending',
  `admin_notes` TEXT DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_nrp` (`nrp`),
  INDEX `idx_status` (`status`),
  INDEX `idx_registration_id` (`registration_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

---

## Langkah 2: Upload & Ekstrak File Zip ke Hostinger

1. Di hPanel Hostinger, pilih domain `bda-shooting-championship.sbs`.
2. Buka menu **Files** → **File Manager**.
3. Masuk ke direktori **`public_html`**.
4. Klik tombol **Upload** di pojok kanan atas, lalu pilih file:
   `D:\BDA-shooting-championship\bda-shooting-championship-hostinger.zip`
5. Setelah selesai terunggah, klik kanan file zip tersebut dan pilih **Extract**.
6. Pilih folder tujuan ekstraksi langsung di dalam `public_html`.
7. Pastikan file seperti `index.html`, `config.php`, `.htaccess`, folder `admin/`, `daftar/`, `e-ticket/`, `api/`, dan `uploads/` berada langsung di dalam `public_html`.

---

## Langkah 3: Sesuaikan Kredensial Database di `config.php`

1. Di File Manager Hostinger, klik kanan file **`config.php`** lalu pilih **Edit**.
2. Sesuaikan 3 baris kredensial database sesuai yang Anda buat pada Langkah 1:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'nama_database_anda');
   define('DB_USER', 'username_database_anda');
   define('DB_PASS', 'password_database_anda');
   ```
3. Simpan file (**Save**).
4. Pastikan folder `uploads/kta/` dan `uploads/bukti/` memiliki permission **755** (default sudah benar).

---

## Langkah 4: Uji Coba Website

Buka peramban web dan akses:
- **Landing Page Utama**: `https://bda-shooting-championship.sbs/`
- **Formulir Pendaftaran**: `https://bda-shooting-championship.sbs/daftar/`
- **Dashboard Admin**: `https://bda-shooting-championship.sbs/admin/` (Password default: `bda2026admin`)
- **E-Ticket Publik**: `https://bda-shooting-championship.sbs/e-ticket/?id=BDA-XXXXXXXX`
