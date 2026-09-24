<?php
/**
 * BDA Shooting Championship 2026 — Scores Presisi 20M API
 * Ring-Based Scoring: X | 10 | 9 | 8 | 7 | 6 | 5 | 4 | 3 | 2 | 1 | jumlah_masuk | nilai
 * X is tiebreaker only, NOT in jumlah_masuk or nilai.
 */
require_once __DIR__ . '/../includes/db.php';
cors();

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    requireAdmin();

    $db = getDB();

    // Auto-migrate nilai to DECIMAL(6,1) if needed
    try {
        $db->exec("ALTER TABLE scores_presisi MODIFY COLUMN nilai DECIMAL(6,1) NOT NULL DEFAULT 0.0");
    } catch (Exception $e) {}

    // Auto-populate verified Presisi participants who are not in scores_presisi yet
    try {
        $db->exec("
            INSERT INTO scores_presisi (registration_id, no_peserta, nama, satuan, ring_x, ring_10, ring_9, ring_8, ring_7, ring_6, ring_5, ring_4, ring_3, ring_2, ring_1, jumlah_masuk, nilai, updated_at)
            SELECT r.registration_id, r.no_peserta, r.nama, r.satuan, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0.0, NOW()
            FROM registrations r
            WHERE (LOWER(r.status) = 'verified' OR r.status = 'Verified')
              AND (LOWER(r.kategori) LIKE '%presisi%' OR LOWER(r.kategori) LIKE '%keduanya%')
              AND r.registration_id NOT IN (SELECT registration_id FROM scores_presisi)
        ");
    } catch (Exception $e) {}

    try {
        $stmt = $db->query('
            SELECT sp.*, r.kategori 
            FROM scores_presisi sp
            LEFT JOIN registrations r ON sp.registration_id = r.registration_id
            ORDER BY sp.nilai DESC, sp.ring_x DESC, sp.jumlah_masuk DESC
        ');
        $scores = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Add ranking
        $rank = 1;
        foreach ($scores as &$score) {
            $score['rank'] = $rank++;
            $score['ring_x'] = (int)($score['ring_x'] ?? 0);
            for ($r = 1; $r <= 10; $r++) {
                $score['ring_' . $r] = (int)($score['ring_' . $r] ?? 0);
            }
            $score['jumlah_masuk'] = (int)($score['jumlah_masuk'] ?? 0);
            $score['nilai'] = round((float)($score['nilai'] ?? 0), 1);
        }
        unset($score);

        jsonResponse([
            'success' => true,
            'data' => $scores,
            'total' => count($scores)
        ]);

    } catch (PDOException $e) {
        jsonResponse(['success' => false, 'message' => 'Gagal mengambil data skor: ' . $e->getMessage()], 500);
    }

} elseif ($method === 'POST') {
    requireAdmin();

    $input = json_decode(file_get_contents('php://input'), true);

    if (!$input) {
        jsonResponse(['success' => false, 'message' => 'Invalid JSON body'], 400);
    }

    // Validate required fields
    $registrationId = trim($input['registration_id'] ?? '');
    if (empty($registrationId)) {
        jsonResponse(['success' => false, 'message' => 'registration_id wajib diisi'], 400);
    }

    $db = getDB();

    // Auto-migrate nilai to DECIMAL(6,1) if needed
    try {
        $db->exec("ALTER TABLE scores_presisi MODIFY COLUMN nilai DECIMAL(6,1) NOT NULL DEFAULT 0.0");
    } catch (Exception $e) {}

    // Verify participant: search registrations, fallback to existing scores_presisi, fallback to input
    $stmt = $db->prepare('SELECT registration_id, no_peserta, nama, satuan FROM registrations WHERE registration_id = ?');
    $stmt->execute([$registrationId]);
    $participant = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$participant) {
        $stmt = $db->prepare('SELECT registration_id, no_peserta, nama, satuan FROM scores_presisi WHERE registration_id = ?');
        $stmt->execute([$registrationId]);
        $participant = $stmt->fetch(PDO::FETCH_ASSOC);
    }

    if (!$participant) {
        $participant = [
            'registration_id' => $registrationId,
            'no_peserta' => $input['no_peserta'] ?? '',
            'nama' => $input['nama'] ?? 'Peserta',
            'satuan' => $input['satuan'] ?? ''
        ];
    } else {
        // Override with any non-empty input values if available
        if (!empty($input['no_peserta'])) $participant['no_peserta'] = $input['no_peserta'];
        if (!empty($input['nama'])) $participant['nama'] = $input['nama'];
        if (!empty($input['satuan'])) $participant['satuan'] = $input['satuan'];
    }

    // Extract Ring values (unfilled inputs safely default to 0)
    $ringX = max(0, (int)($input['ring_x'] ?? 0));
    $ringScores = [];
    $jumlahMasuk = 0;
    $nilaiRing = 0;

    for ($r = 10; $r >= 1; $r--) {
        $val = max(0, (int)($input['ring_' . $r] ?? 0));
        $ringScores['ring_' . $r] = $val;
        $jumlahMasuk += $val;
        $nilaiRing += ($r * $val);
    }

    // Nilai X merupakan nilai 0,1. Nilai X tidak menambah jumlah_masuk.
    $nilai = round($nilaiRing + ($ringX * 0.1), 1);

    try {
        // Check if score already exists for this participant
        $stmt = $db->prepare('SELECT id FROM scores_presisi WHERE registration_id = ?');
        $stmt->execute([$registrationId]);
        $existing = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existing) {
            // Update existing score
            $stmt = $db->prepare('
                UPDATE scores_presisi SET
                    no_peserta = ?, nama = ?, satuan = ?,
                    ring_x = ?,
                    ring_10 = ?, ring_9 = ?, ring_8 = ?, ring_7 = ?, ring_6 = ?,
                    ring_5 = ?, ring_4 = ?, ring_3 = ?, ring_2 = ?, ring_1 = ?,
                    jumlah_masuk = ?, nilai = ?, updated_at = NOW()
                WHERE registration_id = ?
            ');
            $stmt->execute([
                $participant['no_peserta'], $participant['nama'], $participant['satuan'],
                $ringX,
                $ringScores['ring_10'], $ringScores['ring_9'], $ringScores['ring_8'], $ringScores['ring_7'], $ringScores['ring_6'],
                $ringScores['ring_5'], $ringScores['ring_4'], $ringScores['ring_3'], $ringScores['ring_2'], $ringScores['ring_1'],
                $jumlahMasuk, $nilai,
                $registrationId
            ]);

            $message = 'Skor presisi ring berhasil diperbarui';
        } else {
            // Insert new score
            $stmt = $db->prepare('
                INSERT INTO scores_presisi 
                (registration_id, no_peserta, nama, satuan,
                 ring_x, ring_10, ring_9, ring_8, ring_7, ring_6,
                 ring_5, ring_4, ring_3, ring_2, ring_1,
                 jumlah_masuk, nilai, updated_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
            ');
            $stmt->execute([
                $registrationId, $participant['no_peserta'], $participant['nama'], $participant['satuan'],
                $ringX,
                $ringScores['ring_10'], $ringScores['ring_9'], $ringScores['ring_8'], $ringScores['ring_7'], $ringScores['ring_6'],
                $ringScores['ring_5'], $ringScores['ring_4'], $ringScores['ring_3'], $ringScores['ring_2'], $ringScores['ring_1'],
                $jumlahMasuk, $nilai
            ]);

            $message = 'Skor presisi ring berhasil disimpan';
        }

        jsonResponse([
            'success' => true,
            'message' => $message,
            'data' => [
                'registration_id' => $registrationId,
                'no_peserta' => $participant['no_peserta'],
                'nama' => $participant['nama'],
                'ring_x' => $ringX,
                'jumlah_masuk' => $jumlahMasuk,
                'nilai' => $nilai
            ]
        ]);

    } catch (PDOException $e) {
        jsonResponse(['success' => false, 'message' => 'Gagal menyimpan skor: ' . $e->getMessage()], 500);
    }

} else {
    jsonResponse(['success' => false, 'message' => 'Method not allowed'], 405);
}
