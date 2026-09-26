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
        <!-- 1. Organizer Badge with White Canvas for BDA Logo -->
        <div class="inline-flex items-center gap-2.5 pl-1.5 pr-4 py-1.5 rounded-full bg-white/15 backdrop-blur-md border border-white/25 text-xs font-semibold text-white mb-3 shadow-lg">
            <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-full bg-white p-1 shadow-md border border-white/80 flex items-center justify-center shrink-0">
                <picture>
                    <source srcset="/assets/logo-bda-sm.webp" type="image/webp">
                    <img src="/assets/logo-bda.png" alt="Logo BDA" class="w-full h-full object-contain" width="36" height="36" loading="eager" decoding="async" onerror="this.style.display='none'">
                </picture>
            </div>
            <span class="tracking-wide uppercase text-[11px] font-bold text-copper-200">Brigade Diraya Adikara (BDA) 750</span>
        </div>

        <!-- 2. Main Headline -->
        <h1 class="font-display text-4xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight leading-[1.08] text-white mb-3">
            <span data-scramble>BDA SHOOTING</span><br>
            <span data-scramble class="text-transparent bg-clip-text bg-gradient-to-r from-copper-400 via-amber-300 to-copper-500">CHAMPIONSHIP 2026</span>
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
                    <svg class="w-full h-full text-copper-400/35 animate-[spin_50s_linear_infinite]" style="will-change: transform; transform: translateZ(0);" viewBox="0 0 200 200" fill="none">
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
                    <svg class="w-full h-full text-amber-400/30 animate-[spin_30s_linear_infinite_reverse]" style="will-change: transform; transform: translateZ(0);" viewBox="0 0 200 200" fill="none">
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
                    <picture>
                        <source srcset="/assets/logo-championship.webp" type="image/webp">
                        <img src="/assets/logo-championship.png" 
                             alt="Logo BDA Shooting Championship 2026" 
                             class="w-full h-full object-contain drop-shadow-xl select-none pointer-events-none"
                             width="384"
                             height="384"
                             fetchpriority="high"
                             loading="eager"
                             decoding="async">
                    </picture>
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
                    <p class="text-xs sm:text-sm font-bold text-white">Lapangan Tembak Resimen I Pasukan Pelopor, Kedunghalang, Bogor</p>
                </div>
            </div>

            <div class="bg-white/5 backdrop-blur-md rounded-xl p-3.5 border border-copper-400/30 flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-copper-500/20 flex items-center justify-center text-copper-400 shrink-0">
                    <i data-lucide="trophy" class="w-5 h-5"></i>
                </div>
                <div>
                    <p class="text-[10px] text-copper-300 uppercase tracking-wider font-semibold">Total Hadiah</p>
                    <p class="text-xs sm:text-sm font-bold text-white">Jutaan Rupiah + Tropi</p>
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
            <h2 class="font-display text-xl sm:text-2xl font-bold uppercase tracking-wider text-gray-900" data-scramble>Hitung Mundur Menuju Hari-H</h2>
        </div>
        <p class="text-xs text-gray-500 mb-4">17 — 18 Oktober 2026 &bull; Lapangan Tembak Resimen I Pasukan Pelopor, Kedunghalang, Bogor</p>
        
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

<!-- About Section (Tight Padding & Lazy Rendering) -->
<section id="tentang" class="section-lazy py-8 md:py-10 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-6">
            <!-- Centered Dual Logos with X -->
            <div class="flex items-center justify-center gap-2.5 mb-3">
                <div class="w-11 h-11 rounded-full bg-white p-1 shadow-sm border border-gray-200 flex items-center justify-center shrink-0">
                    <picture>
                        <source srcset="/assets/logo-bda-sm.webp" type="image/webp">
                        <img src="/assets/logo-bda.png" alt="Logo BDA 750" class="w-full h-full object-contain" width="44" height="44" loading="lazy" decoding="async">
                    </picture>
                </div>
                <span class="font-display font-bold text-xs sm:text-sm text-copper-600 select-none">X</span>
                <div class="w-11 h-11 rounded-full bg-white p-1 shadow-sm border border-gray-200 flex items-center justify-center shrink-0">
                    <picture>
                        <source srcset="/assets/logo-championship-sm.webp" type="image/webp">
                        <img src="/assets/logo-championship.png" alt="Logo BSC 2026" class="w-full h-full object-contain" width="44" height="44" loading="lazy" decoding="async">
                    </picture>
                </div>
            </div>
            <span class="text-xs font-bold uppercase tracking-widest text-copper-600">Tentang Kejuaraan</span>
            <h2 class="font-display text-2xl sm:text-3xl font-bold mt-1 text-gray-900 uppercase tracking-wide" data-scramble>Kejuaraan Menembak BDA 750</h2>
            <p class="text-xs sm:text-sm text-gray-600 max-w-2xl mx-auto mt-2 leading-relaxed">
                Diselenggarakan oleh Brigade Diraya Adikara (BDA) 750 dalam rangka mempererat silaturahmi, sportivitas, dan mengasah ketangkasan menembak bagi anggota POLRI dan Letting BDA 750 se-Korbrimob Polri.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 sm:gap-6">
            <div class="bg-white rounded-2xl p-6 border border-gray-200 shadow-sm hover:border-copper-400 hover:shadow-md transition text-center flex flex-col items-center">
                <div class="w-12 h-12 rounded-xl bg-copper-100 flex items-center justify-center mb-3 text-copper-600 mx-auto">
                    <i data-lucide="crosshair" class="w-6 h-6"></i>
                </div>
                <h3 class="font-display text-lg font-bold mb-1.5 text-gray-900 text-center">3 Kategori / Kelas Lomba</h3>
                <p class="text-xs text-gray-600 leading-relaxed text-center">
                    Pistol Presisi 20M Umum, Dueling Plat 15M Umum POLRI, dan Dueling Plat Khusus BDA Korbrimob.
                </p>
            </div>
            
            <div class="bg-white rounded-2xl p-6 border border-gray-200 shadow-sm hover:border-copper-400 hover:shadow-md transition text-center flex flex-col items-center">
                <div class="w-12 h-12 rounded-xl bg-copper-100 flex items-center justify-center mb-3 text-copper-600 mx-auto">
                    <i data-lucide="trophy" class="w-6 h-6"></i>
                </div>
                <h3 class="font-display text-lg font-bold mb-1.5 text-gray-900 text-center">Hadiah Uang Tunai</h3>
                <p class="text-xs text-gray-600 leading-relaxed text-center">
                    Uang tunai Juara I, II, dan III per kategori + Tropi + Sertifikat penghargaan resmi.
                </p>
            </div>

            <div class="bg-white rounded-2xl p-6 border border-gray-200 shadow-sm hover:border-copper-400 hover:shadow-md transition text-center flex flex-col items-center">
                <div class="w-12 h-12 rounded-xl bg-copper-100 flex items-center justify-center mb-3 text-copper-600 mx-auto">
                    <i data-lucide="shield-check" class="w-6 h-6"></i>
                </div>
                <h3 class="font-display text-lg font-bold mb-1.5 text-gray-900 text-center">Peserta Kejuaraan</h3>
                <p class="text-xs text-gray-600 leading-relaxed text-center">
                    Terbuka untuk anggota POLRI dan Letting BDA 750 se-Korbrimob Polri.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Categories Section (Tight Padding) -->
