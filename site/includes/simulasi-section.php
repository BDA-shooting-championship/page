<!-- ================= INTERACTIVE TARGET SIMULATOR (CLEAN LIVE SCORE & ARENA) ================= -->
<section id="simulasi-lesan" class="py-8 sm:py-12 bg-gray-950 text-gray-100 relative overflow-hidden border-t border-b border-gray-800">
    <!-- Ambient Background Glow -->
    <div class="absolute inset-0 pointer-events-none opacity-20">
        <div class="absolute top-1/2 left-1/4 -translate-y-1/2 w-96 h-96 bg-copper-600/30 rounded-full blur-3xl"></div>
        <div class="absolute top-1/2 right-1/4 -translate-y-1/2 w-96 h-96 bg-amber-500/20 rounded-full blur-3xl"></div>
    </div>

    <div class="max-w-4xl mx-auto px-3 sm:px-6 lg:px-8 relative z-10 flex flex-col items-center">
        
        <!-- Section Header (Clean & Minimalist) -->
        <div class="text-center mb-5">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-copper-950 text-copper-300 border border-copper-700/60 uppercase tracking-widest shadow-sm">
                <i data-lucide="crosshair" class="w-3.5 h-3.5 text-amber-400"></i> Game Simulasi Presisi 25M
            </span>
            <h2 class="font-display text-2xl sm:text-3xl font-bold mt-1.5 text-white tracking-wide uppercase" data-scramble>
                Simulasi Lesan Presisi &bull; Seri 10 Butir
            </h2>
        </div>

        <div class="w-full max-w-[500px] flex flex-col items-center">
            
            <!-- ================= TOP: LIVE SCOREBOARD & TIMER (DI ATAS LESAN) ================= -->
            <div class="w-full bg-gray-900/95 border border-gray-800 rounded-2xl p-3 sm:p-4 shadow-xl mb-3">
                <!-- Header: Timer, Status, Total Skor, Inner X -->
                <div class="flex items-center justify-between pb-2.5 mb-2.5 border-b border-gray-800/80 text-xs font-mono">
                    <div class="flex items-center gap-2">
                        <!-- Countdown Timer 3 Menit -->
                        <span id="matchTimerBadge" class="flex items-center gap-1.5 px-3 py-1 rounded-xl bg-gray-950 border border-gray-700 text-amber-400 font-bold text-sm sm:text-base font-mono shadow-inner">
                            <i data-lucide="clock" class="w-3.5 h-3.5 text-amber-400"></i>
                            <span id="matchTimerText">03:00</span>
                        </span>
                        <span id="sessionStatusPill" class="text-[10px] font-bold px-2.5 py-1 rounded-lg bg-gray-800 text-gray-300 border border-gray-700 uppercase tracking-wider">
                            SIAP MULAI
                        </span>
                    </div>

                    <div class="flex items-center gap-3">
                        <div class="text-right">
                            <span class="text-[9px] text-gray-400 block uppercase tracking-wider">Total Skor</span>
                            <span id="topTotalScore" class="font-display font-bold text-base sm:text-lg text-copper-400 leading-none">0</span>
                        </div>
                        <div class="text-right pl-3 border-l border-gray-800">
                            <span class="text-[9px] text-gray-400 block uppercase tracking-wider">Inner X</span>
                            <span id="topInnerX" class="font-display font-bold text-base sm:text-lg text-amber-400 leading-none">0X</span>
                        </div>
                        <button id="soundToggleBtn" type="button" class="ml-1 p-1.5 rounded-xl bg-gray-800 hover:bg-gray-700 border border-gray-700 text-copper-400 transition cursor-pointer" title="Toggle Suara">
                            <i data-lucide="volume-2" id="soundIcon" class="w-4 h-4"></i>
                        </button>
                    </div>
                </div>

                <!-- 10-Shot Live Scoreboard Table -->
                <div class="overflow-x-auto">
                    <table class="w-full text-center border-collapse">
                        <thead>
                            <tr class="text-[10px] font-mono text-gray-400 uppercase border-b border-gray-800/80">
                                <th class="py-1 px-0.5 font-semibold">#1</th>
                                <th class="py-1 px-0.5 font-semibold">#2</th>
                                <th class="py-1 px-0.5 font-semibold">#3</th>
                                <th class="py-1 px-0.5 font-semibold">#4</th>
                                <th class="py-1 px-0.5 font-semibold">#5</th>
                                <th class="py-1 px-0.5 font-semibold">#6</th>
                                <th class="py-1 px-0.5 font-semibold">#7</th>
                                <th class="py-1 px-0.5 font-semibold">#8</th>
                                <th class="py-1 px-0.5 font-semibold">#9</th>
                                <th class="py-1 px-0.5 font-semibold">#10</th>
                                <th class="py-1 px-1.5 font-bold text-copper-400 bg-gray-950/60 rounded-t">TOTAL</th>
                                <th class="py-1 px-1.5 font-bold text-amber-400 bg-gray-950/60 rounded-t">X</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="font-mono text-xs font-bold">
                                <td id="shotCell1" class="py-1 px-0.5"><span class="block py-1 rounded bg-gray-950/80 border border-gray-800 text-gray-500">-</span></td>
                                <td id="shotCell2" class="py-1 px-0.5"><span class="block py-1 rounded bg-gray-950/80 border border-gray-800 text-gray-500">-</span></td>
                                <td id="shotCell3" class="py-1 px-0.5"><span class="block py-1 rounded bg-gray-950/80 border border-gray-800 text-gray-500">-</span></td>
                                <td id="shotCell4" class="py-1 px-0.5"><span class="block py-1 rounded bg-gray-950/80 border border-gray-800 text-gray-500">-</span></td>
                                <td id="shotCell5" class="py-1 px-0.5"><span class="block py-1 rounded bg-gray-950/80 border border-gray-800 text-gray-500">-</span></td>
                                <td id="shotCell6" class="py-1 px-0.5"><span class="block py-1 rounded bg-gray-950/80 border border-gray-800 text-gray-500">-</span></td>
                                <td id="shotCell7" class="py-1 px-0.5"><span class="block py-1 rounded bg-gray-950/80 border border-gray-800 text-gray-500">-</span></td>
                                <td id="shotCell8" class="py-1 px-0.5"><span class="block py-1 rounded bg-gray-950/80 border border-gray-800 text-gray-500">-</span></td>
                                <td id="shotCell9" class="py-1 px-0.5"><span class="block py-1 rounded bg-gray-950/80 border border-gray-800 text-gray-500">-</span></td>
                                <td id="shotCell10" class="py-1 px-0.5"><span class="block py-1 rounded bg-gray-950/80 border border-gray-800 text-gray-500">-</span></td>
                                <td class="py-1 px-1 bg-gray-950/80 font-display font-extrabold text-copper-400 text-sm border-x border-gray-800" id="tableTotalNilai">0</td>
                                <td class="py-1 px-1 bg-gray-950/80 font-display font-extrabold text-amber-400 text-sm rounded-b" id="tableTotalX">0</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- ================= CENTER: TARGET CANVAS & OVERLAYS ================= -->
            <div id="rangeCard" class="relative w-full aspect-square rounded-2xl bg-stone-900 border-4 border-stone-800 shadow-2xl overflow-hidden cursor-none flex items-center justify-center select-none group touch-none">
                
                <!-- Muzzle Flash Overlay -->
                <div id="muzzleFlash" class="absolute inset-0 bg-amber-100 pointer-events-none z-30 opacity-0 transition-opacity"></div>

                <!-- Target Canvas (Paper & Bullet Holes) -->
                <canvas id="targetCanvas" class="w-full h-full block z-10"></canvas>

                <!-- Sights Canvas (Rear Sight + Front Sight + Recoil Kick) -->
                <canvas id="sightsCanvas" class="absolute inset-0 w-full h-full pointer-events-none z-20"></canvas>

                <!-- Floating Feedback Toast in Canvas Center -->
                <div id="hitToast" class="absolute top-6 sm:top-8 pointer-events-none z-40 px-3.5 py-1.5 rounded-full font-display font-extrabold text-xs sm:text-sm tracking-wider opacity-0 transition-all duration-300 transform -translate-y-2 shadow-lg"></div>

                <!-- Range Border Ambient Glow -->
                <div class="absolute inset-0 pointer-events-none border border-copper-500/20 rounded-xl z-25"></div>

                <!-- OVERLAY 1: START SESSION (MENUTUPI TARGET SEBELUM MULAI) -->
                <div id="startOverlay" class="absolute inset-0 z-40 bg-gray-950/90 backdrop-blur-md flex flex-col items-center justify-center p-6 text-center transition-all duration-300">
                    <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-full border-2 border-dashed border-copper-500/60 flex items-center justify-center mb-2">
                        <i data-lucide="crosshair" class="w-7 h-7 sm:w-8 sm:h-8 text-amber-400 animate-pulse"></i>
                    </div>
                    
                    <h3 class="font-display text-xl sm:text-2xl font-bold text-white tracking-wide uppercase mb-0.5">
                        Pistol Presisi 25M
                    </h3>
                    <p class="text-xs text-copper-300 font-mono mb-3">
                        10 Butir Peluru &bull; Waktu 3 Menit &bull; Poros Pusat X
                    </p>

                    <!-- Pilihan Mode Kontrol: Gyro OFF vs Gyro ON Sebelum Mulai -->
                    <div class="w-full max-w-[280px] mb-4 bg-gray-900/90 p-1.5 rounded-2xl border border-gray-800 shadow-inner">
                        <div class="text-[10px] font-mono text-gray-400 uppercase tracking-wider mb-1.5 text-center font-semibold">Pilih Mode Bidikan</div>
                        <div class="grid grid-cols-2 gap-1.5">
                            <button id="startGyroOffBtn" type="button" class="py-2 px-2 rounded-xl font-bold text-xs flex flex-col items-center justify-center gap-0.5 transition cursor-pointer bg-copper-600 text-white shadow-md ring-1 ring-amber-400/50">
                                <div class="flex items-center gap-1">
                                    <i data-lucide="hand" class="w-3.5 h-3.5"></i>
                                    <span>Gyro: OFF</span>
                                </div>
                                <span class="text-[9px] font-mono opacity-80">Drag + Sway Halus</span>
                            </button>
                            <button id="startGyroOnBtn" type="button" class="py-2 px-2 rounded-xl font-bold text-xs flex flex-col items-center justify-center gap-0.5 transition cursor-pointer bg-gray-800 text-gray-400 hover:bg-gray-700">
                                <div class="flex items-center gap-1">
                                    <i data-lucide="smartphone" class="w-3.5 h-3.5"></i>
                                    <span>Gyro: ON</span>
                                </div>
                                <span class="text-[9px] font-mono opacity-80">Gerak HP (Tanpa Sway)</span>
                            </button>
                        </div>
                    </div>

                    <!-- Big Start Button -->
                    <button id="startSessionBtn" type="button" class="px-8 py-3 rounded-2xl bg-gradient-to-r from-copper-600 via-copper-500 to-amber-500 hover:from-copper-500 hover:to-amber-400 active:scale-95 text-white font-display font-bold text-lg sm:text-xl tracking-wider shadow-2xl shadow-copper-600/50 flex items-center gap-2.5 transition-all border border-amber-300/40 cursor-pointer">
                        <i data-lucide="play" class="w-5 h-5 fill-white"></i>
                        <span>MULAI</span>
                    </button>
                </div>

                <!-- OVERLAY ZEROING / KALIBRASI GYRO (AKTIF SAAT GYRO ON SEBELUM MULAI) -->
                <div id="zeroingOverlay" class="absolute inset-0 z-45 bg-gray-950/40 backdrop-blur-[2px] flex flex-col justify-between p-3.5 sm:p-5 pointer-events-auto hidden transition-all duration-300">
                    <!-- Top HUD Bar -->
                    <div class="flex items-center justify-between gap-2 bg-gray-950/90 backdrop-blur-md px-3 py-2 rounded-xl border border-amber-500/40 shadow-lg">
                        <div class="flex items-center gap-2 text-left">
                            <span class="relative flex h-2.5 w-2.5">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-amber-500"></span>
                            </span>
                            <div>
                                <div class="text-[10px] sm:text-xs font-bold font-display tracking-wide uppercase text-amber-300 leading-tight">KALIBRASI ZERO GYRO</div>
                                <div class="text-[9px] font-mono text-gray-400">Atur postur tembak &amp; luruskan pejera</div>
                            </div>
                        </div>
                        <div class="flex items-center gap-1.5 bg-gray-900/90 px-2.5 py-1 rounded-lg border border-gray-700">
                            <i data-lucide="timer" class="w-3.5 h-3.5 text-amber-400"></i>
                            <span id="zeroingTimerText" class="font-mono font-bold text-xs sm:text-sm text-amber-400">30s</span>
                        </div>
                    </div>

                    <!-- Center Helper Hint (Non-intrusive) -->
                    <div class="self-center bg-gray-950/80 backdrop-blur-md px-3 py-1.5 rounded-full border border-gray-700/80 text-[10px] sm:text-xs font-mono text-gray-200 shadow-md text-center pointer-events-none max-w-[90%]">
                        Pegang HP senyaman postur bidik &bull; Tekan tombol <b class="text-amber-400">ZERO</b> untuk memosisikan pejera lurus di poros X
                    </div>

                    <!-- Bottom Controls: Zero Button & Ready Button -->
                    <div class="flex flex-col sm:flex-row items-center justify-center gap-2 bg-gray-950/90 backdrop-blur-md p-2.5 rounded-xl border border-gray-800 shadow-2xl">
                        <!-- Tombol Zero (Maks 3x) -->
                        <button id="zeroNowBtn" type="button" class="w-full sm:w-auto flex-1 py-2.5 px-4 rounded-xl bg-gradient-to-r from-amber-600 via-amber-500 to-amber-600 hover:from-amber-500 hover:to-amber-400 active:scale-95 text-white font-display font-bold text-xs sm:text-sm uppercase tracking-wider shadow-lg shadow-amber-600/30 flex items-center justify-center gap-2 transition cursor-pointer border border-amber-300/40">
                            <i data-lucide="crosshair" class="w-4 h-4"></i>
                            <span>ZERO / NOL-KAN</span>
                            <span id="zeroChancesBadge" class="px-1.5 py-0.5 rounded bg-black/40 text-[10px] font-mono text-amber-200 border border-amber-300/30">3x</span>
                        </button>

                        <!-- Tombol Selesai & Mulai Tembak -->
                        <button id="finishZeroBtn" type="button" class="w-full sm:w-auto py-2.5 px-4 rounded-xl bg-gray-800 hover:bg-gray-700 active:scale-95 text-white font-display font-bold text-xs sm:text-sm uppercase tracking-wider border border-gray-600 flex items-center justify-center gap-1.5 transition cursor-pointer">
                            <i data-lucide="play" class="w-3.5 h-3.5 fill-white"></i>
                            <span>SIAP &amp; MULAI</span>
                        </button>
                    </div>
                </div>

                <!-- OVERLAY 2: COUNTDOWN 3, 2, 1 -->
                <div id="countdownOverlay" class="absolute inset-0 z-50 bg-gray-950/95 backdrop-blur-md flex flex-col items-center justify-center p-6 text-center hidden">
                    <span class="text-xs font-mono text-copper-400 uppercase tracking-widest mb-2 font-bold">BERSIAP</span>
                    <div id="countdownNumber" class="font-display font-black text-7xl sm:text-9xl text-amber-400 transition-transform duration-200">
                        3
                    </div>
                    <p class="text-xs text-gray-400 font-mono mt-3">Bidik lurus pejera pada poros pusat X!</p>
                </div>

                <!-- OVERLAY 3: SESI SELESAI (DIMUNCULKAN NILAI & DETAIL PADA PAPAN LESAN) -->
                <div id="resultOverlay" class="absolute inset-0 z-40 bg-gray-950/95 backdrop-blur-md flex flex-col items-center justify-center p-5 text-center hidden transition-opacity duration-300">
                    <div class="w-12 h-12 rounded-full bg-amber-500/20 border border-amber-500/40 flex items-center justify-center mb-2">
                        <i data-lucide="trophy" class="w-6 h-6 text-amber-400"></i>
                    </div>
                    
                    <h3 class="font-display text-lg sm:text-xl font-bold text-white tracking-wide uppercase">
                        SESI SELESAI
                    </h3>
                    <p id="resultReasonText" class="text-[11px] font-mono text-gray-400 mb-3">10 Peluru Telah Ditembakkan</p>

                    <!-- Big Score Badges -->
                    <div class="grid grid-cols-2 gap-2.5 w-full max-w-[280px] mb-3">
                        <div class="bg-gray-900 p-2.5 rounded-xl border border-gray-800">
                            <span class="text-[10px] font-mono text-gray-400 block uppercase">Total Nilai</span>
                            <span id="finalScoreVal" class="font-display font-extrabold text-2xl sm:text-3xl text-copper-400 leading-none">0</span>
                            <span class="text-[9px] text-gray-500 font-mono">Maks 100</span>
                        </div>
                        <div class="bg-gray-900 p-2.5 rounded-xl border border-gray-800">
                            <span class="text-[10px] font-mono text-gray-400 block uppercase">Inner X</span>
                            <span id="finalXVal" class="font-display font-extrabold text-2xl sm:text-3xl text-amber-400 leading-none">0X</span>
                            <span class="text-[9px] text-gray-500 font-mono">Pusat Bullseye</span>
                        </div>
                    </div>

                    <!-- Detail Perolehan Ring Tags -->
                    <div id="finalBreakdownTags" class="flex flex-wrap items-center justify-center gap-1.5 max-w-[320px] mb-4 text-[10px] font-mono">
                        <!-- Ring counts dynamically injected -->
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center gap-2">
                        <button id="restartSessionBtn" type="button" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-copper-600 via-copper-500 to-amber-500 hover:from-copper-500 hover:to-amber-400 active:scale-95 text-white font-display font-bold text-xs uppercase tracking-wider shadow-lg shadow-copper-600/40 flex items-center gap-1.5 cursor-pointer">
                            <i data-lucide="rotate-ccw" class="w-3.5 h-3.5"></i>
                            <span>Mulai Lagi (Reset)</span>
                        </button>
                        <button id="peekTargetBtn" type="button" class="px-3.5 py-2.5 rounded-xl bg-gray-900 hover:bg-gray-800 border border-gray-700 text-gray-300 font-mono text-xs flex items-center gap-1.5 transition cursor-pointer" title="Lihat lubang lesan di kertas">
                            <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                            <span id="peekBtnText">Lihat Lesan</span>
                        </button>
                    </div>
                </div>

                <!-- Floating Instruction Banner -->
                <div id="hoverInstruction" class="absolute bottom-2.5 bg-gray-950/90 backdrop-blur-md px-3 py-1 rounded-full border border-gray-700 text-[10px] font-mono text-copper-300 pointer-events-none z-30 flex items-center gap-1.5 transition-opacity duration-500 text-center shadow-lg">
                    <span class="hidden sm:inline">Arahkan kursor, sejajarkan pejera depan di celah pejera belakang &amp; KLIK</span>
                    <span class="sm:hidden">Geser layar &amp; sejajarkan pejera depan di celah pejera belakang</span>
                </div>
            </div>

            <!-- ================= BOTTOM: CLEAN CONTROLS ================= -->
            <div class="w-full mt-3 flex flex-wrap sm:flex-nowrap items-center gap-2 justify-between">
                <div class="flex items-center gap-1.5">
                    <!-- Gyro Toggle Button -->
                    <button id="gyroToggleBtn" type="button" class="py-2 px-2.5 rounded-xl bg-gray-900 hover:bg-gray-800 border border-gray-700 text-gray-200 font-bold text-xs flex items-center gap-1.5 transition select-none shadow-md cursor-pointer" title="Sensor Gyroscope HP (ON / OFF)">
                        <i data-lucide="smartphone" class="w-3.5 h-3.5 text-amber-400"></i>
                        <span id="gyroText" class="text-[11px]">Gyro: OFF</span>
                    </button>

                    <!-- Invert Gyro Button -->
                    <button id="gyroInvertBtn" type="button" class="py-2 px-2.5 rounded-xl bg-gray-900 hover:bg-gray-800 border border-gray-700 text-gray-300 font-bold text-xs flex items-center gap-1.5 transition select-none shadow-md cursor-pointer" title="Balik Arah Gerak Sway Gyroscope (Invers Arah)">
                        <i data-lucide="arrow-up-down" class="w-3.5 h-3.5 text-purple-400"></i>
                        <span id="gyroInvertText" class="text-[11px]">Invers: OFF</span>
                    </button>

                    <!-- Calibrate Zero Button -->
                    <button id="calibrateGyroBtn" type="button" class="py-2 px-2.5 rounded-xl bg-gray-900 hover:bg-gray-800 border border-gray-700 text-gray-300 font-bold text-xs flex items-center gap-1.5 transition select-none shadow-md cursor-pointer" title="Setel Ulang Sudut Posisi Tangan">
                        <i data-lucide="compass" class="w-3.5 h-3.5 text-amber-400"></i>
                        <span class="text-[11px]">Nol-kan</span>
                    </button>
                </div>

                <div class="flex items-center gap-1.5 ml-auto">
                    <!-- Steady Breath Button -->
                    <button id="steadyBreathBtn" type="button" class="py-2 px-2.5 rounded-xl bg-gray-900 active:bg-blue-600 hover:bg-gray-800 border border-gray-700 text-gray-200 font-bold text-xs flex items-center gap-1 transition select-none shadow-md cursor-pointer" title="Tahan Nafas 3 Detik">
                        <i data-lucide="wind" class="w-3.5 h-3.5 text-blue-400"></i>
                        <span id="breathLabel" class="text-[11px]">Nafas</span>
                    </button>

                    <!-- Big Trigger Button (Mobile & Touch) -->
                    <button id="mobileTriggerBtn" type="button" class="py-2 px-3.5 rounded-xl bg-gradient-to-r from-copper-600 to-amber-600 active:scale-95 text-white font-display font-bold text-xs uppercase tracking-wider flex items-center gap-1.5 shadow-md shadow-copper-600/30 select-none border border-amber-300/30 cursor-pointer">
                        <i data-lucide="crosshair" class="w-3.5 h-3.5"></i>
                        <span>Tembak!</span>
                    </button>

                    <!-- Quick Reset Button -->
                    <button id="quickResetBtn" type="button" class="py-2 px-2.5 rounded-xl bg-gray-900 hover:bg-gray-800 border border-gray-700 text-gray-400 hover:text-red-400 transition cursor-pointer" title="Reset Sesi">
                        <i data-lucide="rotate-ccw" class="w-3.5 h-3.5"></i>
                    </button>
                </div>
            </div>

        </div>

    </div>
