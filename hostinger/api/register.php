<?php
// API Endpoint: Registrasi Peserta Baru (POST)
require_once __DIR__ . '/../cors.php';
require_once __DIR__ . '/../db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

$rawInput = file_get_contents('php://input');
$input = json_decode($rawInput, true);

if (!$input) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Payload JSON tidak valid']);
    exit;
}

// Validasi field wajib
$requiredFields = ['nama', 'email', 'telepon', 'pangkat', 'nrp', 'satuan', 'kategori'];
foreach ($requiredFields as $field) {
    if (empty(trim($input[$field] ?? ''))) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => "Field '$field' wajib diisi"]);
        exit;
    }
}

$db = getDB();

// Cek duplikasi NRP
$stmt = $db->prepare('SELECT id FROM registrations WHERE nrp = ? LIMIT 1');
$stmt->execute([trim($input['nrp'])]);
if ($stmt->fetch()) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error'   => 'NRP ' . htmlspecialchars($input['nrp']) . ' sudah terdaftar sebelumnya. Silakan hubungi panitia jika Anda memerlukan perubahan.'
    ]);
    exit;
}

// Generate unik Registration ID (contoh: BDA-A1B2C3D4)
$regId = 'BDA-' . strtoupper(substr(bin2hex(random_bytes(4)), 0, 8));

// Helper upload file Base64
function saveBase64File($fileData, $prefix, $subdir) {
    if (empty($fileData) || empty($fileData['data'])) {
        return null;
    }
    
    // Hilangkan data:image/xxx;base64, jika masih ada
    $base64String = preg_replace('#^data:image/\w+;base64,#i', '', $fileData['data']);
    $binaryData = base64_decode($base64String);
    
    if ($binaryData === false) {
        throw new Exception('Data gambar Base64 tidak valid');
    }
    
    if (strlen($binaryData) > MAX_FILE_SIZE) {
        throw new Exception('Ukuran file melebihi batas maksimal 2MB');
    }
    
    $mime = $fileData['mimeType'] ?? 'image/jpeg';
    if (!in_array($mime, ALLOWED_TYPES)) {
        throw new Exception('Format file harus berupa JPG, PNG, atau WebP');
    }
    
    $ext = match($mime) {
        'image/jpeg' => '.jpg',
        'image/png'  => '.png',
        'image/webp' => '.webp',
        default      => '.jpg',
    };
    
    $filename = $prefix . '_' . time() . $ext;
    $targetDir = UPLOAD_DIR . $subdir . '/';
    
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0755, true);
    }
    
    if (file_put_contents($targetDir . $filename, $binaryData) === false) {
        throw new Exception("Gagal menyimpan file ke direktori $subdir");
    }
    
    return $filename;
}

$ktaFilename = null;
$buktiFilename = null;

try {
    if (!empty($input['fotoKTA'])) {
        $ktaFilename = saveBase64File($input['fotoKTA'], $regId . '_kta', 'kta');
    }
    if (!empty($input['buktiTransfer'])) {
        $buktiFilename = saveBase64File($input['buktiTransfer'], $regId . '_bukti', 'bukti');
    }
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    exit;
}

// Simpan ke Database MySQL
try {
    $insertSql = '
        INSERT INTO registrations 
        (registration_id, nama, email, telepon, pangkat, nrp, satuan, kategori, kta_filename, bukti_filename, status)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, "Pending")
    ';
    
    $insertStmt = $db->prepare($insertSql);
    $insertStmt->execute([
        $regId,
        trim($input['nama']),
        trim($input['email']),
        trim($input['telepon']),
        trim($input['pangkat']),
        trim($input['nrp']),
        trim($input['satuan']),
        is_array($input['kategori']) ? implode(', ', $input['kategori']) : trim($input['kategori']),
        $ktaFilename,
        $buktiFilename
    ]);

    echo json_encode([
        'success'        => true,
        'registrationId' => $regId,
        'message'        => 'Pendaftaran berhasil dikirim. Panitia akan memverifikasi pembayaran Anda.'
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Gagal menyimpan data ke database: ' . $e->getMessage()]);
}
