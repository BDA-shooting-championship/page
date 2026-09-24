<?php
$pageTitle = 'BDA Shooting Championship 2026';
$currentPage = 'home';
require_once __DIR__ . '/includes/header.php';
?>

<!-- Hero Section -->
<section class="relative hero-gradient text-white overflow-hidden">
    <div class="absolute inset-0 target-pattern opacity-30"></div>
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 md:py-32">
        <div class="text-center">
            <div class="flex justify-center items-center gap-5 sm:gap-8 mb-8">
                <div class="p-1 rounded-2xl bg-white/10 backdrop-blur-md border border-white/20 shadow-2xl hover:scale-105 transition-transform duration-300">
                    <img src="/assets/logo-bda-clean.jpg" alt="Logo Brigade Diraya Adikara 750" class="h-20 w-20 sm:h-28 sm:w-28 rounded-xl object-cover">
                </div>
                <div class="p-1 rounded-2xl bg-white/10 backdrop-blur-md border border-copper-400/40 shadow-2xl shadow-copper-500/20 hover:scale-105 transition-transform duration-300">
                    <img src="/assets/logo-championship-clean.jpg" alt="Logo BDA Shooting Championship 2026" class="h-20 w-20 sm:h-28 sm:w-28 rounded-xl object-cover">
                </div>
            </div>
            <p class="text-copper-300 font-medium tracking-widest uppercase text-sm mb-4">Brigade Diraya Adikara (BDA) 750 Presents</p>
            <h1 class="font-display text-4xl md:text-6xl lg:text-7xl font-bold mb-6 leading-tight">
                BDA SHOOTING<br>CHAMPIONSHIP <span class="text-copper-400">2026</span>
            </h1>
            <p class="text-lg md:text-xl text-gray-300 max-w-2xl mx-auto mb-4">
                Kejuaraan Menembak Pistol Presisi 20M & Dueling Plat
            </p>
            <div class="flex flex-wrap justify-center gap-4 text-sm text-gray-300 mb-8">
                <span class="flex items-center gap-2"><i data-lucide="calendar" class="w-4 h-4 text-copper-400"></i>17 — 18 Oktober 2026</span>
                <span class="flex items-center gap-2"><i data-lucide="map-pin" class="w-4 h-4 text-copper-400"></i>Lap. Tembak Shooting House, Bogor</span>
            </div>
            <div class="flex flex-wrap justify-center gap-4">
                <a href="/daftar.php" class="px-8 py-3 bg-copper-600 hover:bg-copper-700 text-white font-semibold rounded-lg transition shadow-lg shadow-copper-600/25">
                    Daftar Sekarang
                </a>
                <a href="/live-score.php" class="px-8 py-3 border border-white/30 hover:bg-white/10 text-white font-semibold rounded-lg transition">
                    Live Score
                </a>
            </div>
        </div>
    </div>
    <!-- Decorative bottom curve -->
    <div class="absolute bottom-0 left-0 right-0">
        <svg viewBox="0 0 1440 60" fill="none"><path d="M0 60L1440 60L1440 0C1200 50 240 50 0 0L0 60Z" class="fill-white dark:fill-gray-950"/></svg>
    </div>
</section>

<!-- Countdown Section -->
<section class="py-16 bg-white dark:bg-gray-950" x-data="countdown()" x-init="start()">
    <div class="max-w-4xl mx-auto px-4 text-center">
        <h2 class="font-display text-3xl md:text-4xl font-bold mb-2">Hitung Mundur</h2>
        <p class="text-gray-500 dark:text-gray-400 mb-8">Menuju Hari Pertandingan</p>
        <div class="grid grid-cols-4 gap-4 max-w-lg mx-auto">
            <div class="bg-gray-50 dark:bg-gray-900 rounded-xl p-4">
                <span class="font-display text-3xl md:text-5xl font-bold text-copper-600 dark:text-copper-400" x-text="days">0</span>
                <p class="text-xs text-gray-500 mt-1 uppercase tracking-wider">Hari</p>
            </div>
            <div class="bg-gray-50 dark:bg-gray-900 rounded-xl p-4">
                <span class="font-display text-3xl md:text-5xl font-bold text-copper-600 dark:text-copper-400" x-text="hours">0</span>
                <p class="text-xs text-gray-500 mt-1 uppercase tracking-wider">Jam</p>
            </div>
            <div class="bg-gray-50 dark:bg-gray-900 rounded-xl p-4">
                <span class="font-display text-3xl md:text-5xl font-bold text-copper-600 dark:text-copper-400" x-text="minutes">0</span>
                <p class="text-xs text-gray-500 mt-1 uppercase tracking-wider">Menit</p>
            </div>
            <div class="bg-gray-50 dark:bg-gray-900 rounded-xl p-4">
                <span class="font-display text-3xl md:text-5xl font-bold text-copper-600 dark:text-copper-400" x-text="seconds">0</span>
                <p class="text-xs text-gray-500 mt-1 uppercase tracking-wider">Detik</p>
            </div>
        </div>
    </div>