<section id="kategori" class="section-lazy py-8 md:py-10 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-6">
            <span class="text-xs font-bold uppercase tracking-widest text-copper-600">Detail Pertandingan</span>
            <h2 class="font-display text-2xl sm:text-3xl font-bold mt-1 text-gray-900 uppercase tracking-wide" data-scramble>Kategori Lomba</h2>
            <p class="text-xs sm:text-sm text-gray-600 max-w-xl mx-auto mt-1">2 Cabang Materi Lomba dengan 3 Sub-Kelas Pertandingan Resmi</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 max-w-7xl mx-auto">
            <!-- 1. Presisi 20M (Umum POLRI) -->
            <div class="group relative border border-gray-200 rounded-2xl p-6 hover:border-copper-400 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 bg-white flex flex-col justify-between">
                <div>
                    <div class="flex items-center gap-3.5 mb-5 pb-4 border-b border-gray-100">
                        <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center group-hover:scale-105 group-hover:bg-blue-600 group-hover:text-white transition-all duration-300 shrink-0">
                            <i data-lucide="crosshair" class="w-6 h-6 group-hover:rotate-45 transition-transform duration-300"></i>
                        </div>
                        <div>
                            <span class="inline-block px-2 py-0.5 rounded bg-blue-100 text-blue-800 text-[10px] font-bold uppercase tracking-wider mb-1">Kelas Individu</span>
                            <h3 class="font-display text-lg font-bold text-gray-900 group-hover:text-copper-600 transition-colors">Pistol Presisi 20M</h3>
                            <p class="text-[11px] text-gray-500 font-semibold">Umum (POLRI)</p>
                        </div>
                    </div>

                    <div class="space-y-2 text-xs">
                        <div class="flex items-center justify-between py-2 px-3 rounded-xl bg-gray-50 border border-gray-100/80">
                            <span class="text-gray-500 font-medium">Jarak Tembak</span>
                            <span class="font-bold text-gray-900">20 meter</span>
                        </div>
                        <div class="flex items-center justify-between py-2 px-3 rounded-xl bg-gray-50 border border-gray-100/80">
                            <span class="text-gray-500 font-medium">Sikap Menembak</span>
                            <span class="font-bold text-gray-900">Berdiri, 2 tangan (wajib)</span>
                        </div>
                        <div class="flex items-center justify-between py-2 px-3 rounded-xl bg-gray-50 border border-gray-100/80">
                            <span class="text-gray-500 font-medium">Sasaran Lesan</span>
                            <span class="font-bold text-gray-900">Lesan ISSF (1-10 + X, T: 140cm)</span>
                        </div>
                        <div class="flex items-center justify-between py-2 px-3 rounded-xl bg-gray-50 border border-gray-100/80">
                            <span class="text-gray-500 font-medium">Amunisi</span>
                            <span class="font-bold text-gray-900">13 butir (3 coba + 10 nilai)</span>
                        </div>
                        <div class="flex items-center justify-between py-2 px-3 rounded-xl bg-gray-50 border border-gray-100/80">
                            <span class="text-gray-500 font-medium">Batas Waktu</span>
                            <span class="font-bold text-gray-900">1 mnt coba + 3 mnt nilai</span>
                        </div>
                        <div class="flex items-center justify-between py-2.5 px-3 rounded-xl bg-copper-50 border border-copper-200/80">
                            <span class="text-copper-800 font-bold">Biaya Pendaftaran</span>
                            <span class="font-extrabold text-sm text-copper-600">Rp 200.000</span>
                        </div>
                    </div>
                </div>

                <!-- Tombol Coba Simulasi Lesan & Visir -->
                <a href="/simulasi-lesan.html" class="mt-4 flex items-center justify-center gap-2 py-2.5 px-3 rounded-xl bg-gray-900 hover:bg-copper-600 text-white font-semibold text-xs transition duration-200 shadow-md group/btn">
                    <i data-lucide="crosshair" class="w-4 h-4 text-copper-400 group-hover/btn:rotate-45 transition-transform"></i>
                    <span>Coba Simulasi Lesan 20M &amp; Visir</span>
                    <i data-lucide="arrow-right" class="w-3.5 h-3.5 opacity-70 group-hover/btn:translate-x-0.5 transition-transform"></i>
                </a>
            </div>

            <!-- 2. Dueling Plat (Umum POLRI) -->
            <div class="group relative border border-gray-200 rounded-2xl p-6 hover:border-copper-400 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 bg-white flex flex-col justify-between">
                <div>
                    <div class="flex items-center gap-3.5 mb-5 pb-4 border-b border-gray-100">
                        <div class="w-12 h-12 rounded-xl bg-red-50 text-red-600 flex items-center justify-center group-hover:scale-105 group-hover:bg-red-600 group-hover:text-white transition-all duration-300 shrink-0">
                            <i data-lucide="swords" class="w-6 h-6 group-hover:rotate-12 transition-transform duration-300"></i>
                        </div>
                        <div>
                            <span class="inline-block px-2 py-0.5 rounded bg-red-100 text-red-800 text-[10px] font-bold uppercase tracking-wider mb-1">Kelas Individu</span>
                            <h3 class="font-display text-lg font-bold text-gray-900 group-hover:text-copper-600 transition-colors">Dueling Plat 15M</h3>
                            <p class="text-[11px] text-gray-500 font-semibold">Umum (POLRI)</p>
                        </div>
                    </div>

                    <div class="space-y-2 text-xs">
                        <div class="flex items-center justify-between py-2 px-3 rounded-xl bg-gray-50 border border-gray-100/80">
                            <span class="text-gray-500 font-medium">Jarak Tembak</span>
                            <span class="font-bold text-gray-900">15 meter</span>
                        </div>
                        <div class="flex items-center justify-between py-2 px-3 rounded-xl bg-gray-50 border border-gray-100/80">
                            <span class="text-gray-500 font-medium">Posisi Start</span>
                            <span class="font-bold text-gray-900">Duduk 5m di belakang meja</span>
                        </div>
                        <div class="flex items-center justify-between py-2 px-3 rounded-xl bg-gray-50 border border-gray-100/80">
                            <span class="text-gray-500 font-medium">Sasaran Logam</span>
                            <span class="font-bold text-gray-900">5 plat bulat + 1 stop popper</span>
                        </div>
                        <div class="flex items-center justify-between py-2 px-3 rounded-xl bg-gray-50 border border-gray-100/80">
                            <span class="text-gray-500 font-medium">Amunisi</span>
                            <span class="font-bold text-gray-900">10 butir per putaran duel</span>
                        </div>
                        <div class="flex items-center justify-between py-2 px-3 rounded-xl bg-gray-50 border border-gray-100/80">
                            <span class="text-gray-500 font-medium">Sistem Gugur</span>
                            <span class="font-bold text-gray-900">Head-to-Head (Popper duluan = DQ)</span>
                        </div>
                        <div class="flex items-center justify-between py-2.5 px-3 rounded-xl bg-copper-50 border border-copper-200/80">
                            <span class="text-copper-800 font-bold">Biaya Pendaftaran</span>
                            <span class="font-extrabold text-sm text-copper-600">Rp 200.000</span>
                        </div>
                    </div>
                </div>

                <div class="mt-4 py-2.5 px-3 rounded-xl bg-gray-50 border border-gray-200 text-center text-[11px] text-gray-600 font-medium">
                    Bagan turnamen terbuka untuk seluruh anggota POLRI
                </div>
            </div>

            <!-- 3. Dueling Plat (Khusus BDA Korbrimob) -->
            <div class="group relative border-2 border-copper-300 rounded-2xl p-6 hover:border-copper-500 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 bg-gradient-to-b from-amber-50/40 via-white to-white flex flex-col justify-between">
                <div>
                    <div class="flex items-center gap-3.5 mb-5 pb-4 border-b border-copper-100">
                        <div class="w-12 h-12 rounded-xl bg-amber-100 text-amber-800 flex items-center justify-center group-hover:scale-105 group-hover:bg-amber-600 group-hover:text-white transition-all duration-300 shrink-0">
                            <i data-lucide="shield-check" class="w-6 h-6"></i>
                        </div>
                        <div>
                            <span class="inline-block px-2 py-0.5 rounded bg-amber-500 text-white text-[10px] font-extrabold uppercase tracking-wider mb-1 shadow-sm">Khusus Letting BDA 750</span>
                            <h3 class="font-display text-lg font-bold text-gray-900 group-hover:text-copper-600 transition-colors">Dueling Plat 15M</h3>
                            <p class="text-[11px] text-copper-700 font-bold">BDA Korbrimob POLRI</p>
                        </div>
                    </div>

                    <div class="space-y-2 text-xs">
                        <div class="flex items-center justify-between py-2 px-3 rounded-xl bg-white border border-amber-200/60 shadow-sm">
                            <span class="text-gray-500 font-medium">Jarak Tembak</span>
                            <span class="font-bold text-gray-900">15 meter</span>
                        </div>
                        <div class="flex items-center justify-between py-2 px-3 rounded-xl bg-white border border-amber-200/60 shadow-sm">
                            <span class="text-gray-500 font-medium">Posisi Start</span>
                            <span class="font-bold text-gray-900">Duduk 5m di belakang meja</span>
                        </div>
                        <div class="flex items-center justify-between py-2 px-3 rounded-xl bg-white border border-amber-200/60 shadow-sm">
                            <span class="text-gray-500 font-medium">Sasaran Logam</span>
                            <span class="font-bold text-gray-900">5 plat bulat + 1 stop popper</span>
                        </div>
                        <div class="flex items-center justify-between py-2 px-3 rounded-xl bg-white border border-amber-200/60 shadow-sm">
                            <span class="text-gray-500 font-medium">Amunisi</span>
                            <span class="font-bold text-gray-900">10 butir per putaran duel</span>
                        </div>
                        <div class="flex items-center justify-between py-2 px-3 rounded-xl bg-white border border-amber-200/60 shadow-sm">
                            <span class="text-gray-500 font-medium">Bagan Khusus</span>
                            <span class="font-bold text-copper-700">Turnamen Letting BDA 750</span>
                        </div>
                        <div class="flex items-center justify-between py-2.5 px-3 rounded-xl bg-amber-100/70 border border-amber-300">
                            <span class="text-amber-900 font-bold">Biaya Pendaftaran</span>
                            <span class="font-extrabold text-sm text-copper-700">Rp 100.000</span>
                        </div>
                    </div>
                </div>

                <div class="mt-4 py-2.5 px-3 rounded-xl bg-amber-50 border border-amber-200 text-center text-[11px] text-amber-900 font-semibold">
                    Eksklusif untuk personel BDA se-Korbrimob Polri
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Prizes Section -->
<section id="hadiah" class="section-lazy py-8 md:py-10 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-8">
            <span class="text-xs font-bold uppercase tracking-widest text-copper-600">Penghargaan Resmi</span>
            <h2 class="font-display text-2xl sm:text-3xl font-bold mt-1 text-gray-900 uppercase tracking-wide" data-scramble>Hadiah Pemenang</h2>
            <p class="text-xs text-gray-500 mt-1 max-w-xl mx-auto">Diberikan kepada penembak terbaik berupa Uang Tunai + Tropi + Piagam Resmi</p>
        </div>

        <div class="space-y-10 max-w-6xl mx-auto">
            <!-- 1. HADIAH KELAS UMUM (POLRI) -->
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-gray-200 shadow-sm">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-6 pb-4 border-b border-gray-100">
                    <div>
                        <span class="px-3 py-1 rounded-full bg-copper-100 text-copper-800 text-[11px] font-bold uppercase tracking-wider">Kelas Umum (POLRI)</span>
                        <h3 class="font-display text-xl font-bold text-gray-900 mt-1.5">Pistol Presisi 20M &amp; Dueling Plat 15M (Umum)</h3>
                    </div>
                    <span class="text-xs text-gray-500 font-medium">Berlaku untuk masing-masing cabang lomba umum</span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-5 items-end">
                    <!-- Juara 2 -->
                    <div class="order-2 md:order-1 text-center bg-gray-50 rounded-2xl p-6 border border-gray-200 shadow-sm hover:shadow-md transition">
                        <div class="w-12 h-12 mx-auto mb-3 rounded-full bg-gray-200 flex items-center justify-center font-display text-xl font-bold text-gray-700">
                            2
                        </div>
                        <h4 class="font-display text-base font-bold text-gray-900 mb-1">Juara II</h4>
                        <p class="font-display text-2xl font-extrabold text-copper-600 mb-1">Rp 2.000.000</p>
                        <p class="text-xs text-gray-500">+ Tropi + Piagam Resmi</p>
                    </div>

                    <!-- Juara 1 (Featured) -->
                    <div class="order-1 md:order-2 text-center bg-gradient-to-b from-copper-50 via-white to-copper-50/40 rounded-2xl p-7 border-2 border-copper-500 shadow-xl relative group">
                        <div class="absolute inset-0 rounded-2xl overflow-hidden pointer-events-none">
                            <div class="absolute -inset-full w-[300%] h-[300%] animate-gold-shimmer bg-gradient-to-r from-transparent via-amber-400/25 to-transparent"></div>
                        </div>
                        <span class="absolute -top-3.5 left-1/2 -translate-x-1/2 px-4 py-1 bg-copper-600 text-white text-[11px] font-bold uppercase tracking-widest rounded-full shadow-md z-20 flex items-center gap-1.5 border border-amber-300/40">
                            <i data-lucide="sparkles" class="w-3 h-3 text-amber-300"></i>
                            <span>Juara Utama</span>
                        </span>
                        <div class="w-14 h-14 mx-auto mb-3 rounded-full bg-copper-100 flex items-center justify-center text-copper-600 relative z-10 group-hover:scale-110 transition-transform">
                            <i data-lucide="crown" class="w-7 h-7"></i>
                        </div>
                        <h4 class="font-display text-xl font-bold text-copper-700 mb-1 relative z-10">Juara I</h4>
                        <p class="font-display text-3xl font-extrabold text-copper-600 mb-1 relative z-10">Rp 3.000.000</p>
                        <p class="text-xs text-gray-600 font-semibold relative z-10">+ Tropi + Piagam Resmi</p>
                    </div>

                    <!-- Juara 3 -->
                    <div class="order-3 text-center bg-gray-50 rounded-2xl p-6 border border-gray-200 shadow-sm hover:shadow-md transition">
                        <div class="w-12 h-12 mx-auto mb-3 rounded-full bg-amber-100 flex items-center justify-center font-display text-xl font-bold text-amber-800">
                            3
                        </div>
                        <h4 class="font-display text-base font-bold text-gray-900 mb-1">Juara III</h4>
                        <p class="font-display text-2xl font-extrabold text-copper-600 mb-1">Rp 1.000.000</p>
                        <p class="text-xs text-gray-500">+ Tropi + Piagam Resmi</p>
                    </div>
                </div>
            </div>

            <!-- 2. HADIAH KELAS KHUSUS BDA KORBRIMOB -->
            <div class="bg-gradient-to-br from-amber-50/50 via-white to-orange-50/30 rounded-3xl p-6 sm:p-8 border-2 border-amber-300 shadow-sm">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-6 pb-4 border-b border-amber-200/60">
                    <div>
                        <span class="px-3 py-1 rounded-full bg-amber-500 text-white text-[11px] font-extrabold uppercase tracking-wider shadow-sm">Khusus Letting BDA 750</span>
                        <h3 class="font-display text-xl font-bold text-gray-900 mt-1.5">Dueling Plat 15M (Khusus BDA Korbrimob POLRI)</h3>
                    </div>
                    <span class="text-xs text-amber-900 font-semibold">Khusus kategori letting BDA se-Korbrimob Polri</span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-5 items-end">
                    <!-- Juara 2 BDA -->
                    <div class="order-2 md:order-1 text-center bg-white rounded-2xl p-6 border border-gray-200 shadow-sm hover:shadow-md transition">
                        <div class="w-12 h-12 mx-auto mb-3 rounded-full bg-gray-100 flex items-center justify-center font-display text-xl font-bold text-gray-700">
                            2
                        </div>
                        <h4 class="font-display text-base font-bold text-gray-900 mb-1">Juara II</h4>
                        <p class="font-display text-2xl font-extrabold text-copper-600 mb-1">Rp 1.250.000</p>
                        <p class="text-xs text-gray-500">+ Tropi + Piagam Resmi</p>
                    </div>

                    <!-- Juara 1 BDA -->
                    <div class="order-1 md:order-2 text-center bg-white rounded-2xl p-7 border-2 border-amber-500 shadow-lg relative group">
                        <span class="absolute -top-3.5 left-1/2 -translate-x-1/2 px-4 py-1 bg-amber-600 text-white text-[11px] font-bold uppercase tracking-widest rounded-full shadow-md z-20 flex items-center gap-1.5 border border-amber-300/40">
                            <i data-lucide="award" class="w-3 h-3 text-amber-200"></i>
                            <span>Juara I BDA</span>
                        </span>
                        <div class="w-14 h-14 mx-auto mb-3 rounded-full bg-amber-100 flex items-center justify-center text-amber-700 relative z-10 group-hover:scale-110 transition-transform">
                            <i data-lucide="crown" class="w-7 h-7"></i>
                        </div>
                        <h4 class="font-display text-xl font-bold text-amber-900 mb-1 relative z-10">Juara I</h4>
                        <p class="font-display text-3xl font-extrabold text-copper-600 mb-1 relative z-10">Rp 2.000.000</p>
                        <p class="text-xs text-gray-600 font-semibold relative z-10">+ Tropi + Piagam Resmi</p>
                    </div>

                    <!-- Juara 3 BDA -->
                    <div class="order-3 text-center bg-white rounded-2xl p-6 border border-gray-200 shadow-sm hover:shadow-md transition">
                        <div class="w-12 h-12 mx-auto mb-3 rounded-full bg-amber-100 flex items-center justify-center font-display text-xl font-bold text-amber-800">
                            3
                        </div>
                        <h4 class="font-display text-base font-bold text-gray-900 mb-1">Juara III</h4>
                        <p class="font-display text-2xl font-extrabold text-copper-600 mb-1">Rp 750.000</p>
                        <p class="text-xs text-gray-500">+ Tropi + Piagam Resmi</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Schedule Section (Tight Padding) -->
