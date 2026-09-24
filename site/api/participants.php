<?php
/**
 * BDA Shooting Championship 2026 — Participant Management API
 * Direct participant creation, editing, status update, and deletion.
 */
session_start();
require_once __DIR__ . '/../includes/db.php';
cors();

// Authenticate via token or active admin session
$token = $_SERVER['HTTP_X_ADMIN_TOKEN'] ?? $_GET['token'] ?? '';
$isSessionAdmin = isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;

if ($token !== ADMIN_TOKEN && !$isSessionAdmin) {
    jsonResponse(['success' => false, 'error' => 'Unauthorized'], 401);
}

$method = $_SERVER['REQUEST_METHOD'];
$db = getDB();

if ($method === 'GET') {
    // List participants (can filter by status or search)
    $status = $_GET['status'] ?? 'all';
    $search = trim($_GET['q'] ?? '');
    
    $sql = 'SELECT * FROM registrations';
    $params = [];
    $where = [];
    
    if ($status !== 'all') {
        $where[] = 'LOWER(status) = LOWER(?)';
        $params[] = $status;
    }
    if ($search !== '') {
        $where[] = '(nama LIKE ? OR no_peserta LIKE ? OR nrp LIKE ? OR satuan LIKE ? OR registration_id LIKE ?)';
        $term = "%$search%";
        $params[] = $term;
        $params[] = $term;
        $params[] = $term;
        $params[] = $term;
        $params[] = $term;
    }
    if (!empty($where)) {
        $sql .= ' WHERE ' . implode(' AND ', $where);
    }
    $sql .= ' ORDER BY id DESC';
    
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($rows as &$r) {
        $r['kta_url'] = $r['kta_filename'] ? SITE_URL . '/uploads/kta/' . $r['kta_filename'] : null;
        $r['bukti_url'] = $r['bukti_filename'] ? SITE_URL . '/uploads/bukti/' . $r['bukti_filename'] : null;
    }
    unset($r);
    
    jsonResponse(['success' => true, 'data' => $rows, 'total' => count($rows)]);

} elseif ($method === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    if (!$input) {
        jsonResponse(['success' => false, 'error' => 'Invalid JSON body'], 400);
    }
    
    $action = $input['action'] ?? 'edit';
    
    if ($action === 'create') {
        // Direct addition of new participant
        $nama = trim($input['nama'] ?? '');
        $pangkat = trim($input['pangkat'] ?? '');
        $nrp = trim($input['nrp'] ?? '');
        $satuan = trim($input['satuan'] ?? '');
        $telepon = trim($input['telepon'] ?? '');
        $email = trim($input['email'] ?? '');
        $kategori = trim($input['kategori'] ?? 'presisi');
        $rawStatus = trim($input['status'] ?? 'Verified');
        $status = ucfirst(strtolower($rawStatus));
        $adminNotes = trim($input['admin_notes'] ?? '');
        $noPeserta = trim($input['no_peserta'] ?? '');
        
        if (empty($nama)) {
            jsonResponse(['success' => false, 'error' => 'Nama lengkap wajib diisi'], 400);
        }
        
        // Generate unique registration_id: REG-26-XXXXX
        $uniqueId = 'REG-' . date('y') . '-' . strtoupper(substr(md5(uniqid((string)mt_rand(), true)), 0, 5));
        
        // If status is Verified and no_peserta is empty, auto-generate BSC-26xxx
        if ($status === 'Verified' && empty($noPeserta)) {
            $stmt = $db->query("SELECT no_peserta FROM registrations WHERE no_peserta LIKE 'BSC-26%'");
            $allPeserta = $stmt ? $stmt->fetchAll(PDO::FETCH_COLUMN) : [];
            $maxNumber = 0;
            foreach ($allPeserta as $p) {
                if (preg_match('/BSC-26(\d+)/i', $p, $matches)) {
                    $num = (int)$matches[1];
                    if ($num > $maxNumber) $maxNumber = $num;
                }
            }
            $noPeserta = 'BSC-26' . str_pad($maxNumber + 1, 3, '0', STR_PAD_LEFT);
        }
        
        $stmt = $db->prepare('
            INSERT INTO registrations (
                registration_id, nama, email, telepon, pangkat, nrp, satuan,
                kategori, status, no_peserta, admin_notes, created_at, updated_at
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
        ');
        $stmt->execute([
            $uniqueId, $nama, $email, $telepon, $pangkat, $nrp, $satuan,
            $kategori, $status, $noPeserta, $adminNotes
        ]);
        
        // If presisi, sync into scores_presisi
        if ($status === 'Verified' && (stripos($kategori, 'presisi') !== false || stripos($kategori, 'keduanya') !== false)) {
            try {
                $chk = $db->prepare('SELECT id FROM scores_presisi WHERE registration_id = ?');
                $chk->execute([$uniqueId]);
                if (!$chk->fetch()) {
                    $insScore = $db->prepare('INSERT INTO scores_presisi (registration_id, no_peserta, nama, satuan) VALUES (?, ?, ?, ?)');
                    $insScore->execute([$uniqueId, $noPeserta, $nama, $satuan]);
                }
            } catch (Exception $e) {}
        }
        
        // Return full new record
        $stmt = $db->prepare('SELECT * FROM registrations WHERE registration_id = ?');
        $stmt->execute([$uniqueId]);
        $newReg = $stmt->fetch(PDO::FETCH_ASSOC);
        
        jsonResponse([
            'success' => true,
            'message' => 'Peserta baru berhasil ditambahkan' . ($noPeserta ? " dengan No. Peserta $noPeserta" : ''),
            'data' => $newReg
        ]);
        
    } elseif ($action === 'edit') {
        // Direct edit of all participant details
        $regId = trim($input['registration_id'] ?? '');
        $id = $input['id'] ?? null;
        
        if (empty($regId) && empty($id)) {
            jsonResponse(['success' => false, 'error' => 'registration_id atau id wajib diisi'], 400);
        }
        
        // Fetch current
        if (!empty($regId)) {
            $stmt = $db->prepare('SELECT * FROM registrations WHERE registration_id = ?');
            $stmt->execute([$regId]);
        } else {
            $stmt = $db->prepare('SELECT * FROM registrations WHERE id = ?');
            $stmt->execute([$id]);
        }
        $existing = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$existing) {
            jsonResponse(['success' => false, 'error' => 'Peserta tidak ditemukan'], 404);
        }
        
        $regId = $existing['registration_id'];
        $nama = isset($input['nama']) ? trim($input['nama']) : $existing['nama'];
        $email = isset($input['email']) ? trim($input['email']) : $existing['email'];
        $telepon = isset($input['telepon']) ? trim($input['telepon']) : $existing['telepon'];
        $pangkat = isset($input['pangkat']) ? trim($input['pangkat']) : $existing['pangkat'];
        $nrp = isset($input['nrp']) ? trim($input['nrp']) : $existing['nrp'];
        $satuan = isset($input['satuan']) ? trim($input['satuan']) : $existing['satuan'];
        $kategori = isset($input['kategori']) ? trim($input['kategori']) : $existing['kategori'];
        $rawStatus = isset($input['status']) ? trim($input['status']) : $existing['status'];
        $status = ucfirst(strtolower($rawStatus));
        $adminNotes = isset($input['admin_notes']) ? trim($input['admin_notes']) : $existing['admin_notes'];
        $noPeserta = isset($input['no_peserta']) ? trim($input['no_peserta']) : $existing['no_peserta'];
        
        // If status becomes Verified and no_peserta is empty, auto-generate BSC-26xxx
        if ($status === 'Verified' && empty($noPeserta)) {
            $stmt = $db->query("SELECT no_peserta FROM registrations WHERE no_peserta LIKE 'BSC-26%'");
            $allPeserta = $stmt ? $stmt->fetchAll(PDO::FETCH_COLUMN) : [];
            $maxNumber = 0;
            foreach ($allPeserta as $p) {
                if (preg_match('/BSC-26(\d+)/i', $p, $matches)) {
                    $num = (int)$matches[1];
                    if ($num > $maxNumber) $maxNumber = $num;
                }
            }
            $noPeserta = 'BSC-26' . str_pad($maxNumber + 1, 3, '0', STR_PAD_LEFT);
        }
        
        $upd = $db->prepare('
            UPDATE registrations SET
                nama = ?, email = ?, telepon = ?, pangkat = ?, nrp = ?, satuan = ?,
                kategori = ?, status = ?, no_peserta = ?, admin_notes = ?, updated_at = NOW()
            WHERE registration_id = ?
        ');
        $upd->execute([
            $nama, $email, $telepon, $pangkat, $nrp, $satuan,
            $kategori, $status, $noPeserta, $adminNotes, $regId
        ]);
        
        // Also update scores_presisi if exists so name, satuan, no_peserta match
        try {
            $updScore = $db->prepare('UPDATE scores_presisi SET nama = ?, satuan = ?, no_peserta = ? WHERE registration_id = ?');
            $updScore->execute([$nama, $satuan, $noPeserta, $regId]);
        } catch (Exception $e) {}
        
        // Return updated record
        $stmt = $db->prepare('SELECT * FROM registrations WHERE registration_id = ?');
        $stmt->execute([$regId]);
        $updated = $stmt->fetch(PDO::FETCH_ASSOC);
        
        jsonResponse([
            'success' => true,
            'message' => 'Data peserta ' . htmlspecialchars($nama) . ' berhasil diperbarui',
            'data' => $updated
        ]);
        
    } elseif ($action === 'delete') {
        $regId = trim($input['registration_id'] ?? '');
        if (empty($regId)) {
            jsonResponse(['success' => false, 'error' => 'registration_id wajib diisi'], 400);
        }
        
        $del = $db->prepare('DELETE FROM registrations WHERE registration_id = ?');
        $del->execute([$regId]);
        
        try {
            $del2 = $db->prepare('DELETE FROM scores_presisi WHERE registration_id = ?');
            $del2->execute([$regId]);
        } catch (Exception $e) {}
        
        jsonResponse(['success' => true, 'message' => 'Peserta berhasil dihapus']);
    } else {
        jsonResponse(['success' => false, 'error' => 'Action tidak dikenali'], 400);
    }
} else {
    jsonResponse(['success' => false, 'error' => 'Method not allowed'], 405);
}