</section>

<!-- ================= SIMULATOR JAVASCRIPT ENGINE ================= -->
<script>
// --- Web Audio Synthesizer ---
class GunshotAudioSynth {
    constructor() {
        this.audioCtx = null;
        this.soundEnabled = true;
    }
    init() {
        if (!this.audioCtx) {
            const AudioContext = window.AudioContext || window.webkitAudioContext;
            if (AudioContext) {
                this.audioCtx = new AudioContext();
            }
        }
        if (this.audioCtx && this.audioCtx.state === 'suspended') {
            this.audioCtx.resume();
        }
    }
    playGunshotSound() {
        if (!this.soundEnabled) return;
        this.init();
        if (!this.audioCtx) return;
        const ctx = this.audioCtx;
        const now = ctx.currentTime;

        const bufferSize = ctx.sampleRate * 0.4;
        const buffer = ctx.createBuffer(1, bufferSize, ctx.sampleRate);
        const data = buffer.getChannelData(0);
        for (let i = 0; i < bufferSize; i++) {
            data[i] = (Math.random() * 2 - 1) * Math.exp(-i / (ctx.sampleRate * 0.065));
        }
        const noise = ctx.createBufferSource();
        noise.buffer = buffer;
        const filter = ctx.createBiquadFilter();
        filter.type = 'lowpass';
        filter.frequency.setValueAtTime(2800, now);
        filter.frequency.exponentialRampToValueAtTime(150, now + 0.35);
        const noiseGain = ctx.createGain();
        noiseGain.gain.setValueAtTime(1.0, now);
        noiseGain.gain.exponentialRampToValueAtTime(0.001, now + 0.4);
        noise.connect(filter);
        filter.connect(noiseGain);
        noiseGain.connect(ctx.destination);
        noise.start(now);

        const subOsc = ctx.createOscillator();
        const subGain = ctx.createGain();
        subOsc.type = 'sine';
        subOsc.frequency.setValueAtTime(110, now);
        subOsc.frequency.exponentialRampToValueAtTime(35, now + 0.22);
        subGain.gain.setValueAtTime(0.9, now);
        subGain.gain.exponentialRampToValueAtTime(0.001, now + 0.25);
        subOsc.connect(subGain);
        subGain.connect(ctx.destination);
        subOsc.start(now);
        subOsc.stop(now + 0.26);
    }
    playTargetHitSound(isX) {
        if (!this.soundEnabled) return;
        this.init();
        if (!this.audioCtx) return;
        const ctx = this.audioCtx;
        const now = ctx.currentTime + 0.06;
        const osc = ctx.createOscillator();
        const gain = ctx.createGain();
        osc.type = isX ? 'sine' : 'triangle';
        osc.frequency.setValueAtTime(isX ? 880 : 540, now);
        osc.frequency.exponentialRampToValueAtTime(isX ? 1100 : 220, now + 0.1);
        gain.gain.setValueAtTime(0.4, now);
        gain.gain.exponentialRampToValueAtTime(0.001, now + 0.12);
        osc.connect(gain);
        gain.connect(ctx.destination);
        osc.start(now);
        osc.stop(now + 0.13);
    }
    playBeep(freq = 600, duration = 0.12) {
        if (!this.soundEnabled) return;
        this.init();
        if (!this.audioCtx) return;
        const ctx = this.audioCtx;
        const now = ctx.currentTime;
        const osc = ctx.createOscillator();
        const gain = ctx.createGain();
        osc.type = 'sine';
        osc.frequency.setValueAtTime(freq, now);
        gain.gain.setValueAtTime(0.25, now);
        gain.gain.exponentialRampToValueAtTime(0.001, now + duration);
        osc.connect(gain);
        gain.connect(ctx.destination);
        osc.start(now);
        osc.stop(now + duration);
    }
    playFinishBuzzer() {
        if (!this.soundEnabled) return;
        this.init();
        if (!this.audioCtx) return;
        const ctx = this.audioCtx;
        const now = ctx.currentTime;
        const osc = ctx.createOscillator();
        const gain = ctx.createGain();
        osc.type = 'sawtooth';
        osc.frequency.setValueAtTime(220, now);
        osc.frequency.setValueAtTime(180, now + 0.2);
        gain.gain.setValueAtTime(0.3, now);
        gain.gain.exponentialRampToValueAtTime(0.001, now + 0.45);
        osc.connect(gain);
        gain.connect(ctx.destination);
        osc.start(now);
        osc.stop(now + 0.45);
    }
}
const audio = new GunshotAudioSynth();

