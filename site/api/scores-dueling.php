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

    // ==================== HELPER: PLAN TOURNAMENT ROUNDS ====================
    if (!function_exists('planTournamentRounds')) {
        function planTournamentRounds($N) {
            if ($N < 2) return [];

            if ($N === 2) {
                return [
                    [
                        'order' => 1,
                        'name' => 'Final',
                        'count' => 2,
                        'matches' => 1,
                        'byes' => 0,
                        'type' => 'final'
                    ]
                ];
            }

            if ($N === 3) {
                return [
                    [
                        'order' => 1,
                        'name' => 'Babak 3 Besar (Playoff)',
                        'count' => 3,
                        'matches' => 1,
                        'byes' => 1,
                        'type' => 'playoff_3'
                    ],
                    [
                        'order' => 2,
                        'name' => 'Perebutan Juara 3',
                        'count' => 2,
                        'matches' => 1,
                        'byes' => 0,
                        'type' => 'bronze'
                    ],
                    [
                        'order' => 3,
                        'name' => 'Final',
                        'count' => 2,
                        'matches' => 1,
                        'byes' => 0,
                        'type' => 'final'
                    ]
                ];
            }

            $rounds = [];
            $roundOrder = 1;
            $K = $N;

            while ($K > 4) {
                if ($K >= 5 && $K <= 7) {
                    $matches = $K - 4;
                    $byes = $K - (2 * $matches);
                    $roundName = ($K === 7) ? 'Babak 7 Besar' : (($K === 6) ? 'Babak 6 Besar' : 'Babak 5 Besar');
                    $rounds[] = [
                        'order' => $roundOrder++,
                        'name' => $roundName,
                        'count' => $K,
                        'matches' => $matches,
                        'byes' => $byes,
                        'type' => 'playoff_to_semi'
                    ];
                    $K = 4;
                } else {
                    $matches = (int)floor($K / 2);
                    $byes = $K % 2;
                    $roundName = ($K === 8) ? 'Perempat Final' : ('Babak ' . $K . ' Besar');
                    $rounds[] = [
                        'order' => $roundOrder++,
                        'name' => $roundName,
                        'count' => $K,
                        'matches' => $matches,
                        'byes' => $byes,
                        'type' => 'normal'
                    ];
                    $K = $matches + $byes;
                }
            }

            // Semifinal (4 Besar)
            $rounds[] = [
                'order' => $roundOrder++,
                'name' => 'Semifinal',
                'count' => 4,
                'matches' => 2,
                'byes' => 0,
                'type' => 'semifinal'
            ];

            // Perebutan Juara 3 (Match tersendiri)
            $rounds[] = [
                'order' => $roundOrder++,
                'name' => 'Perebutan Juara 3',
                'count' => 2,
                'matches' => 1,
                'byes' => 0,
                'type' => 'bronze'
            ];

            // Final (Match Juara 1 & 2)
            $rounds[] = [
                'order' => $roundOrder++,
                'name' => 'Final',
                'count' => 2,
                'matches' => 1,
                'byes' => 0,
                'type' => 'final'
            ];

            return $rounds;
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

            // Siapkan pool peserta Babak 1
            $pool = [];
            for ($i = 0; $i < $count; $i++) {
                if (isset($verifiedList[$i])) {
                    $pool[] = [
                        'id' => $verifiedList[$i]['registration_id'],
                        'name' => $verifiedList[$i]['nama'],
                        'satuan' => $verifiedList[$i]['satuan']
                    ];
                } else {
                    $pool[] = [
                        'id' => null,
                        'name' => 'Peserta ' . ($i + 1),
                        'satuan' => '-'
                    ];
                }
            }

            // Acak peserta untuk Babak 1
            shuffle($pool);

            $roundsPlan = planTournamentRounds($count);
            $matchSpecs = [];
            $roundMatchMap = [];
            $currentMatchNum = 1;

            // Susun seluruh spesifikasi pertandingan
            foreach ($roundsPlan as $r) {
                $rOrder = $r['order'];
                $roundMatchMap[$rOrder] = [];

                // Partai kompetitif
                for ($m = 0; $m < $r['matches']; $m++) {
                    $mNum = $currentMatchNum++;
                    $roundMatchMap[$rOrder][] = $mNum;
                    $matchSpecs[$mNum] = [
                        'round_name' => $r['name'],
                        'round_order' => $rOrder,
                        'match_number' => $mNum,
                        'type' => $r['type'],
                        'is_bye' => false
                    ];
                }

                // Partai BYE (tiket lolos langsung)
                for ($b = 0; $b < $r['byes']; $b++) {
                    $mNum = $currentMatchNum++;
                    $roundMatchMap[$rOrder][] = $mNum;
                    $matchSpecs[$mNum] = [
                        'round_name' => $r['name'],
                        'round_order' => $rOrder,
                        'match_number' => $mNum,
                        'type' => $r['type'],
                        'is_bye' => true
                    ];
                }
            }

            // Masukkan seluruh kerangka pertandingan ke basis data
            $insertStmt = $db->prepare('
                INSERT INTO dueling_matches 
                (category, round_name, round_order, match_number, match_status, updated_at) 
                VALUES (?, ?, ?, ?, "upcoming", NOW())
            ');
            $matchIdMap = [];
            foreach ($matchSpecs as $mNum => $spec) {
                $insertStmt->execute([$targetCat, $spec['round_name'], $spec['round_order'], $mNum]);
                $matchIdMap[$mNum] = (int)$db->lastInsertId();
            }

            // Tautkan Semifinal ke Final (Winner) & Perebutan Juara 3 (Loser)
            $semiOrder = null;
            $bronzeOrder = null;
            $finalOrder = null;
            foreach ($roundsPlan as $r) {
                if ($r['type'] === 'semifinal') $semiOrder = $r['order'];
                if ($r['type'] === 'bronze') $bronzeOrder = $r['order'];
                if ($r['type'] === 'final') $finalOrder = $r['order'];
            }

            if ($semiOrder && $bronzeOrder && $finalOrder) {
                $semiMatches = $roundMatchMap[$semiOrder];
                $bronzeMatches = $roundMatchMap[$bronzeOrder];
                $finalMatches = $roundMatchMap[$finalOrder];

                $linkStmt = $db->prepare('
                    UPDATE dueling_matches SET
                        next_match_id = ?, next_slot = ?,
                        loser_next_match_id = ?, loser_next_slot = ?
                    WHERE id = ?
                ');

                // SF1: Pemenang ke Final Slot 1, Kalah ke Juara 3 Slot 1
                $linkStmt->execute([
                    $matchIdMap[$finalMatches[0]], 1,
                    $matchIdMap[$bronzeMatches[0]], 1,
                    $matchIdMap[$semiMatches[0]]
                ]);

                // SF2: Pemenang ke Final Slot 2, Kalah ke Juara 3 Slot 2
                $linkStmt->execute([
                    $matchIdMap[$finalMatches[0]], 2,
                    $matchIdMap[$bronzeMatches[0]], 2,
                    $matchIdMap[$semiMatches[1]]
                ]);
            }

            // Isi peserta Babak 1
            $r1MatchNums = $roundMatchMap[1];
            $r1Comp = [];
            $r1Byes = [];
            foreach ($r1MatchNums as $mNum) {
                if ($matchSpecs[$mNum]['is_bye']) $r1Byes[] = $mNum;
                else $r1Comp[] = $mNum;
            }

            $updateMatchStmt = $db->prepare('
                UPDATE dueling_matches SET
                    participant_1_id = ?, participant_1_name = ?, participant_1_satuan = ?,
                    participant_2_id = ?, participant_2_name = ?, participant_2_satuan = ?,
                    winner_id = ?, match_status = ?, time_1 = ?, time_2 = NULL, updated_at = NOW()
                WHERE id = ?
            ');

            // 1) Alokasikan tiket BYE Babak 1 (jika ada)
            foreach ($r1Byes as $bNum) {
                $pBye = array_pop($pool);
                $updateMatchStmt->execute([
                    $pBye['id'], $pBye['name'], $pBye['satuan'],
                    null, 'BYE (Lolos Otomatis)', '-',
                    $pBye['id'] ?? $pBye['name'], 'finished', 0,
                    $matchIdMap[$bNum]
                ]);
            }

            // 2) Alokasikan partai kompetitif Babak 1
            foreach ($r1Comp as $cNum) {
                $p1 = array_shift($pool);
                $p2 = array_shift($pool);
                $updateMatchStmt->execute([
                    $p1['id'], $p1['name'], $p1['satuan'],
                    $p2['id'], $p2['name'], $p2['satuan'],
                    null, 'upcoming', null,
                    $matchIdMap[$cNum]
                ]);
            }

            // 3) Berikan teks penjelas placeholder untuk babak-babak selanjutnya
            for ($rIdx = 2; $rIdx <= count($roundsPlan); $rIdx++) {
                $rType = $roundsPlan[$rIdx - 1]['type'];
                $matchNums = $roundMatchMap[$rIdx];
                foreach ($matchNums as $mNum) {
                    if ($rType === 'bronze') {
                        $updateMatchStmt->execute([
                            null, 'Kalah Semifinal 1', 'Menunggu duel',
                            null, 'Kalah Semifinal 2', 'Menunggu duel',
                            null, 'upcoming', null,
                            $matchIdMap[$mNum]
                        ]);
                    } elseif ($rType === 'final') {
                        $updateMatchStmt->execute([
                            null, 'Pemenang Semifinal 1', 'Menunggu duel',
                            null, 'Pemenang Semifinal 2', 'Menunggu duel',
                            null, 'upcoming', null,
                            $matchIdMap[$mNum]
                        ]);
                    } else {
                        $isBye = $matchSpecs[$mNum]['is_bye'];
                        if ($isBye) {
                            $updateMatchStmt->execute([
                                null, 'Menunggu Hasil Undian', '-',
                                null, 'BYE (Lolos Otomatis)', '-',
                                null, 'upcoming', null,
                                $matchIdMap[$mNum]
                            ]);
                        } else {
                            $updateMatchStmt->execute([
                                null, 'Menunggu Hasil Undian', 'Menunggu duel',
                                null, 'Menunggu Hasil Undian', 'Menunggu duel',
                                null, 'upcoming', null,
                                $matchIdMap[$mNum]
                            ]);
                        }
                    }
                }
            }

            $db->commit();

            $r1Matches = count($r1Comp);
            $r1ByesCount = count($r1Byes);
            $byeText = $r1ByesCount > 0 ? " + $r1ByesCount BYE" : "";

            jsonResponse([
                'success' => true,
                'message' => "Bagan turnamen untuk $count peserta berhasil dibuat! Babak 1: $r1Matches Match$byeText.",
                'total_matches' => count($matchSpecs),
                'count' => $count
            ]);

        } catch (PDOException $e) {
            $db->rollBack();
            error_log('Gagal membuat bagan dueling: ' . $e->getMessage());
            jsonResponse(['success' => false, 'message' => 'Terjadi kesalahan sistem saat membuat bagan dueling.'], 500);
        }
    }

    // ==================== ACTION: RANDOMIZE SPECIFIC ROUND ====================
    if ($action === 'randomize_round') {
        $targetCat = trim($input['category'] ?? 'umum');
        if ($targetCat !== 'bda') $targetCat = 'umum';
        $roundOrder = (int)($input['round_order'] ?? 1);

        if ($roundOrder < 1) {
            jsonResponse(['success' => false, 'message' => 'Babak tidak valid'], 400);
        }

        try {
            $db->beginTransaction();

            // 1. Ambil match di babak yang ditargetkan
            $curStmt = $db->prepare('
                SELECT * FROM dueling_matches 
                WHERE category = ? AND round_order = ? 
                ORDER BY match_number ASC
            ');
            $curStmt->execute([$targetCat, $roundOrder]);
            $currentMatches = $curStmt->fetchAll(PDO::FETCH_ASSOC);

            if (empty($currentMatches)) {
                jsonResponse(['success' => false, 'message' => 'Babak ini belum memiliki jadwal pertandingan'], 400);
            }

            $roundName = $currentMatches[0]['round_name'] ?? ('Babak ' . $roundOrder);

            // Larang pengundian langsung pada Final atau Perebutan Juara 3 (karena otomatis dari Semifinal)
            if ($roundName === 'Final' || $roundName === 'Perebutan Juara 3') {
                jsonResponse([
                    'success' => false, 
                    'message' => 'Partai ' . $roundName . ' terisi otomatis dari hasil Semifinal dan tidak dapat diundi acak secara manual.'
                ], 400);
            }

            // Jika Babak 1: Undi ulang dari peserta verified pendaftaran
            if ($roundOrder === 1) {
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

                $pool = [];
                $totalSlots = 0;
                foreach ($currentMatches as $m) {
                    $isBye = (stripos($m['participant_2_name'] ?? '', 'bye') !== false);
                    $totalSlots += $isBye ? 1 : 2;
                }

                for ($i = 0; $i < $totalSlots; $i++) {
                    if (isset($verifiedList[$i])) {
                        $pool[] = [
                            'id' => $verifiedList[$i]['registration_id'],
                            'name' => $verifiedList[$i]['nama'],
                            'satuan' => $verifiedList[$i]['satuan'],
                            'was_bye' => false
                        ];
                    } else {
                        $pool[] = [
                            'id' => null,
                            'name' => 'Peserta ' . ($i + 1),
                            'satuan' => '-',
                            'was_bye' => false
                        ];
                    }
                }
                shuffle($pool);

                $upd = $db->prepare('
                    UPDATE dueling_matches SET
                        participant_1_id = ?, participant_1_name = ?, participant_1_satuan = ?,
                        participant_2_id = ?, participant_2_name = ?, participant_2_satuan = ?,
                        winner_id = ?, match_status = ?, time_1 = ?, time_2 = NULL, updated_at = NOW()
                    WHERE id = ?
                ');

                foreach ($currentMatches as $m) {
                    $isBye = (stripos($m['participant_2_name'] ?? '', 'bye') !== false);
                    if ($isBye) {
                        $pBye = array_pop($pool);
                        $upd->execute([
                            $pBye['id'], $pBye['name'], $pBye['satuan'],
                            null, 'BYE (Lolos Otomatis)', '-',
                            $pBye['id'] ?? $pBye['name'], 'finished', 0,
                            $m['id']
                        ]);
                    } else {
                        $p1 = array_shift($pool);
                        $p2 = array_shift($pool);
                        $upd->execute([
                            $p1['id'], $p1['name'], $p1['satuan'],
                            $p2['id'], $p2['name'], $p2['satuan'],
                            null, 'upcoming', null,
                            $m['id']
                        ]);
                    }
                }

                $db->commit();
                jsonResponse([
                    'success' => true,
                    'message' => "Babak 1 ({$roundName}) berhasil diundi ulang dengan acakan baru!"
                ]);
            }

            // Babak > 1: Ambil seluruh peserta yang lolos dari babak sebelumnya (round_order - 1)
            $prevStmt = $db->prepare('
                SELECT * FROM dueling_matches 
                WHERE category = ? AND round_order = ? 
                ORDER BY match_number ASC
            ');
            $prevStmt->execute([$targetCat, $roundOrder - 1]);
            $prevMatches = $prevStmt->fetchAll(PDO::FETCH_ASSOC);

            if (empty($prevMatches)) {
                jsonResponse(['success' => false, 'message' => 'Babak sebelumnya tidak ditemukan'], 400);
            }

            // Validasi kelengkapan: seluruh pertandingan di babak sebelumnya harus sudah selesai
            $unfinished = [];
            foreach ($prevMatches as $pm) {
                if ($pm['match_status'] !== 'finished' || empty($pm['winner_id'])) {
                    $unfinished[] = $pm['match_number'];
                }
            }
            if (!empty($unfinished)) {
                jsonResponse([
                    'success' => false,
                    'message' => 'Babak sebelumnya belum selesai! Masih ada Match #' . implode(', #', $unfinished) . ' yang belum memiliki pemenang. Harap selesaikan seluruh pertandingan sebelum mengundi babak ini.'
                ], 400);
            }

            // Kumpulkan peserta yang lolos (pemenang duel & pemegang BYE)
            $qualified = [];
            foreach ($prevMatches as $pm) {
                $wId = trim($pm['winner_id']);
                $isBye = (stripos($pm['participant_2_name'] ?? '', 'bye') !== false);

                if ($wId === trim($pm['participant_1_id'] ?? '') || $wId === trim($pm['participant_1_name'] ?? '') || $wId === 'p1') {
                    $qualified[] = [
                        'id' => $pm['participant_1_id'],
                        'name' => $pm['participant_1_name'],
                        'satuan' => $pm['participant_1_satuan'],
                        'was_bye' => $isBye
                    ];
                } else {
                    $qualified[] = [
                        'id' => $pm['participant_2_id'],
                        'name' => $pm['participant_2_name'],
                        'satuan' => $pm['participant_2_satuan'],
                        'was_bye' => $isBye
                    ];
                }
            }

            // Pisahkan partai kompetitif dan slot BYE di babak ini
            $compMatches = [];
            $byeMatches = [];
            foreach ($currentMatches as $cm) {
                $isBye = (stripos($cm['participant_2_name'] ?? '', 'bye') !== false);
                if ($isBye) $byeMatches[] = $cm;
                else $compMatches[] = $cm;
            }

            $totalNeeded = (count($compMatches) * 2) + count($byeMatches);
            if (count($qualified) < $totalNeeded) {
                jsonResponse([
                    'success' => false,
                    'message' => "Jumlah peserta yang lolos (" . count($qualified) . ") tidak mencukupi kebutuhan slot ({$totalNeeded})."
                ], 400);
            }

            // Acak seluruh peserta yang lolos
            shuffle($qualified);

            // Penerapan Aturan Anti-Double BYE:
            // Peserta yang pada babak sebelumnya menikmati BYE tidak boleh mendapat BYE lagi
            $byeCount = count($byeMatches);
            $assignedByes = [];

            for ($b = 0; $b < $byeCount; $b++) {
                $chosenIdx = -1;
                foreach ($qualified as $idx => $cand) {
                    if (!$cand['was_bye']) {
                        $chosenIdx = $idx;
                        break;
                    }
                }
                if ($chosenIdx === -1) $chosenIdx = 0; // Fallback darurat jika seluruhnya was_bye

                $assignedByes[] = $qualified[$chosenIdx];
                array_splice($qualified, $chosenIdx, 1);
            }

            $upd = $db->prepare('
                UPDATE dueling_matches SET
                    participant_1_id = ?, participant_1_name = ?, participant_1_satuan = ?,
                    participant_2_id = ?, participant_2_name = ?, participant_2_satuan = ?,
                    winner_id = ?, match_status = ?, time_1 = ?, time_2 = NULL, updated_at = NOW()
                WHERE id = ?
            ');

            // Isi partai BYE babak ini
            foreach ($byeMatches as $idx => $bm) {
                $pBye = $assignedByes[$idx];
                $upd->execute([
                    $pBye['id'], $pBye['name'], $pBye['satuan'],
                    null, 'BYE (Lolos Otomatis)', '-',
                    $pBye['id'] ?? $pBye['name'], 'finished', 0,
                    $bm['id']
                ]);
            }

            // Isi partai kompetitif babak ini
            foreach ($compMatches as $cm) {
                $p1 = array_shift($qualified);
                $p2 = array_shift($qualified);
                $upd->execute([
                    $p1['id'], $p1['name'], $p1['satuan'],
                    $p2['id'], $p2['name'], $p2['satuan'],
                    null, 'upcoming', null,
                    $cm['id']
                ]);
            }

            $db->commit();

            $cCount = count($compMatches);
            $bCount = count($byeMatches);
            $byeMsg = $bCount > 0 ? " (Termasuk $bCount peserta mendapat tiket BYE)" : "";

            jsonResponse([
                'success' => true,
                'message' => "Pengundian {$roundName} berhasil! {$cCount} partai telah dipasangkan{$byeMsg}."
            ]);

        } catch (PDOException $e) {
            $db->rollBack();
            error_log('Gagal mengundi babak dueling: ' . $e->getMessage());
            jsonResponse(['success' => false, 'message' => 'Terjadi kesalahan sistem saat mengundi babak.'], 500);
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
