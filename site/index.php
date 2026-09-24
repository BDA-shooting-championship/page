<?php
$pageTitle = 'BDA Shooting Championship 2026';
$currentPage = 'home';
require_once __DIR__ . '/includes/header.php';
?>

<!-- Hero Section with Requested Flow & White Canvas Logo -->
<section class="relative hero-gradient text-white overflow-hidden">
    <div class="absolute inset-0 target-pattern opacity-25"></div>
    <!-- Ambient Glow -->
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[500px] h-[500px] bg-copper-500/15 rounded-full blur-3xl pointer-events-none"></div>

    <div class="relative max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12 text-center">
        <!-- 1. Organizer Badge with Transparent BDA Logo -->
        <div class="inline-flex items-center gap-2.5 px-3.5 py-1.5 rounded-full bg-white/10 backdrop-blur-md border border-white/15 text-xs font-semibold text-copper-300 mb-3">
            <img src="/assets/logo-bda.png" alt="Logo BDA" class="h-6 w-6 object-contain" onerror="this.style.display='none'">
            <span class="tracking-wide uppercase text-[11px]">Brigade Diraya Adikara (BDA) 750</span>
        </div>

        <!-- 2. Main Headline -->
        <h1 class="font-display text-4xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight leading-[1.08] text-white mb-3">
            BDA SHOOTING<br>
            <span class="text-transparent bg-clip-text bg-gradient-to-r from-copper-400 via-amber-300 to-copper-500">
                CHAMPIONSHIP 2026
            </span>
        </h1>

        <!-- 3. Deskripsi Kejuaraan Menembak Pistol -->
        <p class="text-sm sm:text-base md:text-lg text-gray-300 max-w-2xl mx-auto leading-relaxed mb-6">
            Kejuaraan Menembak Pistol Presisi 20 Meter &amp; Dueling Plat Speed dalam rangka memperingati Anniversary Letting BDA 750 ke-7.
        </p>

        <!-- 4. Logo BDA Shooting dengan Latar Belakang Putih & Dual Concentric Reticle Motion -->
        <div class="flex justify-center items-center mb-6" x-data="heroLogoTilt()" @mousemove="handleMouseMove($event)" @mouseleave="handleMouseLeave()">
            <div class="relative group">
                <!-- Outer Reticle Ring (Clockwise Rotation 50s) with Degree Ticks -->
                <div class="absolute -inset-8 sm:-inset-11 md:-inset-14 pointer-events-none flex items-center justify-center">
                    <svg class="w-full h-full text-copper-400/35 animate-[spin_50s_linear_infinite]" viewBox="0 0 200 200" fill="none">
                        <circle cx="100" cy="100" r="94" stroke="currentColor" stroke-width="1.2" stroke-dasharray="4 8" />
                        <circle cx="100" cy="100" r="88" stroke="currentColor" stroke-width="0.8" stroke-dasharray="1 10" opacity="0.6" />
                        <!-- Degree ticks -->
                        <line x1="100" y1="2" x2="100" y2="10" stroke="currentColor" stroke-width="2" />
                        <line x1="100" y1="190" x2="100" y2="198" stroke="currentColor" stroke-width="2" />
                        <line x1="2" y1="100" x2="10" y2="100" stroke="currentColor" stroke-width="2" />
                        <line x1="190" y1="100" x2="198" y2="100" stroke="currentColor" stroke-width="2" />
                        <!-- Degree coordinate labels -->
                        <text x="100" y="16" fill="currentColor" font-size="4.5" text-anchor="middle" font-family="monospace">000°</text>
                        <text x="186" y="101.5" fill="currentColor" font-size="4.5" text-anchor="middle" font-family="monospace">090°</text>
                        <text x="100" y="188" fill="currentColor" font-size="4.5" text-anchor="middle" font-family="monospace">180°</text>
                        <text x="14" y="101.5" fill="currentColor" font-size="4.5" text-anchor="middle" font-family="monospace">270°</text>
                    </svg>
                </div>

                <!-- Inner Crosshair Reticle Ring (Counter-Clockwise Rotation 30s) -->
                <div class="absolute -inset-4 sm:-inset-6 md:-inset-8 pointer-events-none flex items-center justify-center">
                    <svg class="w-full h-full text-amber-400/30 animate-[spin_30s_linear_infinite_reverse]" viewBox="0 0 200 200" fill="none">
                        <circle cx="100" cy="100" r="82" stroke="currentColor" stroke-width="1" stroke-dasharray="24 16" />
                        <line x1="100" y1="14" x2="100" y2="28" stroke="currentColor" stroke-width="1.5" />
                        <line x1="100" y1="172" x2="100" y2="186" stroke="currentColor" stroke-width="1.5" />
                        <line x1="14" y1="100" x2="28" y2="100" stroke="currentColor" stroke-width="1.5" />
                        <line x1="172" y1="100" x2="186" y2="100" stroke="currentColor" stroke-width="1.5" />
                    </svg>
                </div>

                <!-- Soft Glow Behind White Canvas -->
                <div class="absolute -inset-4 bg-gradient-to-r from-copper-500/40 via-amber-400/30 to-copper-600/40 rounded-full blur-2xl opacity-75 group-hover:opacity-100 transition duration-700 pointer-events-none"></div>

                <!-- White Canvas Container for Logo with 3D Parallax Tilt -->
                <div class="relative w-56 h-56 sm:w-72 sm:h-72 md:w-80 md:h-80 lg:w-96 lg:h-96 rounded-full bg-white shadow-2xl p-5 sm:p-7 md:p-8 flex items-center justify-center border-4 sm:border-8 border-copper-400/60 ring-4 sm:ring-8 ring-white/20 transition-transform duration-200 will-change-transform cursor-pointer"
                     :style="'transform: perspective(1000px) rotateX(' + tiltX + 'deg) rotateY(' + tiltY + 'deg) scale3d(' + (isHovered ? '1.04, 1.04, 1.04' : '1, 1, 1') + ')'"
                     @mouseenter="isHovered = true"
                     @mouseleave="isHovered = false">
                    <img src="/assets/logo-championship.png" 
                         alt="Logo BDA Shooting Championship 2026" 
                         class="w-full h-full object-contain drop-shadow-xl select-none pointer-events-none">
                </div>
            </div>
        </div>

        <!-- 5. Tombol Daftar dan Live Skor Sejajar dengan Tactical HUD Micro-Interactions -->
        <div class="flex flex-row justify-center items-center gap-3 sm:gap-4 mb-3">
            <!-- Tombol Daftar dengan HUD Corner Brackets -->
            <a href="/daftar.php" class="relative group px-6 sm:px-8 py-3 bg-gradient-to-r from-copper-600 to-copper-500 hover:from-copper-700 hover:to-copper-600 text-white font-bold rounded-xl transition-all duration-300 shadow-lg shadow-copper-600/30 flex items-center justify-center gap-2 text-sm hover:-translate-y-0.5 overflow-hidden">
                <!-- Tactical Corner Locks -->
                <span class="absolute top-1 left-1 w-2 h-2 border-t-2 border-l-2 border-amber-300 opacity-40 group-hover:opacity-100 group-hover:scale-110 transition-all duration-200"></span>
                <span class="absolute top-1 right-1 w-2 h-2 border-t-2 border-r-2 border-amber-300 opacity-40 group-hover:opacity-100 group-hover:scale-110 transition-all duration-200"></span>
                <span class="absolute bottom-1 left-1 w-2 h-2 border-b-2 border-l-2 border-amber-300 opacity-40 group-hover:opacity-100 group-hover:scale-110 transition-all duration-200"></span>
                <span class="absolute bottom-1 right-1 w-2 h-2 border-b-2 border-r-2 border-amber-300 opacity-40 group-hover:opacity-100 group-hover:scale-110 transition-all duration-200"></span>
                
                <i data-lucide="send" class="w-4 h-4 group-hover:translate-x-0.5 transition-transform"></i>
                <span class="tracking-wide">Daftar</span>
            </a>

            <!-- Tombol Live Skor dengan Real-time Radar Beacon -->
            <a href="/live-score.php" class="relative group px-5 sm:px-7 py-3 bg-white/10 hover:bg-white/15 border border-white/20 hover:border-copper-400/50 text-white font-semibold rounded-xl transition-all duration-300 backdrop-blur-sm flex items-center justify-center gap-2 text-sm hover:-translate-y-0.5 shadow-sm">
                <!-- Radar Beacon Indicator -->
                <span class="relative flex h-2 w-2">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-copper-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-copper-500"></span>
                </span>
                <i data-lucide="crosshair" class="w-4 h-4 text-copper-400 group-hover:rotate-45 transition-transform duration-300"></i>
                <span class="tracking-wide">Live Skor</span>
            </a>
        </div>

        <!-- 6. Klik Petunjuk Teknis -->
        <div class="mb-6">
            <a href="#juknis" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full bg-white/5 hover:bg-white/10 border border-white/10 text-xs sm:text-sm text-copper-300 hover:text-white transition font-medium group">
                <i data-lucide="file-text" class="w-4 h-4 text-copper-400 group-hover:scale-110 transition"></i>
                <span>Klik Petunjuk Teknis</span>
                <i data-lucide="arrow-down" class="w-3.5 h-3.5 text-copper-400 group-hover:translate-y-0.5 transition"></i>
            </a>
        </div>

        <!-- 7. Keterangan Lainnya: Waktu, Tempat, Hadiah -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 max-w-3xl mx-auto pt-2 text-left">
            <div class="bg-white/5 backdrop-blur-md rounded-xl p-3.5 border border-white/10 flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-copper-500/20 flex items-center justify-center text-copper-400 shrink-0">
                    <i data-lucide="calendar" class="w-5 h-5"></i>
                </div>
                <div>
                    <p class="text-[10px] text-gray-400 uppercase tracking-wider font-semibold">Waktu Pelaksanaan</p>
                    <p class="text-xs sm:text-sm font-bold text-white">17 — 18 Oktober 2026</p>
                </div>
            </div>

            <div class="bg-white/5 backdrop-blur-md rounded-xl p-3.5 border border-white/10 flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-copper-500/20 flex items-center justify-center text-copper-400 shrink-0">
                    <i data-lucide="map-pin" class="w-5 h-5"></i>
                </div>
                <div>
                    <p class="text-[10px] text-gray-400 uppercase tracking-wider font-semibold">Tempat</p>
                    <p class="text-xs sm:text-sm font-bold text-white">Lap. Tembak Shooting House Bogor</p>
                </div>
            </div>

            <div class="bg-white/5 backdrop-blur-md rounded-xl p-3.5 border border-copper-400/30 flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-copper-500/20 flex items-center justify-center text-copper-400 shrink-0">
                    <i data-lucide="trophy" class="w-5 h-5"></i>
                </div>
                <div>
                    <p class="text-[10px] text-copper-300 uppercase tracking-wider font-semibold">Total Hadiah</p>
                    <p class="text-xs sm:text-sm font-bold text-white">Jutaan Rupiah + Trophy</p>
                </div>
            </div>
        </div>

    </div>

    <!-- Decorative bottom divider -->
    <div class="h-4 bg-gradient-to-b from-transparent to-white"></div>