// Sound Toggle
const soundToggleBtn = document.getElementById('soundToggleBtn');
const soundIcon = document.getElementById('soundIcon');
if (soundToggleBtn) {
    soundToggleBtn.addEventListener('click', (e) => {
        e.preventDefault();
        audio.soundEnabled = !audio.soundEnabled;
        if (soundIcon) {
            soundIcon.setAttribute('data-lucide', audio.soundEnabled ? 'volume-2' : 'volume-x');
            if (window.lucide) lucide.createIcons();
        }
        audio.init();
    });
}

// Canvas & DOM Elements
const targetCanvas = document.getElementById('targetCanvas');
const sightsCanvas = document.getElementById('sightsCanvas');
const tCtx = targetCanvas.getContext('2d');
const sCtx = sightsCanvas.getContext('2d');
const rangeCard = document.getElementById('rangeCard');
const muzzleFlash = document.getElementById('muzzleFlash');
const hitToast = document.getElementById('hitToast');
const hoverInstruction = document.getElementById('hoverInstruction');

// Overlays & Session Controls
const startOverlay = document.getElementById('startOverlay');
const startSessionBtn = document.getElementById('startSessionBtn');
const countdownOverlay = document.getElementById('countdownOverlay');
const countdownNumber = document.getElementById('countdownNumber');
const zeroingOverlay = document.getElementById('zeroingOverlay');
const zeroingTimerText = document.getElementById('zeroingTimerText');
const zeroNowBtn = document.getElementById('zeroNowBtn');
const zeroChancesBadge = document.getElementById('zeroChancesBadge');
const finishZeroBtn = document.getElementById('finishZeroBtn');
const resultOverlay = document.getElementById('resultOverlay');
const resultReasonText = document.getElementById('resultReasonText');
const finalScoreVal = document.getElementById('finalScoreVal');
const finalXVal = document.getElementById('finalXVal');
const finalBreakdownTags = document.getElementById('finalBreakdownTags');
const restartSessionBtn = document.getElementById('restartSessionBtn');
const peekTargetBtn = document.getElementById('peekTargetBtn');
const peekBtnText = document.getElementById('peekBtnText');
const quickResetBtn = document.getElementById('quickResetBtn');

// Top Scoreboard Elements
const matchTimerBadge = document.getElementById('matchTimerBadge');
const matchTimerText = document.getElementById('matchTimerText');
const sessionStatusPill = document.getElementById('sessionStatusPill');
const topTotalScore = document.getElementById('topTotalScore');
const topInnerX = document.getElementById('topInnerX');
const tableTotalNilai = document.getElementById('tableTotalNilai');
const tableTotalX = document.getElementById('tableTotalX');

// Physical Action Buttons
const mobileTriggerBtn = document.getElementById('mobileTriggerBtn');
const steadyBreathBtn = document.getElementById('steadyBreathBtn');
const breathLabel = document.getElementById('breathLabel');
const gyroToggleBtn = document.getElementById('gyroToggleBtn');
const gyroText = document.getElementById('gyroText');
const startGyroOffBtn = document.getElementById('startGyroOffBtn');
const startGyroOnBtn = document.getElementById('startGyroOnBtn');
const gyroInvertBtn = document.getElementById('gyroInvertBtn');
const gyroInvertText = document.getElementById('gyroInvertText');
const calibrateGyroBtn = document.getElementById('calibrateGyroBtn');

// Game State
let cWidth = 460;
let cHeight = 460;
let targetCenter = { x: 230, y: 230 };
const TARGET_MAX_RADIUS = 200;

// Standard ISSF 25M Precision Target Radii
const RING_RADII = {
    X: TARGET_MAX_RADIUS * 0.05,
    10: TARGET_MAX_RADIUS * 0.10,
    9: TARGET_MAX_RADIUS * 0.20,
    8: TARGET_MAX_RADIUS * 0.30,
    7: TARGET_MAX_RADIUS * 0.40,
    6: TARGET_MAX_RADIUS * 0.50,
    5: TARGET_MAX_RADIUS * 0.60,
    4: TARGET_MAX_RADIUS * 0.70,
    3: TARGET_MAX_RADIUS * 0.80,
    2: TARGET_MAX_RADIUS * 0.90,
    1: TARGET_MAX_RADIUS * 1.00
};

const MATCH_MAX_SHOTS = 10;
let shots = [];

// Proteksi Rapid Fire: Jeda minimal 1 detik antar tembakan
let lastShotTimestamp = 0;
const MIN_SHOT_INTERVAL = 1000; // ms

// Session & 3-Minute Timer
let sessionActive = false;
let matchTimeRemaining = 180; // 180 detik = 3 menit
let matchTimerInterval = null;

// Poros pada X (Zero Ballistic Elevation Offset)
function getElevationDrop() {
    return 0; // Paten: Poros tembakan tepat pada pusat X!
}

// Gyro Sway Engine & Mode Toggle (Mode Gyro ON / OFF)
let gyroActive = false; // Default: Gyro OFF
const GYRO_SENS_MULTIPLIER = 1.95;
let gyroInverted = false;
let gyroAngle = { beta: 0, gamma: 0 };
let gyroRef = { beta: null, gamma: null };
let gyroVelocity = { beta: 0, gamma: 0 };
let lastGyroTimestamp = 0;
let lastRawAngle = { beta: 0, gamma: 0 };
let hasGyroSensor = false;
let gyroPermissionRequested = false;

// Sights & Recoil
let recoilTime = 0;
let isRecoil = false;
let recoilOffsetY = 0;
let recoilAngle = 0;
let frontSightOffset = { x: 0, y: 0 };

function resizeCanvas() {
    const rect = rangeCard.getBoundingClientRect();
    const dpr = Math.min(window.devicePixelRatio || 1, 2);
    cWidth = rect.width;
    cHeight = rect.height;
    targetCenter = { x: cWidth / 2, y: cHeight / 2 };

    targetCanvas.width = cWidth * dpr;
    targetCanvas.height = cHeight * dpr;
    sightsCanvas.width = cWidth * dpr;
    sightsCanvas.height = cHeight * dpr;

    tCtx.setTransform(1, 0, 0, 1, 0, 0);
    sCtx.setTransform(1, 0, 0, 1, 0, 0);
    tCtx.scale(dpr, dpr);
    sCtx.scale(dpr, dpr);

    drawTargetPaper();
}
window.addEventListener('resize', resizeCanvas);

