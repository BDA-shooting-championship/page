<?php
/**
 * BDA Shooting Championship 2026 — Configuration
 */

// Database
define('DB_HOST', 'localhost');
define('DB_NAME', 'u741010203_bda');
define('DB_USER', 'u741010203_bda');
define('DB_PASS', '@Sigsauer750');

// Admin
define('ADMIN_USER', 'admin');
define('ADMIN_PASS', 'BDA750admin');
define('ADMIN_TOKEN', 'bsc2026-secret-token');

// Site
define('SITE_URL', 'https://bda-shooting-championship.sbs');
define('UPLOAD_DIR', __DIR__ . '/../uploads/');
define('MAX_FILE_SIZE', 10 * 1024 * 1024); // 10MB

// Event
define('EVENT_NAME', 'BDA Shooting Championship 2026');
define('EVENT_DATE', '17-18 Oktober 2026');
define('EVENT_LOCATION', 'Lapangan Tembak Resimen I Pasukan Pelopor, Kedunghalang, Bogor');
define('REGISTRATION_FEE', 200000);

// Bank
define('BANK_NAME', 'BRI');
define('BANK_ACCOUNT', '053801072120508');
define('BANK_HOLDER', 'Ruly Ardana Putra');
