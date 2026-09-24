<?php
// API Endpoint: Mendapatkan Data E-Ticket Publik Berdasarkan Registration ID (GET)
require_once __DIR__ . '/../cors.php';
require_once __DIR__ . '/../db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

$regId = trim($_GET['id'] ?? '');

if (empty($regId)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Parameter ID pendaftaran wajib disertakan']);
    exit;
}

$db = getDB();

try {
    $stmt = $db->prepare('
        SELECT registration_id, no_peserta, nama, email, telepon, pangkat, nrp, satuan, kategori, kta_filename, status, created_at 
        FROM registrations 
        WHERE registration_id = ? 
        LIMIT 1
    ');
    $stmt->execute([$regId]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => 'Data E-Ticket tidak ditemukan']);
        exit;
    }

    if ($row['status'] !== 'Verified') {
        http_response_code(403);
        echo json_encode([
            'success' => false, 
            'error'   => 'E-Ticket belum aktif karena pembayaran masih dalam proses verifikasi oleh panitia'
        ]);
        exit;
    }

    $ktaUrl = $row['kta_filename'] 
        ? BASE_URL . '/uploads/kta/' . $row['kta_filename'] 
        : null;

    echo json_encode([
        'success' => true,
        'data'    => [
            'registrationId' => $row['registration_id'],
            'noPeserta'      => $row['no_peserta'],
            'nama'           => $row['nama'],
            'email'          => $row['email'],
            'telepon'        => $row['telepon'],
            'pangkat'        => $row['pangkat'],
            'nrp'            => $row['nrp'],
            'satuan'         => $row['satuan'],
            'kategori'       => $row['kategori'],
            'ktaUrl'         => $ktaUrl,
            'createdAt'      => $row['created_at'],
            'status'         => $row['status']
        ]
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
}
