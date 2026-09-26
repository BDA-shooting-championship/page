<?php
/**
 * BDA Shooting Championship 2026 — Database Migration Runner
 * Safely updates database schema to multi-user auth and ring-based scoring.
 */
require_once __DIR__ . '/../includes/db.php';
cors();

// Protect with secret token
$token = $_SERVER['HTTP_X_ADMIN_TOKEN'] ?? $_GET['token'] ?? '';
if ($token !== ADMIN_TOKEN) {
    jsonResponse(['success' => false, 'error' => 'Unauthorized migration request'], 401);
}

$db = getDB();
$results = [];

try {
    // 1. Create admin_users table
    $db->exec('
        CREATE TABLE IF NOT EXISTS admin_users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) NOT NULL UNIQUE,
            password_hash VARCHAR(255) NOT NULL,
            display_name VARCHAR(100) NOT NULL,
            role ENUM("superadmin","admin") DEFAULT "admin",
            permissions JSON NOT NULL,
            is_active TINYINT(1) DEFAULT 1,
            last_login TIMESTAMP NULL DEFAULT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_username (username),
            INDEX idx_role (role)
        ) ENGINE=InnoDB;
    ');
    $results['admin_users_table'] = 'OK';

    // 2. Seed superadmin account if not exists
    $checkAdmin = $db->query('SELECT COUNT(*) FROM admin_users WHERE username = "superadmin"')->fetchColumn();
    if ($checkAdmin == 0) {
        $superHash = password_hash('BDA750admin', PASSWORD_BCRYPT);
        $insAdmin = $db->prepare('
            INSERT INTO admin_users (username, password_hash, display_name, role, permissions, is_active)
            VALUES (?, ?, ?, "superadmin", ?, 1)
        ');
        $insAdmin->execute([
            'superadmin',
            $superHash,
            'Super Administrator',
            json_encode(['antrean', 'peserta', 'scores', 'users'])
        ]);
        $results['seed_superadmin'] = 'Created (superadmin / BDA750admin)';
    } else {
        $results['seed_superadmin'] = 'Already exists';
    }

    // 3. Migrate scores_presisi to Ring-Based system
    // Check existing columns in scores_presisi
    $colStmt = $db->query('SHOW COLUMNS FROM scores_presisi');
    $cols = $colStmt->fetchAll(PDO::FETCH_COLUMN);

    // If ring_x doesn't exist, add ring columns
    if (!in_array('ring_x', $cols)) {
        $db->exec('
            ALTER TABLE scores_presisi
            ADD COLUMN ring_x INT DEFAULT 0 COMMENT "Peluru masuk X (tiebreaker)" AFTER satuan,
            ADD COLUMN ring_10 INT DEFAULT 0 AFTER ring_x,
            ADD COLUMN ring_9 INT DEFAULT 0 AFTER ring_10,
            ADD COLUMN ring_8 INT DEFAULT 0 AFTER ring_9,
            ADD COLUMN ring_7 INT DEFAULT 0 AFTER ring_8,
            ADD COLUMN ring_6 INT DEFAULT 0 AFTER ring_7,
            ADD COLUMN ring_5 INT DEFAULT 0 AFTER ring_6,
            ADD COLUMN ring_4 INT DEFAULT 0 AFTER ring_5,
            ADD COLUMN ring_3 INT DEFAULT 0 AFTER ring_4,
            ADD COLUMN ring_2 INT DEFAULT 0 AFTER ring_3,
            ADD COLUMN ring_1 INT DEFAULT 0 AFTER ring_2,
            ADD COLUMN jumlah_masuk INT DEFAULT 0 COMMENT "Total peluru masuk ring 1-10" AFTER ring_1,
            ADD COLUMN nilai INT DEFAULT 0 COMMENT "Total akumulasi nilai 1-10" AFTER jumlah_masuk;
        ');
        $results['add_ring_columns'] = 'OK';
    } else {
        $results['add_ring_columns'] = 'Already exist';
    }

    // Drop old seri columns if they still exist
    $dropCols = [];
    foreach (range(1, 10) as $i) {
        if (in_array('seri_' . $i, $cols)) {
            $dropCols[] = 'DROP COLUMN seri_' . $i;
        }
    }
    if (in_array('x_count', $cols)) {
        $dropCols[] = 'DROP COLUMN x_count';
    }
    if (in_array('total_score', $cols)) {
        $dropCols[] = 'DROP COLUMN total_score';
    }

    if (!empty($dropCols)) {
        $db->exec('ALTER TABLE scores_presisi ' . implode(', ', $dropCols));
        $results['drop_old_seri_columns'] = 'Dropped ' . count($dropCols) . ' old columns';
    } else {
        $results['drop_old_seri_columns'] = 'None to drop';
    }

    // Ensure index on nilai and ring_x
    try {
        $db->exec('ALTER TABLE scores_presisi DROP INDEX idx_total');
    } catch (Exception $e) {}

    try {
        $db->exec('ALTER TABLE scores_presisi ADD INDEX idx_nilai (nilai DESC, ring_x DESC)');
        $results['index_nilai'] = 'Created';
    } catch (Exception $e) {
        $results['index_nilai'] = 'Exists or skipped';
    }

    // 4. Expand registrations.kategori to VARCHAR(150)
    try {
        $db->exec('ALTER TABLE registrations MODIFY COLUMN kategori VARCHAR(150) NOT NULL');
        $results['registrations_kategori_expand'] = 'OK (VARCHAR(150))';
    } catch (Exception $e) {
        $results['registrations_kategori_expand'] = 'Failed: ' . $e->getMessage();
    }

    // 5. Add category column to dueling_matches if not exists
    try {
        $dmCols = $db->query('SHOW COLUMNS FROM dueling_matches')->fetchAll(PDO::FETCH_COLUMN);
        if (!in_array('category', $dmCols)) {
            $db->exec("ALTER TABLE dueling_matches ADD COLUMN category VARCHAR(50) DEFAULT 'umum' COMMENT 'umum atau bda' AFTER id");
            $results['dueling_matches_category'] = 'Added column category';
        } else {
            $results['dueling_matches_category'] = 'Already exists';
        }
    } catch (Exception $e) {
        $results['dueling_matches_category'] = 'Failed: ' . $e->getMessage();
    }

    // Get final table structures
    $adminCols = $db->query('SHOW COLUMNS FROM admin_users')->fetchAll(PDO::FETCH_ASSOC);
    $scoresCols = $db->query('SHOW COLUMNS FROM scores_presisi')->fetchAll(PDO::FETCH_ASSOC);
    $duelingCols = $db->query('SHOW COLUMNS FROM dueling_matches')->fetchAll(PDO::FETCH_ASSOC);

    jsonResponse([
        'success' => true,
        'message' => 'Migrasi Database Berhasil!',
        'results' => $results,
        'tables' => [
            'admin_users_columns' => array_column($adminCols, 'Field'),
            'scores_presisi_columns' => array_column($scoresCols, 'Field'),
            'dueling_matches_columns' => array_column($duelingCols, 'Field')
        ]
    ]);

} catch (PDOException $e) {
    jsonResponse(['success' => false, 'error' => 'Migration failed: ' . $e->getMessage()], 500);
}