</section>

<!-- Countdown Section (Tight Padding with Ambient Radar Sweep) -->
<section class="py-6 md:py-8 bg-white border-b border-gray-100 relative overflow-hidden" x-data="countdown()" x-init="start()">
    <!-- Ambient Radar Sweep -->
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-80 h-80 sm:w-96 sm:h-96 pointer-events-none opacity-10">
        <div class="w-full h-full rounded-full border border-copper-500/40 animate-radar-sweep bg-gradient-to-tr from-copper-500/15 via-transparent to-transparent"></div>
    </div>

    <div class="relative max-w-4xl mx-auto px-4 text-center">
        <div class="flex items-center justify-center gap-2 mb-2">
            <i data-lucide="timer" class="w-4 h-4 text-copper-600"></i>
            <h2 class="font-display text-xl sm:text-2xl font-bold uppercase tracking-wider text-gray-900">Hitung Mundur Menuju Hari-H</h2>
        </div>
        <p class="text-xs text-gray-500 mb-4">17 — 18 Oktober 2026 &bull; Lapangan Tembak Shooting House Brimob Bogor</p>
        
        <div class="grid grid-cols-4 gap-3 sm:gap-4 max-w-md mx-auto">
            <div class="bg-gray-50 rounded-xl p-3 border border-gray-200 shadow-sm">
                <span class="font-display text-2xl sm:text-4xl font-bold text-copper-600" x-text="days">0</span>
                <p class="text-[10px] text-gray-500 uppercase tracking-wider font-semibold mt-0.5">Hari</p>
            </div>
            <div class="bg-gray-50 rounded-xl p-3 border border-gray-200 shadow-sm">
                <span class="font-display text-2xl sm:text-4xl font-bold text-copper-600" x-text="hours">0</span>
                <p class="text-[10px] text-gray-500 uppercase tracking-wider font-semibold mt-0.5">Jam</p>
            </div>
            <div class="bg-gray-50 rounded-xl p-3 border border-gray-200 shadow-sm">
                <span class="font-display text-2xl sm:text-4xl font-bold text-copper-600" x-text="minutes">0</span>
                <p class="text-[10px] text-gray-500 uppercase tracking-wider font-semibold mt-0.5">Menit</p>
            </div>
            <div class="bg-gray-50 rounded-xl p-3 border border-gray-200 shadow-sm">
                <span class="font-display text-2xl sm:text-4xl font-bold text-copper-600" x-text="seconds">0</span>
                <p class="text-[10px] text-gray-500 uppercase tracking-wider font-semibold mt-0.5">Detik</p>
            </div>
        </div>
    </div>
