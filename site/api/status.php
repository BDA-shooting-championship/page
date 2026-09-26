<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
cors();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false, 'message' => 'Method not allowed'], 405);
}

// Parse JSON body
$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    jsonResponse(['success' => false, 'message' => 'Invalid JSON body'], 400);
}

// Authenticate using token from Header, JSON body, Query Param, or active admin session
$token = $_SERVER['HTTP_X_ADMIN_TOKEN'] ?? $input['token'] ?? $_GET['token'] ?? '';
$isSessionAdmin = isAdminLoggedIn();

if ($token !== ADMIN_TOKEN && !$isSessionAdmin) {
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
$stmt = $db->prepare('SELECT id, status, no_peserta, kategori, nama, satuan FROM registrations WHERE registration_id = ?');
$stmt->execute([$registrationId]);
$registration = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$registration) {
    jsonResponse(['success' => false, 'message' => 'Registrasi tidak ditemukan'], 404);
}

try {
    $noPeserta = !empty($customNoPeserta) ? $customNoPeserta : $registration['no_peserta'];

    // Auto-generate no_peserta atomically when status is Verified and no_peserta is not yet assigned
    if ($status === 'Verified' && empty($noPeserta)) {
        $stmt = $db->query("SELECT COALESCE(MAX(CAST(SUBSTRING(no_peserta, 7) AS UNSIGNED)), 0) + 1 FROM registrations WHERE no_peserta LIKE 'BSC-26%'");
        $nextNum = (int)$stmt->fetchColumn();
        $noPeserta = 'BSC-26' . str_pad($nextNum, 3, '0', STR_PAD_LEFT);
    } elseif ($status === 'Rejected') {
        // Clear no_peserta and remove from scores_presisi if rejected
        $noPeserta = null;
        try {
            $db->prepare('DELETE FROM scores_presisi WHERE registration_id = ?')->execute([$registrationId]);
        } catch (Exception $e) {}
    }

    // Update the registration
    $stmt = $db->prepare('
        UPDATE registrations 
        SET status = ?, no_peserta = ?, admin_notes = ?, updated_at = NOW()
        WHERE registration_id = ?
    ');
    $stmt->execute([$status, $noPeserta, $notes, $registrationId]);

    // If verified, ensure present in scores_presisi if presisi category
    if ($status === 'Verified') {
        $kategori = $registration['kategori'] ?? '';
        if (stripos($kategori, 'presisi') !== false || stripos($kategori, 'keduanya') !== false) {
            $chk = $db->prepare('SELECT id FROM scores_presisi WHERE registration_id = ?');
            $chk->execute([$registrationId]);
            if (!$chk->fetch()) {
                $db->prepare('INSERT INTO scores_presisi (registration_id, no_peserta, nama, satuan) VALUES (?, ?, ?, ?)')
                   ->execute([$registrationId, $noPeserta, $registration['nama'], $registration['satuan']]);
            }
        }
    }

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
    error_log('Gagal memperbarui status: ' . $e->getMessage());
    jsonResponse(['success' => false, 'message' => 'Terjadi kesalahan sistem saat memperbarui status.'], 500);
}
