<?php
require_once __DIR__ . '/../includes/db.php';
cors();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false, 'message' => 'Method not allowed'], 405);
}

// Validate required fields
$requiredFields = ['nama', 'email', 'telepon', 'pangkat', 'nrp', 'satuan', 'kategori'];
$missing = [];
foreach ($requiredFields as $field) {
    if (empty($_POST[$field])) {
        $missing[] = $field;
    }
}

if (!empty($missing)) {
    jsonResponse(['success' => false, 'message' => 'Field wajib tidak lengkap: ' . implode(', ', $missing)], 400);
}

$nama = trim($_POST['nama']);
$email = trim($_POST['email']);
$teleponRaw = trim($_POST['telepon']);
$pangkat = trim($_POST['pangkat']);
$nrp = trim($_POST['nrp']);
$satuan = trim($_POST['satuan']);

// Normalisasi nomor telepon (08x, +628x, 628x, spasi, strip)
$cleanedPhone = preg_replace('/[^\d+]/', '', $teleponRaw);
$cleanedPhone = ltrim($cleanedPhone, '+');
if (strpos($cleanedPhone, '0') === 0) {
    $telepon = '0' . substr($cleanedPhone, 1);
} elseif (strpos($cleanedPhone, '62') === 0) {
    $telepon = '0' . substr($cleanedPhone, 2);
} elseif (strpos($cleanedPhone, '8') === 0) {
    $telepon = '0' . $cleanedPhone;
} else {
    $telepon = $cleanedPhone;
}

// Handle kategori (can be array or string)
$kategori = $_POST['kategori'];
if (is_array($kategori)) {
    $kategori = implode(',', array_map('trim', $kategori));
} else {
    $kategori = trim($kategori);
}

// Validate email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    jsonResponse(['success' => false, 'message' => 'Format email tidak valid'], 400);
}

// Validate file uploads existence
if (!isset($_FILES['foto_kta']) || $_FILES['foto_kta']['error'] !== UPLOAD_ERR_OK) {
    jsonResponse(['success' => false, 'message' => 'File foto KTA wajib diupload'], 400);
}

if (!isset($_FILES['bukti_transfer']) || $_FILES['bukti_transfer']['error'] !== UPLOAD_ERR_OK) {
    jsonResponse(['success' => false, 'message' => 'File bukti transfer wajib diupload'], 400);
}

// Check file sizes (Maks 10MB)
if ($_FILES['foto_kta']['size'] > MAX_FILE_SIZE) {
    jsonResponse(['success' => false, 'message' => 'Ukuran file KTA melebihi batas maksimum 10MB'], 400);
}

if ($_FILES['bukti_transfer']['size'] > MAX_FILE_SIZE) {
    jsonResponse(['success' => false, 'message' => 'Ukuran file bukti transfer melebihi batas maksimum 10MB'], 400);
}

// Strict File Extension Allowlist Validation (SEC-01)
$allowedExtensions = ['jpg', 'jpeg', 'png', 'webp', 'pdf'];
$ktaExt = strtolower(pathinfo($_FILES['foto_kta']['name'], PATHINFO_EXTENSION));
$buktiExt = strtolower(pathinfo($_FILES['bukti_transfer']['name'], PATHINFO_EXTENSION));

if (!in_array($ktaExt, $allowedExtensions, true)) {
    jsonResponse(['success' => false, 'message' => 'Ekstensi file KTA tidak diizinkan. Gunakan JPG, PNG, WebP, atau PDF'], 400);
}

if (!in_array($buktiExt, $allowedExtensions, true)) {
    jsonResponse(['success' => false, 'message' => 'Ekstensi file bukti transfer tidak diizinkan. Gunakan JPG, PNG, WebP, atau PDF'], 400);
}