</section>

<!-- About Section (Tight Padding) -->
<section id="tentang" class="py-8 md:py-10 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-6">
            <!-- Centered Dual Logos with X -->
            <div class="flex items-center justify-center gap-2.5 mb-3">
                <img src="/assets/logo-bda.png" alt="Logo BDA 750" class="h-10 w-10 object-contain drop-shadow-sm">
                <span class="font-display font-bold text-xs sm:text-sm text-copper-600 select-none">X</span>
                <img src="/assets/logo-championship.png" alt="Logo BSC 2026" class="h-10 w-10 object-contain drop-shadow-sm">
            </div>
            <span class="text-xs font-bold uppercase tracking-widest text-copper-600">Tentang Kejuaraan</span>
            <h2 class="font-display text-2xl sm:text-3xl font-bold mt-1 text-gray-900">Kejuaraan Menembak BDA 750</h2>
            <p class="text-xs sm:text-sm text-gray-600 max-w-2xl mx-auto mt-2 leading-relaxed">
                Diselenggarakan oleh Brigade Diraya Adikara (BDA) 750 dalam rangka mempererat silaturahmi, sportivitas, dan mengasah ketangkasan menembak bagi anggota Brimob Resimen I Pasukan Pelopor dan Letting BDA 750 se-Korbrimob Polri.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 sm:gap-6">
            <div class="bg-white rounded-2xl p-6 border border-gray-200 shadow-sm hover:border-copper-400 hover:shadow-md transition text-center flex flex-col items-center">
                <div class="w-12 h-12 rounded-xl bg-copper-100 flex items-center justify-center mb-3 text-copper-600 mx-auto">
                    <i data-lucide="crosshair" class="w-6 h-6"></i>
                </div>
                <h3 class="font-display text-lg font-bold mb-1.5 text-gray-900 text-center">2 Kategori Lomba</h3>
                <p class="text-xs text-gray-600 leading-relaxed text-center">
                    Pistol Presisi 20 Meter lesan ring dan Dueling Plat Speed sistem gugur head-to-head.
                </p>
            </div>
            
            <div class="bg-white rounded-2xl p-6 border border-gray-200 shadow-sm hover:border-copper-400 hover:shadow-md transition text-center flex flex-col items-center">
                <div class="w-12 h-12 rounded-xl bg-copper-100 flex items-center justify-center mb-3 text-copper-600 mx-auto">
                    <i data-lucide="trophy" class="w-6 h-6"></i>
                </div>
                <h3 class="font-display text-lg font-bold mb-1.5 text-gray-900 text-center">Hadiah Pembinaan</h3>
                <p class="text-xs text-gray-600 leading-relaxed text-center">
                    Uang pembinaan Juara I, II, dan III per kategori + Trophy + Sertifikat penghargaan resmi.
                </p>
            </div>

            <div class="bg-white rounded-2xl p-6 border border-gray-200 shadow-sm hover:border-copper-400 hover:shadow-md transition text-center flex flex-col items-center">
                <div class="w-12 h-12 rounded-xl bg-copper-100 flex items-center justify-center mb-3 text-copper-600 mx-auto">
                    <i data-lucide="shield-check" class="w-6 h-6"></i>
                </div>
                <h3 class="font-display text-lg font-bold mb-1.5 text-gray-900 text-center">Peserta Kejuaraan</h3>
                <p class="text-xs text-gray-600 leading-relaxed text-center">
                    Terbuka untuk anggota Brimob Resimen I Pasukan Pelopor dan Letting BDA 750 se-Korbrimob Polri.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Categories Section (Tight Padding) -->
