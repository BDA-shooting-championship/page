<?php
session_start();
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: 0");
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';

// Auth & Permission check
if (!isAdminLoggedIn()) {
    header('Location: /admin/index.php');
    exit;
}

if (!hasPermission('scores')) {
    die('Akses ditolak: Akun Anda tidak memiliki izin untuk mengakses menu Live Skor.');
}

$pageTitle = 'Kelola Live Score — Admin BSC 2026';
$currentPage = 'admin-scores';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="py-8 bg-gray-50 min-h-screen" x-data="adminScoresApp()" x-init="initData()">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header Bar -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8 pb-6 border-b border-gray-200">
            <div>
                <h1 class="font-display text-2xl sm:text-3xl font-bold text-gray-900">
                    Manajemen Skor &amp; Pertandingan
                </h1>
            </div>

            <div class="flex items-center gap-3">
                <a href="/admin/index.php" class="px-4 py-2 bg-white border border-gray-200 rounded-xl text-xs font-semibold hover:bg-gray-50 transition flex items-center gap-1.5 shadow-sm">
                    <i data-lucide="users" class="w-4 h-4"></i> Dashboard Admin
                </a>
                <a href="/live-score.php" target="_blank" class="px-4 py-2 bg-copper-600 hover:bg-copper-700 text-white rounded-xl text-xs font-semibold transition flex items-center gap-1.5 shadow-md">
                    <i data-lucide="external-link" class="w-4 h-4"></i> Lihat Live Score Publik
                </a>
            </div>
        </div>

        <!-- Alert Notification -->
        <div x-show="alertMessage" x-cloak class="mb-6 p-4 rounded-xl border text-xs font-semibold flex items-center justify-between"
             :class="alertType === 'success' ? 'bg-green-50 border-green-200 text-green-700' : 'bg-red-50 border-red-200 text-red-700'">
            <span x-text="alertMessage"></span>
            <button @click="alertMessage = null" class="text-gray-400 hover:text-gray-600">&times;</button>
        </div>

        <!-- Navigation Tabs -->
        <div class="flex border-b border-gray-200 mb-6 space-x-4">
            <button @click="activeTab = 'presisi'" 
                    :class="activeTab === 'presisi' ? 'border-copper-600 text-copper-600 border-b-2 font-bold' : 'border-transparent text-gray-500 hover:text-gray-700'"
                    class="py-3 px-4 font-display text-base transition flex items-center gap-2">
                <i data-lucide="crosshair" class="w-4 h-4"></i>
                Input Skor Presisi 20M
            </button>
            <button @click="activeTab = 'dueling'" 
                    :class="activeTab === 'dueling' ? 'border-copper-600 text-copper-600 border-b-2 font-bold' : 'border-transparent text-gray-500 hover:text-gray-700'"
                    class="py-3 px-4 font-display text-base transition flex items-center gap-2">
                <i data-lucide="swords" class="w-4 h-4"></i>
                Bagan Pertandingan Dueling Plat
            </button>
        </div>

        <!-- ================= TAB 1: PRESISI 20M EDITOR ================= -->
        <div x-show="activeTab === 'presisi'" x-transition>
            
            <div class="flex items-center justify-end mb-4">
                <button type="button" @click="syncVerifiedParticipants()" :disabled="isSyncing"
                        class="px-4 py-2 bg-white border border-gray-200 hover:bg-gray-50 text-xs font-semibold rounded-xl transition flex items-center gap-1.5 shrink-0 shadow-sm">
                    <i data-lucide="user-plus" class="w-4 h-4 text-copper-600"></i>
                    <span x-text="isSyncing ? 'Menyinkronkan...' : 'Sinkron Peserta Verified'"></span>
                </button>
            </div>

            <!-- Table of Scores -->
            <div class="bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-gray-100 uppercase font-bold text-gray-600 border-b border-gray-200">
                            <tr>
                                <th class="py-3 px-3 w-10 text-center">Rank</th>
                                <th class="py-3 px-3 w-20">No. Peserta</th>
                                <th class="py-3 px-3">Nama &amp; Satuan</th>
                                <th class="py-3 px-1 text-center w-11 bg-amber-50 text-amber-700 font-extrabold" title="Inner X">X</th>
                                <th class="py-3 px-1 text-center w-10">10</th>
                                <th class="py-3 px-1 text-center w-10">9</th>
                                <th class="py-3 px-1 text-center w-10">8</th>
                                <th class="py-3 px-1 text-center w-10">7</th>
                                <th class="py-3 px-1 text-center w-10">6</th>
                                <th class="py-3 px-1 text-center w-10">5</th>
                                <th class="py-3 px-1 text-center w-10">4</th>
                                <th class="py-3 px-1 text-center w-10">3</th>
                                <th class="py-3 px-1 text-center w-10">2</th>
                                <th class="py-3 px-1 text-center w-10">1</th>
                                <th class="py-3 px-2 text-center w-14 font-bold bg-gray-50">Jml Masuk</th>
                                <th class="py-3 px-3 text-center w-16 font-extrabold text-copper-700 bg-copper-50/50">Nilai</th>
                                <th class="py-3 px-3 text-center w-20">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <template x-for="(item, index) in presisiList" :key="item.id || item.registration_id">
                                <tr class="hover:bg-gray-50/50 transition">
                                    <td class="py-2 px-3 text-center font-bold text-gray-500" x-text="index + 1"></td>
                                    <td class="py-2 px-3 font-mono font-bold text-copper-600" x-text="item.no_peserta || '-'"></td>
                                    <td class="py-2 px-3">
                                        <div class="font-bold text-gray-900" x-text="item.nama"></div>
                                        <div class="text-[11px] text-gray-500" x-text="item.satuan"></div>
                                    </td>

                                    <!-- X Input -->
                                    <td class="py-2 px-1 text-center bg-amber-50/30">
                                        <input type="number" min="0" max="10" 
                                               x-model.number="item.ring_x"
                                               @input="calculateNilai(item); item._saved = false;"
                                               class="w-9 text-center py-1 rounded bg-amber-50 border border-amber-300 text-xs font-mono font-bold text-amber-800 focus:ring-1 focus:ring-amber-500 focus:outline-none">
                                    </td>

                                    <!-- Ring 10 down to 1 Inputs (Static & Robust) -->
                                    <td class="py-2 px-1 text-center">
                                        <input type="number" min="0" max="10" x-model.number="item.ring_10" @input="calculateNilai(item); item._saved = false;"
                                               class="w-9 text-center py-1 rounded bg-gray-50 border border-gray-300 text-xs font-mono font-bold focus:ring-1 focus:ring-copper-500 focus:outline-none">
                                    </td>
                                    <td class="py-2 px-1 text-center">
                                        <input type="number" min="0" max="10" x-model.number="item.ring_9" @input="calculateNilai(item); item._saved = false;"
                                               class="w-9 text-center py-1 rounded bg-gray-50 border border-gray-300 text-xs font-mono font-bold focus:ring-1 focus:ring-copper-500 focus:outline-none">
                                    </td>
                                    <td class="py-2 px-1 text-center">
                                        <input type="number" min="0" max="10" x-model.number="item.ring_8" @input="calculateNilai(item); item._saved = false;"
                                               class="w-9 text-center py-1 rounded bg-gray-50 border border-gray-300 text-xs font-mono font-bold focus:ring-1 focus:ring-copper-500 focus:outline-none">
                                    </td>
                                    <td class="py-2 px-1 text-center">
                                        <input type="number" min="0" max="10" x-model.number="item.ring_7" @input="calculateNilai(item); item._saved = false;"
                                               class="w-9 text-center py-1 rounded bg-gray-50 border border-gray-300 text-xs font-mono font-bold focus:ring-1 focus:ring-copper-500 focus:outline-none">
                                    </td>
                                    <td class="py-2 px-1 text-center">
                                        <input type="number" min="0" max="10" x-model.number="item.ring_6" @input="calculateNilai(item); item._saved = false;"
                                               class="w-9 text-center py-1 rounded bg-gray-50 border border-gray-300 text-xs font-mono font-bold focus:ring-1 focus:ring-copper-500 focus:outline-none">
                                    </td>
                                    <td class="py-2 px-1 text-center">
                                        <input type="number" min="0" max="10" x-model.number="item.ring_5" @input="calculateNilai(item); item._saved = false;"
                                               class="w-9 text-center py-1 rounded bg-gray-50 border border-gray-300 text-xs font-mono font-bold focus:ring-1 focus:ring-copper-500 focus:outline-none">
                                    </td>
                                    <td class="py-2 px-1 text-center">
                                        <input type="number" min="0" max="10" x-model.number="item.ring_4" @input="calculateNilai(item); item._saved = false;"
                                               class="w-9 text-center py-1 rounded bg-gray-50 border border-gray-300 text-xs font-mono font-bold focus:ring-1 focus:ring-copper-500 focus:outline-none">
                                    </td>
                                    <td class="py-2 px-1 text-center">
                                        <input type="number" min="0" max="10" x-model.number="item.ring_3" @input="calculateNilai(item); item._saved = false;"
                                               class="w-9 text-center py-1 rounded bg-gray-50 border border-gray-300 text-xs font-mono font-bold focus:ring-1 focus:ring-copper-500 focus:outline-none">
                                    </td>
                                    <td class="py-2 px-1 text-center">
                                        <input type="number" min="0" max="10" x-model.number="item.ring_2" @input="calculateNilai(item); item._saved = false;"
                                               class="w-9 text-center py-1 rounded bg-gray-50 border border-gray-300 text-xs font-mono font-bold focus:ring-1 focus:ring-copper-500 focus:outline-none">
                                    </td>
                                    <td class="py-2 px-1 text-center">
                                        <input type="number" min="0" max="10" x-model.number="item.ring_1" @input="calculateNilai(item); item._saved = false;"
                                               class="w-9 text-center py-1 rounded bg-gray-50 border border-gray-300 text-xs font-mono font-bold focus:ring-1 focus:ring-copper-500 focus:outline-none">
                                    </td>

                                    <!-- Jumlah Masuk -->
                                    <td class="py-2 px-2 text-center font-mono font-bold text-gray-800 bg-gray-50/50" x-text="item.jumlah_masuk"></td>

                                    <!-- Nilai -->
                                    <td class="py-2 px-3 text-center font-mono font-extrabold text-sm text-copper-700 bg-copper-50/30" x-text="item.nilai"></td>

                                    <!-- Save Action: ALWAYS ENABLED -->
                                    <td class="py-2 px-3 text-center whitespace-nowrap">
                                        <button type="button" @click="savePresisiScore(item)"
                                                class="px-3.5 py-1.5 rounded-lg font-bold text-xs shadow transition-all duration-150 flex items-center justify-center gap-1 mx-auto min-w-[75px] cursor-pointer"
                                                :class="item._saved ? 'bg-emerald-600 text-white ring-2 ring-emerald-300' : (item._saving ? 'bg-amber-600 text-white animate-pulse' : 'bg-copper-600 hover:bg-copper-700 active:scale-95 text-white')">
                                            <template x-if="item._saving">
                                                <span class="inline-flex items-center gap-1">
                                                    <svg class="animate-spin h-3.5 w-3.5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                                                    <span>Menyimpan...</span>
                                                </span>
                                            </template>
                                            <template x-if="!item._saving && item._saved">
                                                <span class="font-bold flex items-center gap-1">✓ Tersimpan</span>
                                            </template>
                                            <template x-if="!item._saving && !item._saved">
                                                <span>Simpan</span>
                                            </template>
                                        </button>
                                        <div x-show="item._error" x-text="item._error" class="text-[10px] text-red-600 font-bold mt-1"></div>
                                    </td>
                                </tr>
                            </template>
                            <template x-if="presisiList.length === 0">
                                <tr>
                                    <td colspan="17" class="py-8 text-center text-gray-500">
                                        Belum ada peserta Presisi yang disinkronkan. Klik tombol <strong>Sinkron Peserta Verified</strong> di atas.
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- ================= TAB 2: DUELING PLAT MATCH EDITOR ================= -->
        <div x-show="activeTab === 'dueling'" x-transition>
            
            <!-- Tournament Bracket Toolbar -->
            <div class="bg-white rounded-2xl shadow-sm p-4 sm:p-5 border border-gray-200 mb-6">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                    <div class="flex items-center gap-2">
                        <h3 class="font-display text-lg font-bold text-gray-900">Bagan Turnamen Dueling Plat</h3>
                        <span class="text-xs text-gray-500 font-semibold" x-text="duelingList.length + ' Match'"></span>
                    </div>

                    <!-- Participant Count & Generator Controls -->
                    <div class="flex flex-wrap items-center gap-2.5 shrink-0">
                        <div class="flex items-center gap-1.5 bg-gray-50 px-3 py-1.5 rounded-xl border border-gray-300">
                            <label class="text-xs font-bold text-gray-700 whitespace-nowrap">Jml Peserta:</label>
                            <input type="number" min="2" max="256" x-model.number="bracketParticipantCount" placeholder="Contoh: 16"
                                   class="w-20 text-center py-1 rounded bg-white border border-gray-300 text-xs font-mono font-bold focus:ring-1 focus:ring-copper-500">
                        </div>

                        <button type="button" @click="generateTournamentBracket(bracketParticipantCount)" 
                                class="px-4 py-2 bg-copper-600 hover:bg-copper-700 text-white rounded-xl font-bold text-xs shadow transition flex items-center gap-1.5 cursor-pointer">
                            <i data-lucide="git-merge" class="w-4 h-4"></i>
                            <span x-text="'Buat Bagan ' + bracketParticipantCount + ' Peserta'"></span>
                        </button>

                        <button type="button" @click="resetTournamentBracket()" 
                                class="px-3 py-2 bg-white hover:bg-red-50 text-red-600 border border-red-200 rounded-xl font-bold text-xs transition flex items-center gap-1 cursor-pointer" title="Reset semua pertandingan">
                            <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                            <span>Reset</span>
                        </button>
                    </div>
                </div>

                <!-- View Mode Switcher -->
                <div class="flex items-center justify-between gap-2 mt-4 pt-3 border-t border-gray-100 text-xs">
                    <div class="flex items-center gap-2">
                        <span class="font-bold text-gray-700">Tampilan:</span>
                        <div class="inline-flex rounded-xl p-1 bg-gray-100 border border-gray-200">
                            <button type="button" @click="duelingAdminView = 'bracket'" 
                                    :class="duelingAdminView === 'bracket' ? 'bg-white shadow text-copper-700 font-bold' : 'text-gray-600 hover:text-gray-900'"
                                    class="px-3 py-1 text-xs rounded-lg transition flex items-center gap-1.5 cursor-pointer">
                                <i data-lucide="git-merge" class="w-3.5 h-3.5"></i>
                                <span>Bagan Visual</span>
                            </button>
                            <button type="button" @click="duelingAdminView = 'table'" 
                                    :class="duelingAdminView === 'table' ? 'bg-white shadow text-copper-700 font-bold' : 'text-gray-600 hover:text-gray-900'"
                                    class="px-3 py-1 text-xs rounded-lg transition flex items-center gap-1.5 cursor-pointer">
                                <i data-lucide="table" class="w-3.5 h-3.5"></i>
                                <span>Tabel</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Participant Datalist Autocomplete -->
            <datalist id="duelingParticipantsList">
                <template x-for="p in verifiedParticipants" :key="p.registration_id || p.no_peserta">
                    <option :value="p.nama" x-text="(p.no_peserta ? '[' + p.no_peserta + '] ' : '') + p.nama + (p.satuan ? ' - ' + p.satuan : '')"></option>
                </template>
            </datalist>

            <!-- ================= VIEW 1: INTERACTIVE VISUAL TOURNAMENT TREE BRACKET ================= -->
            <div x-show="duelingAdminView === 'bracket' && groupedDuelingMatches.length > 0" class="overflow-x-auto pb-6">
                <div class="w-max min-w-full flex items-stretch gap-6 justify-start">
                    <template x-for="round in groupedDuelingMatches" :key="round.name">
                        <div class="w-[310px] shrink-0 flex flex-col">
                            <!-- Round Header -->
                            <div class="mb-4 text-center pb-2.5 border-b-2 border-copper-600 bg-white rounded-t-xl pt-2.5 shadow-sm">
                                <h4 class="font-display font-bold text-gray-900 text-sm tracking-wide uppercase" x-text="round.name"></h4>
                                <span class="text-[10px] text-gray-500 font-semibold" x-text="round.matches.length + ' Match'"></span>
                            </div>

                            <!-- Matches in Round -->
                            <div class="flex-1 flex flex-col justify-around gap-4 py-2">
                                <template x-for="match in round.matches" :key="match.id">
                                    <div class="bg-white rounded-2xl border shadow-sm transition hover:shadow-md relative overflow-hidden"
                                         :class="{
                                             'border-red-400 ring-2 ring-red-100': match.match_status === 'live',
                                             'border-emerald-400 ring-1 ring-emerald-200': match.match_status === 'finished',
                                             'border-gray-200': match.match_status === 'upcoming'
                                         }">
                                        <!-- Match Badge & Actions Header -->
                                        <div class="px-3 py-1.5 bg-gray-50 border-b border-gray-100 flex items-center justify-between text-[11px]">
                                            <div class="flex items-center gap-1.5">
                                                <span class="font-mono font-bold text-copper-700" x-text="'Match #' + match.match_number"></span>
                                                <template x-if="match.next_match_id">
                                                    <span class="text-[9px] text-copper-600 font-medium" :title="'Pemenang lolos ke Match #' + (duelingList.find(x => x.id == match.next_match_id)?.match_number || '-')">
                                                        → M#<span x-text="duelingList.find(x => x.id == match.next_match_id)?.match_number || '-'"></span> (Slot <span x-text="match.next_slot"></span>)
                                                    </span>
                                                </template>
                                            </div>
                                            <div class="flex items-center gap-1">
                                                <select x-model="match.match_status" @change="saveDuelingMatch(match)" 
                                                        class="text-[10px] font-bold rounded px-1.5 py-0.5 border border-gray-300 bg-white cursor-pointer focus:ring-1 focus:ring-copper-500"
                                                        :class="{
                                                            'text-gray-600': match.match_status === 'upcoming',
                                                            'text-red-600 font-extrabold': match.match_status === 'live',
                                                            'text-emerald-700 font-extrabold': match.match_status === 'finished'
                                                        }">
                                                    <option value="upcoming">Upcoming</option>
                                                    <option value="live">🔴 Live</option>
                                                    <option value="finished">✓ Selesai</option>
                                                </select>
                                                <button type="button" @click="deleteDuelingMatch(match)" class="p-1 text-gray-400 hover:text-red-600 rounded transition" title="Hapus match">
                                                    <i data-lucide="trash-2" class="w-3 h-3"></i>
                                                </button>
                                            </div>
                                        </div>

                                        <!-- Participant 1 Box -->
                                        <div class="p-2.5 border-b border-gray-100 transition"
                                             :class="isSlotWinner(match, 1) ? 'bg-emerald-50/80 border-emerald-200' : 'bg-white'">
                                            <div class="flex items-center justify-between gap-1.5">
                                                <div class="flex-1 min-w-0">
                                                    <div class="flex items-center gap-1">
                                                        <span class="text-[10px] font-bold text-gray-400 w-4">#1</span>
                                                        <input type="text" list="duelingParticipantsList"
                                                               x-model="match.participant_1_name"
                                                               @change="onParticipantChange(match, 1); saveDuelingMatch(match)"
                                                               placeholder="Nama Peserta 1..."
                                                               class="w-full px-2 py-1 rounded border text-xs font-semibold focus:ring-1 focus:ring-copper-500"
                                                               :class="isSlotWinner(match, 1) ? 'bg-emerald-100/60 border-emerald-400 text-emerald-950 font-bold' : 'bg-gray-50 border-gray-200 text-gray-800'">
                                                    </div>
                                                    <input type="text" x-model="match.participant_1_satuan" @change="saveDuelingMatch(match)"
                                                           placeholder="Satuan / Kontingen..."
                                                           class="w-full text-[10px] px-2 py-0.5 mt-0.5 rounded border border-transparent hover:border-gray-200 focus:border-gray-300 text-gray-500 bg-transparent">
                                                </div>
                                                <button type="button" @click="setMatchWinner(match, 1)"
                                                        class="shrink-0 px-2 py-1 rounded-lg text-xs font-bold transition flex items-center gap-1 cursor-pointer"
                                                        :class="isSlotWinner(match, 1) ? 'bg-emerald-600 text-white shadow-sm ring-1 ring-emerald-300' : 'bg-gray-100 hover:bg-emerald-50 text-gray-500 hover:text-emerald-700 border border-gray-200'"
                                                        :title="isSlotWinner(match, 1) ? 'Pemenang (Klik untuk batalkan)' : 'Tentukan sebagai pemenang'">
                                                    <span class="text-[10px]" x-text="isSlotWinner(match, 1) ? 'MENANG' : 'Pilih'"></span>
                                                </button>
                                            </div>
                                        </div>

                                        <!-- Participant 2 Box -->
                                        <div class="p-2.5 transition"
                                             :class="isSlotWinner(match, 2) ? 'bg-emerald-50/80 border-emerald-200' : 'bg-white'">
                                            <div class="flex items-center justify-between gap-1.5">
                                                <div class="flex-1 min-w-0">
                                                    <div class="flex items-center gap-1">
                                                        <span class="text-[10px] font-bold text-gray-400 w-4">#2</span>
                                                        <input type="text" list="duelingParticipantsList"
                                                               x-model="match.participant_2_name"
                                                               @change="onParticipantChange(match, 2); saveDuelingMatch(match)"
                                                               placeholder="Nama Peserta 2..."
                                                               class="w-full px-2 py-1 rounded border text-xs font-semibold focus:ring-1 focus:ring-copper-500"
                                                               :class="isSlotWinner(match, 2) ? 'bg-emerald-100/60 border-emerald-400 text-emerald-950 font-bold' : 'bg-gray-50 border-gray-200 text-gray-800'">
                                                    </div>
                                                    <input type="text" x-model="match.participant_2_satuan" @change="saveDuelingMatch(match)"
                                                           placeholder="Satuan / Kontingen..."
                                                           class="w-full text-[10px] px-2 py-0.5 mt-0.5 rounded border border-transparent hover:border-gray-200 focus:border-gray-300 text-gray-500 bg-transparent">
                                                </div>
                                                <button type="button" @click="setMatchWinner(match, 2)"
                                                        class="shrink-0 px-2 py-1 rounded-lg text-xs font-bold transition flex items-center gap-1 cursor-pointer"
                                                        :class="isSlotWinner(match, 2) ? 'bg-emerald-600 text-white shadow-sm ring-1 ring-emerald-300' : 'bg-gray-100 hover:bg-emerald-50 text-gray-500 hover:text-emerald-700 border border-gray-200'"
                                                        :title="isSlotWinner(match, 2) ? 'Pemenang (Klik untuk batalkan)' : 'Tentukan sebagai pemenang'">
                                                    <span class="text-[10px]" x-text="isSlotWinner(match, 2) ? 'MENANG' : 'Pilih'"></span>
                                                </button>
                                            </div>
                                        </div>

                                        <!-- Match Quick Actions Footer -->
                                        <div class="px-3 py-1.5 bg-gray-50/80 border-t border-gray-100 flex items-center justify-between text-[10px]">
                                            <div>
                                                <template x-if="match.winner_id">
                                                    <button type="button" @click="clearMatchWinner(match)" class="text-gray-400 hover:text-red-600 underline cursor-pointer">
                                                        Batal Pemenang
                                                    </button>
                                                </template>
                                            </div>
                                            <button type="button" @click="saveDuelingMatch(match)"
                                                    class="px-2.5 py-0.5 rounded text-white font-bold transition flex items-center gap-1 cursor-pointer"
                                                    :class="match._saved ? 'bg-emerald-600' : (match._saving ? 'bg-amber-600 animate-pulse' : 'bg-copper-600 hover:bg-copper-700')">
                                                <span x-text="match._saving ? '...' : (match._saved ? '✓ Tersimpan' : 'Simpan')"></span>
                                            </button>
                                        </div>

                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- ================= VIEW 2: FLAT TABLE VIEW ================= -->
            <div x-show="duelingAdminView === 'table'" class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden mb-6">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-gray-100 uppercase font-bold text-gray-600 border-b border-gray-200">
                            <tr>
                                <th class="py-3 px-3">Babak</th>
                                <th class="py-3 px-2 w-16 text-center">Match #</th>
                                <th class="py-3 px-3">Peserta 1</th>
                                <th class="py-3 px-3">Peserta 2</th>
                                <th class="py-3 px-3">Pemenang Duel</th>
                                <th class="py-3 px-3 text-center w-28">Status</th>
                                <th class="py-3 px-3 text-center w-28">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <template x-for="match in duelingList" :key="match.id">
                                <tr class="hover:bg-gray-50/50 transition">
                                    <td class="py-2.5 px-3 font-semibold text-gray-900" x-text="match.round_name"></td>
                                    <td class="py-2.5 px-2 text-center font-mono font-bold text-copper-700" x-text="match.match_number"></td>

                                    <!-- Participant 1 -->
                                    <td class="py-2.5 px-3">
                                        <input type="text" list="duelingParticipantsList" x-model="match.participant_1_name" 
                                               @change="onParticipantChange(match, 1)"
                                               class="w-full px-2 py-1 rounded border border-gray-300 bg-gray-50 text-xs">
                                        <div class="text-[10px] text-gray-400 mt-0.5" x-text="match.participant_1_satuan || '-'"></div>
                                    </td>

                                    <!-- Participant 2 -->
                                    <td class="py-2.5 px-3">
                                        <input type="text" list="duelingParticipantsList" x-model="match.participant_2_name" 
                                               @change="onParticipantChange(match, 2)"
                                               class="w-full px-2 py-1 rounded border border-gray-300 bg-gray-50 text-xs">
                                        <div class="text-[10px] text-gray-400 mt-0.5" x-text="match.participant_2_satuan || '-'"></div>
                                    </td>

                                    <!-- Winner Selector with Auto-Progression -->
                                    <td class="py-2.5 px-3">
                                        <select x-model="match.winner_id" @change="saveDuelingMatch(match)" 
                                                class="w-full px-2 py-1 rounded border border-gray-300 bg-gray-50 text-xs font-bold focus:ring-1 focus:ring-copper-500">
                                            <option value="">-- Pilih Pemenang --</option>
                                            <option :value="match.participant_1_id || match.participant_1_name" 
                                                    x-text="match.participant_1_name ? match.participant_1_name : 'Peserta 1'"></option>
                                            <option :value="match.participant_2_id || match.participant_2_name" 
                                                    x-text="match.participant_2_name ? match.participant_2_name : 'Peserta 2'"></option>
                                        </select>
                                        <template x-if="match.next_match_id">
                                            <div class="text-[10px] text-copper-600 font-semibold mt-0.5 flex items-center gap-0.5">
                                                <i data-lucide="corner-down-right" class="w-2.5 h-2.5"></i>
                                                <span x-text="'Maju ke Match #' + (duelingList.find(x => x.id == match.next_match_id)?.match_number || '-') + ' (Slot ' + match.next_slot + ')'"></span>
                                            </div>
                                        </template>
                                    </td>

                                    <!-- Status Selector -->
                                    <td class="py-2.5 px-3 text-center">
                                        <select x-model="match.match_status" class="w-full px-1.5 py-1 rounded border border-gray-300 bg-gray-50 text-xs font-bold">
                                            <option value="upcoming">Upcoming</option>
                                            <option value="live">🔴 Live</option>
                                            <option value="finished">✓ Selesai</option>
                                        </select>
                                    </td>

                                    <!-- Actions -->
                                    <td class="py-2.5 px-3 text-center space-x-1 whitespace-nowrap">
                                        <button type="button" @click="saveDuelingMatch(match)"
                                                class="px-3 py-1 bg-copper-600 hover:bg-copper-700 active:scale-95 text-white rounded-lg text-xs font-bold transition shadow-sm cursor-pointer"
                                                :class="match._saved ? 'bg-emerald-600 ring-2 ring-emerald-300' : ''">
                                            <span x-text="match._saving ? '...' : (match._saved ? '✓' : 'Simpan')"></span>
                                        </button>
                                        <button type="button" @click="deleteDuelingMatch(match)"
                                                class="p-1 bg-red-50 hover:bg-red-100 text-red-600 border border-red-200 rounded-lg transition text-xs cursor-pointer" title="Hapus match">
                                            <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                        </button>
                                    </td>
                                </tr>
                            </template>
                            <template x-if="duelingList.length === 0">
                                <tr>
                                    <td colspan="7" class="py-8 text-center text-gray-400">
                                        Belum ada jadwal pertandingan dueling plat.
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Empty Bracket Notice -->
            <template x-if="duelingList.length === 0">
                <div class="bg-white rounded-2xl p-8 text-center border border-gray-200 shadow-sm mb-6">
                    <p class="font-semibold text-gray-600">Belum ada pertandingan dueling plat.</p>
                </div>
            </template>

            <!-- Collapsible: Manual Match Form -->
            <details class="bg-white rounded-2xl shadow-sm p-4 border border-gray-200 mb-8 text-xs">
                <summary class="font-display text-sm font-bold text-gray-800 cursor-pointer flex items-center gap-2 select-none">
                    <i data-lucide="plus-circle" class="w-4 h-4 text-copper-600"></i>
                    <span>Tambah Pertandingan Manual (Opsional)</span>
                </summary>
                
                <form @submit.prevent="createDuelingMatch" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 mt-4 pt-3 border-t border-gray-100">
                    <div>
                        <label class="block font-semibold text-gray-700 mb-1">Babak Pertandingan</label>
                        <select x-model="newMatch.round_name" required class="w-full px-3 py-2 rounded-lg border border-gray-300 bg-gray-50 text-xs">
                            <option value="Babak 32 Besar">Babak 32 Besar</option>
                            <option value="Babak 16 Besar">Babak 16 Besar</option>
                            <option value="Perempat Final">Perempat Final</option>
                            <option value="Semifinal">Semifinal</option>
                            <option value="Perebutan Juara 3">Perebutan Juara 3</option>
                            <option value="Final">Final</option>
                            <option value="Penyisihan">Penyisihan</option>
                        </select>
                    </div>

                    <div>
                        <label class="block font-semibold text-gray-700 mb-1">Nomor Match</label>
                        <input type="number" min="1" x-model.number="newMatch.match_number" required placeholder="1"
                               class="w-full px-3 py-2 rounded-lg border border-gray-300 bg-gray-50 text-xs font-mono font-bold">
                    </div>

                    <div>
                        <label class="block font-semibold text-gray-700 mb-1">Nama Peserta 1</label>
                        <input type="text" list="duelingParticipantsList" x-model="newMatch.participant_1_name" placeholder="Nama Peserta 1" required
                               class="w-full px-3 py-2 rounded-lg border border-gray-300 bg-gray-50 text-xs">
                    </div>

                    <div>
                        <label class="block font-semibold text-gray-700 mb-1">Nama Peserta 2</label>
                        <input type="text" list="duelingParticipantsList" x-model="newMatch.participant_2_name" placeholder="Nama Peserta 2" required
                               class="w-full px-3 py-2 rounded-lg border border-gray-300 bg-gray-50 text-xs">
                    </div>

                    <div class="sm:col-span-2 md:col-span-4 flex justify-end">
                        <button type="submit" class="px-5 py-2.5 bg-copper-600 hover:bg-copper-700 text-white rounded-xl font-bold text-xs shadow-md transition flex items-center gap-1.5 cursor-pointer">
                            <i data-lucide="plus" class="w-4 h-4"></i> Tambah Pertandingan
                        </button>
                    </div>
                </form>
            </details>

        </div>

    </div>
