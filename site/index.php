<?php
$pageTitle = 'BDA Shooting Championship 2026';
$currentPage = 'home';
require_once __DIR__ . '/includes/header.php';
?>

<!-- Hero Section with Hero Image Showcase -->
<section class="relative hero-gradient text-white overflow-hidden">
    <div class="absolute inset-0 target-pattern opacity-25"></div>
    <!-- Ambient Glow behind hero image -->
    <div class="absolute top-1/2 right-1/4 -translate-y-1/2 w-96 h-96 bg-copper-500/20 rounded-full blur-3xl pointer-events-none"></div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 md:py-16">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-center">
            
            <!-- Left Column: Content & CTA (7 cols) -->
            <div class="lg:col-span-7 text-center lg:text-left space-y-4">
                
                <!-- Organizer Badge with Transparent BDA Logo -->
                <div class="inline-flex items-center gap-2.5 px-3.5 py-1.5 rounded-full bg-white/10 backdrop-blur-md border border-white/15 text-xs font-semibold text-copper-300">
                    <img src="/assets/logo-bda.png" alt="Logo BDA" class="h-6 w-6 object-contain" onerror="this.style.display='none'">
                    <span class="tracking-wide uppercase text-[11px]">Brigade Diraya Adikara (BDA) 750</span>
                </div>

                <!-- Main Headline -->
                <h1 class="font-display text-4xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight leading-[1.08] text-white">
                    BDA SHOOTING<br>
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-copper-400 via-amber-300 to-copper-500">
                        CHAMPIONSHIP 2026
                    </span>
                </h1>

                <!-- Subtitle -->
                <p class="text-base sm:text-lg text-gray-300 max-w-2xl mx-auto lg:mx-0 leading-relaxed">
                    Kejuaraan Menembak Pistol Presisi 20 Meter & Dueling Plat Speed dalam rangka memperingati Anniversary Letting BDA 750 ke-7.
                </p>

                <!-- Key Meta Badges -->
                <div class="flex flex-wrap justify-center lg:justify-start gap-3 pt-1 text-xs text-gray-200">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-white/5 border border-white/10 backdrop-blur-sm">
                        <i data-lucide="calendar" class="w-4 h-4 text-copper-400"></i>
                        <strong>17 — 18 Oktober 2026</strong>
                    </span>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-white/5 border border-white/10 backdrop-blur-sm">
                        <i data-lucide="map-pin" class="w-4 h-4 text-copper-400"></i>
                        Lap. Tembak Shooting House, Bogor
                    </span>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-copper-500/20 border border-copper-400/30 text-copper-300 font-semibold">
                        <i data-lucide="trophy" class="w-4 h-4 text-copper-400"></i>
                        Total Hadiah Jutaan Rupiah
                    </span>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-wrap justify-center lg:justify-start gap-3.5 pt-2">
                    <a href="/daftar.php" class="px-7 py-3 bg-gradient-to-r from-copper-600 to-copper-500 hover:from-copper-700 hover:to-copper-600 text-white font-bold rounded-xl transition shadow-lg shadow-copper-600/30 flex items-center gap-2 text-sm hover:-translate-y-0.5">
                        <i data-lucide="send" class="w-4 h-4"></i>
                        Daftar Sekarang
                    </a>
                    <a href="/live-score.php" class="px-6 py-3 bg-white/10 hover:bg-white/15 border border-white/20 text-white font-semibold rounded-xl transition backdrop-blur-sm flex items-center gap-2 text-sm hover:-translate-y-0.5">
                        <i data-lucide="crosshair" class="w-4 h-4 text-copper-400"></i>
                        Papan Live Score
                    </a>
                    <a href="#juknis" class="px-4 py-3 text-gray-300 hover:text-white font-medium text-sm flex items-center gap-1.5 transition">
                        <span>Petunjuk Teknis</span>
                        <i data-lucide="arrow-down" class="w-3.5 h-3.5"></i>
                    </a>
                </div>
            </div>

            <!-- Right Column: Prominent Hero Image (5 cols) -->
            <div class="lg:col-span-5 flex justify-center items-center">
                <div class="relative group">
                    <!-- Ambient Glow Behind Logo -->
                    <div class="absolute -inset-4 bg-gradient-to-r from-copper-600/40 via-amber-500/30 to-copper-700/40 rounded-full blur-2xl opacity-75 group-hover:opacity-100 transition duration-500"></div>
                    
                    <!-- Circular Target Ring Ornament -->
                    <div class="relative w-64 h-64 sm:w-80 sm:h-80 md:w-96 md:h-96 rounded-full p-4 flex items-center justify-center">
                        <div class="absolute inset-0 rounded-full border-2 border-dashed border-copper-400/30 animate-[spin_60s_linear_infinite]"></div>
                        <div class="absolute inset-4 rounded-full border border-copper-400/20"></div>
                        
                        <!-- Main Hero Image: Transparent BDA Shooting Championship Logo -->
                        <img src="/assets/logo-championship.png" 
                             alt="Logo BDA Shooting Championship 2026" 
                             class="relative z-10 w-full h-full object-contain drop-shadow-[0_20px_35px_rgba(0,0,0,0.6)] transform group-hover:scale-105 transition-transform duration-500">
                    </div>

                    <!-- Floating Quality Badge -->
                    <div class="absolute -bottom-2 left-1/2 -translate-x-1/2 px-4 py-1.5 bg-gray-950/80 backdrop-blur-md rounded-full border border-copper-400/40 text-[11px] font-bold text-copper-300 whitespace-nowrap shadow-xl flex items-center gap-1.5">
                        <i data-lucide="award" class="w-3.5 h-3.5 text-copper-400"></i>
                        <span>Resimen I Pasukan Pelopor</span>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Decorative bottom divider -->
    <div class="h-4 bg-gradient-to-b from-transparent to-white dark:to-gray-950"></div>