<section id="kategori" class="py-8 md:py-10 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-6">
            <span class="text-xs font-bold uppercase tracking-widest text-copper-600">Detail Pertandingan</span>
            <h2 class="font-display text-2xl sm:text-3xl font-bold mt-1 text-gray-900">Kategori Lomba</h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 max-w-5xl mx-auto">
            <!-- Presisi 20M -->
            <div class="group relative border border-gray-200 rounded-2xl p-6 hover:border-copper-400 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 bg-white">
                <div class="flex items-center gap-3.5 mb-5 pb-4 border-b border-gray-100">
                    <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center group-hover:scale-105 group-hover:bg-blue-600 group-hover:text-white transition-all duration-300">
                        <i data-lucide="crosshair" class="w-6 h-6 group-hover:rotate-45 transition-transform duration-300"></i>
                    </div>
                    <div>
                        <h3 class="font-display text-xl font-bold text-gray-900 group-hover:text-copper-600 transition-colors">Pistol Presisi 20M</h3>
                        <p class="text-[11px] text-gray-500 font-medium">Kelas Individu (Lesan Ring Target)</p>
                    </div>
                </div>

                <div class="space-y-2 text-xs">
                    <div class="flex items-center justify-between py-2 px-3 rounded-xl bg-gray-50 border border-gray-100/80">
                        <span class="text-gray-500 font-medium">Jarak</span>
                        <span class="font-bold text-gray-900">20 meter</span>
                    </div>
                    <div class="flex items-center justify-between py-2 px-3 rounded-xl bg-gray-50 border border-gray-100/80">
                        <span class="text-gray-500 font-medium">Sikap</span>
                        <span class="font-bold text-gray-900">Berdiri, 2 tangan</span>
                    </div>
                    <div class="flex items-center justify-between py-2 px-3 rounded-xl bg-gray-50 border border-gray-100/80">
                        <span class="text-gray-500 font-medium">Sasaran</span>
                        <span class="font-bold text-gray-900">Lesan Ring (1 - 10 + Inner X)</span>
                    </div>
                    <div class="flex items-center justify-between py-2 px-3 rounded-xl bg-gray-50 border border-gray-100/80">
                        <span class="text-gray-500 font-medium">Amunisi</span>
                        <span class="font-bold text-gray-900">13 butir (3 coba + 10 nilai)</span>
                    </div>
                    <div class="flex items-center justify-between py-2 px-3 rounded-xl bg-gray-50 border border-gray-100/80">
                        <span class="text-gray-500 font-medium">Batas Waktu</span>
                        <span class="font-bold text-gray-900">3 Menit</span>
                    </div>
                    <div class="flex items-center justify-between py-2.5 px-3 rounded-xl bg-copper-50 border border-copper-200/80">
                        <span class="text-copper-800 font-bold">Biaya</span>
                        <span class="font-extrabold text-sm text-copper-600">Rp 200.000</span>
                    </div>
                </div>
            </div>
            
            <!-- Dueling Plat -->
            <div class="group relative border border-gray-200 rounded-2xl p-6 hover:border-copper-400 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 bg-white">
                <div class="flex items-center gap-3.5 mb-5 pb-4 border-b border-gray-100">
                    <div class="w-12 h-12 rounded-xl bg-red-50 text-red-600 flex items-center justify-center group-hover:scale-105 group-hover:bg-red-600 group-hover:text-white transition-all duration-300">
                        <i data-lucide="swords" class="w-6 h-6 group-hover:rotate-12 transition-transform duration-300"></i>
                    </div>
                    <div>
                        <h3 class="font-display text-xl font-bold text-gray-900 group-hover:text-copper-600 transition-colors">Dueling Plat Speed</h3>
                        <p class="text-[11px] text-gray-500 font-medium">Kelas Individu (Eliminasi Head-to-Head)</p>
                    </div>
                </div>

                <div class="space-y-2 text-xs">
                    <div class="flex items-center justify-between py-2 px-3 rounded-xl bg-gray-50 border border-gray-100/80">
                        <span class="text-gray-500 font-medium">Jarak</span>
                        <span class="font-bold text-gray-900">15 meter</span>
                    </div>
                    <div class="flex items-center justify-between py-2 px-3 rounded-xl bg-gray-50 border border-gray-100/80">
                        <span class="text-gray-500 font-medium">Sikap</span>
                        <span class="font-bold text-gray-900">Berdiri (lari 10m ke meja)</span>
                    </div>
                    <div class="flex items-center justify-between py-2 px-3 rounded-xl bg-gray-50 border border-gray-100/80">
                        <span class="text-gray-500 font-medium">Sasaran</span>
                        <span class="font-bold text-gray-900">5 plat bulat + 1 stop popper</span>
                    </div>
                    <div class="flex items-center justify-between py-2 px-3 rounded-xl bg-gray-50 border border-gray-100/80">
                        <span class="text-gray-500 font-medium">Amunisi</span>
                        <span class="font-bold text-gray-900">10 butir per putaran match</span>
                    </div>
                    <div class="flex items-center justify-between py-2 px-3 rounded-xl bg-gray-50 border border-gray-100/80">
                        <span class="text-gray-500 font-medium">Sistem</span>
                        <span class="font-bold text-gray-900">Waktu Tercepat (Eliminasi)</span>
                    </div>
                    <div class="flex items-center justify-between py-2.5 px-3 rounded-xl bg-copper-50 border border-copper-200/80">
                        <span class="text-copper-800 font-bold">Biaya</span>
                        <span class="font-extrabold text-sm text-copper-600">Rp 200.000</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Interactive Target Simulator Widget (Simulasi Tembak Lesan 20M) -->
