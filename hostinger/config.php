<?php
/**
 * Konfigurasi Database & Backend Hostinger
 * Silakan sesuaikan kredensial di bawah ini dengan database MySQL Anda di Hostinger
 */

define('DB_HOST', 'localhost');
define('DB_NAME', 'u123456789_bda');      // Ganti dengan nama database Anda
define('DB_USER', 'u123456789_bda_user'); // Ganti dengan username database Anda
define('DB_PASS', 'PasswordDatabaseAnda123'); // Ganti dengan password database Anda

// Token rahasia admin untuk akses API daftar peserta & verifikasi
define('ADMIN_TOKEN', 'bda_secret_token_2026_supersecure');

// Path penyimpanan file upload
define('UPLOAD_DIR', __DIR__ . '/uploads/');

// Base URL folder API di server Hostinger (tanpa trailing slash)
// Contoh: https://api.namadomainanda.com/bda atau https://namadomainanda.com/api-bda
define('BASE_URL', 'https://api-bda.yourdomain.com');

// Batasan ukuran & jenis file
define('MAX_FILE_SIZE', 2 * 1024 * 1024); // 2 MB
define('ALLOWED_TYPES', ['image/jpeg', 'image/png', 'image/webp']);

// Domain frontend GitHub Pages yang diizinkan untuk CORS
define('FRONTEND_URL', 'https://bda-shooting-championship.github.io');
