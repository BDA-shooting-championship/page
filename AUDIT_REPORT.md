# 🔍 LAPORAN AUDIT MENYELURUH — BDA SHOOTING CHAMPIONSHIP 2026

**Tanggal Audit**: 26 September 2026  
**Target**: Website `bda-shooting-championship.sbs` — Seluruh halaman & backend  
**Metode**: 6 auditor paralel (Security, Frontend UI/UX, PHP Quality, Performance, Functional Logic, Simulasi Lesan)  
**Total Temuan**: **68 temuan** — 11 CRITICAL · 18 HIGH · 23 MEDIUM · 16 LOW

---

## DAFTAR ISI

1. [Executive Summary](#1-executive-summary)
2. [Distribusi Temuan](#2-distribusi-temuan)
3. [Top 10 Temuan Paling Kritis](#3-top-10-temuan-paling-kritis)
4. [Audit Security](#4-audit-security-15-temuan)
5. [Audit Frontend UI/UX & Aksesibilitas](#5-audit-frontend-uiux--aksesibilitas-18-temuan)
6. [Audit PHP Code Quality & Arsitektur](#6-audit-php-code-quality--arsitektur-14-temuan)
7. [Audit Performance & Optimasi Aset](#7-audit-performance--optimasi-aset-10-temuan)
8. [Audit Functional Logic & Business Rules](#8-audit-functional-logic--business-rules-12-temuan)
9. [Audit Simulasi Lesan (Target Shooting Simulator)](#9-audit-simulasi-lesan-6-temuan)
10. [Rencana Remediasi 4 Fase](#10-rencana-remediasi-4-fase)
11. [Lampiran: Temuan Positif](#11-lampiran-temuan-positif)

---

## 1. Executive Summary

Website BDA Shooting Championship 2026 adalah platform manajemen turnamen menembak yang dibangun dengan **PHP prosedural**, **Alpine.js**, dan **Tailwind CSS**. Platform ini mengelola pendaftaran peserta, verifikasi KTA & bukti transfer, E-ticket dengan QR Code, scoring presisi pistol 20M (ring 1–10 + Inner X), dan bagan eliminasi dueling plat 15M.

Meskipun secara fungsional kaya dan memiliki UI yang menarik secara visual, audit menyeluruh menemukan kerentanan keamanan kritis, bottleneck performa berat, bug logika bisnis yang berdampak pada keabsahan skor turnamen, dan pelanggaran aksesibilitas web.

### Temuan Paling Kritikal:

1. **Hardcoded master token** (`bsc2026-secret-token`) yang terekspos di client-side JavaScript memungkinkan bypass autentikasi total.
2. **Remote Code Execution (RCE)** dimungkinkan melalui upload file tanpa validasi ekstensi.
3. **6 perintah `ALTER TABLE`** dijalankan pada setiap HTTP request, menyebabkan risiko database deadlock saat traffic live scoring tinggi.
4. **Formula scoring Inner X salah** — dihitung 0.1 poin alih-alih 10 poin, berpotensi mengubah pemenang hadiah.
5. **Data PII anggota POLRI** (nama, NRP, pangkat, no HP, foto KTA) dapat di-harvest melalui endpoint publik tanpa autentikasi.
6. **Landing page berukuran 4.8–8.5 MB** karena gambar 2000×2000px untuk elemen 36px dan Tailwind Play CDN di production.

---

## 2. Distribusi Temuan

| Aspek Audit | CRITICAL | HIGH | MEDIUM | LOW | Total |
|:---|:---:|:---:|:---:|:---:|:---:|
| 🔒 Security | 2 | 6 | 3 | 4 | **15** |
| 🎨 Frontend UI/UX | 5 | 5 | 4 | 4 | **18** |
| 🏗️ PHP Code Quality | 2 | 4 | 4 | 4 | **14** |
| ⚡ Performance | 3 | 4 | 2 | 1 | **10** |
| 🔧 Functional Logic | 2 | 4 | 3 | 3 | **12** |
| 🎯 Simulasi Lesan | 2 | 1 | 1 | 2 | **6** |
| **TOTAL** | **11** | **18** | **23** | **16** | **68** |

---

## 3. Top 10 Temuan Paling Kritis

### #1 — Hardcoded Master Token & Auth Bypass Universal

- **Severity**: 🔴 CRITICAL
- **File**: `site/includes/config.php:15`, `site/includes/db.php:39-50`, `site/admin/index.php:2585`
- **Deskripsi**: Token statis `bsc2026-secret-token` di-hardcode dalam `config.php` dan di-inject langsung ke client-side JavaScript melalui `<?= json_encode(ADMIN_TOKEN) ?>`. Passing `?token=bsc2026-secret-token` atau header `X-Admin-Token` pada API endpoint manapun memberikan akses admin penuh tanpa session validation.
- **Kode Bermasalah**:
  ```php
  // config.php:15
  define('ADMIN_TOKEN', 'bsc2026-secret-token');

  // admin/index.php:2585 — injected ke client JS
  adminToken: <?= json_encode(ADMIN_TOKEN) ?>,
  ```
- **Dampak**: Siapapun yang meng-inspect DevTools browser mendapat token dan bisa: menghapus seluruh peserta, mengubah skor turnamen, membuat akun superadmin, atau mengekstrak data PII anggota Polri.
- **Rekomendasi**:
  1. Hapus `ADMIN_TOKEN` dari `config.php` sepenuhnya.
  2. Gunakan PHP session (`$_SESSION['admin_user']`) untuk semua autentikasi API.
  3. Jika token stateless diperlukan, generate token kriptografis per-user (`bin2hex(random_bytes(32))`) yang disimpan hashed di database dengan masa berlaku.
  4. Hapus seluruh variabel `ADMIN_TOKEN` dari client-side scripts.

---

### #2 — Arbitrary File Upload & Potensi Remote Code Execution (RCE)

- **Severity**: 🔴 CRITICAL
- **File**: `site/api/register.php:74-128`, `site/.htaccess:18-21`
- **Deskripsi**: Upload file (`foto_kta` dan `bukti_transfer`) divalidasi hanya menggunakan `mime_content_type()` (magic bytes). Ekstensi file diambil langsung dari nama file yang dikirim user tanpa validasi terhadap allowlist. `.htaccess` hanya memblokir lowercase `.php`.
- **Kode Bermasalah**:
  ```php
  // register.php:114-116 — ekstensi dari user tanpa validasi
  $ktaExt = pathinfo($_FILES['foto_kta']['name'], PATHINFO_EXTENSION);
  $ktaFilename = $registrationId . '_kta_' . time() . '.' . strtolower($ktaExt);
  move_uploaded_file($_FILES['foto_kta']['tmp_name'], $ktaDir . '/' . $ktaFilename);
  ```
- **Vektor Serangan**: Attacker membuat polyglot file (header JPEG valid + PHP payload) bernama `exploit.phtml`. `mime_content_type()` membaca magic bytes sebagai `image/jpeg` (lolos validasi), tapi Apache mengeksekusi `.phtml` sebagai PHP.
- **Dampak**: Remote Code Execution — attacker dapat menjalankan perintah OS pada server hosting.
- **Rekomendasi**:
  1. **Validasi ekstensi file** terhadap allowlist strict:
     ```php
     $allowedExt = ['jpg', 'jpeg', 'png', 'webp'];
     $ext = strtolower(pathinfo($_FILES['foto_kta']['name'], PATHINFO_EXTENSION));
     if (!in_array($ext, $allowedExt, true)) {
         jsonResponse(['success' => false, 'message' => 'Ekstensi file tidak diizinkan'], 400);
     }
     ```
  2. **Re-encode gambar** dengan GD library untuk strip embedded payloads:
     ```php
     $img = @imagecreatefromstring(file_get_contents($_FILES['foto_kta']['tmp_name']));
     if (!$img) jsonResponse(['success' => false, 'message' => 'File bukan gambar valid'], 400);
     imagejpeg($img, $targetPath, 85);
     imagedestroy($img);
     ```
  3. **Buat `site/uploads/.htaccess`**:
     ```apache
     <FilesMatch "(?i)\.(php|phtml|phar|php[0-9]|inc|cgi|pl|py|sh)$">
         Require all denied
     </FilesMatch>
     Options -ExecCGI -Indexes
     RemoveHandler .php .phtml .phar
     ```

---

### #3 — Runtime ALTER TABLE pada Setiap HTTP Request

- **Severity**: 🔴 CRITICAL
- **File**: `site/api/scores-dueling.php:51-62`, `site/api/scores-presisi.php:18-20, 83-86`
- **Deskripsi**: 6 perintah `ALTER TABLE` DDL dieksekusi pada **setiap POST dan GET request** ke API scoring:
- **Kode Bermasalah**:
  ```php
  // scores-dueling.php:51-62 — SETIAP REQUEST
  try { $db->exec("ALTER TABLE dueling_matches MODIFY COLUMN winner_id VARCHAR(100) NULL"); } catch (Exception $e) {}
  try { $db->exec("ALTER TABLE dueling_matches ADD COLUMN IF NOT EXISTS category VARCHAR(50) DEFAULT 'umum'"); } catch (Exception $e) {}
  try {
      $db->exec("ALTER TABLE dueling_matches ADD COLUMN IF NOT EXISTS next_match_id INT NULL");
      $db->exec("ALTER TABLE dueling_matches ADD COLUMN IF NOT EXISTS next_slot TINYINT NULL");
      $db->exec("ALTER TABLE dueling_matches ADD COLUMN IF NOT EXISTS loser_next_match_id INT NULL");
      $db->exec("ALTER TABLE dueling_matches ADD COLUMN IF NOT EXISTS loser_next_slot TINYINT NULL");
  } catch (Exception $e) {}

  // scores-presisi.php:18-20 — SETIAP GET DAN POST
  $db->exec("ALTER TABLE scores_presisi MODIFY COLUMN nilai DECIMAL(6,1) NOT NULL DEFAULT 0.0");
  ```
- **Dampak**: MySQL `ALTER TABLE` mengakuisisi Metadata Lock (MDL) eksklusif. Pada traffic tinggi (300+ penonton live score polling setiap 10 detik), DDL queries menimbulkan thread contention, blocking, dan risiko `Lock wait timeout exceeded (error 1205)`. Database bisa crash saat pertandingan berlangsung.
- **Rekomendasi**:
  1. **Hapus seluruh `ALTER TABLE`** dari `scores-dueling.php` dan `scores-presisi.php` secara langsung.
  2. **Update `schema.sql`** agar mencerminkan schema final (termasuk `nilai DECIMAL(6,1)`, kolom `category`, `next_match_id`, dll.).
  3. Alterasi schema hanya boleh dilakukan melalui migration script satu kali, bukan pada runtime HTTP.

---

### #4 — Landing Page 4.8–8.5 MB (Extreme Page Weight)

- **Severity**: 🔴 CRITICAL
- **File**: `site/includes/header.php:18`, `site/assets/logo-bda.png`, `site/assets/logo-championship.png`
- **Deskripsi**:
  | Masalah | Detail | Ukuran Terbuang |
  |:---|:---|:---|
  | Tailwind Play CDN | `cdn.tailwindcss.com` ~3.8 MB JIT compiler di production | ~3.8 MB |
  | Logo BDA | 2000×2000px PNG ditampilkan pada 36×36px | 2,398 KB (99.5% waste) |
  | Logo Championship | 2000×2000px PNG ditampilkan pada 36–384px | 1,730 KB (97.8% waste) |
  | Favicon | 512×512px 240 KB (ada 32×32px 2.88 KB yang tidak dipakai) | 237 KB waste |
  | Simulator embed | 81 KB simulasi 1,775 baris di-embed di homepage | Unnecessary weight |
- **Dampak**: First Contentful Paint >3 detik pada 4G. Pengalaman sangat buruk bagi 300+ spectator saat event.
- **Rekomendasi**:
  1. **Generate WebP thumbnails**: `logo-bda-80.webp` (80×80px ~4 KB), `logo-championship-80.webp` (80×80px ~4 KB), `logo-championship-600.webp` (hero, ~28 KB).
  2. **Ganti favicon** dari `favicon-bsc.png` (240 KB) ke `favicon-32x32.png` (2.88 KB) yang sudah tersedia.
  3. **Replace Tailwind Play CDN** dengan precompiled CSS:
     ```bash
     npx tailwindcss -i ./src/input.css -o ./assets/css/app.min.css --minify
     ```
     Ganti `<script src="https://cdn.tailwindcss.com">` dengan `<link rel="stylesheet" href="/assets/css/app.min.css">`.
  4. Tambah `loading="lazy"`, `decoding="async"`, dan explicit `width`/`height` pada semua `<img>`.
  5. **Estimasi hasil**: Page weight turun dari ~4.8 MB ke ~350–550 KB (**>90% reduksi**).

---

### #5 — Bracket Generator Crash pada N<4 Peserta

- **Severity**: 🔴 CRITICAL (BUG)
- **File**: `site/api/scores-dueling.php:168-245`
- **Deskripsi**: Saat admin membuat bracket dengan 2 atau 3 peserta, `while ($curM >= 2)` tidak pernah dieksekusi. Array `$rounds` kosong. Akses `$rounds[0]['matches']` atau `$rounds[1]` menyebabkan `Undefined array key` PHP Fatal Error.
- **Reproduksi**:
  1. Buka admin panel → Dueling Plat → Set peserta = 2 atau 3.
  2. Klik "Buat Bagan".
  3. Server mengembalikan HTTP 500 Internal Server Error.
- **Rekomendasi**: Tambahkan special-case handling:
  ```php
  if ($count == 2) {
      // Hanya 1 match (Final). Langsung assign Peserta 1 vs Peserta 2.
      $rounds[] = ['name' => 'Final', 'order' => 1, 'matches' => [
          ['match_number' => 1, 'p1' => 'TBD', 'p2' => 'TBD', 'status' => 'upcoming']
      ]];
  } elseif ($count == 3) {
      // 1 Pra-Eliminasi (Match #1) + 1 Final (Match #2).
      $rounds[] = ['name' => 'Pra-Eliminasi', 'order' => 0, 'matches' => [
          ['match_number' => 1, 'p1' => 'TBD', 'p2' => 'TBD', 'status' => 'upcoming']
      ]];
      $rounds[] = ['name' => 'Final', 'order' => 1, 'matches' => [
          ['match_number' => 2, 'p1' => 'TBD (Direct Seed)', 'p2' => 'Winner M#1', 'status' => 'upcoming']
      ]];
  }
  ```

---

### #6 — Scoring Presisi: Inner X Dihitung 0.1 Poin (Seharusnya 10)

- **Severity**: 🔴 CRITICAL (LOGIC_ERROR)
- **File**: `site/api/scores-presisi.php:127`, `site/admin/index.php:3044-3056`
- **Deskripsi**: Dalam aturan menembak presisi, Inner X adalah tembakan yang mengenai pusat ring 10 — bernilai 10 poin dengan tambahan tiebreaker X. Namun kode saat ini:
  ```php
  // scores-presisi.php:127
  $nilai = round($nilaiRing + ($ringX * 0.1), 1);
  ```
  Dan di admin/index.php:
  ```javascript
  // admin/index.php:3053
  totalNilai += (ringX * 0.1);
  ```
  Inner X dihitung **hanya 0.1 poin**, bukan 10 poin.
- **Contoh Dampak**:
  - Penembak mengenai 3 Inner X, 5 Ring 10, 2 Ring 9.
  - **Skor seharusnya**: (3×10) + (5×10) + (2×9) = **98 poin** (dengan 3X tiebreaker).
  - **Skor yang terhitung**: (5×10) + (2×9) + (3×0.1) = **68.3 poin** — kehilangan ~30 poin!
- **Dampak**: Peringkat dan pemenang hadiah uang tunai (Juara 1: 3 juta, Juara 2: 2 juta, Juara 3: 1 juta) bisa keliru. Penembak terbaik justru bisa dikalahkan karena inner X-nya merugikan.
- **Rekomendasi**: Perlakukan X sebagai ring 10 dengan tiebreaker terpisah:
  ```php
  // Backend (scores-presisi.php)
  $jumlahMasuk = $ringX; // X masuk hitungan peluru
  $nilaiRing = $ringX * 10; // X bernilai 10 poin
  for ($r = 10; $r >= 1; $r--) {
      $val = max(0, (int)($input['ring_' . $r] ?? 0));
      $jumlahMasuk += $val;
      $nilaiRing += ($r * $val);
  }
  $nilai = $nilaiRing; // Integer, maks 100
  // ring_x tetap disimpan terpisah sebagai tiebreaker
  // ORDER BY nilai DESC, ring_x DESC
  ```
  ```javascript
  // Frontend (admin/index.php calculateNilai)
  let jmlMasuk = parseInt(item.ring_x) || 0;
  let totalNilai = jmlMasuk * 10; // X = 10 poin
  for (let r = 10; r >= 1; r--) {
      const count = parseInt(item['ring_' + r]) || 0;
      jmlMasuk += count;
      totalNilai += (r * count);
  }
  item.jumlah_masuk = jmlMasuk;
  item.nilai = totalNilai;
  ```

---

### #7 — Backup Files dengan Plaintext Credentials Terekspos

- **Severity**: 🟠 HIGH
- **File**: `site/includes/config.php.bak_20260926_171912` dan 11+ file `.bak` lainnya
- **Deskripsi**: File `.bak` berisi plaintext production credentials:
  ```
  DB_PASS = '@Sigsauer750'
  ADMIN_PASS = 'BDA750admin'
  ADMIN_TOKEN = 'bsc2026-secret-token'
  ```
  Apache/LiteSpeed menyajikan file `.bak` sebagai plaintext (`text/plain`). Siapapun yang mengakses URL `https://bda-shooting-championship.sbs/includes/config.php.bak_20260926_171912` akan mendapatkan seluruh credentials database.
- **Rekomendasi**:
  1. **Segera hapus** semua file `.bak` dari production server.
  2. Update `.htaccess` untuk blokir akses ke seluruh file sensitif:
     ```apache
     <FilesMatch "(?i)\.(bak.*|sql.*|env.*|git.*|old|orig|swp)$">
         Require all denied
     </FilesMatch>
     ```
  3. Pindahkan credentials ke file `.env` di **luar** web root dan tambahkan ke `.gitignore`.

---

### #8 — PII Harvesting Data Anggota POLRI

- **Severity**: 🟠 HIGH
- **File**: `site/api/public-scores.php:18-26`, `site/api/eticket.php:18-55`
- **Deskripsi**:
  1. `public-scores.php` (endpoint publik tanpa auth) mengembalikan `registration_id` untuk setiap peserta.
  2. `eticket.php` (juga publik tanpa auth) menerima `?id=<registration_id>` dan mengembalikan: nama lengkap, NRP, pangkat, nomor HP/WhatsApp, email, satuan, dan URL langsung ke foto KTA POLRI.
  3. Scraper otomatis bisa mengumpulkan seluruh `registration_id` dari leaderboard, lalu query e-ticket satu per satu.
- **Dampak**: Pelanggaran privasi serius terhadap data identitas anggota Polri RI (doxxing, spear-phishing, social engineering, impersonasi).
- **Rekomendasi**:
  1. **Hapus `registration_id`** dari output `public-scores.php`. Hanya tampilkan `no_peserta`, `nama`, `satuan`, dan skor.
  2. **Batasi akses `eticket.php`**: Tambahkan verifikasi (misalnya last 4 digit NRP atau nomor HP) sebelum menampilkan data PII.
  3. **Jangan tampilkan foto KTA** di halaman e-ticket publik — batasi hanya untuk admin dashboard.

---

### #9 — Race Condition pada Nomor Peserta BSC-26xxx

- **Severity**: 🟠 HIGH (CONCURRENCY BUG)
- **File**: `site/api/participants.php:89-101`, `site/api/status.php:53-67`, `site/schema.sql:34,41`
- **Deskripsi**: Generasi nomor peserta menggunakan pola non-atomic read-then-write:
  ```php
  // Fetch ALL nomor ke PHP memory, regex satu per satu
  $stmt = $db->query("SELECT no_peserta FROM registrations WHERE no_peserta LIKE 'BSC-26%'");
  $allPeserta = $stmt->fetchAll(PDO::FETCH_COLUMN);
  $maxNumber = 0;
  foreach ($allPeserta as $p) {
      if (preg_match('/BSC-26(\d+)/i', $p, $matches)) {
          $num = (int)$matches[1];
          if ($num > $maxNumber) $maxNumber = $num;
      }
  }
  $noPeserta = 'BSC-26' . str_pad($maxNumber + 1, 3, '0', STR_PAD_LEFT);
  ```
  Skenario race: Admin A dan Admin B verifikasi 2 peserta bersamaan → keduanya baca `$maxNumber = 25` → keduanya assign `BSC-26026` → duplikasi. Index di schema hanya `INDEX` bukan `UNIQUE`.
- **Rekomendasi**:
  1. Tambah `UNIQUE INDEX` pada `no_peserta`:
     ```sql
     ALTER TABLE registrations ADD UNIQUE INDEX uq_no_peserta (no_peserta);
     ```
  2. Gunakan atomic SQL dalam transaksi:
     ```php
     $db->beginTransaction();
     $stmt = $db->query("SELECT COALESCE(MAX(CAST(SUBSTRING(no_peserta, 7) AS UNSIGNED)), 0) + 1 
                          FROM registrations WHERE no_peserta LIKE 'BSC-26%' FOR UPDATE");
     $nextNum = (int)$stmt->fetchColumn();
     $noPeserta = 'BSC-26' . str_pad($nextNum, 3, '0', STR_PAD_LEFT);
     // UPDATE participant...
     $db->commit();
     ```

---

### #10 — Form Pendaftaran Tidak Accessible via Keyboard

- **Severity**: 🔴 CRITICAL (WCAG)
- **File**: `site/daftar.php:136-248`
- **Deskripsi**:
  1. **6 input form** tanpa `id` dan tanpa `<label for="...">` binding — screen reader tidak bisa mengasosiasikan label dengan input.
  2. **3 category checkbox** diimplementasikan sebagai `<div @click="toggleKategori(...)">` tanpa `tabindex="0"`, `role="checkbox"`, `aria-checked`, atau keyboard handler. Pengguna keyboard tidak bisa memilih kategori lomba.
  3. **File upload input** di-set `class="hidden"` (display:none) tanpa label wrapper yang focusable — keyboard user melewati file picker sepenuhnya.
- **Dampak**: Pengguna yang bergantung pada keyboard atau assistive technology tidak bisa menyelesaikan proses pendaftaran.
- **Rekomendasi**:
  1. Tambahkan `id` pada setiap input dan `for` pada setiap label.
  2. Konversi div kategori menjadi semantic checkbox:
     ```html
     <div role="checkbox" tabindex="0" 
          :aria-checked="formData.kategori.includes('Pistol Presisi 20M')"
          @click="toggleKategori('Pistol Presisi 20M')"
          @keydown.space.prevent="toggleKategori('Pistol Presisi 20M')"
          @keydown.enter.prevent="toggleKategori('Pistol Presisi 20M')">
     ```
  3. Gunakan Tailwind `sr-only` alih-alih `hidden` pada file input agar tetap dalam accessibility tree.

---

## 4. Audit Security (15 Temuan)

### 4.1 CRITICAL

| ID | Temuan | File & Baris | Rekomendasi |
|:---|:---|:---|:---|
| SEC-01 | **Arbitrary File Upload tanpa validasi ekstensi** — MIME-only check memungkinkan polyglot `.phtml`/`.phar` | `register.php:74-128`, `.htaccess:18-21` | Allowlist ekstensi strict (`jpg,jpeg,png,webp`). Re-encode via GD. Buat `uploads/.htaccess` yang disable script handlers. |
| SEC-02 | **Hardcoded master token & auth bypass** — Token statis di config.php di-inject ke client JS | `config.php:15`, `db.php:39-50`, `admin/index.php:2585` | Hapus `ADMIN_TOKEN`. Gunakan session-based auth. Generate per-user cryptographic tokens. |

### 4.2 HIGH

| ID | Temuan | File & Baris | Rekomendasi |
|:---|:---|:---|:---|
| SEC-03 | **Broken function-level authorization** — API endpoint tanpa `requirePermission()` | `participants.php:10-17`, `scores-*.php`, `admin-users.php:13-19` | Enforce `requirePermission('peserta')`, `requirePermission('scores')` di awal setiap API. |
| SEC-04 | **Exposed backup files** — 11+ file `.bak` berisi plaintext DB credentials di web root | `config.php.bak_*`, multiple | Hapus semua `.bak`. Blokir via `.htaccess`. Pindahkan secrets ke `.env` di luar web root. |
| SEC-05 | **PII enumeration** — `registration_id` di public scores → harvest data POLRI via eticket | `public-scores.php:18-26`, `eticket.php:18-55` | Hapus `registration_id` dari output publik. Batasi akses eticket dengan verifikasi tambahan. |
| SEC-06 | **CSV Formula Injection (CWE-1236)** — Data peserta langsung masuk `fputcsv()` tanpa sanitasi | `export-excel.php:88-112` | Prefix `'` pada string yang dimulai dengan `=`, `+`, `-`, `@`. |
| SEC-07 | **Zero CSRF protection** — Seluruh POST endpoint tanpa anti-CSRF token | Seluruh API `POST` | Generate `$_SESSION['csrf_token'] = bin2hex(random_bytes(32))`. Verifikasi `X-CSRF-Token` header. |
| SEC-08 | **Session fixation** — Tidak ada `session_regenerate_id()` saat login | `auth.php:78-85`, `admin/index.php:55-64` | Panggil `session_regenerate_id(true)` setelah login berhasil. Set cookie flags: `HttpOnly`, `Secure`, `SameSite=Strict`. |

### 4.3 MEDIUM

| ID | Temuan | File & Baris | Rekomendasi |
|:---|:---|:---|:---|
| SEC-09 | **Info disclosure via `$e->getMessage()`** — Error PDO bocor ke client di 35+ lokasi | Seluruh API endpoints | `error_log($e->getMessage())` + return pesan generik ke client. |
| SEC-10 | **Zero rate limiting** — Login & registrasi tanpa proteksi brute-force | `auth.php:24`, `register.php:5` | Implement failed login counter + lockout 15 min setelah 5 gagal. Integrate CAPTCHA. |
| SEC-11 | **HTTP header injection** — `$scope` tidak divalidasi di Content-Disposition export | `export-excel.php:28,72,78` | Whitelist `$scope`: `in_array($scope, ['all','antrean','peserta','scores'], true)`. |

### 4.4 LOW

| ID | Temuan | File & Baris | Rekomendasi |
|:---|:---|:---|:---|
| SEC-12 | **CORS wildcard reflection** — Echo arbitrary `$_SERVER['HTTP_ORIGIN']` | `db.php:54-67` | Whitelist allowed origins dari config. |
| SEC-13 | **Third-party PII transmission** — Registration URL dikirim ke `api.qrserver.com` | `e-ticket.php:153-155` | Generate QR lokal via PHP library (`chillerlan/php-qrcode`) atau client-side JS. |
| SEC-14 | **Test/migration scripts exposed** — Debug scripts di public web root | `test_bracket_v2.php`, `migrate.php` | Hapus `test_bracket_v2.php`. Disable `migrate.php` setelah deploy. |
| SEC-15 | **Missing security headers** — Tidak ada HSTS, CSP, Referrer-Policy, Permissions-Policy | `.htaccess:33-38` | Tambahkan `Strict-Transport-Security`, `Referrer-Policy: strict-origin-when-cross-origin`, `Permissions-Policy`. |

### 4.5 Catatan Positif Security

> **SQL Injection**: Seluruh query database menggunakan PDO prepared statements dengan benar. Tidak ditemukan kerentanan SQL Injection di seluruh codebase. `PDO::ATTR_EMULATE_PREPARES = false` dan `PDO::ERRMODE_EXCEPTION` sudah dikonfigurasi.

---

## 5. Audit Frontend UI/UX & Aksesibilitas (18 Temuan)

### 5.1 CRITICAL

| ID | Temuan | Halaman | Rekomendasi |
|:---|:---|:---|:---|
| UI-01 | **60fps RAF loop berjalan terus di homepage** — canvas + gyro listener aktif meskipun user di hero section | `index.php` via `simulasi-section.php:1754-1768` | Gunakan `IntersectionObserver` untuk pause animation saat canvas di luar viewport. |
| UI-02 | **Form pendaftaran tidak accessible** — 6 input tanpa label, custom checkbox tanpa keyboard, file upload hidden | `daftar.php:136-248` | Tambah `id`/`for` binding, `role="checkbox"`, `tabindex="0"`, keyboard handlers. |
| UI-03 | **5 admin modal tanpa dialog pattern** — Missing `role="dialog"`, `aria-modal`, focus trap, Escape handler | `admin/index.php` (5 modals) | Implement WAI-ARIA dialog pattern: `role="dialog"`, `aria-modal="true"`, focus trap, `@keydown.escape.window`. |
| UI-04 | **11 score inputs per baris tanpa label** — Screen reader mengumumkan "edit text 0" berulang tanpa konteks | `admin/index.php:920-972` | Tambah `:aria-label="'Ring 10 untuk ' + item.nama"` pada setiap input. |
| UI-05 | **E-Ticket header invisible saat dicetak** — Gradient background tidak dicetak, teks putih di atas kertas putih | `e-ticket.php:99,270-276` | Tambah `print-color-adjust: exact; -webkit-print-color-adjust: exact;` pada `.hero-gradient`. Hide navbar spacer di print. |

### 5.2 HIGH

| ID | Temuan | Halaman | Rekomendasi |
|:---|:---|:---|:---|
| UI-06 | **Link navbar ke `/simulasi-lesan.html`** — Bypass PHP template, active state tidak bekerja | `header.php:94,114` | Ubah ke `/simulasi-lesan.php`. |
| UI-07 | **Admin layout collision** — Public navbar + bottom admin bar menyisakan <70% viewport di mobile | `admin/index.php:101,1585,3633` | Pisahkan layout admin dari public `header.php`/`footer.php`. |
| UI-08 | **17-kolom tabel tanpa sticky name** — Scroll horizontal menghilangkan identitas penembak | `admin/index.php`, `live-score.php` | `sticky left-0 bg-white shadow-sm z-10` pada kolom nama/nomor. |
| UI-09 | **Missing ARIA tab pattern** — Category switcher tanpa `role="tablist"`, `role="tab"`, `role="tabpanel"` | `live-score.php:57-72` | Implement WAI-ARIA tabs pattern lengkap. |
| UI-10 | **QR code 100% external dependency** — `api.qrserver.com` down = ticket tanpa QR | `e-ticket.php:152-157` | Generate lokal via PHP library atau client-side JS (`qrcodejs`). |

### 5.3 MEDIUM

| ID | Temuan | Halaman | Rekomendasi |
|:---|:---|:---|:---|
| UI-11 | **Mobile menu toggle tanpa ARIA** — Missing `aria-label`, `aria-expanded` | `header.php:101` | Tambah `aria-label="Menu navigasi"` dan `:aria-expanded="mobileMenu"`. |
| UI-12 | **Background polling tanpa pause** — `setInterval` live score berjalan saat tab hidden | `live-score.php:463-467` | Wrap dalam `visibilitychange` event listener. |
| UI-13 | **Memory leak blob URLs** — `createObjectURL()` tidak di-revoke saat re-select file | `daftar.php:390,402` | Panggil `URL.revokeObjectURL(this.ktaPreview)` sebelum assign baru. |
| UI-14 | **Native `alert()` untuk validasi** — Blocking thread, inconsistent styling | `daftar.php:385,397` | Gunakan styled inline error banner via Alpine state. |

### 5.4 LOW

| ID | Temuan | Halaman | Rekomendasi |
|:---|:---|:---|:---|
| UI-15 | **Kontras rendah** — `text-gray-400` pada background terang (~2.1:1, min WCAG = 4.5:1) | Multiple pages | Ganti `text-gray-400` ke `text-gray-500` atau `text-gray-600`. |
| UI-16 | **`target="_blank"` tanpa `rel="noopener"`** — Risiko tab-nabbing | `footer.php`, `index.php` | Tambah `rel="noopener noreferrer"` pada semua external links. |
| UI-17 | **`x-cloak` tanpa CSS rule** — Flash of unstyled content (FOUC) | `header.php` | Tambah `[x-cloak] { display: none !important; }` di `<style>`. |
| UI-18 | **Unused scroll listener** — `scrolled` diset tapi tidak digunakan | `header.php:71` | Hapus event listener jika tidak ada binding. |

---

## 6. Audit PHP Code Quality & Arsitektur (14 Temuan)

### 6.1 CRITICAL

| ID | Temuan | File & Baris | Rekomendasi |
|:---|:---|:---|:---|
| PHP-01 | **Runtime DDL pada setiap request** — 6× `ALTER TABLE` per API call | `scores-dueling.php:51-62`, `scores-presisi.php:18-20` | Hapus semua `ALTER TABLE` dari runtime. Pindahkan ke migration script satu kali. |
| PHP-02 | **Hardcoded production credentials** — DB password, admin password, token di source control | `config.php:7-15` | Migrasi ke `.env` file di luar web root. Gunakan `parse_ini_file()` atau `vlucas/phpdotenv`. |

### 6.2 HIGH

| ID | Temuan | File & Baris | Rekomendasi |
|:---|:---|:---|:---|
| PHP-03 | **Race condition BSC-26xxx** — Non-atomic read-then-write tanpa lock | `participants.php:89-101`, `status.php:53-67` | Atomic SQL dengan `FOR UPDATE` dalam transaksi. Tambah `UNIQUE INDEX`. |
| PHP-04 | **Schema mismatch** — `schema.sql` definisi `nilai INT`, app butuh `DECIMAL(6,1)` | `schema.sql:65`, `scores-presisi.php:127` | Sinkronkan `schema.sql` ke `DECIMAL(6,1)` sebagai single source of truth. |
| PHP-05 | **Monolith 3,634 baris** — `admin/index.php` campur auth, HTML, 1,500+ baris Alpine JS | `admin/index.php` | Extract JS ke `assets/js/admin.js`. Pisah modal templates. Adopsi layered structure. |
| PHP-06 | **Error message leak** — `$e->getMessage()` mengekspos schema DB ke client | 35+ lokasi di seluruh API | `error_log()` + return pesan generik "Terjadi kesalahan internal server." |

### 6.3 MEDIUM

| ID | Temuan | File & Baris | Rekomendasi |
|:---|:---|:---|:---|
| PHP-07 | **Zero foreign keys** — Tidak ada FK constraint, orphan records mungkin | `schema.sql` | Tambah `FOREIGN KEY (registration_id) REFERENCES registrations(registration_id) ON DELETE CASCADE`. |
| PHP-08 | **1NF violation** — Kategori disimpan sebagai CSV string, query LIKE full scan | `schema.sql:30` | Normalisasi ke tabel relasi `registration_categories` atau gunakan ENUM flags. |
| PHP-09 | **Triplikasi kode** — Generator BSC-26xxx copy-paste di 3 file | `participants.php`, `status.php` | Extract ke function reusable: `ParticipantService::generateNumber(PDO $db): string`. |
| PHP-10 | **Zero logging** — Tidak ada audit trail untuk aksi admin kritis | Seluruh aplikasi | Implement file logger: `[date] [level] [user] [IP] message`. Minimal log: login, konfirmasi, hapus, edit skor. |

### 6.4 LOW

| ID | Temuan | File & Baris | Rekomendasi |
|:---|:---|:---|:---|
| PHP-11 | **Naming inconsistency** — Mix `camelCase` dan `snake_case` di API response | Seluruh API | Standardisasi ke `snake_case` untuk JSON API, `camelCase` untuk PHP variabel. |
| PHP-12 | **Debug script di production** — `test_bracket_v2.php` terbuka | `test_bracket_v2.php` | Hapus dari production web root. |
| PHP-13 | **Loose equality** — `==` alih-alih `===` di perbandingan kritis | Multiple files | Ganti dengan `===` dan enable `declare(strict_types=1)`. |
| PHP-14 | **No PHP 8.x features** — Tidak ada Enums, match(), typed properties | Seluruh codebase | Adopt PHP 8.1 Enums untuk `RegistrationStatus`, `match()` expressions, typed properties. |

---

## 7. Audit Performance & Optimasi Aset (10 Temuan)

### 7.1 CRITICAL

| ID | Temuan | Detail Dampak | Rekomendasi |
|:---|:---|:---|:---|
| PERF-01 | **Logo images 4.37 MB total** — 2000×2000px PNG untuk elemen 36px | 99.5% byte terbuang per page load | Generate WebP thumbnails 80×80 (~4 KB ea.). Reduce ke ~12 KB total (**99.7% reduksi**). |
| PERF-02 | **Tailwind Play CDN di production** — ~3.8 MB JIT compiler loaded per page | FCP +1.5–2.5 detik, CPU blocking | `npx tailwindcss --minify` → precompiled CSS ~15–25 KB gzipped. |
| PERF-03 | **6 ALTER TABLE DDL per request** — MySQL metadata lock pada runtime | Database deadlock saat 300+ concurrent users | Hapus seluruh DDL dari API. Eksekusi sekali via migration. |

### 7.2 HIGH

| ID | Temuan | Detail Dampak | Rekomendasi |
|:---|:---|:---|:---|
| PERF-04 | **Zero caching** — Tidak ada `Cache-Control`, `ETag`, `mod_expires`, `mod_deflate` | 30 req/sec DB load pada 300 users polling | Tambah `.htaccess` caching directives + ETag pada polling API. |
| PERF-05 | **Simulator 60fps RAF loop** — 81 KB code + continuous animation di homepage | Battery drain & GPU heat mobile | `IntersectionObserver` pause saat off-viewport, atau pindahkan ke halaman terpisah. |
| PERF-06 | **No Gzip/Brotli** — Text responses dikirim uncompressed | 2–4× unnecessary network transfer | Tambah `mod_deflate` rules di `.htaccess`. |
| PERF-07 | **API tanpa pagination** — `fetchAll()` seluruh data tanpa limit | 350–500 KB per request pada 500 peserta | Implement `?page=1&limit=50` dengan metadata pagination. |

### 7.3 MEDIUM

| ID | Temuan | Detail Dampak | Rekomendasi |
|:---|:---|:---|:---|
| PERF-08 | **10 MB upload tanpa kompresi** — Raw camera photos langsung disimpan | PHP memory spike, disk waste | Client-side canvas resize ke maks 1600px @ 82% JPEG sebelum upload. |
| PERF-09 | **Missing database indexes** — `category`, `jumlah_masuk` tanpa index, leading `%` wildcard | Filesort & full table scan pada setiap query | Tambah composite indexes: `idx_cat_round_match`, `idx_presisi_ranking`. |

### 7.4 LOW

| ID | Temuan | Detail Dampak | Rekomendasi |
|:---|:---|:---|:---|
| PERF-10 | **Favicon 240 KB** — `favicon-bsc.png` 512×512 dipakai, `favicon-32x32.png` 2.88 KB diabaikan | 237 KB waste per page view | Ganti `<link rel="icon">` ke `favicon-32x32.png`. |

### Estimasi Dampak Keseluruhan Setelah Optimasi

| Metrik | Sebelum | Sesudah | Peningkatan |
|:---|:---|:---|:---:|
| Page Weight Landing | ~4.8–8.5 MB | ~350–550 KB | **>90%** ↓ |
| Image Weight Header | 4,368 KB | ~12 KB | **99.7%** ↓ |
| First Contentful Paint | ~1.8–3.2s | ~0.4–0.7s | **~4×** lebih cepat |
| Time to Interactive | ~3.5–5.5s | ~0.8–1.2s | **~4×** lebih cepat |
| DB Queries (300 users) | 60 queries/sec | <1 query/sec | **98%** ↓ |
| Runtime DDL | 6 ALTER/request | 0 | **Eliminasi total** |

---

## 8. Audit Functional Logic & Business Rules (12 Temuan)

### 8.1 CRITICAL

| ID | Temuan | File & Baris | Rekomendasi |
|:---|:---|:---|:---|
| LOGIC-01 | **Bracket generator crash N<4** — PHP Fatal Error saat 2 atau 3 peserta | `scores-dueling.php:168-245` | Special-case handling untuk N=2 (1 Final) dan N=3 (1 Pra-Eliminasi + 1 Final). |
| LOGIC-02 | **Inner X = 0.1 poin** — Seharusnya 10 poin dengan tiebreaker terpisah | `scores-presisi.php:127`, `admin/index.php:3053` | `$nilai = $ringX * 10 + sum(ring_scores)`. `ring_x` hanya tiebreaker. |

### 8.2 HIGH

| ID | Temuan | File & Baris | Rekomendasi |
|:---|:---|:---|:---|
| LOGIC-03 | **Stale downstream winner** — Ubah pemenang semifinal, final masih declare pemenang lama | `scores-dueling.php:604-647` | Cascade reset downstream match saat upstream participant slot diubah. |
| LOGIC-04 | **Dual-registration excludes dari Umum** — Filter `NOT LIKE '%bda%'` memblokir peserta BDA dari Dueling Umum | `admin/index.php:2677`, `scores-dueling.php:133` | Ganti negative filter ke positive inclusion: `LIKE '%umum%' AND LIKE '%dueling%'`. |
| LOGIC-05 | **Rejected peserta di leaderboard** — `no_peserta` dan `scores_presisi` tidak dibersihkan saat ditolak | `status.php:50-75` | Saat reject: `SET no_peserta = NULL`. `DELETE FROM scores_presisi WHERE registration_id = ?`. |
| LOGIC-06 | **Manual match drops category** — INSERT manual tanpa kolom `category`, default 'umum' padahal di tab BDA | `scores-dueling.php:548-567` | Tambah `category` pada `INSERT INTO dueling_matches`. |

### 8.3 MEDIUM

| ID | Temuan | File & Baris | Rekomendasi |
|:---|:---|:---|:---|
| LOGIC-07 | **Error message swallowed** — API return `message`, frontend cek `data.error` (undefined) | `daftar.php:446` | `throw new Error(data.message \|\| data.error \|\| 'Gagal menyimpan')`. |
| LOGIC-08 | **Rejected peserta blocked re-registrasi** — NRP check tanpa exclude status Rejected | `register.php:89-93` | `SELECT id FROM registrations WHERE nrp = ? AND status != 'Rejected'`. |
| LOGIC-09 | **Export Excel campur kategori** — Dueling Umum dan BDA interleaved tanpa kolom kategori | `export-excel.php:54-58` | Tambah kolom "Kelas/Kategori" pada output CSV dan SpreadsheetML. |

### 8.4 LOW

| ID | Temuan | File & Baris | Rekomendasi |
|:---|:---|:---|:---|
| LOGIC-10 | **Input >10 tembakan** — Tidak ada validasi batas peluru masuk | `scores-presisi.php:114-124` | `if ($jumlahMasuk > 10) return error 400`. |
| LOGIC-11 | **Hapus peserta tanpa cleanup** — Orphaned `dueling_matches` references dan file upload di disk | `participants.php:214-228` | Nullify dueling references. `@unlink()` file KTA dan bukti. |
| LOGIC-12 | **E-Ticket rejected = "Menunggu Verifikasi"** — Badge status kontradiktif dengan headline | `e-ticket.php:46-67` | Tambah branch `elseif ($ticket['status'] === 'Rejected')` dengan pesan ditolak dan catatan admin. |

---

## 9. Audit Simulasi Lesan (6 Temuan)

> **Catatan**: "Lesan" = kertas sasaran tembak (terminologi Perbakin/militer), bukan ujian lisan. Fitur ini adalah **Interactive 2D Canvas Physics Shooting Simulator** — simulasi presisi pistol 25M dengan bidikan visir, goyangan nafas/otot, giroskop HP, dan penilaian ring 1–10 + Inner X.

### 9.1 CRITICAL

| ID | Temuan | File & Baris | Rekomendasi |
|:---|:---|:---|:---|
| SIM-01 | **Zero keyboard support** — Tidak ada `keydown` listener. Pengguna keyboard tidak bisa bermain. | `simulasi-section.php:1674-1750` | Tambah: Space/Enter → tembak, Shift → tahan nafas, Arrow/WASD → geser bidikan. |
| SIM-02 | **Zero screen reader feedback** — Canvas & hit toast tanpa `aria-live` | `simulasi-section.php:105,1101-1107` | Tambah `role="status" aria-live="polite"` pada `#hitToast`. `aria-label` pada canvas. |

### 9.2 HIGH

| ID | Temuan | File & Baris | Rekomendasi |
|:---|:---|:---|:---|
| SIM-03 | **Desktop gyro exploit** — Mode Gyro ON tanpa sensor = stabilitas 100% (laser-pointer cheat) | `simulasi-lesan.html:828-836` | Deteksi `hasGyroSensor` sebelum izinkan mode gyro. Fallback ke mouse sway jika sensor tidak ada. |

### 9.3 MEDIUM

| ID | Temuan | File & Baris | Rekomendasi |
|:---|:---|:---|:---|
| SIM-04 | **Shot position drift on resize** — Koordinat lubang peluru absolut; rotasi HP = posisi melompat | `simulasi-lesan.html:707,1123` | Simpan posisi relatif ternormalisasi (-1.0 s.d. +1.0) terhadap pusat target. |

### 9.4 LOW

| ID | Temuan | File & Baris | Rekomendasi |
|:---|:---|:---|:---|
| SIM-05 | **Garis sumbu vertikal diagonal** — Canvas drawing defect pada crosshair target | `simulasi-section.php:618` | Perbaiki: `tCtx.moveTo(cx, cy - (10*scale)); tCtx.lineTo(cx, cy + (10*scale));` |
| SIM-06 | **Timer countdown leak** — `setInterval` lokal tidak di-clear saat quick reset (race condition) | `simulasi-section.php:1198` | Simpan interval ID ke variabel global modul. Clear di `resetNewMatchSeries()`. |

### 9.5 Temuan Tambahan (Non-Defect)

| Temuan | Detail |
|:---|:---|
| **Inkonsistensi jarak** | Turnamen resmi = Presisi **20M**, simulator bertuliskan **25M**. Selaraskan judul. |
| **Halaman orphaned** | `site/simulasi-lesan.php` lengkap dengan template PHP tapi tidak ada link yang menuju ke sana — semua link mengarah ke `.html` standalone. |
| **Dead code** | Fungsi `getElevationDrop()` dan konstanta `GYRO_SENS_MULTIPLIER` tidak terpakai. |
| **Tembakan miss hilang** | Key `'0 (Lepas)'` tidak cocok dengan array order `'0'` di rekap hasil — miss shots tidak ditampilkan di ringkasan. |

---

## 10. Rencana Remediasi 4 Fase

### 🔴 FASE 1 — Tindakan Kritis Segera (Hari 1)

Target: Menutup kerentanan keamanan paling berbahaya dan memperbaiki bug yang berdampak pada keabsahan skor turnamen.

| # | Aksi | File Target | Est. Waktu |
|:---:|:---|:---|:---:|
| 1 | Hapus semua file `.bak` dari production server | `site/includes/*.bak*`, `site/api/*.bak*`, `site/*.bak*` | 10 menit |
| 2 | Buat `site/uploads/.htaccess` yang blokir eksekusi script | Buat file baru | 5 menit |
| 3 | Validasi ekstensi file upload dengan allowlist strict | `site/api/register.php:114-128` | 15 menit |
| 4 | Hapus seluruh `ALTER TABLE` dari runtime API | `site/api/scores-dueling.php:51-62`, `site/api/scores-presisi.php:18-20, 83-86` | 15 menit |
| 5 | Update `schema.sql` ke schema final (`nilai DECIMAL(6,1)`, kolom `category`, dll.) | `site/schema.sql` | 20 menit |
| 6 | Fix scoring formula: Inner X = 10 poin + tiebreaker | `site/api/scores-presisi.php:119-127`, `site/admin/index.php:3044-3056` | 30 menit |
| 7 | Hapus `$e->getMessage()` dari seluruh API response (ganti pesan generik) | 35+ lokasi di seluruh API | 30 menit |
| 8 | Hapus `test_bracket_v2.php` dari production | `site/test_bracket_v2.php` | 2 menit |

### 🟠 FASE 2 — Keamanan & Konfigurasi (Hari 2-3)

Target: Menghilangkan backdoor autentikasi, melindungi data PII, dan mengatasi bug logika bisnis kritis.

| # | Aksi | File Target | Est. Waktu |
|:---:|:---|:---|:---:|
| 1 | Deprecate `ADMIN_TOKEN` — enforce session-based auth | `site/includes/config.php`, `site/includes/db.php`, `site/admin/index.php` | 2 jam |
| 2 | Enforce `requirePermission()` di semua API admin | `site/api/participants.php`, `site/api/scores-*.php`, `site/api/admin-users.php` | 1 jam |
| 3 | Strip `registration_id` dari `public-scores.php` | `site/api/public-scores.php:18-26` | 15 menit |
| 4 | Implement `session_regenerate_id(true)` + secure cookie flags | `site/api/auth.php`, `site/admin/index.php`, `site/includes/auth.php` | 30 menit |
| 5 | Fix CORS: whitelist origins | `site/includes/db.php:54-67` | 15 menit |
| 6 | Sanitize CSV export dari formula injection | `site/admin/export-excel.php:88-112` | 20 menit |
| 7 | Fix bracket generator untuk N=2 dan N=3 | `site/api/scores-dueling.php:168-245` | 1 jam |
| 8 | Fix category filtering (positive inclusion) | `site/admin/index.php:2677`, `site/api/scores-dueling.php:133` | 30 menit |
| 9 | Clean status transitions: reject → clear `no_peserta` + delete scores | `site/api/status.php:50-75`, `site/api/participants.php` | 30 menit |
| 10 | Atomic `no_peserta` generation dengan `SELECT ... FOR UPDATE` + `UNIQUE INDEX` | `site/api/participants.php:89-101`, `site/schema.sql` | 45 menit |
| 11 | Fix manual match insert — include `category` | `site/api/scores-dueling.php:548-567` | 10 menit |
| 12 | Fix error message key mismatch di `daftar.php` | `site/daftar.php:446` | 5 menit |
| 13 | Fix e-ticket rejected state display | `site/e-ticket.php:46-67` | 20 menit |
| 14 | Tambah kolom kategori pada dueling export | `site/admin/export-excel.php` | 20 menit |

### 🟡 FASE 3 — Performance & Optimasi (Minggu 1)

Target: Mengurangi page weight >90%, menghilangkan bottleneck database, dan mengoptimasi pengalaman pengguna.

| # | Aksi | File Target | Est. Waktu |
|:---:|:---|:---|:---:|
| 1 | Generate WebP thumbnails untuk logo (80×80px ~4 KB) | `site/assets/` | 30 menit |
| 2 | Ganti favicon ke `favicon-32x32.png` (2.88 KB) | `site/includes/header.php:13-15` | 5 menit |
| 3 | Replace Tailwind Play CDN dengan precompiled CSS | `site/includes/header.php:18` + build system | 2 jam |
| 4 | Tambah Gzip/Brotli + Cache-Control di `.htaccess` | `site/.htaccess` | 20 menit |
| 5 | Hapus embed simulator dari `index.php` atau tambah IntersectionObserver | `site/index.php:778`, `site/includes/simulasi-section.php` | 1 jam |
| 6 | Implement ETag/304 pada `public-scores.php` polling | `site/api/public-scores.php` | 30 menit |
| 7 | Tambah database indexes | `site/schema.sql` | 15 menit |
| 8 | Implement pagination pada `participants.php` API | `site/api/participants.php` | 1 jam |
| 9 | Pause polling saat tab hidden (`visibilitychange`) | `site/live-score.php:463-467` | 15 menit |
| 10 | Client-side image resize sebelum upload | `site/daftar.php` | 1 jam |

### 🟢 FASE 4 — UX, Aksesibilitas & Arsitektur (Minggu 2-4)

Target: WCAG 2.1 compliance, perbaikan arsitektur kode, dan polish pengalaman pengguna.

| # | Aksi | File Target | Est. Waktu |
|:---:|:---|:---|:---:|
| 1 | Fix form accessibility: `id`/`for` binding, keyboard checkboxes | `site/daftar.php:136-248` | 1 jam |
| 2 | Fix 5 modal accessibility: `role="dialog"`, focus trap, Escape | `site/admin/index.php` (5 modals) | 2 jam |
| 3 | Label admin scoring inputs dengan `aria-label` | `site/admin/index.php:920-972` | 30 menit |
| 4 | Sticky participant name pada tabel skor | `site/admin/index.php`, `site/live-score.php` | 30 menit |
| 5 | Pisahkan admin layout dari public header/footer | `site/admin/index.php` | 2 jam |
| 6 | Generate QR code lokal (hapus dependency `api.qrserver.com`) | `site/e-ticket.php:152-157` | 1 jam |
| 7 | Fix e-ticket print styles | `site/e-ticket.php:270-276` | 30 menit |
| 8 | ARIA tab pattern pada live-score category switcher | `site/live-score.php:57-72` | 30 menit |
| 9 | Implement CSRF token | `site/includes/auth.php`, seluruh API | 2 jam |
| 10 | Implement audit logging | Buat `site/includes/logger.php` | 1 jam |
| 11 | Tambah keyboard controls ke simulator | `site/includes/simulasi-section.php` | 1 jam |
| 12 | Selaraskan jarak simulator (20M vs 25M) | `site/includes/simulasi-section.php` | 15 menit |
| 13 | Fix link navbar ke `/simulasi-lesan.php` | `site/includes/header.php:94,114` | 5 menit |
| 14 | Implement rate limiting pada login & registrasi | `site/api/auth.php`, `site/api/register.php` | 2 jam |
| 15 | Pindahkan credentials ke `.env` file | `site/includes/config.php` | 1 jam |
| 16 | Fix kontras teks rendah (WCAG AA) | Multiple pages | 30 menit |

---

## 11. Lampiran: Temuan Positif

Meskipun banyak temuan yang perlu ditangani, audit juga mengidentifikasi beberapa aspek positif yang patut dicatat:

| Aspek | Detail |
|:---|:---|
| ✅ **Zero SQL Injection** | Seluruh query database menggunakan PDO prepared statements dengan parameter binding. `PDO::ATTR_EMULATE_PREPARES = false` dikonfigurasi. |
| ✅ **UI/Visual Design** | Desain frontend kohesif dengan estetika tactical yang konsisten. Penggunaan Oswald + Inter + JetBrains Mono menciptakan hierarki tipografi yang kuat. |
| ✅ **Empty States** | Tabel-tabel pada admin, peserta, dan live score sudah memiliki empty state yang informatif dengan ikon dan pesan. |
| ✅ **Loading Feedback** | Form pendaftaran memiliki spinner loading state saat submit. Tombol refresh live score memiliki animasi spin. |
| ✅ **Simulator Physics** | Physics engine simulasi lesan sangat canggih — mensimulasikan sight alignment error, siklus pernapasan, tremor otot, tahan nafas 3 detik, recoil, dan penilaian ring ISSF yang akurat. |
| ✅ **Audio Synthesis** | Web Audio API synthesizer menghasilkan efek suara tembakan, denting sasaran, dan buzzer akhir sesi secara procedural tanpa file audio eksternal. |
| ✅ **Mobile Touch** | Simulator mendukung multi-touch (jari bidik + jari tembak bersamaan) dan sensor gyroscope smartphone. |
| ✅ **Reduced Motion** | `@media (prefers-reduced-motion: reduce)` sudah dipertimbangkan pada animasi homepage dan tactical scramble. |

---

**Akhir Laporan Audit**

*Dokumen ini dihasilkan dari analisis kode sumber lengkap oleh 6 auditor paralel. Seluruh temuan telah diverifikasi terhadap file produksi aktual.*
