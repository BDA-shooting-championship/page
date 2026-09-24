# Panduan Setup Backend Hostinger (PHP + MySQL)

Folder ini berisi seluruh kode backend REST API untuk sistem pendaftaran dan e-ticket **BDA Shooting Championship 2026**.

---

## 1. Buat Database MySQL di Hostinger (hPanel)

1. Masuk ke **hPanel Hostinger** Anda.
2. Buka menu **Databases** → **MySQL Databases**.
3. Buat database baru:
   - **MySQL Database Name**: contoh `u123456789_bda`
   - **MySQL Username**: contoh `u123456789_bda_user`
   - **Password**: Buat password yang kuat dan catat baik-baik.
4. Klik **Create**.

---

## 2. Import Tabel Database (via phpMyAdmin)

1. Pada daftar database di hPanel, klik tombol **Enter phpMyAdmin** di samping database yang baru dibuat.
2. Klik tab **SQL** di bagian atas phpMyAdmin.
3. Salin dan jalankan (*Execute/Go*) perintah SQL berikut:

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

## 3. Upload File Backend ke Hostinger

1. Di hPanel, buka **File Manager** (atau gunakan FTP/SFTP).
2. Tentukan lokasi folder untuk API:
   - Jika menggunakan subdomain (misal `api-bda.yourdomain.com`): upload semua isi folder `hostinger/` langsung ke `public_html/api-bda/` (atau direktori root subdomain Anda).
   - Jika diletakkan di dalam domain utama: upload folder `hostinger/` dan namakan misalnya `api-bda`.
3. Buka file `config.php` di File Manager Hostinger dan edit:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'nama_database_anda');
   define('DB_USER', 'username_database_anda');
   define('DB_PASS', 'password_database_anda');
   define('BASE_URL', 'https://api-bda.yourdomain.com'); // Ganti dengan URL domain/subdomain Anda
   define('ADMIN_TOKEN', 'bda_secret_token_2026_supersecure'); // Pastikan sama dengan di frontend
   ```
4. Pastikan direktori `uploads/kta/` dan `uploads/bukti/` memiliki hak akses tulis (*write permissions*, chmod `755` atau `775`).

---

## 4. Hubungkan ke Frontend GitHub Pages

Setelah backend di-upload dan memiliki URL aktif (misal `https://api-bda.yourdomain.com`), tambahkan environment variable di repository GitHub Anda:

1. Buka repo GitHub Anda: `https://github.com/BDA-shooting-championship/page/settings/secrets/actions`
2. Tambahkan **Repository Secret** atau **Variable**:
   - `NEXT_PUBLIC_API_URL` = `https://api-bda.yourdomain.com`
   - `NEXT_PUBLIC_ADMIN_TOKEN` = `bda_secret_token_2026_supersecure`
3. Frontend Next.js secara default juga memiliki fallback mode lokal/demo jika backend belum dikonfigurasi, sehingga halaman pendaftaran dan admin tetap dapat diuji.
