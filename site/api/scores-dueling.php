<?php
require_once __DIR__ . '/../includes/db.php';
cors();

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    requireAdmin();

    $db = getDB();

    try {
        $stmt = $db->query('
            SELECT * FROM dueling_matches 
            ORDER BY round_order ASC, match_number ASC
        ');
        $matches = $stmt->fetchAll(PDO::FETCH_ASSOC);

        jsonResponse([
            'success' => true,
            'data' => $matches,
            'total' => count($matches)
        ]);

    } catch (PDOException $e) {
        jsonResponse(['success' => false, 'message' => 'Gagal mengambil data dueling: ' . $e->getMessage()], 500);
    }

} elseif ($method === 'POST') {
    requireAdmin();

    $input = json_decode(file_get_contents('php://input'), true);

    if (!$input) {
        jsonResponse(['success' => false, 'message' => 'Invalid JSON body'], 400);
    }

    // Validate required fields
    $requiredFields = ['round_name', 'round_order', 'match_number'];
    $missing = [];
    foreach ($requiredFields as $field) {
        if (!isset($input[$field]) || $input[$field] === '') {
            $missing[] = $field;
        }
    }

    if (!empty($missing)) {
        jsonResponse(['success' => false, 'message' => 'Field wajib tidak lengkap: ' . implode(', ', $missing)], 400);
    }

    // Extract fields
    $id = $input['id'] ?? null;
    $roundName = trim($input['round_name']);
    $roundOrder = (int) $input['round_order'];
    $matchNumber = (int) $input['match_number'];
    $participant1Id = $input['participant_1_id'] ?? null;
    $participant1Name = $input['participant_1_name'] ?? null;
    $participant1Satuan = $input['participant_1_satuan'] ?? null;
    $participant2Id = $input['participant_2_id'] ?? null;
    $participant2Name = $input['participant_2_name'] ?? null;
    $participant2Satuan = $input['participant_2_satuan'] ?? null;
    $time1 = isset($input['time_1']) ? (float) $input['time_1'] : null;
    $time2 = isset($input['time_2']) ? (float) $input['time_2'] : null;
    $winnerId = $input['winner_id'] ?? null;
    $matchStatus = strtolower($input['match_status'] ?? 'upcoming');
    if (!in_array($matchStatus, ['upcoming', 'live', 'finished'])) {
        $matchStatus = 'upcoming';
    }

    $db = getDB();

    try {
        if ($id) {
            // Update existing match
            $stmt = $db->prepare('SELECT id FROM dueling_matches WHERE id = ?');
            $stmt->execute([$id]);
            if (!$stmt->fetch()) {
                jsonResponse(['success' => false, 'message' => 'Match tidak ditemukan'], 404);
            }

            $stmt = $db->prepare('
                UPDATE dueling_matches SET
                    round_name = ?, round_order = ?, match_number = ?,
                    participant_1_id = ?, participant_1_name = ?, participant_1_satuan = ?,
                    participant_2_id = ?, participant_2_name = ?, participant_2_satuan = ?,
                    time_1 = ?, time_2 = ?, winner_id = ?, match_status = ?,
                    updated_at = NOW()
                WHERE id = ?
            ');
            $stmt->execute([
                $roundName, $roundOrder, $matchNumber,
                $participant1Id, $participant1Name, $participant1Satuan,
                $participant2Id, $participant2Name, $participant2Satuan,
                $time1, $time2, $winnerId, $matchStatus,
                $id
            ]);

            jsonResponse([
                'success' => true,
                'message' => 'Match berhasil diperbarui',
                'data' => ['id' => (int) $id]
            ]);

        } else {
            // Insert new match
            $stmt = $db->prepare('
                INSERT INTO dueling_matches 
                (round_name, round_order, match_number,
                 participant_1_id, participant_1_name, participant_1_satuan,
                 participant_2_id, participant_2_name, participant_2_satuan,
                 time_1, time_2, winner_id, match_status, updated_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
            ');
            $stmt->execute([
                $roundName, $roundOrder, $matchNumber,
                $participant1Id, $participant1Name, $participant1Satuan,
                $participant2Id, $participant2Name, $participant2Satuan,
                $time1, $time2, $winnerId, $matchStatus
            ]);

            $newId = $db->lastInsertId();

            jsonResponse([
                'success' => true,
                'message' => 'Match berhasil dibuat',
                'data' => ['id' => (int) $newId]
            ], 201);
        }

    } catch (PDOException $e) {
        jsonResponse(['success' => false, 'message' => 'Gagal menyimpan data match: ' . $e->getMessage()], 500);
    }

} else {
    jsonResponse(['success' => false, 'message' => 'Method not allowed'], 405);
}