</section>

<!-- About Section -->
<section id="tentang" class="py-16 bg-gray-50 dark:bg-gray-900">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="font-display text-3xl md:text-4xl font-bold mb-4">Tentang Kejuaraan</h2>
            <p class="text-gray-600 dark:text-gray-400 max-w-2xl mx-auto">
                BDA Shooting Championship 2026 merupakan kejuaraan menembak pistol yang diselenggarakan oleh Brigade Diraya Adikara (BDA) 750 — Resimen I Pasukan Pelopor, Kedung Halang, Bogor.
            </p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="text-center p-6">
                <div class="w-14 h-14 mx-auto mb-4 rounded-full bg-copper-100 dark:bg-copper-900/30 flex items-center justify-center">
                    <i data-lucide="target" class="w-7 h-7 text-copper-600 dark:text-copper-400"></i>
                </div>
                <h3 class="font-display text-xl font-semibold mb-2">2 Kategori</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400">Pistol Presisi 20M dan Dueling Plat</p>
            </div>
            <div class="text-center p-6">
                <div class="w-14 h-14 mx-auto mb-4 rounded-full bg-copper-100 dark:bg-copper-900/30 flex items-center justify-center">
                    <i data-lucide="trophy" class="w-7 h-7 text-copper-600 dark:text-copper-400"></i>
                </div>
                <h3 class="font-display text-xl font-semibold mb-2">Rp 3 Juta / Juara</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400">Total hadiah + Trophy + Sertifikat</p>
            </div>
            <div class="text-center p-6">
                <div class="w-14 h-14 mx-auto mb-4 rounded-full bg-copper-100 dark:bg-copper-900/30 flex items-center justify-center">
                    <i data-lucide="users" class="w-7 h-7 text-copper-600 dark:text-copper-400"></i>
                </div>
                <h3 class="font-display text-xl font-semibold mb-2">Terbuka Umum</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400">Anggota Polri, TNI, dan Sipil</p>
            </div>
        </div>
    </div>
</section>