function drawTargetPaper() {
    tCtx.clearRect(0, 0, cWidth, cHeight);
    tCtx.fillStyle = '#f8f4e6';
    tCtx.fillRect(0, 0, cWidth, cHeight);

    // Subtle paper grid lines
    tCtx.strokeStyle = 'rgba(0, 0, 0, 0.04)';
    tCtx.lineWidth = 1;
    const gridSize = 35;
    for (let x = 0; x < cWidth; x += gridSize) {
        tCtx.beginPath(); tCtx.moveTo(x, 0); tCtx.lineTo(x, cHeight); tCtx.stroke();
    }
    for (let y = 0; y < cHeight; y += gridSize) {
        tCtx.beginPath(); tCtx.moveTo(0, y); tCtx.lineTo(cWidth, y); tCtx.stroke();
    }

    const scale = cWidth / 500;
    const cx = targetCenter.x;
    const cy = targetCenter.y;

    // Rings 1 to 6 (White/Cream paper background)
    for (let r = 1; r <= 6; r++) {
        const radius = (RING_RADII[r] * (cWidth / 430));
        tCtx.beginPath();
        tCtx.arc(cx, cy, radius, 0, Math.PI * 2);
        tCtx.fillStyle = (r % 2 === 0) ? '#fbf8ee' : '#f5f0dc';
        tCtx.fill();
        tCtx.strokeStyle = '#222222';
        tCtx.lineWidth = 1.2 * scale;
        tCtx.stroke();
        drawRingNumbers(r.toString(), radius, scale, '#222222');
    }

    // Rings 7 to 10 & Inner X (Black Bullseye Center)
    const blackRadius = (RING_RADII[7] * (cWidth / 430));
    tCtx.beginPath();
    tCtx.arc(cx, cy, blackRadius, 0, Math.PI * 2);
    tCtx.fillStyle = '#111827';
    tCtx.fill();

    for (let r = 7; r <= 10; r++) {
        const radius = (RING_RADII[r] * (cWidth / 430));
        tCtx.beginPath();
        tCtx.arc(cx, cy, radius, 0, Math.PI * 2);
        tCtx.strokeStyle = '#f8fafc';
        tCtx.lineWidth = 1.2 * scale;
        tCtx.stroke();
        drawRingNumbers(r.toString(), radius, scale, '#f8fafc');
    }

    // Inner X ring
    const xRadius = (RING_RADII.X * (cWidth / 430));
    tCtx.beginPath();
    tCtx.arc(cx, cy, xRadius, 0, Math.PI * 2);
    tCtx.strokeStyle = '#f8fafc';
    tCtx.lineWidth = 1.2 * scale;
    tCtx.stroke();

    // Crosshair at center X
    tCtx.beginPath();
    tCtx.moveTo(cx - (10 * scale), cy); tCtx.lineTo(cx + (10 * scale), cy);
    tCtx.moveTo(cx, cy - (10 * scale)); tCtx.lineTo(cx, cy + (10 * scale));
    tCtx.strokeStyle = '#f8fafc';
    tCtx.lineWidth = 1.2 * scale;
    tCtx.stroke();

    // Re-draw fired bullet holes
    shots.forEach(s => drawBulletHole(s.x, s.y, s.isX));
}

function drawRingNumbers(text, radius, scale, color) {
    tCtx.fillStyle = color;
    const cx = targetCenter.x;
    const cy = targetCenter.y;
    const offset = 7 * scale;
    const fontSize = Math.max(9, 10 * scale);
    tCtx.font = `600 ${fontSize}px 'Oswald', sans-serif`;
    tCtx.textAlign = 'center';
    tCtx.textBaseline = 'middle';
    tCtx.fillText(text, cx, cy - radius + offset);
    tCtx.fillText(text, cx, cy + radius - offset);
    tCtx.fillText(text, cx - radius + offset, cy);
    tCtx.fillText(text, cx + radius - offset, cy);
}

function drawBulletHole(x, y, isX) {
    const scale = cWidth / 500;
    const caliberRadius = 4.5 * scale; // 9mm caliber scale

    tCtx.save();
    // Shadow burn effect
    tCtx.beginPath();
    tCtx.arc(x, y, caliberRadius * 1.35, 0, Math.PI * 2);
    tCtx.fillStyle = 'rgba(0, 0, 0, 0.45)';
    tCtx.fill();

    // Inner punched lead hole
    tCtx.beginPath();
    tCtx.arc(x, y, caliberRadius, 0, Math.PI * 2);
    tCtx.fillStyle = '#090d16';
    tCtx.fill();
    tCtx.strokeStyle = isX ? '#f59e0b' : '#334155';
    tCtx.lineWidth = 1.5;
    tCtx.stroke();

    // Center grease ring
    tCtx.beginPath();
    tCtx.arc(x, y, caliberRadius * 0.45, 0, Math.PI * 2);
    tCtx.fillStyle = '#1e293b';
    tCtx.fill();
    tCtx.restore();
}

// Aiming Coordinates & Gyro Sway Physics
let mousePos = { x: 230, y: 230 }; // Posisi bidikan utama yang digeser lewat drag sentuhan/mouse
let smoothedAim = { x: 230, y: 230 }; // Posisi drag yang dihaluskan (licin & fluida)
let currentAim = { x: 230, y: 230 }; // Posisi visual akhir pisir belakang (drag + sway gyro)
let dragVelocity = { x: 0, y: 0 }; // Kecepatan geser untuk inersia licin & motion sway
let time = 0;

// Breath State Machine (Maks 3 Detik Tahan Nafas, 6 Detik Pemulihan/Exhaustion)
let isHoldingBreath = false;
let breathTimeLeft = 0;
let breathCooldownLeft = 0;
let breathTimer = null;
let breathCooldownTimer = null;

function updateSightsAndTremor() {
    const S = cWidth / 430;
    time += 0.05;

    // Inersia ekstra licin saat menggeser (Momentum Glide 0.94)
    if (!isDraggingAim) {
        dragVelocity.x *= 0.94;
        dragVelocity.y *= 0.94;
        if (Math.abs(dragVelocity.x) > 0.03 || Math.abs(dragVelocity.y) > 0.03) {
            mousePos.x = Math.max(25, Math.min(cWidth - 25, mousePos.x + dragVelocity.x));
            mousePos.y = Math.max(25, Math.min(cHeight - 25, mousePos.y + dragVelocity.y));
        }
    }

    // Dynamic Motion Sway ketika digeser (Inersia laras & celah visir saat digerakkan - Lebih Sensitif)
    const motionLagFsX = -dragVelocity.x * 0.45;
    const motionLagFsY = -dragVelocity.y * 0.45;
    const motionSwayRearX = dragVelocity.x * 0.18;
    const motionSwayRearY = dragVelocity.y * 0.18;

    // Hitung goyangan pemulihan nafas jika baru selesai tahan nafas 3 detik (perlahan stabil dalam 6 detik)
    let exhaustionSwayX = 0;
    let exhaustionSwayY = 0;
    const recProgress = Math.max(0, breathCooldownLeft) / 6.0; // 1.0 turun perlahan ke 0.0

    if (breathCooldownLeft > 0) {
        // Gerakan nafas terengah-engah / dada naik turun yang perlahan mereda seiring countdown 6 detik
        exhaustionSwayX = (Math.sin(time * 2.8) * 10.0 + Math.cos(time * 5.1) * 4.0) * recProgress;
        exhaustionSwayY = (Math.cos(time * 2.0) * 18.0 + Math.sin(time * 4.2) * 6.0) * recProgress;
    }

    // 1. Natural Physiological Sway: Getaran Otot & Siklus Pernapasan (Aktif pada mode Gyro: OFF - Lebih Lebar & Dipercepat 30%)
    // A. Siklus Pernapasan (Breathing Sway - Ritme nafas mengayun lebih lebar, tempo +30% lebih hidup)
    const breathSwayRearX = Math.cos(time * 0.36) * 8.5 * S;
    const breathSwayRearY = Math.sin(time * 0.55) * 18.0 * S;

    // B. Getaran Otot (Muscle Tremor - Drift postural lengan berayun lebar & responsif)
    const muscleDriftX = (Math.sin(time * 0.78) * 8.0 + Math.cos(time * 1.24) * 5.0) * S;
    const muscleDriftY = (Math.cos(time * 0.68) * 9.5 + Math.sin(time * 1.10) * 5.5) * S;
    const microTremorX = ((Math.random() - 0.5) * 0.9 + Math.sin(time * 7.8) * 0.7) * S;
    const microTremorY = ((Math.random() - 0.5) * 0.9 + Math.cos(time * 6.5) * 0.7) * S;

    const naturalMuscleSwayRearX = muscleDriftX + microTremorX;
    const naturalMuscleSwayRearY = muscleDriftY + microTremorY;

    // C. Getaran Kesejajaran Visir (Sight Alignment Tremor - Celah & rata air visir berayun lebih dinamis)
    const naturalFsWobbleX = (Math.sin(time * 0.91) * 3.8 + (Math.random() - 0.5) * 0.6);
    const naturalFsWobbleY = (Math.cos(time * 0.75) * 3.4 + (Math.random() - 0.5) * 0.6);

    // Total Sway Fisiologis (Pernapasan + Getaran Otot)
    let bodySwayRearX = breathSwayRearX + naturalMuscleSwayRearX;
    let bodySwayRearY = breathSwayRearY + naturalMuscleSwayRearY;
    let bodySwayFsX = (breathSwayRearX * 0.25) + naturalFsWobbleX;
    let bodySwayFsY = (breathSwayRearY * 0.25) + naturalFsWobbleY;

    // Mode Gyro ON / OFF
    let gyroSwayRearX = 0;
    let gyroSwayRearY = 0;
    let gyroFsOffsetDeltaX = 0;
    let gyroFsOffsetDeltaY = 0;

    if (gyroActive) {
        // Mode Gyro ON: Hilangkan semua sway buatan (0 procedural sway - murni gerakan fisik tangan dari sensor hp)
        bodySwayRearX = 0;
        bodySwayRearY = 0;
        bodySwayFsX = 0;
        bodySwayFsY = 0;
        exhaustionSwayX = 0;
        exhaustionSwayY = 0;

        // Inisialisasi sudut nol referensi gyro
        if (gyroRef.beta === null || gyroRef.gamma === null) {
            gyroRef.beta = gyroAngle.beta;
            gyroRef.gamma = gyroAngle.gamma;
        } else {
            // Penyesuaian nol sangat halus agar sudut tidak hanyut (drift) tanpa memakan gerakan tangan pemain
            gyroRef.beta += (gyroAngle.beta - gyroRef.beta) * 0.0004;
            gyroRef.gamma += (gyroAngle.gamma - gyroRef.gamma) * 0.0004;
        }

        const inv = gyroInverted ? -1 : 1;
        const deltaGamma = (gyroAngle.gamma - gyroRef.gamma) * inv;
        const deltaBeta = (gyroAngle.beta - gyroRef.beta) * inv;

        // Goyangan Pejera Belakang dari sensor Gyro (Sensitifitas Ditingkatkan Lebih Responsif)
        const REAR_SWAY_SENS = 17.5;
        gyroSwayRearX = deltaGamma * REAR_SWAY_SENS;
        gyroSwayRearY = deltaBeta * REAR_SWAY_SENS;

        // Goyangan Pejera Depan dari sensor Gyro (Sensitifitas Ditingkatkan Lebih Responsif)
        const CANT_SENS = 10.5;
        const PITCH_SENS = 9.5;
        const VEL_LAG = 0.90;
        gyroFsOffsetDeltaX = (deltaGamma * CANT_SENS) + (gyroVelocity.gamma * VEL_LAG * inv);
        gyroFsOffsetDeltaY = (deltaBeta * PITCH_SENS) + (gyroVelocity.beta * VEL_LAG * inv);
    }

    const MAX_REAR_SWAY = 150.0 * S;
    let rearSwayX = Math.max(-MAX_REAR_SWAY, Math.min(MAX_REAR_SWAY, bodySwayRearX + gyroSwayRearX + motionSwayRearX));
    let rearSwayY = Math.max(-MAX_REAR_SWAY, Math.min(MAX_REAR_SWAY, bodySwayRearY + gyroSwayRearY + motionSwayRearY));

    let targetFsX = bodySwayFsX + gyroFsOffsetDeltaX + motionLagFsX;
    let targetFsY = bodySwayFsY + gyroFsOffsetDeltaY + motionLagFsY;

    // Pengaruh Tahan Nafas (Maks 3s)
    // Mode OFF: mengurangi sway 30% | Mode ON: mengurangi sensitifitas gyro 30% | Kelicinan drag geser tetap terjaga 100%
    if (isHoldingBreath) {
        const breathDampening = 0.70;
        rearSwayX *= breathDampening;
        rearSwayY *= breathDampening;
        targetFsX *= breathDampening;
        targetFsY *= breathDampening;
    }

    // Pengaruh Masa Pemulihan 6s (Pejera bergoyang terengah-engah dan berangsur stabil)
    if (breathCooldownLeft > 0) {
        rearSwayX += exhaustionSwayX;
        rearSwayY += exhaustionSwayY;
        targetFsX += exhaustionSwayX * 0.45;
        targetFsY += exhaustionSwayY * 0.45;
    }

    const MAX_SIGHT_ERROR = 48.0;
    targetFsX = Math.max(-MAX_SIGHT_ERROR, Math.min(MAX_SIGHT_ERROR, targetFsX));
    targetFsY = Math.max(-MAX_SIGHT_ERROR, Math.min(MAX_SIGHT_ERROR, targetFsY));

    const fsAlpha = isHoldingBreath ? 0.50 : 0.70;
    frontSightOffset.x += (targetFsX - frontSightOffset.x) * fsAlpha;
    frontSightOffset.y += (targetFsY - frontSightOffset.y) * fsAlpha;

    if (isRecoil) {
        recoilTime += 0.12;
        if (recoilTime >= 1.0) {
            isRecoil = false;
            recoilOffsetY = 0;
            recoilAngle = 0;
        } else {
            const progress = recoilTime;
            recoilOffsetY = -Math.sin(progress * Math.PI) * 44 * (1 - progress * 0.6);
            recoilAngle = -Math.sin(progress * Math.PI) * 0.055;
        }
    }

    // Menggeser ekstra halus & licin: smoothedAim mengejar mousePos dengan redaman fluida
    const dragAlpha = 0.18; // Lebih sensitif & responsif mengejar jari, tetap licin berbobot (fluid glide)
    smoothedAim.x += (mousePos.x - smoothedAim.x) * dragAlpha;
    smoothedAim.y += (mousePos.y - smoothedAim.y) * dragAlpha;

    // Posisi visual akhir pisir belakang di target: posisi drag + goyangan gyro
    currentAim.x = smoothedAim.x + rearSwayX;
    currentAim.y = smoothedAim.y + rearSwayY;

    renderSights();
}