</section>

<!-- Countdown Section (Tight Padding) -->
<section class="py-6 md:py-8 bg-white dark:bg-gray-950 border-b border-gray-100 dark:border-gray-800" x-data="countdown()" x-init="start()">
    <div class="max-w-4xl mx-auto px-4 text-center">
        <div class="flex items-center justify-center gap-2 mb-2">
            <i data-lucide="timer" class="w-4 h-4 text-copper-600 dark:text-copper-400"></i>
            <h2 class="font-display text-xl sm:text-2xl font-bold uppercase tracking-wider text-gray-900 dark:text-white">Hitung Mundur Menuju Hari-H</h2>
        </div>
        <p class="text-xs text-gray-500 dark:text-gray-400 mb-4">17 — 18 Oktober 2026 &bull; Lapangan Tembak Shooting House Brimob Bogor</p>
        
        <div class="grid grid-cols-4 gap-3 sm:gap-4 max-w-md mx-auto">
            <div class="bg-gray-50 dark:bg-gray-900 rounded-xl p-3 border border-gray-200 dark:border-gray-800 shadow-sm">
                <span class="font-display text-2xl sm:text-4xl font-bold text-copper-600 dark:text-copper-400" x-text="days">0</span>
                <p class="text-[10px] text-gray-500 uppercase tracking-wider font-semibold mt-0.5">Hari</p>
            </div>
            <div class="bg-gray-50 dark:bg-gray-900 rounded-xl p-3 border border-gray-200 dark:border-gray-800 shadow-sm">
                <span class="font-display text-2xl sm:text-4xl font-bold text-copper-600 dark:text-copper-400" x-text="hours">0</span>
                <p class="text-[10px] text-gray-500 uppercase tracking-wider font-semibold mt-0.5">Jam</p>
            </div>
            <div class="bg-gray-50 dark:bg-gray-900 rounded-xl p-3 border border-gray-200 dark:border-gray-800 shadow-sm">
                <span class="font-display text-2xl sm:text-4xl font-bold text-copper-600 dark:text-copper-400" x-text="minutes">0</span>
                <p class="text-[10px] text-gray-500 uppercase tracking-wider font-semibold mt-0.5">Menit</p>
            </div>
            <div class="bg-gray-50 dark:bg-gray-900 rounded-xl p-3 border border-gray-200 dark:border-gray-800 shadow-sm">
                <span class="font-display text-2xl sm:text-4xl font-bold text-copper-600 dark:text-copper-400" x-text="seconds">0</span>
                <p class="text-[10px] text-gray-500 uppercase tracking-wider font-semibold mt-0.5">Detik</p>
            </div>
        </div>
    </div>