<!-- Categories Section -->
<section id="kategori" class="py-16 bg-white dark:bg-gray-950">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="font-display text-3xl md:text-4xl font-bold text-center mb-12">Kategori Lomba</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Presisi 20M -->
            <div class="border border-gray-200 dark:border-gray-800 rounded-xl p-8 hover:border-copper-400 dark:hover:border-copper-600 transition">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-12 h-12 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                        <i data-lucide="crosshair" class="w-6 h-6 text-blue-600 dark:text-blue-400"></i>
                    </div>
                    <h3 class="font-display text-2xl font-bold">Pistol Presisi 20M</h3>
                </div>
                <ul class="space-y-3 text-sm text-gray-600 dark:text-gray-400">
                    <li class="flex gap-2"><i data-lucide="ruler" class="w-4 h-4 text-copper-500 shrink-0 mt-0.5"></i>Jarak: 20 meter</li>
                    <li class="flex gap-2"><i data-lucide="user" class="w-4 h-4 text-copper-500 shrink-0 mt-0.5"></i>Posisi: Berdiri, 2 tangan</li>
                    <li class="flex gap-2"><i data-lucide="target" class="w-4 h-4 text-copper-500 shrink-0 mt-0.5"></i>Sasaran: Ring target (1-10 + X)</li>
                    <li class="flex gap-2"><i data-lucide="hash" class="w-4 h-4 text-copper-500 shrink-0 mt-0.5"></i>13 tembakan: 3 percobaan + 10 seri</li>
                    <li class="flex gap-2"><i data-lucide="timer" class="w-4 h-4 text-copper-500 shrink-0 mt-0.5"></i>Batas waktu: 3 menit</li>
                    <li class="flex gap-2"><i data-lucide="calculator" class="w-4 h-4 text-copper-500 shrink-0 mt-0.5"></i>Skor: Akumulasi ring (max 100)</li>
                </ul>
            </div>
            
            <!-- Dueling Plat -->
            <div class="border border-gray-200 dark:border-gray-800 rounded-xl p-8 hover:border-copper-400 dark:hover:border-copper-600 transition">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-12 h-12 rounded-lg bg-red-100 dark:bg-red-900/30 flex items-center justify-center">
                        <i data-lucide="swords" class="w-6 h-6 text-red-600 dark:text-red-400"></i>
                    </div>
                    <h3 class="font-display text-2xl font-bold">Dueling Plat</h3>
                </div>
                <ul class="space-y-3 text-sm text-gray-600 dark:text-gray-400">
                    <li class="flex gap-2"><i data-lucide="ruler" class="w-4 h-4 text-copper-500 shrink-0 mt-0.5"></i>Jarak: 15 meter</li>
                    <li class="flex gap-2"><i data-lucide="user" class="w-4 h-4 text-copper-500 shrink-0 mt-0.5"></i>Posisi: Berdiri (lari 10m ke meja)</li>
                    <li class="flex gap-2"><i data-lucide="target" class="w-4 h-4 text-copper-500 shrink-0 mt-0.5"></i>Sasaran: 5 plat + 1 popper</li>
                    <li class="flex gap-2"><i data-lucide="hash" class="w-4 h-4 text-copper-500 shrink-0 mt-0.5"></i>10 peluru per ronde</li>
                    <li class="flex gap-2"><i data-lucide="timer" class="w-4 h-4 text-copper-500 shrink-0 mt-0.5"></i>Waktu: Unlimited</li>
                    <li class="flex gap-2"><i data-lucide="trophy" class="w-4 h-4 text-copper-500 shrink-0 mt-0.5"></i>Format: Eliminasi head-to-head</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- Prizes Section -->
<section id="hadiah" class="py-16 bg-gray-50 dark:bg-gray-900">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="font-display text-3xl md:text-4xl font-bold text-center mb-4">Hadiah</h2>
        <p class="text-center text-gray-500 dark:text-gray-400 mb-12">Per kategori lomba</p>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 max-w-4xl mx-auto">
            <!-- Juara 2 -->
            <div class="order-2 md:order-1 text-center bg-white dark:bg-gray-800 rounded-xl p-8 border border-gray-200 dark:border-gray-700 md:mt-8">
                <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-gray-200 dark:bg-gray-600 flex items-center justify-center">
                    <span class="font-display text-2xl font-bold text-gray-600 dark:text-gray-300">2</span>
                </div>
                <h3 class="font-display text-xl font-bold mb-2">Juara II</h3>
                <p class="text-2xl font-bold text-gray-700 dark:text-gray-300 mb-2">Rp 2.000.000</p>
                <p class="text-sm text-gray-500">+ Trophy + Sertifikat</p>
            </div>
            
            <!-- Juara 1 -->
            <div class="order-1 md:order-2 text-center bg-gradient-to-b from-copper-50 to-white dark:from-copper-900/20 dark:to-gray-800 rounded-xl p-8 border-2 border-copper-400 dark:border-copper-600 shadow-lg">
                <div class="w-20 h-20 mx-auto mb-4 rounded-full bg-copper-100 dark:bg-copper-900/40 flex items-center justify-center">
                    <i data-lucide="crown" class="w-10 h-10 text-copper-600 dark:text-copper-400"></i>
                </div>
                <h3 class="font-display text-xl font-bold mb-2 text-copper-700 dark:text-copper-300">Juara I</h3>
                <p class="text-3xl font-bold text-copper-600 dark:text-copper-400 mb-2">Rp 3.000.000</p>
                <p class="text-sm text-gray-500">+ Trophy + Sertifikat</p>
            </div>
            
            <!-- Juara 3 -->
            <div class="order-3 text-center bg-white dark:bg-gray-800 rounded-xl p-8 border border-gray-200 dark:border-gray-700 md:mt-8">
                <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center">
                    <span class="font-display text-2xl font-bold text-amber-700 dark:text-amber-400">3</span>
                </div>
                <h3 class="font-display text-xl font-bold mb-2">Juara III</h3>
                <p class="text-2xl font-bold text-gray-700 dark:text-gray-300 mb-2">Rp 1.500.000</p>
                <p class="text-sm text-gray-500">+ Trophy + Sertifikat</p>
            </div>
        </div>
    </div>