function renderSights() {
    sCtx.clearRect(0, 0, cWidth, cHeight);
    if (!sessionActive && startOverlay && !startOverlay.classList.contains('hidden')) {
        // Do not draw active sights when start overlay is covering the target
        return;
    }

    sCtx.save();
    const aimX = currentAim.x;
    const aimY = currentAim.y;

    sCtx.translate(aimX, aimY + recoilOffsetY);
    sCtx.rotate(recoilAngle);

    const scale = cWidth / 500;
    // 2X SIGHT SIZES (PERBESAR PEJERA DEPAN & BELAKANG 2X LIPAT)
    const rearWidth = 168 * scale;
    const rearHeight = 68 * scale;
    const notchWidth = 48 * scale;
    const notchHeight = 40 * scale;

    const frontPostWidth = 26 * scale;
    const frontPostHeight = 68 * scale;

    const fsX = frontSightOffset.x * scale * 1.8;
    const fsY = frontSightOffset.y * scale * 1.8;

    // A. Front Sight (Pejera Depan - 2x Size)
    sCtx.save();
    sCtx.translate(fsX, fsY);
    sCtx.fillStyle = '#1c1917';
    sCtx.beginPath();
    sCtx.moveTo(-frontPostWidth / 2, 0);
    sCtx.lineTo(frontPostWidth / 2, 0);
    sCtx.lineTo(frontPostWidth / 2, frontPostHeight);
    sCtx.lineTo(-frontPostWidth / 2, frontPostHeight);
    sCtx.closePath();
    sCtx.fill();
    sCtx.strokeStyle = '#292524';
    sCtx.lineWidth = 2.0 * scale;
    sCtx.stroke();

    // Top highlight
    sCtx.fillStyle = 'rgba(255, 255, 255, 0.25)';
    sCtx.fillRect(-frontPostWidth / 2, 0, frontPostWidth, 3.0 * scale);

    // Titik Pejera Depan: Putih Biasa (Matte White Contrast Dot, 2x Size)
    const dotRadius = 6.0 * scale;
    const dotY = 13.6 * scale;

    sCtx.beginPath();
    sCtx.arc(0, dotY, dotRadius, 0, Math.PI * 2);
    sCtx.fillStyle = '#ffffff';
    sCtx.fill();
    sCtx.strokeStyle = 'rgba(0, 0, 0, 0.45)';
    sCtx.lineWidth = 1.5 * scale;
    sCtx.stroke();
    sCtx.restore();

    // B. Rear Sight (Pejera Belakang - 2x Size)
    sCtx.fillStyle = '#0f172a';
    sCtx.strokeStyle = '#1e293b';
    sCtx.lineWidth = 2.4 * scale;

    // Left Ear
    sCtx.beginPath();
    sCtx.moveTo(-rearWidth / 2, 0);
    sCtx.lineTo(-notchWidth / 2, 0);
    sCtx.lineTo(-notchWidth / 2, notchHeight);
    sCtx.lineTo(-rearWidth / 2, notchHeight);
    sCtx.closePath();
    sCtx.fill();
    sCtx.stroke();

    // Right Ear
    sCtx.beginPath();
    sCtx.moveTo(notchWidth / 2, 0);
    sCtx.lineTo(rearWidth / 2, 0);
    sCtx.lineTo(rearWidth / 2, notchHeight);
    sCtx.lineTo(notchWidth / 2, notchHeight);
    sCtx.closePath();
    sCtx.fill();
    sCtx.stroke();

    // Sight Base
    sCtx.beginPath();
    sCtx.moveTo(-rearWidth / 2, notchHeight);
    sCtx.lineTo(rearWidth / 2, notchHeight);
    sCtx.lineTo(rearWidth / 2 - 8 * scale, rearHeight + 10 * scale);
    sCtx.lineTo(-rearWidth / 2 + 8 * scale, rearHeight + 10 * scale);
    sCtx.closePath();
    sCtx.fill();
    sCtx.stroke();

    // Anti-glare serrations
    sCtx.strokeStyle = 'rgba(255, 255, 255, 0.08)';
    sCtx.lineWidth = 1.2 * scale;
    for (let y = 8 * scale; y < rearHeight; y += 7 * scale) {
        sCtx.beginPath();
        sCtx.moveTo(-rearWidth / 2 + 4 * scale, y);
        sCtx.lineTo(-notchWidth / 2 - 4 * scale, y);
        sCtx.stroke();

        sCtx.beginPath();
        sCtx.moveTo(notchWidth / 2 + 4 * scale, y);
        sCtx.lineTo(rearWidth / 2 - 4 * scale, y);
        sCtx.stroke();
    }

    // Dual Tritium White Dots (2x Size)
    const rearDotRadius = 5.2 * scale;
    const dotOffsetX = rearWidth / 4 + 4 * scale;
    const dotOffsetY = 19.0 * scale;

    sCtx.beginPath();
    sCtx.arc(-dotOffsetX, dotOffsetY, rearDotRadius, 0, Math.PI * 2);
    sCtx.fillStyle = '#e2e8f0';
    sCtx.fill();

    sCtx.beginPath();
    sCtx.arc(dotOffsetX, dotOffsetY, rearDotRadius, 0, Math.PI * 2);
    sCtx.fillStyle = '#e2e8f0';
    sCtx.fill();

    sCtx.restore();
}

// Shooting Physics & Score Determination
function shootTarget() {
    if (!sessionActive) return;
    if (shots.length >= MATCH_MAX_SHOTS) return;

    // Proteksi Rapid Fire: Jarak tiap tembakan minimal 1 detik
    const now = performance.now();
    if (now - lastShotTimestamp < MIN_SHOT_INTERVAL) {
        showHitToast('JEDA TEMBAKAN 1 DETIK', 'bg-amber-900/90 text-amber-200 border-amber-600');
        return;
    }
    lastShotTimestamp = now;

    // Visual Cooldown pada Tombol Tembak
    if (mobileTriggerBtn) {
        mobileTriggerBtn.classList.add('opacity-50', 'pointer-events-none');
        setTimeout(() => {
            if (mobileTriggerBtn) mobileTriggerBtn.classList.remove('opacity-50', 'pointer-events-none');
        }, MIN_SHOT_INTERVAL);
    }

    audio.playGunshotSound();

    // Visual Flash & Recoil
    muzzleFlash.style.opacity = '0.9';
    setTimeout(() => { muzzleFlash.style.opacity = '0'; }, 70);

    isRecoil = true;
    recoilTime = 0;

    const scale = cWidth / 500;
    
    // 1. Posisi Pejera Belakang (Rear Sight) pada bidang lesan
    const rearX = currentAim.x;
    const rearY = currentAim.y;

    // 2. Kesejajaran Antara Pejera Depan dan Pejera Belakang (Sight Alignment Error)
    // Penyimpangan horizontal (tengah celah visir) & vertikal (rata air puncak visir)
    const alignErrX = frontSightOffset.x * scale;
    const alignErrY = frontSightOffset.y * scale;

    // 3. Posisi Visual Pejera Depan pada bidang lesan (Sight Picture dengan skala 2x)
    const frontSightX = rearX + (alignErrX * 1.8);
    const frontSightY = rearY + (alignErrY * 1.8);

    // 4. Pembiasan Balistik dari Ketidaksejajaran Pejera Depan & Pejera Belakang (Diperbesar Signifikan)
    // Pada jarak 25 meter, deviasi sudut visir (celah visir & rata air) sangat menghukum.
    // Jika pejera depan tidak tepat di tengah celah atau tidak rata air dengan pejera belakang,
    // sudut laras membias tajam sehingga perkenaan peluru melenceng jauh dari titik bidik.
    const SIGHT_ALIGNMENT_FACTOR = 8.8; // Diperbesar signifikan dari 3.6 (efek ketidaksejajaran 2.5x lebih kuat)
    const errMagnitude = Math.sqrt(alignErrX * alignErrX + alignErrY * alignErrY);
    const progressiveFactor = 1.0 + Math.max(0, (errMagnitude - (2.0 * scale)) / (3.2 * scale)) * 0.85;

    const alignmentDeflectionX = alignErrX * SIGHT_ALIGNMENT_FACTOR * progressiveFactor;
    const alignmentDeflectionY = alignErrY * SIGHT_ALIGNMENT_FACTOR * progressiveFactor;

    // Dispersi mekanis laras peluru standar
    const barrelDispersionX = (Math.random() - 0.5) * 2.4 * scale;
    const barrelDispersionY = (Math.random() - 0.5) * 2.4 * scale;

    // Titik Perkenaan Tembakan Akhir (Impact Point):
    // Ditentukan oleh posisi pejera depan DAN kesejajaran antara pejera depan & pejera belakang!
    const impactX = frontSightX + alignmentDeflectionX + barrelDispersionX;
    const impactY = frontSightY + alignmentDeflectionY + barrelDispersionY;

    // Hit Distance from Center X
    const dx = impactX - targetCenter.x;
    const dy = impactY - targetCenter.y;
    const dist = Math.sqrt(dx * dx + dy * dy);

    const hitResult = calculateScoreFromRadius(dist, cWidth);
    audio.playTargetHitSound(hitResult.isX);

    shots.push({
        x: impactX,
        y: impactY,
        score: hitResult.score,
        isX: hitResult.isX,
        ring: hitResult.ringText,
        timeSec: 180 - matchTimeRemaining
    });

    drawBulletHole(impactX, impactY, hitResult.isX);

    // Deteksi apakah tembakan melenceng akibat visir tidak sejajar
    const isMisaligned = errMagnitude > (2.8 * scale) && hitResult.score <= 8;
    const toastMsg = hitResult.isX 
        ? 'INNER BULLSEYE (X)!' 
        : (isMisaligned 
            ? `SKOR: ${hitResult.score} (VISIR TIDAK SEJAJAR!)` 
            : `SKOR: ${hitResult.score} (RING ${hitResult.ringText})`);
    const toastStyle = isMisaligned 
        ? 'bg-amber-900/90 text-amber-200 border border-amber-600' 
        : hitResult.badgeStyle;

    showHitToast(toastMsg, toastStyle);

    updateScoreboardUI();

    // Recoil kick to sights
    const recoilUpMagnitude = 7.5 + Math.random() * 4.5;
    const recoilSideMagnitude = (Math.random() > 0.5 ? 1 : -1) * (2.0 + Math.random() * 2.5);
    frontSightOffset.y -= recoilUpMagnitude;
    frontSightOffset.x += recoilSideMagnitude;

    if (gyroRef.beta !== null) {
        gyroRef.beta -= (recoilUpMagnitude * 0.45);
        gyroRef.gamma += (recoilSideMagnitude * 0.35);
    }

    // Check if 10 bullets reached
    if (shots.length >= MATCH_MAX_SHOTS) {
        setTimeout(() => {
            endMatchSession('10 BUTIR PELURU SELESAI');
        }, 500);
    }
}