</section>

<!-- About Section (Tight Padding) -->
<section id="tentang" class="py-8 md:py-10 bg-gray-50 dark:bg-gray-900/60">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-6">
            <span class="text-xs font-bold uppercase tracking-widest text-copper-600 dark:text-copper-400">Tentang Kejuaraan</span>
            <h2 class="font-display text-2xl sm:text-3xl font-bold mt-1">Kejuaraan Menembak BDA 750</h2>
            <p class="text-xs sm:text-sm text-gray-600 dark:text-gray-400 max-w-2xl mx-auto mt-1">
                Diselenggarakan oleh Brigade Diraya Adikara (BDA) 750 dalam rangka mempererat silaturahmi, sportivitas, dan mengasah ketangkasan menembak bagi anggota Polri, TNI, serta masyarakat sipil.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 sm:gap-6">
            <div class="bg-white dark:bg-gray-800 rounded-xl p-5 border border-gray-200 dark:border-gray-700 shadow-sm hover:border-copper-400 transition">
                <div class="w-10 h-10 rounded-lg bg-copper-100 dark:bg-copper-900/30 flex items-center justify-center mb-3">
                    <i data-lucide="crosshair" class="w-5 h-5 text-copper-600 dark:text-copper-400"></i>
                </div>
                <h3 class="font-display text-lg font-bold mb-1">2 Kategori Lomba</h3>
                <p class="text-xs text-gray-600 dark:text-gray-400 leading-relaxed">
                    Pistol Presisi 20 Meter lesan ring dan Dueling Plat Speed sistem gugur head-to-head.
                </p>
            </div>
            
            <div class="bg-white dark:bg-gray-800 rounded-xl p-5 border border-gray-200 dark:border-gray-700 shadow-sm hover:border-copper-400 transition">
                <div class="w-10 h-10 rounded-lg bg-copper-100 dark:bg-copper-900/30 flex items-center justify-center mb-3">
                    <i data-lucide="trophy" class="w-5 h-5 text-copper-600 dark:text-copper-400"></i>
                </div>
                <h3 class="font-display text-lg font-bold mb-1">Hadiah Pembinaan</h3>
                <p class="text-xs text-gray-600 dark:text-gray-400 leading-relaxed">
                    Uang pembinaan Juara I, II, dan III per kategori + Trophy + Sertifikat penghargaan resmi.
                </p>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl p-5 border border-gray-200 dark:border-gray-700 shadow-sm hover:border-copper-400 transition">
                <div class="w-10 h-10 rounded-lg bg-copper-100 dark:bg-copper-900/30 flex items-center justify-center mb-3">
                    <i data-lucide="users" class="w-5 h-5 text-copper-600 dark:text-copper-400"></i>
                </div>
                <h3 class="font-display text-lg font-bold mb-1">Terbuka Umum</h3>
                <p class="text-xs text-gray-600 dark:text-gray-400 leading-relaxed">
                    Dapat diikuti oleh anggota Polri aktif, TNI, club menembak Perbakin, maupun masyarakat umum.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Categories Section (Tight Padding) -->