</section>

<!-- Schedule Section -->
<section id="jadwal" class="py-16 bg-white dark:bg-gray-950">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="font-display text-3xl md:text-4xl font-bold text-center mb-12">Jadwal Kegiatan</h2>
        <div class="space-y-8">
            <!-- Day 1 -->
            <div class="flex gap-6">
                <div class="flex flex-col items-center">
                    <div class="w-12 h-12 rounded-full bg-copper-600 flex items-center justify-center text-white font-bold text-sm shrink-0">H-1</div>
                    <div class="w-0.5 h-full bg-gray-200 dark:bg-gray-700 mt-2"></div>
                </div>
                <div class="pb-8">
                    <h3 class="font-display text-xl font-bold mb-1">Jumat, 17 Oktober 2026</h3>
                    <p class="text-sm text-copper-600 dark:text-copper-400 mb-3">Hari Pertama</p>
                    <ul class="space-y-2 text-sm text-gray-600 dark:text-gray-400">
                        <li>07:00 — Registrasi Ulang Peserta</li>
                        <li>08:00 — Upacara Pembukaan</li>
                        <li>09:00 — Pertandingan Presisi 20M (Babak Penyisihan)</li>
                        <li>13:00 — Pertandingan Dueling Plat (Babak Penyisihan)</li>
                        <li>17:00 — Penutupan Hari Pertama</li>
                    </ul>
                </div>
            </div>
            
            <!-- Day 2 -->
            <div class="flex gap-6">
                <div class="flex flex-col items-center">
                    <div class="w-12 h-12 rounded-full bg-copper-600 flex items-center justify-center text-white font-bold text-sm shrink-0">H-2</div>
                </div>
                <div>
                    <h3 class="font-display text-xl font-bold mb-1">Sabtu, 18 Oktober 2026</h3>
                    <p class="text-sm text-copper-600 dark:text-copper-400 mb-3">Hari Kedua — Final</p>
                    <ul class="space-y-2 text-sm text-gray-600 dark:text-gray-400">
                        <li>08:00 — Presisi 20M (Babak Final)</li>
                        <li>10:00 — Dueling Plat (Semifinal & Final)</li>
                        <li>14:00 — Perhitungan Nilai & Rekapitulasi</li>
                        <li>15:00 — Upacara Penutupan & Penyerahan Hadiah</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Rules Section -->