<section id="jadwal" class="section-lazy py-8 md:py-10 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-6">
            <span class="text-xs font-bold uppercase tracking-widest text-copper-600">Rundown Acara Resmi</span>
            <h2 class="font-display text-2xl sm:text-3xl font-bold mt-1 text-gray-900 uppercase tracking-wide" data-scramble>Jadwal Pertandingan</h2>
            <p class="text-xs sm:text-sm text-gray-500 mt-0.5">Tahapan teknis, uji coba, dan jadwal pelaksanaan kejuaraan</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- 1. TM (Minggu, 4 Oktober 2026) -->
            <div class="p-5 bg-gray-50 rounded-2xl border border-gray-200 flex flex-col justify-between shadow-sm">
                <div>
                    <div class="flex items-center gap-2 mb-3">
                        <span class="px-2.5 py-1 bg-amber-600 text-white rounded-lg font-display font-bold text-xs uppercase tracking-wider shadow-sm shadow-amber-600/20">TM</span>
                        <h3 class="font-display font-bold text-base text-gray-900">Minggu, 4 Okt 2026</h3>
                    </div>
                    <div class="mb-3 inline-flex items-center gap-1.5 px-2 py-0.5 rounded-md bg-amber-50 border border-amber-200/60 text-amber-800 text-[10px] font-medium">
                        <i data-lucide="calendar-clock" class="w-3 h-3 text-amber-600 shrink-0"></i>
                        <span>09.00 WIB s.d. Selesai</span>
                    </div>
                    <ul class="space-y-2.5 text-xs text-gray-700">
                        <li class="flex items-start gap-2">
                            <div class="w-4 h-4 rounded-full bg-amber-100 text-amber-700 flex items-center justify-center shrink-0 mt-0.5 font-bold text-[9px]">1</div>
                            <span class="leading-relaxed">Penyampaian Juknis &amp; Regulasi Pertandingan</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <div class="w-4 h-4 rounded-full bg-amber-100 text-amber-700 flex items-center justify-center shrink-0 mt-0.5 font-bold text-[9px]">2</div>
                            <span class="leading-relaxed">Sesi Diskusi &amp; Tanya Jawab Peserta</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <div class="w-4 h-4 rounded-full bg-amber-100 text-amber-700 flex items-center justify-center shrink-0 mt-0.5 font-bold text-[9px]">3</div>
                            <span class="leading-relaxed">Pengundian Lajur Tembak &amp; Skema Bagan Dueling</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <div class="w-4 h-4 rounded-full bg-amber-100 text-amber-700 flex items-center justify-center shrink-0 mt-0.5 font-bold text-[9px]">4</div>
                            <span class="leading-relaxed">Briefing Keselamatan Lapangan Tembak</span>
                        </li>
                    </ul>
                </div>
                <div class="mt-4 pt-3 border-t border-gray-200 text-[11px] text-gray-500 flex items-center gap-1.5">
                    <i data-lucide="info" class="w-3.5 h-3.5 text-amber-600 shrink-0"></i>
                    <span>Daftar ulang &amp; gun check pada hari H lomba.</span>
                </div>
            </div>

            <!-- 2. Uji Coba Lapangan (Kamis, 15 Oktober 2026) -->
            <div class="p-5 bg-gray-50 rounded-2xl border border-gray-200 flex flex-col justify-between shadow-sm">
                <div>
                    <div class="flex items-center gap-2 mb-3">
                        <span class="px-2.5 py-1 bg-blue-600 text-white rounded-lg font-display font-bold text-xs uppercase tracking-wider shadow-sm shadow-blue-600/20">Uji Coba</span>
                        <h3 class="font-display font-bold text-base text-gray-900">Kamis, 15 Okt 2026</h3>
                    </div>
                    <div class="mb-3 inline-flex items-center gap-1.5 px-2 py-0.5 rounded-md bg-blue-50 border border-blue-200/60 text-blue-800 text-[10px] font-medium">
                        <i data-lucide="clock" class="w-3 h-3 text-blue-600 shrink-0"></i>
                        <span>08.00 – 16.00 WIB</span>
                    </div>
                    <ul class="space-y-2.5 text-xs text-gray-700">
                        <li class="flex items-start gap-2">
                            <div class="w-4 h-4 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center shrink-0 mt-0.5 font-bold text-[9px]">1</div>
                            <span class="leading-relaxed">Uji Coba Lapangan Presisi 20 Meter</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <div class="w-4 h-4 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center shrink-0 mt-0.5 font-bold text-[9px]">2</div>
                            <span class="leading-relaxed">Uji Coba Lapangan &amp; Mekanisme Dueling Plat 15M</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <div class="w-4 h-4 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center shrink-0 mt-0.5 font-bold text-[9px]">3</div>
                            <span class="leading-relaxed">Adaptasi Fisik Lapangan bagi Peserta</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <div class="w-4 h-4 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center shrink-0 mt-0.5 font-bold text-[9px]">4</div>
                            <span class="leading-relaxed">Penyesuaian Visir &amp; Standar Senjata</span>
                        </li>
                    </ul>
                </div>
                <div class="mt-4 pt-3 border-t border-gray-200 text-[11px] text-gray-500 flex items-center gap-1.5">
                    <i data-lucide="map-pin" class="w-3.5 h-3.5 text-blue-600 shrink-0"></i>
                    <span>Lokasi: Lapangan Tembak Resimen I</span>
                </div>
            </div>

            <!-- 3. Hari ke-1 (Sabtu, 17 Oktober 2026) -->
            <div class="p-5 bg-gray-50 rounded-2xl border border-gray-200 flex flex-col justify-between shadow-sm">
                <div>
                    <div class="flex items-center gap-2 mb-3">
                        <span class="px-2.5 py-1 bg-copper-600 text-white rounded-lg font-display font-bold text-xs uppercase tracking-wider shadow-sm shadow-copper-600/20">Hari ke-1</span>
                        <h3 class="font-display font-bold text-base text-gray-900">Sabtu, 17 Okt 2026</h3>
                    </div>
                    <ul class="space-y-2 text-xs text-gray-700">
                        <li class="pb-1.5 border-b border-gray-200/80">
                            <div class="flex items-center justify-between text-[11px] mb-0.5">
                                <span class="font-mono font-bold text-copper-700 bg-copper-50 px-1.5 py-0.5 rounded text-[10px]">08.00 – 11.30 WIB</span>
                                <span class="text-[9px] font-semibold text-gray-500 uppercase">Pembukaan &amp; Presisi</span>
                            </div>
                            <p class="leading-tight text-gray-600 text-[11px]">Upacara pembukaan oleh Danresimen I dilanjutkan penembakan Presisi 20M</p>
                        </li>
                        <li class="py-1 px-2 bg-gray-100 rounded flex items-center justify-between text-[10px]">
                            <span class="font-mono font-medium text-gray-600">11.30 – 12.45 WIB</span>
                            <span class="font-bold text-amber-700">ISHOMA</span>
                        </li>
                        <li class="py-1.5 border-b border-gray-200/80">
                            <div class="flex items-center justify-between text-[11px] mb-0.5">
                                <span class="font-mono font-bold text-copper-700 bg-copper-50 px-1.5 py-0.5 rounded text-[10px]">12.45 – 15.00 WIB</span>
                                <span class="text-[9px] font-semibold text-gray-500 uppercase">Presisi &amp; Dueling</span>
                            </div>
                            <p class="leading-tight text-gray-600 text-[11px]">Lanjutan Presisi 20M &amp; babak penyisihan Dueling Plat</p>
                        </li>
                        <li class="py-1 px-2 bg-gray-100 rounded flex items-center justify-between text-[10px]">
                            <span class="font-mono font-medium text-gray-600">15.00 – 15.30 WIB</span>
                            <span class="font-bold text-amber-700">ISHOMA</span>
                        </li>
                        <li class="pt-1">
                            <div class="flex items-center justify-between text-[11px] mb-0.5">
                                <span class="font-mono font-bold text-copper-700 bg-copper-50 px-1.5 py-0.5 rounded text-[10px]">15.30 – 17.00 WIB</span>
                                <span class="text-[9px] font-semibold text-gray-500 uppercase">Presisi &amp; Dueling</span>
                            </div>
                            <p class="leading-tight text-gray-600 text-[11px]">Lanjutan Presisi 20M s.d. selesai &amp; babak penyisihan Dueling Plat</p>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- 4. Hari ke-2 (Minggu, 18 Oktober 2026) -->
            <div class="p-5 bg-gray-50 rounded-2xl border border-gray-200 flex flex-col justify-between shadow-sm">
                <div>
                    <div class="flex items-center gap-2 mb-3">
                        <span class="px-2.5 py-1 bg-emerald-600 text-white rounded-lg font-display font-bold text-xs uppercase tracking-wider shadow-sm shadow-emerald-600/20">Hari ke-2</span>
                        <h3 class="font-display font-bold text-base text-gray-900">Minggu, 18 Okt 2026</h3>
                    </div>
                    <ul class="space-y-2 text-xs text-gray-700">
                        <li class="pb-1.5 border-b border-gray-200/80">
                            <div class="flex items-center justify-between text-[11px] mb-0.5">
                                <span class="font-mono font-bold text-copper-700 bg-copper-50 px-1.5 py-0.5 rounded text-[10px]">08.00 – 11.00 WIB</span>
                                <span class="text-[9px] font-semibold text-gray-500 uppercase">Dueling Plat</span>
                            </div>
                            <p class="leading-tight text-gray-600 text-[11px]">Lanjutan pertandingan babak gugur Dueling Plat</p>
                        </li>
                        <li class="py-1 px-2 bg-gray-100 rounded flex items-center justify-between text-[10px]">
                            <span class="font-mono font-medium text-gray-600">11.00 – 13.00 WIB</span>
                            <span class="font-bold text-amber-700">ISHOMA</span>
                        </li>
                        <li class="py-1.5 border-b border-gray-200/80">
                            <div class="flex items-center justify-between text-[11px] mb-0.5">
                                <span class="font-mono font-bold text-copper-700 bg-copper-50 px-1.5 py-0.5 rounded text-[10px]">13.00 – 16.00 WIB</span>
                                <span class="text-[9px] font-semibold text-gray-500 uppercase">Semifinal &amp; Final</span>
                            </div>
                            <p class="leading-tight text-gray-600 text-[11px]">Babak semifinal, perebutan juara 3, hingga babak final</p>
                        </li>
                        <li class="pt-1">
                            <div class="flex items-center justify-between text-[11px] mb-0.5">
                                <span class="font-mono font-bold text-emerald-700 bg-emerald-50 px-1.5 py-0.5 rounded text-[10px]">16.00 – Selesai</span>
                                <span class="text-[9px] font-bold text-emerald-600 uppercase">Penutupan</span>
                            </div>
                            <p class="leading-tight text-gray-600 text-[11px]">Pengumuman juara, upacara penyerahan hadiah &amp; penutupan resmi</p>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Rules Section (Accordion, Tight Padding) -->
