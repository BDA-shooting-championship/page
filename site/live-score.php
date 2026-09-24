<?php
$pageTitle = 'Live Score — BDA Shooting Championship 2026';
$currentPage = 'live-score';
require_once __DIR__ . '/includes/header.php';
?>

<div class="py-12 bg-gray-50 dark:bg-gray-900 min-h-screen" x-data="liveScoreApp()" x-init="initScores()">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header & Live Indicator -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8">
            <div class="flex items-center gap-4">
                <div class="flex items-center gap-2.5 shrink-0">
                    <img src="/assets/logo-bda.png" alt="Logo BDA 750" class="h-14 w-14 object-contain drop-shadow-md">
                    <img src="/assets/logo-championship.png" alt="Logo BSC 2026" class="h-14 w-14 object-contain drop-shadow-md">
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="relative flex h-3 w-3">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-3 w-3 bg-red-500"></span>
                        </span>
                        <span class="text-xs uppercase font-bold tracking-widest text-red-600 dark:text-red-400">Papan Skor Resmi</span>
                    </div>
                    <h1 class="font-display text-2xl sm:text-3xl font-bold mt-0.5 text-gray-900 dark:text-white">
                        LIVE SCORE PAPAN PERTANDINGAN
                    </h1>
                    <p class="text-xs sm:text-sm text-gray-600 dark:text-gray-400">
                        Hasil dan perolehan poin terkini BDA Shooting Championship 2026
                    </p>
                </div>
            </div>

            <!-- Controls: Auto Refresh & Refresh Button -->
            <div class="flex items-center gap-3">
                <div class="flex items-center gap-2 bg-white dark:bg-gray-800 px-3 py-2 rounded-xl border border-gray-200 dark:border-gray-700 text-xs">
                    <span class="text-gray-500 dark:text-gray-400">Update otomatis:</span>
                    <button type="button" @click="toggleAutoRefresh()" 
                            :class="autoRefresh ? 'bg-green-600 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300'"
                            class="px-2 py-0.5 rounded text-[11px] font-bold uppercase transition"
                            x-text="autoRefresh ? 'Aktif' : 'Nonaktif'"></button>
                </div>
                <button type="button" @click="fetchScores(true)" :disabled="isLoading" 
                        class="p-2.5 bg-white dark:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700 border border-gray-200 dark:border-gray-700 rounded-xl transition shadow-sm text-gray-700 dark:text-gray-300 flex items-center gap-1.5 text-xs font-semibold">
                    <i data-lucide="refresh-cw" class="w-4 h-4" :class="isLoading ? 'animate-spin' : ''"></i>
                    <span class="hidden sm:inline">Segarkan</span>
                </button>
            </div>
        </div>

        <!-- Category Selector Tabs -->
        <div class="flex border-b border-gray-200 dark:border-gray-800 mb-8 space-x-4">
            <button @click="activeTab = 'presisi'" 
                    :class="activeTab === 'presisi' ? 'border-copper-600 text-copper-600 dark:text-copper-400 border-b-2' : 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300'"
                    class="py-3 px-4 font-display text-lg font-bold transition flex items-center gap-2">
                <i data-lucide="crosshair" class="w-5 h-5"></i>
                Pistol Presisi 20M
                <span class="text-xs px-2 py-0.5 rounded-full bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400 font-sans" x-text="presisiScores.length"></span>
            </button>
            <button @click="activeTab = 'dueling'" 
                    :class="activeTab === 'dueling' ? 'border-copper-600 text-copper-600 dark:text-copper-400 border-b-2' : 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300'"
                    class="py-3 px-4 font-display text-lg font-bold transition flex items-center gap-2">
                <i data-lucide="swords" class="w-5 h-5"></i>
                Dueling Plat
                <span class="text-xs px-2 py-0.5 rounded-full bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400 font-sans" x-text="duelingMatches.length"></span>
            </button>
        </div>

        <!-- ================= TAB 1: PRESISI 20M ================= -->
        <div x-show="activeTab === 'presisi'" x-transition>
            
            <!-- Rules Banner -->
            <div class="mb-6 p-4 rounded-xl bg-copper-50 dark:bg-copper-900/20 border border-copper-200 dark:border-copper-800/50 flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-xs">
                <span class="text-copper-800 dark:text-copper-300">
                    <strong>Ketentuan Perangkingan:</strong> Urutan juara ditentukan oleh <strong>Total Skor</strong> tertinggi. Jika terjadi nilai kembar (draw), penentuan pemenang diurutkan berdasarkan <strong>Jumlah Tembakan X (Inner-10)</strong> terbanyak.
                </span>
                <span class="text-gray-500 dark:text-gray-400 shrink-0" x-text="'Terakhir diperbarui: ' + lastUpdated"></span>
            </div>

            <!-- Leaderboard Table -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs sm:text-sm">
                        <thead class="bg-gray-100 dark:bg-gray-900/80 uppercase font-bold text-gray-600 dark:text-gray-400 tracking-wider border-b border-gray-200 dark:border-gray-700">
                            <tr>
                                <th class="py-3.5 px-4 text-center w-16">Peringkat</th>
                                <th class="py-3.5 px-4">No. Peserta</th>
                                <th class="py-3.5 px-4">Nama Lengkap</th>
                                <th class="py-3.5 px-4">Satuan / Club</th>
                                <th class="py-3.5 px-2 text-center text-gray-500">S1</th>
                                <th class="py-3.5 px-2 text-center text-gray-500">S2</th>
                                <th class="py-3.5 px-2 text-center text-gray-500">S3</th>
                                <th class="py-3.5 px-2 text-center text-gray-500">S4</th>
                                <th class="py-3.5 px-2 text-center text-gray-500">S5</th>
                                <th class="py-3.5 px-2 text-center text-gray-500">S6</th>
                                <th class="py-3.5 px-2 text-center text-gray-500">S7</th>
                                <th class="py-3.5 px-2 text-center text-gray-500">S8</th>
                                <th class="py-3.5 px-2 text-center text-gray-500">S9</th>
                                <th class="py-3.5 px-2 text-center text-gray-500">S10</th>
                                <th class="py-3.5 px-3 text-center font-bold text-copper-700 dark:text-copper-400">Jml X</th>
                                <th class="py-3.5 px-4 text-center font-bold text-copper-700 dark:text-copper-400 bg-copper-50/50 dark:bg-copper-900/20">Total Skor</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            <template x-for="(score, index) in presisiScores" :key="score.registration_id || index">
                                <tr :class="{
                                    'bg-amber-50/60 dark:bg-amber-950/20 font-semibold': index === 0,
                                    'bg-gray-50/80 dark:bg-gray-800/60': index === 1,
                                    'bg-orange-50/40 dark:bg-orange-950/10': index === 2,
                                    'hover:bg-gray-50 dark:hover:bg-gray-700/30': index > 2
                                }" class="transition">
                                    <!-- Rank with Badges -->
                                    <td class="py-3 px-4 text-center">
                                        <template x-if="index === 0">
                                            <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-amber-400 text-gray-900 font-bold text-xs shadow-sm">1</span>
                                        </template>
                                        <template x-if="index === 1">
                                            <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-gray-300 text-gray-800 font-bold text-xs shadow-sm">2</span>
                                        </template>
                                        <template x-if="index === 2">
                                            <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-amber-600 text-white font-bold text-xs shadow-sm">3</span>
                                        </template>
                                        <template x-if="index > 2">
                                            <span class="text-gray-500 font-mono text-sm" x-text="index + 1"></span>
                                        </template>
                                    </td>
                                    
                                    <td class="py-3 px-4 font-mono font-bold text-copper-600 dark:text-copper-400" x-text="score.no_peserta || '-'"></td>
                                    <td class="py-3 px-4">
                                        <span class="font-bold text-gray-900 dark:text-white" x-text="score.nama"></span>
                                    </td>
                                    <td class="py-3 px-4 text-gray-600 dark:text-gray-400 text-xs" x-text="score.satuan || '-'"></td>
                                    
                                    <!-- Seri 1 - 10 -->
                                    <td class="py-3 px-2 text-center font-mono text-xs text-gray-600 dark:text-gray-300" x-text="score.seri_1"></td>
                                    <td class="py-3 px-2 text-center font-mono text-xs text-gray-600 dark:text-gray-300" x-text="score.seri_2"></td>
                                    <td class="py-3 px-2 text-center font-mono text-xs text-gray-600 dark:text-gray-300" x-text="score.seri_3"></td>
                                    <td class="py-3 px-2 text-center font-mono text-xs text-gray-600 dark:text-gray-300" x-text="score.seri_4"></td>
                                    <td class="py-3 px-2 text-center font-mono text-xs text-gray-600 dark:text-gray-300" x-text="score.seri_5"></td>
                                    <td class="py-3 px-2 text-center font-mono text-xs text-gray-600 dark:text-gray-300" x-text="score.seri_6"></td>
                                    <td class="py-3 px-2 text-center font-mono text-xs text-gray-600 dark:text-gray-300" x-text="score.seri_7"></td>
                                    <td class="py-3 px-2 text-center font-mono text-xs text-gray-600 dark:text-gray-300" x-text="score.seri_8"></td>
                                    <td class="py-3 px-2 text-center font-mono text-xs text-gray-600 dark:text-gray-300" x-text="score.seri_9"></td>
                                    <td class="py-3 px-2 text-center font-mono text-xs text-gray-600 dark:text-gray-300" x-text="score.seri_10"></td>
                                    
                                    <!-- X Count -->
                                    <td class="py-3 px-3 text-center font-mono font-bold text-copper-600 dark:text-copper-400" x-text="score.x_count + 'x'"></td>
                                    
                                    <!-- Total Score -->
                                    <td class="py-3 px-4 text-center font-mono font-extrabold text-base text-gray-900 dark:text-white bg-copper-50/30 dark:bg-copper-900/10" x-text="score.total_score"></td>
                                </tr>
                            </template>
                            <template x-if="presisiScores.length === 0">
                                <tr>
                                    <td colspan="16" class="py-12 text-center text-gray-500">
                                        <i data-lucide="target" class="w-10 h-10 mx-auto mb-2 text-gray-400"></i>
                                        <p class="font-semibold">Belum ada skor pertandingan yang diinput.</p>
                                        <p class="text-xs text-gray-400 mt-1">Skor akan muncul saat pertandingan babak presisi berlangsung.</p>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- ================= TAB 2: DUELING PLAT ================= -->
        <div x-show="activeTab === 'dueling'" x-transition>
            
            <!-- Rules Banner -->
            <div class="mb-6 p-4 rounded-xl bg-copper-50 dark:bg-copper-900/20 border border-copper-200 dark:border-copper-800/50 flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-xs">
                <span class="text-copper-800 dark:text-copper-300">
                    <strong>Sistem Eliminasi (Gugur):</strong> Pertandingan head-to-head 5 plat + 1 popper. Peserta dengan <strong>catatan waktu tercepat</strong> berhak melaju ke babak berikutnya hingga Babak Final.
                </span>
                <span class="text-gray-500 dark:text-gray-400 shrink-0" x-text="'Terakhir diperbarui: ' + lastUpdated"></span>
            </div>

            <!-- Matches by Round -->
            <template x-for="round in groupedDuelingMatches" :key="round.name">
                <div class="mb-8">
                    <div class="flex items-center gap-3 mb-4">
                        <span class="w-2.5 h-6 rounded bg-copper-600"></span>
                        <h3 class="font-display text-xl font-bold text-gray-900 dark:text-white" x-text="round.name"></h3>
                        <span class="text-xs px-2.5 py-0.5 rounded-full bg-gray-200 dark:bg-gray-700 text-gray-600 dark:text-gray-300 font-sans" x-text="round.matches.length + ' Match'"></span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <template x-for="match in round.matches" :key="match.id">
                            <div class="bg-white dark:bg-gray-800 rounded-xl p-5 border border-gray-200 dark:border-gray-700 shadow-md relative overflow-hidden">
                                
                                <!-- Match Header Badge -->
                                <div class="flex justify-between items-center mb-3 pb-2 border-b border-gray-100 dark:border-gray-700 text-xs">
                                    <span class="font-mono font-bold text-copper-600 dark:text-copper-400" x-text="'Match #' + match.match_number"></span>
                                    <span :class="{
                                        'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300': match.match_status === 'upcoming',
                                        'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300 animate-pulse font-bold': match.match_status === 'live',
                                        'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-300 font-semibold': match.match_status === 'finished'
                                    }" class="px-2 py-0.5 rounded text-[10px] uppercase tracking-wider" x-text="match.match_status"></span>
                                </div>

                                <!-- Competitor 1 -->
                                <div class="flex justify-between items-center p-2.5 rounded-lg mb-2 transition"
                                     :class="match.winner_id && match.winner_id === match.participant_1_id ? 'bg-green-50 dark:bg-green-950/30 border border-green-300 dark:border-green-800' : 'bg-gray-50 dark:bg-gray-900/40'">
                                    <div class="truncate mr-2">
                                        <div class="flex items-center gap-1.5">
                                            <template x-if="match.winner_id && match.winner_id === match.participant_1_id">
                                                <i data-lucide="crown" class="w-3.5 h-3.5 text-amber-500 shrink-0"></i>
                                            </template>
                                            <span class="font-bold text-sm text-gray-900 dark:text-white truncate" x-text="match.participant_1_name || 'TBD'"></span>
                                        </div>
                                        <span class="text-[11px] text-gray-500 block truncate" x-text="match.participant_1_satuan || '-'"></span>
                                    </div>
                                    <div class="text-right shrink-0">
                                        <span class="font-mono font-bold text-sm" 
                                              :class="match.time_1 ? 'text-gray-900 dark:text-white' : 'text-gray-400'"
                                              x-text="match.time_1 ? match.time_1 + 's' : '-'"></span>
                                    </div>
                                </div>

                                <!-- VS Divider -->
                                <div class="text-center my-1">
                                    <span class="text-[10px] uppercase font-bold text-gray-400 tracking-widest">&bull; VS &bull;</span>
                                </div>

                                <!-- Competitor 2 -->
                                <div class="flex justify-between items-center p-2.5 rounded-lg transition"
                                     :class="match.winner_id && match.winner_id === match.participant_2_id ? 'bg-green-50 dark:bg-green-950/30 border border-green-300 dark:border-green-800' : 'bg-gray-50 dark:bg-gray-900/40'">
                                    <div class="truncate mr-2">
                                        <div class="flex items-center gap-1.5">
                                            <template x-if="match.winner_id && match.winner_id === match.participant_2_id">
                                                <i data-lucide="crown" class="w-3.5 h-3.5 text-amber-500 shrink-0"></i>
                                            </template>
                                            <span class="font-bold text-sm text-gray-900 dark:text-white truncate" x-text="match.participant_2_name || 'TBD'"></span>
                                        </div>
                                        <span class="text-[11px] text-gray-500 block truncate" x-text="match.participant_2_satuan || '-'"></span>
                                    </div>
                                    <div class="text-right shrink-0">
                                        <span class="font-mono font-bold text-sm" 
                                              :class="match.time_2 ? 'text-gray-900 dark:text-white' : 'text-gray-400'"
                                              x-text="match.time_2 ? match.time_2 + 's' : '-'"></span>
                                    </div>
                                </div>

                            </div>
                        </template>
                    </div>
                </div>
            </template>

            <template x-if="groupedDuelingMatches.length === 0">
                <div class="bg-white dark:bg-gray-800 rounded-2xl p-12 text-center border border-gray-200 dark:border-gray-700 shadow-md">
                    <i data-lucide="swords" class="w-10 h-10 mx-auto mb-2 text-gray-400"></i>
                    <p class="font-semibold text-gray-800 dark:text-gray-200">Belum ada bagan pertandingan dueling plat yang disusun.</p>
                    <p class="text-xs text-gray-400 mt-1">Bagan eliminasi akan tampil setelah sesi undian (drawing) selesai dilakukan oleh panitia.</p>
                </div>
            </template>

        </div>

    </div>