<section id="simulasi" class="py-8 md:py-10 bg-gray-50 border-t border-gray-200" x-data="targetSimulator()">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-copper-100 text-copper-700 text-xs font-bold uppercase tracking-wider mb-2">
            <i data-lucide="target" class="w-3.5 h-3.5"></i>
            Simulasi Tembak Interaktif
        </div>
        <h2 class="font-display text-2xl sm:text-3xl font-bold text-gray-900">Uji Akurasi Lesan 20 Meter</h2>
        <p class="text-xs sm:text-sm text-gray-600 max-w-xl mx-auto mt-1 mb-6">
            Klik pada lesan sasaran di bawah untuk mensimulasikan perolehan nilai tembakan kelas Pistol Presisi 20M (Nilai 1 hingga 10 dan Inner X).
        </p>

        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-gray-200 shadow-xl max-w-xl mx-auto">
            <!-- Target Canvas Area -->
            <div class="relative w-64 h-64 sm:w-72 sm:h-72 mx-auto select-none cursor-crosshair group rounded-full overflow-hidden shadow-2xl border-4 border-gray-300 bg-white" @click="shoot($event)">
                <!-- SVG Target Rings (1-10 + Inner X) -->
                <svg viewBox="0 0 300 300" class="w-full h-full">
                    <!-- Outer White Rings (1 - 6) -->
                    <circle cx="150" cy="150" r="148" fill="#fafafa" stroke="#333" stroke-width="1.5" />
                    <circle cx="150" cy="150" r="130" fill="#fafafa" stroke="#555" stroke-width="1" />
                    <circle cx="150" cy="150" r="112" fill="#fafafa" stroke="#555" stroke-width="1" />
                    <circle cx="150" cy="150" r="94" fill="#fafafa" stroke="#555" stroke-width="1" />
                    <circle cx="150" cy="150" r="76" fill="#fafafa" stroke="#555" stroke-width="1" />

                    <!-- Inner Black Rings (7 - 10) -->
                    <circle cx="150" cy="150" r="58" fill="#18181b" stroke="#777" stroke-width="1" />
                    <circle cx="150" cy="150" r="42" fill="#18181b" stroke="#e4e4e7" stroke-width="0.8" />
                    <circle cx="150" cy="150" r="28" fill="#18181b" stroke="#e4e4e7" stroke-width="0.8" />
                    <circle cx="150" cy="150" r="14" fill="#18181b" stroke="#e4e4e7" stroke-width="0.8" />

                    <!-- Inner X Center Cross -->
                    <line x1="145" y1="150" x2="155" y2="150" stroke="#f59e0b" stroke-width="1.5" />
                    <line x1="150" y1="145" x2="150" y2="155" stroke="#f59e0b" stroke-width="1.5" />

                    <!-- Ring Numbers -->
                    <text x="150" y="24" font-size="9" fill="#71717a" text-anchor="middle" font-family="monospace">1</text>
                    <text x="150" y="44" font-size="9" fill="#71717a" text-anchor="middle" font-family="monospace">2</text>
                    <text x="150" y="62" font-size="9" fill="#71717a" text-anchor="middle" font-family="monospace">3</text>
                    <text x="150" y="80" font-size="9" fill="#71717a" text-anchor="middle" font-family="monospace">4</text>
                    <text x="150" y="98" font-size="8" fill="#71717a" text-anchor="middle" font-family="monospace">5</text>
                    <text x="150" y="116" font-size="8" fill="#a1a1aa" text-anchor="middle" font-family="monospace">6</text>
                    <text x="150" y="132" font-size="8" fill="#d4d4d8" text-anchor="middle" font-family="monospace">7</text>
                    <text x="150" y="144" font-size="7" fill="#e4e4e7" text-anchor="middle" font-family="monospace">8</text>
                </svg>

                <!-- Bullet Holes Decals on Target -->
                <template x-for="(hit, i) in hits" :key="i">
                    <div class="absolute w-3 h-3 -ml-1.5 -mt-1.5 rounded-full bg-zinc-950 border-2 border-amber-400 shadow-lg pointer-events-none flex items-center justify-center"
                         :style="'left: ' + hit.x + '%; top: ' + hit.y + '%;'">
                        <span class="w-1 h-1 rounded-full bg-amber-300"></span>
                    </div>
                </template>
            </div>

            <!-- Score Dashboard -->
            <div class="mt-6 pt-4 border-t border-gray-100 flex flex-wrap items-center justify-between gap-4">
                <div class="text-left">
                    <p class="text-xs text-gray-500">Total Tembakan: <span class="font-bold text-gray-900" x-text="hits.length + ' / 10'"></span></p>
                    <p class="text-xs text-gray-500 mt-0.5">Inner X: <span class="font-bold text-amber-500" x-text="innerXCount"></span></p>
                </div>

                <div class="flex items-center gap-4">
                    <div class="text-right">
                        <span class="text-[10px] text-gray-400 uppercase tracking-wider font-semibold block">Total Skor</span>
                        <span class="font-display text-3xl font-extrabold text-copper-600" x-text="totalScore">0</span>
                    </div>

                    <button @click="resetTarget()" type="button" class="px-3.5 py-2 rounded-xl bg-gray-100 hover:bg-gray-200 text-xs font-semibold text-gray-700 transition flex items-center gap-1.5 shadow-sm">
                        <i data-lucide="rotate-ccw" class="w-3.5 h-3.5"></i>
                        <span>Reset</span>
                    </button>
                </div>
            </div>

            <template x-if="lastScoreText">
                <div class="mt-3 text-xs font-bold text-copper-600" x-text="lastScoreText"></div>
            </template>
        </div>
    </div>
</section>