<section id="kategori" class="py-8 md:py-10 bg-white dark:bg-gray-950">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-6">
            <span class="text-xs font-bold uppercase tracking-widest text-copper-600 dark:text-copper-400">Detail Pertandingan</span>
            <h2 class="font-display text-2xl sm:text-3xl font-bold mt-1">Kategori Lomba</h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Presisi 20M -->
            <div class="border border-gray-200 dark:border-gray-800 rounded-2xl p-6 hover:border-copper-400 transition bg-gray-50/50 dark:bg-gray-900/40">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-xl bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                        <i data-lucide="crosshair" class="w-5 h-5 text-blue-600 dark:text-blue-400"></i>
                    </div>
                    <div>
                        <h3 class="font-display text-xl font-bold">Pistol Presisi 20M</h3>
                        <p class="text-[11px] text-gray-500">Kelas Individu (Lesan Ring Target)</p>
                    </div>
                </div>
                <ul class="space-y-2 text-xs text-gray-600 dark:text-gray-400">
                    <li class="flex items-center gap-2"><i data-lucide="check-circle" class="w-3.5 h-3.5 text-copper-500 shrink-0"></i><strong>Jarak Tembak:</strong> 20 meter</li>
                    <li class="flex items-center gap-2"><i data-lucide="check-circle" class="w-3.5 h-3.5 text-copper-500 shrink-0"></i><strong>Sikap Tembak:</strong> Berdiri, 2 tangan</li>
                    <li class="flex items-center gap-2"><i data-lucide="check-circle" class="w-3.5 h-3.5 text-copper-500 shrink-0"></i><strong>Sasaran:</strong> Lesan Ring (Nilai 1 - 10 + Inner X)</li>
                    <li class="flex items-center gap-2"><i data-lucide="check-circle" class="w-3.5 h-3.5 text-copper-500 shrink-0"></i><strong>Amunisi:</strong> 13 butir (3 percobaan + 10 penilaian)</li>
                    <li class="flex items-center gap-2"><i data-lucide="check-circle" class="w-3.5 h-3.5 text-copper-500 shrink-0"></i><strong>Batas Waktu:</strong> 3 Menit</li>
                    <li class="flex items-center gap-2"><i data-lucide="check-circle" class="w-3.5 h-3.5 text-copper-500 shrink-0"></i><strong>Biaya Registrasi:</strong> Rp 200.000</li>
                </ul>
            </div>
            
            <!-- Dueling Plat -->
            <div class="border border-gray-200 dark:border-gray-800 rounded-2xl p-6 hover:border-copper-400 transition bg-gray-50/50 dark:bg-gray-900/40">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-xl bg-red-100 dark:bg-red-900/30 flex items-center justify-center">
                        <i data-lucide="swords" class="w-5 h-5 text-red-600 dark:text-red-400"></i>
                    </div>
                    <div>
                        <h3 class="font-display text-xl font-bold">Dueling Plat Speed</h3>
                        <p class="text-[11px] text-gray-500">Kelas Individu (Eliminasi Head-to-Head)</p>
                    </div>
                </div>
                <ul class="space-y-2 text-xs text-gray-600 dark:text-gray-400">
                    <li class="flex items-center gap-2"><i data-lucide="check-circle" class="w-3.5 h-3.5 text-copper-500 shrink-0"></i><strong>Jarak Tembak:</strong> 15 meter</li>
                    <li class="flex items-center gap-2"><i data-lucide="check-circle" class="w-3.5 h-3.5 text-copper-500 shrink-0"></i><strong>Sikap Tembak:</strong> Berdiri (lari 10 meter menuju meja senjata)</li>
                    <li class="flex items-center gap-2"><i data-lucide="check-circle" class="w-3.5 h-3.5 text-copper-500 shrink-0"></i><strong>Sasaran:</strong> 5 plat bulat + 1 stop popper</li>
                    <li class="flex items-center gap-2"><i data-lucide="check-circle" class="w-3.5 h-3.5 text-copper-500 shrink-0"></i><strong>Amunisi:</strong> 10 butir per putaran match</li>
                    <li class="flex items-center gap-2"><i data-lucide="check-circle" class="w-3.5 h-3.5 text-copper-500 shrink-0"></i><strong>Sistem:</strong> Catatan waktu tercepat melaju ke babak berikutnya</li>
                    <li class="flex items-center gap-2"><i data-lucide="check-circle" class="w-3.5 h-3.5 text-copper-500 shrink-0"></i><strong>Biaya Registrasi:</strong> Rp 200.000</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- Prizes Section (Updated: Juara 3 = Rp 1.000.000) -->
