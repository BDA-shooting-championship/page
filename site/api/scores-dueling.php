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

    $db = getDB();

    // Auto-migrate schema columns if needed
    try {
        $db->exec("ALTER TABLE dueling_matches MODIFY COLUMN winner_id VARCHAR(100) NULL");
    } catch (Exception $e) {}
    try {
        $db->exec("ALTER TABLE dueling_matches ADD COLUMN IF NOT EXISTS next_match_id INT NULL");
        $db->exec("ALTER TABLE dueling_matches ADD COLUMN IF NOT EXISTS next_slot TINYINT NULL");
        $db->exec("ALTER TABLE dueling_matches ADD COLUMN IF NOT EXISTS loser_next_match_id INT NULL");
        $db->exec("ALTER TABLE dueling_matches ADD COLUMN IF NOT EXISTS loser_next_slot TINYINT NULL");
    } catch (Exception $e) {}

    $action = $input['action'] ?? '';

    // ==================== ACTION: RESET BRACKET ====================
    if ($action === 'reset_bracket') {
        try {
            $db->exec('TRUNCATE TABLE dueling_matches');
            jsonResponse(['success' => true, 'message' => 'Bagan pertandingan berhasil direset']);
        } catch (PDOException $e) {
            $db->exec('DELETE FROM dueling_matches');
            jsonResponse(['success' => true, 'message' => 'Bagan pertandingan berhasil dibersihkan']);
        }
    }

    // ==================== ACTION: DELETE SINGLE MATCH ====================
    if ($action === 'delete') {
        $delId = (int)($input['id'] ?? 0);
        if ($delId > 0) {
            $stmt = $db->prepare('DELETE FROM dueling_matches WHERE id = ?');
            $stmt->execute([$delId]);
            jsonResponse(['success' => true, 'message' => 'Match berhasil dihapus']);
        } else {
            jsonResponse(['success' => false, 'message' => 'ID match tidak valid'], 400);
        }
    }

    // ==================== ACTION: GENERATE TOURNAMENT BRACKET ====================
    if ($action === 'generate_bracket') {
        $count = (int)($input['count'] ?? $input['size'] ?? 8);
        if ($count < 2) $count = 2;
        if ($count > 256) $count = 256;

        // Tentukan kapasitas bagan P (power of 2 terkecil >= count)
        $p = 1;
        while ($p < $count) {
            $p *= 2;
        }
        if ($p < 4) $p = 4;
        $size = $p;

        $byes = $size - $count; // Jumlah BYE untuk normalisasi (0 <= byes < size / 2)

        try {
            $db->beginTransaction();
            // Bersihkan bagan sebelumnya
            $db->exec('DELETE FROM dueling_matches');

            // Ambil daftar peserta verified kategori Dueling jika ada
            $regStmt = $db->query("
                SELECT registration_id, no_peserta, nama, satuan 
                FROM registrations 
                WHERE status = 'Verified' AND (kategori LIKE '%dueling%' OR kategori LIKE '%keduanya%')
                ORDER BY id ASC
            ");
            $verifiedList = $regStmt->fetchAll(PDO::FETCH_ASSOC);

            // Susun struktur babak universal untuk kapasitas $size
            $matchSpecs = [];
            $rounds = [];
            $currentM = $size / 2;
            $currentMatchNum = 1;
            $roundOrder = 1;

            while ($currentM >= 2) {
                if ($currentM === 2) {
                    $roundName = 'Semifinal';
                } elseif ($currentM === 4) {
                    $roundName = 'Perempat Final';
                } else {
                    $roundName = 'Babak ' . ($currentM * 2) . ' Besar';
                }

                $roundMatches = [];
                for ($i = 0; $i < $currentM; $i++) {
                    $roundMatches[] = $currentMatchNum;
                    $matchSpecs[$currentMatchNum] = [
                        'round_name' => $roundName,
                        'round_order' => $roundOrder,
                        'match_number' => $currentMatchNum
                    ];
                    $currentMatchNum++;
                }

                $rounds[] = [
                    'name' => $roundName,
                    'order' => $roundOrder,
                    'matches' => $roundMatches
                ];

                $roundOrder++;
                $currentM = (int)($currentM / 2);
            }

            // Tambahkan Perebutan Juara 3 dan Final
            $bronzeMatchNum = $currentMatchNum;
            $matchSpecs[$bronzeMatchNum] = [
                'round_name' => 'Perebutan Juara 3',
                'round_order' => $roundOrder,
                'match_number' => $bronzeMatchNum
            ];
            $currentMatchNum++;

            $finalMatchNum = $currentMatchNum;
            $matchSpecs[$finalMatchNum] = [
                'round_name' => 'Final',
                'round_order' => $roundOrder + 1,
                'match_number' => $finalMatchNum
            ];

            // Insert initial match rows
            $matchIdMap = []; // match_number => DB id
            $insertStmt = $db->prepare('
                INSERT INTO dueling_matches 
                (round_name, round_order, match_number, match_status, updated_at) 
                VALUES (?, ?, ?, "upcoming", NOW())
            ');

            foreach ($matchSpecs as $mNum => $spec) {
                $insertStmt->execute([$spec['round_name'], $spec['round_order'], $mNum]);
                $matchIdMap[$mNum] = (int)$db->lastInsertId();
            }

            // Setup links antar babak secara universal
            $linkStmt = $db->prepare('
                UPDATE dueling_matches SET
                    next_match_id = ?,
                    next_slot = ?,
                    loser_next_match_id = ?,
                    loser_next_slot = ?
                WHERE id = ?
            ');

            $numRounds = count($rounds);
            for ($r = 0; $r < $numRounds; $r++) {
                $curMatches = $rounds[$r]['matches'];
                $isSemifinal = ($r === $numRounds - 1);

                if ($isSemifinal) {
                    // Semifinal 1 (curMatches[0]) -> Final Slot 1, Loser -> Juara 3 Slot 1
                    $linkStmt->execute([
                        $matchIdMap[$finalMatchNum], 1,
                        $matchIdMap[$bronzeMatchNum], 1,
                        $matchIdMap[$curMatches[0]]
                    ]);
                    // Semifinal 2 (curMatches[1]) -> Final Slot 2, Loser -> Juara 3 Slot 2
                    $linkStmt->execute([
                        $matchIdMap[$finalMatchNum], 2,
                        $matchIdMap[$bronzeMatchNum], 2,
                        $matchIdMap[$curMatches[1]]
                    ]);
                } else {
                    // Round r -> Round r + 1
                    $nextMatches = $rounds[$r + 1]['matches'];
                    foreach ($curMatches as $idx => $mNum) {
                        $targetMatchIdx = (int)floor($idx / 2);
                        $targetMatchNum = $nextMatches[$targetMatchIdx];
                        $targetSlot = ($idx % 2 === 0) ? 1 : 2;
                        $linkStmt->execute([
                            $matchIdMap[$targetMatchNum], $targetSlot,
                            null, null,
                            $matchIdMap[$mNum]
                        ]);
                    }
                }
            }

            // Normalisasi Jumlah Peserta & Penempatan Peserta / BYE di Babak Pertama:
            $round1Matches = $rounds[0]['matches'];
            $round1Count = count($round1Matches);
            $byeMatches = [];
            $topByes = (int)ceil($byes / 2);
            $bottomByes = $byes - $topByes;

            for ($i = 0; $i < $topByes; $i++) {
                $byeMatches[$round1Matches[$i]] = true;
            }
            for ($i = 0; $i < $bottomByes; $i++) {
                $byeMatches[$round1Matches[$round1Count - 1 - $i]] = true;
            }

            $updateMatchStmt = $db->prepare('
                UPDATE dueling_matches SET
                    participant_1_id = ?, participant_1_name = ?, participant_1_satuan = ?,
                    participant_2_id = ?, participant_2_name = ?, participant_2_satuan = ?,
                    winner_id = ?, match_status = ?
                WHERE id = ?
            ');

            $advanceStmt = $db->prepare('
                UPDATE dueling_matches SET
                    participant_1_id = CASE WHEN ? = 1 THEN ? ELSE participant_1_id END,
                    participant_1_name = CASE WHEN ? = 1 THEN ? ELSE participant_1_name END,
                    participant_1_satuan = CASE WHEN ? = 1 THEN ? ELSE participant_1_satuan END,
                    participant_2_id = CASE WHEN ? = 2 THEN ? ELSE participant_2_id END,
                    participant_2_name = CASE WHEN ? = 2 THEN ? ELSE participant_2_name END,
                    participant_2_satuan = CASE WHEN ? = 2 THEN ? ELSE participant_2_satuan END
                WHERE id = ?
            ');

            $vIdx = 0;
            $autoAdvancements = [];

            foreach ($round1Matches as $mNum) {
                $matchId = $matchIdMap[$mNum];

                // Peserta 1
                $p1 = $verifiedList[$vIdx] ?? null;
                $p1Id = $p1 ? $p1['registration_id'] : null;
                $p1Name = $p1 ? $p1['nama'] : ('Peserta ' . ($vIdx + 1));
                $p1Sat = $p1 ? $p1['satuan'] : '';
                $vIdx++;

                // Peserta 2 atau BYE
                $isBye = isset($byeMatches[$mNum]);
                $winnerId = null;
                $status = 'upcoming';

                if ($isBye) {
                    $p2Id = null;
                    $p2Name = 'BYE (Lolos Otomatis)';
                    $p2Sat = '-';
                    // Peserta 1 otomatis menang dan lolos
                    $winnerId = $p1Name;
                    $status = 'finished';
                    $autoAdvancements[] = [
                        'from_match' => $mNum,
                        'winner_id' => $p1Id,
                        'winner_name' => $p1Name,
                        'winner_satuan' => $p1Sat
                    ];
                } else {
                    $p2 = $verifiedList[$vIdx] ?? null;
                    $p2Id = $p2 ? $p2['registration_id'] : null;
                    $p2Name = $p2 ? $p2['nama'] : ('Peserta ' . ($vIdx + 1));
                    $p2Sat = $p2 ? $p2['satuan'] : '';
                    $vIdx++;
                }

                $updateMatchStmt->execute([
                    $p1Id, $p1Name, $p1Sat,
                    $p2Id, $p2Name, $p2Sat,
                    $winnerId, $status,
                    $matchId
                ]);
            }

            // Eksekusi auto-advancement untuk peserta yang mendapat BYE
            foreach ($autoAdvancements as $adv) {
                $q = $db->prepare('SELECT next_match_id, next_slot FROM dueling_matches WHERE id = ?');
                $q->execute([$matchIdMap[$adv['from_match']]]);
                $link = $q->fetch(PDO::FETCH_ASSOC);
                if ($link && !empty($link['next_match_id']) && !empty($link['next_slot'])) {
                    $advanceStmt->execute([
                        $link['next_slot'], $adv['winner_id'],
                        $link['next_slot'], $adv['winner_name'],
                        $link['next_slot'], $adv['winner_satuan'],
                        $link['next_slot'], $adv['winner_id'],
                        $link['next_slot'], $adv['winner_name'],
                        $link['next_slot'], $adv['winner_satuan'],
                        $link['next_match_id']
                    ]);
                }
            }

            $db->commit();

            $byeMsg = $byes > 0 ? " dengan normalisasi $byes slot BYE otomatis" : "";
            jsonResponse([
                'success' => true,
                'message' => "Bagan eliminasi untuk $count peserta berhasil dibuat (Kapasitas $size$byeMsg)!",
                'total_matches' => count($matchSpecs),
                'count' => $count,
                'size' => $size,
                'byes' => $byes
            ]);

        } catch (PDOException $e) {
            $db->rollBack();
            jsonResponse(['success' => false, 'message' => 'Gagal membuat bagan: ' . $e->getMessage()], 500);
        }
    }

    // ==================== DEFAULT: INSERT OR UPDATE SINGLE MATCH ====================
    $id = isset($input['id']) && (int)$input['id'] > 0 ? (int)$input['id'] : null;
    $roundName = trim($input['round_name'] ?? 'Penyisihan');
    if (empty($roundName)) $roundName = 'Penyisihan';

    $roundOrderMap = [
        'Babak 256 Besar' => 1,
        'Babak 128 Besar' => 2,
        'Babak 64 Besar' => 3,
        'Babak 32 Besar' => 4,
        'Babak 16 Besar' => 5,
        'Perempat Final' => 6,
        'Semifinal' => 7,
        'Perebutan Juara 3' => 8,
        'Final' => 9,
        'Penyisihan' => 1
    ];
    $roundOrder = isset($input['round_order']) && $input['round_order'] !== '' 
        ? (int)$input['round_order'] 
        : ($roundOrderMap[$roundName] ?? 1);

    $matchNumber = isset($input['match_number']) && $input['match_number'] !== '' 
        ? (int)$input['match_number'] 
        : 1;

    $participant1Id = $input['participant_1_id'] ?? null;
    $participant1Name = trim($input['participant_1_name'] ?? '');
    $participant1Satuan = $input['participant_1_satuan'] ?? null;

    $participant2Id = $input['participant_2_id'] ?? null;
    $participant2Name = trim($input['participant_2_name'] ?? '');
    $participant2Satuan = $input['participant_2_satuan'] ?? null;

    $time1 = isset($input['time_1']) && $input['time_1'] !== '' ? (float) $input['time_1'] : null;
    $time2 = isset($input['time_2']) && $input['time_2'] !== '' ? (float) $input['time_2'] : null;

    $winnerId = !empty($input['winner_id']) ? trim($input['winner_id']) : null;
    $matchStatus = strtolower($input['match_status'] ?? 'upcoming');
    if (!in_array($matchStatus, ['upcoming', 'live', 'finished'])) {
        $matchStatus = 'upcoming';
    }

    $nextMatchId = isset($input['next_match_id']) && (int)$input['next_match_id'] > 0 ? (int)$input['next_match_id'] : null;
    $nextSlot = isset($input['next_slot']) && (int)$input['next_slot'] > 0 ? (int)$input['next_slot'] : null;
    $loserNextMatchId = isset($input['loser_next_match_id']) && (int)$input['loser_next_match_id'] > 0 ? (int)$input['loser_next_match_id'] : null;
    $loserNextSlot = isset($input['loser_next_slot']) && (int)$input['loser_next_slot'] > 0 ? (int)$input['loser_next_slot'] : null;

    try {
        if ($id) {
            // Fetch current state to preserve linkages if not in payload
            $fetchStmt = $db->prepare('SELECT * FROM dueling_matches WHERE id = ?');
            $fetchStmt->execute([$id]);
            $currentMatch = $fetchStmt->fetch(PDO::FETCH_ASSOC);

            if (!$currentMatch) {
                jsonResponse(['success' => false, 'message' => 'Match tidak ditemukan'], 404);
            }

            if ($nextMatchId === null && !empty($currentMatch['next_match_id'])) {
                $nextMatchId = (int)$currentMatch['next_match_id'];
            }
            if ($nextSlot === null && !empty($currentMatch['next_slot'])) {
                $nextSlot = (int)$currentMatch['next_slot'];
            }
            if ($loserNextMatchId === null && !empty($currentMatch['loser_next_match_id'])) {
                $loserNextMatchId = (int)$currentMatch['loser_next_match_id'];
            }
            if ($loserNextSlot === null && !empty($currentMatch['loser_next_slot'])) {
                $loserNextSlot = (int)$currentMatch['loser_next_slot'];
            }

            $stmt = $db->prepare('
                UPDATE dueling_matches SET
                    round_name = ?, round_order = ?, match_number = ?,
                    participant_1_id = ?, participant_1_name = ?, participant_1_satuan = ?,
                    participant_2_id = ?, participant_2_name = ?, participant_2_satuan = ?,
                    time_1 = ?, time_2 = ?, winner_id = ?, match_status = ?,
                    next_match_id = ?, next_slot = ?,
                    loser_next_match_id = ?, loser_next_slot = ?,
                    updated_at = NOW()
                WHERE id = ?
            ');
            $stmt->execute([
                $roundName, $roundOrder, $matchNumber,
                $participant1Id, $participant1Name, $participant1Satuan,
                $participant2Id, $participant2Name, $participant2Satuan,
                $time1, $time2, $winnerId, $matchStatus,
                $nextMatchId, $nextSlot,
                $loserNextMatchId, $loserNextSlot,
                $id
            ]);

            $targetId = $id;

        } else {
            // Insert new match
            $stmt = $db->prepare('
                INSERT INTO dueling_matches 
                (round_name, round_order, match_number,
                 participant_1_id, participant_1_name, participant_1_satuan,
                 participant_2_id, participant_2_name, participant_2_satuan,
                 time_1, time_2, winner_id, match_status,
                 next_match_id, next_slot, loser_next_match_id, loser_next_slot,
                 updated_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
            ');
            $stmt->execute([
                $roundName, $roundOrder, $matchNumber,
                $participant1Id, $participant1Name, $participant1Satuan,
                $participant2Id, $participant2Name, $participant2Satuan,
                $time1, $time2, $winnerId, $matchStatus,
                $nextMatchId, $nextSlot, $loserNextMatchId, $loserNextSlot
            ]);

            $targetId = (int)$db->lastInsertId();
        }

        // ==================== AUTOMATIC BRACKET ADVANCEMENT ====================
        // When a winner is selected, automatically update next round slot
        $winnerData = null;
        $loserData = null;

        if (!empty($winnerId)) {
            // Check if winner matches participant 1
            if ($winnerId === $participant1Id || $winnerId === $participant1Name || $winnerId === 'p1') {
                $winnerData = [
                    'id' => $participant1Id,
                    'name' => $participant1Name,
                    'satuan' => $participant1Satuan
                ];
                $loserData = [
                    'id' => $participant2Id,
                    'name' => $participant2Name,
                    'satuan' => $participant2Satuan
                ];
            } 
            // Check if winner matches participant 2
            elseif ($winnerId === $participant2Id || $winnerId === $participant2Name || $winnerId === 'p2') {
                $winnerData = [
                    'id' => $participant2Id,
                    'name' => $participant2Name,
                    'satuan' => $participant2Satuan
                ];
                $loserData = [
                    'id' => $participant1Id,
                    'name' => $participant1Name,
                    'satuan' => $participant1Satuan
                ];
            }
        }

        // 1. Advance winner to next_match_id at next_slot
        if ($nextMatchId && $nextSlot) {
            $nextName = $winnerData ? $winnerData['name'] : null;
            $nextId = $winnerData ? $winnerData['id'] : null;
            $nextSatuan = $winnerData ? $winnerData['satuan'] : null;

            if ($nextSlot === 1) {
                $advStmt = $db->prepare('
                    UPDATE dueling_matches 
                    SET participant_1_id = ?, participant_1_name = ?, participant_1_satuan = ?, updated_at = NOW() 
                    WHERE id = ?
                ');
                $advStmt->execute([$nextId, $nextName, $nextSatuan, $nextMatchId]);
            } elseif ($nextSlot === 2) {
                $advStmt = $db->prepare('
                    UPDATE dueling_matches 
                    SET participant_2_id = ?, participant_2_name = ?, participant_2_satuan = ?, updated_at = NOW() 
                    WHERE id = ?
                ');
                $advStmt->execute([$nextId, $nextName, $nextSatuan, $nextMatchId]);
            }
        }

        // 2. Advance semifinal loser to loser_next_match_id at loser_next_slot (Perebutan Juara 3)
        if ($loserNextMatchId && $loserNextSlot) {
            $loserName = $loserData ? $loserData['name'] : null;
            $loserId = $loserData ? $loserData['id'] : null;
            $loserSatuan = $loserData ? $loserData['satuan'] : null;

            if ($loserNextSlot === 1) {
                $advStmt = $db->prepare('
                    UPDATE dueling_matches 
                    SET participant_1_id = ?, participant_1_name = ?, participant_1_satuan = ?, updated_at = NOW() 
                    WHERE id = ?
                ');
                $advStmt->execute([$loserId, $loserName, $loserSatuan, $loserNextMatchId]);
            } elseif ($loserNextSlot === 2) {
                $advStmt = $db->prepare('
                    UPDATE dueling_matches 
                    SET participant_2_id = ?, participant_2_name = ?, participant_2_satuan = ?, updated_at = NOW() 
                    WHERE id = ?
                ');
                $advStmt->execute([$loserId, $loserName, $loserSatuan, $loserNextMatchId]);
            }
        }

        jsonResponse([
            'success' => true,
            'message' => 'Data match berhasil disimpan & bagan diperbarui!',
            'data' => [
                'id' => $targetId,
                'winner' => $winnerData ? $winnerData['name'] : null,
                'advanced' => (bool)$winnerData
            ]
        ]);

    } catch (PDOException $e) {
        jsonResponse(['success' => false, 'message' => 'Gagal menyimpan data match: ' . $e->getMessage()], 500);
    }

} else {
    jsonResponse(['success' => false, 'message' => 'Method not allowed'], 405);
}