function calculateScoreFromRadius(dist, canvasWidth) {
    const scale = canvasWidth / 430;
    if (dist <= RING_RADII.X * scale) {
        return { score: 10, isX: true, ringText: 'X', badgeStyle: 'bg-amber-500 text-gray-950 border-amber-300 font-extrabold' };
    }
    for (let r = 10; r >= 1; r--) {
        if (dist <= RING_RADII[r] * scale) {
            let color = 'bg-stone-800 text-white border-stone-600';
            if (r === 10) color = 'bg-emerald-600 text-white border-emerald-400';
            else if (r === 9) color = 'bg-teal-600 text-white border-teal-400';
            else if (r === 8) color = 'bg-blue-600 text-white border-blue-400';
            return { score: r, isX: false, ringText: r.toString(), badgeStyle: color };
        }
    }
    return { score: 0, isX: false, ringText: '0 (Lepas)', badgeStyle: 'bg-red-800 text-white border-red-600' };
}

function showHitToast(text, styleClass) {
    hitToast.textContent = text;
    hitToast.className = `absolute top-6 sm:top-8 pointer-events-none z-40 px-4 py-1.5 rounded-full font-display font-extrabold text-xs sm:text-sm tracking-wider opacity-100 shadow-xl transition-all duration-300 transform translate-y-0 ${styleClass}`;
    setTimeout(() => {
        hitToast.classList.add('opacity-0', '-translate-y-2');
    }, 1500);
}

// Update Top Live Scoreboard Table
function updateScoreboardUI() {
    let total = 0;
    let countX = 0;

    for (let i = 1; i <= 10; i++) {
        const cell = document.getElementById(`shotCell${i}`);
        if (!cell) continue;

        const shot = shots[i - 1];
        if (shot) {
            total += shot.score;
            if (shot.isX) countX++;
            const val = shot.isX ? 'X' : shot.score;
            let style = 'bg-gray-800 text-gray-200 border-gray-700';
            if (shot.isX) style = 'bg-amber-500/25 text-amber-300 border-amber-500/60 font-black';
            else if (shot.score === 10) style = 'bg-emerald-500/20 text-emerald-300 border-emerald-500/50';
            else if (shot.score === 9) style = 'bg-teal-500/20 text-teal-300 border-teal-500/50';
            else if (shot.score === 8) style = 'bg-blue-500/20 text-blue-300 border-blue-500/40';
            else if (shot.score === 0) style = 'bg-red-950 text-red-400 border-red-800';

            cell.innerHTML = `<span class="block py-1 rounded border ${style}">${val}</span>`;
        } else if (i === shots.length + 1 && sessionActive) {
            // Next active shot indicator
            cell.innerHTML = `<span class="block py-1 rounded border border-amber-400 bg-amber-950/40 text-amber-300 animate-pulse font-bold">...</span>`;
        } else {
            cell.innerHTML = `<span class="block py-1 rounded bg-gray-950/80 border border-gray-800 text-gray-600">-</span>`;
        }
    }

    topTotalScore.textContent = total;
    topInnerX.textContent = `${countX}X`;
    tableTotalNilai.textContent = total;
    tableTotalX.textContent = countX;
}

// 3-Minute Timer Engine
function updateTimerDisplay() {
    const mins = Math.floor(Math.max(0, matchTimeRemaining) / 60);
    const secs = Math.max(0, matchTimeRemaining) % 60;
    matchTimerText.textContent = `${String(mins).padStart(2, '0')}:${String(secs).padStart(2, '0')}`;

    if (matchTimeRemaining <= 10) {
        matchTimerBadge.className = 'flex items-center gap-1.5 px-3 py-1 rounded-xl bg-red-950/80 border border-red-600 text-red-400 font-bold text-sm sm:text-base font-mono animate-pulse shadow-inner';
    } else if (matchTimeRemaining <= 30) {
        matchTimerBadge.className = 'flex items-center gap-1.5 px-3 py-1 rounded-xl bg-amber-950/80 border border-amber-600 text-amber-400 font-bold text-sm sm:text-base font-mono shadow-inner';
    } else {
        matchTimerBadge.className = 'flex items-center gap-1.5 px-3 py-1 rounded-xl bg-gray-950 border border-gray-700 text-amber-400 font-bold text-sm sm:text-base font-mono shadow-inner';
    }
}

function beginActiveMatchSession() {
    sessionActive = true;
    matchTimeRemaining = 180;
    updateTimerDisplay();

    sessionStatusPill.textContent = 'MENEMBAK';
    sessionStatusPill.className = 'text-[10px] font-bold px-2.5 py-1 rounded-lg bg-emerald-950 text-emerald-300 border border-emerald-700 animate-pulse uppercase tracking-wider';

    if (matchTimerInterval) clearInterval(matchTimerInterval);
    matchTimerInterval = setInterval(() => {
        if (!sessionActive) {
            clearInterval(matchTimerInterval);
            return;
        }
        matchTimeRemaining--;
        updateTimerDisplay();
        if (matchTimeRemaining <= 0) {
            clearInterval(matchTimerInterval);
            endMatchSession('WAKTU HABIS (3 MENIT)');
        }
    }, 1000);

    updateScoreboardUI();
}

function triggerStartCountdown() {
    if (zeroingInterval) {
        clearInterval(zeroingInterval);
        zeroingInterval = null;
    }
    if (startOverlay) startOverlay.classList.add('hidden');
    if (zeroingOverlay) zeroingOverlay.classList.add('hidden');
    countdownOverlay.classList.remove('hidden');

    let count = 3;
    countdownNumber.textContent = count;
    audio.playBeep(600, 0.15);

    const timer = setInterval(() => {
        count--;
        if (count > 0) {
            countdownNumber.textContent = count;
            countdownNumber.classList.add('scale-125');
            setTimeout(() => { countdownNumber.classList.remove('scale-125'); }, 150);
            audio.playBeep(600, 0.15);
        } else if (count === 0) {
            countdownNumber.textContent = 'MULAI!';
            audio.playBeep(1200, 0.35);
        } else {
            clearInterval(timer);
            countdownOverlay.classList.add('hidden');
            beginActiveMatchSession();
        }
    }, 1000);
}

function endMatchSession(reason = 'SESI SELESAI') {
    sessionActive = false;
    if (matchTimerInterval) clearInterval(matchTimerInterval);
    audio.playFinishBuzzer();

    sessionStatusPill.textContent = 'SELESAI';
    sessionStatusPill.className = 'text-[10px] font-bold px-2.5 py-1 rounded-lg bg-red-950 text-red-300 border border-red-700 uppercase tracking-wider';

    resultReasonText.textContent = reason;

    let total = 0;
    let countX = 0;
    const ringCounts = {};
    shots.forEach(s => {
        total += s.score;
        if (s.isX) countX++;
        const key = s.isX ? 'X' : s.ring;
        ringCounts[key] = (ringCounts[key] || 0) + 1;
    });

    finalScoreVal.textContent = total;
    finalXVal.textContent = `${countX}X`;

    let html = '';
    const order = ['X', '10', '9', '8', '7', '6', '5', '4', '3', '2', '1', '0'];
    order.forEach(r => {
        if (ringCounts[r]) {
            html += `<span class="px-2 py-0.5 rounded bg-gray-800 text-gray-200 border border-gray-700">Ring ${r}: <b class="${r === 'X' ? 'text-amber-400 font-extrabold' : 'text-white'}">${ringCounts[r]}x</b></span>`;
        }
    });

    const timeUsed = 180 - Math.max(0, matchTimeRemaining);
    const mUsed = Math.floor(timeUsed / 60);
    const sUsed = timeUsed % 60;
    html += `<span class="px-2 py-0.5 rounded bg-copper-950 text-copper-300 border border-copper-700 font-bold">Waktu: ${String(mUsed).padStart(2,'0')}:${String(sUsed).padStart(2,'0')}</span>`;
    finalBreakdownTags.innerHTML = html;

    resultOverlay.classList.remove('hidden', 'opacity-0', 'pointer-events-none');
    peekBtnText.textContent = 'Lihat Lesan';
}

