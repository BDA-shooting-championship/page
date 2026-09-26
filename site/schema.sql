-- BDA Shooting Championship 2026 — Database Schema
-- Ready to import directly into target database

-- Admin Users (Multi-User Leveled Access)
CREATE TABLE IF NOT EXISTS admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    display_name VARCHAR(100) NOT NULL,
    role ENUM('superadmin', 'admin') DEFAULT 'admin',
    permissions JSON NOT NULL COMMENT 'Array menu: ["antrean","peserta","scores","users"]',
    is_active TINYINT(1) DEFAULT 1,
    last_login TIMESTAMP NULL DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_username (username),
    INDEX idx_role (role)
) ENGINE=InnoDB;

-- Registrations table
CREATE TABLE IF NOT EXISTS registrations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    registration_id VARCHAR(20) NOT NULL UNIQUE,
    nama VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    telepon VARCHAR(20) NOT NULL,
    pangkat VARCHAR(50) NOT NULL,
    nrp VARCHAR(30) NOT NULL,
    satuan VARCHAR(100) NOT NULL,
    kategori VARCHAR(150) NOT NULL COMMENT 'presisi_umum, dueling_umum, dueling_bda, atau kombinasi',
    kta_filename VARCHAR(255) DEFAULT NULL,
    bukti_filename VARCHAR(255) DEFAULT NULL,
    status ENUM('Pending','Verified','Rejected') DEFAULT 'Pending',
    no_peserta VARCHAR(20) DEFAULT NULL COMMENT 'Auto: BSC-26001, set on verify',
    admin_notes TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_nrp (nrp),
    INDEX idx_status (status),
    INDEX idx_nama (nama),
    INDEX idx_satuan (satuan),
    INDEX idx_registration_id (registration_id),
    UNIQUE INDEX uq_no_peserta (no_peserta)
) ENGINE=InnoDB;

-- Presisi 20M Scores (Ring-Based System)
-- Ring: X | 10 | 9 | 8 | 7 | 6 | 5 | 4 | 3 | 2 | 1 | jumlah_masuk | nilai
-- Sesuai aturan panitia BSC 2026:
-- Peluru ring 10 diinput terpisah, X bernilai 0.1 poin tambahan (total 10.1).
-- X tidak dimasukkan ke dalam hitungan jumlah_masuk.
CREATE TABLE IF NOT EXISTS scores_presisi (
    id INT AUTO_INCREMENT PRIMARY KEY,
    registration_id VARCHAR(20) NOT NULL,
    no_peserta VARCHAR(20) DEFAULT NULL,
    nama VARCHAR(100) NOT NULL,
    satuan VARCHAR(100) DEFAULT NULL,
    ring_x INT DEFAULT 0 COMMENT 'Jumlah peluru masuk X (tambahan 0.1 nilai)',
    ring_10 INT DEFAULT 0 COMMENT 'Jumlah peluru masuk ring 10 (10 poin)',
    ring_9 INT DEFAULT 0 COMMENT 'Jumlah peluru masuk ring 9 (9 poin)',
    ring_8 INT DEFAULT 0 COMMENT 'Jumlah peluru masuk ring 8 (8 poin)',
    ring_7 INT DEFAULT 0 COMMENT 'Jumlah peluru masuk ring 7 (7 poin)',
    ring_6 INT DEFAULT 0 COMMENT 'Jumlah peluru masuk ring 6 (6 poin)',
    ring_5 INT DEFAULT 0 COMMENT 'Jumlah peluru masuk ring 5 (5 poin)',
    ring_4 INT DEFAULT 0 COMMENT 'Jumlah peluru masuk ring 4 (4 poin)',
    ring_3 INT DEFAULT 0 COMMENT 'Jumlah peluru masuk ring 3 (3 poin)',
    ring_2 INT DEFAULT 0 COMMENT 'Jumlah peluru masuk ring 2 (2 poin)',
    ring_1 INT DEFAULT 0 COMMENT 'Jumlah peluru masuk ring 1 (1 poin)',
    jumlah_masuk INT DEFAULT 0 COMMENT 'Total peluru masuk ring (sum 10..1, X tidak dihitung)',
    nilai DECIMAL(6,1) NOT NULL DEFAULT 0.0 COMMENT 'Total akumulasi skor: 10*ring_10 + ... + 1*ring_1 + (0.1*ring_x)',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE INDEX idx_reg_id (registration_id),
    INDEX idx_presisi_ranking (nilai DESC, ring_x DESC, jumlah_masuk DESC)
) ENGINE=InnoDB;

-- Dueling Plat Matches
CREATE TABLE IF NOT EXISTS dueling_matches (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category VARCHAR(50) DEFAULT 'umum' COMMENT 'umum (Kelas Individu Umum POLRI) atau bda (Kelas Khusus BDA Korbrimob)',
    round_name VARCHAR(50) NOT NULL COMMENT 'Babak 32 Besar, 16 Besar, Perempat Final, Semifinal, Perebutan Juara 3, Final',
    round_order INT DEFAULT 0 COMMENT 'For sorting: 1=32 Besar, 2=16 Besar, 3=Perempat, 4=Semi, 5=Juara3, 6=Final',
    match_number INT NOT NULL,
    participant_1_id VARCHAR(20) DEFAULT NULL,
    participant_1_name VARCHAR(100) DEFAULT NULL,
    participant_1_satuan VARCHAR(100) DEFAULT NULL,
    participant_2_id VARCHAR(20) DEFAULT NULL,
    participant_2_name VARCHAR(100) DEFAULT NULL,
    participant_2_satuan VARCHAR(100) DEFAULT NULL,
    time_1 DECIMAL(8,3) DEFAULT NULL COMMENT 'Waktu peserta 1 (detik)',
    time_2 DECIMAL(8,3) DEFAULT NULL COMMENT 'Waktu peserta 2 (detik)',
    winner_id VARCHAR(100) DEFAULT NULL,
    match_status ENUM('upcoming','live','finished') DEFAULT 'upcoming',
    next_match_id INT DEFAULT NULL COMMENT 'ID match berikutnya yang dituju oleh pemenang',
    next_slot TINYINT DEFAULT NULL COMMENT 'Slot di match berikutnya: 1=participant_1, 2=participant_2',
    loser_next_match_id INT DEFAULT NULL COMMENT 'ID match untuk peserta yang kalah (misal Perebutan Juara 3)',
    loser_next_slot TINYINT DEFAULT NULL COMMENT 'Slot peserta kalah: 1=participant_1, 2=participant_2',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_cat_round_match (category, round_order, match_number),
    INDEX idx_status (match_status)
) ENGINE=InnoDB;