<section id="hadiah" class="py-8 md:py-10 bg-gray-50 dark:bg-gray-900/60">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-6">
            <span class="text-xs font-bold uppercase tracking-widest text-copper-600 dark:text-copper-400">Penghargaan</span>
            <h2 class="font-display text-2xl sm:text-3xl font-bold mt-1">Hadiah Pemenang</h2>
            <p class="text-xs text-gray-500 mt-0.5">Diberikan untuk masing-masing kategori lomba</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-5 max-w-4xl mx-auto items-end">
            <!-- Juara 2 -->
            <div class="order-2 md:order-1 text-center bg-white dark:bg-gray-800 rounded-2xl p-6 border border-gray-200 dark:border-gray-700 shadow-sm">
                <div class="w-12 h-12 mx-auto mb-3 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center font-display text-xl font-bold text-gray-700 dark:text-gray-200">
                    2
                </div>
                <h3 class="font-display text-lg font-bold text-gray-800 dark:text-gray-200 mb-1">Juara II</h3>
                <p class="font-display text-2xl font-bold text-copper-600 dark:text-copper-400 mb-1">Rp 2.000.000</p>
                <p class="text-xs text-gray-500">+ Trophy + Piagam Resmi</p>
            </div>
            
            <!-- Juara 1 (Featured) -->
            <div class="order-1 md:order-2 text-center bg-gradient-to-b from-copper-50 via-white to-copper-50/30 dark:from-gray-800 dark:to-gray-850 rounded-2xl p-7 border-2 border-copper-500 shadow-xl relative">
                <span class="absolute -top-3 left-1/2 -translate-x-1/2 px-3 py-0.5 bg-copper-600 text-white text-[10px] font-bold uppercase tracking-widest rounded-full shadow-md">
                    Utama
                </span>
                <div class="w-14 h-14 mx-auto mb-3 rounded-full bg-copper-100 dark:bg-copper-900/40 flex items-center justify-center text-copper-600 dark:text-copper-400">
                    <i data-lucide="crown" class="w-7 h-7"></i>
                </div>
                <h3 class="font-display text-xl font-bold text-copper-700 dark:text-copper-300 mb-1">Juara I</h3>
                <p class="font-display text-3xl font-extrabold text-copper-600 dark:text-copper-400 mb-1">Rp 3.000.000</p>
                <p class="text-xs text-gray-600 dark:text-gray-300 font-semibold">+ Trophy Bergilir + Piagam Resmi</p>
            </div>
            
            <!-- Juara 3 (UPDATED TO 1 JUTA) -->
            <div class="order-3 text-center bg-white dark:bg-gray-800 rounded-2xl p-6 border border-gray-200 dark:border-gray-700 shadow-sm">
                <div class="w-12 h-12 mx-auto mb-3 rounded-full bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center font-display text-xl font-bold text-amber-700 dark:text-amber-400">
                    3
                </div>
                <h3 class="font-display text-lg font-bold text-gray-800 dark:text-gray-200 mb-1">Juara III</h3>
                <p class="font-display text-2xl font-bold text-copper-600 dark:text-copper-400 mb-1">Rp 1.000.000</p>
                <p class="text-xs text-gray-500">+ Trophy + Piagam Resmi</p>
            </div>
        </div>
    </div>
</section>

