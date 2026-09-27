<?php
$pageTitle = 'BDA Shooting Championship 2026';
$currentPage = 'home';
require_once __DIR__ . '/includes/header.php';
?>

<!-- Hero Section with 2.5D Canvas, Synthwave Floor Grid, Particle System & Gyro Parallax -->
<section id="hero-section" class="relative hero-gradient text-white overflow-hidden" x-data="heroEngine()" x-init="initEngine()" @mousemove="handleMouseMove($event)" @mouseleave="handleMouseLeave()">
    <!-- 2.5D Interactive FX Canvas: Synthwave Floor Grid + Dust Particle System + 3D Target Reticle Vectors -->
    <canvas id="hero-fx-canvas" class="absolute inset-0 w-full h-full pointer-events-none z-0"></canvas>
    
    <div class="absolute inset-0 target-pattern opacity-15 pointer-events-none"></div>
    <!-- Horizon Line & Retrowave Horizon Glow behind the grid -->
    <div class="absolute inset-0 pointer-events-none overflow-hidden">
        <div class="absolute bottom-0 inset-x-0 h-96 bg-gradient-to-t from-black/80 via-transparent to-transparent"></div>
    </div>

    <div class="relative z-10 max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 pt-8 sm:pt-10 md:pt-12 pb-8 sm:pb-12 text-center">
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
        <h1 class="font-display text-4xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight leading-[1.08] text-white mb-5 sm:mb-6">
            <span data-scramble>BDA SHOOTING</span><br>
            <span data-scramble class="text-transparent bg-clip-text bg-gradient-to-r from-copper-400 via-amber-300 to-copper-500">CHAMPIONSHIP 2026</span>
        </h1>

        <!-- 3. Logo BDA Shooting dengan Latar Belakang Putih & Dual Concentric Reticle Motion (Parallax Depth: Pointer + Gyroscope) -->
        <div class="flex justify-center items-center mb-6">
            <div class="relative group">
                <!-- Outermost Rotating Reticle Ring (Clockwise Rotation 24s) -->
                <div class="absolute -inset-8 sm:-inset-11 md:-inset-14 pointer-events-none flex items-center justify-center">
                    <svg class="w-full h-full text-copper-400/75 animate-ring-outer-cw" viewBox="0 0 200 200" fill="none">
                        <circle cx="100" cy="100" r="95" stroke="currentColor" stroke-width="1.2" stroke-dasharray="4 8" />
                        <circle cx="100" cy="100" r="90" stroke="currentColor" stroke-width="0.8" stroke-dasharray="1 10" opacity="0.7" />
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

                <!-- Soft Glow Behind White Canvas -->
                <div class="absolute -inset-4 bg-gradient-to-r from-copper-500/40 via-amber-400/35 to-copper-600/40 rounded-full blur-2xl opacity-80 group-hover:opacity-100 transition duration-700 pointer-events-none"></div>

                <!-- White Canvas Container for Logo with 3D Parallax Tilt (Pointer + Gyroscope) -->
                <div id="hero-logo-canvas-box"
                     class="relative w-56 h-56 sm:w-72 sm:h-72 md:w-80 md:h-80 lg:w-96 lg:h-96 rounded-full bg-white shadow-2xl p-5 sm:p-7 md:p-8 flex items-center justify-center border-4 sm:border-8 border-copper-400/60 ring-4 sm:ring-8 ring-white/20 transition-transform duration-100 ease-out will-change-transform cursor-pointer"
                     :style="'transform: perspective(1000px) rotateX(' + tiltX.toFixed(2) + 'deg) rotateY(' + tiltY.toFixed(2) + 'deg) translate3d(' + (tiltY * 0.8).toFixed(1) + 'px, ' + (-tiltX * 0.8).toFixed(1) + 'px, 0)'"
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

        <!-- 4. Badge Tanggal Pelaksanaan (Lokasi Dihilangkan Sesuai Permintaan) -->
        <div class="flex items-center justify-center mt-4 sm:mt-5 mb-5 sm:mb-6 px-1">
            <div class="flex items-center gap-2 border border-white/15 bg-black/60 px-3.5 py-1.5 rounded-lg font-mono text-[11px] sm:text-xs tracking-[0.15em] text-gray-200 backdrop-blur-md shadow-sm">
                <i data-lucide="calendar" class="w-3.5 h-3.5 text-amber-400 shrink-0"></i>
                <span class="font-bold">17 — 18 OKTOBER 2026</span>
            </div>
        </div>

        <!-- 5. Countdown Box Tactical (Sesuai Desain Gambar) -->
        <div id="hero-countdown-box" class="w-full max-w-sm sm:max-w-md mx-auto mb-5 sm:mb-6" x-data="countdown()" x-init="start()">
            <!-- Subheader -->
            <p class="text-center font-mono text-[10px] sm:text-[11px] font-bold tracking-[0.35em] text-gray-400 uppercase mb-2.5">
                — COUNTDOWN HARI-H —
            </p>

            <!-- 4 Tactical Cards -->
            <div class="grid grid-cols-4 gap-2 sm:gap-2.5 text-center">
                <!-- Hari -->
                <div class="relative border border-white/15 bg-[#0e1017]/90 backdrop-blur-md px-1.5 py-3 sm:py-3.5 rounded-sm shadow-lg overflow-hidden">
                    <span class="absolute left-0 top-0 h-2.5 w-2.5 border-l-2 border-t-2 border-amber-400/90 pointer-events-none"></span>
                    <span class="absolute bottom-0 right-0 h-2.5 w-2.5 border-b-2 border-r-2 border-amber-400/90 pointer-events-none"></span>
                    
                    <p class="font-mono text-2xl sm:text-3xl font-extrabold text-white tabular-nums tracking-tight" x-text="days">00</p>
                    <p class="font-mono text-[9px] sm:text-[10px] font-bold tracking-[0.25em] text-gray-400 uppercase mt-1">HARI</p>
                </div>

                <!-- Jam -->
                <div class="relative border border-white/15 bg-[#0e1017]/90 backdrop-blur-md px-1.5 py-3 sm:py-3.5 rounded-sm shadow-lg overflow-hidden">
                    <span class="absolute left-0 top-0 h-2.5 w-2.5 border-l-2 border-t-2 border-amber-400/90 pointer-events-none"></span>
                    <span class="absolute bottom-0 right-0 h-2.5 w-2.5 border-b-2 border-r-2 border-amber-400/90 pointer-events-none"></span>
                    
                    <p class="font-mono text-2xl sm:text-3xl font-extrabold text-white tabular-nums tracking-tight" x-text="hours">00</p>
                    <p class="font-mono text-[9px] sm:text-[10px] font-bold tracking-[0.25em] text-gray-400 uppercase mt-1">JAM</p>
                </div>

                <!-- Menit -->
                <div class="relative border border-white/15 bg-[#0e1017]/90 backdrop-blur-md px-1.5 py-3 sm:py-3.5 rounded-sm shadow-lg overflow-hidden">
                    <span class="absolute left-0 top-0 h-2.5 w-2.5 border-l-2 border-t-2 border-amber-400/90 pointer-events-none"></span>
                    <span class="absolute bottom-0 right-0 h-2.5 w-2.5 border-b-2 border-r-2 border-amber-400/90 pointer-events-none"></span>
                    
                    <p class="font-mono text-2xl sm:text-3xl font-extrabold text-white tabular-nums tracking-tight" x-text="minutes">00</p>
                    <p class="font-mono text-[9px] sm:text-[10px] font-bold tracking-[0.25em] text-gray-400 uppercase mt-1">MENIT</p>
                </div>

                <!-- Detik -->
                <div class="relative border border-white/15 bg-[#0e1017]/90 backdrop-blur-md px-1.5 py-3 sm:py-3.5 rounded-sm shadow-lg overflow-hidden">
                    <span class="absolute left-0 top-0 h-2.5 w-2.5 border-l-2 border-t-2 border-amber-400/90 pointer-events-none"></span>
                    <span class="absolute bottom-0 right-0 h-2.5 w-2.5 border-b-2 border-r-2 border-amber-400/90 pointer-events-none"></span>
                    
                    <p class="font-mono text-2xl sm:text-3xl font-extrabold text-white tabular-nums tracking-tight" x-text="seconds">00</p>
                    <p class="font-mono text-[9px] sm:text-[10px] font-bold tracking-[0.25em] text-gray-400 uppercase mt-1">DETIK</p>
                </div>
            </div>
        </div>

        <!-- 6. Tombol Aksi Hero Full-Width Stacked (Sesuai Desain Gambar) -->
        <div id="hero-action-box" class="w-full max-w-sm sm:max-w-md mx-auto flex flex-col gap-2.5 sm:gap-3 mb-6">
            <!-- Tombol Daftar Sekarang (Solid Gold/Amber) -->
            <a href="/daftar.php" 
               class="group relative w-full py-3.5 sm:py-4 px-6 bg-[#f0b23e] hover:bg-[#ffd97e] text-[#060709] font-mono text-xs sm:text-sm font-extrabold tracking-[0.2em] uppercase rounded-sm flex items-center justify-center gap-2 shadow-[0_0_30px_rgba(240,178,62,0.3)] hover:shadow-[0_0_45px_rgba(240,178,62,0.5)] transition-all duration-300">
                <span>DAFTAR SEKARANG</span>
                <i data-lucide="arrow-up-right" class="w-4 h-4 text-[#060709] stroke-[2.5] group-hover:translate-x-0.5 group-hover:-translate-y-0.5 transition-transform"></i>
            </a>

            <!-- Tombol Kedua: Sebelum 16 Oktober -> PETUNJUK TEKNIS (simbol buku -> #juknis) / Mulai 16 Oktober -> LIVE SKOR (/live-score.php) -->
            <?php
            // Batas pergantian tombol: 16 Oktober 2026 00:00:00 WIB
            $isLiveScoreActive = (time() >= strtotime('2026-10-16 00:00:00'));
            if (!$isLiveScoreActive):
            ?>
            <!-- Tombol Petunjuk Teknis (Sebelum 16 Oktober) -->
            <a href="#juknis" 
               class="group relative w-full py-3.5 sm:py-4 px-6 bg-[#060709]/85 hover:bg-[#060709] border border-white/20 hover:border-[#f0b23e] text-white hover:text-[#f0b23e] font-mono text-xs sm:text-sm font-extrabold tracking-[0.2em] uppercase rounded-sm backdrop-blur-sm flex items-center justify-center gap-2.5 transition-all duration-300 shadow-md">
                <i data-lucide="book-open" class="w-4 h-4 text-[#f0b23e] group-hover:scale-110 transition-transform"></i>
                <span>PETUNJUK TEKNIS</span>
            </a>
            <?php else: ?>
            <!-- Tombol Live Skor (Mulai 16 Oktober) -->
            <a href="/live-score.php" 
               class="group relative w-full py-3.5 sm:py-4 px-6 bg-[#060709]/85 hover:bg-[#060709] border border-white/20 hover:border-[#f0b23e] text-white hover:text-[#f0b23e] font-mono text-xs sm:text-sm font-extrabold tracking-[0.2em] uppercase rounded-sm backdrop-blur-sm flex items-center justify-center gap-2 transition-all duration-300 shadow-md">
                <span class="inline-flex items-center gap-1 text-red-500 font-mono text-sm leading-none shrink-0">
                    <span class="opacity-70 animate-pulse font-bold">(</span>
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-500 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-red-600"></span>
                    </span>
                    <span class="opacity-70 animate-pulse font-bold">)</span>
                </span>
                <span>LIVE SKOR</span>
            </a>
            <?php endif; ?>
        </div>

        <!-- 7. Scroll Down Indicator (Sesuai Desain Gambar) -->
        <div id="hero-scroll-box" class="flex justify-center pt-2 sm:pt-3 pb-3">
            <a href="#tentang" class="group flex flex-col items-center gap-1 text-gray-400 hover:text-[#f0b23e] transition-colors cursor-pointer select-none">
                <span class="font-mono text-[9px] sm:text-[10px] tracking-[0.4em] uppercase font-bold">SCROLL</span>
                <i data-lucide="chevron-down" class="w-4 h-4 group-hover:translate-y-1 transition-transform animate-bounce"></i>
            </a>
        </div>

    </div>
</section>

<!-- Tactical Moving Police Line / Caution Marquee Tape (Clear & Crisp, Tanpa Terpotong) -->
<div class="relative w-full overflow-hidden z-20 -my-2.5 sm:-my-3 select-none">
    <div class="relative -rotate-[1deg] w-[106%] -ml-[3%] border-y-[3px] border-[#060709] bg-[#f0b23e] py-3 sm:py-3.5 shadow-[0_12px_35px_rgba(0,0,0,0.5)]">
        <div class="flex w-max animate-police-line">
            <!-- Track A -->
            <div class="flex items-center shrink-0">
                <?php
                $policeItems = [
                    'DUELING PLAT 15M',
                    'PISTOL PRESISI 20M',
                    'BDA750',
                    '17 - 18 OKTOBER 2026',
                    'KEDUNGHALANG - BOGOR',
                    'RESIMEN I PASPELOPOR'
                ];
                for ($cycle = 0; $cycle < 2; $cycle++):
                    foreach ($policeItems as $item):
                ?>
                    <span class="flex items-center gap-6 sm:gap-8 pr-6 sm:pr-8">
                        <span class="whitespace-nowrap font-display text-sm sm:text-base md:text-lg font-black tracking-[0.18em] text-[#060709] uppercase drop-shadow-sm"><?= htmlspecialchars($item) ?></span>
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 shrink-0 text-[#060709]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"></circle>
                            <circle cx="12" cy="12" r="5"></circle>
                            <circle cx="12" cy="12" r="1.5" fill="currentColor"></circle>
                            <line x1="12" y1="1" x2="12" y2="4"></line>
                            <line x1="12" y1="20" x2="12" y2="23"></line>
                            <line x1="1" y1="12" x2="4" y2="12"></line>
                            <line x1="20" y1="12" x2="23" y2="12"></line>
                        </svg>
                    </span>
                <?php 
                    endforeach;
                endfor; 
                ?>
            </div>

            <!-- Track B (Duplicate for Seamless Infinite Marquee Loop) -->
            <div class="flex items-center shrink-0" aria-hidden="true">
                <?php
                for ($cycle = 0; $cycle < 2; $cycle++):
                    foreach ($policeItems as $item):
                ?>
                    <span class="flex items-center gap-6 sm:gap-8 pr-6 sm:pr-8">
                        <span class="whitespace-nowrap font-display text-sm sm:text-base md:text-lg font-black tracking-[0.18em] text-[#060709] uppercase drop-shadow-sm"><?= htmlspecialchars($item) ?></span>
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 shrink-0 text-[#060709]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"></circle>
                            <circle cx="12" cy="12" r="5"></circle>
                            <circle cx="12" cy="12" r="1.5" fill="currentColor"></circle>
                            <line x1="12" y1="1" x2="12" y2="4"></line>
                            <line x1="12" y1="20" x2="12" y2="23"></line>
                            <line x1="1" y1="12" x2="4" y2="12"></line>
                            <line x1="20" y1="12" x2="23" y2="12"></line>
                        </svg>
                    </span>
                <?php 
                    endforeach;
                endfor; 
                ?>
            </div>
        </div>
    </div>
</div>

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
                <h3 class="font-display text-lg font-bold mb-1.5 text-gray-900 text-center uppercase" data-scramble>3 Kategori / Kelas Lomba</h3>
                <p class="text-xs text-gray-600 leading-relaxed text-center">
                    Pistol Presisi 20M Umum, Dueling Plat 15M Umum POLRI, dan Dueling Plat Khusus BDA Korbrimob.
                </p>
            </div>
            
            <div class="bg-white rounded-2xl p-6 border border-gray-200 shadow-sm hover:border-copper-400 hover:shadow-md transition text-center flex flex-col items-center">
                <div class="w-12 h-12 rounded-xl bg-copper-100 flex items-center justify-center mb-3 text-copper-600 mx-auto">
                    <i data-lucide="trophy" class="w-6 h-6"></i>
                </div>
                <h3 class="font-display text-lg font-bold mb-1.5 text-gray-900 text-center uppercase" data-scramble>Hadiah Uang Tunai</h3>
                <p class="text-xs text-gray-600 leading-relaxed text-center">
                    Uang tunai Juara I, II, dan III per kategori + Tropi + Sertifikat penghargaan resmi.
                </p>
            </div>

            <div class="bg-white rounded-2xl p-6 border border-gray-200 shadow-sm hover:border-copper-400 hover:shadow-md transition text-center flex flex-col items-center">
                <div class="w-12 h-12 rounded-xl bg-copper-100 flex items-center justify-center mb-3 text-copper-600 mx-auto">
                    <i data-lucide="shield-check" class="w-6 h-6"></i>
                </div>
                <h3 class="font-display text-lg font-bold mb-1.5 text-gray-900 text-center uppercase" data-scramble>Peserta Kejuaraan</h3>
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
                            <h3 class="font-display text-lg font-bold text-gray-900 group-hover:text-copper-600 transition-colors uppercase" data-scramble>Pistol Presisi 20M</h3>
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
                            <h3 class="font-display text-lg font-bold text-gray-900 group-hover:text-copper-600 transition-colors uppercase" data-scramble>Dueling Plat 15M</h3>
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
                            <h3 class="font-display text-lg font-bold text-gray-900 group-hover:text-copper-600 transition-colors uppercase" data-scramble>Dueling Plat 15M</h3>
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
                        <h3 class="font-display text-xl font-bold text-gray-900 mt-1.5 uppercase" data-scramble>Pistol Presisi 20M &amp; Dueling Plat 15M (Umum)</h3>
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
                        <h3 class="font-display text-xl font-bold text-gray-900 mt-1.5 uppercase" data-scramble>Dueling Plat 15M (Khusus BDA Korbrimob POLRI)</h3>
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
                        <h3 class="font-display font-bold text-base text-gray-900 uppercase" data-scramble>Minggu, 4 Okt 2026</h3>
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
                        <h3 class="font-display font-bold text-base text-gray-900 uppercase" data-scramble>Kamis, 15 Okt 2026</h3>
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
                        <h3 class="font-display font-bold text-base text-gray-900 uppercase" data-scramble>Sabtu, 17 Okt 2026</h3>
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
                        <h3 class="font-display font-bold text-base text-gray-900 uppercase" data-scramble>Minggu, 18 Okt 2026</h3>
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
        <div class="text-center mb-7 sm:mb-8">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-copper-100/70 border border-copper-200 text-xs sm:text-sm font-bold uppercase tracking-widest text-copper-700 mb-2">
                <i data-lucide="book-open" class="w-3.5 h-3.5 text-copper-600"></i>
                Buku Regulasi &amp; Panduan Resmi
            </span>
            <h2 class="font-display text-3xl sm:text-4xl md:text-5xl font-extrabold text-gray-900 uppercase tracking-tight" data-scramble>PETUNJUK TEKNIS</h2>
            <p class="text-sm sm:text-base font-bold text-copper-600 uppercase tracking-wider mt-1.5">Peraturan &amp; Persyaratan Pertandingan</p>
            <p class="text-xs sm:text-sm text-gray-600 max-w-xl mx-auto mt-2">
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
                    <p>&bull; Seragam pertandingan: <strong>PDO</strong> atau <strong>Tactical</strong>, bersepatu.</p>
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

            <!-- Rule 6: Tempat & Lokasi Pertandingan -->
            <div class="border border-gray-200 rounded-xl overflow-hidden bg-white">
                <button @click="open = open === 6 ? null : 6" class="w-full flex items-center justify-between p-3.5 text-left font-semibold hover:bg-gray-50 transition text-sm">
                    <span class="flex items-center gap-2"><i data-lucide="map-pin" class="w-4 h-4 text-copper-500"></i> Tempat &amp; Lokasi Pertandingan</span>
                    <i data-lucide="chevron-down" class="w-4 h-4 transition-transform text-gray-500" :class="open === 6 ? 'rotate-180' : ''"></i>
                </button>
                <div x-show="open === 6" x-transition class="px-4 pb-3.5 text-xs text-gray-600 space-y-2 border-t border-gray-100 pt-2.5">
                    <p>&bull; <strong>Tempat:</strong> Lapangan Tembak Resimen I Pasukan Pelopor, Kedunghalang, Bogor.</p>
                    <p>&bull; Navigasi dan rute perjalanan menuju lokasi pertandingan dapat diakses langsung via Google Maps.</p>
                    <div class="pt-1">
                        <a href="https://maps.app.goo.gl/Yu9CHBdxc85ddAuR7" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-copper-600 hover:bg-copper-700 text-white rounded-lg font-bold text-xs shadow-sm shadow-copper-600/20 transition hover:scale-105 active:scale-95">
                            <i data-lucide="map" class="w-3.5 h-3.5"></i>
                            <span>Buka di Google Maps</span>
                            <i data-lucide="external-link" class="w-3.5 h-3.5"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Quick Location & Document Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5 mt-4">
                <!-- Location Card -->
                <div class="p-4 sm:p-5 rounded-2xl bg-white border border-gray-200 shadow-sm flex flex-col justify-between gap-3 text-left">
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 rounded-xl bg-copper-50 border border-copper-200 flex items-center justify-center text-copper-600 shrink-0">
                            <i data-lucide="map-pin" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <h4 class="font-display font-bold text-sm sm:text-base text-gray-900 uppercase" data-scramble>Lokasi Lapangan Tembak</h4>
                            <p class="text-xs text-gray-500">Resimen I Pasukan Pelopor, Kedunghalang, Bogor.</p>
                        </div>
                    </div>
                    <a
                        href="https://maps.app.goo.gl/Yu9CHBdxc85ddAuR7"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-gray-900 hover:bg-gray-800 text-white rounded-xl text-xs font-bold transition shadow-sm hover:scale-[1.02] active:scale-95"
                    >
                        <i data-lucide="map" class="w-4 h-4 text-copper-400"></i>
                        <span>Buka di Google Maps</span>
                        <i data-lucide="external-link" class="w-3.5 h-3.5"></i>
                    </a>
                </div>

                <!-- Download Document Card Banner -->
                <div class="p-4 sm:p-5 rounded-2xl bg-white border border-gray-200 shadow-sm flex flex-col justify-between gap-3 text-left">
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 rounded-xl bg-copper-50 border border-copper-200 flex items-center justify-center text-copper-600 shrink-0">
                            <i data-lucide="file-text" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <h4 class="font-display font-bold text-sm sm:text-base text-gray-900 uppercase" data-scramble>Buku Petunjuk Teknis Lengkap (PDF)</h4>
                            <p class="text-xs text-gray-500">Unduh dokumen resmi Petunjuk Teknis BDA Shooting Championship 2026.</p>
                        </div>
                    </div>
                    <a
                        href="/JUKNIS%20BDA%20SHOOTING%20CHAMPIONSHIP%202026.pdf"
                        download="JUKNIS BDA SHOOTING CHAMPIONSHIP 2026.pdf"
                        target="_blank"
                        class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-copper-600 hover:bg-copper-700 text-white rounded-xl text-xs font-bold shadow-md shadow-copper-600/20 transition hover:scale-[1.02] active:scale-95"
                    >
                        <i data-lucide="download" class="w-4 h-4"></i>
                        <span>Download PDF Resmi</span>
                    </a>
                </div>
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
@keyframes reticleRotateCW {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
.animate-ring-outer-cw {
    animation: reticleRotateCW 24s linear infinite;
    transform-origin: center center;
    will-change: transform;
}
@keyframes radarSweep {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
.animate-radar-sweep {
    animation: radarSweep 14s linear infinite;
}
@keyframes policeLineMarquee {
    0% { transform: translate3d(0, 0, 0); }
    100% { transform: translate3d(-50%, 0, 0); }
}
.animate-police-line {
    display: flex;
    width: max-content;
    animation: policeLineMarquee 48s linear infinite;
    will-change: transform;
}
.animate-police-line:hover {
    animation-play-state: paused;
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
function heroEngine() {
    return {
        tiltX: 0,
        tiltY: 0,
        targetTiltX: 0,
        targetTiltY: 0,
        isHovered: false,
        animFrameId: null,

        initEngine() {
            this.initCanvas();
            this.initGyroscope();
        },

        handleMouseMove(e) {
            const section = document.getElementById('hero-section');
            if (!section) return;
            const rect = section.getBoundingClientRect();
            const nx = ((e.clientX - rect.left) / rect.width - 0.5) * 2;
            const ny = ((e.clientY - rect.top) / rect.height - 0.5) * 2;
            this.targetTiltY = nx * 9;
            this.targetTiltX = -ny * 9;
        },

        handleMouseLeave() {
            this.targetTiltX = 0;
            this.targetTiltY = 0;
        },

        initGyroscope() {
            const handleOrientation = (e) => {
                if (e.gamma === null || e.beta === null) return;
                const gamma = Math.max(-40, Math.min(40, e.gamma));
                const beta = Math.max(-40, Math.min(40, e.beta - 32));
                this.targetTiltY = (gamma / 40) * 12;
                this.targetTiltX = (-beta / 40) * 12;
            };

            if (typeof DeviceOrientationEvent !== 'undefined' && typeof DeviceOrientationEvent.requestPermission === 'function') {
                const triggerPermission = () => {
                    DeviceOrientationEvent.requestPermission()
                        .then(perm => {
                            if (perm === 'granted') {
                                window.addEventListener('deviceorientation', handleOrientation, { passive: true });
                            }
                        })
                        .catch(() => {});
                    window.removeEventListener('click', triggerPermission);
                    window.removeEventListener('touchstart', triggerPermission);
                };
                window.addEventListener('click', triggerPermission, { once: true });
                window.addEventListener('touchstart', triggerPermission, { once: true });
            } else if (window.DeviceOrientationEvent) {
                window.addEventListener('deviceorientation', handleOrientation, { passive: true });
            }
        },

        initCanvas() {
            const canvas = document.getElementById('hero-fx-canvas');
            if (!canvas) return;
            const ctx = canvas.getContext('2d');
            if (!ctx) return;

            let width = 0, height = 0;
            let dpr = Math.min(window.devicePixelRatio || 1, 2);

            const resize = () => {
                const rect = canvas.getBoundingClientRect();
                width = rect.width;
                height = rect.height;
                dpr = Math.min(window.devicePixelRatio || 1, 2);
                canvas.width = Math.floor(width * dpr);
                canvas.height = Math.floor(height * dpr);
            };
            resize();
            window.addEventListener('resize', resize, { passive: true });

            // 1. Perspective Projection Function: project(x,y,z) with focalLength
            const project = (x, y, z, focalLength, cx, cy) => {
                if (z <= -focalLength + 1) return { x: cx, y: cy, scale: 0, visible: false };
                const scale = focalLength / (focalLength + z);
                return {
                    x: cx + x * scale,
                    y: cy + y * scale,
                    scale: scale,
                    visible: true
                };
            };

            // 2. Initialize Particle System (Floating dust & embers with twinkle)
            const particleCount = 65;
            const particles = [];
            for (let i = 0; i < particleCount; i++) {
                particles.push({
                    x: (Math.random() - 0.5) * 900,
                    y: (Math.random() - 0.5) * 1000,
                    z: Math.random() * 700 + 40,
                    size: Math.random() * 2 + 1.2,
                    speedY: Math.random() * 0.45 + 0.25,
                    twinkleSpeed: Math.random() * 2.5 + 1.2,
                    phase: Math.random() * Math.PI * 2,
                    baseAlpha: Math.random() * 0.45 + 0.35,
                    color: Math.random() > 0.3 ? 'amber' : 'white'
                });
            }

            // 3. Grid Variables (Synthwave / Retrowave Floor Grid)
            let gridZOffset = 0;
            const gridSpacing = 50;
            const gridMaxZ = 750;
            const gridSpeed = 0.85;

            // Pause when offscreen to preserve battery
            let isVisible = true;
            if ('IntersectionObserver' in window) {
                const observer = new IntersectionObserver((entries) => {
                    isVisible = entries[0].isIntersecting;
                }, { threshold: 0.05 });
                observer.observe(canvas);
            }

            let lastTime = performance.now();

            const render = (now) => {
                this.animFrameId = requestAnimationFrame(render);
                if (!isVisible) return;

                const dt = Math.min((now - lastTime) / 1000, 0.1);
                lastTime = now;

                // Smooth tilt interpolation (lerp)
                this.tiltX += (this.targetTiltX - this.tiltX) * 0.08;
                this.tiltY += (this.targetTiltY - this.tiltY) * 0.08;

                ctx.save();
                ctx.scale(dpr, dpr);
                ctx.clearRect(0, 0, width, height);

                const logoBox = document.getElementById('hero-logo-canvas-box');
                let cx = width / 2;
                let cy = height * 0.36;
                if (logoBox) {
                    const lRect = logoBox.getBoundingClientRect();
                    const cRect = canvas.getBoundingClientRect();
                    cx = (lRect.left + lRect.width / 2) - cRect.left;
                    cy = (lRect.top + lRect.height / 2) - cRect.top;
                }

                const canvasRect = canvas.getBoundingClientRect();
                const countdownEl = document.getElementById('hero-countdown-box');
                const actionBox = document.getElementById('hero-action-box');
                const scrollEl = document.getElementById('hero-scroll-box');

                // Floor grid: ujung hero atas (y = 0) sampai ujung hero bawah (y = height)
                const focalLength = 380;
                const vpy = -height * 0.35; // Vanishing point virtual horizon di atas hero
                const zNear = 38;
                const floorY = (height - vpy) * ((focalLength + zNear) / focalLength);
                const zFar = Math.max(zNear + 200, ((height - vpy) * (focalLength + zNear) / (-vpy)) - focalLength);

                const parallaxX = (this.tiltY / 9) * 25;
                const parallaxY = (-this.tiltX / 9) * 20;

                // -------------------------------------------------------------
                // A. SYNTHWAVE / RETROWAVE FLOOR GRID (Full-bleed Hero: Top to Bottom)
                // -------------------------------------------------------------
                gridZOffset = (gridZOffset + gridSpeed) % gridSpacing;

                ctx.save();
                ctx.beginPath();
                ctx.rect(0, 0, width, height);
                ctx.clip();

                // Top Ambient Horizon Glow
                const horizonGrad = ctx.createRadialGradient(
                    cx + parallaxX * 0.25, 0, 10,
                    cx + parallaxX * 0.25, 0, width * 0.75
                );
                horizonGrad.addColorStop(0, 'rgba(245, 158, 11, 0.25)');
                horizonGrad.addColorStop(0.4, 'rgba(228, 85, 22, 0.08)');
                horizonGrad.addColorStop(1, 'rgba(0, 0, 0, 0)');
                ctx.fillStyle = horizonGrad;
                ctx.fillRect(0, 0, width, Math.min(260, height * 0.35));

                // Perspective Longitudinal Lines (Fanning out from top to bottom)
                const lineCount = Math.floor(width / 45) + 6;
                const lineSpacingX = 85;
                ctx.lineWidth = 1.1;

                for (let i = -lineCount; i <= lineCount; i++) {
                    const worldX = i * lineSpacingX;
                    const pFar = project(worldX + parallaxX * 0.35, floorY, zFar, focalLength, cx, vpy);
                    const pNear = project(worldX + parallaxX * 0.55, floorY, zNear, focalLength, cx, vpy);

                    if (pFar.visible && pNear.visible) {
                        const lineGrad = ctx.createLinearGradient(pFar.x, pFar.y, pNear.x, pNear.y);
                        lineGrad.addColorStop(0, 'rgba(245, 158, 11, 0.08)');
                        lineGrad.addColorStop(0.3, 'rgba(245, 158, 11, 0.20)');
                        lineGrad.addColorStop(0.7, 'rgba(240, 178, 62, 0.35)');
                        lineGrad.addColorStop(1, 'rgba(228, 85, 22, 0.50)');
                        ctx.strokeStyle = lineGrad;

                        ctx.beginPath();
                        ctx.moveTo(pFar.x, Math.max(0, pFar.y));
                        ctx.lineTo(pNear.x, Math.min(height, pNear.y));
                        ctx.stroke();
                    }
                }

                // Transverse Horizontal Lines (Scrolling smoothly from top y=0 to bottom y=height)
                for (let z = zFar - ((zFar - gridZOffset) % gridSpacing); z >= zNear; z -= gridSpacing) {
                    const pL = project(-1600 + parallaxX * 0.45, floorY, z, focalLength, cx, vpy);
                    const pR = project(1600 + parallaxX * 0.45, floorY, z, focalLength, cx, vpy);

                    if (pL.visible && pR.visible) {
                        const depthRatio = 1 - ((z - zNear) / (zFar - zNear));
                        const alpha = Math.max(0.04, Math.min(0.55, Math.pow(depthRatio, 1.25) * 0.58));

                        ctx.strokeStyle = `rgba(240, 178, 62, ${alpha.toFixed(3)})`;
                        ctx.lineWidth = 0.9 + (depthRatio * 1.1);
                        ctx.beginPath();
                        ctx.moveTo(pL.x, pL.y);
                        ctx.lineTo(pR.x, pR.y);
                        ctx.stroke();
                    }
                }

                ctx.restore();

                // -------------------------------------------------------------
                // B. PARTICLE SYSTEM (Floating dust & embers with twinkle)
                // -------------------------------------------------------------
                for (let i = 0; i < particles.length; i++) {
                    const p = particles[i];

                    p.y -= p.speedY;
                    if (p.y < -height * 0.65) {
                        p.y = height * 0.65;
                        p.x = (Math.random() - 0.5) * width * 1.5;
                    }

                    const twinkle = Math.sin(now * 0.001 * p.twinkleSpeed + p.phase);
                    const currentAlpha = Math.max(0.08, Math.min(0.9, p.baseAlpha + twinkle * 0.35));

                    const depthFactor = 1 - (p.z / 750);
                    const proj = project(
                        p.x + parallaxX * depthFactor * 1.4,
                        p.y + parallaxY * depthFactor * 1.4,
                        p.z,
                        focalLength,
                        cx,
                        cy
                    );

                    if (proj.visible) {
                        const radius = Math.max(0.8, p.size * proj.scale);

                        ctx.beginPath();
                        ctx.arc(proj.x, proj.y, radius, 0, Math.PI * 2);

                        if (p.color === 'amber') {
                            ctx.fillStyle = `rgba(245, 158, 11, ${currentAlpha.toFixed(2)})`;
                            ctx.shadowColor = 'rgba(245, 158, 11, 0.6)';
                            ctx.shadowBlur = radius * 3;
                        } else {
                            ctx.fillStyle = `rgba(255, 255, 255, ${(currentAlpha * 0.85).toFixed(2)})`;
                            ctx.shadowColor = 'rgba(255, 255, 255, 0.5)';
                            ctx.shadowBlur = radius * 2;
                        }
                        ctx.fill();
                        ctx.shadowBlur = 0;
                    }
                }

                ctx.restore();
            };

            this.animFrameId = requestAnimationFrame(render);
        }
    };
}

function countdown() {
    return {
        days: '00', hours: '00', minutes: '00', seconds: '00',
        target: new Date('2026-10-17T07:00:00+07:00').getTime(),
        start() {
            this.update();
            setInterval(() => this.update(), 1000);
        },
        update() {
            const now = Date.now();
            const diff = Math.max(0, this.target - now);
            this.days = String(Math.floor(diff / (1000 * 60 * 60 * 24))).padStart(2, '0');
            this.hours = String(Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60))).padStart(2, '0');
            this.minutes = String(Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60))).padStart(2, '0');
            this.seconds = String(Math.floor((diff % (1000 * 60)) / 1000)).padStart(2, '0');
        }
    }
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
