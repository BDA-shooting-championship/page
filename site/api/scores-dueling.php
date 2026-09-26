<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
cors();

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    requireAdmin();
    if (isAdminLoggedIn() && !hasPermission('scores')) {
        jsonResponse(['success' => false, 'error' => 'Akses ditolak: Akun Anda tidak memiliki izin untuk mengelola skor.'], 403);
    }

    $db = getDB();
    $category = $_GET['category'] ?? 'all';

    try {
        if ($category !== 'all' && !empty($category)) {
            $stmt = $db->prepare('
                SELECT * FROM dueling_matches 
                WHERE category = ?
                ORDER BY round_order ASC, match_number ASC
            ');
            $stmt->execute([$category]);
        } else {
            $stmt = $db->query('
                SELECT * FROM dueling_matches 
                ORDER BY category ASC, round_order ASC, match_number ASC
            ');
        }
        $matches = $stmt->fetchAll(PDO::FETCH_ASSOC);

        jsonResponse([
            'success' => true,
            'data' => $matches,
            'total' => count($matches)
        ]);

    } catch (PDOException $e) {
        error_log('Gagal mengambil data dueling: ' . $e->getMessage());
        jsonResponse(['success' => false, 'message' => 'Terjadi kesalahan sistem saat mengambil data dueling.'], 500);
    }

} elseif ($method === 'POST') {
    requireAdmin();
    if (isAdminLoggedIn() && !hasPermission('scores')) {
        jsonResponse(['success' => false, 'error' => 'Akses ditolak: Akun Anda tidak memiliki izin untuk mengelola skor.'], 403);
    }

    $input = json_decode(file_get_contents('php://input'), true);

    if (!$input) {
        jsonResponse(['success' => false, 'message' => 'Invalid JSON body'], 400);
    }

    $db = getDB();
    $action = $input['action'] ?? '';

    // ==================== ACTION: RESET BRACKET ====================
    if ($action === 'reset_bracket') {
        $targetCat = trim($input['category'] ?? '');
        try {
            if (!empty($targetCat) && $targetCat !== 'all') {
                $del = $db->prepare('DELETE FROM dueling_matches WHERE category = ?');
                $del->execute([$targetCat]);
                jsonResponse(['success' => true, 'message' => "Bagan dueling ({$targetCat}) berhasil direset"]);
            } else {
                $db->exec('DELETE FROM dueling_matches');
                jsonResponse(['success' => true, 'message' => 'Seluruh bagan dueling berhasil direset']);
            }
        } catch (PDOException $e) {
            error_log('Gagal mereset bagan dueling: ' . $e->getMessage());
            jsonResponse(['success' => false, 'message' => 'Terjadi kesalahan sistem saat mereset bagan.'], 500);
        }
    }

    // ==================== ACTION: DELETE SINGLE MATCH ====================
    if ($action === 'delete') {
        $id = (int)($input['id'] ?? 0);
        if ($id > 0) {
            try {
                $del = $db->prepare('DELETE FROM dueling_matches WHERE id = ?');
                $del->execute([$id]);
                jsonResponse(['success' => true, 'message' => 'Match berhasil dihapus']);
            } catch (PDOException $e) {
                error_log('Gagal menghapus match: ' . $e->getMessage());
                jsonResponse(['success' => false, 'message' => 'Terjadi kesalahan sistem saat menghapus match.'], 500);
            }
        } else {
            jsonResponse(['success' => false, 'message' => 'ID match tidak valid'], 400);
        }
    }

    // ==================== ACTION: GENERATE TOURNAMENT BRACKET ====================
    if ($action === 'generate_bracket') {
        $count = (int)($input['count'] ?? $input['size'] ?? 8);
        $targetCat = trim($input['category'] ?? 'umum');
        if ($targetCat !== 'bda') $targetCat = 'umum';

        if ($count < 2) $count = 2;
        if ($count > 256) $count = 256;

        try {
            $db->beginTransaction();
            // Bersihkan bagan sebelumnya khusus kategori ini
            $del = $db->prepare('DELETE FROM dueling_matches WHERE category = ?');
            $del->execute([$targetCat]);

            // Ambil daftar peserta verified kategori Dueling sesuai sub-kategori
            // Peserta yang mendaftar Umum & BDA tidak boleh terblokir dari Umum
            if ($targetCat === 'bda') {
                $regStmt = $db->query("
                    SELECT registration_id, no_peserta, nama, satuan 
                    FROM registrations 
                    WHERE status = 'Verified' AND LOWER(kategori) LIKE '%bda%'
                    ORDER BY id ASC
                ");
            } else {
                $regStmt = $db->query("
                    SELECT registration_id, no_peserta, nama, satuan 
                    FROM registrations 
                    WHERE status = 'Verified' AND (LOWER(kategori) LIKE '%umum%' OR LOWER(kategori) LIKE '%keduanya%' OR LOWER(kategori) NOT LIKE '%bda%') AND LOWER(kategori) LIKE '%dueling%'
                    ORDER BY id ASC
                ");
            }
            $verifiedList = $regStmt->fetchAll(PDO::FETCH_ASSOC);

            // ================= SPECIAL CASE: 2 PESERTA =================
            if ($count === 2) {
                $p1 = $verifiedList[0] ?? null;
                $p2 = $verifiedList[1] ?? null;

                $ins = $db->prepare('
                    INSERT INTO dueling_matches 
                    (category, round_name, round_order, match_number,
                     participant_1_id, participant_1_name, participant_1_satuan,
                     participant_2_id, participant_2_name, participant_2_satuan,
                     match_status, updated_at) 
                    VALUES (?, "Final", 1, 1, ?, ?, ?, ?, ?, ?, "upcoming", NOW())
                ');
                $ins->execute([
                    $targetCat,
                    $p1 ? $p1['registration_id'] : null,
                    $p1 ? $p1['nama'] : 'Peserta 1',
                    $p1 ? $p1['satuan'] : '',
                    $p2 ? $p2['registration_id'] : null,
                    $p2 ? $p2['nama'] : 'Peserta 2',
                    $p2 ? $p2['satuan'] : ''
                ]);

                $db->commit();
                jsonResponse([
                    'success' => true,
                    'message' => "Bagan Final langsung untuk 2 peserta berhasil dibuat!",
                    'total_matches' => 1,
                    'count' => 2,
                    'p_base' => 2,
                    'm_pre' => 0,
                    'direct_seeds' => 2
                ]);
            }

            // ================= SPECIAL CASE: 3 PESERTA =================
            if ($count === 3) {
                $p1 = $verifiedList[0] ?? null; // Direct seed ke Final
                $p2 = $verifiedList[1] ?? null; // Pra-eliminasi slot 1
                $p3 = $verifiedList[2] ?? null; // Pra-eliminasi slot 2

                // Match 1: Pra-Eliminasi
                $insPre = $db->prepare('
                    INSERT INTO dueling_matches 
                    (category, round_name, round_order, match_number,
                     participant_1_id, participant_1_name, participant_1_satuan,
                     participant_2_id, participant_2_name, participant_2_satuan,
                     match_status, updated_at) 
                    VALUES (?, "Babak Pra-Eliminasi", 1, 1, ?, ?, ?, ?, ?, ?, "upcoming", NOW())
                ');
                $insPre->execute([
                    $targetCat,
                    $p2 ? $p2['registration_id'] : null,
                    $p2 ? $p2['nama'] : 'Peserta 2',
                    $p2 ? $p2['satuan'] : '',
                    $p3 ? $p3['registration_id'] : null,
                    $p3 ? $p3['nama'] : 'Peserta 3',
                    $p3 ? $p3['satuan'] : ''
                ]);
                $preId = (int)$db->lastInsertId();

                // Match 2: Final
                $insFinal = $db->prepare('
                    INSERT INTO dueling_matches 
                    (category, round_name, round_order, match_number,
                     participant_1_id, participant_1_name, participant_1_satuan,
                     participant_2_id, participant_2_name, participant_2_satuan,
                     match_status, updated_at) 
                    VALUES (?, "Final", 2, 2, ?, ?, ?, null, "Pemenang Pra-Eliminasi #1", "Menunggu duel", "upcoming", NOW())
                ');
                $insFinal->execute([
                    $targetCat,
                    $p1 ? $p1['registration_id'] : null,
                    $p1 ? $p1['nama'] : 'Peserta 1 (Lolos Langsung)',
                    $p1 ? $p1['satuan'] : ''
                ]);
                $finalId = (int)$db->lastInsertId();

                // Hubungkan Match 1 ke Match 2 slot 2
                $db->prepare('UPDATE dueling_matches SET next_match_id = ?, next_slot = 2 WHERE id = ?')
                   ->execute([$finalId, $preId]);

                $db->commit();
                jsonResponse([
                    'success' => true,
                    'message' => "Bagan 3 peserta berhasil dibuat (1 Pra-Eliminasi + 1 Final, 1 lolos langsung)!",
                    'total_matches' => 2,
                    'count' => 3,
                    'p_base' => 2,
                    'm_pre' => 1,
                    'direct_seeds' => 1
                ]);
            }

            // ================= GENERAL CASE: >= 4 PESERTA =================
            // Tentukan kapasitas babak dasar P_base (perpangkatan 2 <= count)
            $isPow2 = (($count & ($count - 1)) === 0);
            if ($isPow2) {
                $pBase = $count;
                $mPre = 0;
            } else {
                $pBase = (int)pow(2, floor(log($count, 2)));
                if ($pBase < 4) $pBase = 4;
                $mPre = $count - $pBase;
            }

            // Susun struktur babak turnamen
            $matchSpecs = [];
            $rounds = [];
            $currentMatchNum = 1;
            $roundOrder = 1;

            // 1. Babak Pra-Eliminasi (hanya jika ada kelebihan peserta di atas P_base)
            if ($mPre > 0) {
                $roundName = 'Babak Pra-Eliminasi';
                $preMatches = [];
                for ($i = 0; $i < $mPre; $i++) {
                    $preMatches[] = $currentMatchNum;
                    $matchSpecs[$currentMatchNum] = [
                        'round_name' => $roundName,
                        'round_order' => $roundOrder,
                        'match_number' => $currentMatchNum,
                        'type' => 'pre'
                    ];
                    $currentMatchNum++;
                }
                $rounds[] = [
                    'name' => $roundName,
                    'order' => $roundOrder,
                    'matches' => $preMatches
                ];
                $roundOrder++;
            }

            // 2. Babak Utama (P_base Besar s.d. Semifinal)
            $curM = (int)($pBase / 2);
            while ($curM >= 2) {
                if ($curM === 2) {
                    $roundName = 'Semifinal';
                } elseif ($curM === 4) {
                    $roundName = 'Perempat Final';
                } else {
                    $roundName = 'Babak ' . ($curM * 2) . ' Besar';
                }

                $roundMatches = [];
                for ($i = 0; $i < $curM; $i++) {
                    $roundMatches[] = $currentMatchNum;
                    $matchSpecs[$currentMatchNum] = [
                        'round_name' => $roundName,
                        'round_order' => $roundOrder,
                        'match_number' => $currentMatchNum,
                        'type' => 'main'
                    ];
                    $currentMatchNum++;
                }

                $rounds[] = [
                    'name' => $roundName,
                    'order' => $roundOrder,
                    'matches' => $roundMatches
                ];

                $roundOrder++;
                $curM = (int)($curM / 2);
            }

            // 3. Tambahkan Perebutan Juara 3 dan Final
            $bronzeMatchNum = $currentMatchNum;
            $matchSpecs[$bronzeMatchNum] = [
                'round_name' => 'Perebutan Juara 3',
                'round_order' => $roundOrder,
                'match_number' => $bronzeMatchNum,
                'type' => 'bronze'
            ];
            $currentMatchNum++;

            $finalMatchNum = $currentMatchNum;
            $matchSpecs[$finalMatchNum] = [
                'round_name' => 'Final',
                'round_order' => $roundOrder + 1,
                'match_number' => $finalMatchNum,
                'type' => 'final'
            ];

            // Insert initial match rows ke database
            $matchIdMap = []; // match_number => DB id
            $insertStmt = $db->prepare('
                INSERT INTO dueling_matches 
                (category, round_name, round_order, match_number, match_status, updated_at) 
                VALUES (?, ?, ?, ?, "upcoming", NOW())
            ');

            foreach ($matchSpecs as $mNum => $spec) {
                $insertStmt->execute([$targetCat, $spec['round_name'], $spec['round_order'], $mNum]);
                $matchIdMap[$mNum] = (int)$db->lastInsertId();
            }

            // Hubungkan tautan pemenang antar babak (next_match_id & next_slot)
            $linkStmt = $db->prepare('
                UPDATE dueling_matches SET
                    next_match_id = ?,
                    next_slot = ?,
                    loser_next_match_id = ?,
                    loser_next_slot = ?
                WHERE id = ?
            ');

            // Indeks babak utama pertama di dalam array $rounds
            $mainRoundIndex = ($mPre > 0) ? 1 : 0;
            $mainFirstMatches = $rounds[$mainRoundIndex]['matches'];
            $k = count($mainFirstMatches); // Jumlah partai di babak P_base Besar (P_base / 2)

            // A. Sambungkan pemenang Pra-Eliminasi ke Babak P_base Besar
            if ($mPre > 0) {
                $preMatchNums = $rounds[0]['matches'];
                if ($mPre <= $k) {
                    for ($j = 0; $j < $mPre; $j++) {
                        $preMNum = $preMatchNums[$j];
                        $targetMainMNum = $mainFirstMatches[$j];
                        $linkStmt->execute([
                            $matchIdMap[$targetMainMNum], 2, // Pemenang Pra-Eliminasi masuk ke Slot 2
                            null, null,
                            $matchIdMap[$preMNum]
                        ]);
                    }
                } else {
                    $excess = $mPre - $k;
                    $pIdx = 0;
                    for ($j = 0; $j < $excess; $j++) {
                        $pre1 = $preMatchNums[$pIdx++];
                        $pre2 = $preMatchNums[$pIdx++];
                        $targetMainMNum = $mainFirstMatches[$j];
                        $linkStmt->execute([
                            $matchIdMap[$targetMainMNum], 1,
                            null, null,
                            $matchIdMap[$pre1]
                        ]);
                        $linkStmt->execute([
                            $matchIdMap[$targetMainMNum], 2,
                            null, null,
                            $matchIdMap[$pre2]
                        ]);
                    }
                    for ($j = $excess; $j < $k; $j++) {
                        $preM = $preMatchNums[$pIdx++];
                        $targetMainMNum = $mainFirstMatches[$j];
                        $linkStmt->execute([
                            $matchIdMap[$targetMainMNum], 2,
                            null, null,
                            $matchIdMap[$preM]
                        ]);
                    }
                }
            }

            // B. Sambungkan antar babak utama (dari mainRoundIndex s.d. Semifinal)
            $totalRounds = count($rounds);
            for ($r = $mainRoundIndex; $r < $totalRounds; $r++) {
                $curMatches = $rounds[$r]['matches'];
                $isSemifinal = ($r === $totalRounds - 1);

                if ($isSemifinal) {
                    // Semifinal 1 -> Final Slot 1, Loser -> Juara 3 Slot 1
                    $linkStmt->execute([
                        $matchIdMap[$finalMatchNum], 1,
                        $matchIdMap[$bronzeMatchNum], 1,
                        $matchIdMap[$curMatches[0]]
                    ]);
                    // Semifinal 2 -> Final Slot 2, Loser -> Juara 3 Slot 2
                    $linkStmt->execute([
                        $matchIdMap[$finalMatchNum], 2,
                        $matchIdMap[$bronzeMatchNum], 2,
                        $matchIdMap[$curMatches[1]]
                    ]);
                } else {
                    // Babak r -> Babak r + 1
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

            // C. Penempatan Peserta Riil ke dalam Slot Pertandingan
            $updateMatchStmt = $db->prepare('
                UPDATE dueling_matches SET
                    participant_1_id = ?, participant_1_name = ?, participant_1_satuan = ?,
                    participant_2_id = ?, participant_2_name = ?, participant_2_satuan = ?,
                    winner_id = NULL, match_status = "upcoming"
                WHERE id = ?
            ');

            $vIdx = 0;

            // 1) Isi peserta di Babak Pra-Eliminasi (jika ada)
            if ($mPre > 0) {
                foreach ($rounds[0]['matches'] as $mNum) {
                    $p1 = $verifiedList[$vIdx] ?? null;
                    $p1Id = $p1 ? $p1['registration_id'] : null;
                    $p1Name = $p1 ? $p1['nama'] : ('Peserta ' . ($vIdx + 1));
                    $p1Sat = $p1 ? $p1['satuan'] : '';
                    $vIdx++;

                    $p2 = $verifiedList[$vIdx] ?? null;
                    $p2Id = $p2 ? $p2['registration_id'] : null;
                    $p2Name = $p2 ? $p2['nama'] : ('Peserta ' . ($vIdx + 1));
                    $p2Sat = $p2 ? $p2['satuan'] : '';
                    $vIdx++;

                    $updateMatchStmt->execute([
                        $p1Id, $p1Name, $p1Sat,
                        $p2Id, $p2Name, $p2Sat,
                        $matchIdMap[$mNum]
                    ]);
                }
            }

            // 2) Kumpulkan peserta yang Lolos Langsung (Direct Seeds) ke Babak Utama
            $directPlayers = [];
            $totalDirect = $count - ($mPre * 2);
            for ($d = 0; $d < $totalDirect; $d++) {
                $p = $verifiedList[$vIdx] ?? null;
                $directPlayers[] = [
                    'id' => $p ? $p['registration_id'] : null,
                    'name' => $p ? $p['nama'] : ('Peserta ' . ($vIdx + 1)),
                    'satuan' => $p ? $p['satuan'] : ''
                ];
                $vIdx++;
            }

            $dpIdx = 0;
            if ($mPre === 0) {
                // Semua slot diisi direct players
                foreach ($mainFirstMatches as $mNum) {
                    $p1 = $directPlayers[$dpIdx++] ?? ['id' => null, 'name' => 'TBD', 'satuan' => ''];
                    $p2 = $directPlayers[$dpIdx++] ?? ['id' => null, 'name' => 'TBD', 'satuan' => ''];
                    $updateMatchStmt->execute([
                        $p1['id'], $p1['name'], $p1['satuan'],
                        $p2['id'], $p2['name'], $p2['satuan'],
                        $matchIdMap[$mNum]
                    ]);
                }
            } elseif ($mPre <= $k) {
                for ($j = 0; $j < $mPre; $j++) {
                    $mNum = $mainFirstMatches[$j];
                    $p1 = $directPlayers[$dpIdx++] ?? ['id' => null, 'name' => 'TBD', 'satuan' => ''];
                    // Slot 2 kosong, menunggu pemenang Pra-Eliminasi
                    $updateMatchStmt->execute([
                        $p1['id'], $p1['name'], $p1['satuan'],
                        null, 'Pemenang Pra-Eliminasi #' . ($j + 1), 'Menunggu hasil duel',
                        $matchIdMap[$mNum]
                    ]);
                }
                for ($j = $mPre; $j < $k; $j++) {
                    $mNum = $mainFirstMatches[$j];
                    $p1 = $directPlayers[$dpIdx++] ?? ['id' => null, 'name' => 'TBD', 'satuan' => ''];
                    $p2 = $directPlayers[$dpIdx++] ?? ['id' => null, 'name' => 'TBD', 'satuan' => ''];
                    $updateMatchStmt->execute([
                        $p1['id'], $p1['name'], $p1['satuan'],
                        $p2['id'], $p2['name'], $p2['satuan'],
                        $matchIdMap[$mNum]
                    ]);
                }
            } else {
                $excess = $mPre - $k;
                $preTrack = 1;
                for ($j = 0; $j < $excess; $j++) {
                    $mNum = $mainFirstMatches[$j];
                    // Kedua slot menunggu pemenang Pra-Eliminasi
                    $p1Name = 'Pemenang Pra-Eliminasi #' . ($preTrack++);
                    $p2Name = 'Pemenang Pra-Eliminasi #' . ($preTrack++);
                    $updateMatchStmt->execute([
                        null, $p1Name, 'Menunggu hasil duel',
                        null, $p2Name, 'Menunggu hasil duel',
                        $matchIdMap[$mNum]
                    ]);
                }
                for ($j = $excess; $j < $k; $j++) {
                    $mNum = $mainFirstMatches[$j];
                    $p1 = $directPlayers[$dpIdx++] ?? ['id' => null, 'name' => 'TBD', 'satuan' => ''];
                    $p2Name = 'Pemenang Pra-Eliminasi #' . ($preTrack++);
                    $updateMatchStmt->execute([
                        $p1['id'], $p1['name'], $p1['satuan'],
                        null, $p2Name, 'Menunggu hasil duel',
                        $matchIdMap[$mNum]
                    ]);
                }
            }

            $db->commit();

            $detailMsg = ($mPre > 0) 
                ? " ($mPre partai Pra-Eliminasi + Babak $pBase Besar, $totalDirect peserta lolos langsung)"
                : " (Bagan baku Babak $pBase Besar)";

            jsonResponse([
                'success' => true,
                'message' => "Bagan turnamen eliminasi untuk $count peserta berhasil dibuat$detailMsg!",
                'total_matches' => count($matchSpecs),
                'count' => $count,
                'p_base' => $pBase,
                'm_pre' => $mPre,
                'direct_seeds' => $totalDirect
            ]);

        } catch (PDOException $e) {
            $db->rollBack();
            error_log('Gagal membuat bagan dueling: ' . $e->getMessage());
            jsonResponse(['success' => false, 'message' => 'Terjadi kesalahan sistem saat membuat bagan dueling.'], 500);
        }
    }

    // ==================== DEFAULT: INSERT OR UPDATE SINGLE MATCH ====================
    $id = isset($input['id']) && (int)$input['id'] > 0 ? (int)$input['id'] : null;
    $targetCategory = trim($input['category'] ?? 'umum');
    if ($targetCategory !== 'bda') $targetCategory = 'umum';

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
                    category = ?, round_name = ?, round_order = ?, match_number = ?,
                    participant_1_id = ?, participant_1_name = ?, participant_1_satuan = ?,
                    participant_2_id = ?, participant_2_name = ?, participant_2_satuan = ?,
                    time_1 = ?, time_2 = ?, winner_id = ?, match_status = ?,
                    next_match_id = ?, next_slot = ?,
                    loser_next_match_id = ?, loser_next_slot = ?,
                    updated_at = NOW()
                WHERE id = ?
            ');
            $stmt->execute([
                $targetCategory, $roundName, $roundOrder, $matchNumber,
                $participant1Id, $participant1Name, $participant1Satuan,
                $participant2Id, $participant2Name, $participant2Satuan,
                $time1, $time2, $winnerId, $matchStatus,
                $nextMatchId, $nextSlot,
                $loserNextMatchId, $loserNextSlot,
                $id
            ]);

            $targetId = $id;

        } else {
            // Insert new match with category
            $stmt = $db->prepare('
                INSERT INTO dueling_matches 
                (category, round_name, round_order, match_number,
                 participant_1_id, participant_1_name, participant_1_satuan,
                 participant_2_id, participant_2_name, participant_2_satuan,
                 time_1, time_2, winner_id, match_status,
                 next_match_id, next_slot, loser_next_match_id, loser_next_slot,
                 updated_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
            ');
            $stmt->execute([
                $targetCategory,
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

            // Cascade reset downstream if winner changed or is cleared
            if (empty($winnerId)) {
                $db->prepare("UPDATE dueling_matches SET winner_id = NULL, match_status = 'upcoming', time_1 = NULL, time_2 = NULL WHERE id = ?")->execute([$nextMatchId]);
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

            // Cascade reset bronze if winner cleared
            if (empty($winnerId)) {
                $db->prepare("UPDATE dueling_matches SET winner_id = NULL, match_status = 'upcoming', time_1 = NULL, time_2 = NULL WHERE id = ?")->execute([$loserNextMatchId]);
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
        error_log('Gagal menyimpan data match dueling: ' . $e->getMessage());
        jsonResponse(['success' => false, 'message' => 'Terjadi kesalahan sistem saat menyimpan data match.'], 500);
    }

} else {
    jsonResponse(['success' => false, 'message' => 'Method not allowed'], 405);
}