<section id="juknis" class="py-16 bg-gray-50 dark:bg-gray-900">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="font-display text-3xl md:text-4xl font-bold text-center mb-12">Petunjuk Teknis</h2>
        <div class="space-y-3" x-data="{ open: null }">
            <!-- Rule 1 -->
            <div class="border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
                <button @click="open = open === 1 ? null : 1" class="w-full flex items-center justify-between p-4 text-left font-semibold hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                    <span>Persyaratan Peserta</span>
                    <i data-lucide="chevron-down" class="w-5 h-5 transition-transform" :class="open === 1 ? 'rotate-180' : ''"></i>
                </button>
                <div x-show="open === 1" x-transition class="px-4 pb-4 text-sm text-gray-600 dark:text-gray-400 space-y-1">
                    <p>• Anggota Polri aktif / purnawirawan, TNI, atau sipil</p>
                    <p>• Memiliki KTA (Kartu Tanda Anggota) yang masih berlaku</p>
                    <p>• Mendaftar melalui formulir resmi dan melakukan pembayaran</p>
                    <p>• Bersedia mematuhi seluruh peraturan pertandingan</p>
                </div>
            </div>
            
            <!-- Rule 2 -->
            <div class="border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
                <button @click="open = open === 2 ? null : 2" class="w-full flex items-center justify-between p-4 text-left font-semibold hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                    <span>Ketentuan Senjata</span>
                    <i data-lucide="chevron-down" class="w-5 h-5 transition-transform" :class="open === 2 ? 'rotate-180' : ''"></i>
                </button>
                <div x-show="open === 2" x-transition class="px-4 pb-4 text-sm text-gray-600 dark:text-gray-400 space-y-1">
                    <p>• Pistol kaliber .22 LR atau kaliber .177 (air pistol) untuk Presisi 20M</p>
                    <p>• Pistol kaliber 9mm atau kaliber .22 LR untuk Dueling Plat</p>
                    <p>• Senjata harus dalam kondisi laik tembak</p>
                    <p>• Pemeriksaan senjata oleh panitia sebelum pertandingan</p>
                </div>
            </div>
            
            <!-- Rule 3 -->
            <div class="border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
                <button @click="open = open === 3 ? null : 3" class="w-full flex items-center justify-between p-4 text-left font-semibold hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                    <span>Sistem Penilaian</span>
                    <i data-lucide="chevron-down" class="w-5 h-5 transition-transform" :class="open === 3 ? 'rotate-180' : ''"></i>
                </button>
                <div x-show="open === 3" x-transition class="px-4 pb-4 text-sm text-gray-600 dark:text-gray-400 space-y-1">
                    <p><strong>Presisi 20M:</strong></p>
                    <p>• Skor berdasarkan akumulasi ring (1-10) dari 10 tembakan seri</p>
                    <p>• Skor maksimal: 100 poin</p>
                    <p>• Tie-breaker: Jumlah X (inner-10 / center hit) tertinggi</p>
                    <p class="mt-2"><strong>Dueling Plat:</strong></p>
                    <p>• Sistem eliminasi head-to-head</p>
                    <p>• Pemenang ditentukan oleh waktu tercepat menyelesaikan 5 plat + 1 popper</p>
                    <p>• Bracket: Penyisihan → Perempat Final → Semifinal → Final</p>
                </div>
            </div>
            
            <!-- Rule 4 -->
            <div class="border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
                <button @click="open = open === 4 ? null : 4" class="w-full flex items-center justify-between p-4 text-left font-semibold hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                    <span>Biaya & Pembayaran</span>
                    <i data-lucide="chevron-down" class="w-5 h-5 transition-transform" :class="open === 4 ? 'rotate-180' : ''"></i>
                </button>
                <div x-show="open === 4" x-transition class="px-4 pb-4 text-sm text-gray-600 dark:text-gray-400 space-y-1">
                    <p>• Biaya pendaftaran: <strong>Rp 200.000 per kategori</strong></p>
                    <p>• Peserta yang mengikuti 2 kategori: Rp 400.000</p>
                    <p>• Transfer ke: <strong>BRI 053801071906503</strong> a.n. <strong>Ahyandi Hi Karim</strong></p>
                    <p>• Upload bukti transfer saat mendaftar</p>
                    <p>• Pembayaran diverifikasi oleh panitia</p>
                </div>
            </div>
            
            <!-- Rule 5 -->
            <div class="border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
                <button @click="open = open === 5 ? null : 5" class="w-full flex items-center justify-between p-4 text-left font-semibold hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                    <span>Keselamatan & Disiplin</span>
                    <i data-lucide="chevron-down" class="w-5 h-5 transition-transform" :class="open === 5 ? 'rotate-180' : ''"></i>
                </button>
                <div x-show="open === 5" x-transition class="px-4 pb-4 text-sm text-gray-600 dark:text-gray-400 space-y-1">
                    <p>• Wajib menggunakan perlengkapan keselamatan (pelindung telinga & mata)</p>
                    <p>• Arah laras senjata selalu mengarah ke sasaran</p>
                    <p>• Dilarang mengarahkan senjata ke orang lain</p>
                    <p>• Keputusan wasit bersifat final dan mengikat</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Contact Section -->
