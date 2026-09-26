<?php
/**
 * BDA Shooting Championship 2026 — Export Data to Excel
 * Generates beautifully formatted XML Spreadsheet (SpreadsheetML) compatible with MS Excel, LibreOffice, and Google Sheets.
 * Also supports direct CSV download via ?format=csv parameter.
 * Supports multiple worksheets: Pendaftar & Peserta, Live Skor Presisi 20M, and Live Skor Dueling Plat.
 */

// Discard any accidental whitespace or buffered output
while (ob_get_level()) {
    ob_end_clean();
}
ob_start();

session_start();
require_once __DIR__ . '/../includes/db.php';

// Authentication: Token from GET or active Admin Session
$token = $_GET['token'] ?? $_SERVER['HTTP_X_ADMIN_TOKEN'] ?? '';
$isSessionAdmin = isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;

if ($token !== ADMIN_TOKEN && !$isSessionAdmin) {
    http_response_code(401);
    die('Unauthorized: Akses ditolak. Silakan login sebagai admin.');
}

$allowedScopes = ['all', 'antrean', 'peserta', 'presisi', 'dueling', 'scores'];
$rawScope = strtolower($_GET['scope'] ?? 'all');
$scope = in_array($rawScope, $allowedScopes, true) ? $rawScope : 'all';
$format = strtolower($_GET['format'] ?? 'xls'); // 'xls' (SpreadsheetML) or 'csv'
$timestamp = date('Ymd_His');

function xmlEscape($value): string {
    if ($value === null || $value === false) return '';
    $str = (string)$value;
    // Strip invalid XML 1.0 control characters
    $str = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $str);
    return htmlspecialchars($str, ENT_QUOTES | ENT_SUBSTITUTE | ENT_XML1, 'UTF-8');
}

// CWE-1236 Formula Injection sanitization for CSV
function safeCsvRow(array $row): array {
    return array_map(function($val) {
        if ($val === null) return '';
        $str = (string)$val;
        if (isset($str[0]) && in_array($str[0], ['=', '+', '-', '@', "\t", "\r"], true)) {
            return "'" . $str;
        }
        return $str;
    }, $row);
}

// Fetch Registrations
$stmtReg = $db->query('SELECT * FROM registrations ORDER BY id ASC');
$registrations = $stmtReg ? $stmtReg->fetchAll(PDO::FETCH_ASSOC) : [];

