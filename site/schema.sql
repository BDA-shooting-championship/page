-- BDA Shooting Championship 2026 — Database Schema
-- Ready to import directly into target database

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
    kategori VARCHAR(50) NOT NULL COMMENT 'presisi, dueling, atau keduanya',
    kta_filename VARCHAR(255) DEFAULT NULL,
    bukti_filename VARCHAR(255) DEFAULT NULL,
    status ENUM('Pending','Verified','Rejected') DEFAULT 'Pending',
    no_peserta VARCHAR(20) DEFAULT NULL COMMENT 'Auto: BSC-001, set on verify',
    admin_notes TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_nrp (nrp),
    INDEX idx_status (status),
    INDEX idx_registration_id (registration_id),
    INDEX idx_no_peserta (no_peserta)
) ENGINE=InnoDB;

-- Presisi 20M Scores
CREATE TABLE IF NOT EXISTS scores_presisi (
    id INT AUTO_INCREMENT PRIMARY KEY,
    registration_id VARCHAR(20) NOT NULL,
    no_peserta VARCHAR(20) DEFAULT NULL,
    nama VARCHAR(100) NOT NULL,
    satuan VARCHAR(100) DEFAULT NULL,
    seri_1 INT DEFAULT 0,
    seri_2 INT DEFAULT 0,
    seri_3 INT DEFAULT 0,
    seri_4 INT DEFAULT 0,
    seri_5 INT DEFAULT 0,
    seri_6 INT DEFAULT 0,
    seri_7 INT DEFAULT 0,
    seri_8 INT DEFAULT 0,
    seri_9 INT DEFAULT 0,
    seri_10 INT DEFAULT 0,
    x_count INT DEFAULT 0 COMMENT 'Jumlah tembakan X (inner-10)',
    total_score INT DEFAULT 0 COMMENT 'Auto-calculated: sum seri_1..seri_10',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE INDEX idx_reg_id (registration_id),
    INDEX idx_total (total_score DESC, x_count DESC)
) ENGINE=InnoDB;

-- Dueling Plat Matches
CREATE TABLE IF NOT EXISTS dueling_matches (
    id INT AUTO_INCREMENT PRIMARY KEY,
    round_name VARCHAR(50) NOT NULL COMMENT 'Penyisihan, Perempat Final, Semifinal, Perebutan Juara 3, Final',
    round_order INT DEFAULT 0 COMMENT 'For sorting: 1=Penyisihan, 2=Perempat, 3=Semi, 4=Juara3, 5=Final',
    match_number INT NOT NULL,
    participant_1_id VARCHAR(20) DEFAULT NULL,
    participant_1_name VARCHAR(100) DEFAULT NULL,
    participant_1_satuan VARCHAR(100) DEFAULT NULL,
    participant_2_id VARCHAR(20) DEFAULT NULL,
    participant_2_name VARCHAR(100) DEFAULT NULL,
    participant_2_satuan VARCHAR(100) DEFAULT NULL,
    time_1 DECIMAL(8,3) DEFAULT NULL COMMENT 'Waktu peserta 1 (detik)',
    time_2 DECIMAL(8,3) DEFAULT NULL COMMENT 'Waktu peserta 2 (detik)',
    winner_id VARCHAR(20) DEFAULT NULL,
    match_status ENUM('upcoming','live','finished') DEFAULT 'upcoming',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_round (round_order, match_number),
    INDEX idx_status (match_status)
) ENGINE=InnoDB;
