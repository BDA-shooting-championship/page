<?php
require_once __DIR__ . '/../includes/config.php';
$pageTitle = 'BDA Shooting Championship 2026 — Tactical Official Briefing';
?>
<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <meta name="description" content="Kejuaraan Menembak Pistol Presisi 20M & Dueling Plat Speed dalam rangka HUT Letting BDA 750 ke-7 di Lapangan Tembak Kedunghalang Bogor.">
    <link rel="icon" type="image/png" sizes="32x32" href="/assets/favicon-32x32.png">
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    <link rel="apple-touch-icon" href="/assets/favicon-192.png">

    <!-- Preconnect & Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Chakra+Petch:wght@500;600;700&family=Rajdhani:wght@500;600;700&family=Teko:wght@500;600;700&display=swap" rel="stylesheet">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        bda: {
                            50: '#fffbeb',
                            100: '#fef3c7',
                            200: '#fde68a',
                            300: '#fcd34d',
                            400: '#fbbf24',
                            500: '#f59e0b', // Gold Primary
                            600: '#d97706', // Copper Accent
                            700: '#b45309', // Dark Bronze
                            800: '#92400e',
                            900: '#78350f',
                            dark: '#070a0d',
                            panel: '#0e141a',
                            subtle: '#121921'
                        }
                    },
                    fontFamily: {
                        teko: ['Teko', 'sans-serif'],
                        rajdhani: ['Rajdhani', 'sans-serif'],
                        hud: ['Chakra Petch', 'monospace'],
                    }
                }
            }
        }
    </script>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        [x-cloak] { display: none !important; }
        html {
            touch-action: manipulation;
            -webkit-tap-highlight-color: transparent;
            text-size-adjust: 100%;
        }
        body {
            background-color: #06080b;
            color: #f1f5f9;
            font-family: 'Rajdhani', sans-serif;
            overflow-x: hidden;
        }

        /* UAE SWAT Inspired HUD Accents */
        .corner-bracket {
            position: relative;
        }
        .corner-bracket::before {
            content: '';
            position: absolute;
            top: -2px; left: -2px;
            width: 8px; height: 8px;
            border-top: 2px solid #f59e0b;
            border-left: 2px solid #f59e0b;
        }
        .corner-bracket::after {
            content: '';
            position: absolute;
            bottom: -2px; right: -2px;
            width: 8px; height: 8px;
            border-bottom: 2px solid #f59e0b;
            border-right: 2px solid #f59e0b;
        }

        /* Notched Tab for Telemetry Grid */
        .notched-label-container {
            position: relative;
        }
        .notched-label {
            position: absolute;
            bottom: -11px;
            left: 50%;
            transform: translateX(-50%);
            background: #06080b;
            padding: 2px 12px;
            border: 1px solid rgba(245, 158, 11, 0.35);
            border-radius: 9999px;
            font-size: 9px;
            letter-spacing: 0.12em;
            white-space: nowrap;
            text-transform: uppercase;
            font-weight: 700;
            color: #fbbf24;
        }

        /* Reticle Rotation Animation (28s Clockwise) */
        @keyframes spin-cw {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        .animate-reticle-slow {
            animation: spin-cw 28s linear infinite;
        }

        /* Subtle Target Pattern */
        .tactical-grid {
            background-image: radial-gradient(rgba(245, 158, 11, 0.15) 1px, transparent 1px);
            background-size: 24px 24px;
        }
    </style>
</head>
<body class="antialiased min-h-screen">

    <!-- Top Sticky Tactical Navigation Bar -->
    <nav class="sticky top-0 z-50 bg-[#090d12]/95 backdrop-blur-md border-b border-white/10 px-4 py-2.5">
        <div class="max-w-5xl mx-auto flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <a href="/" class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-full bg-white p-0.5 border border-amber-400 flex items-center justify-center shrink-0">
                        <img src="/assets/logo-bda.png" alt="Logo BDA" class="w-full h-full object-contain" onerror="this.style.display='none'">
                    </div>
                    <div>
                        <span class="font-teko text-xl tracking-wider text-white uppercase block leading-none">BDA 750</span>
                        <span class="font-hud text-[9px] text-amber-400 tracking-widest uppercase block leading-none">BRIGADE DIRAYA ADIKARA</span>
                    </div>
                </a>
            </div>

            <!-- Right Status & Direct Action -->
            <div class="flex items-center gap-2">
                <span class="hidden sm:inline-flex items-center gap-1.5 px-2.5 py-1 rounded bg-amber-500/10 border border-amber-500/25 text-[10px] font-hud text-amber-300">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-pulse"></span>
                    17-18 OKTOBER 2026
                </span>
                <a href="/daftar.php" class="px-3.5 py-1.5 rounded-lg bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-black font-bold font-rajdhani text-xs uppercase tracking-wider flex items-center gap-1 shadow-md transition">
                    <span>DAFTAR</span>
                    <span class="text-[10px]">↗</span>
                </a>
            </div>
        </div>
    </nav>

    <!-- ================= HERO SECTION ================= -->
    <header class="relative bg-gradient-to-b from-[#0e141b] via-[#080c10] to-[#06080b] border-b border-white/10 overflow-hidden">
        <!-- Background Grid & Glow -->
        <div class="absolute inset-0 tactical-grid opacity-30 pointer-events-none"></div>
        <div class="absolute top-1/3 left-1/2 -translate-x-1/2 w-96 h-96 bg-amber-500/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative max-w-4xl mx-auto px-4 pt-6 pb-10">
            
            <!-- Top Telemetry Tag & Anniversary Badge -->
            <div class="flex items-center justify-between gap-2 mb-6">
                <!-- Location Tag -->
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-lg bg-white/5 border border-white/10 text-[11px] font-hud text-slate-300">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span>
                    RESIMEN I PASUKAN PELOPOR &bull; KEDUNGHALANG
                </div>

                <!-- Floating Anniversary Badge (Cutout Style) -->
                <div class="bg-[#121921] border border-amber-500/40 rounded-xl px-3 py-1.5 shadow-lg flex items-center gap-2.5">
                    <div class="w-7 h-7 rounded-full bg-gradient-to-tr from-amber-600 to-amber-400 flex items-center justify-center text-black font-bold font-hud text-xs shadow-md border border-amber-200 shrink-0">
                        7TH
                    </div>
                    <div class="text-right">
                        <span class="font-teko text-sm text-amber-400 uppercase leading-none block">ANNIVERSARY BDA 750</span>
                        <span class="font-hud text-[9px] text-slate-400 block leading-none">2019 — 2026</span>
                    </div>
                </div>
            </div>

            <!-- Corner-Bracket Category Label -->
            <div class="text-center mb-1">
                <span class="font-hud text-[11px] text-amber-400 tracking-widest uppercase">
                  ⌜ KEJUARAAN MENEMBAK PISTOL 2026 ⌝
                </span>
            </div>

            <!-- Stencil Headline (Teko Display Font) -->
            <div class="text-center mb-5">
                <h1 class="font-teko text-5xl sm:text-6xl md:text-7xl uppercase tracking-tight text-white leading-[0.9]">
                    BDA SHOOTING<br>
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-amber-400 via-amber-300 to-amber-500">
                        CHAMPIONSHIP 2026
                    </span>
                </h1>
                <p class="font-rajdhani text-sm sm:text-base text-slate-300 max-w-xl mx-auto mt-2 leading-relaxed">
                    Pistol Presisi 20 Meter &amp; Dueling Plat Speed dalam rangka memperingati Anniversary Letting BDA 750 ke-7.
                </p>
            </div>

            <!-- Rotating Reticle & White Canvas BDA Logo -->
            <div class="flex justify-center items-center my-6">
                <div class="relative w-52 h-52 sm:w-60 sm:h-60 flex items-center justify-center">
                    
                    <!-- Outermost Rotating Reticle Ring with Degree Coordinates -->
                    <svg class="absolute inset-0 w-full h-full text-amber-400/80 animate-reticle-slow pointer-events-none" viewBox="0 0 200 200" fill="none">
                        <circle cx="100" cy="100" r="95" stroke="currentColor" stroke-width="1.2" stroke-dasharray="4 8" />
                        <circle cx="100" cy="100" r="90" stroke="currentColor" stroke-width="0.8" stroke-dasharray="1 10" opacity="0.6" />
                        <!-- Degree Ticks -->
                        <line x1="100" y1="2" x2="100" y2="10" stroke="currentColor" stroke-width="2" />
                        <line x1="100" y1="190" x2="100" y2="198" stroke="currentColor" stroke-width="2" />
                        <line x1="2" y1="100" x2="10" y2="100" stroke="currentColor" stroke-width="2" />
                        <line x1="190" y1="100" x2="198" y2="100" stroke="currentColor" stroke-width="2" />
                        <!-- Coordinates -->
                        <text x="100" y="16" fill="currentColor" font-size="5" text-anchor="middle" font-family="monospace">000°</text>
                        <text x="186" y="102" fill="currentColor" font-size="5" text-anchor="middle" font-family="monospace">090°</text>
                        <text x="100" y="188" fill="currentColor" font-size="5" text-anchor="middle" font-family="monospace">180°</text>
                        <text x="14" y="102" fill="currentColor" font-size="5" text-anchor="middle" font-family="monospace">270°</text>
                    </svg>

                    <!-- Inner Warm Glow -->
                    <div class="absolute inset-4 rounded-full bg-gradient-to-tr from-amber-500/25 to-copper-500/20 blur-xl pointer-events-none"></div>

                    <!-- White Canvas Container for Official Logo -->
                    <div class="relative w-36 h-36 sm:w-44 sm:h-44 rounded-full bg-white shadow-2xl p-4 flex items-center justify-center border-4 border-amber-400 ring-4 ring-white/10">
                        <img src="/assets/logo-championship.png" alt="Logo BDA Shooting Championship" class="w-full h-full object-contain drop-shadow-md select-none" onerror="this.src='/assets/logo-bda.png'">
                    </div>
                </div>
            </div>

            <!-- Primary CTAs -->
            <div class="flex items-center justify-center gap-3 sm:gap-4 mb-6">
                <a href="/daftar.php" class="px-6 sm:px-8 py-3 rounded-full bg-gradient-to-r from-amber-500 via-amber-400 to-amber-500 hover:from-amber-400 hover:to-amber-300 text-black font-bold font-rajdhani text-sm sm:text-base uppercase tracking-wider flex items-center gap-2 shadow-lg shadow-amber-500/25 transition transform active:scale-95">
                    <span>DAFTAR SEKARANG</span>
                    <span class="w-5 h-5 rounded-md bg-black/20 flex items-center justify-center text-xs">↗</span>
                </a>
                <a href="/live-score.php" class="px-5 sm:px-7 py-3 rounded-full bg-[#121921] hover:bg-[#1a232e] text-white border border-white/20 hover:border-amber-400/50 font-bold font-rajdhani text-sm sm:text-base uppercase tracking-wider flex items-center gap-2 transition transform active:scale-95">
                    <span class="w-2 h-2 rounded-full bg-amber-400 animate-pulse"></span>
                    <span>LIVE SKOR</span>
                </a>
            </div>

            <!-- Tactical Countdown Panel (Framed Box with Corner Brackets) -->
            <div class="bg-[#0b1016]/95 border border-white/15 rounded-2xl p-4 max-w-sm mx-auto shadow-2xl relative corner-bracket" x-data="tacticalCountdown()" x-init="start()">
                <p class="font-teko text-center text-sm text-slate-400 uppercase tracking-widest leading-none mb-2.5">
                    COUNTDOWN MENUJU HARI-H (17 OKTOBER 2026)
                </p>
                <div class="grid grid-cols-4 gap-2 text-center">
                    <div class="bg-[#121a22] border border-white/10 rounded-xl py-2 px-1">
                        <span class="font-teko text-2xl sm:text-3xl text-amber-400 font-bold leading-none block" x-text="days">0</span>
                        <span class="font-hud text-[9px] text-slate-400 block mt-0.5">HARI</span>
                    </div>
                    <div class="bg-[#121a22] border border-white/10 rounded-xl py-2 px-1">
                        <span class="font-teko text-2xl sm:text-3xl text-amber-400 font-bold leading-none block" x-text="hours">0</span>
                        <span class="font-hud text-[9px] text-slate-400 block mt-0.5">JAM</span>
                    </div>
                    <div class="bg-[#121a22] border border-white/10 rounded-xl py-2 px-1">
                        <span class="font-teko text-2xl sm:text-3xl text-amber-400 font-bold leading-none block" x-text="minutes">0</span>
                        <span class="font-hud text-[9px] text-slate-400 block mt-0.5">MENIT</span>
                    </div>
                    <div class="bg-[#121a22] border border-white/10 rounded-xl py-2 px-1">
                        <span class="font-teko text-2xl sm:text-3xl text-amber-300 font-bold leading-none block" x-text="seconds">0</span>
                        <span class="font-hud text-[9px] text-slate-400 block mt-0.5">DETIK</span>
                    </div>
                </div>
            </div>

        </div>
    </header>

    <!-- ================= EVENT TELEMETRY HUD (2x2 GRID DENGAN NOTCHED TAB) ================= -->
    <section class="py-8 px-4 bg-[#06080b] border-b border-white/10">
        <div class="max-w-4xl mx-auto">
            <div class="text-center mb-6">
                <span class="font-hud text-[11px] text-amber-400 uppercase tracking-widest">
                    ⌜ EVENT TELEMETRY ⌝
                </span>
                <h2 class="font-teko text-3xl uppercase tracking-wider text-white">METRIK RESMI KEJUARAAN</h2>
            </div>

            <!-- Grid 2x2 di Mobile, 4 Kolom di Desktop -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 sm:gap-4 mb-2">
                
                <!-- Metrik 1: Kategori -->
                <div class="bg-[#0e141a] border border-white/10 rounded-2xl p-4 sm:p-5 text-center notched-label-container shadow-lg">
                    <div class="w-8 h-8 mx-auto mb-2 text-amber-400 flex items-center justify-center">
                        <svg class="w-7 h-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 2v20M2 12h20"/></svg>
                    </div>
                    <div class="font-teko text-3xl sm:text-4xl font-bold text-white leading-none">2</div>
                    <div class="notched-label">KATEGORI TANDING</div>
                </div>

                <!-- Metrik 2: Jarak Tembak -->
                <div class="bg-[#0e141a] border border-white/10 rounded-2xl p-4 sm:p-5 text-center notched-label-container shadow-lg">
                    <div class="w-8 h-8 mx-auto mb-2 text-amber-400 flex items-center justify-center">
                        <svg class="w-7 h-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m18 15-6-6-6 6"/></svg>
                    </div>
                    <div class="font-teko text-3xl sm:text-4xl font-bold text-white leading-none">15M &amp; 20M</div>
                    <div class="notched-label">JARAK TEMBAK</div>
                </div>

                <!-- Metrik 3: 7 Tahun BDA 750 -->
                <div class="bg-[#0e141a] border border-white/10 rounded-2xl p-4 sm:p-5 text-center notched-label-container shadow-lg mt-2 sm:mt-0">
                    <div class="w-8 h-8 mx-auto mb-2 text-amber-400 flex items-center justify-center">
                        <svg class="w-7 h-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                    </div>
                    <div class="font-teko text-3xl sm:text-4xl font-bold text-white leading-none">7 TAHUN</div>
                    <div class="notched-label">LETTING BDA 750</div>
                </div>

                <!-- Metrik 4: Total Hadiah -->
                <div class="bg-[#0e141a] border border-white/10 rounded-2xl p-4 sm:p-5 text-center notched-label-container shadow-lg mt-2 sm:mt-0">
                    <div class="w-8 h-8 mx-auto mb-2 text-amber-400 flex items-center justify-center">
                        <svg class="w-7 h-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9H4.5a2.5 2.5 0 0 1 0-5H6"/><path d="M18 9h1.5a2.5 2.5 0 0 0 0-5H18"/><path d="M4 22h16"/><path d="M10 14.66V17c0 .55-.47.98-.97 1.21C7.85 18.75 7 20.24 7 22"/><path d="M14 14.66V17c0 .55.47.98.97 1.21C16.15 18.75 17 20.24 17 22"/><path d="M18 2H6v7a6 6 0 0 0 12 0V2Z"/></svg>
                    </div>
                    <div class="font-teko text-3xl sm:text-4xl font-bold text-white leading-none">BELASAN JUTA</div>
                    <div class="notched-label">TOTAL HADIAH</div>
                </div>

            </div>
        </div>
    </section>

    <!-- ================= QUICK TACTICAL LINKS (NAVIGASI CEPAT) ================= -->
    <section class="py-6 px-4 bg-[#0a0f14] border-b border-white/10">
        <div class="max-w-4xl mx-auto space-y-2.5">
            <a href="#juknis" class="flex items-center justify-between p-3.5 rounded-xl bg-[#121921] hover:bg-[#18222d] border border-white/10 hover:border-amber-500/40 transition group">
                <div class="flex items-center gap-3">
                    <span class="w-7 h-7 rounded-lg bg-amber-500/10 border border-amber-500/30 flex items-center justify-center text-amber-400 font-hud text-xs font-bold">01</span>
                    <div>
                        <p class="font-teko text-lg text-white uppercase leading-none">JUKNIS RESMI KEJUARAAN</p>
                        <p class="text-xs text-slate-400">Aturan senjata, amunisi standar 9mm, seragam &amp; sistem nilai</p>
                    </div>
                </div>
                <span class="w-7 h-7 rounded-md bg-white/10 flex items-center justify-center text-xs group-hover:bg-amber-500 group-hover:text-black transition">↗</span>
            </a>

            <a href="https://maps.google.com/?q=Lapangan+Tembak+Resimen+I+Pasukan+Pelopor+Kedunghalang+Bogor" target="_blank" rel="noopener" class="flex items-center justify-between p-3.5 rounded-xl bg-[#121921] hover:bg-[#18222d] border border-white/10 hover:border-amber-500/40 transition group">
                <div class="flex items-center gap-3">
                    <span class="w-7 h-7 rounded-lg bg-amber-500/10 border border-amber-500/30 flex items-center justify-center text-amber-400 font-hud text-xs font-bold">02</span>
                    <div>
                        <p class="font-teko text-lg text-white uppercase leading-none">LOKASI LAPANGAN TEMBAK (GOOGLE MAPS)</p>
                        <p class="text-xs text-slate-400">Lapangan Tembak Resimen I Pasukan Pelopor, Kedunghalang, Bogor</p>
                    </div>
                </div>
                <span class="w-7 h-7 rounded-md bg-white/10 flex items-center justify-center text-xs group-hover:bg-amber-500 group-hover:text-black transition">↗</span>
            </a>

            <a href="#seragam" class="flex items-center justify-between p-3.5 rounded-xl bg-[#121921] hover:bg-[#18222d] border border-white/10 hover:border-amber-500/40 transition group">
                <div class="flex items-center gap-3">
                    <span class="w-7 h-7 rounded-lg bg-amber-500/10 border border-amber-500/30 flex items-center justify-center text-amber-400 font-hud text-xs font-bold">03</span>
                    <div>
                        <p class="font-teko text-lg text-white uppercase leading-none">KETENTUAN SERAGAM PERTANDINGAN</p>
                        <p class="text-xs text-slate-400">PDO atau Tactical, bersepatu (tanpa singkatan panjang)</p>
                    </div>
                </div>
                <span class="w-7 h-7 rounded-md bg-white/10 flex items-center justify-center text-xs group-hover:bg-amber-500 group-hover:text-black transition">↗</span>
            </a>
        </div>
    </section>

    <!-- ================= STAGE CHALLENGES DOSSIER ================= -->
    <section class="py-10 px-4 bg-[#06080b] border-b border-white/10" id="stages">
        <div class="max-w-4xl mx-auto">
            <div class="text-center mb-8">
                <span class="font-hud text-[11px] text-amber-400 uppercase tracking-widest">
                    ⌜ COMPETITION STAGES ⌝
                </span>
                <h2 class="font-teko text-4xl uppercase tracking-wider text-white">STAGE &amp; KATEGORI PERTANDINGAN</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                
                <!-- STAGE 01: PISTOL PRESISI 20M -->
                <div class="relative bg-[#0e141a] border border-white/15 rounded-2xl overflow-hidden shadow-xl flex flex-col justify-between">
                    <div>
                        <div class="bg-gradient-to-r from-amber-500/20 via-transparent to-transparent p-3.5 border-b border-white/10 flex items-center justify-between">
                            <span class="font-hud text-xs text-amber-400 font-bold">STAGE 01 // PRECISION</span>
                            <span class="font-hud text-[11px] px-2.5 py-0.5 rounded bg-white/10 text-slate-300">20 METER</span>
                        </div>
                        <div class="p-5">
                            <h3 class="font-teko text-3xl uppercase text-white leading-none mb-2">PISTOL PRESISI 20 METER</h3>
                            <p class="text-sm text-slate-300 mb-4 leading-relaxed">
                                Uji ketepatan dan akurasi tembakan jarak 20 meter pada sasaran presisi. Total 13 butir (3 percobaan + 10 penilaian) posisi berdiri dua tangan.
                            </p>
                            
                            <div class="grid grid-cols-2 gap-2 font-hud text-xs text-slate-300 mb-4 bg-[#06080b] p-3 rounded-xl border border-white/5">
                                <div>• Jarak: <strong class="text-white">20 Meter</strong></div>
                                <div>• Kaliber: <strong class="text-white">9x19 mm</strong></div>
                                <div>• Waktu: <strong class="text-white">1 &amp; 3 Menit</strong></div>
                                <div>• Sikap: <strong class="text-white">Berdiri 2 Tangan</strong></div>
                                <div>• Biaya Polri/Umum: <strong class="text-amber-400">Rp 200.000</strong></div>
                                <div>• Khusus Letting: <strong class="text-amber-400">Rp 100.000</strong></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="p-5 pt-0">
                        <a href="/daftar.php?kategori=presisi" class="w-full py-2.5 rounded-xl bg-amber-500/10 hover:bg-amber-500/20 border border-amber-500/40 text-amber-400 font-bold text-sm uppercase flex items-center justify-center gap-2 transition">
                            <span>DAFTAR STAGE PRESISI</span>
                            <span>↗</span>
                        </a>
                    </div>
                </div>

                <!-- STAGE 02: DUELING PLAT SPEED 15M -->
                <div class="relative bg-[#0e141a] border border-amber-500/30 rounded-2xl overflow-hidden shadow-xl flex flex-col justify-between">
                    <div>
                        <div class="bg-gradient-to-r from-amber-500/30 via-transparent to-transparent p-3.5 border-b border-white/10 flex items-center justify-between">
                            <span class="font-hud text-xs text-amber-400 font-bold">STAGE 02 // SPEED DUEL</span>
                            <span class="font-hud text-[11px] px-2.5 py-0.5 rounded bg-white/10 text-slate-300">15 METER</span>
                        </div>
                        <div class="p-5">
                            <h3 class="font-teko text-3xl uppercase text-white leading-none mb-2">DUELING PLAT 15 METER</h3>
                            <p class="text-sm text-slate-300 mb-4 leading-relaxed">
                                Duel adu cepat dan tepat menjatuhkan 5 plat baja sasaran + 1 stop popper secara head-to-head sistem gugur sesuai bagan pertandingan.
                            </p>
                            
                            <div class="grid grid-cols-2 gap-2 font-hud text-xs text-slate-300 mb-4 bg-[#06080b] p-3 rounded-xl border border-white/5">
                                <div>• Jarak: <strong class="text-white">15 Meter</strong></div>
                                <div>• Target: <strong class="text-white">5 Plat + Popper</strong></div>
                                <div>• Amunisi: <strong class="text-white">10 Butir</strong></div>
                                <div>• Start: <strong class="text-white">Lari 5m ke Meja</strong></div>
                                <div>• Biaya Polri/Umum: <strong class="text-amber-400">Rp 200.000</strong></div>
                                <div>• Khusus Letting: <strong class="text-amber-400">Rp 100.000</strong></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="p-5 pt-0">
                        <a href="/daftar.php?kategori=dueling" class="w-full py-2.5 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-black font-bold text-sm uppercase flex items-center justify-center gap-2 transition shadow-md">
                            <span>DAFTAR STAGE DUELING</span>
                            <span>↗</span>
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- ================= JUKNIS RESMI ACCORDION ================= -->
    <section class="py-10 px-4 bg-[#090d12] border-b border-white/10" id="juknis" x-data="{ open: 1 }">
        <div class="max-w-4xl mx-auto">
            <div class="text-center mb-8">
                <span class="font-hud text-[11px] text-amber-400 uppercase tracking-widest">
                    ⌜ TECHNICAL DIRECTIVES ⌝
                </span>
                <h2 class="font-teko text-4xl uppercase tracking-wider text-white">PETUNJUK TEKNIS RESMI (JUKNIS)</h2>
            </div>

            <div class="space-y-3">
                
                <!-- 1. Peserta & Berkas -->
                <div class="border border-white/15 rounded-xl overflow-hidden bg-[#0e141a]">
                    <button @click="open = open === 1 ? null : 1" class="w-full flex items-center justify-between p-4 text-left font-bold hover:bg-white/5 transition text-base">
                        <span class="flex items-center gap-2.5 text-white">
                            <span class="w-6 h-6 rounded bg-amber-500/10 text-amber-400 font-hud text-xs flex items-center justify-center">1</span>
                            Persyaratan Peserta &amp; Berkas
                        </span>
                        <span class="text-slate-400 text-sm font-hud" x-text="open === 1 ? '▲' : '▼'"></span>
                    </button>
                    <div x-show="open === 1" x-transition class="px-5 pb-4 text-sm text-slate-300 space-y-2 border-t border-white/10 pt-3">
                        <p>&bull; Terbuka untuk seluruh anggota POLRI dan Letting BDA 750 se-Korbrimob Polri.</p>
                        <p>&bull; Wajib mengunggah <strong>Foto KTA (Kartu Tanda Anggota) Polri</strong> aktif pada saat pendaftaran online.</p>
                        <p>&bull; Mengisi formulir resmi dengan mencantumkan <strong>NRP</strong> dan <strong>Kesatuan / Club</strong>.</p>
                        <p>&bull; Menyelesaikan administrasi pendaftaran sesuai kategori yang diikuti.</p>
                    </div>
                </div>

                <!-- 2. Senjata & Amunisi -->
                <div class="border border-white/15 rounded-xl overflow-hidden bg-[#0e141a]">
                    <button @click="open = open === 2 ? null : 2" class="w-full flex items-center justify-between p-4 text-left font-bold hover:bg-white/5 transition text-base">
                        <span class="flex items-center gap-2.5 text-white">
                            <span class="w-6 h-6 rounded bg-amber-500/10 text-amber-400 font-hud text-xs flex items-center justify-center">2</span>
                            Ketentuan Senjata &amp; Amunisi
                        </span>
                        <span class="text-slate-400 text-sm font-hud" x-text="open === 2 ? '▲' : '▼'"></span>
                    </button>
                    <div x-show="open === 2" x-transition class="px-5 pb-4 text-sm text-slate-300 space-y-2 border-t border-white/10 pt-3">
                        <p>&bull; Senjata berupa <strong>Pistol kaliber 9x19mm organik dinas satuan</strong> dalam kondisi laik tembak.</p>
                        <p>&bull; Pisir standar pabrikan (non-optik), panjang laras senjata maksimal 5 inci.</p>
                        <p>&bull; Amunisi disediakan oleh masing-masing peserta / kontingen.</p>
                        <p>&bull; Pemeriksaan fisik senjata (<em>gun check</em>) dilakukan panitia sebelum peserta bertanding.</p>
                    </div>
                </div>

                <!-- 3. Seragam & Perlengkapan Wajib -->
                <div class="border border-white/15 rounded-xl overflow-hidden bg-[#0e141a]" id="seragam">
                    <button @click="open = open === 3 ? null : 3" class="w-full flex items-center justify-between p-4 text-left font-bold hover:bg-white/5 transition text-base">
                        <span class="flex items-center gap-2.5 text-white">
                            <span class="w-6 h-6 rounded bg-amber-500/10 text-amber-400 font-hud text-xs flex items-center justify-center">3</span>
                            Pakaian &amp; Perlengkapan Wajib
                        </span>
                        <span class="text-slate-400 text-sm font-hud" x-text="open === 3 ? '▲' : '▼'"></span>
                    </button>
                    <div x-show="open === 3" x-transition class="px-5 pb-4 text-sm text-slate-300 space-y-2 border-t border-white/10 pt-3">
                        <p>&bull; Seragam pertandingan: <strong>PDO atau Tactical, bersepatu</strong>.</p>
                        <p>&bull; Perlengkapan sabuk / belt dan holster standar yang aman.</p>
                        <p>&bull; Wajib mengenakan kacamata pelindung (<em>safety glasses</em>) dan pelindung telinga (<em>earmuff / earplug</em>) selama berada di lajur tembak.</p>
                    </div>
                </div>

                <!-- 4. Penilaian & Sistem Gugur -->
                <div class="border border-white/15 rounded-xl overflow-hidden bg-[#0e141a]">
                    <button @click="open = open === 4 ? null : 4" class="w-full flex items-center justify-between p-4 text-left font-bold hover:bg-white/5 transition text-base">
                        <span class="flex items-center gap-2.5 text-white">
                            <span class="w-6 h-6 rounded bg-amber-500/10 text-amber-400 font-hud text-xs flex items-center justify-center">4</span>
                            Sistem Pertandingan &amp; Penilaian
                        </span>
                        <span class="text-slate-400 text-sm font-hud" x-text="open === 4 ? '▲' : '▼'"></span>
                    </button>
                    <div x-show="open === 4" x-transition class="px-5 pb-4 text-sm text-slate-300 space-y-2 border-t border-white/10 pt-3">
                        <p><strong>Pistol Presisi 20M:</strong> Total 13 butir (Magasen 1: 3 butir percobaan 1 menit; Magasen 2: 10 butir penilaian 3 menit). Sikap berdiri 2 tangan. Nilai maksimal 100,10. Jika draw, dihitung dari jumlah Inner X terbanyak; jika masih sama dilakukan shoot-off 5 butir (1 menit).</p>
                        <p><strong>Dueling Plat 15M:</strong> Sasaran 5 plat bulat + 1 stop popper. Amunisi 10 butir. Penembak duduk di kursi 5m di belakang meja tembak, berlari ke meja setelah aba-aba. Sistem gugur langsung head-to-head. <strong>Menjatuhkan stop popper sebelum 5 plat bulat roboh dinyatakan Diskualifikasi (DQ)</strong>.</p>
                    </div>
                </div>

                <!-- 5. Biaya & Rekening Resmi -->
                <div class="border border-amber-500/40 rounded-xl overflow-hidden bg-[#0e141a]">
                    <button @click="open = open === 5 ? null : 5" class="w-full flex items-center justify-between p-4 text-left font-bold hover:bg-white/5 transition text-base">
                        <span class="flex items-center gap-2.5 text-amber-400">
                            <span class="w-6 h-6 rounded bg-amber-500/20 text-amber-300 font-hud text-xs flex items-center justify-center">5</span>
                            Biaya Pendaftaran &amp; Rekening Pembayaran
                        </span>
                        <span class="text-slate-400 text-sm font-hud" x-text="open === 5 ? '▲' : '▼'"></span>
                    </button>
                    <div x-show="open === 5" x-transition class="px-5 pb-4 text-sm text-slate-300 space-y-2.5 border-t border-white/10 pt-3">
                        <p>&bull; Anggota Polri / Umum: <strong>Rp 200.000 / Kategori</strong></p>
                        <p>&bull; Khusus Anggota Letting BDA 750: <strong>Rp 100.000 / Kategori</strong></p>
                        <div class="mt-2 p-3 rounded-lg bg-[#06080b] border border-amber-500/30">
                            <p class="text-xs text-slate-400 font-hud uppercase">Rekening Resmi Pembayaran Panitia:</p>
                            <p class="font-hud text-base font-bold text-amber-400">Bank <?= BANK_NAME ?>: <?= BANK_ACCOUNT ?></p>
                            <p class="text-xs text-white">a.n. <?= BANK_HOLDER ?></p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- ================= KONTAK PANITIA PELAKSANA ================= -->
    <section class="py-10 px-4 bg-[#06080b] border-b border-white/10" id="kontak">
        <div class="max-w-4xl mx-auto">
            <div class="text-center mb-8">
                <span class="font-hud text-[11px] text-amber-400 uppercase tracking-widest">
                    ⌜ DIRECT CONTACT ⌝
                </span>
                <h2 class="font-teko text-4xl uppercase tracking-wider text-white">KONTAK RESMI PANITIA</h2>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3">
                
                <a href="https://wa.me/6282134651503" target="_blank" rel="noopener" class="bg-[#0e141a] hover:bg-[#151e27] border border-white/10 hover:border-amber-500/40 p-4 rounded-xl transition group">
                    <p class="text-[11px] font-hud text-amber-400 uppercase">Pendaftaran</p>
                    <p class="font-teko text-xl text-white">Briptu Zyaldi</p>
                    <p class="text-xs text-slate-400 font-hud">0821-3465-1503</p>
                    <span class="mt-2 inline-block text-[11px] text-amber-400 font-bold">Chat WhatsApp ↗</span>
                </a>

                <a href="https://wa.me/6285775015786" target="_blank" rel="noopener" class="bg-[#0e141a] hover:bg-[#151e27] border border-white/10 hover:border-amber-500/40 p-4 rounded-xl transition group">
                    <p class="text-[11px] font-hud text-amber-400 uppercase">Pendaftaran</p>
                    <p class="font-teko text-xl text-white">Briptu Rully</p>
                    <p class="text-xs text-slate-400 font-hud">0857-7501-5786</p>
                    <span class="mt-2 inline-block text-[11px] text-amber-400 font-bold">Chat WhatsApp ↗</span>
                </a>

                <a href="https://wa.me/6285283525761" target="_blank" rel="noopener" class="bg-[#0e141a] hover:bg-[#151e27] border border-white/10 hover:border-amber-500/40 p-4 rounded-xl transition group">
                    <p class="text-[11px] font-hud text-amber-400 uppercase">Materi &amp; Teknis</p>
                    <p class="font-teko text-xl text-white">Briptu Ady</p>
                    <p class="text-xs text-slate-400 font-hud">0852-8352-5761</p>
                    <span class="mt-2 inline-block text-[11px] text-amber-400 font-bold">Chat WhatsApp ↗</span>
                </a>

                <a href="https://wa.me/6285272377704" target="_blank" rel="noopener" class="bg-[#0e141a] hover:bg-[#151e27] border border-white/10 hover:border-amber-500/40 p-4 rounded-xl transition group">
                    <p class="text-[11px] font-hud text-amber-400 uppercase">Ketua Pelaksana</p>
                    <p class="font-teko text-xl text-white">Briptu Huges</p>
                    <p class="text-xs text-slate-400 font-hud">0852-7237-7704</p>
                    <span class="mt-2 inline-block text-[11px] text-amber-400 font-bold">Chat WhatsApp ↗</span>
                </a>

            </div>
        </div>
    </section>

    <!-- ================= FOOTER ================= -->
    <footer class="py-8 px-4 bg-[#040608] text-center border-t border-white/10">
        <div class="max-w-4xl mx-auto space-y-4">
            
            <!-- Dual Insignia -->
            <div class="flex items-center justify-center gap-4">
                <div class="w-10 h-10 rounded-full bg-white p-1 border border-amber-400 shadow-md">
                    <img src="/assets/logo-bda.png" alt="BDA 750" class="w-full h-full object-contain">
                </div>
                <span class="text-slate-600 font-hud text-xs">//</span>
                <span class="font-hud text-xs text-slate-400 uppercase tracking-widest">RESIMEN I PASUKAN PELOPOR</span>
            </div>

            <p class="text-xs text-slate-500 font-rajdhani">
                &copy; 2026 Panitia Pelaksana BDA Shooting Championship. Letting BDA 750 se-Korbrimob Polri.
            </p>

            <div class="pt-2">
                <a href="/" class="inline-flex items-center gap-1.5 px-3 py-1 rounded bg-white/5 hover:bg-white/10 border border-white/10 text-xs font-hud text-slate-400 hover:text-white transition">
                    <span>← KEMBALI KE BERANDA UTAMA</span>
                </a>
            </div>
        </div>
    </footer>

    <!-- Alpine Countdown Script -->
    <script>
        function tacticalCountdown() {
            return {
                targetDate: new Date('2026-10-17T08:00:00+07:00').getTime(),
                days: 0,
                hours: 0,
                minutes: 0,
                seconds: 0,
                start() {
                    const update = () => {
                        const now = new Date().getTime();
                        const diff = this.targetDate - now;
                        if (diff > 0) {
                            this.days = Math.floor(diff / (1000 * 60 * 60 * 24));
                            this.hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                            this.minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                            this.seconds = Math.floor((diff % (1000 * 60)) / 1000);
                        } else {
                            this.days = 0;
                            this.hours = 0;
                            this.minutes = 0;
                            this.seconds = 0;
                        }
                    };
                    update();
                    setInterval(update, 1000);
                }
            }
        }
    </script>
</body>
</html>
