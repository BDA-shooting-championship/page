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

<div class="py-8 sm:py-12 bg-gray-50 min-h-screen">
    <div class="max-w-2xl mx-auto px-4 sm:px-6">

        <?php if ($error): ?>
            <div class="bg-white rounded-3xl p-8 border border-red-200 text-center shadow-lg">
                <div class="w-16 h-16 bg-red-100 text-red-600 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i data-lucide="alert-circle" class="w-8 h-8"></i>
                </div>
                <h2 class="font-display text-2xl font-bold text-gray-900 mb-2">E-Ticket Tidak Ditemukan</h2>
                <p class="text-sm text-gray-600 mb-6"><?= htmlspecialchars($error) ?></p>
                <a href="/" class="px-6 py-2.5 bg-copper-600 hover:bg-copper-700 text-white rounded-xl text-sm font-semibold transition">
                    Kembali ke Beranda
                </a>
            </div>

        <?php elseif ($ticket['status'] !== 'Verified'): ?>
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-amber-200 text-center shadow-lg">
                <!-- Dual Logos in White Circles -->
                <div class="flex justify-center items-center gap-2.5 mb-4">
                    <div class="w-12 h-12 rounded-full bg-white p-1.5 shadow-md border border-gray-100 flex items-center justify-center shrink-0">
                        <img src="/assets/logo-bda.png" alt="Logo BDA 750" class="w-full h-full object-contain">
                    </div>
                    <span class="text-gray-400 font-bold text-xs select-none">✕</span>
                    <div class="w-12 h-12 rounded-full bg-white p-1.5 shadow-md border border-gray-100 flex items-center justify-center shrink-0">
                        <img src="/assets/logo-championship.png" alt="Logo BSC 2026" class="w-full h-full object-contain">
                    </div>
                </div>

                <span class="px-3 py-1 bg-amber-100 text-amber-800 rounded-full text-xs font-bold uppercase tracking-wider inline-block">
                    Status: <?= htmlspecialchars($ticket['status']) ?>
                </span>
                <h2 class="font-display text-2xl font-bold text-gray-900 mt-4 mb-2">Menunggu Verifikasi Panitia</h2>
                <p class="text-sm text-gray-600 max-w-md mx-auto mb-6">
                    Pendaftaran untuk <strong><?= htmlspecialchars($ticket['nama']) ?></strong> (ID: <code class="font-mono text-copper-600 font-bold"><?= htmlspecialchars($ticket['registration_id']) ?></code>) sedang dalam proses verifikasi bukti transfer.
                    E-Ticket resmi dan Nomor Peserta akan aktif otomatis setelah verifikasi selesai.
                </p>

                <?php
                $msgTemplateZyaldi = "Halo Panitia BDA Shooting Championship 2026 (Briptu Zyaldi - Seksi Pendaftaran),\n\nSaya ingin menanyakan status pendaftaran saya:\n• ID Registrasi: " . $ticket['registration_id'] . "\n• Nama Lengkap: " . $ticket['nama'] . "\n• Satuan: " . $ticket['satuan'] . "\n\nMohon konfirmasi dan informasinya. Terima kasih.";
                $msgTemplateRully = "Halo Panitia BDA Shooting Championship 2026 (Briptu Rully - Seksi Pendaftaran),\n\nSaya ingin menanyakan status pendaftaran saya:\n• ID Registrasi: " . $ticket['registration_id'] . "\n• Nama Lengkap: " . $ticket['nama'] . "\n• Satuan: " . $ticket['satuan'] . "\n\nMohon konfirmasi dan informasinya. Terima kasih.";
                ?>
                <div class="mt-4 mb-6 p-4 bg-emerald-50 border border-emerald-200 rounded-2xl max-w-lg mx-auto text-left">
                    <p class="text-xs text-emerald-900 mb-2 font-bold flex items-center gap-1.5">
                        <i data-lucide="message-circle" class="w-4 h-4 text-emerald-600"></i>
                        Hubungi Seksi Pendaftaran untuk Pertanyaan / Konfirmasi:
                    </p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                        <a href="https://wa.me/6282134651503?text=<?= urlencode($msgTemplateZyaldi) ?>" target="_blank" class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold transition flex items-center justify-center gap-2 shadow-sm">
                            <i data-lucide="message-circle" class="w-3.5 h-3.5"></i> Briptu Zyaldi (WA)
                        </a>
                        <a href="https://wa.me/6285775015786?text=<?= urlencode($msgTemplateRully) ?>" target="_blank" class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold transition flex items-center justify-center gap-2 shadow-sm">
                            <i data-lucide="message-circle" class="w-3.5 h-3.5"></i> Briptu Rully (WA)
                        </a>
                    </div>
                </div>

                <div class="flex justify-center">
                    <a href="/" class="px-5 py-2.5 border border-gray-300 hover:bg-gray-100 text-xs font-semibold rounded-xl transition text-gray-700">
                        Kembali ke Beranda
                    </a>
                </div>
            </div>

        <?php else: ?>
            <!-- Verified E-Ticket Card -->
            <div id="ticket-print" class="bg-white rounded-3xl shadow-2xl border border-gray-200 overflow-hidden">
                
                <!-- Ticket Header (Optimized for Mobile with White Circle Logos) -->
                <div class="hero-gradient p-5 sm:p-8 text-white relative overflow-hidden">
                    <!-- Subtle Glow -->
                    <div class="absolute -top-12 -right-12 w-48 h-48 bg-copper-500/20 rounded-full blur-2xl pointer-events-none"></div>

                    <!-- Top Bar: Dual Logos in White Circles & Nomor Peserta -->
                    <div class="flex items-center justify-between gap-3 relative z-10">
                        <!-- Dual Logos wrapped in White Circles -->
                        <div class="flex items-center gap-2 sm:gap-2.5 shrink-0">
                            <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-full bg-white p-1.5 shadow-md border border-white/20 flex items-center justify-center shrink-0">
                                <img src="/assets/logo-bda.png" alt="Logo BDA 750" class="w-full h-full object-contain">
                            </div>
                            <span class="text-white/60 font-bold text-xs select-none">✕</span>
                            <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-full bg-white p-1.5 shadow-md border border-white/20 flex items-center justify-center shrink-0">
                                <img src="/assets/logo-championship.png" alt="Logo BSC 2026" class="w-full h-full object-contain">
                            </div>
                        </div>

                        <!-- Nomor Peserta Box -->
                        <div class="text-right bg-white/10 backdrop-blur-md px-3.5 py-1.5 sm:px-4 sm:py-2 rounded-2xl border border-white/20 shrink-0 shadow-sm">
                            <span class="text-[9px] sm:text-[10px] uppercase tracking-wider text-copper-200 block font-semibold">Nomor Peserta</span>
                            <span class="font-mono text-lg sm:text-2xl font-black text-amber-300 tracking-wider">
                                <?= htmlspecialchars($ticket['no_peserta'] ?: 'BSC-000') ?>
                            </span>
                        </div>
                    </div>

                    <!-- Bottom Info: Title & Subtitle with Clean Wrapping on Mobile -->
                    <div class="mt-4 sm:mt-5 relative z-10">
                        <div class="inline-flex items-center px-3 py-1 bg-white/15 backdrop-blur-md rounded-full text-[10px] sm:text-xs font-bold uppercase tracking-wider text-copper-200 border border-white/20 whitespace-nowrap mb-2">
                            Official E-Ticket Resmi
                        </div>
                        <h1 class="font-display text-xl sm:text-2xl md:text-3xl font-extrabold text-white tracking-tight leading-tight">
                            BDA SHOOTING CHAMPIONSHIP 2026
                        </h1>
                        <p class="text-xs sm:text-sm text-copper-200/90 mt-1 flex items-center gap-1.5">
                            <i data-lucide="map-pin" class="w-3.5 h-3.5 text-copper-400 shrink-0 inline"></i>
                            <span>Resimen I Pasukan Pelopor — Kedung Halang, Bogor</span>
                        </p>
                    </div>
                </div>

                <!-- Perforated Line Decoration -->
                <div class="relative flex items-center justify-between px-4 py-2 bg-gray-100 border-y border-dashed border-gray-300">
                    <div class="w-6 h-6 -ml-7 rounded-full bg-gray-50 border-r border-gray-300"></div>
                    <span class="text-[10px] sm:text-xs uppercase tracking-widest font-mono font-bold text-gray-500">PENGESAHAN RESMI &bull; LUNAS</span>
                    <div class="w-6 h-6 -mr-7 rounded-full bg-gray-50 border-l border-gray-300"></div>
                </div>

                <!-- Ticket Body -->
                <div class="p-5 sm:p-8 space-y-6">
                    
                    <!-- QR Code & Quick Info -->
                    <div class="flex flex-col sm:flex-row items-center gap-5 p-5 rounded-2xl bg-gray-50 border border-gray-200">
                        <?php 
                            $qrData = SITE_URL . '/e-ticket.php?id=' . urlencode($ticket['registration_id']);
                            $qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=160x160&data=' . urlencode($qrData);
                        ?>
                        <div class="bg-white p-3 rounded-2xl border border-gray-200 shadow-sm shrink-0">
                            <img src="<?= $qrUrl ?>" alt="QR Code" class="w-32 h-32 sm:w-36 sm:h-36 object-contain" />
                        </div>
                        <div class="text-center sm:text-left flex-1">
                            <span class="inline-block px-3 py-1 rounded-full bg-emerald-100 text-emerald-800 text-xs font-bold uppercase tracking-wider mb-2">
                                TERVERIFIKASI &bull; LUNAS
                            </span>
                            <h3 class="font-display text-2xl sm:text-3xl font-bold text-gray-900">
                                <?= htmlspecialchars($ticket['nama']) ?>
                            </h3>
                            <p class="text-sm font-bold text-gray-700 mt-1">
                                <?= htmlspecialchars($ticket['pangkat']) ?> &bull; NRP: <?= htmlspecialchars($ticket['nrp']) ?>
                            </p>
                            <p class="text-xs text-gray-500 mt-1">
                                Satuan: <strong class="text-gray-800"><?= htmlspecialchars($ticket['satuan']) ?></strong>
                            </p>
                        </div>
                    </div>

                    <!-- Details Grid: Isian Form Pendaftaran & Kategori -->
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 text-xs">
                        <div class="p-3.5 bg-gray-50 rounded-xl border border-gray-200">
                            <span class="text-gray-400 block mb-0.5 text-[11px] uppercase tracking-wider font-semibold">ID Registrasi</span>
                            <span class="font-mono font-bold text-copper-700 text-sm"><?= htmlspecialchars($ticket['registration_id']) ?></span>
                        </div>
                        <div class="p-3.5 bg-gray-50 rounded-xl border border-gray-200">
                            <span class="text-gray-400 block mb-0.5 text-[11px] uppercase tracking-wider font-semibold">Kategori Lomba</span>
                            <span class="font-bold text-gray-900"><?= htmlspecialchars($ticket['kategori']) ?></span>
                        </div>
                        <div class="p-3.5 bg-gray-50 rounded-xl border border-gray-200">
                            <span class="text-gray-400 block mb-0.5 text-[11px] uppercase tracking-wider font-semibold">Status Pembayaran</span>
                            <span class="font-bold text-emerald-700 flex items-center gap-1">
                                <i data-lucide="check-circle" class="w-3.5 h-3.5 text-emerald-600"></i> LUNAS
                            </span>
                        </div>
                        <div class="p-3.5 bg-gray-50 rounded-xl border border-gray-200">
                            <span class="text-gray-400 block mb-0.5 text-[11px] uppercase tracking-wider font-semibold">Satuan / Club</span>
                            <span class="font-semibold text-gray-800"><?= htmlspecialchars($ticket['satuan']) ?></span>
                        </div>
                        <div class="p-3.5 bg-gray-50 rounded-xl border border-gray-200">
                            <span class="text-gray-400 block mb-0.5 text-[11px] uppercase tracking-wider font-semibold">No. WhatsApp</span>
                            <span class="font-mono font-semibold text-gray-800"><?= htmlspecialchars($ticket['telepon']) ?></span>
                        </div>
                        <div class="p-3.5 bg-gray-50 rounded-xl border border-gray-200">
                            <span class="text-gray-400 block mb-0.5 text-[11px] uppercase tracking-wider font-semibold">Tanggal Pelaksanaan</span>
                            <span class="font-semibold text-gray-800">17 — 18 Okt 2026</span>
                        </div>
                    </div>

                    <!-- KTA Image Preview if Available -->
                    <?php if (!empty($ticket['kta_filename'])): ?>
                        <div class="p-4 rounded-2xl bg-gray-50 border border-gray-200">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-xs font-bold text-gray-700 uppercase tracking-wider flex items-center gap-1.5">
                                    <i data-lucide="id-card" class="w-4 h-4 text-copper-600"></i>
                                    Foto KTA / Tanda Pengenal Peserta
                                </span>
                                <a href="/uploads/kta/<?= htmlspecialchars($ticket['kta_filename']) ?>" target="_blank" class="text-xs text-copper-600 font-bold hover:underline flex items-center gap-1">
                                    <span>Buka Gambar Asli</span>
                                    <i data-lucide="external-link" class="w-3.5 h-3.5"></i>
                                </a>
                            </div>
                            <div class="w-full sm:max-w-md h-48 rounded-xl overflow-hidden border border-gray-200 bg-white">
                                <img src="/uploads/kta/<?= htmlspecialchars($ticket['kta_filename']) ?>" alt="Foto KTA <?= htmlspecialchars($ticket['nama']) ?>" class="w-full h-full object-contain p-1" onerror="this.parentElement.style.display='none'">
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Important Notice / Catatan Wajib Peserta -->
                    <div class="p-5 rounded-2xl bg-gradient-to-r from-copper-50 to-amber-50 border border-copper-300 text-xs text-gray-800 space-y-2">
                        <p class="font-bold text-copper-800 text-sm flex items-center gap-2">
                            <i data-lucide="alert-triangle" class="w-4 h-4 text-copper-600"></i>
                            Catatan Penting Untuk Peserta:
                        </p>
                        <ul class="space-y-1.5 list-disc list-inside text-gray-700">
                            <li><strong>Simpan pesan, link E-Ticket, atau tangkapan layar (screenshot) QR Code ini</strong> agar tidak hilang.</li>
                            <li>Tunjukkan E-Ticket &amp; QR Code ini saat <strong>Daftar Ulang</strong> di meja registrasi lapangan tembak.</li>
                            <li><strong>Wajib membawa fisik KTA &amp; KTP Asli</strong> untuk verifikasi data keabsahan peserta.</li>
                            <li>Seluruh peserta <strong>Wajib Hadir saat Technical Meeting (TM)</strong> sebelum rangkaian pertandingan dimulai.</li>
                            <li>Membawa perlengkapan safety (earmuff / earplug dan safety glasses) pribadi.</li>
                        </ul>
                    </div>

                    <!-- Print / Share Actions -->
                    <div class="flex flex-col sm:flex-row gap-3 pt-2 print:hidden">
                        <button onclick="window.print()" class="flex-1 py-3.5 bg-copper-600 hover:bg-copper-700 text-white rounded-xl text-sm font-bold transition flex items-center justify-center gap-2 shadow-md shadow-copper-600/20">
                            <i data-lucide="printer" class="w-4 h-4"></i> Cetak / Simpan PDF
                        </button>
                        <a href="https://wa.me/?text=<?= urlencode("E-Ticket Resmi BDA Shooting Championship 2026 atas nama " . $ticket['nama'] . " (No. Peserta: " . ($ticket['no_peserta'] ?: '-') . "): " . SITE_URL . "/e-ticket.php?id=" . $ticket['registration_id']) ?>" target="_blank" class="px-6 py-3.5 border border-gray-300 hover:bg-gray-100 rounded-xl text-sm font-bold text-gray-700 transition flex items-center justify-center gap-2">
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
