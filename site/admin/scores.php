<?php
session_start();
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
                <div class="flex items-center gap-2">
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-copper-100 text-copper-700 uppercase tracking-wider">
                        Admin Control Panel
                    </span>
                </div>
                <h1 class="font-display text-3xl font-bold mt-1 text-gray-900">
                    Manajemen Skor & Pertandingan
                </h1>
                <p class="text-xs text-gray-500 mt-0.5">Input perkenaan Ring Presisi 20M (X, 10-1) & update bagan eliminasi Dueling Plat</p>
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
                Input Skor Presisi 20M (Ring)
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
            
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-4">
                <div class="text-xs text-gray-500">
                    <p class="font-semibold text-gray-700">Panduan Penilaian Sistem Ring:</p>
                    <p>Ketik jumlah peluru masuk pada kolom <strong>X</strong>, dan kolom <strong>10 s/d 1</strong>. Kolom <strong>Jml Masuk</strong> dan <strong>Nilai</strong> otomatis terhitung. Nilai X bernilai 0,1 dan tidak menambah jumlah masuk.</p>
                </div>
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
                                               @input="calculateNilai(item)"
                                               @keydown.enter="savePresisiScore(item)"
                                               class="w-9 text-center py-1 rounded bg-amber-50 border border-amber-300 text-xs font-mono font-bold text-amber-800 focus:ring-1 focus:ring-amber-500 focus:outline-none">
                                    </td>

                                    <!-- Ring 10 down to 1 Inputs (Static & Robust) -->
                                    <td class="py-2 px-1 text-center">
                                        <input type="number" min="0" max="10" x-model.number="item.ring_10" @input="calculateNilai(item)" @keydown.enter="savePresisiScore(item)"
                                               class="w-9 text-center py-1 rounded bg-gray-50 border border-gray-300 text-xs font-mono font-bold focus:ring-1 focus:ring-copper-500 focus:outline-none">
                                    </td>
                                    <td class="py-2 px-1 text-center">
                                        <input type="number" min="0" max="10" x-model.number="item.ring_9" @input="calculateNilai(item)" @keydown.enter="savePresisiScore(item)"
                                               class="w-9 text-center py-1 rounded bg-gray-50 border border-gray-300 text-xs font-mono font-bold focus:ring-1 focus:ring-copper-500 focus:outline-none">
                                    </td>
                                    <td class="py-2 px-1 text-center">
                                        <input type="number" min="0" max="10" x-model.number="item.ring_8" @input="calculateNilai(item)" @keydown.enter="savePresisiScore(item)"
                                               class="w-9 text-center py-1 rounded bg-gray-50 border border-gray-300 text-xs font-mono font-bold focus:ring-1 focus:ring-copper-500 focus:outline-none">
                                    </td>
                                    <td class="py-2 px-1 text-center">
                                        <input type="number" min="0" max="10" x-model.number="item.ring_7" @input="calculateNilai(item)" @keydown.enter="savePresisiScore(item)"
                                               class="w-9 text-center py-1 rounded bg-gray-50 border border-gray-300 text-xs font-mono font-bold focus:ring-1 focus:ring-copper-500 focus:outline-none">
                                    </td>
                                    <td class="py-2 px-1 text-center">
                                        <input type="number" min="0" max="10" x-model.number="item.ring_6" @input="calculateNilai(item)" @keydown.enter="savePresisiScore(item)"
                                               class="w-9 text-center py-1 rounded bg-gray-50 border border-gray-300 text-xs font-mono font-bold focus:ring-1 focus:ring-copper-500 focus:outline-none">
                                    </td>
                                    <td class="py-2 px-1 text-center">
                                        <input type="number" min="0" max="10" x-model.number="item.ring_5" @input="calculateNilai(item)" @keydown.enter="savePresisiScore(item)"
                                               class="w-9 text-center py-1 rounded bg-gray-50 border border-gray-300 text-xs font-mono font-bold focus:ring-1 focus:ring-copper-500 focus:outline-none">
                                    </td>
                                    <td class="py-2 px-1 text-center">
                                        <input type="number" min="0" max="10" x-model.number="item.ring_4" @input="calculateNilai(item)" @keydown.enter="savePresisiScore(item)"
                                               class="w-9 text-center py-1 rounded bg-gray-50 border border-gray-300 text-xs font-mono font-bold focus:ring-1 focus:ring-copper-500 focus:outline-none">
                                    </td>
                                    <td class="py-2 px-1 text-center">
                                        <input type="number" min="0" max="10" x-model.number="item.ring_3" @input="calculateNilai(item)" @keydown.enter="savePresisiScore(item)"
                                               class="w-9 text-center py-1 rounded bg-gray-50 border border-gray-300 text-xs font-mono font-bold focus:ring-1 focus:ring-copper-500 focus:outline-none">
                                    </td>
                                    <td class="py-2 px-1 text-center">
                                        <input type="number" min="0" max="10" x-model.number="item.ring_2" @input="calculateNilai(item)" @keydown.enter="savePresisiScore(item)"
                                               class="w-9 text-center py-1 rounded bg-gray-50 border border-gray-300 text-xs font-mono font-bold focus:ring-1 focus:ring-copper-500 focus:outline-none">
                                    </td>
                                    <td class="py-2 px-1 text-center">
                                        <input type="number" min="0" max="10" x-model.number="item.ring_1" @input="calculateNilai(item)" @keydown.enter="savePresisiScore(item)"
                                               class="w-9 text-center py-1 rounded bg-gray-50 border border-gray-300 text-xs font-mono font-bold focus:ring-1 focus:ring-copper-500 focus:outline-none">
                                    </td>

                                    <!-- Jumlah Masuk -->
                                    <td class="py-2 px-2 text-center font-mono font-bold text-gray-800 bg-gray-50/50" x-text="item.jumlah_masuk"></td>

                                    <!-- Nilai -->
                                    <td class="py-2 px-3 text-center font-mono font-extrabold text-sm text-copper-700 bg-copper-50/30" x-text="item.nilai"></td>

                                    <!-- Save Action -->
                                    <td class="py-2 px-3 text-center">
                                        <button type="button" @click="savePresisiScore(item)" :disabled="item._saving"
                                                class="px-3 py-1.5 rounded-lg font-bold text-xs shadow-sm transition flex items-center justify-center gap-1 mx-auto min-w-[70px]"
                                                :class="item._saved ? 'bg-emerald-600 text-white' : 'bg-copper-600 hover:bg-copper-700 disabled:opacity-50 text-white'">
                                            <template x-if="item._saving">
                                                <span class="inline-flex items-center gap-1">
                                                    <svg class="animate-spin h-3 w-3 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                                                    <span>...</span>
                                                </span>
                                            </template>
                                            <template x-if="!item._saving && item._saved">
                                                <span>✓ Tersimpan</span>
                                            </template>
                                            <template x-if="!item._saving && !item._saved">
                                                <span>Simpan</span>
                                            </template>
                                        </button>
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
            
            <!-- Create New Match Form Card -->
            <div class="bg-white rounded-2xl shadow-xl p-6 border border-gray-200 mb-8">
                <h3 class="font-display text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <i data-lucide="plus-circle" class="w-4 h-4 text-copper-600"></i> Buat Pertandingan Dueling Baru
                </h3>
                
                <form @submit.prevent="createDuelingMatch" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 text-xs">
                    <div>
                        <label class="block font-semibold text-gray-700 mb-1">Babak Pertandingan</label>
                        <select x-model="newMatch.round_name" required class="w-full px-3 py-2 rounded-lg border border-gray-300 bg-gray-50 text-xs">
                            <option value="Penyisihan">Penyisihan</option>
                            <option value="Perempat Final">Perempat Final</option>
                            <option value="Semifinal">Semifinal</option>
                            <option value="Perebutan Juara 3">Perebutan Juara 3</option>
                            <option value="Final">Final</option>
                        </select>
                    </div>

                    <div>
                        <label class="block font-semibold text-gray-700 mb-1">Nomor Match</label>
                        <input type="number" min="1" x-model.number="newMatch.match_number" required placeholder="1"
                               class="w-full px-3 py-2 rounded-lg border border-gray-300 bg-gray-50 text-xs font-mono font-bold">
                    </div>

                    <div>
                        <label class="block font-semibold text-gray-700 mb-1">Nama Peserta 1</label>
                        <input type="text" x-model="newMatch.participant_1_name" placeholder="Nama Peserta 1" required
                               class="w-full px-3 py-2 rounded-lg border border-gray-300 bg-gray-50 text-xs">
                    </div>

                    <div>
                        <label class="block font-semibold text-gray-700 mb-1">Nama Peserta 2</label>
                        <input type="text" x-model="newMatch.participant_2_name" placeholder="Nama Peserta 2" required
                               class="w-full px-3 py-2 rounded-lg border border-gray-300 bg-gray-50 text-xs">
                    </div>

                    <div class="sm:col-span-2 md:col-span-4 flex justify-end">
                        <button type="submit" class="px-5 py-2.5 bg-copper-600 hover:bg-copper-700 text-white rounded-xl font-bold text-xs shadow-md transition flex items-center gap-1.5">
                            <i data-lucide="plus" class="w-4 h-4"></i> Tambah Pertandingan
                        </button>
                    </div>
                </form>
            </div>

            <!-- Matches Table -->
            <div class="bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-gray-100 uppercase font-bold text-gray-600 border-b border-gray-200">
                            <tr>
                                <th class="py-3 px-3">Babak</th>
                                <th class="py-3 px-2 w-16 text-center">Match #</th>
                                <th class="py-3 px-3">Peserta 1</th>
                                <th class="py-3 px-2 text-center w-24">Waktu 1 (s)</th>
                                <th class="py-3 px-3">Peserta 2</th>
                                <th class="py-3 px-2 text-center w-24">Waktu 2 (s)</th>
                                <th class="py-3 px-3">Pemenang</th>
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
                                        <input type="text" x-model="match.participant_1_name" class="w-full px-2 py-1 rounded border border-gray-300 bg-gray-50 text-xs">
                                    </td>
                                    <td class="py-2.5 px-2 text-center">
                                        <input type="number" step="0.001" x-model.number="match.time_1" placeholder="0.000" class="w-20 text-center px-1.5 py-1 rounded border border-gray-300 bg-gray-50 text-xs font-mono font-bold">
                                    </td>

                                    <!-- Participant 2 -->
                                    <td class="py-2.5 px-3">
                                        <input type="text" x-model="match.participant_2_name" class="w-full px-2 py-1 rounded border border-gray-300 bg-gray-50 text-xs">
                                    </td>
                                    <td class="py-2.5 px-2 text-center">
                                        <input type="number" step="0.001" x-model.number="match.time_2" placeholder="0.000" class="w-20 text-center px-1.5 py-1 rounded border border-gray-300 bg-gray-50 text-xs font-mono font-bold">
                                    </td>

                                    <!-- Winner Selector -->
                                    <td class="py-2.5 px-3">
                                        <select x-model="match.winner_id" class="w-full px-2 py-1 rounded border border-gray-300 bg-gray-50 text-xs font-semibold">
                                            <option value="">-- Belum Ada --</option>
                                            <option :value="match.participant_1_id || match.participant_1_name" x-text="match.participant_1_name"></option>
                                            <option :value="match.participant_2_id || match.participant_2_name" x-text="match.participant_2_name"></option>
                                        </select>
                                    </td>

                                    <!-- Status Selector -->
                                    <td class="py-2.5 px-3 text-center">
                                        <select x-model="match.match_status" class="w-full px-1.5 py-1 rounded border border-gray-300 bg-gray-50 text-xs font-semibold">
                                            <option value="upcoming">Upcoming</option>
                                            <option value="live">Live</option>
                                            <option value="finished">Finished</option>
                                        </select>
                                    </td>

                                    <!-- Actions -->
                                    <td class="py-2.5 px-3 text-center space-x-1">
                                        <button type="button" @click="saveDuelingMatch(match)" :disabled="match._saving"
                                                class="px-2.5 py-1 bg-copper-600 hover:bg-copper-700 disabled:opacity-50 text-white rounded text-xs font-semibold transition">
                                            <span x-text="match._saving ? '...' : 'Simpan'"></span>
                                        </button>
                                    </td>
                                </tr>
                            </template>
                            <template x-if="duelingList.length === 0">
                                <tr>
                                    <td colspan="9" class="py-8 text-center text-gray-500">
                                        Belum ada jadwal pertandingan dueling plat. Gunakan formulir di atas untuk menambahkan pertandingan.
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

    </div>
</div>

<script>
const ADMIN_TOKEN = <?= json_encode(ADMIN_TOKEN) ?>;

function adminScoresApp() {
    return {
        activeTab: 'presisi',
        presisiList: [],
        duelingList: [],
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
                    setTimeout(() => { item._saved = false; }, 2500);
                    this.showAlert('Skor ring peserta ' + item.nama + ' berhasil disimpan! (Nilai: ' + payload.nilai + ')', 'success');
                    if (data.data) {
                        item.nilai = data.data.nilai;
                        item.jumlah_masuk = data.data.jumlah_masuk;
                    }
                    await this.fetchPresisiScores(); // re-sort
                } else {
                    throw new Error(data.error || data.message || 'Gagal menyimpan skor');
                }
            } catch (e) {
                this.showAlert(e.message || 'Gagal menyimpan skor', 'error');
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
                    this.showAlert('Data match #' + match.match_number + ' berhasil diperbarui!', 'success');
                } else {
                    throw new Error(data.error || data.message || 'Gagal menyimpan match');
                }
            } catch (e) {
                this.showAlert(e.message, 'error');
            } finally {
                match._saving = false;
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