</div>

<script>
function liveScoreApp() {
    return {
        activeTab: 'presisi',
        presisiScores: [],
        duelingMatches: [],
        isLoading: false,
        autoRefresh: true,
        refreshTimer: null,
        lastUpdated: '-',

        initScores() {
            this.fetchScores();
            this.startAutoRefresh();
        },

        startAutoRefresh() {
            if (this.refreshTimer) clearInterval(this.refreshTimer);
            this.refreshTimer = setInterval(() => {
                if (this.autoRefresh) {
                    this.fetchScores(false);
                }
            }, 10000); // 10 detik
        },

        toggleAutoRefresh() {
            this.autoRefresh = !this.autoRefresh;
            if (this.autoRefresh) {
                this.startAutoRefresh();
            } else if (this.refreshTimer) {
                clearInterval(this.refreshTimer);
            }
        },

        async fetchScores(manual = false) {
            if (manual) this.isLoading = true;
            try {
                const res = await fetch('/api/public-scores.php');
                const data = await res.json();
                if (data.success) {
                    this.presisiScores = data.presisi || [];
                    this.duelingMatches = data.dueling || [];
                    const now = new Date();
                    this.lastUpdated = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' }) + ' WIB';
                }
            } catch (e) {
                console.error('Gagal mengambil data live score:', e);
            } finally {
                if (manual) this.isLoading = false;
                setTimeout(() => { if (typeof lucide !== 'undefined') lucide.createIcons(); }, 100);
            }
        },

        get groupedDuelingMatches() {
            const groups = {};
            const order = ['Penyisihan', 'Perempat Final', 'Semifinal', 'Perebutan Juara 3', 'Final'];
            this.duelingMatches.forEach(m => {
                const round = m.round_name || 'Penyisihan';
                if (!groups[round]) groups[round] = [];
                groups[round].push(m);
            });
            const result = [];
            order.forEach(name => {
                if (groups[name] && groups[name].length > 0) {
                    result.push({ name, matches: groups[name] });
                }
            });
            // Include any custom round name
            Object.keys(groups).forEach(name => {
                if (!order.includes(name)) {
                    result.push({ name, matches: groups[name] });
                }
            });
            return result;
        }
    }
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
