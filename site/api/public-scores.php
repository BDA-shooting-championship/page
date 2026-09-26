<?php
/**
 * BDA Shooting Championship 2026 — Public Live Scores API
 * Serves live scores for public leaderboard (Presisi 20M & Dueling Plat)
 */
require_once __DIR__ . '/../includes/db.php';
cors();

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    jsonResponse(['success' => false, 'message' => 'Method not allowed'], 405);
}

$db = getDB();

try {
    // Fetch presisi scores ranked by nilai DESC, ring_x DESC, jumlah_masuk DESC
    // Note: sp.registration_id is omitted to protect participant PII
    $stmtPresisi = $db->query('
        SELECT sp.no_peserta, sp.nama, sp.satuan,
               sp.ring_x, sp.ring_10, sp.ring_9, sp.ring_8, sp.ring_7,
               sp.ring_6, sp.ring_5, sp.ring_4, sp.ring_3, sp.ring_2, sp.ring_1,
               sp.jumlah_masuk, sp.nilai,
               r.kategori
        FROM scores_presisi sp
        LEFT JOIN registrations r ON sp.registration_id = r.registration_id
        ORDER BY sp.nilai DESC, sp.ring_x DESC, sp.jumlah_masuk DESC
    ');
    $presisi = $stmtPresisi->fetchAll(PDO::FETCH_ASSOC);

    // Add ranking and cast integers
    $rank = 1;
    foreach ($presisi as &$score) {
        $score['rank'] = $rank++;
        $score['ring_x'] = (int)($score['ring_x'] ?? 0);
        for ($r = 1; $r <= 10; $r++) {
            $score['ring_' . $r] = (int)($score['ring_' . $r] ?? 0);
        }
        $score['jumlah_masuk'] = (int)($score['jumlah_masuk'] ?? 0);
        $score['nilai'] = round((float)($score['nilai'] ?? 0), 1);
    }
    unset($score);

    // Fetch dueling matches ordered by round_order, match_number
    $stmtDueling = $db->query('
        SELECT id, category, round_name, round_order, match_number,
               participant_1_name, participant_1_satuan,
               participant_2_name, participant_2_satuan,
               time_1, time_2, winner_id, match_status,
               next_match_id, next_slot, loser_next_match_id, loser_next_slot
        FROM dueling_matches
        ORDER BY round_order ASC, match_number ASC
    ');
    $dueling = $stmtDueling->fetchAll(PDO::FETCH_ASSOC);

    jsonResponse([
        'success' => true,
        'presisi' => $presisi,
        'dueling' => $dueling
    ]);

} catch (PDOException $e) {
    error_log('Gagal mengambil data skor publik: ' . $e->getMessage());
    jsonResponse(['success' => false, 'message' => 'Terjadi kesalahan sistem saat mengambil data skor live.'], 500);
}
