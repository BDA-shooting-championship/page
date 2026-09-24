<?php
// API Endpoint: Update Status Pendaftaran & Auto-Generate No. Peserta (POST)
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

// Verifikasi token admin
$token = $input['token'] ?? '';
if ($token !== ADMIN_TOKEN) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Akses ditolak: Token admin tidak valid']);
    exit;
}

$regId = trim($input['registrationId'] ?? '');
$newStatus = trim($input['status'] ?? '');
$notes = trim($input['notes'] ?? '');

if (empty($regId) || !in_array($newStatus, ['Pending', 'Verified', 'Rejected'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Registration ID atau Status tidak valid']);
    exit;
}

$db = getDB();

try {
    // Ambil data peserta saat ini
    $selectStmt = $db->prepare('SELECT id, no_peserta, status FROM registrations WHERE registration_id = ?');
    $selectStmt->execute([$regId]);
    $current = $selectStmt->fetch();

    if (!$current) {
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => 'Data pendaftaran tidak ditemukan']);
        exit;
    }

    $noPeserta = $current['no_peserta'];

    // Jika di-verifikasi dan belum punya No. Peserta, buatkan nomor otomatis sequential: BSC-001, BSC-002...
    if ($newStatus === 'Verified' && empty($noPeserta)) {
        // Cari angka tertinggi yang sudah terdaftar
        $maxStmt = $db->query("
            SELECT MAX(CAST(SUBSTRING(no_peserta, 5) AS UNSIGNED)) as max_num 
            FROM registrations 
            WHERE no_peserta IS NOT NULL AND no_peserta LIKE 'BSC-%'
        ");
        $maxNum = (int)$maxStmt->fetchColumn();
        $nextNum = $maxNum + 1;
        $noPeserta = 'BSC-' . str_pad($nextNum, 3, '0', STR_PAD_LEFT);
    }

    // Update database
    $updateStmt = $db->prepare('
        UPDATE registrations 
        SET status = ?, no_peserta = ?, admin_notes = ? 
        WHERE registration_id = ?
    ');
    $updateStmt->execute([$newStatus, $noPeserta, $notes, $regId]);

    echo json_encode([
        'success'        => true,
        'message'        => "Status berhasil diperbarui menjadi $newStatus",
        'registrationId' => $regId,
        'status'         => $newStatus,
        'noPeserta'      => $noPeserta
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
}