<section id="juknis" class="section-lazy py-8 md:py-10 bg-gray-50">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-6">
            <span class="text-xs font-bold uppercase tracking-widest text-copper-600">Petunjuk Teknis Resmi</span>
            <h2 class="font-display text-2xl sm:text-3xl font-bold mt-1 text-gray-900 uppercase tracking-wide" data-scramble>Peraturan & Persyaratan</h2>
            <p class="text-xs sm:text-sm text-gray-600 max-w-xl mx-auto mt-1">
                Panduan resmi pelaksanaan pertandingan, regulasi senjata, perlengkapan, dan sistem penilaian BDA Shooting Championship 2026.
            </p>
        </div>

        <div class="space-y-2.5" x-data="{ open: 1 }">
            <!-- Rule 1 -->
            <div class="border border-gray-200 rounded-xl overflow-hidden bg-white">
                <button @click="open = open === 1 ? null : 1" class="w-full flex items-center justify-between p-3.5 text-left font-semibold hover:bg-gray-50 transition text-sm">
                    <span class="flex items-center gap-2"><i data-lucide="user-check" class="w-4 h-4 text-copper-500"></i> Persyaratan Peserta &amp; Berkas</span>
                    <i data-lucide="chevron-down" class="w-4 h-4 transition-transform text-gray-500" :class="open === 1 ? 'rotate-180' : ''"></i>
                </button>
                <div x-show="open === 1" x-transition class="px-4 pb-3.5 text-xs text-gray-600 space-y-1.5 border-t border-gray-100 pt-2.5">
                    <p>&bull; Terbuka untuk seluruh anggota POLRI dan Letting BDA 750 se-Korbrimob Polri.</p>
                    <p>&bull; Wajib mengunggah <strong>Foto KTA (Kartu Tanda Anggota) Polri</strong> yang masih berlaku pada saat pendaftaran online.</p>
                    <p>&bull; Mengisi data resmi dengan mencantumkan <strong>Nomor Registrasi Pokok (NRP)</strong> dan Kesatuan/Club.</p>
                    <p>&bull; Menyelesaikan administrasi biaya pendaftaran resmi sesuai kategori yang diikuti.</p>
                </div>
            </div>
            
            <!-- Rule 2 -->
            <div class="border border-gray-200 rounded-xl overflow-hidden bg-white">
                <button @click="open = open === 2 ? null : 2" class="w-full flex items-center justify-between p-3.5 text-left font-semibold hover:bg-gray-50 transition text-sm">
                    <span class="flex items-center gap-2"><i data-lucide="crosshair" class="w-4 h-4 text-copper-500"></i> Ketentuan Senjata &amp; Amunisi</span>
                    <i data-lucide="chevron-down" class="w-4 h-4 transition-transform text-gray-500" :class="open === 2 ? 'rotate-180' : ''"></i>
                </button>
                <div x-show="open === 2" x-transition class="px-4 pb-3.5 text-xs text-gray-600 space-y-1.5 border-t border-gray-100 pt-2.5">
                    <p>&bull; Senjata berupa <strong>Pistol kaliber 9x19mm organik dinas satuan</strong> dalam kondisi laik tembak.</p>
                    <p>&bull; Pisir standar pabrikan (non-optik), panjang laras senjata maksimal 5 inci.</p>
                    <p>&bull; Amunisi disediakan oleh masing-masing peserta / kontingen.</p>
                    <p>&bull; Pemeriksaan fisik senjata (<em>gun check</em>) dan kelayakan dilakukan panitia sebelum peserta dipanggil ke lajur tembak.</p>
                </div>
            </div>

            <!-- Rule 3 -->
            <div class="border border-gray-200 rounded-xl overflow-hidden bg-white">
                <button @click="open = open === 3 ? null : 3" class="w-full flex items-center justify-between p-3.5 text-left font-semibold hover:bg-gray-50 transition text-sm">
                    <span class="flex items-center gap-2"><i data-lucide="shield" class="w-4 h-4 text-copper-500"></i> Pakaian &amp; Perlengkapan Wajib</span>
                    <i data-lucide="chevron-down" class="w-4 h-4 transition-transform text-gray-500" :class="open === 3 ? 'rotate-180' : ''"></i>
                </button>
                <div x-show="open === 3" x-transition class="px-4 pb-3.5 text-xs text-gray-600 space-y-1.5 border-t border-gray-100 pt-2.5">
                    <p>&bull; Seragam pertandingan: <strong>Pakaian Dinas Olahraga (PDO)</strong> atau <strong>Tactical</strong>, bersepatu.</p>
                    <p>&bull; Perlengkapan membawa senjata: <strong>Sabuk / belt dan holster</strong> standar yang aman.</p>
                    <p>&bull; Wajib mengenakan kacamata pelindung (<em>safety glasses</em>) dan pelindung telinga (<em>earmuff / earplug</em>) selama di area menembak.</p>
                </div>
            </div>

            <!-- Rule 4 -->
            <div class="border border-gray-200 rounded-xl overflow-hidden bg-white">
                <button @click="open = open === 4 ? null : 4" class="w-full flex items-center justify-between p-3.5 text-left font-semibold hover:bg-gray-50 transition text-sm">
                    <span class="flex items-center gap-2"><i data-lucide="calculator" class="w-4 h-4 text-copper-500"></i> Sistem Pertandingan &amp; Penilaian</span>
                    <i data-lucide="chevron-down" class="w-4 h-4 transition-transform text-gray-500" :class="open === 4 ? 'rotate-180' : ''"></i>
                </button>
                <div x-show="open === 4" x-transition class="px-4 pb-3.5 text-xs text-gray-600 space-y-2 border-t border-gray-100 pt-2.5">
                    <p><strong>Pistol Presisi 20M:</strong> Total 13 butir amunisi dalam 2 magasen (Magasen 1: 3 butir percobaan 1 menit; Magasen 2: 10 butir penilaian 3 menit). Sikap berdiri dengan 2 tangan wajib. Tinggi pusat lesan 140 cm. Nilai maksimal 100,10. Jika draw/nilai sama, penentuan pemenang dihitung dari jumlah Inner X terbanyak; jika masih sama dilakukan <em>shoot-off</em> 5 butir (1 menit).</p>
                    <p><strong>Dueling Plat 15M:</strong> Jarak tembak 15 meter, sasaran 5 plat bulat + 1 stop popper. Penembak duduk di kursi berjarak 5 meter di belakang meja tembak menghadap depan, berlari ke meja setelah peluit berbunyi. Amunisi 10 butir. Sistem gugur langsung head-to-head. <strong>Menjatuhkan stop popper sebelum 5 plat bulat jatuh seluruhnya dinyatakan Diskualifikasi (DQ)</strong>.</p>
                </div>
            </div>

            <!-- Rule 5 -->
            <div class="border border-gray-200 rounded-xl overflow-hidden bg-white">
                <button @click="open = open === 5 ? null : 5" class="w-full flex items-center justify-between p-3.5 text-left font-semibold hover:bg-gray-50 transition text-sm">
                    <span class="flex items-center gap-2"><i data-lucide="credit-card" class="w-4 h-4 text-copper-500"></i> Biaya &amp; Pembayaran</span>
                    <i data-lucide="chevron-down" class="w-4 h-4 transition-transform text-gray-500" :class="open === 5 ? 'rotate-180' : ''"></i>
                </button>
                <div x-show="open === 5" x-transition class="px-4 pb-3.5 text-xs text-gray-600 space-y-1.5 border-t border-gray-100 pt-2.5">
                    <p>&bull; Biaya pendaftaran: <strong>Rp 200.000 per kategori</strong> (Pistol Presisi 20M &amp; Dueling Plat 15M Umum POLRI) dan <strong>Rp 100.000 khusus untuk kategori Dueling Plat 15M (Khusus BDA Korbrimob POLRI)</strong>.</p>
                    <p>&bull; Transfer ke rekening resmi: <strong>Bank BRI 0538 0107 2120 508</strong> a.n. <strong>Ruly Ardana Putra</strong>.</p>
                    <p>&bull; Lampirkan bukti transfer saat pengisian formulir pendaftaran online.</p>
                </div>
            </div>

            <!-- Download Document Card Banner -->
            <div class="mt-4 p-4 sm:p-5 rounded-2xl bg-white border border-gray-200 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="flex items-center gap-3.5 text-left">
                    <div class="w-11 h-11 rounded-xl bg-copper-50 border border-copper-200 flex items-center justify-center text-copper-600 shrink-0">
                        <i data-lucide="file-text" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h4 class="font-display font-bold text-sm sm:text-base text-gray-900">Buku Petunjuk Teknis Lengkap (PDF)</h4>
                        <p class="text-xs text-gray-500">Unduh dokumen resmi Petunjuk Teknis BDA Shooting Championship 2026.</p>
                    </div>
                </div>
                <a
                    href="/JUKNIS_BDA_SHOOTING_CHAMPIONSHIP_2026.pdf"
                    download="JUKNIS_BDA_SHOOTING_CHAMPIONSHIP_2026.pdf"
                    target="_blank"
                    class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-copper-600 hover:bg-copper-700 text-white rounded-xl text-xs font-bold shadow-md shadow-copper-600/20 transition shrink-0 hover:scale-105 active:scale-95"
                >
                    <i data-lucide="download" class="w-4 h-4"></i>
                    <span>Download PDF Resmi</span>
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Interactive Lesan 25M Motion Graphics Simulation Section -->
<?php require_once __DIR__ . '/includes/simulasi-section.php'; ?>