function resetNewMatchSeries() {
    sessionActive = false;
    lastShotTimestamp = 0;
    if (mobileTriggerBtn) mobileTriggerBtn.classList.remove('opacity-50', 'pointer-events-none');
    if (matchTimerInterval) clearInterval(matchTimerInterval);
    if (breathTimer) clearInterval(breathTimer);
    if (breathCooldownTimer) clearInterval(breathCooldownTimer);
    isHoldingBreath = false;
    breathTimeLeft = 0;
    breathCooldownLeft = 0;
    steadyBreathBtn.className = 'py-2 px-2.5 rounded-xl bg-gray-900 active:bg-blue-600 hover:bg-gray-800 border border-gray-700 text-gray-200 font-bold text-xs flex items-center gap-1 transition select-none shadow-md cursor-pointer';
    breathLabel.textContent = 'Nafas';

    shots = [];
    matchTimeRemaining = 180;
    updateTimerDisplay();

    sessionStatusPill.textContent = 'SIAP MULAI';
    sessionStatusPill.className = 'text-[10px] font-bold px-2.5 py-1 rounded-lg bg-gray-800 text-gray-300 border border-gray-700 uppercase tracking-wider';

    if (zeroingInterval) {
        clearInterval(zeroingInterval);
        zeroingInterval = null;
    }
    if (zeroingOverlay) zeroingOverlay.classList.add('hidden');
    resultOverlay.classList.add('hidden');
    resultOverlay.classList.remove('opacity-0', 'pointer-events-none');
    countdownOverlay.classList.add('hidden');
    startOverlay.classList.remove('hidden');

    drawTargetPaper();
    updateScoreboardUI();
    updateGyroUI();
}

// ================= ZEROING / KALIBRASI MODE (GYRO ON) =================
let zeroChancesRemaining = 3;
let zeroingTimer = 30;
let zeroingInterval = null;

function startZeroingPhase() {
    if (startOverlay) startOverlay.classList.add('hidden');
    if (zeroingOverlay) zeroingOverlay.classList.remove('hidden');

    zeroChancesRemaining = 3;
    zeroingTimer = 30;
    if (zeroChancesBadge) zeroChancesBadge.textContent = '3x';
    if (zeroingTimerText) zeroingTimerText.textContent = '30s';
    if (zeroNowBtn) {
        zeroNowBtn.disabled = false;
        zeroNowBtn.classList.remove('opacity-50', 'pointer-events-none');
        zeroNowBtn.classList.add('cursor-pointer');
    }
    if (finishZeroBtn) {
        finishZeroBtn.classList.remove('ring-2', 'ring-amber-400', 'animate-pulse');
    }

    // Posisi awal pejera depan & belakang diasumsikan lurus dalam kondisi poros
    calibrateGyroCenter();

    if (zeroingInterval) clearInterval(zeroingInterval);
    zeroingInterval = setInterval(() => {
        zeroingTimer--;
        if (zeroingTimerText) zeroingTimerText.textContent = `${zeroingTimer}s`;
        if (zeroingTimer <= 5 && zeroingTimer > 0) {
            audio.playBeep(520, 0.08);
        }
        if (zeroingTimer <= 0) {
            clearInterval(zeroingInterval);
            zeroingInterval = null;
            finishZeroingAndStartMatch();
        }
    }, 1000);

    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
}

function finishZeroingAndStartMatch() {
    if (zeroingInterval) {
        clearInterval(zeroingInterval);
        zeroingInterval = null;
    }
    if (zeroingOverlay) zeroingOverlay.classList.add('hidden');
    triggerStartCountdown();
}

if (zeroingOverlay) {
    ['touchstart', 'touchmove', 'touchend'].forEach(evt => {
        zeroingOverlay.addEventListener(evt, (e) => {
            e.stopPropagation();
        }, { passive: false });
    });
}

if (zeroNowBtn) {
    zeroNowBtn.addEventListener('click', (e) => {
        e.preventDefault();
        e.stopPropagation();
        if (zeroChancesRemaining <= 0) return;

        zeroChancesRemaining--;
        calibrateGyroCenter();
        audio.playBeep(880, 0.15);
        showHitToast(`NOL TERKALIBRASI (POROS X) • SISA ${zeroChancesRemaining}X`, 'bg-amber-600 text-white border-amber-400');

        if (zeroChancesRemaining > 0) {
            if (zeroChancesBadge) zeroChancesBadge.textContent = `${zeroChancesRemaining}x`;
        } else {
            if (zeroChancesBadge) zeroChancesBadge.textContent = 'Habis';
            zeroNowBtn.disabled = true;
            zeroNowBtn.classList.add('opacity-50', 'pointer-events-none');
            zeroNowBtn.classList.remove('cursor-pointer');
            if (finishZeroBtn) finishZeroBtn.classList.add('ring-2', 'ring-amber-400', 'animate-pulse');
            setTimeout(() => {
                finishZeroingAndStartMatch();
            }, 400);
        }
    });
}

if (finishZeroBtn) {
    finishZeroBtn.addEventListener('click', (e) => {
        e.preventDefault();
        e.stopPropagation();
        finishZeroingAndStartMatch();
    });
}

// Event Listeners for Session Controls
startSessionBtn.addEventListener('click', async (e) => {
    e.preventDefault();
    audio.init();
    if (gyroActive) {
        await requestGyroPermissionIfNeeded();
        startZeroingPhase();
    } else {
        triggerStartCountdown();
    }
});

restartSessionBtn.addEventListener('click', (e) => {
    e.preventDefault();
    resetNewMatchSeries();
});

quickResetBtn.addEventListener('click', (e) => {
    e.preventDefault();
    if (confirm('Reset sesi menembak presisi?')) {
        resetNewMatchSeries();
    }
});

let isPeeking = false;
peekTargetBtn.addEventListener('click', (e) => {
    e.preventDefault();
    isPeeking = !isPeeking;
    if (isPeeking) {
        resultOverlay.classList.add('opacity-0', 'pointer-events-none');
        peekBtnText.textContent = 'Tutup Lesan';
        showHitToast('KLIK TOMBOL UNTUK KEMBALI KE HASIL', 'bg-gray-800 text-amber-300 border border-gray-700');
    } else {
        resultOverlay.classList.remove('opacity-0', 'pointer-events-none');
        peekBtnText.textContent = 'Lihat Lesan';
    }
});

// Steady Breath Controller (Maks 3s, Cooldown 6s dengan Pejera Goyang dan Perlahan Stabil)
function toggleHoldBreath(e) {
    if (e) {
        e.preventDefault();
        e.stopPropagation();
    }
    audio.init();

    if (breathCooldownLeft > 0) {
        showHitToast(`PULIHKAN NAFAS (${breathCooldownLeft}s)`, 'bg-red-800 text-white border-red-600');
        return;
    }

    if (isHoldingBreath) {
        // Dilepas lebih awal -> langsung masuk masa pemulihan 6 detik
        startBreathExhaustionCooldown();
        return;
    }

    // Mulai Tahan Nafas (Maksimal 3 Detik)
    isHoldingBreath = true;
    breathTimeLeft = 3;
    steadyBreathBtn.className = 'py-2 px-2.5 rounded-xl bg-blue-600 text-white ring-2 ring-blue-400 border border-blue-400 font-bold text-xs flex items-center gap-1 transition select-none shadow-md cursor-pointer';
    breathLabel.textContent = 'Nafas: 3s';
    showHitToast('TAHAN NAFAS (MAKS 3s)', 'bg-blue-600 text-white border-blue-400');

    if (breathTimer) clearInterval(breathTimer);
    breathTimer = setInterval(() => {
        breathTimeLeft--;
        if (breathTimeLeft > 0) {
            breathLabel.textContent = `Nafas: ${breathTimeLeft}s`;
        } else {
            // Tepat 3 detik habis -> Masuk pemulihan 6 detik
            startBreathExhaustionCooldown();
        }
    }, 1000);
}

function startBreathExhaustionCooldown() {
    if (breathTimer) clearInterval(breathTimer);
    isHoldingBreath = false;
    breathTimeLeft = 0;
    breathCooldownLeft = 6;

    steadyBreathBtn.className = 'py-2 px-2.5 rounded-xl bg-red-950/80 border border-red-700 text-red-300 font-bold text-xs flex items-center gap-1 transition select-none shadow-md cursor-not-allowed opacity-90';
    breathLabel.textContent = 'Pulih: 6s';
    showHitToast('NAFAS HABIS! PEMULIHAN 6s', 'bg-red-900 text-red-200 border-red-700');

    if (breathCooldownTimer) clearInterval(breathCooldownTimer);
    breathCooldownTimer = setInterval(() => {
        breathCooldownLeft--;
        if (breathCooldownLeft > 0) {
            breathLabel.textContent = `Pulih: ${breathCooldownLeft}s`;
        } else {
            // Selesai pemulihan 6 detik -> Pejera kembali stabil total
            clearInterval(breathCooldownTimer);
            breathCooldownLeft = 0;
            steadyBreathBtn.className = 'py-2 px-2.5 rounded-xl bg-gray-900 active:bg-blue-600 hover:bg-gray-800 border border-gray-700 text-gray-200 font-bold text-xs flex items-center gap-1 transition select-none shadow-md cursor-pointer';
            breathLabel.textContent = 'Nafas';
            showHitToast('NAFAS KEMBALI STABIL', 'bg-emerald-600 text-white border-emerald-400');
        }
    }, 1000);
}

// Event bindings supporting simultaneous multi-touch and click
steadyBreathBtn.addEventListener('touchstart', toggleHoldBreath, { passive: false });
steadyBreathBtn.addEventListener('click', toggleHoldBreath);

// Mobile Trigger Button Controller (Mendukung Multi-Touch / Multi-Click)
function triggerShoot(e) {
    if (e) {
        e.preventDefault();
        e.stopPropagation();
    }
    audio.init();
    shootTarget();
}
mobileTriggerBtn.addEventListener('touchstart', triggerShoot, { passive: false });
mobileTriggerBtn.addEventListener('click', triggerShoot);

// Synchronized Gyro Controller for Start Overlay & Bottom Controls
function setGyroMode(active) {
    gyroActive = active;
    updateGyroUI();
}

function updateGyroUI() {
    if (gyroActive) {
        if (startGyroOnBtn && startGyroOffBtn) {
            startGyroOnBtn.className = 'py-2 px-2 rounded-xl font-bold text-xs flex flex-col items-center justify-center gap-0.5 transition cursor-pointer bg-emerald-600 text-white shadow-md ring-1 ring-emerald-400';
            startGyroOffBtn.className = 'py-2 px-2 rounded-xl font-bold text-xs flex flex-col items-center justify-center gap-0.5 transition cursor-pointer bg-gray-800 text-gray-400 hover:bg-gray-700';
        }
        if (gyroToggleBtn && gyroText) {
            gyroToggleBtn.classList.remove('bg-gray-900', 'text-gray-200', 'border-gray-700');
            gyroToggleBtn.classList.add('bg-emerald-950', 'text-emerald-300', 'border-emerald-600', 'ring-1', 'ring-emerald-400');
            gyroText.textContent = 'Gyro: ON';
        }
    } else {
        if (startGyroOnBtn && startGyroOffBtn) {
            startGyroOffBtn.className = 'py-2 px-2 rounded-xl font-bold text-xs flex flex-col items-center justify-center gap-0.5 transition cursor-pointer bg-copper-600 text-white shadow-md ring-1 ring-amber-400/50';
            startGyroOnBtn.className = 'py-2 px-2 rounded-xl font-bold text-xs flex flex-col items-center justify-center gap-0.5 transition cursor-pointer bg-gray-800 text-gray-400 hover:bg-gray-700';
        }
        if (gyroToggleBtn && gyroText) {
            gyroToggleBtn.classList.remove('bg-emerald-950', 'text-emerald-300', 'border-emerald-600', 'ring-1', 'ring-emerald-400');
            gyroToggleBtn.classList.add('bg-gray-900', 'text-gray-200', 'border-gray-700');
            gyroText.textContent = 'Gyro: OFF';
        }
    }
}

