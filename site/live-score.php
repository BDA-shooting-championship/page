<?php
$pageTitle = 'Live Score — BDA Shooting Championship 2026';
$currentPage = 'live-score';
require_once __DIR__ . '/includes/header.php';
?>

<div class="py-12 bg-gray-50 min-h-screen" x-data="liveScoreApp()" x-init="initScores()">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header & Live Indicator -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8">
            <div class="flex items-center gap-4">
                <div class="flex items-center gap-2.5 shrink-0">
                    <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-full bg-white p-1.5 shadow-md border border-gray-100 flex items-center justify-center shrink-0">
                        <img src="/assets/logo-bda.png" alt="Logo BDA 750" class="w-full h-full object-contain">
                    </div>
                    <span class="text-gray-400 font-bold text-xs select-none">✕</span>
                    <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-full bg-white p-1.5 shadow-md border border-gray-100 flex items-center justify-center shrink-0">
                        <img src="/assets/logo-championship.png" alt="Logo BSC 2026" class="w-full h-full object-contain">
                    </div>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="relative flex h-3 w-3">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-3 w-3 bg-red-500"></span>
                        </span>
                        <span class="text-xs uppercase font-bold tracking-widest text-red-600">Papan Skor Resmi</span>
                    </div>
                    <h1 class="font-display text-2xl sm:text-3xl font-bold mt-0.5 text-gray-900">
                        LIVE SCORE PAPAN PERTANDINGAN
                    </h1>
                    <p class="text-xs sm:text-sm text-gray-600">
                        Hasil dan perolehan poin terkini BDA Shooting Championship 2026
                    </p>
                </div>
            </div>

            <!-- Controls: Auto Refresh & Refresh Button -->
            <div class="flex items-center gap-3">
                <div class="flex items-center gap-2 bg-white px-3 py-2 rounded-xl border border-gray-200 text-xs">
                    <span class="text-gray-500">Update otomatis:</span>
                    <button type="button" @click="toggleAutoRefresh()" 
                            :class="autoRefresh ? 'bg-green-600 text-white' : 'bg-gray-200 text-gray-700'"
                            class="px-2 py-0.5 rounded text-[11px] font-bold uppercase transition"
                            x-text="autoRefresh ? 'Aktif' : 'Nonaktif'"></button>
                </div>
                <button type="button" @click="fetchScores(true)" :disabled="isLoading" 
                        class="p-2.5 bg-white hover:bg-gray-100 border border-gray-200 rounded-xl transition shadow-sm text-gray-700 flex items-center gap-1.5 text-xs font-semibold">
                    <i data-lucide="refresh-cw" class="w-4 h-4" :class="isLoading ? 'animate-spin' : ''"></i>
                    <span class="hidden sm:inline">Segarkan</span>
                </button>
            </div>
        </div>

        <!-- Category Selector Tabs -->
        <div class="flex border-b border-gray-200 mb-8 space-x-4">
            <button @click="activeTab = 'presisi'" 
                    :class="activeTab === 'presisi' ? 'border-copper-600 text-copper-600 border-b-2' : 'border-transparent text-gray-500 hover:text-gray-700'"
                    class="py-3 px-4 font-display text-lg font-bold transition flex items-center gap-2">
                <i data-lucide="crosshair" class="w-5 h-5"></i>
                Pistol Presisi 20M (Umum POLRI)
                <span class="text-xs px-2 py-0.5 rounded-full bg-gray-100 text-gray-600 font-sans" x-text="presisiScores.length"></span>
            </button>
            <button @click="activeTab = 'dueling'" 
                    :class="activeTab === 'dueling' ? 'border-copper-600 text-copper-600 border-b-2' : 'border-transparent text-gray-500 hover:text-gray-700'"
                    class="py-3 px-4 font-display text-lg font-bold transition flex items-center gap-2">
                <i data-lucide="swords" class="w-5 h-5"></i>
                Dueling Plat 15M
                <span class="text-xs px-2 py-0.5 rounded-full bg-gray-100 text-gray-600 font-sans" x-text="duelingMatches.length"></span>
            </button>
        </div>

        <!-- ================= TAB 1: PRESISI 20M ================= -->
        <div x-show="activeTab === 'presisi'" x-transition>
            
            <!-- Rules Banner -->
            <div class="mb-6 p-4 rounded-xl bg-copper-50 border border-copper-200 flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-xs">
                <span class="text-copper-800">
                    <strong>Ketentuan Penilaian Ring:</strong> Urutan juara ditentukan oleh <strong>Nilai</strong> tertinggi. Jika terdapat nilai kembar, penentuan pemenang diurutkan berdasarkan <strong>Inner X</strong> terbanyak sebagai tiebreaker. Setiap peluru yang mengenai X bernilai 0,1 poin dan tidak menambah akumulasi jumlah masuk.
                </span>
                <span class="text-gray-500 shrink-0" x-text="'Terakhir diperbarui: ' + lastUpdated"></span>
            </div>

            <!-- Leaderboard Table -->
            <div class="bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs sm:text-sm">
                        <thead class="bg-gray-100 uppercase font-bold text-gray-600 tracking-wider border-b border-gray-200">
                            <tr>
                                <th class="py-3.5 px-3 text-center w-14">Rank</th>
                                <th class="py-3.5 px-3">No. Peserta</th>
                                <th class="py-3.5 px-4">Nama Lengkap</th>
                                <th class="py-3.5 px-3">Kesatuan / Club</th>
                                <th class="py-3.5 px-2 text-center text-amber-700 bg-amber-50 font-extrabold" title="Inner X (Tiebreaker)">X</th>
                                <th class="py-3.5 px-2 text-center text-gray-700">10</th>
                                <th class="py-3.5 px-2 text-center text-gray-700">9</th>
                                <th class="py-3.5 px-2 text-center text-gray-700">8</th>
                                <th class="py-3.5 px-2 text-center text-gray-700">7</th>
                                <th class="py-3.5 px-2 text-center text-gray-700">6</th>
                                <th class="py-3.5 px-2 text-center text-gray-700">5</th>
                                <th class="py-3.5 px-2 text-center text-gray-700">4</th>
                                <th class="py-3.5 px-2 text-center text-gray-700">3</th>
                                <th class="py-3.5 px-2 text-center text-gray-700">2</th>
                                <th class="py-3.5 px-2 text-center text-gray-700">1</th>
                                <th class="py-3.5 px-3 text-center font-bold text-gray-800 bg-gray-50">Jml Masuk</th>
                                <th class="py-3.5 px-4 text-center font-extrabold text-copper-700 bg-copper-50">Nilai</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <template x-for="(score, index) in presisiScores" :key="score.registration_id || index">
                                <tr :class="{
                                    'bg-amber-50/60 font-semibold': index === 0,
                                    'bg-gray-50/80': index === 1,
                                    'bg-orange-50/40': index === 2,
                                    'hover:bg-gray-50': index > 2
                                }" class="transition">
                                    <!-- Rank with Badges -->
                                    <td class="py-3 px-3 text-center">
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
                                    
                                    <td class="py-3 px-3 font-mono font-bold text-copper-600" x-text="score.no_peserta || '-'"></td>
                                    <td class="py-3 px-4">
                                        <span class="font-bold text-gray-900" x-text="score.nama"></span>
                                    </td>
                                    <td class="py-3 px-3 text-gray-600 text-xs" x-text="score.satuan || '-'"></td>
                                    
                                    <!-- Inner X -->
                                    <td class="py-3 px-2 text-center font-mono font-extrabold text-amber-700 bg-amber-50/40" x-text="score.ring_x"></td>

                                    <!-- Ring 10 - 1 -->
                                    <td class="py-3 px-2 text-center font-mono text-xs text-gray-800" x-text="score.ring_10"></td>
                                    <td class="py-3 px-2 text-center font-mono text-xs text-gray-800" x-text="score.ring_9"></td>
                                    <td class="py-3 px-2 text-center font-mono text-xs text-gray-800" x-text="score.ring_8"></td>
                                    <td class="py-3 px-2 text-center font-mono text-xs text-gray-800" x-text="score.ring_7"></td>
                                    <td class="py-3 px-2 text-center font-mono text-xs text-gray-700" x-text="score.ring_6"></td>
                                    <td class="py-3 px-2 text-center font-mono text-xs text-gray-700" x-text="score.ring_5"></td>
                                    <td class="py-3 px-2 text-center font-mono text-xs text-gray-700" x-text="score.ring_4"></td>
                                    <td class="py-3 px-2 text-center font-mono text-xs text-gray-700" x-text="score.ring_3"></td>
                                    <td class="py-3 px-2 text-center font-mono text-xs text-gray-700" x-text="score.ring_2"></td>
                                    <td class="py-3 px-2 text-center font-mono text-xs text-gray-700" x-text="score.ring_1"></td>
                                    
                                    <!-- Jumlah Masuk -->
                                    <td class="py-3 px-3 text-center font-mono font-bold text-gray-800 bg-gray-50/50" x-text="score.jumlah_masuk"></td>
                                    
                                    <!-- Nilai -->
                                    <td class="py-3 px-4 text-center font-mono font-extrabold text-base text-copper-700 bg-copper-50/40" x-text="score.nilai"></td>
                                </tr>
                            </template>
                            <template x-if="presisiScores.length === 0">
                                <tr>
                                    <td colspan="17" class="py-12 text-center text-gray-500">
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
            <div class="mb-6 p-4 rounded-xl bg-copper-50 border border-copper-200 flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-xs">
                <span class="text-copper-800">
                    <strong>Sistem Eliminasi (Gugur):</strong> Pertandingan head-to-head 5 plat + 1 popper (jarak 15M). Amunisi 10 butir (5 plat + 1 popper + 4 cadangan). Pemenang setiap duel berhak melaju ke babak berikutnya hingga Babak Final.
                </span>
                <span class="text-gray-500 shrink-0" x-text="'Terakhir diperbarui: ' + lastUpdated"></span>
            </div>

            <!-- Dueling Category Selector Pills -->
            <div class="flex flex-wrap items-center justify-between gap-3 mb-6 p-3 rounded-2xl bg-white border border-gray-200 shadow-sm">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="text-xs font-bold text-gray-700 mr-1">Kelas Dueling:</span>
                    <button type="button" @click="duelingCategory = 'umum'"
                            :class="duelingCategory === 'umum' ? 'bg-copper-600 text-white shadow-md font-bold' : 'bg-gray-100 text-gray-700 hover:bg-gray-200 font-medium'"
                            class="px-4 py-2 rounded-xl text-xs sm:text-sm transition flex items-center gap-2 cursor-pointer">
                        <i data-lucide="shield" class="w-4 h-4"></i>
                        <span>Kelas Umum POLRI (15M)</span>
                        <span class="ml-1 text-[11px] px-2 py-0.5 rounded-full" :class="duelingCategory === 'umum' ? 'bg-white/20 text-white' : 'bg-gray-200 text-gray-600'" x-text="duelingMatches.filter(m => (m.category || 'umum') === 'umum').length"></span>
                    </button>
                    <button type="button" @click="duelingCategory = 'bda'"
                            :class="duelingCategory === 'bda' ? 'bg-copper-600 text-white shadow-md font-bold' : 'bg-gray-100 text-gray-700 hover:bg-gray-200 font-medium'"
                            class="px-4 py-2 rounded-xl text-xs sm:text-sm transition flex items-center gap-2 cursor-pointer">
                        <i data-lucide="award" class="w-4 h-4"></i>
                        <span>Khusus BDA Korbrimob POLRI (15M)</span>
                        <span class="ml-1 text-[11px] px-2 py-0.5 rounded-full" :class="duelingCategory === 'bda' ? 'bg-white/20 text-white' : 'bg-gray-200 text-gray-600'" x-text="duelingMatches.filter(m => m.category === 'bda').length"></span>
                    </button>
                </div>
                <div class="text-xs text-gray-500 italic">
                    <span x-show="duelingCategory === 'umum'">Bagan Eliminasi Kelas Umum POLRI</span>
                    <span x-show="duelingCategory === 'bda'">Bagan Eliminasi Khusus Anggota BDA Korbrimob</span>
                </div>
            </div>

            <!-- View Mode Switcher -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6">
                <div class="flex items-center gap-2">
                    <span class="text-xs font-bold text-gray-700">Tampilan Bagan:</span>
                    <div class="inline-flex rounded-xl p-1 bg-gray-200/80 border border-gray-300">
                        <button type="button" @click="duelingViewMode = 'bracket'" 
                                :class="duelingViewMode === 'bracket' ? 'bg-white shadow text-copper-700 font-bold' : 'text-gray-600 hover:text-gray-900'"
                                class="px-3 py-1 text-xs rounded-lg transition flex items-center gap-1.5 cursor-pointer">
                            <i data-lucide="git-merge" class="w-3.5 h-3.5"></i>
                            <span>Bagan Turnamen (Tree)</span>
                        </button>
                        <button type="button" @click="duelingViewMode = 'list'" 
                                :class="duelingViewMode === 'list' ? 'bg-white shadow text-copper-700 font-bold' : 'text-gray-600 hover:text-gray-900'"
                                class="px-3 py-1 text-xs rounded-lg transition flex items-center gap-1.5 cursor-pointer">
                            <i data-lucide="layout-grid" class="w-3.5 h-3.5"></i>
                            <span>Kartu Per Babak</span>
                        </button>
                    </div>
                </div>
                <div class="text-[11px] text-gray-500 flex items-center gap-3">
                    <span class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-full bg-emerald-500 inline-block"></span> Pemenang</span>
                    <span class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-full bg-red-500 inline-block animate-pulse"></span> Live Match</span>
                </div>
            </div>

            <!-- Round Quick Navigator Bar -->
            <div x-show="groupedDuelingMatches.length > 0" class="flex flex-wrap items-center gap-1.5 mb-4 p-2 bg-white rounded-xl border border-gray-200 shadow-sm text-xs">
                <span class="text-gray-500 font-semibold px-2 flex items-center gap-1">
                    <i data-lucide="layers" class="w-3.5 h-3.5 text-copper-600"></i>
                    <span>Fokus Babak:</span>
                </span>
                <button type="button" @click="activeDuelingRound = 'all'"
                        :class="activeDuelingRound === 'all' ? 'bg-copper-600 text-white font-bold shadow-sm' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                        class="px-2.5 py-1 rounded-lg transition cursor-pointer">
                    Semua Babak
                </button>
                <template x-for="r in groupedDuelingMatches" :key="r.name">
                    <button type="button" @click="activeDuelingRound = r.name"
                            :class="activeDuelingRound === r.name ? 'bg-copper-600 text-white font-bold shadow-sm' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                            class="px-2.5 py-1 rounded-lg transition cursor-pointer flex items-center gap-1">
                        <span x-text="r.name"></span>
                        <span class="text-[10px] opacity-75 font-mono" x-text="'(' + r.matches.length + ')'"></span>
                    </button>
                </template>
            </div>

            <!-- ================= VIEW 1: TOURNAMENT BRACKET TREE ================= -->
            <div x-show="duelingViewMode === 'bracket' && groupedDuelingMatches.length > 0" class="overflow-x-auto pb-6">
                <div class="w-max min-w-full flex items-stretch gap-6 justify-start">
                    <template x-for="round in groupedDuelingMatches" :key="round.name">
                        <div x-show="activeDuelingRound === 'all' || activeDuelingRound === round.name" class="w-[280px] shrink-0 flex flex-col">
                            <!-- Round Header -->
                            <div class="mb-4 text-center pb-2 border-b-2 border-copper-600 bg-white rounded-t-xl pt-2 shadow-sm">
                                <h4 class="font-display font-bold text-gray-900 text-sm tracking-wide uppercase" x-text="round.name"></h4>
                                <span class="text-[10px] text-gray-500 font-semibold" x-text="round.matches.length + ' Match'"></span>
                            </div>

                            <!-- Matches in Round -->
                            <div class="flex-1 flex flex-col justify-around gap-4 py-2">
                                <template x-for="match in round.matches" :key="match.id">
                                    <div class="bg-white rounded-xl border shadow-sm transition hover:shadow-md relative overflow-hidden"
                                         :class="{
                                             'border-red-400 ring-2 ring-red-100': match.match_status === 'live',
                                             'border-emerald-300': match.match_status === 'finished',
                                             'border-gray-200': match.match_status === 'upcoming'
                                         }">
                                        <!-- Match Badge -->
                                        <div class="px-3 py-1.5 bg-gray-50 border-b border-gray-100 flex items-center justify-between text-[11px]">
                                            <span class="font-mono font-bold text-copper-700" x-text="'Match #' + match.match_number"></span>
                                            <span :class="{
                                                'bg-gray-100 text-gray-600': match.match_status === 'upcoming',
                                                'bg-red-500 text-white font-bold animate-pulse': match.match_status === 'live',
                                                'bg-emerald-100 text-emerald-800 font-semibold': match.match_status === 'finished'
                                            }" class="px-1.5 py-0.5 rounded text-[9px] uppercase tracking-wider" x-text="match.match_status"></span>
                                        </div>

                                        <!-- Participant 1 -->
                                        <div class="p-2.5 flex items-center justify-between transition border-b border-gray-100 text-xs"
                                             :class="(match.winner_id && (match.winner_id === match.participant_1_id || match.winner_id === match.participant_1_name)) ? 'bg-emerald-50 text-emerald-950 font-bold' : 'text-gray-800'">
                                            <div class="truncate mr-2">
                                                <div class="flex items-center gap-1">
                                                    <template x-if="match.winner_id && (match.winner_id === match.participant_1_id || match.winner_id === match.participant_1_name)">
                                                        <span class="text-amber-500 font-bold text-xs">👑</span>
                                                    </template>
                                                    <span class="truncate" :class="(match.winner_id && (match.winner_id === match.participant_1_id || match.winner_id === match.participant_1_name)) ? 'font-bold text-emerald-900' : ''" x-text="match.participant_1_name || 'TBD'"></span>
                                                </div>
                                                <div class="text-[10px] text-gray-400 truncate" x-text="match.participant_1_satuan || '-'"></div>
                                            </div>
                                            <template x-if="match.winner_id && (match.winner_id === match.participant_1_id || match.winner_id === match.participant_1_name)">
                                                <span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 font-bold text-[10px] tracking-wider shrink-0 border border-emerald-300">
                                                    MENANG
                                                </span>
                                            </template>
                                            <template x-if="match.match_status === 'finished' && (!match.winner_id || (match.winner_id !== match.participant_1_id && match.winner_id !== match.participant_1_name))">
                                                <span class="text-[10px] text-gray-400 shrink-0">Kalah</span>
                                            </template>
                                        </div>

                                        <!-- Participant 2 -->
                                        <div class="p-2.5 flex items-center justify-between transition text-xs"
                                             :class="(match.winner_id && (match.winner_id === match.participant_2_id || match.winner_id === match.participant_2_name)) ? 'bg-emerald-50 text-emerald-950 font-bold' : 'text-gray-800'">
                                            <div class="truncate mr-2">
                                                <div class="flex items-center gap-1">
                                                    <template x-if="match.winner_id && (match.winner_id === match.participant_2_id || match.winner_id === match.participant_2_name)">
                                                        <span class="text-amber-500 font-bold text-xs">👑</span>
                                                    </template>
                                                    <span class="truncate" :class="(match.winner_id && (match.winner_id === match.participant_2_id || match.winner_id === match.participant_2_name)) ? 'font-bold text-emerald-900' : ''" x-text="match.participant_2_name || 'TBD'"></span>
                                                </div>
                                                <div class="text-[10px] text-gray-400 truncate" x-text="match.participant_2_satuan || '-'"></div>
                                            </div>
                                            <template x-if="match.winner_id && (match.winner_id === match.participant_2_id || match.winner_id === match.participant_2_name)">
                                                <span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 font-bold text-[10px] tracking-wider shrink-0 border border-emerald-300">
                                                    MENANG
                                                </span>
                                            </template>
                                            <template x-if="match.match_status === 'finished' && (!match.winner_id || (match.winner_id !== match.participant_2_id && match.winner_id !== match.participant_2_name))">
                                                <span class="text-[10px] text-gray-400 shrink-0">Kalah</span>
                                            </template>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- ================= VIEW 2: CARD GRID PER ROUND ================= -->
            <div x-show="duelingViewMode === 'list'">
                <template x-for="round in groupedDuelingMatches" :key="round.name">
                    <div x-show="activeDuelingRound === 'all' || activeDuelingRound === round.name" class="mb-8">
                        <div class="flex items-center gap-3 mb-4">
                            <span class="w-2.5 h-6 rounded bg-copper-600"></span>
                            <h3 class="font-display text-xl font-bold text-gray-900" x-text="round.name"></h3>
                            <span class="text-xs px-2.5 py-0.5 rounded-full bg-gray-200 text-gray-600 font-sans" x-text="round.matches.length + ' Match'"></span>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            <template x-for="match in round.matches" :key="match.id">
                                <div class="bg-white rounded-xl p-5 border border-gray-200 shadow-md relative overflow-hidden">
                                    <!-- Match Header Badge -->
                                    <div class="flex justify-between items-center mb-3 pb-2 border-b border-gray-100 text-xs">
                                        <span class="font-mono font-bold text-copper-600" x-text="'Match #' + match.match_number"></span>
                                        <span :class="{
                                            'bg-gray-100 text-gray-600': match.match_status === 'upcoming',
                                            'bg-red-100 text-red-700 animate-pulse font-bold': match.match_status === 'live',
                                            'bg-green-100 text-green-700 font-semibold': match.match_status === 'finished'
                                        }" class="px-2 py-0.5 rounded text-[10px] uppercase tracking-wider" x-text="match.match_status"></span>
                                    </div>

                                    <!-- Competitor 1 -->
                                    <div class="flex justify-between items-center p-2.5 rounded-lg mb-2 transition"
                                         :class="(match.winner_id && (match.winner_id === match.participant_1_id || match.winner_id === match.participant_1_name)) ? 'bg-green-50 border border-green-300 font-bold' : 'bg-gray-50'">
                                        <div class="truncate mr-2">
                                            <div class="flex items-center gap-1.5">
                                                <template x-if="match.winner_id && (match.winner_id === match.participant_1_id || match.winner_id === match.participant_1_name)">
                                                    <i data-lucide="crown" class="w-3.5 h-3.5 text-amber-500 shrink-0"></i>
                                                </template>
                                                <span class="font-bold text-sm text-gray-900 truncate" x-text="match.participant_1_name || 'TBD'"></span>
                                            </div>
                                            <span class="text-[11px] text-gray-500 block truncate" x-text="match.participant_1_satuan || '-'"></span>
                                        </div>
                                        <div class="text-right shrink-0">
                                            <template x-if="match.winner_id && (match.winner_id === match.participant_1_id || match.winner_id === match.participant_1_name)">
                                                <span class="px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-800 font-bold text-xs border border-emerald-300">
                                                    MENANG
                                                </span>
                                            </template>
                                            <template x-if="match.match_status === 'finished' && (!match.winner_id || (match.winner_id !== match.participant_1_id && match.winner_id !== match.participant_1_name))">
                                                <span class="text-xs text-gray-400">Kalah</span>
                                            </template>
                                        </div>
                                    </div>

                                    <!-- VS Divider -->
                                    <div class="text-center my-1">
                                        <span class="text-[10px] uppercase font-bold text-gray-400 tracking-widest">&bull; VS &bull;</span>
                                    </div>

                                    <!-- Competitor 2 -->
                                    <div class="flex justify-between items-center p-2.5 rounded-lg transition"
                                         :class="(match.winner_id && (match.winner_id === match.participant_2_id || match.winner_id === match.participant_2_name)) ? 'bg-green-50 border border-green-300 font-bold' : 'bg-gray-50'">
                                        <div class="truncate mr-2">
                                            <div class="flex items-center gap-1.5">
                                                <template x-if="match.winner_id && (match.winner_id === match.participant_2_id || match.winner_id === match.participant_2_name)">
                                                    <i data-lucide="crown" class="w-3.5 h-3.5 text-amber-500 shrink-0"></i>
                                                </template>
                                                <span class="font-bold text-sm text-gray-900 truncate" x-text="match.participant_2_name || 'TBD'"></span>
                                            </div>
                                            <span class="text-[11px] text-gray-500 block truncate" x-text="match.participant_2_satuan || '-'"></span>
                                        </div>
                                        <div class="text-right shrink-0">
                                            <template x-if="match.winner_id && (match.winner_id === match.participant_2_id || match.winner_id === match.participant_2_name)">
                                                <span class="px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-800 font-bold text-xs border border-emerald-300">
                                                    MENANG
                                                </span>
                                            </template>
                                            <template x-if="match.match_status === 'finished' && (!match.winner_id || (match.winner_id !== match.participant_2_id && match.winner_id !== match.participant_2_name))">
                                                <span class="text-xs text-gray-400">Kalah</span>
                                            </template>
                                        </div>
                                    </div>

                                </div>
                            </template>
                        </div>
                    </div>
                </template>
            </div>

            <template x-if="groupedDuelingMatches.length === 0">
                <div class="bg-white rounded-2xl p-12 text-center border border-gray-200 shadow-md">
                    <i data-lucide="swords" class="w-10 h-10 mx-auto mb-2 text-gray-400"></i>
                    <p class="font-semibold text-gray-800">Belum ada bagan pertandingan dueling plat yang disusun.</p>
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
        duelingViewMode: 'bracket',
        duelingCategory: 'umum',
        activeDuelingRound: 'all',
        presisiScores: [],
        duelingMatches: [],
        isLoading: false,
        autoRefresh: true,
        refreshTimer: null,
        lastUpdated: '-',

        initScores() {
            this.fetchScores();
            this.startAutoRefresh();
            this.$watch('duelingCategory', () => {
                setTimeout(() => { if (typeof lucide !== 'undefined') lucide.createIcons(); }, 50);
            });
            this.$watch('duelingViewMode', () => {
                setTimeout(() => { if (typeof lucide !== 'undefined') lucide.createIcons(); }, 50);
            });
            document.addEventListener('visibilitychange', () => {
                if (!document.hidden && this.autoRefresh) {
                    this.fetchScores(false);
                }
            });
        },

        startAutoRefresh() {
            if (this.refreshTimer) clearInterval(this.refreshTimer);
            this.refreshTimer = setInterval(() => {
                if (this.autoRefresh && !document.hidden) {
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

        get filteredDuelingMatches() {
            const cat = this.duelingCategory;
            return this.duelingMatches.filter(m => {
                const mCat = m.category || 'umum';
                return mCat === cat;
            });
        },

        get groupedDuelingMatches() {
            const groups = {};
            this.filteredDuelingMatches.forEach(m => {
                const round = m.round_name || 'Penyisihan';
                if (!groups[round]) {
                    groups[round] = { name: round, order: parseInt(m.round_order) || 99, matches: [] };
                }
                groups[round].matches.push(m);
            });
            const result = Object.values(groups);
            result.sort((a, b) => a.order - b.order);
            result.forEach(g => {
                g.matches.sort((a, b) => (parseInt(a.match_number) || 0) - (parseInt(b.match_number) || 0));
            });
            return result;
        }
    }
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
