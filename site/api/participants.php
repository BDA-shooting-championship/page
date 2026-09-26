<?php
/**
 * BDA Shooting Championship 2026 — Participant Management API
 * Direct participant creation, editing, status update, and deletion.
 */
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
cors();

// Authenticate via token or active admin session with 'peserta' or 'antrean' permission
$token = $_SERVER['HTTP_X_ADMIN_TOKEN'] ?? $_GET['token'] ?? '';
$isSessionAdmin = isAdminLoggedIn();

if ($token !== ADMIN_TOKEN) {
    if (!$isSessionAdmin) {
        jsonResponse(['success' => false, 'error' => 'Unauthorized: Sesi admin tidak valid.'], 401);
    }
    if (!hasPermission('peserta') && !hasPermission('antrean')) {
        jsonResponse(['success' => false, 'error' => 'Akses ditolak: Akun Anda tidak memiliki izin untuk mengelola peserta.'], 403);
    }
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
    
    try {
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($rows as &$r) {
            $r['kta_url'] = $r['kta_filename'] ? SITE_URL . '/uploads/kta/' . $r['kta_filename'] : null;
            $r['bukti_url'] = $r['bukti_filename'] ? SITE_URL . '/uploads/bukti/' . $r['bukti_filename'] : null;
        }
        unset($r);
        
        jsonResponse(['success' => true, 'data' => $rows, 'total' => count($rows)]);
    } catch (PDOException $e) {
        error_log('Gagal mengambil daftar peserta: ' . $e->getMessage());
        jsonResponse(['success' => false, 'error' => 'Terjadi kesalahan sistem saat mengambil data peserta.'], 500);
    }

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
        
        // If status is Verified and no_peserta is empty, auto-generate atomic BSC-26xxx
        if ($status === 'Verified' && empty($noPeserta)) {
            $stmt = $db->query("SELECT COALESCE(MAX(CAST(SUBSTRING(no_peserta, 7) AS UNSIGNED)), 0) + 1 FROM registrations WHERE no_peserta LIKE 'BSC-26%'");
            $nextNum = (int)$stmt->fetchColumn();
            $noPeserta = 'BSC-26' . str_pad($nextNum, 3, '0', STR_PAD_LEFT);
        } elseif ($status === 'Rejected') {
            $noPeserta = null;
        }
        
        try {
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
            
            // If presisi and verified, sync into scores_presisi
            if ($status === 'Verified' && (stripos($kategori, 'presisi') !== false || stripos($kategori, 'keduanya') !== false)) {
                $chk = $db->prepare('SELECT id FROM scores_presisi WHERE registration_id = ?');
                $chk->execute([$uniqueId]);
                if (!$chk->fetch()) {
                    $insScore = $db->prepare('INSERT INTO scores_presisi (registration_id, no_peserta, nama, satuan) VALUES (?, ?, ?, ?)');
                    $insScore->execute([$uniqueId, $noPeserta, $nama, $satuan]);
                }
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
        } catch (PDOException $e) {
            error_log('Gagal menambah peserta: ' . $e->getMessage());
            jsonResponse(['success' => false, 'error' => 'Terjadi kesalahan sistem saat menambahkan peserta.'], 500);
        }
        
    } elseif ($action === 'edit') {
        // Direct edit of participant details
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
        
        // If status becomes Verified and no_peserta is empty, auto-generate atomic BSC-26xxx
        if ($status === 'Verified' && empty($noPeserta)) {
            $stmt = $db->query("SELECT COALESCE(MAX(CAST(SUBSTRING(no_peserta, 7) AS UNSIGNED)), 0) + 1 FROM registrations WHERE no_peserta LIKE 'BSC-26%'");
            $nextNum = (int)$stmt->fetchColumn();
            $noPeserta = 'BSC-26' . str_pad($nextNum, 3, '0', STR_PAD_LEFT);
        } elseif ($status === 'Rejected') {
            // Clear no_peserta and remove from scores_presisi if rejected
            $noPeserta = null;
            try {
                $db->prepare('DELETE FROM scores_presisi WHERE registration_id = ?')->execute([$regId]);
            } catch (Exception $e) {}
        }
        
        try {
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
            
            // Sync with scores_presisi if exists or if newly verified
            if ($status === 'Verified') {
                $chk = $db->prepare('SELECT id FROM scores_presisi WHERE registration_id = ?');
                $chk->execute([$regId]);
                if ($chk->fetch()) {
                    $updScore = $db->prepare('UPDATE scores_presisi SET nama = ?, satuan = ?, no_peserta = ? WHERE registration_id = ?');
                    $updScore->execute([$nama, $satuan, $noPeserta, $regId]);
                } elseif (stripos($kategori, 'presisi') !== false || stripos($kategori, 'keduanya') !== false) {
                    $insScore = $db->prepare('INSERT INTO scores_presisi (registration_id, no_peserta, nama, satuan) VALUES (?, ?, ?, ?)');
                    $insScore->execute([$regId, $noPeserta, $nama, $satuan]);
                }
            }
            
            // Return updated record
            $stmt = $db->prepare('SELECT * FROM registrations WHERE registration_id = ?');
            $stmt->execute([$regId]);
            $updated = $stmt->fetch(PDO::FETCH_ASSOC);
            
            jsonResponse([
                'success' => true,
                'message' => 'Data peserta ' . htmlspecialchars($nama) . ' berhasil diperbarui',
                'data' => $updated
            ]);
        } catch (PDOException $e) {
            error_log('Gagal memperbarui peserta: ' . $e->getMessage());
            jsonResponse(['success' => false, 'error' => 'Terjadi kesalahan sistem saat memperbarui peserta.'], 500);
        }
        
    } elseif ($action === 'delete') {
        $regId = trim($input['registration_id'] ?? '');
        if (empty($regId)) {
            jsonResponse(['success' => false, 'error' => 'registration_id wajib diisi'], 400);
        }
        
        try {
            // Fetch file names to unlink
            $fStmt = $db->prepare('SELECT kta_filename, bukti_filename FROM registrations WHERE registration_id = ?');
            $fStmt->execute([$regId]);
            $existingFiles = $fStmt->fetch(PDO::FETCH_ASSOC);

            // Delete from registrations
            $del = $db->prepare('DELETE FROM registrations WHERE registration_id = ?');
            $del->execute([$regId]);
            
            // Delete from scores_presisi
            $del2 = $db->prepare('DELETE FROM scores_presisi WHERE registration_id = ?');
            $del2->execute([$regId]);

            // Nullify participant in dueling_matches
            $db->prepare("UPDATE dueling_matches SET participant_1_id = NULL, participant_1_name = 'TBD' WHERE participant_1_id = ?")->execute([$regId]);
            $db->prepare("UPDATE dueling_matches SET participant_2_id = NULL, participant_2_name = 'TBD' WHERE participant_2_id = ?")->execute([$regId]);

            // Unlink upload files
            if ($existingFiles) {
                if (!empty($existingFiles['kta_filename'])) {
                    @unlink(UPLOAD_DIR . '/kta/' . $existingFiles['kta_filename']);
                }
                if (!empty($existingFiles['bukti_filename'])) {
                    @unlink(UPLOAD_DIR . '/bukti/' . $existingFiles['bukti_filename']);
                }
            }
            
            jsonResponse(['success' => true, 'message' => 'Peserta berhasil dihapus']);
        } catch (PDOException $e) {
            error_log('Gagal menghapus peserta: ' . $e->getMessage());
            jsonResponse(['success' => false, 'error' => 'Terjadi kesalahan sistem saat menghapus peserta.'], 500);
        }
    } else {
        jsonResponse(['success' => false, 'error' => 'Action tidak dikenali'], 400);
    }
} else {
    jsonResponse(['success' => false, 'error' => 'Method not allowed'], 405);
}
