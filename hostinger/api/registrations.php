<?php
// API Endpoint: Mendapatkan Semua Data Pendaftaran untuk Admin (GET)
require_once __DIR__ . '/../cors.php';
require_once __DIR__ . '/../db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

// Verifikasi token admin via header atau query param
$authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
$token = $_GET['token'] ?? '';

if (str_starts_with($authHeader, 'Bearer ')) {
    $token = substr($authHeader, 7);
}

if ($token !== ADMIN_TOKEN) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Akses ditolak: Token admin tidak valid']);
    exit;
}

$db = getDB();

try {
    $stmt = $db->query('SELECT * FROM registrations ORDER BY id DESC');
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Tambahkan URL lengkap untuk file KTA dan bukti transfer
    foreach ($rows as &$row) {
        $row['kta_url'] = $row['kta_filename'] 
            ? BASE_URL . '/uploads/kta/' . $row['kta_filename'] 
            : null;
            
        $row['bukti_url'] = $row['bukti_filename'] 
            ? BASE_URL . '/uploads/bukti/' . $row['bukti_filename'] 
            : null;
    }

    echo json_encode([
        'success' => true,
        'data'    => $rows
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
}