<!-- Contact & Payment Section (Tight Padding) -->
<section id="kontak" class="section-lazy py-8 md:py-10 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-6">
            <span class="text-xs font-bold uppercase tracking-widest text-copper-600">Informasi & Bantuan</span>
            <h2 class="font-display text-2xl sm:text-3xl font-bold mt-1 text-gray-900 uppercase tracking-wide" data-scramble>Kontak Panitia Pelaksana</h2>
        </div>
        
        <!-- Payment Info Box -->
        <div class="max-w-md mx-auto mb-6 p-4 bg-copper-50 border border-copper-200 rounded-xl text-center" x-data="{ copied: false }">
            <p class="text-xs uppercase font-bold text-copper-700">Rekening Resmi Pendaftaran</p>
            <p class="font-display text-lg font-bold text-gray-900 mt-1">Bank BRI</p>
            <div class="mt-2 flex items-center justify-center gap-2">
                <span class="font-mono text-base font-bold text-copper-700">0538 0107 2120 508</span>
                <button type="button" @click="navigator.clipboard.writeText('053801072120508'); copied = true; setTimeout(() => copied = false, 2000)" 
                        class="px-2.5 py-1 bg-white rounded-md border border-copper-300 hover:bg-copper-100 text-xs font-semibold transition">
                    <span x-show="!copied">Salin</span>
                    <span x-show="copied" class="text-green-600">Tersalin!</span>
                </button>
            </div>
            <p class="text-[11px] text-gray-500 mt-1">a.n. Ruly Ardana Putra</p>
        </div>
        
        <!-- Contacts Grid -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
            <?php
            $contacts = [
                ['role' => 'Materi & Teknis', 'name' => 'Briptu Ady', 'phone' => '085283525761', 'display' => '0852-8352-5761'],
                ['role' => 'Materi & Teknis', 'name' => 'Briptu Huges Yustisio', 'phone' => '085272377704', 'display' => '0852-7237-7704'],
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
        <h2 class="font-display text-2xl sm:text-3xl font-bold mb-2 uppercase tracking-wide" data-scramble>Daftarkan Diri Anda Sekarang</h2>
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
            // Skip expensive 3D tilt calculation on touch/mobile screens to maintain 60-120fps
            if (window.matchMedia && window.matchMedia('(pointer: coarse)').matches) return;
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
        target: new Date('2026-10-17T07:00:00+07:00').getTime(),
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