<!-- Schedule Section (Tight Padding) -->
<section id="jadwal" class="py-8 md:py-10 bg-white dark:bg-gray-950">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-6">
            <span class="text-xs font-bold uppercase tracking-widest text-copper-600 dark:text-copper-400">Rundown Acara</span>
            <h2 class="font-display text-2xl sm:text-3xl font-bold mt-1">Jadwal Pertandingan</h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Day 1 -->
            <div class="p-5 bg-gray-50 dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800">
                <div class="flex items-center gap-2 mb-3">
                    <span class="px-2.5 py-1 bg-copper-600 text-white rounded font-display font-bold text-xs">Hari ke-1</span>
                    <h3 class="font-display font-bold text-base">Jumat, 17 Oktober 2026</h3>
                </div>
                <ul class="space-y-1.5 text-xs text-gray-600 dark:text-gray-400">
                    <li class="flex justify-between pb-1 border-b border-gray-200 dark:border-gray-800"><span>07:00 WIB</span><span class="font-medium text-gray-800 dark:text-gray-200">Daftar Ulang & Pengecekan Senjata</span></li>
                    <li class="flex justify-between pb-1 border-b border-gray-200 dark:border-gray-800"><span>08:00 WIB</span><span class="font-medium text-gray-800 dark:text-gray-200">Upacara Pembukaan Resmi</span></li>
                    <li class="flex justify-between pb-1 border-b border-gray-200 dark:border-gray-800"><span>09:00 WIB</span><span class="font-medium text-gray-800 dark:text-gray-200">Babak Penyisihan Presisi 20M</span></li>
                    <li class="flex justify-between pb-1 border-b border-gray-200 dark:border-gray-800"><span>13:30 WIB</span><span class="font-medium text-gray-800 dark:text-gray-200">Babak Penyisihan Dueling Plat</span></li>
                    <li class="flex justify-between"><span>17:00 WIB</span><span class="font-medium text-gray-800 dark:text-gray-200">Penutupan Hari Pertama</span></li>
                </ul>
            </div>

            <!-- Day 2 -->
            <div class="p-5 bg-gray-50 dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800">
                <div class="flex items-center gap-2 mb-3">
                    <span class="px-2.5 py-1 bg-copper-600 text-white rounded font-display font-bold text-xs">Hari ke-2</span>
                    <h3 class="font-display font-bold text-base">Sabtu, 18 Oktober 2026</h3>
                </div>
                <ul class="space-y-1.5 text-xs text-gray-600 dark:text-gray-400">
                    <li class="flex justify-between pb-1 border-b border-gray-200 dark:border-gray-800"><span>08:00 WIB</span><span class="font-medium text-gray-800 dark:text-gray-200">Babak Final Pistol Presisi 20M</span></li>
                    <li class="flex justify-between pb-1 border-b border-gray-200 dark:border-gray-800"><span>10:00 WIB</span><span class="font-medium text-gray-800 dark:text-gray-200">Semifinal & Final Dueling Plat</span></li>
                    <li class="flex justify-between pb-1 border-b border-gray-200 dark:border-gray-800"><span>14:00 WIB</span><span class="font-medium text-gray-800 dark:text-gray-200">Rekapitulasi Poin & Verifikasi Wasit</span></li>
                    <li class="flex justify-between pb-1 border-b border-gray-200 dark:border-gray-800"><span>15:30 WIB</span><span class="font-medium text-gray-800 dark:text-gray-200">Upacara Penutupan & Penyerahan Hadiah</span></li>
                    <li class="flex justify-between"><span>17:00 WIB</span><span class="font-medium text-gray-800 dark:text-gray-200">Selesai & Ramah Tamah</span></li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- Rules Section (Accordion, Tight Padding) -->