<!-- Prizes Section (Updated: Juara 3 = Rp 1.000.000) -->
<section id="hadiah" class="py-8 md:py-10 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-6">
            <span class="text-xs font-bold uppercase tracking-widest text-copper-600">Penghargaan</span>
            <h2 class="font-display text-2xl sm:text-3xl font-bold mt-1 text-gray-900">Hadiah Pemenang</h2>
            <p class="text-xs text-gray-500 mt-0.5">Diberikan untuk masing-masing kategori lomba</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-5 max-w-4xl mx-auto items-end pt-3">
            <!-- Juara 2 -->
            <div class="order-2 md:order-1 text-center bg-white rounded-2xl p-6 border border-gray-200 shadow-sm hover:shadow-md transition">
                <div class="w-12 h-12 mx-auto mb-3 rounded-full bg-gray-100 flex items-center justify-center font-display text-xl font-bold text-gray-700">
                    2
                </div>
                <h3 class="font-display text-lg font-bold text-gray-900 mb-1">Juara II</h3>
                <p class="font-display text-2xl font-bold text-copper-600 mb-1">Rp 2.000.000</p>
                <p class="text-xs text-gray-500">+ Trophy + Piagam Resmi</p>
            </div>
            
            <!-- Juara 1 (Featured with Radiant Metallic Shimmer & Visible Floating Badge) -->
            <div class="order-1 md:order-2 text-center bg-gradient-to-b from-copper-50 via-white to-copper-50/40 rounded-2xl p-7 border-2 border-copper-500 shadow-xl relative group">
                <!-- Inner overflow-hidden container for shimmer beam -->
                <div class="absolute inset-0 rounded-2xl overflow-hidden pointer-events-none">
                    <div class="absolute -inset-full w-[300%] h-[300%] animate-gold-shimmer bg-gradient-to-r from-transparent via-amber-400/25 to-transparent"></div>
                </div>

                <!-- Floating Badge Utama - fully visible outside overflow -->
                <span class="absolute -top-3.5 left-1/2 -translate-x-1/2 px-4 py-1 bg-copper-600 text-white text-[11px] font-bold uppercase tracking-widest rounded-full shadow-md z-20 flex items-center gap-1.5 border border-amber-300/40">
                    <i data-lucide="sparkles" class="w-3 h-3 text-amber-300"></i>
                    <span>Utama</span>
                </span>

                <div class="w-14 h-14 mx-auto mb-3 rounded-full bg-copper-100 flex items-center justify-center text-copper-600 relative z-10 group-hover:scale-110 transition-transform">
                    <i data-lucide="crown" class="w-7 h-7"></i>
                </div>
                <h3 class="font-display text-xl font-bold text-copper-700 mb-1 relative z-10">Juara I</h3>
                <p class="font-display text-3xl font-extrabold text-copper-600 mb-1 relative z-10">Rp 3.000.000</p>
                <p class="text-xs text-gray-600 font-semibold relative z-10">+ Trophy Bergilir + Piagam Resmi</p>
            </div>
            
            <!-- Juara 3 (UPDATED TO 1 JUTA) -->
            <div class="order-3 text-center bg-white rounded-2xl p-6 border border-gray-200 shadow-sm hover:shadow-md transition">
                <div class="w-12 h-12 mx-auto mb-3 rounded-full bg-amber-100 flex items-center justify-center font-display text-xl font-bold text-amber-700">
                    3
                </div>
                <h3 class="font-display text-lg font-bold text-gray-900 mb-1">Juara III</h3>
                <p class="font-display text-2xl font-bold text-copper-600 mb-1">Rp 1.000.000</p>
                <p class="text-xs text-gray-500">+ Trophy + Piagam Resmi</p>
            </div>
        </div>
    </div>
</section>

<!-- Schedule Section (Tight Padding) -->
<section id="jadwal" class="py-8 md:py-10 bg-white">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-6">
            <span class="text-xs font-bold uppercase tracking-widest text-copper-600">Rundown Acara</span>
            <h2 class="font-display text-2xl sm:text-3xl font-bold mt-1 text-gray-900">Jadwal Pertandingan</h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Day 1 -->
            <div class="p-5 bg-gray-50 rounded-xl border border-gray-200">
                <div class="flex items-center gap-2 mb-3">
                    <span class="px-2.5 py-1 bg-copper-600 text-white rounded font-display font-bold text-xs">Hari ke-1</span>
                    <h3 class="font-display font-bold text-base text-gray-900">Jumat, 17 Oktober 2026</h3>
                </div>
                <ul class="space-y-1.5 text-xs text-gray-600">
                    <li class="flex justify-between pb-1 border-b border-gray-200"><span>07:00 WIB</span><span class="font-medium text-gray-800">Daftar Ulang & Pengecekan Senjata</span></li>
                    <li class="flex justify-between pb-1 border-b border-gray-200"><span>08:00 WIB</span><span class="font-medium text-gray-800">Upacara Pembukaan Resmi</span></li>
                    <li class="flex justify-between pb-1 border-b border-gray-200"><span>09:00 WIB</span><span class="font-medium text-gray-800">Babak Penyisihan Presisi 20M</span></li>
                    <li class="flex justify-between pb-1 border-b border-gray-200"><span>13:30 WIB</span><span class="font-medium text-gray-800">Babak Penyisihan Dueling Plat</span></li>
                    <li class="flex justify-between"><span>17:00 WIB</span><span class="font-medium text-gray-800">Penutupan Hari Pertama</span></li>
                </ul>
            </div>

            <!-- Day 2 -->
            <div class="p-5 bg-gray-50 rounded-xl border border-gray-200">
                <div class="flex items-center gap-2 mb-3">
                    <span class="px-2.5 py-1 bg-copper-600 text-white rounded font-display font-bold text-xs">Hari ke-2</span>
                    <h3 class="font-display font-bold text-base text-gray-900">Sabtu, 18 Oktober 2026</h3>
                </div>
                <ul class="space-y-1.5 text-xs text-gray-600">
                    <li class="flex justify-between pb-1 border-b border-gray-200"><span>08:00 WIB</span><span class="font-medium text-gray-800">Babak Final Pistol Presisi 20M</span></li>
                    <li class="flex justify-between pb-1 border-b border-gray-200"><span>10:00 WIB</span><span class="font-medium text-gray-800">Semifinal & Final Dueling Plat</span></li>
                    <li class="flex justify-between pb-1 border-b border-gray-200"><span>14:00 WIB</span><span class="font-medium text-gray-800">Rekapitulasi Poin & Verifikasi Wasit</span></li>
                    <li class="flex justify-between pb-1 border-b border-gray-200"><span>15:30 WIB</span><span class="font-medium text-gray-800">Upacara Penutupan & Penyerahan Hadiah</span></li>
                    <li class="flex justify-between"><span>17:00 WIB</span><span class="font-medium text-gray-800">Selesai & Ramah Tamah</span></li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- Rules Section (Accordion, Tight Padding) -->
