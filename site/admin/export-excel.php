<?php
/**
 * BDA Shooting Championship 2026 — Export Data to Excel
 * Generates beautifully formatted XML Spreadsheet (SpreadsheetML) compatible with MS Excel, LibreOffice, and Google Sheets.
 * Supports multiple worksheets: Pendaftar & Peserta, Live Skor Presisi 20M, and Live Skor Dueling Plat.
 */
session_start();
require_once __DIR__ . '/../includes/db.php';

// Authentication: Token from GET or active Admin Session
$token = $_GET['token'] ?? $_SERVER['HTTP_X_ADMIN_TOKEN'] ?? '';
$isSessionAdmin = isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;

if ($token !== ADMIN_TOKEN && !$isSessionAdmin) {
    http_response_code(401);
    die('Unauthorized: Akses ditolak. Silakan login sebagai admin.');
}

$db = getDB();
$scope = $_GET['scope'] ?? 'all'; // 'all', 'antrean', 'peserta', 'presisi', 'dueling'
$timestamp = date('Ymd_His');
$filename = 'BSC2026_DataExport_' . $scope . '_' . $timestamp . '.xls';

function xmlEscape($value): string {
    if ($value === null || $value === false) return '';
    return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

// Fetch Registrations
$stmtReg = $db->query('SELECT * FROM registrations ORDER BY id ASC');
$registrations = $stmtReg ? $stmtReg->fetchAll(PDO::FETCH_ASSOC) : [];

// Fetch Presisi Scores
$stmtPresisi = $db->query('
    SELECT sp.*, r.kategori 
    FROM scores_presisi sp
    LEFT JOIN registrations r ON sp.registration_id = r.registration_id
    ORDER BY sp.total_score DESC, sp.x_count DESC
');
$presisiScores = $stmtPresisi ? $stmtPresisi->fetchAll(PDO::FETCH_ASSOC) : [];

// Fetch Dueling Matches
$stmtDueling = $db->query('
    SELECT * FROM dueling_matches 
    ORDER BY round_order ASC, match_number ASC
');
$duelingMatches = $stmtDueling ? $stmtDueling->fetchAll(PDO::FETCH_ASSOC) : [];

// Set headers for Excel download
header('Content-Type: application/vnd.ms-excel; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Cache-Control: max-age=0');
header('Pragma: public');

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
echo '<?mso-application progid="Excel.Sheet"?>' . "\n";
?>
<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"
 xmlns:o="urn:schemas-microsoft-com:office:office"
 xmlns:x="urn:schemas-microsoft-com:office:excel"
 xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet"
 xmlns:html="http://www.w3.org/TR/REC-html40">
 <DocumentProperties xmlns="urn:schemas-microsoft-com:office:office">
  <Title>Data BDA Shooting Championship 2026</Title>
  <Author>Panitia Pelaksana BSC 2026</Author>
  <Created><?= date('Y-m-d\TH:i:s\Z') ?></Created>
 </DocumentProperties>
 <Styles>
  <Style ss:ID="Default" ss:Name="Normal">
   <Alignment ss:Vertical="Center"/>
   <Borders/>
   <Font ss:FontName="Segoe UI" ss:Size="10" ss:Color="#1F2937"/>
   <Interior/>
   <NumberFormat/>
   <Protection/>
  </Style>
  <!-- Main Title Style -->
  <Style ss:ID="HeaderTitle">
   <Alignment ss:Horizontal="Left" ss:Vertical="Center"/>
   <Font ss:FontName="Segoe UI" ss:Size="14" ss:Bold="1" ss:Color="#92400E"/>
  </Style>
  <Style ss:ID="HeaderSub">
   <Alignment ss:Horizontal="Left" ss:Vertical="Center"/>
   <Font ss:FontName="Segoe UI" ss:Size="9" ss:Italic="1" ss:Color="#6B7280"/>
  </Style>
  <!-- Table Header Style -->
  <Style ss:ID="ColHeader">
   <Alignment ss:Horizontal="Center" ss:Vertical="Center" ss:WrapText="1"/>
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#4B5563"/>
    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#4B5563"/>
    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#4B5563"/>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#4B5563"/>
   </Borders>
   <Font ss:FontName="Segoe UI" ss:Size="10" ss:Bold="1" ss:Color="#FFFFFF"/>
   <Interior ss:Color="#92400E" ss:Pattern="Solid"/>
  </Style>
  <Style ss:ID="ColHeaderBlue">
   <Alignment ss:Horizontal="Center" ss:Vertical="Center" ss:WrapText="1"/>
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#1E3A8A"/>
    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#1E3A8A"/>
    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#1E3A8A"/>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#1E3A8A"/>
   </Borders>
   <Font ss:FontName="Segoe UI" ss:Size="10" ss:Bold="1" ss:Color="#FFFFFF"/>
   <Interior ss:Color="#1E3A8A" ss:Pattern="Solid"/>
  </Style>
  <!-- Regular Cell Styles -->
  <Style ss:ID="CellText">
   <Alignment ss:Vertical="Center"/>
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E5E7EB"/>
    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E5E7EB"/>
    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E5E7EB"/>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E5E7EB"/>
   </Borders>
   <Font ss:FontName="Segoe UI" ss:Size="9.5"/>
  </Style>
  <Style ss:ID="CellCenter">
   <Alignment ss:Horizontal="Center" ss:Vertical="Center"/>
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E5E7EB"/>
    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E5E7EB"/>
    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E5E7EB"/>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E5E7EB"/>
   </Borders>
   <Font ss:FontName="Segoe UI" ss:Size="9.5"/>
  </Style>
  <Style ss:ID="CellBoldCenter">
   <Alignment ss:Horizontal="Center" ss:Vertical="Center"/>
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E5E7EB"/>
    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E5E7EB"/>
    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E5E7EB"/>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E5E7EB"/>
   </Borders>
   <Font ss:FontName="Segoe UI" ss:Size="9.5" ss:Bold="1"/>
  </Style>
  <Style ss:ID="CellBadgePending">
   <Alignment ss:Horizontal="Center" ss:Vertical="Center"/>
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E5E7EB"/>
    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E5E7EB"/>
    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E5E7EB"/>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E5E7EB"/>
   </Borders>
   <Font ss:FontName="Segoe UI" ss:Size="9.5" ss:Bold="1" ss:Color="#B45309"/>
   <Interior ss:Color="#FEF3C7" ss:Pattern="Solid"/>
  </Style>
  <Style ss:ID="CellBadgeVerified">
   <Alignment ss:Horizontal="Center" ss:Vertical="Center"/>
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E5E7EB"/>
    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E5E7EB"/>
    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E5E7EB"/>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E5E7EB"/>
   </Borders>
   <Font ss:FontName="Segoe UI" ss:Size="9.5" ss:Bold="1" ss:Color="#047857"/>
   <Interior ss:Color="#D1FAE5" ss:Pattern="Solid"/>
  </Style>
  <Style ss:ID="CellBadgeRejected">
   <Alignment ss:Horizontal="Center" ss:Vertical="Center"/>
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E5E7EB"/>
    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E5E7EB"/>
    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E5E7EB"/>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E5E7EB"/>
   </Borders>
   <Font ss:FontName="Segoe UI" ss:Size="9.5" ss:Bold="1" ss:Color="#B91C1C"/>
   <Interior ss:Color="#FEE2E2" ss:Pattern="Solid"/>
  </Style>
  <Style ss:ID="CellNumber">
   <Alignment ss:Horizontal="Right" ss:Vertical="Center"/>
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E5E7EB"/>
    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E5E7EB"/>
    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E5E7EB"/>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E5E7EB"/>
   </Borders>
   <Font ss:FontName="Segoe UI" ss:Size="9.5"/>
   <NumberFormat ss:Format="#,##0"/>
  </Style>
  <Style ss:ID="CellTotalScore">
   <Alignment ss:Horizontal="Center" ss:Vertical="Center"/>
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#D97706"/>
    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#D97706"/>
    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#D97706"/>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#D97706"/>
   </Borders>
   <Font ss:FontName="Segoe UI" ss:Size="10" ss:Bold="1" ss:Color="#92400E"/>
   <Interior ss:Color="#FEF3C7" ss:Pattern="Solid"/>
   <NumberFormat ss:Format="0"/>
  </Style>
 </Styles>

<?php if ($scope === 'all' || $scope === 'antrean' || $scope === 'peserta'): ?>
 <!-- ================= SHEET 1: DATA PENDAFTAR & PESERTA ================= -->
 <Worksheet ss:Name="Data Pendaftar &amp; Peserta">
  <Table ss:DefaultRowHeight="20">
   <Column ss:Width="35"/>   <!-- No -->
   <Column ss:Width="100"/>  <!-- No Peserta -->
   <Column ss:Width="105"/>  <!-- ID Registrasi -->
   <Column ss:Width="160"/>  <!-- Nama -->
   <Column ss:Width="110"/>  <!-- Pangkat -->
   <Column ss:Width="95"/>   <!-- NRP -->
   <Column ss:Width="150"/>  <!-- Satuan -->
   <Column ss:Width="100"/>  <!-- Kategori -->
   <Column ss:Width="85"/>   <!-- Status -->
   <Column ss:Width="115"/>  <!-- Telepon -->
   <Column ss:Width="140"/>  <!-- Email -->
   <Column ss:Width="220"/>  <!-- Link E-Ticket -->
   <Column ss:Width="120"/>  <!-- Tanggal Daftar -->
   <Column ss:Width="160"/>  <!-- Catatan Admin -->

   <!-- Title Row -->
   <Row ss:Height="26">
    <Cell ss:MergeAcross="13" ss:StyleID="HeaderTitle">
     <Data ss:Type="String">BDA SHOOTING CHAMPIONSHIP 2026 — REKAPITULASI PENDAFTAR &amp; PESERTA</Data>
    </Cell>
   </Row>
   <Row ss:Height="18">
    <Cell ss:MergeAcross="13" ss:StyleID="HeaderSub">
     <Data ss:Type="String">Waktu Ekspor: <?= date('d M Y H:i:s') ?> WIB | Total Data: <?= count($registrations) ?> Baris</Data>
    </Cell>
   </Row>
   <Row ss:Height="10"/>

   <!-- Table Header Row -->
   <Row ss:Height="24">
    <Cell ss:StyleID="ColHeader"><Data ss:Type="String">No</Data></Cell>
    <Cell ss:StyleID="ColHeader"><Data ss:Type="String">No. Peserta</Data></Cell>
    <Cell ss:StyleID="ColHeader"><Data ss:Type="String">ID Registrasi</Data></Cell>
    <Cell ss:StyleID="ColHeader"><Data ss:Type="String">Nama Lengkap</Data></Cell>
    <Cell ss:StyleID="ColHeader"><Data ss:Type="String">Pangkat</Data></Cell>
    <Cell ss:StyleID="ColHeader"><Data ss:Type="String">NRP / NIK</Data></Cell>
    <Cell ss:StyleID="ColHeader"><Data ss:Type="String">Satuan / Club</Data></Cell>
    <Cell ss:StyleID="ColHeader"><Data ss:Type="String">Kategori Lomba</Data></Cell>
    <Cell ss:StyleID="ColHeader"><Data ss:Type="String">Status</Data></Cell>
    <Cell ss:StyleID="ColHeader"><Data ss:Type="String">No. Telepon/WA</Data></Cell>
    <Cell ss:StyleID="ColHeader"><Data ss:Type="String">Email</Data></Cell>
    <Cell ss:StyleID="ColHeader"><Data ss:Type="String">Link E-Ticket Resmi</Data></Cell>
    <Cell ss:StyleID="ColHeader"><Data ss:Type="String">Tanggal Pendaftaran</Data></Cell>
    <Cell ss:StyleID="ColHeader"><Data ss:Type="String">Catatan Admin</Data></Cell>
   </Row>

   <!-- Data Rows -->
   <?php
   $no = 1;
   foreach ($registrations as $r):
       if ($scope === 'antrean' && strtolower($r['status']) !== 'pending') continue;
       if ($scope === 'peserta' && strtolower($r['status']) !== 'verified') continue;

       $statusStyle = 'CellCenter';
       $st = strtolower($r['status']);
       if ($st === 'verified') $statusStyle = 'CellBadgeVerified';
       elseif ($st === 'pending') $statusStyle = 'CellBadgePending';
       elseif ($st === 'rejected') $statusStyle = 'CellBadgeRejected';

       $ticketLink = SITE_URL . '/e-ticket.php?id=' . urlencode($r['registration_id']);
   ?>
   <Row ss:Height="21">
    <Cell ss:StyleID="CellCenter"><Data ss:Type="Number"><?= $no++ ?></Data></Cell>
    <Cell ss:StyleID="CellBoldCenter"><Data ss:Type="String"><?= xmlEscape($r['no_peserta'] ?: '-') ?></Data></Cell>
    <Cell ss:StyleID="CellCenter"><Data ss:Type="String"><?= xmlEscape($r['registration_id']) ?></Data></Cell>
    <Cell ss:StyleID="CellText"><Data ss:Type="String"><?= xmlEscape($r['nama']) ?></Data></Cell>
    <Cell ss:StyleID="CellText"><Data ss:Type="String"><?= xmlEscape($r['pangkat']) ?></Data></Cell>
    <Cell ss:StyleID="CellCenter"><Data ss:Type="String"><?= xmlEscape($r['nrp']) ?></Data></Cell>
    <Cell ss:StyleID="CellText"><Data ss:Type="String"><?= xmlEscape($r['satuan']) ?></Data></Cell>
    <Cell ss:StyleID="CellCenter"><Data ss:Type="String"><?= xmlEscape(ucwords($r['kategori'])) ?></Data></Cell>
    <Cell ss:StyleID="<?= $statusStyle ?>"><Data ss:Type="String"><?= xmlEscape(ucfirst($r['status'])) ?></Data></Cell>
    <Cell ss:StyleID="CellCenter"><Data ss:Type="String"><?= xmlEscape($r['telepon']) ?></Data></Cell>
    <Cell ss:StyleID="CellText"><Data ss:Type="String"><?= xmlEscape($r['email']) ?></Data></Cell>
    <Cell ss:StyleID="CellText"><Data ss:Type="String"><?= xmlEscape($ticketLink) ?></Data></Cell>
    <Cell ss:StyleID="CellCenter"><Data ss:Type="String"><?= xmlEscape($r['created_at']) ?></Data></Cell>
    <Cell ss:StyleID="CellText"><Data ss:Type="String"><?= xmlEscape($r['admin_notes']) ?></Data></Cell>
   </Row>
   <?php endforeach; ?>
  </Table>
 </Worksheet>
<?php endif; ?>

<?php if ($scope === 'all' || $scope === 'presisi' || $scope === 'scores'): ?>
 <!-- ================= SHEET 2: SKOR PRESISI 20M ================= -->
 <Worksheet ss:Name="Skor Presisi 20M">
  <Table ss:DefaultRowHeight="20">
   <Column ss:Width="40"/>  <!-- Rank -->
   <Column ss:Width="95"/>  <!-- No Peserta -->
   <Column ss:Width="160"/> <!-- Nama -->
   <Column ss:Width="150"/> <!-- Satuan -->
   <Column ss:Width="38"/>  <!-- S1 -->
   <Column ss:Width="38"/>  <!-- S2 -->
   <Column ss:Width="38"/>  <!-- S3 -->
   <Column ss:Width="38"/>  <!-- S4 -->
   <Column ss:Width="38"/>  <!-- S5 -->
   <Column ss:Width="38"/>  <!-- S6 -->
   <Column ss:Width="38"/>  <!-- S7 -->
   <Column ss:Width="38"/>  <!-- S8 -->
   <Column ss:Width="38"/>  <!-- S9 -->
   <Column ss:Width="38"/>  <!-- S10 -->
   <Column ss:Width="50"/>  <!-- Jml X -->
   <Column ss:Width="65"/>  <!-- Total Skor -->

   <Row ss:Height="26">
    <Cell ss:MergeAcross="15" ss:StyleID="HeaderTitle">
     <Data ss:Type="String">BDA SHOOTING CHAMPIONSHIP 2026 — PAPAN SKOR PISTOL PRESISI 20M</Data>
    </Cell>
   </Row>
   <Row ss:Height="18">
    <Cell ss:MergeAcross="15" ss:StyleID="HeaderSub">
     <Data ss:Type="String">Waktu Ekspor: <?= date('d M Y H:i:s') ?> WIB | Total Peserta Dinilai: <?= count($presisiScores) ?></Data>
    </Cell>
   </Row>
   <Row ss:Height="10"/>

   <!-- Table Header Row -->
   <Row ss:Height="24">
    <Cell ss:StyleID="ColHeader"><Data ss:Type="String">Rank</Data></Cell>
    <Cell ss:StyleID="ColHeader"><Data ss:Type="String">No. Peserta</Data></Cell>
    <Cell ss:StyleID="ColHeader"><Data ss:Type="String">Nama Peserta</Data></Cell>
    <Cell ss:StyleID="ColHeader"><Data ss:Type="String">Satuan / Club</Data></Cell>
    <Cell ss:StyleID="ColHeader"><Data ss:Type="String">S1</Data></Cell>
    <Cell ss:StyleID="ColHeader"><Data ss:Type="String">S2</Data></Cell>
    <Cell ss:StyleID="ColHeader"><Data ss:Type="String">S3</Data></Cell>
    <Cell ss:StyleID="ColHeader"><Data ss:Type="String">S4</Data></Cell>
    <Cell ss:StyleID="ColHeader"><Data ss:Type="String">S5</Data></Cell>
    <Cell ss:StyleID="ColHeader"><Data ss:Type="String">S6</Data></Cell>
    <Cell ss:StyleID="ColHeader"><Data ss:Type="String">S7</Data></Cell>
    <Cell ss:StyleID="ColHeader"><Data ss:Type="String">S8</Data></Cell>
    <Cell ss:StyleID="ColHeader"><Data ss:Type="String">S9</Data></Cell>
    <Cell ss:StyleID="ColHeader"><Data ss:Type="String">S10</Data></Cell>
    <Cell ss:StyleID="ColHeader"><Data ss:Type="String">Jml X</Data></Cell>
    <Cell ss:StyleID="ColHeader"><Data ss:Type="String">Total</Data></Cell>
   </Row>

   <!-- Data Rows -->
   <?php
   $rank = 1;
   foreach ($presisiScores as $sp):
   ?>
   <Row ss:Height="21">
    <Cell ss:StyleID="CellBoldCenter"><Data ss:Type="Number"><?= $rank++ ?></Data></Cell>
    <Cell ss:StyleID="CellBoldCenter"><Data ss:Type="String"><?= xmlEscape($sp['no_peserta'] ?: '-') ?></Data></Cell>
    <Cell ss:StyleID="CellText"><Data ss:Type="String"><?= xmlEscape($sp['nama']) ?></Data></Cell>
    <Cell ss:StyleID="CellText"><Data ss:Type="String"><?= xmlEscape($sp['satuan']) ?></Data></Cell>
    <Cell ss:StyleID="CellCenter"><Data ss:Type="Number"><?= (int)$sp['seri_1'] ?></Data></Cell>
    <Cell ss:StyleID="CellCenter"><Data ss:Type="Number"><?= (int)$sp['seri_2'] ?></Data></Cell>
    <Cell ss:StyleID="CellCenter"><Data ss:Type="Number"><?= (int)$sp['seri_3'] ?></Data></Cell>
    <Cell ss:StyleID="CellCenter"><Data ss:Type="Number"><?= (int)$sp['seri_4'] ?></Data></Cell>
    <Cell ss:StyleID="CellCenter"><Data ss:Type="Number"><?= (int)$sp['seri_5'] ?></Data></Cell>
    <Cell ss:StyleID="CellCenter"><Data ss:Type="Number"><?= (int)$sp['seri_6'] ?></Data></Cell>
    <Cell ss:StyleID="CellCenter"><Data ss:Type="Number"><?= (int)$sp['seri_7'] ?></Data></Cell>
    <Cell ss:StyleID="CellCenter"><Data ss:Type="Number"><?= (int)$sp['seri_8'] ?></Data></Cell>
    <Cell ss:StyleID="CellCenter"><Data ss:Type="Number"><?= (int)$sp['seri_9'] ?></Data></Cell>
    <Cell ss:StyleID="CellCenter"><Data ss:Type="Number"><?= (int)$sp['seri_10'] ?></Data></Cell>
    <Cell ss:StyleID="CellBoldCenter"><Data ss:Type="Number"><?= (int)$sp['x_count'] ?></Data></Cell>
    <Cell ss:StyleID="CellTotalScore"><Data ss:Type="Number"><?= (int)$sp['total_score'] ?></Data></Cell>
   </Row>
   <?php endforeach; ?>
  </Table>
 </Worksheet>
<?php endif; ?>

<?php if ($scope === 'all' || $scope === 'dueling' || $scope === 'scores'): ?>
 <!-- ================= SHEET 3: DUELING PLAT ================= -->
 <Worksheet ss:Name="Bagan Dueling Plat">
  <Table ss:DefaultRowHeight="20">
   <Column ss:Width="110"/> <!-- Babak -->
   <Column ss:Width="65"/>  <!-- Match # -->
   <Column ss:Width="140"/> <!-- Peserta 1 -->
   <Column ss:Width="80"/>  <!-- Waktu 1 -->
   <Column ss:Width="140"/> <!-- Peserta 2 -->
   <Column ss:Width="80"/>  <!-- Waktu 2 -->
   <Column ss:Width="140"/> <!-- Pemenang -->
   <Column ss:Width="95"/>  <!-- Status Match -->

   <Row ss:Height="26">
    <Cell ss:MergeAcross="7" ss:StyleID="HeaderTitle">
     <Data ss:Type="String">BDA SHOOTING CHAMPIONSHIP 2026 — BAGAN PERTANDINGAN DUELING PLAT</Data>
    </Cell>
   </Row>
   <Row ss:Height="18">
    <Cell ss:MergeAcross="7" ss:StyleID="HeaderSub">
     <Data ss:Type="String">Waktu Ekspor: <?= date('d M Y H:i:s') ?> WIB | Total Match: <?= count($duelingMatches) ?></Data>
    </Cell>
   </Row>
   <Row ss:Height="10"/>

   <!-- Table Header Row -->
   <Row ss:Height="24">
    <Cell ss:StyleID="ColHeaderBlue"><Data ss:Type="String">Babak</Data></Cell>
    <Cell ss:StyleID="ColHeaderBlue"><Data ss:Type="String">Match #</Data></Cell>
    <Cell ss:StyleID="ColHeaderBlue"><Data ss:Type="String">Peserta 1</Data></Cell>
    <Cell ss:StyleID="ColHeaderBlue"><Data ss:Type="String">Waktu 1 (dtk)</Data></Cell>
    <Cell ss:StyleID="ColHeaderBlue"><Data ss:Type="String">Peserta 2</Data></Cell>
    <Cell ss:StyleID="ColHeaderBlue"><Data ss:Type="String">Waktu 2 (dtk)</Data></Cell>
    <Cell ss:StyleID="ColHeaderBlue"><Data ss:Type="String">Pemenang</Data></Cell>
    <Cell ss:StyleID="ColHeaderBlue"><Data ss:Type="String">Status</Data></Cell>
   </Row>

   <!-- Data Rows -->
   <?php
   foreach ($duelingMatches as $dm):
       $p1 = $dm['participant_1_name'] ?: '-';
       $p2 = $dm['participant_2_name'] ?: '-';
       $w = '-';
       if (!empty($dm['winner_id'])) {
           if ($dm['winner_id'] == $dm['participant_1_id'] || $dm['winner_id'] === $p1) {
               $w = $p1;
           } elseif ($dm['winner_id'] == $dm['participant_2_id'] || $dm['winner_id'] === $p2) {
               $w = $p2;
           } else {
               $w = $dm['winner_id'];
           }
       }
   ?>
   <Row ss:Height="21">
    <Cell ss:StyleID="CellBoldCenter"><Data ss:Type="String"><?= xmlEscape($dm['round_name']) ?></Data></Cell>
    <Cell ss:StyleID="CellCenter"><Data ss:Type="Number"><?= (int)$dm['match_number'] ?></Data></Cell>
    <Cell ss:StyleID="CellText"><Data ss:Type="String"><?= xmlEscape($p1) ?></Data></Cell>
    <Cell ss:StyleID="CellCenter"><Data ss:Type="String"><?= $dm['time_1'] !== null ? number_format((float)$dm['time_1'], 3) : '-' ?></Data></Cell>
    <Cell ss:StyleID="CellText"><Data ss:Type="String"><?= xmlEscape($p2) ?></Data></Cell>
    <Cell ss:StyleID="CellCenter"><Data ss:Type="String"><?= $dm['time_2'] !== null ? number_format((float)$dm['time_2'], 3) : '-' ?></Data></Cell>
    <Cell ss:StyleID="CellBoldCenter"><Data ss:Type="String"><?= xmlEscape($w) ?></Data></Cell>
    <Cell ss:StyleID="CellCenter"><Data ss:Type="String"><?= xmlEscape(strtoupper($dm['match_status'])) ?></Data></Cell>
   </Row>
   <?php endforeach; ?>
  </Table>
 </Worksheet>
<?php endif; ?>

</Workbook>