<section id="kontak" class="py-16 bg-white dark:bg-gray-950">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="font-display text-3xl md:text-4xl font-bold text-center mb-12">Kontak Panitia</h2>
        
        <!-- Payment Info -->
        <div class="max-w-lg mx-auto mb-12 p-6 bg-copper-50 dark:bg-copper-900/20 border border-copper-200 dark:border-copper-800 rounded-xl text-center" x-data="{ copied: false }">
            <p class="text-sm text-copper-700 dark:text-copper-300 mb-2">Rekening Pembayaran</p>
            <p class="font-display text-xl font-bold text-copper-800 dark:text-copper-200">BRI</p>
            <button @click="navigator.clipboard.writeText('053801071906503'); copied = true; setTimeout(() => copied = false, 2000)" class="mt-2 px-4 py-2 bg-white dark:bg-gray-800 rounded-lg border border-copper-300 dark:border-copper-700 hover:bg-copper-100 dark:hover:bg-gray-700 transition text-sm font-mono">
                <span x-show="!copied">053801071906503 — <i data-lucide="copy" class="w-4 h-4 inline"></i></span>
                <span x-show="copied" class="text-green-600">Tersalin! ✓</span>
            </button>
            <p class="text-sm text-copper-600 dark:text-copper-400 mt-2">a.n. Ahyandi Hi Karim</p>
        </div>
        
        <!-- Contacts Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <?php
            $contacts = [
                ['role' => 'Materi & Teknis', 'name' => 'Briptu Ady', 'phone' => '085283525761', 'display' => '0852-8352-5761'],
                ['role' => 'Ketua Pelaksana', 'name' => 'Briptu Huges Yustisio', 'phone' => '085272377704', 'display' => '0852-7237-7704'],
                ['role' => 'Pendaftaran', 'name' => 'Briptu Rully', 'phone' => '085775015786', 'display' => '0857-7501-5786'],
                ['role' => 'Pendaftaran', 'name' => 'Briptu Zyaldi', 'phone' => '082134651503', 'display' => '0821-3465-1503'],
            ];
            foreach ($contacts as $c): ?>
            <a href="https://wa.me/<?= preg_replace('/^0/', '62', $c['phone']) ?>" target="_blank" 
               class="p-4 bg-gray-50 dark:bg-gray-900 rounded-xl hover:bg-green-50 dark:hover:bg-green-900/20 border border-gray-200 dark:border-gray-800 hover:border-green-400 dark:hover:border-green-700 transition group">
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1"><?= $c['role'] ?></p>
                <p class="font-semibold text-sm mb-1"><?= $c['name'] ?></p>
                <p class="text-sm text-gray-600 dark:text-gray-400 flex items-center gap-1">
                    <i data-lucide="message-circle" class="w-4 h-4 text-green-600 group-hover:text-green-500"></i>
                    <?= $c['display'] ?>
                </p>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-16 hero-gradient text-white">
    <div class="max-w-4xl mx-auto px-4 text-center">
        <h2 class="font-display text-3xl md:text-4xl font-bold mb-4">Siap Bertanding?</h2>
        <p class="text-gray-300 mb-8 max-w-xl mx-auto">Daftarkan diri Anda sekarang dan tunjukkan kemampuan menembak terbaik Anda di BDA Shooting Championship 2026.</p>
        <a href="/daftar.php" class="inline-block px-10 py-4 bg-copper-600 hover:bg-copper-700 text-white font-bold rounded-lg transition shadow-lg shadow-copper-600/25 text-lg">
            Daftar Sekarang
        </a>
    </div>
</section>

<script>
function countdown() {
    return {
        days: 0, hours: 0, minutes: 0, seconds: 0,
        target: new Date('2026-10-17T08:00:00+07:00').getTime(),
        start() {
            this.update();
            setInterval(() => this.update(), 1000);
        },
        update() {
            const now = Date.now();
            const diff = Math.max(0, this.target - now);
            this.days = Math.floor(diff / (1000 * 60 * 60 * 24));
            this.hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            this.minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
            this.seconds = Math.floor((diff % (1000 * 60)) / 1000);
        }
    }
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