<section id="juknis" class="py-8 md:py-10 bg-gray-50">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-6">
            <span class="text-xs font-bold uppercase tracking-widest text-copper-600">Petunjuk Teknis</span>
            <h2 class="font-display text-2xl sm:text-3xl font-bold mt-1 text-gray-900">Peraturan & Persyaratan</h2>
        </div>

        <div class="space-y-2.5" x-data="{ open: 1 }">
            <!-- Rule 1 -->
            <div class="border border-gray-200 rounded-xl overflow-hidden bg-white">
                <button @click="open = open === 1 ? null : 1" class="w-full flex items-center justify-between p-3.5 text-left font-semibold hover:bg-gray-50 transition text-sm">
                    <span class="flex items-center gap-2"><i data-lucide="user-check" class="w-4 h-4 text-copper-500"></i> Persyaratan Peserta</span>
                    <i data-lucide="chevron-down" class="w-4 h-4 transition-transform text-gray-500" :class="open === 1 ? 'rotate-180' : ''"></i>
                </button>
                <div x-show="open === 1" x-transition class="px-4 pb-3.5 text-xs text-gray-600 space-y-1 border-t border-gray-100 pt-2.5">
                    <p>&bull; Terbuka untuk anggota Brimob Resimen I Pasukan Pelopor dan Letting BDA 750 se-Korbrimob Polri.</p>
                    <p>&bull; Memiliki KTA (Kartu Tanda Anggota) Polri yang sah dan diunggah saat pendaftaran.</p>
                    <p>&bull; Melakukan pendaftaran resmi secara online dan menyelesaikan biaya pendaftaran.</p>
                </div>
            </div>
            
            <!-- Rule 2 -->
            <div class="border border-gray-200 rounded-xl overflow-hidden bg-white">
                <button @click="open = open === 2 ? null : 2" class="w-full flex items-center justify-between p-3.5 text-left font-semibold hover:bg-gray-50 transition text-sm">
                    <span class="flex items-center gap-2"><i data-lucide="crosshair" class="w-4 h-4 text-copper-500"></i> Ketentuan Senjata & Amunisi</span>
                    <i data-lucide="chevron-down" class="w-4 h-4 transition-transform text-gray-500" :class="open === 2 ? 'rotate-180' : ''"></i>
                </button>
                <div x-show="open === 2" x-transition class="px-4 pb-3.5 text-xs text-gray-600 space-y-1 border-t border-gray-100 pt-2.5">
                    <p>&bull; Pistol kaliber 9mm organik/standar laik tembak.</p>
                    <p>&bull; Pemeriksaan senjata (gun check) akan dilakukan panitia sebelum pertandingan dimulai.</p>
                    <p>&bull; Wajib menggunakan safety glasses dan pelindung telinga (earmuff/earplug).</p>
                </div>
            </div>

            <!-- Rule 3 -->
            <div class="border border-gray-200 rounded-xl overflow-hidden bg-white">
                <button @click="open = open === 3 ? null : 3" class="w-full flex items-center justify-between p-3.5 text-left font-semibold hover:bg-gray-50 transition text-sm">
                    <span class="flex items-center gap-2"><i data-lucide="calculator" class="w-4 h-4 text-copper-500"></i> Sistem Penilaian & Ranking</span>
                    <i data-lucide="chevron-down" class="w-4 h-4 transition-transform text-gray-500" :class="open === 3 ? 'rotate-180' : ''"></i>
                </button>
                <div x-show="open === 3" x-transition class="px-4 pb-3.5 text-xs text-gray-600 space-y-1.5 border-t border-gray-100 pt-2.5">
                    <p><strong>Presisi 20M:</strong> Total akumulasi poin dari 10 tembakan seri. Jika terjadi draw/skor sama, penentu pemenang dihitung dari jumlah tembakan X (inner-10) terbanyak.</p>
                    <p><strong>Dueling Plat:</strong> Sistem gugur langsung head-to-head. Pemenang adalah penembak yang menjatuhkan 5 plat + 1 popper dengan waktu tercepat.</p>
                </div>
            </div>

            <!-- Rule 4 -->
            <div class="border border-gray-200 rounded-xl overflow-hidden bg-white">
                <button @click="open = open === 4 ? null : 4" class="w-full flex items-center justify-between p-3.5 text-left font-semibold hover:bg-gray-50 transition text-sm">
                    <span class="flex items-center gap-2"><i data-lucide="credit-card" class="w-4 h-4 text-copper-500"></i> Biaya & Pembayaran</span>
                    <i data-lucide="chevron-down" class="w-4 h-4 transition-transform text-gray-500" :class="open === 4 ? 'rotate-180' : ''"></i>
                </button>
                <div x-show="open === 4" x-transition class="px-4 pb-3.5 text-xs text-gray-600 space-y-1 border-t border-gray-100 pt-2.5">
                    <p>&bull; Biaya: <strong>Rp 200.000 per kategori</strong> (Mengikuti 2 kategori: Rp 400.000).</p>
                    <p>&bull; Transfer ke rekening: <strong>Bank BRI 053801071906503</strong> a.n. <strong>Ahyandi Hi Karim</strong>.</p>
                    <p>&bull; Lampirkan bukti transfer saat pengisian formulir pendaftaran.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Contact & Payment Section (Tight Padding) -->
