<?php
require_once __DIR__ . '/../includes/db.php';
cors();

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    requireAdmin();

    $db = getDB();

    try {
        $stmt = $db->query('
            SELECT sp.*, r.kategori 
            FROM scores_presisi sp
            LEFT JOIN registrations r ON sp.registration_id = r.registration_id
            ORDER BY sp.total_score DESC, sp.x_count DESC
        ');
        $scores = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Add ranking
        $rank = 1;
        foreach ($scores as &$score) {
            $score['rank'] = $rank++;
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
    $registrationId = $input['registration_id'] ?? '';
    if (empty($registrationId)) {
        jsonResponse(['success' => false, 'message' => 'registration_id wajib diisi'], 400);
    }

    $db = getDB();

    // Verify participant exists and is verified
    $stmt = $db->prepare('SELECT registration_id, no_peserta, nama, satuan FROM registrations WHERE registration_id = ? AND status = ?');
    $stmt->execute([$registrationId, 'Verified']);
    $participant = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$participant) {
        jsonResponse(['success' => false, 'message' => 'Peserta tidak ditemukan atau belum diverifikasi'], 404);
    }

    // Extract seri scores and calculate total
    $seriScores = [];
    $totalScore = 0;
    for ($i = 1; $i <= 10; $i++) {
        $key = 'seri_' . $i;
        $val = isset($input[$key]) ? (int) $input[$key] : 0;
        $seriScores[$key] = $val;
        $totalScore += $val;
    }

    $xCount = isset($input['x_count']) ? (int) $input['x_count'] : 0;

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
                    seri_1 = ?, seri_2 = ?, seri_3 = ?, seri_4 = ?, seri_5 = ?,
                    seri_6 = ?, seri_7 = ?, seri_8 = ?, seri_9 = ?, seri_10 = ?,
                    x_count = ?, total_score = ?, updated_at = NOW()
                WHERE registration_id = ?
            ');
            $stmt->execute([
                $participant['no_peserta'], $participant['nama'], $participant['satuan'],
                $seriScores['seri_1'], $seriScores['seri_2'], $seriScores['seri_3'],
                $seriScores['seri_4'], $seriScores['seri_5'], $seriScores['seri_6'],
                $seriScores['seri_7'], $seriScores['seri_8'], $seriScores['seri_9'],
                $seriScores['seri_10'],
                $xCount, $totalScore,
                $registrationId
            ]);

            $message = 'Skor presisi berhasil diperbarui';
        } else {
            // Insert new score
            $stmt = $db->prepare('
                INSERT INTO scores_presisi 
                (registration_id, no_peserta, nama, satuan,
                 seri_1, seri_2, seri_3, seri_4, seri_5,
                 seri_6, seri_7, seri_8, seri_9, seri_10,
                 x_count, total_score, updated_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
            ');
            $stmt->execute([
                $registrationId, $participant['no_peserta'], $participant['nama'], $participant['satuan'],
                $seriScores['seri_1'], $seriScores['seri_2'], $seriScores['seri_3'],
                $seriScores['seri_4'], $seriScores['seri_5'], $seriScores['seri_6'],
                $seriScores['seri_7'], $seriScores['seri_8'], $seriScores['seri_9'],
                $seriScores['seri_10'],
                $xCount, $totalScore
            ]);

            $message = 'Skor presisi berhasil disimpan';
        }

        jsonResponse([
            'success' => true,
            'message' => $message,
            'data' => [
                'registration_id' => $registrationId,
                'no_peserta' => $participant['no_peserta'],
                'nama' => $participant['nama'],
                'total_score' => $totalScore,
                'x_count' => $xCount
            ]
        ]);

    } catch (PDOException $e) {
        jsonResponse(['success' => false, 'message' => 'Gagal menyimpan skor: ' . $e->getMessage()], 500);
    }

} else {
    jsonResponse(['success' => false, 'message' => 'Method not allowed'], 405);
}
