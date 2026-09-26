<?php
require_once __DIR__ . '/../includes/db.php';
cors();

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    jsonResponse(['success' => false, 'message' => 'Method not allowed'], 405);
}

$registrationId = $_GET['id'] ?? '';

if (empty($registrationId)) {
    jsonResponse(['success' => false, 'message' => 'Parameter id wajib diisi'], 400);
}

$db = getDB();

try {
    $stmt = $db->prepare('
        SELECT registration_id, nama, email, telepon, pangkat, nrp, satuan, kategori, 
               kta_filename, status, no_peserta, created_at
        FROM registrations 
        WHERE registration_id = ?
    ');
    $stmt->execute([$registrationId]);
    $registration = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$registration) {
        jsonResponse(['success' => false, 'message' => 'Data registrasi tidak ditemukan'], 404);
    }

    if ($registration['status'] !== 'Verified') {
        jsonResponse([
            'success' => false,
            'message' => 'E-Ticket hanya tersedia untuk peserta yang sudah diverifikasi',
            'status' => $registration['status']
        ], 403);
    }

    // Build KTA URL
    $registration['kta_url'] = $registration['kta_filename']
        ? SITE_URL . '/uploads/kta/' . $registration['kta_filename']
        : null;

    // Remove raw filename from response
    unset($registration['kta_filename']);

    // Add event info
    $registration['event_name'] = EVENT_NAME;
    $registration['event_date'] = EVENT_DATE;
    $registration['event_location'] = EVENT_LOCATION;

    jsonResponse([
        'success' => true,
        'data' => $registration
    ]);

} catch (PDOException $e) {
    error_log('Database error in eticket.php: ' . $e->getMessage());
    jsonResponse(['success' => false, 'message' => 'Gagal mengambil data tiket'], 500);
}
