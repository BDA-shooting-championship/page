<?php
require_once __DIR__ . '/../includes/db.php';
cors();

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    jsonResponse(['success' => false, 'message' => 'Method not allowed'], 405);
}

requireAdmin();

$db = getDB();

try {
    $stmt = $db->query('SELECT * FROM registrations ORDER BY id DESC');
    $registrations = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Add file URLs
    foreach ($registrations as &$reg) {
        $reg['kta_url'] = $reg['kta_filename']
            ? SITE_URL . '/uploads/kta/' . $reg['kta_filename']
            : null;
        $reg['bukti_url'] = $reg['bukti_filename']
            ? SITE_URL . '/uploads/bukti/' . $reg['bukti_filename']
            : null;
    }
    unset($reg);

    jsonResponse([
        'success' => true,
        'data' => $registrations,
        'total' => count($registrations)
    ]);

} catch (PDOException $e) {
    jsonResponse(['success' => false, 'message' => 'Gagal mengambil data: ' . $e->getMessage()], 500);
}
