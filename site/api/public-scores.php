<?php
require_once __DIR__ . '/../includes/db.php';
cors();

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    jsonResponse(['success' => false, 'message' => 'Method not allowed'], 405);
}

$db = getDB();

try {
    // Fetch presisi scores ranked by total_score DESC, x_count DESC
    $stmtPresisi = $db->query('
        SELECT sp.registration_id, sp.no_peserta, sp.nama, sp.satuan,
               sp.seri_1, sp.seri_2, sp.seri_3, sp.seri_4, sp.seri_5,
               sp.seri_6, sp.seri_7, sp.seri_8, sp.seri_9, sp.seri_10,
               sp.x_count, sp.total_score,
               r.kategori
        FROM scores_presisi sp
        LEFT JOIN registrations r ON sp.registration_id = r.registration_id
        ORDER BY sp.total_score DESC, sp.x_count DESC
    ');
    $presisi = $stmtPresisi->fetchAll(PDO::FETCH_ASSOC);

    // Add ranking
    $rank = 1;
    foreach ($presisi as &$score) {
        $score['rank'] = $rank++;
    }
    unset($score);

    // Fetch dueling matches ordered by round_order, match_number
    $stmtDueling = $db->query('
        SELECT id, round_name, round_order, match_number,
               participant_1_id, participant_1_name, participant_1_satuan,
               participant_2_id, participant_2_name, participant_2_satuan,
               time_1, time_2, winner_id, match_status
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
    jsonResponse(['success' => false, 'message' => 'Gagal mengambil data skor: ' . $e->getMessage()], 500);
}