<section id="kontak" class="py-8 md:py-10 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-6">
            <span class="text-xs font-bold uppercase tracking-widest text-copper-600">Informasi & Bantuan</span>
            <h2 class="font-display text-2xl sm:text-3xl font-bold mt-1 text-gray-900">Kontak Panitia Pelaksana</h2>
        </div>
        
        <!-- Payment Info Box -->
        <div class="max-w-md mx-auto mb-6 p-4 bg-copper-50 border border-copper-200 rounded-xl text-center" x-data="{ copied: false }">
            <p class="text-xs uppercase font-bold text-copper-700">Rekening Resmi Pendaftaran</p>
            <p class="font-display text-lg font-bold text-gray-900 mt-1">Bank BRI</p>
            <div class="mt-2 flex items-center justify-center gap-2">
                <span class="font-mono text-base font-bold text-copper-700">053801071906503</span>
                <button type="button" @click="navigator.clipboard.writeText('053801071906503'); copied = true; setTimeout(() => copied = false, 2000)" 
                        class="px-2.5 py-1 bg-white rounded-md border border-copper-300 hover:bg-copper-100 text-xs font-semibold transition">
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
               class="p-3.5 bg-gray-50 rounded-xl hover:bg-green-50 border border-gray-200 hover:border-green-400 transition group text-left">
                <p class="text-[10px] text-gray-500 uppercase tracking-wider mb-0.5"><?= $c['role'] ?></p>
                <p class="font-bold text-xs text-gray-900"><?= $c['name'] ?></p>
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

<style>
@keyframes goldShimmer {
    0% { transform: translateX(-120%) rotate(25deg); }
    30%, 100% { transform: translateX(180%) rotate(25deg); }
}
.animate-gold-shimmer {
    animation: goldShimmer 5s cubic-bezier(0.4, 0, 0.2, 1) infinite;
}
@keyframes radarSweep {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
.animate-radar-sweep {
    animation: radarSweep 14s linear infinite;
}
@media (prefers-reduced-motion: reduce) {
    *, ::before, ::after {
        animation-duration: 0.01ms !important;
        animation-iteration-count: 1 !important;
        transition-duration: 0.01ms !important;
    }
}
</style>

<script>
function heroLogoTilt() {
    return {
        tiltX: 0,
        tiltY: 0,
        isHovered: false,
        handleMouseMove(e) {
            const rect = e.currentTarget.getBoundingClientRect();
            const x = e.clientX - rect.left - rect.width / 2;
            const y = e.clientY - rect.top - rect.height / 2;
            this.tiltX = (-y / (rect.height / 2) * 7).toFixed(2);
            this.tiltY = (x / (rect.width / 2) * 7).toFixed(2);
        },
        handleMouseLeave() {
            this.tiltX = 0;
            this.tiltY = 0;
            this.isHovered = false;
        }
    }
}

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

function targetSimulator() {
    return {
        hits: [],
        totalScore: 0,
        innerXCount: 0,
        lastScoreText: '',
        shoot(e) {
            if (this.hits.length >= 10) {
                this.lastScoreText = 'Seri 10 tembakan selesai! Total Skor: ' + this.totalScore + ' (' + this.innerXCount + 'x X). Klik Reset untuk mengulang.';
                return;
            }
            const rect = e.currentTarget.getBoundingClientRect();
            const clickX = e.clientX - rect.left;
            const clickY = e.clientY - rect.top;

            const centerX = rect.width / 2;
            const centerY = rect.height / 2;
            const dx = clickX - centerX;
            const dy = clickY - centerY;
            const dist = Math.sqrt(dx * dx + dy * dy);
            const normDist = (dist / (rect.width / 2)) * 150;

            const pctX = ((clickX / rect.width) * 100).toFixed(1);
            const pctY = ((clickY / rect.height) * 100).toFixed(1);

            let score = 0;
            let label = '';

            if (normDist <= 8) {
                score = 10;
                this.innerXCount++;
                label = 'Bullseye! Inner X (10 Poin)';
            } else if (normDist <= 14) {
                score = 10;
                label = 'Sempurna! Nilai 10';
            } else if (normDist <= 28) {
                score = 9;
                label = 'Hebat! Nilai 9';
            } else if (normDist <= 42) {
                score = 8;
                label = 'Bagus! Nilai 8';
            } else if (normDist <= 58) {
                score = 7;
                label = 'Ring Hitam! Nilai 7';
            } else if (normDist <= 76) {
                score = 6;
                label = 'Nilai 6';
            } else if (normDist <= 94) {
                score = 5;
                label = 'Nilai 5';
            } else if (normDist <= 112) {
                score = 4;
                label = 'Nilai 4';
            } else if (normDist <= 130) {
                score = 3;
                label = 'Nilai 3';
            } else if (normDist <= 140) {
                score = 2;
                label = 'Nilai 2';
            } else if (normDist <= 148) {
                score = 1;
                label = 'Nilai 1';
            } else {
                score = 0;
                label = 'Keluar Lesan (Miss / 0 Poin)';
            }

            this.hits.push({ x: pctX, y: pctY, score });
            this.totalScore += score;
            this.lastScoreText = 'Tembakan ke-' + this.hits.length + ': ' + label;
        },
        resetTarget() {
            this.hits = [];
            this.totalScore = 0;
            this.innerXCount = 0;
            this.lastScoreText = 'Lesan di-reset. Silakan bidik dan klik kembali!';
        }
    }
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
