<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';

$pageTitle = 'E-Ticket Resmi — BDA Shooting Championship 2026';
$currentPage = 'eticket';

$regId = trim($_GET['id'] ?? '');
$ticket = null;
$error = null;

if (!empty($regId)) {
    try {
        $db = getDB();
        $stmt = $db->prepare('SELECT * FROM registrations WHERE registration_id = ? LIMIT 1');
        $stmt->execute([$regId]);
        $ticket = $stmt->fetch();
        if (!$ticket) {
            $error = 'Data E-Ticket tidak ditemukan. Pastikan ID Registrasi sudah benar.';
        }
    } catch (Exception $e) {
        $error = 'Terjadi gangguan saat memuat data e-ticket.';
    }
} else {
    $error = 'ID Registrasi tidak disertakan.';
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="py-12 bg-gray-50 dark:bg-gray-900 min-h-screen">
    <div class="max-w-2xl mx-auto px-4 sm:px-6">

        <?php if ($error): ?>
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-8 border border-red-200 dark:border-red-800 text-center shadow-lg">
                <div class="w-16 h-16 bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i data-lucide="alert-circle" class="w-8 h-8"></i>
                </div>
                <h2 class="font-display text-2xl font-bold text-gray-900 dark:text-white mb-2">E-Ticket Tidak Ditemukan</h2>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-6"><?= htmlspecialchars($error) ?></p>
                <a href="/" class="px-6 py-2.5 bg-copper-600 hover:bg-copper-700 text-white rounded-lg text-sm font-semibold transition">
                    Kembali ke Beranda
                </a>
            </div>

        <?php elseif ($ticket['status'] !== 'Verified'): ?>
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-8 border border-amber-200 dark:border-amber-800 text-center shadow-lg">
                <div class="w-16 h-16 bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i data-lucide="clock" class="w-8 h-8"></i>
                </div>
                <span class="px-3 py-1 bg-amber-100 dark:bg-amber-900/40 text-amber-700 dark:text-amber-300 rounded-full text-xs font-bold uppercase tracking-wider">
                    Status: <?= htmlspecialchars($ticket['status']) ?>
                </span>
                <h2 class="font-display text-2xl font-bold text-gray-900 dark:text-white mt-4 mb-2">Menunggu Verifikasi Panitia</h2>
                <p class="text-sm text-gray-600 dark:text-gray-400 max-w-md mx-auto mb-6">
                    Pendaftaran untuk <strong><?= htmlspecialchars($ticket['nama']) ?></strong> (ID: <code class="font-mono text-copper-600"><?= htmlspecialchars($ticket['registration_id']) ?></code>) sedang dalam proses verifikasi bukti transfer.
                    E-Ticket resmi dan Nomor Peserta akan aktif otomatis setelah verifikasi selesai.
                </p>
                <div class="flex flex-col sm:flex-row justify-center gap-3">
                    <a href="https://wa.me/6285283525761?text=Halo%20Panitia,%20saya%20ingin%20menanyakan%20status%20pendaftaran%20ID%20<?= urlencode($ticket['registration_id']) ?>" target="_blank" class="px-5 py-2.5 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm font-semibold transition flex items-center justify-center gap-2">
                        <i data-lucide="message-circle" class="w-4 h-4"></i> Hubungi Panitia (WA)
                    </a>
                    <a href="/" class="px-5 py-2.5 border border-gray-300 dark:border-gray-700 hover:bg-gray-100 dark:hover:bg-gray-800 text-sm font-semibold rounded-lg transition">
                        Kembali ke Beranda
                    </a>
                </div>
            </div>

        <?php else: ?>
            <!-- Verified E-Ticket Card -->
            <div id="ticket-print" class="bg-white dark:bg-gray-800 rounded-3xl shadow-2xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                
                <!-- Ticket Header -->
                <div class="hero-gradient p-6 sm:p-8 text-white relative">
                    <div class="flex justify-between items-start">
                        <div class="flex items-center gap-3 sm:gap-4">
                            <div class="flex items-center gap-2 shrink-0">
                                <img src="/assets/logo-bda-clean.jpg" alt="Logo BDA 750" class="h-11 w-11 sm:h-12 sm:w-12 rounded-xl object-cover border border-white/30 shadow-md">
                                <img src="/assets/logo-championship-clean.jpg" alt="Logo BSC 2026" class="h-11 w-11 sm:h-12 sm:w-12 rounded-xl object-cover border border-white/30 shadow-md">
                            </div>
                            <div>
                                <span class="px-3 py-0.5 bg-white/20 backdrop-blur-md rounded-full text-[10px] sm:text-xs font-semibold uppercase tracking-wider text-copper-200">
                                    Official E-Ticket
                                </span>
                                <h1 class="font-display text-lg sm:text-2xl font-bold mt-1">BDA SHOOTING CHAMPIONSHIP 2026</h1>
                                <p class="text-[11px] sm:text-xs text-copper-200">Resimen I Pasukan Pelopor — Kedung Halang, Bogor</p>
                            </div>
                        </div>
                        <div class="text-right shrink-0">
                            <span class="text-[10px] uppercase tracking-wider text-gray-300 block">Nomor Peserta</span>
                            <span class="font-display text-2xl sm:text-3xl font-bold text-copper-400 tracking-wider">
                                <?= htmlspecialchars($ticket['no_peserta'] ?: 'BSC-000') ?>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Perforated Line Decoration -->
                <div class="relative flex items-center justify-between px-4 py-1 bg-gray-100 dark:bg-gray-900 border-y border-dashed border-gray-300 dark:border-gray-700">
                    <div class="w-6 h-6 -ml-7 rounded-full bg-gray-50 dark:bg-gray-900"></div>
                    <span class="text-[10px] uppercase tracking-widest font-mono text-gray-400">PENGESAHAN RESMI &bull; LUNAS</span>
                    <div class="w-6 h-6 -mr-7 rounded-full bg-gray-50 dark:bg-gray-900"></div>
                </div>

                <!-- Ticket Body -->
                <div class="p-6 sm:p-8 space-y-6">
                    
                    <!-- QR Code & Quick Info -->
                    <div class="flex flex-col sm:flex-row items-center gap-6 p-4 rounded-2xl bg-gray-50 dark:bg-gray-900/50 border border-gray-200 dark:border-gray-700/60">
                        <?php 
                            $qrData = SITE_URL . '/e-ticket.php?id=' . urlencode($ticket['registration_id']);
                            $qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=160x160&data=' . urlencode($qrData);
                        ?>
                        <div class="bg-white p-2.5 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm shrink-0">
                            <img src="<?= $qrUrl ?>" alt="QR Code" class="w-32 h-32 object-contain" />
                        </div>
                        <div class="text-center sm:text-left flex-1">
                            <span class="px-2.5 py-0.5 rounded-full bg-green-100 dark:bg-green-900/40 text-green-700 dark:text-green-300 text-xs font-bold uppercase tracking-wider">
                                TERVERIFIKASI &bull; LUNAS
                            </span>
                            <h3 class="font-display text-2xl font-bold text-gray-900 dark:text-white mt-2">
                                <?= htmlspecialchars($ticket['nama']) ?>
                            </h3>
                            <p class="text-sm font-semibold text-gray-700 dark:text-gray-300">
                                <?= htmlspecialchars($ticket['pangkat']) ?> &bull; NRP: <?= htmlspecialchars($ticket['nrp']) ?>
                            </p>
                            <p class="text-xs text-gray-500 mt-1">
                                Satuan: <strong class="text-gray-700 dark:text-gray-300"><?= htmlspecialchars($ticket['satuan']) ?></strong>
                            </p>
                        </div>
                    </div>

                    <!-- Details Grid -->
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 text-xs">
                        <div class="p-3 bg-gray-50 dark:bg-gray-900 rounded-xl">
                            <span class="text-gray-400 block mb-0.5">ID Registrasi</span>
                            <span class="font-mono font-bold text-copper-600 dark:text-copper-400"><?= htmlspecialchars($ticket['registration_id']) ?></span>
                        </div>
                        <div class="p-3 bg-gray-50 dark:bg-gray-900 rounded-xl">
                            <span class="text-gray-400 block mb-0.5">Kategori</span>
                            <span class="font-bold text-gray-800 dark:text-gray-200"><?= htmlspecialchars($ticket['kategori']) ?></span>
                        </div>
                        <div class="p-3 bg-gray-50 dark:bg-gray-900 rounded-xl">
                            <span class="text-gray-400 block mb-0.5">Tanggal Lomba</span>
                            <span class="font-semibold text-gray-800 dark:text-gray-200">17 — 18 Okt 2026</span>
                        </div>
                    </div>

                    <!-- KTA Image Preview if Available -->
                    <?php if (!empty($ticket['kta_filename'])): ?>
                        <div class="pt-2">
                            <span class="text-xs font-bold text-gray-500 uppercase tracking-wider block mb-2">Foto KTA / Identitas:</span>
                            <div class="w-full max-w-xs h-36 rounded-xl overflow-hidden border border-gray-200 dark:border-gray-700 bg-gray-100 dark:bg-gray-900">
                                <img src="/uploads/kta/<?= htmlspecialchars($ticket['kta_filename']) ?>" alt="Foto KTA" class="w-full h-full object-cover" onerror="this.parentElement.style.display='none'">
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Important Notice -->
                    <div class="p-4 rounded-xl bg-copper-50 dark:bg-copper-900/20 border border-copper-200 dark:border-copper-800 text-xs text-copper-900 dark:text-copper-200 space-y-1">
                        <p class="font-bold flex items-center gap-1.5"><i data-lucide="shield-check" class="w-4 h-4 text-copper-600"></i> Ketentuan Masuk Lapangan:</p>
                        <p>1. Tunjukkan E-Ticket ini (digital atau print) saat daftar ulang di meja registrasi.</p>
                        <p>2. Wajib membawa senjata dan perlengkapan safety (earmuff & safety glasses) pribadi.</p>
                        <p>3. Hadir 30 menit sebelum jadwal pertandingan dimulai.</p>
                    </div>

                    <!-- Print / Share Actions -->
                    <div class="flex flex-col sm:flex-row gap-3 pt-2 print:hidden">
                        <button onclick="window.print()" class="flex-1 py-3 bg-copper-600 hover:bg-copper-700 text-white rounded-xl text-sm font-semibold transition flex items-center justify-center gap-2 shadow-md">
                            <i data-lucide="printer" class="w-4 h-4"></i> Cetak / Simpan PDF
                        </button>
                        <a href="https://wa.me/?text=<?= urlencode("E-Ticket Resmi BDA Shooting Championship 2026 atas nama " . $ticket['nama'] . ": " . SITE_URL . "/e-ticket.php?id=" . $ticket['registration_id']) ?>" target="_blank" class="px-5 py-3 border border-gray-300 dark:border-gray-700 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-xl text-sm font-semibold transition flex items-center justify-center gap-2">
                            <i data-lucide="share-2" class="w-4 h-4"></i> Bagikan
                        </a>
                    </div>

                </div>

            </div>
        <?php endif; ?>

    </div>
</div>

<style>
@media print {
    nav, footer, .print\:hidden { display: none !important; }
    body { background: white !important; color: black !important; }
    #ticket-print { box-shadow: none !important; border: 1px solid #ccc !important; }
}
</style>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