if (startGyroOffBtn) {
    startGyroOffBtn.addEventListener('click', (e) => {
        e.preventDefault();
        setGyroMode(false);
        showHitToast('MODE GYRO: OFF (DRAG + SWAY HALUS)', 'bg-gray-800 text-copper-300 border border-copper-700');
    });
}

if (startGyroOnBtn) {
    startGyroOnBtn.addEventListener('click', async (e) => {
        e.preventDefault();
        await requestGyroPermissionIfNeeded();
        setGyroMode(true);
        calibrateGyroCenter();
        showHitToast('MODE GYRO: ON (GERAK HP)', 'bg-emerald-600 text-white border-emerald-400');
    });
}

// Gyro Toggle Button (Mode Gyro ON / OFF)
gyroToggleBtn.addEventListener('click', async (e) => {
    e.preventDefault();
    if (!gyroActive) {
        await requestGyroPermissionIfNeeded();
        setGyroMode(true);
        calibrateGyroCenter();
        showHitToast('GYRO AKTIF (GERAKKAN HP)', 'bg-emerald-600 text-white border-emerald-400');
    } else {
        setGyroMode(false);
        showHitToast('GYRO OFF (MODE DRAG LAYAR)', 'bg-gray-700 text-white border-gray-600');
    }
});

// Gyro Invert Button
gyroInvertBtn.addEventListener('click', (e) => {
    e.preventDefault();
    gyroInverted = !gyroInverted;
    if (gyroInverted) {
        gyroInvertBtn.classList.remove('bg-gray-900', 'text-gray-300', 'border-gray-700');
        gyroInvertBtn.classList.add('bg-purple-950', 'text-purple-300', 'border-purple-600', 'ring-1', 'ring-purple-400');
        gyroInvertText.textContent = 'Invers: ON';
        showHitToast('INVERS GYRO: AKTIF', 'bg-purple-600 text-white border-purple-400');
    } else {
        gyroInvertBtn.classList.remove('bg-purple-950', 'text-purple-300', 'border-purple-600', 'ring-1', 'ring-purple-400');
        gyroInvertBtn.classList.add('bg-gray-900', 'text-gray-300', 'border-gray-700');
        gyroInvertText.textContent = 'Invers: OFF';
        showHitToast('INVERS GYRO: OFF', 'bg-gray-700 text-white border-gray-600');
    }
});

// Calibrate Zero
function calibrateGyroCenter() {
    gyroRef.beta = gyroAngle.beta;
    gyroRef.gamma = gyroAngle.gamma;
    gyroVelocity.beta = 0;
    gyroVelocity.gamma = 0;
    frontSightOffset.x = 0;
    frontSightOffset.y = 0;
    mousePos.x = targetCenter.x;
    mousePos.y = targetCenter.y;
    smoothedAim.x = targetCenter.x;
    smoothedAim.y = targetCenter.y;
    currentAim.x = targetCenter.x;
    currentAim.y = targetCenter.y;
    dragVelocity.x = 0;
    dragVelocity.y = 0;
    showHitToast('TITIK NOL DIKALIBRASI (POROS X)', 'bg-amber-600 text-white border-amber-400');
}

calibrateGyroBtn.addEventListener('click', (e) => {
    e.preventDefault();
    requestGyroPermissionIfNeeded();
    calibrateGyroCenter();
});

// Request permission seamlessly on modern browsers (e.g. iOS Safari)
async function requestGyroPermissionIfNeeded() {
    if (gyroPermissionRequested) return;
    if (typeof window !== 'undefined' && window.DeviceOrientationEvent && typeof window.DeviceOrientationEvent.requestPermission === 'function') {
        try {
            gyroPermissionRequested = true;
            const permission = await window.DeviceOrientationEvent.requestPermission();
            if (permission === 'granted') {
                window.addEventListener('deviceorientation', handleDeviceOrientation, { passive: true });
                hasGyroSensor = true;
            }
        } catch (err) {
            console.warn('Gyro permission error:', err);
        }
    } else if (typeof window !== 'undefined' && window.DeviceOrientationEvent) {
        gyroPermissionRequested = true;
        window.addEventListener('deviceorientation', handleDeviceOrientation, { passive: true });
        hasGyroSensor = true;
    }
}

function handleDeviceOrientation(e) {
    if (e.beta === null || e.gamma === null) return;
    hasGyroSensor = true;
    const now = performance.now();
    if (!lastGyroTimestamp) {
        lastGyroTimestamp = now;
        lastRawAngle.beta = e.beta;
        lastRawAngle.gamma = e.gamma;
        gyroAngle.beta = e.beta;
        gyroAngle.gamma = e.gamma;
        if (gyroRef.beta === null) {
            gyroRef.beta = e.beta;
            gyroRef.gamma = e.gamma;
        }
        return;
    }
    const dt = Math.max(0.008, (now - lastGyroTimestamp) / 1000);
    lastGyroTimestamp = now;

    const rawVelGamma = (e.gamma - lastRawAngle.gamma) / dt;
    const rawVelBeta = (e.beta - lastRawAngle.beta) / dt;

    // Filter sensor packet anomalies while preserving physical hand tremors
    if (Math.abs(rawVelGamma) < 360) {
        gyroVelocity.gamma += (rawVelGamma - gyroVelocity.gamma) * 0.65;
    }
    if (Math.abs(rawVelBeta) < 360) {
        gyroVelocity.beta += (rawVelBeta - gyroVelocity.beta) * 0.65;
    }

    lastRawAngle.gamma = e.gamma;
    lastRawAngle.beta = e.beta;

    gyroAngle.beta = e.beta;
    gyroAngle.gamma = e.gamma;
}

// Touch Drag Screen & Mouse Aiming (Geser menggunakan drag - Licin & Responsif)
let isDraggingAim = false;
let aimTouchId = null;
let lastTouchX = 0;
let lastTouchY = 0;
let hasMovedTouch = false;

rangeCard.addEventListener('touchstart', (e) => {
    requestGyroPermissionIfNeeded();
    // Kunci sentuhan jari pertama pada lesan sebagai pengarah bidikan
    if (aimTouchId === null && e.changedTouches.length > 0) {
        const touch = e.changedTouches[0];
        aimTouchId = touch.identifier;
        isDraggingAim = true;
        hasMovedTouch = false;
        lastTouchX = touch.clientX;
        lastTouchY = touch.clientY;
        dragVelocity.x = 0;
        dragVelocity.y = 0;
    }
}, { passive: false });

rangeCard.addEventListener('touchmove', (e) => {
    if (!isDraggingAim || aimTouchId === null) return;
    for (let i = 0; i < e.changedTouches.length; i++) {
        const touch = e.changedTouches[i];
        if (touch.identifier === aimTouchId) {
            const dx = touch.clientX - lastTouchX;
            const dy = touch.clientY - lastTouchY;
            lastTouchX = touch.clientX;
            lastTouchY = touch.clientY;

            if (Math.abs(dx) > 1.5 || Math.abs(dy) > 1.5) {
                hasMovedTouch = true;
            }
            const DRAG_SENSITIVITY = 1.55; // Sensitifitas geser ditingkatkan agar lebih gesit & presisi
            mousePos.x = Math.max(25, Math.min(cWidth - 25, mousePos.x + dx * DRAG_SENSITIVITY));
            mousePos.y = Math.max(25, Math.min(cHeight - 25, mousePos.y + dy * DRAG_SENSITIVITY));

            // Rekam akselerasi untuk inersia licin saat dilepas & sway dinamis
            dragVelocity.x += ((dx * DRAG_SENSITIVITY) - dragVelocity.x) * 0.45;
            dragVelocity.y += ((dy * DRAG_SENSITIVITY) - dragVelocity.y) * 0.45;
            break;
        }
    }
}, { passive: false });

const handleAimTouchEnd = (e) => {
    if (aimTouchId === null) return;
    for (let i = 0; i < e.changedTouches.length; i++) {
        const touch = e.changedTouches[i];
        if (touch.identifier === aimTouchId) {
            isDraggingAim = false;
            aimTouchId = null;
            if (!hasMovedTouch && sessionActive) {
                shootTarget();
            }
            break;
        }
    }
};

rangeCard.addEventListener('touchend', handleAimTouchEnd, { passive: false });
rangeCard.addEventListener('touchcancel', handleAimTouchEnd, { passive: false });

// Desktop Mouse Controls (Licin & Berbobot)
rangeCard.addEventListener('mousemove', (e) => {
    const rect = rangeCard.getBoundingClientRect();
    const targetX = e.clientX - rect.left;
    const targetY = e.clientY - rect.top;
    const dx = targetX - mousePos.x;
    const dy = targetY - mousePos.y;
    dragVelocity.x += (dx * 0.50 - dragVelocity.x) * 0.50;
    dragVelocity.y += (dy * 0.50 - dragVelocity.y) * 0.50;
    mousePos.x = Math.max(25, Math.min(cWidth - 25, targetX));
    mousePos.y = Math.max(25, Math.min(cHeight - 25, targetY));
});

rangeCard.addEventListener('mousedown', (e) => {
    e.preventDefault();
    if (sessionActive) {
        shootTarget();
    }
});

// Keyboard Controls (Space / Enter to Shoot)
window.addEventListener('keydown', (e) => {
    const tag = document.activeElement ? document.activeElement.tagName.toLowerCase() : '';
    if (tag === 'input' || tag === 'textarea' || tag === 'select') return;

    if (e.code === 'Space' || e.code === 'Enter') {
        if (sessionActive && isSimulatorVisible) {
            e.preventDefault();
            shootTarget();
        }
    }
});

// Animation Loop with Visibility & IntersectionObserver optimization
let isSimulatorVisible = true;
let isAnimating = false;

function checkAndStartAnimation() {
    if (isSimulatorVisible && !document.hidden && !isAnimating) {
        isAnimating = true;
        requestAnimationFrame(animate);
    }
}

function animate() {
    if (!isSimulatorVisible || document.hidden) {
        isAnimating = false;
        return;
    }
    updateSightsAndTremor();
    requestAnimationFrame(animate);
}

// Pause when browser tab is inactive
document.addEventListener('visibilitychange', () => {
    if (!document.hidden && isSimulatorVisible) {
        checkAndStartAnimation();
    }
});

// Pause when off-screen using IntersectionObserver
if ('IntersectionObserver' in window && rangeCard) {
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            isSimulatorVisible = entry.isIntersecting;
            if (isSimulatorVisible) {
                checkAndStartAnimation();
            }
        });
    }, { threshold: 0.05 });
    observer.observe(rangeCard);
}

function initSimulatorApp() {
    if (window.lucide) lucide.createIcons();
    resizeCanvas();
    updateScoreboardUI();
    updateGyroUI();
    if (typeof window !== 'undefined' && window.DeviceOrientationEvent && typeof window.DeviceOrientationEvent.requestPermission !== 'function') {
        window.addEventListener('deviceorientation', handleDeviceOrientation, { passive: true });
        hasGyroSensor = true;
    }
    checkAndStartAnimation();
}

if (document.readyState === 'loading') {
    window.addEventListener('DOMContentLoaded', initSimulatorApp);
} else {
    initSimulatorApp();
}
</script>