// Fetch Presisi Scores
$stmtPresisi = $db->query('
    SELECT sp.*, r.kategori 
    FROM scores_presisi sp
    LEFT JOIN registrations r ON sp.registration_id = r.registration_id
    ORDER BY sp.nilai DESC, sp.ring_x DESC, sp.jumlah_masuk DESC
');
$presisiScores = $stmtPresisi ? $stmtPresisi->fetchAll(PDO::FETCH_ASSOC) : [];

// Fetch Dueling Matches
$stmtDueling = $db->query('
    SELECT * FROM dueling_matches 
    ORDER BY category ASC, round_order ASC, match_number ASC
');
$duelingMatches = $stmtDueling ? $stmtDueling->fetchAll(PDO::FETCH_ASSOC) : [];

// Filtered registrations for scope
$filteredRegistrations = [];
foreach ($registrations as $r) {
    if ($scope === 'antrean' && strtolower($r['status']) !== 'pending') continue;
    if ($scope === 'peserta' && strtolower($r['status']) !== 'verified') continue;
    $filteredRegistrations[] = $r;
}

// -------------------------------------------------------------
// CSV EXPORT HANDLER
// -------------------------------------------------------------
if ($format === 'csv') {
    $csvFilename = 'BSC2026_DataExport_' . preg_replace('/[^a-zA-Z0-9_-]/', '', $scope) . '_' . $timestamp . '.csv';
    
    // Clear any previous output
    if (ob_get_length()) ob_clean();

    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $csvFilename . '"');
    header('Cache-Control: max-age=0, no-cache, must-revalidate');
    header('Pragma: public');

    // Output UTF-8 BOM for CSV so Excel opens special characters correctly
    echo "\xEF\xBB\xBF";
    
    $out = fopen('php://output', 'w');

    if ($scope === 'all' || $scope === 'antrean' || $scope === 'peserta') {
        fputcsv($out, [
            'No', 'No. Peserta', 'ID Registrasi', 'Nama Lengkap', 'Pangkat', 'NRP',
            'Kesatuan / Club', 'Kategori Lomba', 'Status', 'No. Telepon/WA', 'Email',
            'Link E-Ticket', 'Tanggal Pendaftaran', 'Catatan Admin'
        ]);
        $no = 1;
        foreach ($filteredRegistrations as $r) {
            $ticketLink = SITE_URL . '/e-ticket.php?id=' . urlencode($r['registration_id']);
            fputcsv($out, safeCsvRow([
                $no++,
                $r['no_peserta'] ?: '-',
                $r['registration_id'],
                $r['nama'],
                $r['pangkat'],
                $r['nrp'],
                $r['satuan'],
                $r['kategori'],
                ucfirst($r['status']),
                $r['telepon'],
                $r['email'],
                $ticketLink,
                $r['created_at'],
                $r['admin_notes']
            ]));
        }
    } elseif ($scope === 'presisi' || $scope === 'scores') {
        fputcsv($out, [
            'Rank', 'No. Peserta', 'Nama Peserta', 'Kesatuan / Club',
            'X', '10', '9', '8', '7', '6', '5', '4', '3', '2', '1',
            'Jumlah Masuk', 'Nilai Total'
        ]);
        $rank = 1;
        foreach ($presisiScores as $sp) {
            fputcsv($out, safeCsvRow([
                $rank++,
                $sp['no_peserta'] ?: '-',
                $sp['nama'],
                $sp['satuan'],
                (int)($sp['ring_x'] ?? 0),
                (int)($sp['ring_10'] ?? 0),
                (int)($sp['ring_9'] ?? 0),
                (int)($sp['ring_8'] ?? 0),
                (int)($sp['ring_7'] ?? 0),
                (int)($sp['ring_6'] ?? 0),
                (int)($sp['ring_5'] ?? 0),
                (int)($sp['ring_4'] ?? 0),
                (int)($sp['ring_3'] ?? 0),
                (int)($sp['ring_2'] ?? 0),
                (int)($sp['ring_1'] ?? 0),
                (int)($sp['jumlah_masuk'] ?? 0),
                round((float)($sp['nilai'] ?? 0), 1)
            ]));
        }
    } elseif ($scope === 'dueling') {
        fputcsv($out, [
            'Kategori / Kelas', 'Babak', 'Match #', 'Peserta 1', 'Waktu 1 (dtk)', 'Peserta 2', 'Waktu 2 (dtk)', 'Pemenang', 'Status'
        ]);
        foreach ($duelingMatches as $dm) {
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
            $catLabel = (isset($dm['category']) && strtolower($dm['category']) === 'bda') ? 'Khusus BDA' : 'Umum POLRI';
            fputcsv($out, safeCsvRow([
                $catLabel,
                $dm['round_name'],
                (int)$dm['match_number'],
                $p1,
                $dm['time_1'] !== null ? number_format((float)$dm['time_1'], 3) : '-',
                $p2,
                $dm['time_2'] !== null ? number_format((float)$dm['time_2'], 3) : '-',
                $w,
                strtoupper($dm['match_status'])
            ]));
        }
    }
    fclose($out);
    exit;
}

// -------------------------------------------------------------
// SPREADSHEETML (EXCEL XML) EXPORT HANDLER
// -------------------------------------------------------------
$filename = 'BSC2026_DataExport_' . $scope . '_' . $timestamp . '.xls';

// Clear any accidental output in buffer before sending XML
if (ob_get_length()) ob_clean();

// Set headers for Excel download
header('Content-Type: application/vnd.ms-excel; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate');
header('Pragma: public');

// Output strictly starting at byte 0 with <?xml (NO BOM!)
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
  <Created><?= gmdate('Y-m-d\TH:i:s\Z') ?></Created>
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
   <NumberFormat ss:Format="0.0"/>
  </Style>
  <Style ss:ID="CellEmptyNotice">
   <Alignment ss:Horizontal="Center" ss:Vertical="Center"/>
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E5E7EB"/>
    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E5E7EB"/>
    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E5E7EB"/>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E5E7EB"/>
   </Borders>
   <Font ss:FontName="Segoe UI" ss:Size="10" ss:Italic="1" ss:Color="#9CA3AF"/>
   <Interior ss:Color="#F9FAFB" ss:Pattern="Solid"/>
  </Style>
 </Styles>

<?php if ($scope === 'all' || $scope === 'antrean' || $scope === 'peserta'): ?>
 <!-- ================= SHEET 1: DATA PENDAFTAR & PESERTA ================= -->
 <Worksheet ss:Name="Pendaftar dan Peserta">
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
     <Data ss:Type="String">Waktu Ekspor: <?= date('d M Y H:i:s') ?> WIB | Total Data: <?= count($filteredRegistrations) ?> Baris</Data>
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
    <Cell ss:StyleID="ColHeader"><Data ss:Type="String">NRP</Data></Cell>
    <Cell ss:StyleID="ColHeader"><Data ss:Type="String">Kesatuan / Club</Data></Cell>
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
   if (empty($filteredRegistrations)):
   ?>
   <Row ss:Height="26">
    <Cell ss:MergeAcross="13" ss:StyleID="CellEmptyNotice">
     <Data ss:Type="String">Belum ada data pendaftar atau peserta terdaftar.</Data>
    </Cell>
   </Row>
   <?php
   else:
   $no = 1;
   foreach ($filteredRegistrations as $r):
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
    <Cell ss:StyleID="CellCenter"><Data ss:Type="String"><?= xmlEscape($r['kategori']) ?></Data></Cell>
    <Cell ss:StyleID="<?= $statusStyle ?>"><Data ss:Type="String"><?= xmlEscape(ucfirst($r['status'])) ?></Data></Cell>
    <Cell ss:StyleID="CellCenter"><Data ss:Type="String"><?= xmlEscape($r['telepon']) ?></Data></Cell>
    <Cell ss:StyleID="CellText"><Data ss:Type="String"><?= xmlEscape($r['email']) ?></Data></Cell>
    <Cell ss:StyleID="CellText"><Data ss:Type="String"><?= xmlEscape($ticketLink) ?></Data></Cell>
    <Cell ss:StyleID="CellCenter"><Data ss:Type="String"><?= xmlEscape($r['created_at']) ?></Data></Cell>
    <Cell ss:StyleID="CellText"><Data ss:Type="String"><?= xmlEscape($r['admin_notes']) ?></Data></Cell>
   </Row>
   <?php endforeach; endif; ?>
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
   <Column ss:Width="38"/>  <!-- X -->
   <Column ss:Width="38"/>  <!-- 10 -->
   <Column ss:Width="38"/>  <!-- 9 -->
   <Column ss:Width="38"/>  <!-- 8 -->
   <Column ss:Width="38"/>  <!-- 7 -->
   <Column ss:Width="38"/>  <!-- 6 -->
   <Column ss:Width="38"/>  <!-- 5 -->
   <Column ss:Width="38"/>  <!-- 4 -->
   <Column ss:Width="38"/>  <!-- 3 -->
   <Column ss:Width="38"/>  <!-- 2 -->
   <Column ss:Width="38"/>  <!-- 1 -->
   <Column ss:Width="65"/>  <!-- Jml Masuk -->
   <Column ss:Width="70"/>  <!-- Nilai -->

   <Row ss:Height="26">
    <Cell ss:MergeAcross="16" ss:StyleID="HeaderTitle">
     <Data ss:Type="String">BDA SHOOTING CHAMPIONSHIP 2026 — PAPAN SKOR PISTOL PRESISI 20M</Data>
    </Cell>
   </Row>
   <Row ss:Height="18">
    <Cell ss:MergeAcross="16" ss:StyleID="HeaderSub">
     <Data ss:Type="String">Waktu Ekspor: <?= date('d M Y H:i:s') ?> WIB | Total Peserta Dinilai: <?= count($presisiScores) ?></Data>
    </Cell>
   </Row>
   <Row ss:Height="10"/>

   <!-- Table Header Row -->
   <Row ss:Height="24">
    <Cell ss:StyleID="ColHeader"><Data ss:Type="String">Rank</Data></Cell>
    <Cell ss:StyleID="ColHeader"><Data ss:Type="String">No. Peserta</Data></Cell>
    <Cell ss:StyleID="ColHeader"><Data ss:Type="String">Nama Peserta</Data></Cell>
    <Cell ss:StyleID="ColHeader"><Data ss:Type="String">Kesatuan / Club</Data></Cell>
    <Cell ss:StyleID="ColHeader"><Data ss:Type="String">X</Data></Cell>
    <Cell ss:StyleID="ColHeader"><Data ss:Type="String">10</Data></Cell>
    <Cell ss:StyleID="ColHeader"><Data ss:Type="String">9</Data></Cell>
    <Cell ss:StyleID="ColHeader"><Data ss:Type="String">8</Data></Cell>
    <Cell ss:StyleID="ColHeader"><Data ss:Type="String">7</Data></Cell>
    <Cell ss:StyleID="ColHeader"><Data ss:Type="String">6</Data></Cell>
    <Cell ss:StyleID="ColHeader"><Data ss:Type="String">5</Data></Cell>
    <Cell ss:StyleID="ColHeader"><Data ss:Type="String">4</Data></Cell>
    <Cell ss:StyleID="ColHeader"><Data ss:Type="String">3</Data></Cell>
    <Cell ss:StyleID="ColHeader"><Data ss:Type="String">2</Data></Cell>
    <Cell ss:StyleID="ColHeader"><Data ss:Type="String">1</Data></Cell>
    <Cell ss:StyleID="ColHeader"><Data ss:Type="String">Jml Masuk</Data></Cell>
    <Cell ss:StyleID="ColHeader"><Data ss:Type="String">Nilai</Data></Cell>
   </Row>

   <!-- Data Rows -->
   <?php
   if (empty($presisiScores)):
   ?>
   <Row ss:Height="26">
    <Cell ss:MergeAcross="16" ss:StyleID="CellEmptyNotice">
     <Data ss:Type="String">Belum ada skor presisi yang tercatat.</Data>
    </Cell>
   </Row>
   <?php
   else:
   $rank = 1;
   foreach ($presisiScores as $sp):
   ?>
   <Row ss:Height="21">
    <Cell ss:StyleID="CellBoldCenter"><Data ss:Type="Number"><?= $rank++ ?></Data></Cell>
    <Cell ss:StyleID="CellBoldCenter"><Data ss:Type="String"><?= xmlEscape($sp['no_peserta'] ?: '-') ?></Data></Cell>
    <Cell ss:StyleID="CellText"><Data ss:Type="String"><?= xmlEscape($sp['nama']) ?></Data></Cell>
    <Cell ss:StyleID="CellText"><Data ss:Type="String"><?= xmlEscape($sp['satuan']) ?></Data></Cell>
    <Cell ss:StyleID="CellBoldCenter"><Data ss:Type="Number"><?= (int)($sp['ring_x'] ?? 0) ?></Data></Cell>
    <Cell ss:StyleID="CellCenter"><Data ss:Type="Number"><?= (int)($sp['ring_10'] ?? 0) ?></Data></Cell>
    <Cell ss:StyleID="CellCenter"><Data ss:Type="Number"><?= (int)($sp['ring_9'] ?? 0) ?></Data></Cell>
    <Cell ss:StyleID="CellCenter"><Data ss:Type="Number"><?= (int)($sp['ring_8'] ?? 0) ?></Data></Cell>
    <Cell ss:StyleID="CellCenter"><Data ss:Type="Number"><?= (int)($sp['ring_7'] ?? 0) ?></Data></Cell>
    <Cell ss:StyleID="CellCenter"><Data ss:Type="Number"><?= (int)($sp['ring_6'] ?? 0) ?></Data></Cell>
    <Cell ss:StyleID="CellCenter"><Data ss:Type="Number"><?= (int)($sp['ring_5'] ?? 0) ?></Data></Cell>
    <Cell ss:StyleID="CellCenter"><Data ss:Type="Number"><?= (int)($sp['ring_4'] ?? 0) ?></Data></Cell>
    <Cell ss:StyleID="CellCenter"><Data ss:Type="Number"><?= (int)($sp['ring_3'] ?? 0) ?></Data></Cell>
    <Cell ss:StyleID="CellCenter"><Data ss:Type="Number"><?= (int)($sp['ring_2'] ?? 0) ?></Data></Cell>
    <Cell ss:StyleID="CellCenter"><Data ss:Type="Number"><?= (int)($sp['ring_1'] ?? 0) ?></Data></Cell>
    <Cell ss:StyleID="CellBoldCenter"><Data ss:Type="Number"><?= (int)($sp['jumlah_masuk'] ?? 0) ?></Data></Cell>
    <Cell ss:StyleID="CellTotalScore"><Data ss:Type="Number"><?= round((float)($sp['nilai'] ?? 0), 1) ?></Data></Cell>
   </Row>
   <?php endforeach; endif; ?>
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
    <Cell ss:MergeAcross="8" ss:StyleID="HeaderTitle">
     <Data ss:Type="String">BDA SHOOTING CHAMPIONSHIP 2026 — BAGAN PERTANDINGAN DUELING PLAT</Data>
    </Cell>
   </Row>
   <Row ss:Height="18">
    <Cell ss:MergeAcross="8" ss:StyleID="HeaderSub">
     <Data ss:Type="String">Waktu Ekspor: <?= date('d M Y H:i:s') ?> WIB | Total Match: <?= count($duelingMatches) ?></Data>
    </Cell>
   </Row>
   <Row ss:Height="10"/>

   <!-- Table Header Row -->
   <Row ss:Height="24">
    <Cell ss:StyleID="ColHeaderBlue"><Data ss:Type="String">Kategori</Data></Cell>
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
   if (empty($duelingMatches)):
   ?>
   <Row ss:Height="26">
    <Cell ss:MergeAcross="8" ss:StyleID="CellEmptyNotice">
     <Data ss:Type="String">Belum ada bagan pertandingan dueling plat.</Data>
    </Cell>
   </Row>
   <?php
   else:
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
       $catLabel = (isset($dm['category']) && strtolower($dm['category']) === 'bda') ? 'Khusus BDA' : 'Umum POLRI';
   ?>
   <Row ss:Height="21">
    <Cell ss:StyleID="CellCenter"><Data ss:Type="String"><?= $catLabel ?></Data></Cell>
    <Cell ss:StyleID="CellBoldCenter"><Data ss:Type="String"><?= xmlEscape($dm['round_name']) ?></Data></Cell>
    <Cell ss:StyleID="CellCenter"><Data ss:Type="Number"><?= (int)$dm['match_number'] ?></Data></Cell>
    <Cell ss:StyleID="CellText"><Data ss:Type="String"><?= xmlEscape($p1) ?></Data></Cell>
    <Cell ss:StyleID="CellCenter"><Data ss:Type="String"><?= $dm['time_1'] !== null ? number_format((float)$dm['time_1'], 3) : '-' ?></Data></Cell>
    <Cell ss:StyleID="CellText"><Data ss:Type="String"><?= xmlEscape($p2) ?></Data></Cell>
    <Cell ss:StyleID="CellCenter"><Data ss:Type="String"><?= $dm['time_2'] !== null ? number_format((float)$dm['time_2'], 3) : '-' ?></Data></Cell>
    <Cell ss:StyleID="CellBoldCenter"><Data ss:Type="String"><?= xmlEscape($w) ?></Data></Cell>
    <Cell ss:StyleID="CellCenter"><Data ss:Type="String"><?= xmlEscape(strtoupper($dm['match_status'])) ?></Data></Cell>
   </Row>
   <?php endforeach; endif; ?>
  </Table>
 </Worksheet>
<?php endif; ?>

</Workbook>
