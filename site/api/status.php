<?php
require_once __DIR__ . '/../includes/db.php';
cors();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false, 'message' => 'Method not allowed'], 405);
}

// Parse JSON body
$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    jsonResponse(['success' => false, 'message' => 'Invalid JSON body'], 400);
}

// Authenticate using token from Header, JSON body, or Query Param
$token = $_SERVER['HTTP_X_ADMIN_TOKEN'] ?? $input['token'] ?? $_GET['token'] ?? '';
if ($token !== ADMIN_TOKEN) {
    jsonResponse(['success' => false, 'message' => 'Unauthorized'], 401);
}

// Validate required fields
$registrationId = trim($input['registrationId'] ?? $input['registration_id'] ?? '');
$rawStatus = trim($input['status'] ?? '');
$status = ucfirst(strtolower($rawStatus));
$notes = trim($input['notes'] ?? $input['admin_notes'] ?? '');
$customNoPeserta = trim($input['no_peserta'] ?? $input['noPeserta'] ?? '');

if (empty($registrationId)) {
    jsonResponse(['success' => false, 'message' => 'registration_id wajib diisi'], 400);
}

$allowedStatuses = ['Pending', 'Verified', 'Rejected'];
if (!in_array($status, $allowedStatuses)) {
    jsonResponse(['success' => false, 'message' => 'Status tidak valid. Gunakan: ' . implode(', ', $allowedStatuses)], 400);
}

$db = getDB();

// Check if registration exists
$stmt = $db->prepare('SELECT id, status, no_peserta FROM registrations WHERE registration_id = ?');
$stmt->execute([$registrationId]);
$registration = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$registration) {
    jsonResponse(['success' => false, 'message' => 'Registrasi tidak ditemukan'], 404);
}

try {
    $noPeserta = !empty($customNoPeserta) ? $customNoPeserta : $registration['no_peserta'];

    // Auto-generate no_peserta when status is Verified and no_peserta is not yet assigned
    if ($status === 'Verified' && empty($noPeserta)) {
        $stmt = $db->query("SELECT no_peserta FROM registrations WHERE no_peserta LIKE 'BSC-26%'");
        $allPeserta = $stmt ? $stmt->fetchAll(PDO::FETCH_COLUMN) : [];
        $maxNumber = 0;
        foreach ($allPeserta as $p) {
            if (preg_match('/BSC-26(\d+)/i', $p, $matches)) {
                $num = (int)$matches[1];
                if ($num > $maxNumber) {
                    $maxNumber = $num;
                }
            }
        }
        $nextNumber = $maxNumber + 1;
        $noPeserta = 'BSC-26' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }

    // Update the registration
    $stmt = $db->prepare('
        UPDATE registrations 
        SET status = ?, no_peserta = ?, admin_notes = ?, updated_at = NOW()
        WHERE registration_id = ?
    ');
    $stmt->execute([$status, $noPeserta, $notes, $registrationId]);

    jsonResponse([
        'success' => true,
        'message' => 'Status berhasil diperbarui',
        'data' => [
            'registration_id' => $registrationId,
            'status' => $status,
            'no_peserta' => $noPeserta,
            'admin_notes' => $notes
        ]
    ]);

} catch (PDOException $e) {
    jsonResponse(['success' => false, 'message' => 'Gagal memperbarui status: ' . $e->getMessage()], 500);
}