</div>

<script>
const ADMIN_TOKEN = <?= json_encode(ADMIN_TOKEN) ?>;

function adminScoresApp() {
    return {
        activeTab: 'presisi',
        duelingAdminView: 'bracket',
        bracketParticipantCount: 8,
        presisiList: [],
        duelingList: [],
        verifiedParticipants: [],
        alertMessage: null,
        alertType: 'success',
        isSyncing: false,
        newMatch: {
            round_name: 'Penyisihan',
            match_number: 1,
            participant_1_name: '',
            participant_2_name: ''
        },

        initData() {
            this.fetchPresisiScores();
            this.fetchDuelingMatches();
            this.fetchVerifiedParticipants();
        },

        calculateNilai(item) {
            let masuk = 0;
            let total = 0;
            for (let r = 10; r >= 1; r--) {
                const count = parseInt(item['ring_' + r]) || 0;
                masuk += count;
                total += (r * count);
            }
            const ringX = parseInt(item.ring_x) || 0;
            total += (ringX * 0.1);
            item.jumlah_masuk = masuk;
            item.nilai = Math.round(total * 10) / 10;
        },

        async fetchPresisiScores() {
            try {
                const res = await fetch('/api/scores-presisi.php?token=' + encodeURIComponent(ADMIN_TOKEN));
                const data = await res.json();
                if (data.success) {
                    this.presisiList = (data.data || []).map(row => {
                        this.calculateNilai(row);
                        return row;
                    });
                }
            } catch (e) {
                console.error('Error fetching presisi:', e);
            }
        },

        async fetchDuelingMatches() {
            try {
                const res = await fetch('/api/scores-dueling.php?token=' + encodeURIComponent(ADMIN_TOKEN));
                const data = await res.json();
                if (data.success) {
                    this.duelingList = data.data || [];
                }
            } catch (e) {
                console.error('Error fetching dueling:', e);
            }
        },

        async fetchVerifiedParticipants() {
            try {
                const res = await fetch('/api/registrations.php?token=' + encodeURIComponent(ADMIN_TOKEN));
                const data = await res.json();
                if (data.success) {
                    this.verifiedParticipants = (data.data || []).filter(r => (r.status || '').toLowerCase() === 'verified');
                }
            } catch (e) {
                console.error('Error fetching verified participants:', e);
            }
        },

        async syncVerifiedParticipants() {
            this.isSyncing = true;
            try {
                const res = await fetch('/api/registrations.php?token=' + encodeURIComponent(ADMIN_TOKEN));
                const data = await res.json();
                if (data.success) {
                    const verifiedPresisi = (data.data || []).filter(r => 
                        r.status === 'Verified' && 
                        (r.kategori.toLowerCase().includes('presisi') || r.kategori.toLowerCase().includes('keduanya'))
                    );
                    
                    for (const r of verifiedPresisi) {
                        const exists = this.presisiList.find(p => p.registration_id === r.registration_id);
                        if (!exists) {
                            const initPayload = {
                                registration_id: r.registration_id,
                                no_peserta: r.no_peserta,
                                nama: r.nama,
                                satuan: r.satuan,
                                ring_x: 0
                            };
                            for (let i = 1; i <= 10; i++) {
                                initPayload['ring_' + i] = 0;
                            }
                            await fetch('/api/scores-presisi.php', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-Admin-Token': ADMIN_TOKEN
                                },
                                body: JSON.stringify(initPayload)
                            });
                        }
                    }
                    await this.fetchPresisiScores();
                    this.showAlert('Sinkronisasi peserta presisi selesai!', 'success');
                }
            } catch (e) {
                this.showAlert('Gagal menyinkronkan peserta: ' + e.message, 'error');
            } finally {
                this.isSyncing = false;
            }
        },

        async savePresisiScore(item) {
            item._saving = true;
            item._saved = false;
            item._error = null;
            this.calculateNilai(item);

            // Sanitize payload so unfilled rings default safely to 0
            const payload = {
                registration_id: item.registration_id,
                no_peserta: item.no_peserta || '',
                nama: item.nama || '',
                satuan: item.satuan || '',
                ring_x: parseInt(item.ring_x) || 0,
                jumlah_masuk: parseInt(item.jumlah_masuk) || 0,
                nilai: Math.round((parseFloat(item.nilai) || 0) * 10) / 10,
                token: ADMIN_TOKEN
            };
            for (let r = 1; r <= 10; r++) {
                payload['ring_' + r] = parseInt(item['ring_' + r]) || 0;
            }

            try {
                const res = await fetch('/api/scores-presisi.php?token=' + encodeURIComponent(ADMIN_TOKEN), {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Admin-Token': ADMIN_TOKEN
                    },
                    body: JSON.stringify(payload)
                });
                const data = await res.json();
                if (data.success) {
                    item._saved = true;
                    item._error = null;
                    setTimeout(() => { item._saved = false; }, 3000);
                    this.showAlert('Skor ring peserta ' + item.nama + ' berhasil disimpan! (Nilai: ' + payload.nilai + ')', 'success');
                    if (data.data) {
                        item.nilai = data.data.nilai;
                        item.jumlah_masuk = data.data.jumlah_masuk;
                    }
                } else {
                    throw new Error(data.error || data.message || 'Gagal menyimpan skor');
                }
            } catch (e) {
                item._error = e.message || 'Gagal menyimpan skor';
                this.showAlert(item._error, 'error');
                alert('Pemberitahuan: ' + item._error);
            } finally {
                item._saving = false;
            }
        },

        async createDuelingMatch() {
            const roundOrderMap = {
                'Penyisihan': 1,
                'Perempat Final': 2,
                'Semifinal': 3,
                'Perebutan Juara 3': 4,
                'Final': 5
            };
            const payload = {
                round_name: this.newMatch.round_name,
                round_order: roundOrderMap[this.newMatch.round_name] || 99,
                match_number: this.newMatch.match_number,
                participant_1_name: this.newMatch.participant_1_name,
                participant_2_name: this.newMatch.participant_2_name,
                match_status: 'upcoming',
                token: ADMIN_TOKEN
            };

            try {
                const res = await fetch('/api/scores-dueling.php?token=' + encodeURIComponent(ADMIN_TOKEN), {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Admin-Token': ADMIN_TOKEN
                    },
                    body: JSON.stringify(payload)
                });
                const data = await res.json();
                if (data.success) {
                    this.showAlert('Pertandingan baru berhasil ditambahkan!', 'success');
                    this.newMatch.match_number++;
                    this.newMatch.participant_1_name = '';
                    this.newMatch.participant_2_name = '';
                    await this.fetchDuelingMatches();
                } else {
                    throw new Error(data.error || data.message || 'Gagal menambahkan match');
                }
            } catch (e) {
                this.showAlert(e.message, 'error');
            }
        },

        async saveDuelingMatch(match) {
            match._saving = true;
            try {
                const res = await fetch('/api/scores-dueling.php?token=' + encodeURIComponent(ADMIN_TOKEN), {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Admin-Token': ADMIN_TOKEN
                    },
                    body: JSON.stringify({ ...match, token: ADMIN_TOKEN })
                });
                const data = await res.json();
                if (data.success) {
                    match._saved = true;
                    setTimeout(() => { match._saved = false; }, 2500);
                    this.showAlert(data.message || ('Data match #' + match.match_number + ' berhasil diperbarui!'), 'success');
                    await this.fetchDuelingMatches();
                } else {
                    throw new Error(data.error || data.message || 'Gagal menyimpan match');
                }
            } catch (e) {
                this.showAlert(e.message, 'error');
                alert('Pemberitahuan: ' + e.message);
            } finally {
                match._saving = false;
            }
        },

        get groupedDuelingMatches() {
            const groups = {};
            this.duelingList.forEach(m => {
                const round = m.round_name || 'Penyisihan';
                if (!groups[round]) {
                    groups[round] = { name: round, order: parseInt(m.round_order) || 99, matches: [] };
                }
                groups[round].matches.push(m);
            });
            const result = Object.values(groups);
            result.sort((a, b) => a.order - b.order);
            return result;
        },

        get bracketCapacityInfo() {
            const n = parseInt(this.bracketParticipantCount) || 2;
            let p = 1;
            while (p < n) { p *= 2; }
            if (p < 4) p = 4;
            const byes = p - n;
            return {
                capacity: p,
                byes: byes,
                label: `Kapasitas: ${p} bagan (${n} peserta${byes > 0 ? `, ${byes} slot BYE otomatis` : ', bagan genap'})`
            };
        },

        isSlotWinner(match, slot) {
            if (!match.winner_id) return false;
            const w = String(match.winner_id).trim().toLowerCase();
            if (slot === 1) {
                const id1 = String(match.participant_1_id || '').trim().toLowerCase();
                const name1 = String(match.participant_1_name || '').trim().toLowerCase();
                return (id1 && w === id1) || (name1 && w === name1);
            } else {
                const id2 = String(match.participant_2_id || '').trim().toLowerCase();
                const name2 = String(match.participant_2_name || '').trim().toLowerCase();
                return (id2 && w === id2) || (name2 && w === name2);
            }
        },

        setMatchWinner(match, slot) {
            if (this.isSlotWinner(match, slot)) {
                this.clearMatchWinner(match);
                return;
            }
            if (slot === 1) {
                const name = (match.participant_1_name || '').trim();
                if (!name || name === 'TBD') {
                    alert('Nama Peserta 1 belum diisi');
                    return;
                }
                match.winner_id = match.participant_1_id || name;
            } else {
                const name = (match.participant_2_name || '').trim();
                if (!name || name === 'TBD') {
                    alert('Nama Peserta 2 belum diisi');
                    return;
                }
                match.winner_id = match.participant_2_id || name;
            }
            match.match_status = 'finished';
            this.saveDuelingMatch(match);
        },

        clearMatchWinner(match) {
            match.winner_id = '';
            match.match_status = 'upcoming';
            this.saveDuelingMatch(match);
        },

        onParticipantChange(match, slot) {
            const name = (slot === 1 ? match.participant_1_name : match.participant_2_name || '').trim().toLowerCase();
            if (!name) return;
            const found = this.verifiedParticipants.find(p => (p.nama || '').trim().toLowerCase() === name);
            if (found) {
                if (slot === 1) {
                    match.participant_1_id = found.no_peserta || found.registration_id;
                    if (!match.participant_1_satuan) match.participant_1_satuan = found.satuan || '';
                } else {
                    match.participant_2_id = found.no_peserta || found.registration_id;
                    if (!match.participant_2_satuan) match.participant_2_satuan = found.satuan || '';
                }
            }
        },

        async generateTournamentBracket(size) {
            const count = parseInt(size) || 8;
            if (!confirm(`Buat bagan turnamen eliminasi ${count} peserta? Bagan pertandingan sebelumnya akan digantikan secara otomatis.`)) return;
            try {
                const res = await fetch('/api/scores-dueling.php?token=' + encodeURIComponent(ADMIN_TOKEN), {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-Admin-Token': ADMIN_TOKEN },
                    body: JSON.stringify({ action: 'generate_bracket', count: count, size: count, token: ADMIN_TOKEN })
                });
                const data = await res.json();
                if (data.success) {
                    this.showAlert(data.message, 'success');
                    await this.fetchDuelingMatches();
                } else {
                    throw new Error(data.message || 'Gagal membuat bagan turnamen');
                }
            } catch (e) {
                this.showAlert(e.message, 'error');
                alert('Gagal membuat bagan: ' + e.message);
            }
        },

        async resetTournamentBracket() {
            if (!confirm('Yakin ingin mereset dan mengosongkan seluruh bagan pertandingan dueling plat?')) return;
            try {
                const res = await fetch('/api/scores-dueling.php?token=' + encodeURIComponent(ADMIN_TOKEN), {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-Admin-Token': ADMIN_TOKEN },
                    body: JSON.stringify({ action: 'reset_bracket', token: ADMIN_TOKEN })
                });
                const data = await res.json();
                if (data.success) {
                    this.showAlert(data.message, 'info');
                    await this.fetchDuelingMatches();
                } else {
                    throw new Error(data.message || 'Gagal mereset bagan');
                }
            } catch (e) {
                this.showAlert(e.message, 'error');
            }
        },

        async deleteDuelingMatch(match) {
            if (!confirm(`Hapus pertandingan Match #${match.match_number} (${match.participant_1_name} vs ${match.participant_2_name})?`)) return;

            try {
                const res = await fetch('/api/scores-dueling.php?token=' + encodeURIComponent(ADMIN_TOKEN), {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-Admin-Token': ADMIN_TOKEN },
                    body: JSON.stringify({ action: 'delete', id: match.id, token: ADMIN_TOKEN })
                });
                const data = await res.json();
                if (data.success) {
                    this.duelingList = this.duelingList.filter(m => m.id !== match.id);
                    this.showAlert('Match berhasil dihapus', 'info');
                } else {
                    throw new Error(data.error || data.message || 'Gagal menghapus match');
                }
            } catch (e) {
                this.showAlert(e.message, 'error');
            }
        },

        showAlert(msg, type = 'success') {
            this.alertMessage = msg;
            this.alertType = type;
            setTimeout(() => { this.alertMessage = null; }, 4000);
        }
    }
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