<section id="juknis" class="py-8 md:py-10 bg-gray-50 dark:bg-gray-900/60">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-6">
            <span class="text-xs font-bold uppercase tracking-widest text-copper-600 dark:text-copper-400">Petunjuk Teknis</span>
            <h2 class="font-display text-2xl sm:text-3xl font-bold mt-1">Peraturan & Persyaratan</h2>
        </div>

        <div class="space-y-2.5" x-data="{ open: 1 }">
            <!-- Rule 1 -->
            <div class="border border-gray-200 dark:border-gray-800 rounded-xl overflow-hidden bg-white dark:bg-gray-800">
                <button @click="open = open === 1 ? null : 1" class="w-full flex items-center justify-between p-3.5 text-left font-semibold hover:bg-gray-50 dark:hover:bg-gray-700/50 transition text-sm">
                    <span class="flex items-center gap-2"><i data-lucide="user-check" class="w-4 h-4 text-copper-500"></i> Persyaratan Peserta</span>
                    <i data-lucide="chevron-down" class="w-4 h-4 transition-transform" :class="open === 1 ? 'rotate-180' : ''"></i>
                </button>
                <div x-show="open === 1" x-transition class="px-4 pb-3.5 text-xs text-gray-600 dark:text-gray-400 space-y-1 border-t border-gray-100 dark:border-gray-700/50 pt-2.5">
                    <p>&bull; Anggota Polri aktif / purnawirawan, TNI, atau anggota club menembak sipil.</p>
                    <p>&bull; Memiliki KTA (Kartu Tanda Anggota) Polri/TNI/KTP yang sah dan diunggah saat pendaftaran.</p>
                    <p>&bull; Melakukan pendaftaran resmi secara online dan menyelesaikan biaya pendaftaran.</p>
                </div>
            </div>
            
            <!-- Rule 2 -->
            <div class="border border-gray-200 dark:border-gray-800 rounded-xl overflow-hidden bg-white dark:bg-gray-800">
                <button @click="open = open === 2 ? null : 2" class="w-full flex items-center justify-between p-3.5 text-left font-semibold hover:bg-gray-50 dark:hover:bg-gray-700/50 transition text-sm">
                    <span class="flex items-center gap-2"><i data-lucide="crosshair" class="w-4 h-4 text-copper-500"></i> Ketentuan Senjata & Amunisi</span>
                    <i data-lucide="chevron-down" class="w-4 h-4 transition-transform" :class="open === 2 ? 'rotate-180' : ''"></i>
                </button>
                <div x-show="open === 2" x-transition class="px-4 pb-3.5 text-xs text-gray-600 dark:text-gray-400 space-y-1 border-t border-gray-100 dark:border-gray-700/50 pt-2.5">
                    <p>&bull; Pistol kaliber 9mm organik/standar laik tembak.</p>
                    <p>&bull; Pemeriksaan senjata (gun check) akan dilakukan panitia sebelum pertandingan dimulai.</p>
                    <p>&bull; Wajib menggunakan safety glasses dan pelindung telinga (earmuff/earplug).</p>
                </div>
            </div>

            <!-- Rule 3 -->
            <div class="border border-gray-200 dark:border-gray-800 rounded-xl overflow-hidden bg-white dark:bg-gray-800">
                <button @click="open = open === 3 ? null : 3" class="w-full flex items-center justify-between p-3.5 text-left font-semibold hover:bg-gray-50 dark:hover:bg-gray-700/50 transition text-sm">
                    <span class="flex items-center gap-2"><i data-lucide="calculator" class="w-4 h-4 text-copper-500"></i> Sistem Penilaian & Ranking</span>
                    <i data-lucide="chevron-down" class="w-4 h-4 transition-transform" :class="open === 3 ? 'rotate-180' : ''"></i>
                </button>
                <div x-show="open === 3" x-transition class="px-4 pb-3.5 text-xs text-gray-600 dark:text-gray-400 space-y-1.5 border-t border-gray-100 dark:border-gray-700/50 pt-2.5">
                    <p><strong>Presisi 20M:</strong> Total akumulasi poin dari 10 tembakan seri. Jika terjadi draw/skor sama, penentu pemenang dihitung dari jumlah tembakan X (inner-10) terbanyak.</p>
                    <p><strong>Dueling Plat:</strong> Sistem gugur langsung head-to-head. Pemenang adalah penembak yang menjatuhkan 5 plat + 1 popper dengan waktu tercepat.</p>
                </div>
            </div>

            <!-- Rule 4 -->
            <div class="border border-gray-200 dark:border-gray-800 rounded-xl overflow-hidden bg-white dark:bg-gray-800">
                <button @click="open = open === 4 ? null : 4" class="w-full flex items-center justify-between p-3.5 text-left font-semibold hover:bg-gray-50 dark:hover:bg-gray-700/50 transition text-sm">
                    <span class="flex items-center gap-2"><i data-lucide="credit-card" class="w-4 h-4 text-copper-500"></i> Biaya Registrasi & Pembayaran</span>
                    <i data-lucide="chevron-down" class="w-4 h-4 transition-transform" :class="open === 4 ? 'rotate-180' : ''"></i>
                </button>
                <div x-show="open === 4" x-transition class="px-4 pb-3.5 text-xs text-gray-600 dark:text-gray-400 space-y-1 border-t border-gray-100 dark:border-gray-700/50 pt-2.5">
                    <p>&bull; Biaya: <strong>Rp 200.000 per kategori</strong> (Mengikuti 2 kategori: Rp 400.000).</p>
                    <p>&bull; Transfer ke rekening: <strong>Bank BRI 053801071906503</strong> a.n. <strong>Ahyandi Hi Karim</strong>.</p>
                    <p>&bull; Lampirkan bukti transfer saat pengisian formulir pendaftaran.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Contact & Payment Section (Tight Padding) -->