// Validate MIME types via magic bytes
$allowedMimes = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp', 'application/pdf'];
$ktaMime = mime_content_type($_FILES['foto_kta']['tmp_name']);
$buktiMime = mime_content_type($_FILES['bukti_transfer']['tmp_name']);

if (!in_array($ktaMime, $allowedMimes, true)) {
    jsonResponse(['success' => false, 'message' => 'Format konten file KTA tidak valid. Gunakan file JPG, PNG, WebP, atau PDF asli'], 400);
}

if (!in_array($buktiMime, $allowedMimes, true)) {
    jsonResponse(['success' => false, 'message' => 'Format konten file bukti transfer tidak valid. Gunakan file JPG, PNG, WebP, atau PDF asli'], 400);
}

$db = getDB();

// Check NRP uniqueness (Exclude Rejected participants so they can re-register if rejected)
$stmt = $db->prepare("SELECT id FROM registrations WHERE nrp = ? AND status != 'Rejected'");
$stmt->execute([$nrp]);
if ($stmt->fetch()) {
    jsonResponse(['success' => false, 'message' => 'NRP sudah terdaftar dan aktif. Jika Anda merasa ini kesalahan, silakan hubungi panitia.'], 409);
}

// Generate unique registration_id: BDA-XXXXXXXX
do {
    $registrationId = 'BDA-' . bin2hex(random_bytes(4));
    $stmt = $db->prepare('SELECT id FROM registrations WHERE registration_id = ?');
    $stmt->execute([$registrationId]);
} while ($stmt->fetch());

// Create upload directories if they don't exist
$ktaDir = UPLOAD_DIR . '/kta';
$buktiDir = UPLOAD_DIR . '/bukti';

if (!is_dir($ktaDir)) {
    mkdir($ktaDir, 0755, true);
}
if (!is_dir($buktiDir)) {
    mkdir($buktiDir, 0755, true);
}

// Safe normalized extensions
$safeKtaExt = ($ktaExt === 'jpeg') ? 'jpg' : $ktaExt;
$safeBuktiExt = ($buktiExt === 'jpeg') ? 'jpg' : $buktiExt;

$ktaFilename = $registrationId . '_kta_' . time() . '.' . $safeKtaExt;
$buktiFilename = $registrationId . '_bukti_' . time() . '.' . $safeBuktiExt;

// Move uploaded files
if (!move_uploaded_file($_FILES['foto_kta']['tmp_name'], $ktaDir . '/' . $ktaFilename)) {
    jsonResponse(['success' => false, 'message' => 'Gagal menyimpan file KTA'], 500);
}

if (!move_uploaded_file($_FILES['bukti_transfer']['tmp_name'], $buktiDir . '/' . $buktiFilename)) {
    @unlink($ktaDir . '/' . $ktaFilename);
    jsonResponse(['success' => false, 'message' => 'Gagal menyimpan file bukti transfer'], 500);
}

// Insert into database
try {
    $stmt = $db->prepare('
        INSERT INTO registrations 
        (registration_id, nama, email, telepon, pangkat, nrp, satuan, kategori, kta_filename, bukti_filename, status, created_at, updated_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
    ');
    $stmt->execute([
        $registrationId,
        $nama,
        $email,
        $telepon,
        $pangkat,
        $nrp,
        $satuan,
        $kategori,
        $ktaFilename,
        $buktiFilename,
        'Pending'
    ]);

    jsonResponse([
        'success' => true,
        'message' => 'Pendaftaran berhasil! Simpan ID registrasi Anda.',
        'registrationId' => $registrationId
    ], 201);

} catch (PDOException $e) {
    @unlink($ktaDir . '/' . $ktaFilename);
    @unlink($buktiDir . '/' . $buktiFilename);
    error_log('Gagal menyimpan pendaftaran: ' . $e->getMessage());
    jsonResponse(['success' => false, 'message' => 'Terjadi kesalahan sistem saat menyimpan pendaftaran. Silakan coba beberapa saat lagi.'], 500);
}