<section id="kontak" class="py-8 md:py-10 bg-white dark:bg-gray-950">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-6">
            <span class="text-xs font-bold uppercase tracking-widest text-copper-600 dark:text-copper-400">Informasi & Bantuan</span>
            <h2 class="font-display text-2xl sm:text-3xl font-bold mt-1">Kontak Panitia Pelaksana</h2>
        </div>
        
        <!-- Payment Info Box -->
        <div class="max-w-md mx-auto mb-6 p-4 bg-copper-50 dark:bg-copper-900/20 border border-copper-200 dark:border-copper-800 rounded-xl text-center" x-data="{ copied: false }">
            <p class="text-xs uppercase font-bold text-copper-700 dark:text-copper-300">Rekening Resmi Pendaftaran</p>
            <p class="font-display text-lg font-bold text-gray-900 dark:text-white mt-1">Bank BRI</p>
            <div class="mt-2 flex items-center justify-center gap-2">
                <span class="font-mono text-base font-bold text-copper-700 dark:text-copper-300">053801071906503</span>
                <button type="button" @click="navigator.clipboard.writeText('053801071906503'); copied = true; setTimeout(() => copied = false, 2000)" 
                        class="px-2.5 py-1 bg-white dark:bg-gray-800 rounded-md border border-copper-300 dark:border-copper-700 hover:bg-copper-100 text-xs font-semibold transition">
                    <span x-show="!copied">Salin</span>
                    <span x-show="copied" class="text-green-600">Tersalin!</span>
                </button>
            </div>
            <p class="text-[11px] text-gray-500 mt-1">a.n. Ahyandi Hi Karim</p>
        </div>
        
        <!-- Contacts Grid -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
            <?php
            $contacts = [
                ['role' => 'Materi & Teknis', 'name' => 'Briptu Ady', 'phone' => '085283525761', 'display' => '0852-8352-5761'],
                ['role' => 'Ketua Pelaksana', 'name' => 'Briptu Huges Yustisio', 'phone' => '085272377704', 'display' => '0852-7237-7704'],
                ['role' => 'Pendaftaran', 'name' => 'Briptu Rully', 'phone' => '085775015786', 'display' => '0857-7501-5786'],
                ['role' => 'Pendaftaran', 'name' => 'Briptu Zyaldi', 'phone' => '082134651503', 'display' => '0821-3465-1503'],
            ];
            foreach ($contacts as $c): ?>
            <a href="https://wa.me/<?= preg_replace('/^0/', '62', $c['phone']) ?>" target="_blank" 
               class="p-3.5 bg-gray-50 dark:bg-gray-900 rounded-xl hover:bg-green-50 dark:hover:bg-green-950/20 border border-gray-200 dark:border-gray-800 hover:border-green-400 transition group text-left">
                <p class="text-[10px] text-gray-500 uppercase tracking-wider mb-0.5"><?= $c['role'] ?></p>
                <p class="font-bold text-xs text-gray-900 dark:text-white"><?= $c['name'] ?></p>
                <p class="text-[11px] text-gray-500 group-hover:text-green-600 flex items-center gap-1 mt-1">
                    <i data-lucide="message-circle" class="w-3.5 h-3.5 text-green-500"></i>
                    <?= $c['display'] ?>
                </p>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Compact CTA Section -->
<section class="py-10 md:py-12 hero-gradient text-white text-center relative overflow-hidden">
    <div class="absolute inset-0 target-pattern opacity-20"></div>
    <div class="relative max-w-3xl mx-auto px-4">
        <h2 class="font-display text-2xl sm:text-3xl font-bold mb-2">Daftarkan Diri Anda Sekarang</h2>
        <p class="text-xs sm:text-sm text-gray-300 mb-5 max-w-lg mx-auto">
            Kuota peserta terbatas untuk menjamin kenyamanan dan standar keselamatan kejuaraan.
        </p>
        <div class="flex flex-wrap justify-center gap-3">
            <a href="/daftar.php" class="px-8 py-3 bg-gradient-to-r from-copper-600 to-copper-500 hover:from-copper-700 hover:to-copper-600 text-white font-bold rounded-xl transition shadow-lg shadow-copper-600/30 text-sm">
                Isi Formulir Pendaftaran
            </a>
            <a href="/live-score.php" class="px-6 py-3 bg-white/10 hover:bg-white/20 border border-white/20 text-white font-semibold rounded-xl transition text-sm">
                Lihat Live Score
            </a>
        </div>
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
