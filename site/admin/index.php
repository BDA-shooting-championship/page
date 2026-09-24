<?php
/**
 * BDA Shooting Championship 2026 — Admin Dashboard
 * 3 Core Menus: Antrean Pendaftar (default), Peserta (direct edit & direct add), Live Skor (direct scoring & brackets)
 * Mobile bottom navigation, Excel exports, WhatsApp E-ticket integration.
 */
session_start();
require_once __DIR__ . '/../includes/config.php';

// Handle login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['admin_login'])) {
    if ($_POST['password'] === ADMIN_PASS) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_login_time'] = time();
        header('Location: /admin/');
        exit;
    } else {
        $loginError = 'Password salah. Silakan coba lagi.';
    }
}

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: /admin/');
    exit;
}

$isLoggedIn = isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;

$pageTitle = 'Admin Dashboard — ' . EVENT_NAME;
$currentPage = 'admin';
include __DIR__ . '/../includes/header.php';
?>

<?php if (!$isLoggedIn): ?>
<!-- ==================== LOGIN SCREEN ==================== -->
<section class="min-h-[85vh] flex items-center justify-center target-pattern px-4 py-12">
    <div class="w-full max-w-md">
        <div class="bg-white rounded-3xl shadow-2xl border border-gray-200 p-8 sm:p-10 relative overflow-hidden">
            <!-- Decorative accent line -->
            <div class="absolute top-0 left-0 right-0 h-2 bg-gradient-to-r from-copper-500 via-amber-500 to-copper-600"></div>

            <!-- Logo & Title -->
            <div class="text-center mb-8">
                <div class="flex justify-center items-center gap-3 mb-4">
                    <div class="w-16 h-16 rounded-full bg-white p-1.5 shadow-md border border-gray-100 flex items-center justify-center">
                        <img src="/assets/logo-bda.png" alt="Logo BDA 750" class="h-12 w-12 object-contain">
                    </div>
                    <span class="text-gray-400 font-bold text-sm">✕</span>
                    <div class="w-16 h-16 rounded-full bg-white p-1.5 shadow-md border border-gray-100 flex items-center justify-center">
                        <img src="/assets/logo-championship.png" alt="Logo BSC 2026" class="h-12 w-12 object-contain">
                    </div>
                </div>
                <h1 class="font-display text-2xl sm:text-3xl font-bold text-gray-900 tracking-tight">Admin Dashboard</h1>
                <p class="text-xs sm:text-sm text-gray-500 mt-1"><?= EVENT_NAME ?></p>
            </div>

            <!-- Login Form -->
            <form method="POST" action="">
                <input type="hidden" name="admin_login" value="1">

                <?php if (isset($loginError)): ?>
                <div class="mb-5 p-3.5 bg-red-50 border border-red-200 rounded-xl text-xs sm:text-sm text-red-600 flex items-center gap-2.5">
                    <i data-lucide="alert-circle" class="w-4 h-4 flex-shrink-0 text-red-500"></i>
                    <span><?= htmlspecialchars($loginError) ?></span>
                </div>
                <?php endif; ?>

                <div class="mb-6">
                    <label for="password" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Password Admin</label>
                    <div class="relative" x-data="{ show: false }">
                        <input
                            :type="show ? 'text' : 'password'"
                            name="password"
                            id="password"
                            required
                            autofocus
                            class="w-full px-4 py-3 pr-12 border border-gray-300 rounded-xl bg-gray-50 text-gray-900 focus:bg-white focus:ring-2 focus:ring-copper-500 focus:border-copper-500 text-sm transition"
                            placeholder="Masukkan password admin"
                        >
                        <button type="button" @click="show = !show" class="absolute right-3.5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 p-1">
                            <i data-lucide="eye" class="w-4 h-4" x-show="!show"></i>
                            <i data-lucide="eye-off" class="w-4 h-4" x-show="show" x-cloak></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="w-full py-3.5 bg-gradient-to-r from-copper-600 to-amber-600 hover:from-copper-700 hover:to-amber-700 text-white font-bold rounded-xl transition shadow-lg shadow-copper-600/25 flex items-center justify-center gap-2 text-sm">
                    <i data-lucide="log-in" class="w-4 h-4"></i>
                    Masuk ke Dashboard
                </button>
            </form>

            <p class="text-center text-xs text-gray-400 mt-6 flex items-center justify-center gap-1.5">
                <i data-lucide="lock" class="w-3.5 h-3.5"></i>
                Halaman otentikasi internal panitia pelaksana.
            </p>
        </div>
    </div>
</section>

<?php else: ?>
<!-- ==================== ADMIN DASHBOARD WRAPPER ==================== -->
<section
    class="min-h-screen bg-gray-50 py-6 px-3 sm:px-6 lg:px-8 pb-28 md:pb-12"
    x-data="adminDashboard()"
    x-init="init()"
>
    <div class="max-w-7xl mx-auto">
        
        <!-- Header Bar -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6 pb-4 border-b border-gray-200">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-2xl bg-white p-1 shadow-sm border border-gray-200 flex items-center justify-center shrink-0">
                    <img src="/assets/logo-bda.png" alt="BDA" class="h-9 w-9 object-contain">
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-copper-100 text-copper-800 uppercase tracking-wider">
                            Panitia Pusat
                        </span>
                        <span class="text-xs text-gray-400">•</span>
                        <span class="text-xs text-gray-500 font-medium"><?= EVENT_NAME ?></span>
                    </div>
                    <h1 class="font-display text-2xl sm:text-3xl font-bold text-gray-900 mt-0.5">
                        Admin Dashboard
                    </h1>
                </div>
            </div>

            <!-- Header Quick Actions -->
            <div class="flex items-center flex-wrap gap-2.5">
                <!-- Dropdown / Direct Export Excel All Data -->
                <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                    <button @click="open = !open" 
                            class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl transition shadow-md shadow-emerald-600/20">
                        <i data-lucide="file-spreadsheet" class="w-4 h-4"></i>
                        <span>Export Excel</span>
                        <i data-lucide="chevron-down" class="w-3.5 h-3.5 transition-transform" :class="open ? 'rotate-180' : ''"></i>
                    </button>

                    <div x-show="open" x-cloak x-transition
                         class="absolute right-0 mt-2 w-64 bg-white rounded-2xl shadow-xl border border-gray-200 py-2 z-50 text-xs">
                        <div class="px-3 py-1.5 text-[11px] font-bold uppercase tracking-wider text-gray-400 border-b border-gray-100">
                            Pilih Format Unduhan Excel
                        </div>
                        <a :href="'/admin/export-excel.php?scope=all&token=' + encodeURIComponent(adminToken)" target="_blank"
                           class="flex items-center gap-2 px-3 py-2 text-gray-800 hover:bg-copper-50 hover:text-copper-700 transition">
                            <i data-lucide="layers" class="w-4 h-4 text-emerald-600"></i>
                            <div>
                                <p class="font-bold">Seluruh Data (Multi-Sheet)</p>
                                <p class="text-[10px] text-gray-400">Pendaftar, Presisi & Dueling Plat</p>
                            </div>
                        </a>
                        <a :href="'/admin/export-excel.php?scope=peserta&token=' + encodeURIComponent(adminToken)" target="_blank"
                           class="flex items-center gap-2 px-3 py-2 text-gray-800 hover:bg-copper-50 hover:text-copper-700 transition">
                            <i data-lucide="shield-check" class="w-4 h-4 text-blue-600"></i>
                            <div>
                                <p class="font-bold">Khusus Data Peserta Resmi</p>
                                <p class="text-[10px] text-gray-400">Peserta dengan No. Peserta BSC-26xxx</p>
                            </div>
                        </a>
                        <a :href="'/admin/export-excel.php?scope=presisi&token=' + encodeURIComponent(adminToken)" target="_blank"
                           class="flex items-center gap-2 px-3 py-2 text-gray-800 hover:bg-copper-50 hover:text-copper-700 transition">
                            <i data-lucide="crosshair" class="w-4 h-4 text-copper-600"></i>
                            <div>
                                <p class="font-bold">Skor Presisi 20M</p>
                                <p class="text-[10px] text-gray-400">Papan skor lengkap Seri 1–10 & X</p>
                            </div>
                        </a>
                        <a :href="'/admin/export-excel.php?scope=dueling&token=' + encodeURIComponent(adminToken)" target="_blank"
                           class="flex items-center gap-2 px-3 py-2 text-gray-800 hover:bg-copper-50 hover:text-copper-700 transition">
                            <i data-lucide="swords" class="w-4 h-4 text-purple-600"></i>
                            <div>
                                <p class="font-bold">Bagan Dueling Plat</p>
                                <p class="text-[10px] text-gray-400">Hasil pertandingan & eliminasi</p>
                            </div>
                        </a>
                    </div>
                </div>

                <!-- Live Score Publik Link -->
                <a href="/live-score.php" target="_blank" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 text-xs font-semibold rounded-xl transition shadow-sm">
                    <i data-lucide="external-link" class="w-3.5 h-3.5 text-copper-600"></i>
                    <span>Live Score Publik</span>
                </a>

                <!-- Logout -->
                <a href="/admin/?logout=1" class="inline-flex items-center gap-1.5 px-3 py-2 bg-red-50 hover:bg-red-100 text-red-600 text-xs font-semibold rounded-xl transition border border-red-200">
                    <i data-lucide="log-out" class="w-3.5 h-3.5"></i>
                    <span>Logout</span>
                </a>
            </div>
        </div>

        <!-- ==================== DESKTOP TOP NAVIGATION TABS (3 Core Menus) ==================== -->
        <div class="hidden md:flex items-center space-x-2 bg-white p-1.5 rounded-2xl border border-gray-200 shadow-sm mb-6">
            <!-- Tab 1: Antrean Pendaftar -->
            <button
                @click="setTab('antrean')"
                type="button"
                class="flex-1 py-2.5 px-4 rounded-xl text-xs sm:text-sm font-bold transition flex items-center justify-center gap-2"
                :class="activeTab === 'antrean' ? 'bg-copper-600 text-white shadow-md shadow-copper-600/20' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900'"
            >
                <i data-lucide="inbox" class="w-4 h-4"></i>
                <span>Antrean Pendaftar</span>
                <span
                    x-show="stats.pending > 0"
                    x-text="stats.pending"
                    class="px-2 py-0.5 text-[10px] font-extrabold rounded-full"
                    :class="activeTab === 'antrean' ? 'bg-white text-copper-700' : 'bg-amber-100 text-amber-700'"
                ></span>
            </button>

            <!-- Tab 2: Peserta -->
            <button
                @click="setTab('peserta')"
                type="button"
                class="flex-1 py-2.5 px-4 rounded-xl text-xs sm:text-sm font-bold transition flex items-center justify-center gap-2"
                :class="activeTab === 'peserta' ? 'bg-copper-600 text-white shadow-md shadow-copper-600/20' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900'"
            >
                <i data-lucide="shield-check" class="w-4 h-4"></i>
                <span>Peserta</span>
                <span
                    x-show="stats.verified > 0"
                    x-text="stats.verified"
                    class="px-2 py-0.5 text-[10px] font-extrabold rounded-full"
                    :class="activeTab === 'peserta' ? 'bg-white text-copper-700' : 'bg-emerald-100 text-emerald-700'"
                ></span>
            </button>

            <!-- Tab 3: Live Skor -->
            <button
                @click="setTab('scores')"
                type="button"
                class="flex-1 py-2.5 px-4 rounded-xl text-xs sm:text-sm font-bold transition flex items-center justify-center gap-2"
                :class="activeTab === 'scores' ? 'bg-copper-600 text-white shadow-md shadow-copper-600/20' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900'"
            >
                <i data-lucide="crosshair" class="w-4 h-4"></i>
                <span>Live Skor</span>
                <span class="w-2 h-2 rounded-full" :class="activeTab === 'scores' ? 'bg-white animate-pulse' : 'bg-copper-500'"></span>
            </button>
        </div>


        <!-- ==================== TAB 1: ANTREAN PENDAFTAR (DEFAULT VIEW) ==================== -->
        <div x-show="activeTab === 'antrean'" x-transition>
            <!-- Stats Bar -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-6">
                <!-- Total -->
                <div class="bg-white rounded-2xl border border-gray-200 p-4 sm:p-5 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-[11px] font-bold text-gray-500 uppercase tracking-wider">Total Pendaftar</p>
                            <p class="text-2xl sm:text-3xl font-bold text-gray-900 mt-1" x-text="stats.total">0</p>
                        </div>
                        <div class="w-11 h-11 bg-blue-50 border border-blue-100 rounded-xl flex items-center justify-center">
                            <i data-lucide="users" class="w-5 h-5 text-blue-600"></i>
                        </div>
                    </div>
                </div>
                <!-- Pending -->
                <div class="bg-white rounded-2xl border border-gray-200 p-4 sm:p-5 shadow-sm ring-1 ring-amber-400/30">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-[11px] font-bold text-amber-700 uppercase tracking-wider">Menunggu Konfirmasi</p>
                            <p class="text-2xl sm:text-3xl font-bold text-amber-600 mt-1" x-text="stats.pending">0</p>
                        </div>
                        <div class="w-11 h-11 bg-amber-50 border border-amber-100 rounded-xl flex items-center justify-center">
                            <i data-lucide="clock" class="w-5 h-5 text-amber-600"></i>
                        </div>
                    </div>
                </div>
                <!-- Verified -->
                <div class="bg-white rounded-2xl border border-gray-200 p-4 sm:p-5 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-[11px] font-bold text-emerald-700 uppercase tracking-wider">Terkonfirmasi</p>
                            <p class="text-2xl sm:text-3xl font-bold text-emerald-600 mt-1" x-text="stats.verified">0</p>
                        </div>
                        <div class="w-11 h-11 bg-emerald-50 border border-emerald-100 rounded-xl flex items-center justify-center">
                            <i data-lucide="check-circle" class="w-5 h-5 text-emerald-600"></i>
                        </div>
                    </div>
                </div>
                <!-- Rejected -->
                <div class="bg-white rounded-2xl border border-gray-200 p-4 sm:p-5 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-[11px] font-bold text-red-700 uppercase tracking-wider">Ditolak</p>
                            <p class="text-2xl sm:text-3xl font-bold text-red-600 mt-1" x-text="stats.rejected">0</p>
                        </div>
                        <div class="w-11 h-11 bg-red-50 border border-red-100 rounded-xl flex items-center justify-center">
                            <i data-lucide="x-circle" class="w-5 h-5 text-red-600"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Search & Filter Bar -->
            <div class="bg-white rounded-2xl border border-gray-200 p-4 mb-6 shadow-sm">
                <div class="flex flex-col sm:flex-row gap-3">
                    <div class="relative flex-1">
                        <i data-lucide="search" class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400"></i>
                        <input
                            type="text"
                            x-model="searchQuery"
                            @input.debounce.300ms="filterRegistrations()"
                            placeholder="Cari nama, NRP, satuan, ID registrasi..."
                            class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-xl bg-gray-50 text-gray-900 text-xs sm:text-sm focus:bg-white focus:ring-2 focus:ring-copper-500 focus:border-copper-500 transition"
                        >
                    </div>
                    <select
                        x-model="statusFilter"
                        @change="filterRegistrations()"
                        class="px-3.5 py-2.5 border border-gray-300 rounded-xl bg-gray-50 text-gray-900 text-xs sm:text-sm font-medium focus:bg-white focus:ring-2 focus:ring-copper-500 transition"
                    >
                        <option value="all">Semua Status</option>
                        <option value="pending">Khusus Pending (Menunggu)</option>
                        <option value="verified">Khusus Terverifikasi</option>
                        <option value="rejected">Khusus Ditolak</option>
                    </select>
                    <button
                        @click="fetchRegistrations()"
                        class="inline-flex items-center justify-center gap-1.5 px-4 py-2.5 bg-copper-50 text-copper-700 hover:bg-copper-100 border border-copper-200 text-xs font-bold rounded-xl transition"
                    >
                        <i data-lucide="refresh-cw" class="w-3.5 h-3.5" :class="loading && 'animate-spin'"></i>
                        <span>Refresh</span>
                    </button>
                    <a
                        :href="'/admin/export-excel.php?scope=antrean&token=' + encodeURIComponent(adminToken)"
                        target="_blank"
                        class="inline-flex items-center justify-center gap-1.5 px-4 py-2.5 bg-emerald-50 text-emerald-700 hover:bg-emerald-100 border border-emerald-200 text-xs font-bold rounded-xl transition"
                    >
                        <i data-lucide="file-spreadsheet" class="w-3.5 h-3.5"></i>
                        <span>Excel Antrean</span>
                    </a>
                </div>
            </div>

            <!-- Table of Antrean -->
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs sm:text-sm">
                        <thead class="bg-gray-100/80 uppercase font-bold text-gray-600 border-b border-gray-200 text-[11px] tracking-wider">
                            <tr>
                                <th class="py-3 px-3 w-12 text-center">No</th>
                                <th class="py-3 px-3">ID Registrasi</th>
                                <th class="py-3 px-3">Nama Lengkap</th>
                                <th class="py-3 px-3 hidden md:table-cell">Pangkat / NRP</th>
                                <th class="py-3 px-3 hidden lg:table-cell">Satuan</th>
                                <th class="py-3 px-3 hidden lg:table-cell">Kategori</th>
                                <th class="py-3 px-3 text-center">Status</th>
                                <th class="py-3 px-3 hidden xl:table-cell">Waktu Daftar</th>
                                <th class="py-3 px-3 text-center w-28">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <template x-for="(reg, index) in filtered" :key="reg.id">
                                <tr class="hover:bg-amber-50/40 transition cursor-pointer" @click="openDetail(reg)">
                                    <td class="py-3 px-3 text-center font-semibold text-gray-500" x-text="index + 1"></td>
                                    <td class="py-3 px-3">
                                        <span class="font-mono text-xs bg-gray-100 text-gray-800 px-2 py-1 rounded font-bold" x-text="reg.registration_id"></span>
                                    </td>
                                    <td class="py-3 px-3">
                                        <div class="font-bold text-gray-900" x-text="reg.nama"></div>
                                        <div class="text-[11px] text-gray-500 md:hidden" x-text="(reg.pangkat || '-') + ' • ' + (reg.satuan || '-')"></div>
                                    </td>
                                    <td class="py-3 px-3 hidden md:table-cell text-gray-700">
                                        <span x-text="reg.pangkat || '-'"></span> / <span class="font-mono text-xs" x-text="reg.nrp"></span>
                                    </td>
                                    <td class="py-3 px-3 hidden lg:table-cell text-gray-700" x-text="reg.satuan"></td>
                                    <td class="py-3 px-3 hidden lg:table-cell">
                                        <span class="inline-block px-2.5 py-0.5 rounded-full text-[11px] font-semibold uppercase tracking-wider"
                                              :class="reg.kategori === 'presisi' ? 'bg-blue-50 text-blue-700 border border-blue-200' : 'bg-purple-50 text-purple-700 border border-purple-200'"
                                              x-text="reg.kategori"></span>
                                    </td>
                                    <td class="py-3 px-3 text-center">
                                        <span
                                            class="inline-block px-2.5 py-1 text-[11px] font-bold rounded-full capitalize"
                                            :class="{
                                                'bg-amber-100 text-amber-800 border border-amber-200': (reg.status || '').toLowerCase() === 'pending',
                                                'bg-emerald-100 text-emerald-800 border border-emerald-200': (reg.status || '').toLowerCase() === 'verified',
                                                'bg-red-100 text-red-800 border border-red-200': (reg.status || '').toLowerCase() === 'rejected'
                                            }"
                                            x-text="reg.status"
                                        ></span>
                                    </td>
                                    <td class="py-3 px-3 text-gray-500 text-xs hidden xl:table-cell" x-text="formatDate(reg.created_at)"></td>
                                    <td class="py-3 px-3 text-center">
                                        <button
                                            type="button"
                                            @click.stop="openDetail(reg)"
                                            class="px-3 py-1.5 bg-copper-50 hover:bg-copper-100 text-copper-700 font-bold rounded-lg transition text-xs inline-flex items-center gap-1 border border-copper-200 shadow-sm"
                                        >
                                            <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                                            <span>Rincian</span>
                                        </button>
                                    </td>
                                </tr>
                            </template>
                            <template x-if="filtered.length === 0 && !loading">
                                <tr>
                                    <td colspan="9" class="py-12 text-center text-gray-400">
                                        <i data-lucide="inbox" class="w-10 h-10 mx-auto text-gray-300 mb-2"></i>
                                        <p class="font-medium text-gray-600">Tidak ada data pendaftar pada filter ini.</p>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                <div class="px-4 py-3 bg-gray-50 border-t border-gray-200 text-xs text-gray-500 flex items-center justify-between">
                    <div>
                        Menampilkan <span class="font-bold text-gray-900" x-text="filtered.length"></span> dari <span class="font-bold text-gray-900" x-text="registrations.length"></span> pendaftar
                    </div>
                </div>
            </div>
        </div>


        <!-- ==================== TAB 2: PESERTA (TERKONFIRMASI + EDIT & TAMBAH LANGSUNG) ==================== -->
        <div x-show="activeTab === 'peserta'" x-transition>
            <!-- Peserta Header & Actions -->
            <div class="bg-white rounded-2xl border border-gray-200 p-5 mb-6 shadow-sm flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <div class="flex items-center gap-2">
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">
                            Peserta Terkonfirmasi Resmi
                        </span>
                    </div>
                    <h2 class="font-display text-xl sm:text-2xl font-bold text-gray-900 mt-1">
                        Manajemen Peserta Kejuaraan
                    </h2>
                    <p class="text-xs text-gray-500 mt-0.5">
                        Kelola data peserta resmi, edit langsung detail seluruh peserta, atau tambahkan peserta baru langsung oleh admin.
                    </p>
                </div>

                <div class="flex items-center flex-wrap gap-2.5">
                    <!-- Button Tambah Peserta Langsung -->
                    <button
                        type="button"
                        @click="openAddPeserta()"
                        class="px-4 py-2.5 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-bold rounded-xl text-xs sm:text-sm shadow-md shadow-emerald-600/20 transition flex items-center gap-2"
                    >
                        <i data-lucide="user-plus" class="w-4 h-4"></i>
                        <span>+ Tambah Peserta Langsung</span>
                    </button>

                    <!-- Export Excel Peserta -->
                    <a
                        :href="'/admin/export-excel.php?scope=peserta&token=' + encodeURIComponent(adminToken)"
                        target="_blank"
                        class="px-4 py-2.5 bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 font-bold rounded-xl text-xs sm:text-sm transition flex items-center gap-1.5 shadow-sm"
                    >
                        <i data-lucide="file-spreadsheet" class="w-4 h-4 text-emerald-600"></i>
                        <span>Export Excel Peserta</span>
                    </a>
                </div>
            </div>

            <!-- Peserta Search & Category Filter -->
            <div class="bg-white rounded-2xl border border-gray-200 p-4 mb-6 shadow-sm flex flex-col sm:flex-row gap-3">
                <div class="relative flex-1">
                    <i data-lucide="search" class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <input
                        type="text"
                        x-model="pesertaSearch"
                        @input.debounce.300ms="filterPeserta()"
                        placeholder="Cari nama, No. Peserta BSC-26xxx, NRP, Satuan..."
                        class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-xl bg-gray-50 text-gray-900 text-xs sm:text-sm focus:bg-white focus:ring-2 focus:ring-copper-500 focus:border-copper-500 transition"
                    >
                </div>
                <select
                    x-model="pesertaCategoryFilter"
                    @change="filterPeserta()"
                    class="px-3.5 py-2.5 border border-gray-300 rounded-xl bg-gray-50 text-gray-900 text-xs sm:text-sm font-medium focus:bg-white focus:ring-2 focus:ring-copper-500 transition"
                >
                    <option value="all">Semua Kategori</option>
                    <option value="presisi">Pistol Presisi 20M</option>
                    <option value="dueling">Dueling Plat</option>
                    <option value="keduanya">Presisi & Dueling Plat</option>
                </select>
            </div>

            <!-- Peserta Table -->
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs sm:text-sm">
                        <thead class="bg-gray-100/80 uppercase font-bold text-gray-600 border-b border-gray-200 text-[11px] tracking-wider">
                            <tr>
                                <th class="py-3 px-3 w-12 text-center">No</th>
                                <th class="py-3 px-3">No. Peserta</th>
                                <th class="py-3 px-3">Nama & Pangkat</th>
                                <th class="py-3 px-3 hidden md:table-cell">NRP / Satuan</th>
                                <th class="py-3 px-3">Kategori</th>
                                <th class="py-3 px-3 hidden lg:table-cell">Kontak</th>
                                <th class="py-3 px-3 text-center">Status</th>
                                <th class="py-3 px-3 text-center w-36">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <template x-for="(p, index) in filteredPeserta" :key="p.id">
                                <tr class="hover:bg-emerald-50/30 transition">
                                    <td class="py-3 px-3 text-center font-semibold text-gray-500" x-text="index + 1"></td>
                                    <td class="py-3 px-3">
                                        <span class="inline-block px-2.5 py-1 rounded-lg bg-copper-100 text-copper-800 font-mono font-bold text-xs" x-text="p.no_peserta || '-'"></span>
                                    </td>
                                    <td class="py-3 px-3">
                                        <div class="font-bold text-gray-900" x-text="p.nama"></div>
                                        <div class="text-[11px] text-gray-500" x-text="p.pangkat || '-'"></div>
                                    </td>
                                    <td class="py-3 px-3 hidden md:table-cell text-gray-700">
                                        <div class="font-mono text-xs font-semibold" x-text="p.nrp"></div>
                                        <div class="text-[11px] text-gray-500" x-text="p.satuan"></div>
                                    </td>
                                    <td class="py-3 px-3">
                                        <span class="inline-block px-2.5 py-0.5 rounded-full text-[11px] font-semibold uppercase tracking-wider"
                                              :class="p.kategori === 'presisi' ? 'bg-blue-50 text-blue-700 border border-blue-200' : 'bg-purple-50 text-purple-700 border border-purple-200'"
                                              x-text="p.kategori"></span>
                                    </td>
                                    <td class="py-3 px-3 hidden lg:table-cell text-xs">
                                        <div class="font-mono text-gray-800" x-text="p.telepon"></div>
                                        <div class="text-gray-400 text-[10px]" x-text="p.email"></div>
                                    </td>
                                    <td class="py-3 px-3 text-center">
                                        <span class="inline-block px-2 py-0.5 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                            Verified
                                        </span>
                                    </td>
                                    <td class="py-3 px-3 text-center">
                                        <div class="flex items-center justify-center gap-1.5">
                                            <!-- Button Edit Langsung -->
                                            <button
                                                type="button"
                                                @click="openEditPeserta(p)"
                                                class="px-2.5 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-700 font-bold rounded-lg transition text-xs inline-flex items-center gap-1 border border-blue-200 shadow-sm"
                                                title="Edit Langsung Detail Peserta"
                                            >
                                                <i data-lucide="edit-3" class="w-3.5 h-3.5"></i>
                                                <span class="hidden sm:inline">Edit</span>
                                            </button>

                                            <!-- Button E-Ticket -->
                                            <a
                                                :href="'/e-ticket.php?id=' + encodeURIComponent(p.registration_id)"
                                                target="_blank"
                                                class="p-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition text-xs"
                                                title="Buka E-Ticket"
                                            >
                                                <i data-lucide="ticket" class="w-3.5 h-3.5"></i>
                                            </a>

                                            <!-- Button Kirim WA -->
                                            <button
                                                type="button"
                                                @click="sendWhatsApp(p)"
                                                class="p-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200 rounded-lg transition text-xs"
                                                title="Kirim E-Ticket ke WA"
                                            >
                                                <i data-lucide="send" class="w-3.5 h-3.5"></i>
                                            </button>

                                            <!-- Button Hapus Peserta -->
                                            <button
                                                type="button"
                                                @click="deletePeserta(p)"
                                                class="p-1.5 bg-red-50 hover:bg-red-100 text-red-600 border border-red-200 rounded-lg transition text-xs"
                                                title="Hapus Peserta"
                                            >
                                                <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </template>
                            <template x-if="filteredPeserta.length === 0">
                                <tr>
                                    <td colspan="8" class="py-12 text-center text-gray-400">
                                        <i data-lucide="users" class="w-10 h-10 mx-auto text-gray-300 mb-2"></i>
                                        <p class="font-medium text-gray-600">Belum ada peserta terkonfirmasi atau sesuai kata kunci.</p>
                                        <button @click="openAddPeserta()" class="mt-3 text-xs text-copper-600 font-bold hover:underline">
                                            + Tambahkan Peserta Pertama
                                        </button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                <div class="px-4 py-3 bg-gray-50 border-t border-gray-200 text-xs text-gray-500 flex items-center justify-between">
                    <div>
                        Menampilkan <span class="font-bold text-gray-900" x-text="filteredPeserta.length"></span> peserta terkonfirmasi
                    </div>
                </div>
            </div>
        </div>


        <!-- ==================== TAB 3: LIVE SKOR (DIRECT SCORE EDITING) ==================== -->
        <div x-show="activeTab === 'scores'" x-transition>
            <!-- Live Skor Control Header -->
            <div class="bg-white rounded-2xl border border-gray-200 p-5 mb-6 shadow-sm flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <div class="flex items-center gap-2">
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-copper-100 text-copper-800">
                            Live Score Control Panel
                        </span>
                    </div>
                    <h2 class="font-display text-xl sm:text-2xl font-bold text-gray-900 mt-1">
                        Manajemen & Input Skor Langsung
                    </h2>
                    <p class="text-xs text-gray-500 mt-0.5">
                        Input langsung poin tembakan Presisi 20M (Seri 1–10 & X) dan update langsung bagan Dueling Plat.
                    </p>
                </div>

                <div class="flex items-center flex-wrap gap-2.5">
                    <a
                        :href="'/admin/export-excel.php?scope=scores&token=' + encodeURIComponent(adminToken)"
                        target="_blank"
                        class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl text-xs transition flex items-center gap-1.5 shadow-md shadow-emerald-600/20"
                    >
                        <i data-lucide="file-spreadsheet" class="w-4 h-4"></i>
                        <span>Export Excel Live Skor</span>
                    </a>
                    <a
                        href="/live-score.php"
                        target="_blank"
                        class="px-4 py-2 bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 font-bold rounded-xl text-xs transition flex items-center gap-1.5 shadow-sm"
                    >
                        <i data-lucide="external-link" class="w-3.5 h-3.5 text-copper-600"></i>
                        <span>Buka Live Score Publik</span>
                    </a>
                </div>
            </div>

            <!-- Subtabs: Presisi 20M vs Dueling Plat -->
            <div class="flex border-b border-gray-200 mb-6 space-x-4">
                <button
                    @click="scoresSubTab = 'presisi'"
                    :class="scoresSubTab === 'presisi' ? 'border-copper-600 text-copper-600 border-b-2 font-bold' : 'border-transparent text-gray-500 hover:text-gray-700'"
                    class="py-3 px-4 font-display text-sm sm:text-base transition flex items-center gap-2"
                >
                    <i data-lucide="crosshair" class="w-4 h-4"></i>
                    <span>Input Skor Presisi 20M</span>
                </button>
                <button
                    @click="scoresSubTab = 'dueling'"
                    :class="scoresSubTab === 'dueling' ? 'border-copper-600 text-copper-600 border-b-2 font-bold' : 'border-transparent text-gray-500 hover:text-gray-700'"
                    class="py-3 px-4 font-display text-sm sm:text-base transition flex items-center gap-2"
                >
                    <i data-lucide="swords" class="w-4 h-4"></i>
                    <span>Bagan Pertandingan Dueling Plat</span>
                </button>
            </div>

            <!-- ===== SUBTAB 1: PRESISI 20M ===== -->
            <div x-show="scoresSubTab === 'presisi'" x-transition>
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-4">
                    <div class="text-xs text-gray-500">
                        <p class="font-bold text-gray-800">Panduan Input Skor Presisi:</p>
                        <p>Ketik poin (0–10) pada kolom S1 s/d S10, dan jumlah X pada kolom Jml X. Total poin otomatis dihitung secara real-time. Klik tombol <strong>Simpan</strong> pada baris peserta untuk menyimpan ke server.</p>
                    </div>
                    <button
                        type="button"
                        @click="syncVerifiedParticipants()"
                        :disabled="isSyncing"
                        class="px-4 py-2.5 bg-white border border-gray-300 hover:bg-gray-50 text-xs font-bold rounded-xl transition flex items-center gap-2 shrink-0 shadow-sm"
                    >
                        <i data-lucide="refresh-cw" class="w-4 h-4 text-copper-600" :class="isSyncing && 'animate-spin'"></i>
                        <span x-text="isSyncing ? 'Menyinkronkan...' : 'Sinkron Peserta Verified'"></span>
                    </button>
                </div>

                <!-- Table of Scores -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-gray-100 uppercase font-bold text-gray-600 border-b border-gray-200">
                                <tr>
                                    <th class="py-3 px-3 w-12 text-center">Rank</th>
                                    <th class="py-3 px-3">No. Peserta</th>
                                    <th class="py-3 px-3">Nama & Satuan</th>
                                    <th class="py-3 px-1 text-center w-11">S1</th>
                                    <th class="py-3 px-1 text-center w-11">S2</th>
                                    <th class="py-3 px-1 text-center w-11">S3</th>
                                    <th class="py-3 px-1 text-center w-11">S4</th>
                                    <th class="py-3 px-1 text-center w-11">S5</th>
                                    <th class="py-3 px-1 text-center w-11">S6</th>
                                    <th class="py-3 px-1 text-center w-11">S7</th>
                                    <th class="py-3 px-1 text-center w-11">S8</th>
                                    <th class="py-3 px-1 text-center w-11">S9</th>
                                    <th class="py-3 px-1 text-center w-11">S10</th>
                                    <th class="py-3 px-2 text-center w-14 font-bold text-copper-700 bg-copper-50/40">Jml X</th>
                                    <th class="py-3 px-3 text-center w-16 font-extrabold bg-amber-50 text-amber-800">Total</th>
                                    <th class="py-3 px-3 text-center w-24">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <template x-for="(item, index) in presisiList" :key="item.id || item.registration_id">
                                    <tr class="hover:bg-gray-50/80 transition">
                                        <td class="py-2 px-3 text-center font-bold text-gray-500" x-text="index + 1"></td>
                                        <td class="py-2 px-3 font-mono font-bold text-copper-700" x-text="item.no_peserta || '-'"></td>
                                        <td class="py-2 px-3">
                                            <div class="font-bold text-gray-900" x-text="item.nama"></div>
                                            <div class="text-[11px] text-gray-500" x-text="item.satuan"></div>
                                        </td>

                                        <!-- 10 Inputs for Seri 1 - 10 -->
                                        <template x-for="i in 10" :key="i">
                                            <td class="py-2 px-1 text-center">
                                                <input
                                                    type="number"
                                                    min="0"
                                                    max="10"
                                                    x-model.number="item['seri_' + i]"
                                                    @input="calculateTotal(item)"
                                                    class="w-10 text-center py-1 rounded-md bg-gray-50 border border-gray-300 text-xs font-mono font-bold focus:bg-white focus:ring-1 focus:ring-copper-500 focus:outline-none"
                                                >
                                            </td>
                                        </template>

                                        <!-- X Count Input -->
                                        <td class="py-2 px-2 text-center bg-copper-50/20">
                                            <input
                                                type="number"
                                                min="0"
                                                max="10"
                                                x-model.number="item.x_count"
                                                class="w-12 text-center py-1 rounded-md bg-copper-50 border border-copper-300 text-xs font-mono font-bold text-copper-800 focus:ring-1 focus:ring-copper-500 focus:outline-none"
                                            >
                                        </td>

                                        <!-- Total Score (Real-time auto calculate) -->
                                        <td class="py-2 px-3 text-center font-mono font-extrabold text-sm text-gray-900 bg-amber-50" x-text="item.total_score"></td>

                                        <!-- Direct Save Action -->
                                        <td class="py-2 px-3 text-center">
                                            <button
                                                type="button"
                                                @click="savePresisiScore(item)"
                                                :disabled="item._saving"
                                                class="px-3 py-1.5 bg-copper-600 hover:bg-copper-700 disabled:opacity-50 text-white rounded-lg font-bold text-xs shadow-sm transition"
                                            >
                                                <span x-text="item._saving ? '...' : 'Simpan'"></span>
                                            </button>
                                        </td>
                                    </tr>
                                </template>
                                <template x-if="presisiList.length === 0">
                                    <tr>
                                        <td colspan="16" class="py-10 text-center text-gray-400">
                                            Belum ada peserta Presisi yang disinkronkan. Klik tombol <strong>Sinkron Peserta Verified</strong> di atas.
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- ===== SUBTAB 2: DUELING PLAT ===== -->
            <div x-show="scoresSubTab === 'dueling'" x-transition>
                <!-- Add New Match Card -->
                <div class="bg-white rounded-2xl shadow-sm p-5 border border-gray-200 mb-6">
                    <h3 class="font-display text-base sm:text-lg font-bold text-gray-900 mb-3 flex items-center gap-2">
                        <i data-lucide="plus-circle" class="w-4 h-4 text-copper-600"></i>
                        <span>Tambah Pertandingan Dueling Plat Baru</span>
                    </h3>

                    <form @submit.prevent="createDuelingMatch" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3 text-xs">
                        <div>
                            <label class="block font-bold text-gray-700 mb-1">Babak Pertandingan</label>
                            <select x-model="newMatch.round_name" required class="w-full px-3 py-2 rounded-lg border border-gray-300 bg-gray-50 text-xs">
                                <option value="Penyisihan">Penyisihan</option>
                                <option value="Perempat Final">Perempat Final</option>
                                <option value="Semifinal">Semifinal</option>
                                <option value="Perebutan Juara 3">Perebutan Juara 3</option>
                                <option value="Final">Final</option>
                            </select>
                        </div>

                        <div>
                            <label class="block font-bold text-gray-700 mb-1">Nomor Match</label>
                            <input type="number" min="1" x-model.number="newMatch.match_number" required placeholder="1"
                                   class="w-full px-3 py-2 rounded-lg border border-gray-300 bg-gray-50 text-xs font-mono font-bold">
                        </div>

                        <div>
                            <label class="block font-bold text-gray-700 mb-1">Nama Peserta 1</label>
                            <input type="text" x-model="newMatch.participant_1_name" placeholder="Nama Peserta 1" required
                                   class="w-full px-3 py-2 rounded-lg border border-gray-300 bg-gray-50 text-xs">
                        </div>

                        <div>
                            <label class="block font-bold text-gray-700 mb-1">Nama Peserta 2</label>
                            <input type="text" x-model="newMatch.participant_2_name" placeholder="Nama Peserta 2" required
                                   class="w-full px-3 py-2 rounded-lg border border-gray-300 bg-gray-50 text-xs">
                        </div>

                        <div class="sm:col-span-2 md:col-span-4 flex justify-end">
                            <button type="submit" class="px-5 py-2 bg-copper-600 hover:bg-copper-700 text-white rounded-xl font-bold text-xs shadow-md transition flex items-center gap-1.5">
                                <i data-lucide="plus" class="w-4 h-4"></i>
                                <span>Tambah ke Bagan</span>
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Existing Matches Table -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
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
                                    <tr class="hover:bg-gray-50/80 transition">
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
                                            <select x-model="match.winner_id" class="w-full px-2 py-1 rounded border border-gray-300 bg-gray-50 text-xs font-bold">
                                                <option value="">-- Belum Ada --</option>
                                                <option :value="match.participant_1_id || match.participant_1_name" x-text="match.participant_1_name"></option>
                                                <option :value="match.participant_2_id || match.participant_2_name" x-text="match.participant_2_name"></option>
                                            </select>
                                        </td>

                                        <!-- Status Selector -->
                                        <td class="py-2.5 px-3 text-center">
                                            <select x-model="match.match_status" class="w-full px-1.5 py-1 rounded border border-gray-300 bg-gray-50 text-xs font-bold">
                                                <option value="upcoming">Upcoming</option>
                                                <option value="live">Live</option>
                                                <option value="finished">Finished</option>
                                            </select>
                                        </td>

                                        <!-- Actions -->
                                        <td class="py-2.5 px-3 text-center space-x-1">
                                            <button type="button" @click="saveDuelingMatch(match)" :disabled="match._saving"
                                                    class="px-2.5 py-1 bg-copper-600 hover:bg-copper-700 disabled:opacity-50 text-white rounded-lg text-xs font-bold transition">
                                                <span x-text="match._saving ? '...' : 'Simpan'"></span>
                                            </button>
                                            <button type="button" @click="deleteDuelingMatch(match)"
                                                    class="p-1 bg-red-50 hover:bg-red-100 text-red-600 border border-red-200 rounded-lg transition text-xs" title="Hapus match">
                                                <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </template>
                                <template x-if="duelingList.length === 0">
                                    <tr>
                                        <td colspan="9" class="py-10 text-center text-gray-400">
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

    <!-- ==================== MOBILE BOTTOM NAVIGATION BAR (FIXED) ==================== -->
    <!-- Requirement: "ketika di buka secara mobile, navigasi berada pada bagian bawah HP, dashboard default adalah antrean pendaftar" -->
    <nav class="md:hidden fixed bottom-0 left-0 right-0 z-50 bg-white/95 backdrop-blur-md border-t border-gray-200 shadow-[0_-4px_16px_rgba(0,0,0,0.08)] py-2 px-3 flex items-center justify-around">
        <!-- Menu 1: Antrean Pendaftar (Default) -->
        <button
            type="button"
            @click="setTab('antrean')"
            class="flex-1 flex flex-col items-center justify-center py-1 transition relative"
            :class="activeTab === 'antrean' ? 'text-copper-600 font-bold' : 'text-gray-400 hover:text-gray-600'"
        >
            <div class="relative">
                <i data-lucide="inbox" class="w-5 h-5"></i>
                <span
                    x-show="stats.pending > 0"
                    x-text="stats.pending"
                    class="absolute -top-1.5 -right-3 px-1.5 py-0.2 bg-amber-500 text-white text-[10px] font-bold rounded-full min-w-[16px] text-center"
                ></span>
            </div>
            <span class="text-[11px] mt-1 tracking-tight">Antrean</span>
        </button>

        <!-- Menu 2: Peserta -->
        <button
            type="button"
            @click="setTab('peserta')"
            class="flex-1 flex flex-col items-center justify-center py-1 transition relative"
            :class="activeTab === 'peserta' ? 'text-copper-600 font-bold' : 'text-gray-400 hover:text-gray-600'"
        >
            <div class="relative">
                <i data-lucide="shield-check" class="w-5 h-5"></i>
                <span
                    x-show="stats.verified > 0"
                    x-text="stats.verified"
                    class="absolute -top-1.5 -right-3 px-1.5 py-0.2 bg-emerald-600 text-white text-[10px] font-bold rounded-full min-w-[16px] text-center"
                ></span>
            </div>
            <span class="text-[11px] mt-1 tracking-tight">Peserta</span>
        </button>

        <!-- Menu 3: Live Skor -->
        <button
            type="button"
            @click="setTab('scores')"
            class="flex-1 flex flex-col items-center justify-center py-1 transition relative"
            :class="activeTab === 'scores' ? 'text-copper-600 font-bold' : 'text-gray-400 hover:text-gray-600'"
        >
            <div class="relative">
                <i data-lucide="crosshair" class="w-5 h-5"></i>
                <span class="absolute -top-1 -right-1 w-2 h-2 bg-copper-600 rounded-full animate-ping"></span>
                <span class="absolute -top-1 -right-1 w-2 h-2 bg-copper-600 rounded-full"></span>
            </div>
            <span class="text-[11px] mt-1 tracking-tight">Live Skor</span>
        </button>
    </nav>


    <!-- ==================== MODAL 1: RINCIAN PENDAFTAR & VERIFIKASI ==================== -->
    <template x-if="selectedReg">
        <div
            class="fixed inset-0 z-[100] flex items-start justify-center p-3 sm:p-4 pt-16 sm:pt-20"
            x-show="showDetailModal"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            @keydown.escape.window="closeDetailModal()"
        >
            <!-- Backdrop -->
            <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" @click="closeDetailModal()"></div>

            <!-- Modal Content -->
            <div
                class="relative w-full max-w-3xl max-h-[85vh] overflow-y-auto bg-white rounded-3xl shadow-2xl border border-gray-200 z-10"
                @click.stop
            >
                <!-- Modal Header -->
                <div class="sticky top-0 z-10 bg-white/95 backdrop-blur-md border-b border-gray-200 px-6 py-4 flex items-center justify-between rounded-t-3xl">
                    <div>
                        <h2 class="font-display text-lg font-bold text-gray-900">Rincian Pendaftar</h2>
                        <p class="text-xs text-gray-500 font-mono mt-0.5" x-text="'ID: ' + selectedReg.registration_id"></p>
                    </div>
                    <button @click="closeDetailModal()" class="p-2 hover:bg-gray-100 rounded-full transition">
                        <i data-lucide="x" class="w-5 h-5 text-gray-500"></i>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="p-6 space-y-6">
                    <!-- Status & Assigned Number Badge -->
                    <div class="flex items-center flex-wrap gap-2.5">
                        <span
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold rounded-full capitalize"
                            :class="{
                                'bg-amber-100 text-amber-800 border border-amber-200': (selectedReg.status || '').toLowerCase() === 'pending',
                                'bg-emerald-100 text-emerald-800 border border-emerald-200': (selectedReg.status || '').toLowerCase() === 'verified',
                                'bg-red-100 text-red-800 border border-red-200': (selectedReg.status || '').toLowerCase() === 'rejected'
                            }"
                            x-text="selectedReg.status"
                        ></span>

                        <template x-if="selectedReg.no_peserta">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold rounded-full bg-copper-100 text-copper-800 border border-copper-200 font-mono">
                                <i data-lucide="hash" class="w-3.5 h-3.5"></i>
                                No. Peserta: <span x-text="selectedReg.no_peserta"></span>
                            </span>
                        </template>
                    </div>

                    <!-- Participant Data Grid -->
                    <div class="bg-gray-50 rounded-2xl p-5 border border-gray-200">
                        <h3 class="font-display font-bold text-gray-900 mb-4 flex items-center gap-2 text-sm">
                            <i data-lucide="user" class="w-4 h-4 text-copper-600"></i>
                            Isian Formulir Pendaftaran
                        </h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs sm:text-sm">
                            <div>
                                <span class="text-gray-400 font-semibold block text-[11px] uppercase">Nama Lengkap</span>
                                <p class="font-bold text-gray-900 mt-0.5" x-text="selectedReg.nama"></p>
                            </div>
                            <div>
                                <span class="text-gray-400 font-semibold block text-[11px] uppercase">Pangkat / Korps</span>
                                <p class="font-bold text-gray-900 mt-0.5" x-text="selectedReg.pangkat || '-'"></p>
                            </div>
                            <div>
                                <span class="text-gray-400 font-semibold block text-[11px] uppercase">NRP / NIK</span>
                                <p class="font-mono font-bold text-gray-900 mt-0.5" x-text="selectedReg.nrp"></p>
                            </div>
                            <div>
                                <span class="text-gray-400 font-semibold block text-[11px] uppercase">Kesatuan / Club</span>
                                <p class="font-bold text-gray-900 mt-0.5" x-text="selectedReg.satuan"></p>
                            </div>
                            <div>
                                <span class="text-gray-400 font-semibold block text-[11px] uppercase">Kategori Pertandingan</span>
                                <p class="font-bold text-copper-700 mt-0.5 capitalize" x-text="selectedReg.kategori"></p>
                            </div>
                            <div>
                                <span class="text-gray-400 font-semibold block text-[11px] uppercase">No. WhatsApp / Telepon</span>
                                <p class="font-mono font-bold text-gray-900 mt-0.5" x-text="selectedReg.telepon"></p>
                            </div>
                            <div>
                                <span class="text-gray-400 font-semibold block text-[11px] uppercase">Email</span>
                                <p class="text-gray-700 mt-0.5" x-text="selectedReg.email || '-'"></p>
                            </div>
                            <div>
                                <span class="text-gray-400 font-semibold block text-[11px] uppercase">Waktu Daftar</span>
                                <p class="text-gray-700 mt-0.5" x-text="formatDate(selectedReg.created_at)"></p>
                            </div>
                        </div>
                    </div>

                    <!-- Images Viewer: KTA & Bukti Pembayaran -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- KTA Photo -->
                        <div class="bg-gray-50 rounded-2xl p-4 border border-gray-200">
                            <div class="flex items-center justify-between mb-3">
                                <h4 class="font-bold text-gray-800 text-xs flex items-center gap-1.5">
                                    <i data-lucide="id-card" class="w-4 h-4 text-copper-600"></i>
                                    Foto KTA / Identitas
                                </h4>
                                <template x-if="selectedReg.kta_filename">
                                    <a :href="'/uploads/kta/' + selectedReg.kta_filename" target="_blank" class="text-xs text-copper-600 font-bold hover:underline flex items-center gap-1">
                                        <span>Buka Full</span>
                                        <i data-lucide="external-link" class="w-3 h-3"></i>
                                    </a>
                                </template>
                            </div>
                            <template x-if="selectedReg.kta_filename">
                                <a :href="'/uploads/kta/' + selectedReg.kta_filename" target="_blank" class="block group relative">
                                    <img
                                        :src="'/uploads/kta/' + selectedReg.kta_filename"
                                        :alt="'KTA ' + selectedReg.nama"
                                        class="w-full h-44 object-contain rounded-xl border border-gray-200 bg-white group-hover:opacity-90 transition"
                                        onerror="this.src='data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 400 300%22><rect fill=%22%23f3f4f6%22 width=%22400%22 height=%22300%22/><text x=%2250%%22 y=%2250%%22 text-anchor=%22middle%22 fill=%22%239ca3af%22 font-size=%2214%22>Gambar tidak tersedia</text></svg>'"
                                    >
                                    <span class="absolute bottom-2 right-2 px-2 py-1 bg-black/60 backdrop-blur-md rounded text-[10px] text-white font-medium flex items-center gap-1">
                                        <i data-lucide="zoom-in" class="w-3 h-3"></i> Klik buka
                                    </span>
                                </a>
                            </template>
                            <template x-if="!selectedReg.kta_filename">
                                <div class="w-full h-44 bg-gray-100 rounded-xl flex items-center justify-center text-gray-400 text-xs">
                                    Tidak ada file KTA diunggah
                                </div>
                            </template>
                        </div>

                        <!-- Bukti Pembayaran -->
                        <div class="bg-gray-50 rounded-2xl p-4 border border-gray-200">
                            <div class="flex items-center justify-between mb-3">
                                <h4 class="font-bold text-gray-800 text-xs flex items-center gap-1.5">
                                    <i data-lucide="receipt" class="w-4 h-4 text-copper-600"></i>
                                    Bukti Transfer Pembayaran
                                </h4>
                                <template x-if="selectedReg.bukti_filename">
                                    <a :href="'/uploads/bukti/' + selectedReg.bukti_filename" target="_blank" class="text-xs text-copper-600 font-bold hover:underline flex items-center gap-1">
                                        <span>Buka Full</span>
                                        <i data-lucide="external-link" class="w-3 h-3"></i>
                                    </a>
                                </template>
                            </div>
                            <template x-if="selectedReg.bukti_filename">
                                <a :href="'/uploads/bukti/' + selectedReg.bukti_filename" target="_blank" class="block group relative">
                                    <img
                                        :src="'/uploads/bukti/' + selectedReg.bukti_filename"
                                        :alt="'Bukti ' + selectedReg.nama"
                                        class="w-full h-44 object-contain rounded-xl border border-gray-200 bg-white group-hover:opacity-90 transition"
                                        onerror="this.src='data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 400 300%22><rect fill=%22%23f3f4f6%22 width=%22400%22 height=%22300%22/><text x=%2250%%22 y=%2250%%22 text-anchor=%22middle%22 fill=%22%239ca3af%22 font-size=%2214%22>Gambar tidak tersedia</text></svg>'"
                                    >
                                    <span class="absolute bottom-2 right-2 px-2 py-1 bg-black/60 backdrop-blur-md rounded text-[10px] text-white font-medium flex items-center gap-1">
                                        <i data-lucide="zoom-in" class="w-3 h-3"></i> Klik buka
                                    </span>
                                </a>
                            </template>
                            <template x-if="!selectedReg.bukti_filename">
                                <div class="w-full h-44 bg-gray-100 rounded-xl flex items-center justify-center text-gray-400 text-xs">
                                    Tidak ada file bukti pembayaran
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Input Nomor Peserta & Catatan Admin -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">
                                Nomor Peserta (Format: BSC-26xxx)
                            </label>
                            <input
                                type="text"
                                x-model="noPesertaInput"
                                class="w-full px-3.5 py-2.5 border border-gray-300 rounded-xl bg-gray-50 text-gray-900 text-xs font-mono font-bold focus:bg-white focus:ring-2 focus:ring-copper-500"
                                placeholder="Kosongkan untuk auto-generate BSC-26xxx"
                            >
                            <p class="text-[10px] text-gray-400 mt-1">Sistem otomatis generate nomor urut BSC-26xxx saat dikonfirmasi.</p>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">
                                Catatan Admin (Opsional)
                            </label>
                            <input
                                type="text"
                                x-model="adminNotesInput"
                                class="w-full px-3.5 py-2.5 border border-gray-300 rounded-xl bg-gray-50 text-gray-900 text-xs focus:bg-white focus:ring-2 focus:ring-copper-500"
                                placeholder="Catatan verifikasi..."
                            >
                        </div>
                    </div>
                </div>

                <!-- Modal Actions Footer -->
                <div class="sticky bottom-0 z-10 bg-white/95 backdrop-blur-md border-t border-gray-200 px-6 py-4 rounded-b-3xl">
                    <div class="flex flex-col sm:flex-row gap-2.5">
                        <!-- Action Konfirmasi -->
                        <template x-if="(selectedReg.status || '').toLowerCase() !== 'verified'">
                            <button
                                type="button"
                                @click="confirmParticipant()"
                                :disabled="updating"
                                class="flex-1 px-4 py-3 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-bold rounded-xl text-xs sm:text-sm shadow-md shadow-emerald-600/20 transition flex items-center justify-center gap-2"
                            >
                                <i data-lucide="check-circle" class="w-4 h-4"></i>
                                <span x-text="updating ? 'Memproses...' : 'Konfirmasi Peserta (Generate BSC-26xxx)'"></span>
                            </button>
                        </template>

                        <!-- Action Kirim WA E-Ticket -->
                        <button
                            type="button"
                            @click="sendWhatsApp(selectedReg)"
                            class="flex-1 px-4 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl text-xs sm:text-sm shadow-md transition flex items-center justify-center gap-2"
                        >
                            <i data-lucide="send" class="w-4 h-4"></i>
                            <span>Kirim E-Ticket ke WA Peserta</span>
                        </button>

                        <!-- Action Tolak -->
                        <template x-if="(selectedReg.status || '').toLowerCase() !== 'rejected'">
                            <button
                                type="button"
                                @click="rejectParticipant()"
                                :disabled="updating"
                                class="px-4 py-3 bg-red-50 hover:bg-red-100 text-red-600 border border-red-200 font-bold rounded-xl text-xs sm:text-sm transition flex items-center justify-center gap-1.5"
                            >
                                <i data-lucide="x-circle" class="w-4 h-4"></i>
                                <span>Tolak</span>
                            </button>
                        </template>

                        <!-- Tutup -->
                        <button
                            type="button"
                            @click="closeDetailModal()"
                            class="px-4 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-xl text-xs sm:text-sm transition"
                        >
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </template>


    <!-- ==================== MODAL 2: EDIT LANGSUNG SELURUH DETAIL PESERTA ==================== -->
    <template x-if="showEditModal">
        <div
            class="fixed inset-0 z-[110] flex items-start justify-center p-3 sm:p-4 pt-14 sm:pt-16"
            x-show="showEditModal"
            x-transition
            @keydown.escape.window="showEditModal = false"
        >
            <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" @click="showEditModal = false"></div>

            <div class="relative w-full max-w-2xl max-h-[85vh] overflow-y-auto bg-white rounded-3xl shadow-2xl border border-gray-200 z-10" @click.stop>
                <!-- Header -->
                <div class="sticky top-0 z-10 bg-white/95 backdrop-blur-md border-b border-gray-200 px-6 py-4 flex items-center justify-between rounded-t-3xl">
                    <div>
                        <h3 class="font-display text-lg font-bold text-gray-900">Edit Langsung Detail Peserta</h3>
                        <p class="text-xs text-gray-500 font-mono" x-text="'ID: ' + editForm.registration_id"></p>
                    </div>
                    <button @click="showEditModal = false" class="p-2 hover:bg-gray-100 rounded-full transition">
                        <i data-lucide="x" class="w-5 h-5 text-gray-500"></i>
                    </button>
                </div>

                <!-- Form Body -->
                <form @submit.prevent="saveEditPeserta()" class="p-6 space-y-4 text-xs sm:text-sm">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block font-bold text-gray-700 mb-1">Nomor Peserta</label>
                            <input type="text" x-model="editForm.no_peserta" required
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 bg-gray-50 font-mono font-bold text-copper-700 focus:bg-white focus:ring-2 focus:ring-copper-500">
                        </div>

                        <div>
                            <label class="block font-bold text-gray-700 mb-1">Status Keikutsertaan</label>
                            <select x-model="editForm.status" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 bg-gray-50 font-bold focus:bg-white">
                                <option value="Verified">Verified (Terkonfirmasi)</option>
                                <option value="Pending">Pending</option>
                                <option value="Rejected">Rejected (Ditolak)</option>
                            </select>
                        </div>

                        <div class="sm:col-span-2">
                            <label class="block font-bold text-gray-700 mb-1">Nama Lengkap</label>
                            <input type="text" x-model="editForm.nama" required
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 bg-gray-50 font-semibold focus:bg-white">
                        </div>

                        <div>
                            <label class="block font-bold text-gray-700 mb-1">Pangkat</label>
                            <input type="text" x-model="editForm.pangkat"
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 bg-gray-50 focus:bg-white">
                        </div>

                        <div>
                            <label class="block font-bold text-gray-700 mb-1">NRP / NIK</label>
                            <input type="text" x-model="editForm.nrp" required
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 bg-gray-50 font-mono focus:bg-white">
                        </div>

                        <div>
                            <label class="block font-bold text-gray-700 mb-1">Kesatuan / Club</label>
                            <input type="text" x-model="editForm.satuan" required
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 bg-gray-50 focus:bg-white">
                        </div>

                        <div>
                            <label class="block font-bold text-gray-700 mb-1">Kategori Lomba</label>
                            <select x-model="editForm.kategori" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 bg-gray-50 font-semibold focus:bg-white">
                                <option value="presisi">Pistol Presisi 20M</option>
                                <option value="dueling">Dueling Plat</option>
                                <option value="keduanya">Keduanya (Presisi & Dueling Plat)</option>
                            </select>
                        </div>

                        <div>
                            <label class="block font-bold text-gray-700 mb-1">No. WhatsApp / Telepon</label>
                            <input type="text" x-model="editForm.telepon" required
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 bg-gray-50 font-mono focus:bg-white">
                        </div>

                        <div>
                            <label class="block font-bold text-gray-700 mb-1">Email</label>
                            <input type="email" x-model="editForm.email"
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 bg-gray-50 focus:bg-white">
                        </div>

                        <div class="sm:col-span-2">
                            <label class="block font-bold text-gray-700 mb-1">Catatan Admin</label>
                            <textarea x-model="editForm.admin_notes" rows="2"
                                      class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 bg-gray-50 focus:bg-white resize-none"
                                      placeholder="Catatan tambahan..."></textarea>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-2.5 pt-4 border-t border-gray-200">
                        <button type="button" @click="showEditModal = false" class="px-4 py-2.5 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold transition">
                            Batal
                        </button>
                        <button type="submit" :disabled="savingEdit" class="px-6 py-2.5 rounded-xl bg-copper-600 hover:bg-copper-700 text-white font-bold transition shadow-md flex items-center gap-1.5">
                            <i data-lucide="check" class="w-4 h-4"></i>
                            <span x-text="savingEdit ? 'Menyimpan...' : 'Simpan Perubahan'"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </template>


    <!-- ==================== MODAL 3: TAMBAH PESERTA LANGSUNG ==================== -->
    <template x-if="showAddModal">
        <div
            class="fixed inset-0 z-[110] flex items-start justify-center p-3 sm:p-4 pt-14 sm:pt-16"
            x-show="showAddModal"
            x-transition
            @keydown.escape.window="showAddModal = false"
        >
            <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" @click="showAddModal = false"></div>

            <div class="relative w-full max-w-2xl max-h-[85vh] overflow-y-auto bg-white rounded-3xl shadow-2xl border border-gray-200 z-10" @click.stop>
                <!-- Header -->
                <div class="sticky top-0 z-10 bg-white/95 backdrop-blur-md border-b border-gray-200 px-6 py-4 flex items-center justify-between rounded-t-3xl">
                    <div>
                        <h3 class="font-display text-lg font-bold text-gray-900">+ Tambah Peserta Baru Langsung</h3>
                        <p class="text-xs text-gray-500">Daftarkan peserta secara manual langsung oleh admin dengan status Verified.</p>
                    </div>
                    <button @click="showAddModal = false" class="p-2 hover:bg-gray-100 rounded-full transition">
                        <i data-lucide="x" class="w-5 h-5 text-gray-500"></i>
                    </button>
                </div>

                <!-- Form Body -->
                <form @submit.prevent="submitAddPeserta()" class="p-6 space-y-4 text-xs sm:text-sm">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="sm:col-span-2">
                            <label class="block font-bold text-gray-700 mb-1">Nama Lengkap Peserta *</label>
                            <input type="text" x-model="addForm.nama" required placeholder="Contoh: Bripda Andi Pratama"
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 bg-gray-50 font-semibold focus:bg-white">
                        </div>

                        <div>
                            <label class="block font-bold text-gray-700 mb-1">Pangkat</label>
                            <input type="text" x-model="addForm.pangkat" placeholder="Contoh: Bripda / Bharada"
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 bg-gray-50 focus:bg-white">
                        </div>

                        <div>
                            <label class="block font-bold text-gray-700 mb-1">NRP / NIK *</label>
                            <input type="text" x-model="addForm.nrp" required placeholder="Contoh: 98010234"
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 bg-gray-50 font-mono focus:bg-white">
                        </div>

                        <div>
                            <label class="block font-bold text-gray-700 mb-1">Kesatuan / Club *</label>
                            <input type="text" x-model="addForm.satuan" required placeholder="Contoh: Resimen I Pasukan Pelopor"
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 bg-gray-50 focus:bg-white">
                        </div>

                        <div>
                            <label class="block font-bold text-gray-700 mb-1">Kategori Lomba *</label>
                            <select x-model="addForm.kategori" required class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 bg-gray-50 font-semibold focus:bg-white">
                                <option value="presisi">Pistol Presisi 20M</option>
                                <option value="dueling">Dueling Plat</option>
                                <option value="keduanya">Keduanya (Presisi & Dueling Plat)</option>
                            </select>
                        </div>

                        <div>
                            <label class="block font-bold text-gray-700 mb-1">Nomor WhatsApp / HP *</label>
                            <input type="text" x-model="addForm.telepon" required placeholder="Contoh: 081234567890"
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 bg-gray-50 font-mono focus:bg-white">
                        </div>

                        <div>
                            <label class="block font-bold text-gray-700 mb-1">Email (Opsional)</label>
                            <input type="email" x-model="addForm.email" placeholder="email@gmail.com"
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 bg-gray-50 focus:bg-white">
                        </div>

                        <div>
                            <label class="block font-bold text-gray-700 mb-1">Nomor Peserta (Opsional)</label>
                            <input type="text" x-model="addForm.no_peserta" placeholder="Kosongkan untuk auto BSC-26xxx"
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 bg-gray-50 font-mono focus:bg-white">
                        </div>

                        <div>
                            <label class="block font-bold text-gray-700 mb-1">Status Pendaftaran</label>
                            <select x-model="addForm.status" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 bg-gray-50 font-bold focus:bg-white">
                                <option value="Verified">Verified (Langsung Resmi)</option>
                                <option value="Pending">Pending</option>
                            </select>
                        </div>

                        <div class="sm:col-span-2">
                            <label class="block font-bold text-gray-700 mb-1">Catatan Tambahan</label>
                            <input type="text" x-model="addForm.admin_notes" placeholder="Catatan internal panitia..."
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 bg-gray-50 focus:bg-white">
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-2.5 pt-4 border-t border-gray-200">
                        <button type="button" @click="showAddModal = false" class="px-4 py-2.5 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold transition">
                            Batal
                        </button>
                        <button type="submit" :disabled="savingAdd" class="px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold transition shadow-md flex items-center gap-1.5">
                            <i data-lucide="user-plus" class="w-4 h-4"></i>
                            <span x-text="savingAdd ? 'Menambahkan...' : 'Daftarkan Peserta'"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </template>


    <!-- ==================== TOAST NOTIFICATION ==================== -->
    <div
        x-show="toast.show"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-3"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-3"
        class="fixed bottom-20 md:bottom-6 right-4 sm:right-6 z-[200] max-w-sm"
        x-cloak
    >
        <div
            class="px-5 py-3.5 rounded-2xl shadow-xl font-bold text-xs sm:text-sm flex items-center gap-3 border"
            :class="{
                'bg-emerald-600 text-white border-emerald-500': toast.type === 'success',
                'bg-red-600 text-white border-red-500': toast.type === 'error',
                'bg-copper-600 text-white border-copper-500': toast.type === 'info'
            }"
        >
            <i data-lucide="check-circle" class="w-5 h-5 flex-shrink-0" x-show="toast.type === 'success'"></i>
            <i data-lucide="alert-circle" class="w-5 h-5 flex-shrink-0" x-show="toast.type === 'error'"></i>
            <i data-lucide="info" class="w-5 h-5 flex-shrink-0" x-show="toast.type === 'info'"></i>
            <span x-text="toast.message"></span>
        </div>
    </div>

</section>

<!-- ==================== JAVASCRIPT LOGIC ==================== -->
<script>
function adminDashboard() {
    return {
        // Active Navigation Tab: 'antrean' (DEFAULT) | 'peserta' | 'scores'
        activeTab: 'antrean',
        scoresSubTab: 'presisi', // 'presisi' | 'dueling'

        // Data arrays
        registrations: [],
        filtered: [],
        filteredPeserta: [],
        presisiList: [],
        duelingList: [],

        // Filter & Search states
        searchQuery: '',
        statusFilter: 'all',
        pesertaSearch: '',
        pesertaCategoryFilter: 'all',

        // Loading states
        loading: true,
        updating: false,
        savingEdit: false,
        savingAdd: false,
        isSyncing: false,

        // Modals state
        showDetailModal: false,
        selectedReg: null,
        noPesertaInput: '',
        adminNotesInput: '',

        showEditModal: false,
        editForm: {
            id: null,
            registration_id: '',
            nama: '',
            pangkat: '',
            nrp: '',
            satuan: '',
            telepon: '',
            email: '',
            kategori: 'presisi',
            status: 'Verified',
            no_peserta: '',
            admin_notes: ''
        },

        showAddModal: false,
        addForm: {
            nama: '',
            pangkat: '',
            nrp: '',
            satuan: '',
            telepon: '',
            email: '',
            kategori: 'presisi',
            status: 'Verified',
            no_peserta: '',
            admin_notes: ''
        },

        newMatch: {
            round_name: 'Penyisihan',
            match_number: 1,
            participant_1_name: '',
            participant_2_name: ''
        },

        // Toast & Stats
        toast: { show: false, message: '', type: 'success' },
        stats: { total: 0, pending: 0, verified: 0, rejected: 0 },

        // Injected Config
        adminToken: <?= json_encode(ADMIN_TOKEN) ?>,
        siteUrl: <?= json_encode(SITE_URL) ?>,

        async init() {
            // Default active tab is 'antrean'
            this.activeTab = 'antrean';
            await this.fetchRegistrations();
            await this.fetchPresisiScores();
            await this.fetchDuelingMatches();
        },

        setTab(tab) {
            this.activeTab = tab;
            this.$nextTick(() => {
                if (typeof lucide !== 'undefined') lucide.createIcons();
            });
        },

        async fetchRegistrations() {
            this.loading = true;
            try {
                const res = await fetch('/api/registrations.php?token=' + encodeURIComponent(this.adminToken));
                const data = await res.json();
                if (data.success && Array.isArray(data.data)) {
                    this.registrations = data.data;
                } else if (Array.isArray(data)) {
                    this.registrations = data;
                }
                this.calculateStats();
                this.filterRegistrations();
                this.filterPeserta();
            } catch (e) {
                console.error('Error fetching registrations:', e);
                this.showToast('Gagal memuat data pendaftaran: ' + e.message, 'error');
            } finally {
                this.loading = false;
                this.$nextTick(() => {
                    if (typeof lucide !== 'undefined') lucide.createIcons();
                });
            }
        },

        calculateStats() {
            const regs = this.registrations;
            this.stats = {
                total: regs.length,
                pending: regs.filter(r => (r.status || '').toLowerCase() === 'pending').length,
                verified: regs.filter(r => (r.status || '').toLowerCase() === 'verified').length,
                rejected: regs.filter(r => (r.status || '').toLowerCase() === 'rejected').length
            };
        },

        filterRegistrations() {
            let res = [...this.registrations];
            if (this.statusFilter !== 'all') {
                res = res.filter(r => (r.status || '').toLowerCase() === this.statusFilter.toLowerCase());
            }
            if (this.searchQuery.trim()) {
                const q = this.searchQuery.toLowerCase().trim();
                res = res.filter(r =>
                    (r.nama && r.nama.toLowerCase().includes(q)) ||
                    (r.registration_id && r.registration_id.toLowerCase().includes(q)) ||
                    (r.nrp && r.nrp.toLowerCase().includes(q)) ||
                    (r.satuan && r.satuan.toLowerCase().includes(q)) ||
                    (r.no_peserta && r.no_peserta.toLowerCase().includes(q))
                );
            }
            this.filtered = res;
        },

        filterPeserta() {
            let res = this.registrations.filter(r => (r.status || '').toLowerCase() === 'verified');
            if (this.pesertaCategoryFilter !== 'all') {
                res = res.filter(r => (r.kategori || '').toLowerCase().includes(this.pesertaCategoryFilter.toLowerCase()));
            }
            if (this.pesertaSearch.trim()) {
                const q = this.pesertaSearch.toLowerCase().trim();
                res = res.filter(r =>
                    (r.nama && r.nama.toLowerCase().includes(q)) ||
                    (r.no_peserta && r.no_peserta.toLowerCase().includes(q)) ||
                    (r.nrp && r.nrp.toLowerCase().includes(q)) ||
                    (r.satuan && r.satuan.toLowerCase().includes(q))
                );
            }
            this.filteredPeserta = res;
        },

        // Detail Modal
        openDetail(reg) {
            this.selectedReg = { ...reg };
            this.noPesertaInput = reg.no_peserta || '';
            this.adminNotesInput = reg.admin_notes || '';
            this.showDetailModal = true;
            this.$nextTick(() => {
                if (typeof lucide !== 'undefined') lucide.createIcons();
            });
        },

        closeDetailModal() {
            this.showDetailModal = false;
            this.selectedReg = null;
        },

        async confirmParticipant() {
            if (!this.selectedReg) return;
            if (!confirm('Konfirmasi pendaftaran ' + this.selectedReg.nama + ' menjadi Peserta Resmi?')) return;

            this.updating = true;
            try {
                const payload = {
                    action: 'edit',
                    registration_id: this.selectedReg.registration_id,
                    status: 'Verified',
                    no_peserta: this.noPesertaInput.trim(),
                    admin_notes: this.adminNotesInput.trim()
                };

                const res = await fetch('/api/participants.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-Admin-Token': this.adminToken },
                    body: JSON.stringify(payload)
                });
                const data = await res.json();
                if (!res.ok || !data.success) throw new Error(data.error || 'Gagal mengkonfirmasi');

                const updated = data.data;
                this.selectedReg.status = 'Verified';
                this.selectedReg.no_peserta = updated.no_peserta;
                this.noPesertaInput = updated.no_peserta;

                // Update in registrations list
                const idx = this.registrations.findIndex(r => r.registration_id === updated.registration_id);
                if (idx !== -1) {
                    this.registrations[idx] = updated;
                }

                this.calculateStats();
                this.filterRegistrations();
                this.filterPeserta();
                this.showToast('Berhasil dikonfirmasi! No. Peserta: ' + updated.no_peserta, 'success');
            } catch (err) {
                this.showToast(err.message, 'error');
            } finally {
                this.updating = false;
            }
        },

        async rejectParticipant() {
            if (!this.selectedReg) return;
            if (!confirm('Tolak pendaftaran ' + this.selectedReg.nama + '?')) return;

            this.updating = true;
            try {
                const payload = {
                    action: 'edit',
                    registration_id: this.selectedReg.registration_id,
                    status: 'Rejected',
                    admin_notes: this.adminNotesInput.trim()
                };

                const res = await fetch('/api/participants.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-Admin-Token': this.adminToken },
                    body: JSON.stringify(payload)
                });
                const data = await res.json();
                if (!res.ok || !data.success) throw new Error(data.error || 'Gagal menolak');

                const updated = data.data;
                this.selectedReg.status = 'Rejected';

                const idx = this.registrations.findIndex(r => r.registration_id === updated.registration_id);
                if (idx !== -1) {
                    this.registrations[idx] = updated;
                }

                this.calculateStats();
                this.filterRegistrations();
                this.filterPeserta();
                this.showToast('Pendaftaran ditolak', 'info');
            } catch (err) {
                this.showToast(err.message, 'error');
            } finally {
                this.updating = false;
            }
        },

        // Edit Peserta Langsung
        openEditPeserta(p) {
            this.editForm = {
                id: p.id,
                registration_id: p.registration_id,
                nama: p.nama || '',
                pangkat: p.pangkat || '',
                nrp: p.nrp || '',
                satuan: p.satuan || '',
                telepon: p.telepon || '',
                email: p.email || '',
                kategori: p.kategori || 'presisi',
                status: p.status || 'Verified',
                no_peserta: p.no_peserta || '',
                admin_notes: p.admin_notes || ''
            };
            this.showEditModal = true;
            this.$nextTick(() => {
                if (typeof lucide !== 'undefined') lucide.createIcons();
            });
        },

        async saveEditPeserta() {
            this.savingEdit = true;
            try {
                const payload = {
                    action: 'edit',
                    ...this.editForm
                };

                const res = await fetch('/api/participants.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-Admin-Token': this.adminToken },
                    body: JSON.stringify(payload)
                });
                const data = await res.json();
                if (!res.ok || !data.success) throw new Error(data.error || 'Gagal menyimpan perubahan');

                const updated = data.data;
                const idx = this.registrations.findIndex(r => r.registration_id === updated.registration_id);
                if (idx !== -1) {
                    this.registrations[idx] = updated;
                }

                this.calculateStats();
                this.filterRegistrations();
                this.filterPeserta();
                this.showEditModal = false;
                this.showToast('Detail peserta berhasil diperbarui!', 'success');
            } catch (err) {
                this.showToast(err.message, 'error');
            } finally {
                this.savingEdit = false;
            }
        },

        // Tambah Peserta Langsung
        openAddPeserta() {
            this.addForm = {
                nama: '',
                pangkat: '',
                nrp: '',
                satuan: '',
                telepon: '',
                email: '',
                kategori: 'presisi',
                status: 'Verified',
                no_peserta: '',
                admin_notes: ''
            };
            this.showAddModal = true;
            this.$nextTick(() => {
                if (typeof lucide !== 'undefined') lucide.createIcons();
            });
        },

        async submitAddPeserta() {
            this.savingAdd = true;
            try {
                const payload = {
                    action: 'create',
                    ...this.addForm
                };

                const res = await fetch('/api/participants.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-Admin-Token': this.adminToken },
                    body: JSON.stringify(payload)
                });
                const data = await res.json();
                if (!res.ok || !data.success) throw new Error(data.error || 'Gagal menambahkan peserta');

                const newReg = data.data;
                this.registrations.unshift(newReg);

                this.calculateStats();
                this.filterRegistrations();
                this.filterPeserta();
                this.showAddModal = false;
                this.showToast(data.message || 'Peserta baru berhasil ditambahkan!', 'success');
            } catch (err) {
                this.showToast(err.message, 'error');
            } finally {
                this.savingAdd = false;
            }
        },

        async deletePeserta(p) {
            if (!confirm(`Hapus peserta "${p.nama}" (${p.no_peserta || p.registration_id}) secara permanen?`)) return;

            try {
                const res = await fetch('/api/participants.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-Admin-Token': this.adminToken },
                    body: JSON.stringify({ action: 'delete', registration_id: p.registration_id })
                });
                const data = await res.json();
                if (!res.ok || !data.success) throw new Error(data.error || 'Gagal menghapus');

                this.registrations = this.registrations.filter(r => r.registration_id !== p.registration_id);
                this.calculateStats();
                this.filterRegistrations();
                this.filterPeserta();
                this.showToast('Peserta berhasil dihapus', 'info');
            } catch (err) {
                this.showToast(err.message, 'error');
            }
        },

        // WhatsApp Sender with E-Ticket & Detailed Instructions
        normalizePhone(phone) {
            if (!phone) return '';
            let cleaned = String(phone).replace(/[^\d+]/g, '').replace(/^\+/, '');
            if (cleaned.startsWith('0')) {
                cleaned = '62' + cleaned.substring(1);
            } else if (cleaned.startsWith('8')) {
                cleaned = '62' + cleaned;
            } else if (!cleaned.startsWith('62')) {
                cleaned = '62' + cleaned;
            }
            return cleaned;
        },

        sendWhatsApp(reg) {
            if (!reg) return;
            const phone = this.normalizePhone(reg.telepon);
            if (!phone) {
                this.showToast('Nomor telepon peserta tidak valid.', 'error');
                return;
            }

            const noPeserta = reg.no_peserta || '-';
            const kategoriLabel = reg.kategori || '-';
            const eTicketUrl = this.siteUrl + '/e-ticket.php?id=' + encodeURIComponent(reg.registration_id);
            const isVerified = (reg.status || '').toLowerCase() === 'verified';
            const statusLabel = isVerified ? '✅ LUNAS / TERKONFIRMASI RESMI' : '⏳ PENDING (Menunggu Konfirmasi)';

            const message = `🎯 *BDA SHOOTING CHAMPIONSHIP 2026*
*RESIMEN I PASUKAN PELOPOR KORPS BRIMOB POLRI*
━━━━━━━━━━━━━━━━━━━━━━━━━━━━
*PENGESAHAN PENDAFTARAN & E-TICKET RESMI*

Halo Bapak/Ibu/Sdr *${reg.nama}*,
Pendaftaran Anda pada BDA Shooting Championship 2026 telah *DIKONFIRMASI & TERVERIFIKASI RESMI*.

📋 *RINCIAN PESERTA:*
• No. Peserta: *${noPeserta}*
• ID Registrasi: ${reg.registration_id}
• Nama Lengkap: ${reg.nama}
• Pangkat: ${reg.pangkat || '-'}
• NRP / NIK: ${reg.nrp}
• Satuan / Club: ${reg.satuan}
• Kategori Lomba: *${kategoriLabel}*
• Status Pembayaran: *${statusLabel}*

🎫 *LINK E-TICKET & QR CODE RESMI:*
${eTicketUrl}
*(Tautan unik di atas memuat QR Code resmi dan tanda pengenal peserta)*

⚠️ *CATATAN PENTING PESERTA (WAJIB DIBACA):*
1. *Simpan pesan ini, link E-Ticket, dan QR Code* agar tidak hilang.
2. Tunjukkan E-Ticket & QR Code ini (pada layar HP atau cetak) saat *Daftar Ulang* di lokasi kejuaraan.
3. *Wajib membawa fisik KTA & KTP Asli* untuk verifikasi data keabsahan peserta di meja panitia.
4. Seluruh peserta *Wajib Hadir saat Technical Meeting (TM)* sebelum rangkaian pertandingan dimulai.

📍 *Lokasi:* Lapangan Tembak Shooting House, Resimen I Pasukan Pelopor, Kedung Halang, Bogor
📅 *Pelaksanaan:* 17 — 18 Oktober 2026

Salam Hormat,
*Panitia Pelaksana BDA Shooting Championship 2026*`;

            const waUrl = 'https://wa.me/' + phone + '?text=' + encodeURIComponent(message);
            window.open(waUrl, '_blank');
            this.showToast('Membuka WhatsApp untuk ' + reg.nama, 'success');
        },

        // Live Skor Methods
        calculateTotal(item) {
            let sum = 0;
            for (let i = 1; i <= 10; i++) {
                sum += Number(item['seri_' + i] || 0);
            }
            item.total_score = sum;
        },

        async fetchPresisiScores() {
            try {
                const res = await fetch('/api/scores-presisi.php?token=' + encodeURIComponent(this.adminToken));
                const data = await res.json();
                if (data.success) {
                    this.presisiList = (data.data || []).map(row => {
                        this.calculateTotal(row);
                        return row;
                    });
                }
            } catch (e) {
                console.error('Error fetching presisi:', e);
            }
        },

        async fetchDuelingMatches() {
            try {
                const res = await fetch('/api/scores-dueling.php?token=' + encodeURIComponent(this.adminToken));
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
                const res = await fetch('/api/registrations.php?token=' + encodeURIComponent(this.adminToken));
                const data = await res.json();
                if (data.success) {
                    const verifiedPresisi = (data.data || []).filter(r =>
                        (r.status || '').toLowerCase() === 'verified' &&
                        ((r.kategori || '').toLowerCase().includes('presisi') || (r.kategori || '').toLowerCase().includes('keduanya'))
                    );

                    let addedCount = 0;
                    for (const r of verifiedPresisi) {
                        const exists = this.presisiList.find(p => p.registration_id === r.registration_id);
                        if (!exists) {
                            await fetch('/api/scores-presisi.php', {
                                method: 'POST',
                                headers: { 'Content-Type': 'application/json', 'X-Admin-Token': this.adminToken },
                                body: JSON.stringify({
                                    registration_id: r.registration_id,
                                    no_peserta: r.no_peserta,
                                    nama: r.nama,
                                    satuan: r.satuan,
                                    seri_1: 0, seri_2: 0, seri_3: 0, seri_4: 0, seri_5: 0,
                                    seri_6: 0, seri_7: 0, seri_8: 0, seri_9: 0, seri_10: 0,
                                    x_count: 0
                                })
                            });
                            addedCount++;
                        }
                    }
                    await this.fetchPresisiScores();
                    this.showToast(`Sinkronisasi selesai! ${addedCount} peserta baru ditambahkan ke papan skor.`, 'success');
                }
            } catch (e) {
                this.showToast('Gagal menyinkronkan: ' + e.message, 'error');
            } finally {
                this.isSyncing = false;
            }
        },

        async savePresisiScore(item) {
            item._saving = true;
            this.calculateTotal(item);
            try {
                const res = await fetch('/api/scores-presisi.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-Admin-Token': this.adminToken },
                    body: JSON.stringify(item)
                });
                const data = await res.json();
                if (data.success) {
                    this.showToast('Skor ' + item.nama + ' berhasil disimpan!', 'success');
                    await this.fetchPresisiScores();
                } else {
                    throw new Error(data.error || 'Gagal menyimpan skor');
                }
            } catch (e) {
                this.showToast(e.message, 'error');
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
                match_status: 'upcoming'
            };

            try {
                const res = await fetch('/api/scores-dueling.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-Admin-Token': this.adminToken },
                    body: JSON.stringify(payload)
                });
                const data = await res.json();
                if (data.success) {
                    this.showToast('Match baru berhasil ditambahkan!', 'success');
                    this.newMatch.match_number++;
                    this.newMatch.participant_1_name = '';
                    this.newMatch.participant_2_name = '';
                    await this.fetchDuelingMatches();
                } else {
                    throw new Error(data.error || 'Gagal menambahkan match');
                }
            } catch (e) {
                this.showToast(e.message, 'error');
            }
        },

        async saveDuelingMatch(match) {
            match._saving = true;
            try {
                const res = await fetch('/api/scores-dueling.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-Admin-Token': this.adminToken },
                    body: JSON.stringify(match)
                });
                const data = await res.json();
                if (data.success) {
                    this.showToast('Data match #' + match.match_number + ' diperbarui!', 'success');
                } else {
                    throw new Error(data.error || 'Gagal menyimpan match');
                }
            } catch (e) {
                this.showToast(e.message, 'error');
            } finally {
                match._saving = false;
            }
        },

        async deleteDuelingMatch(match) {
            if (!confirm(`Hapus pertandingan Match #${match.match_number} (${match.participant_1_name} vs ${match.participant_2_name})?`)) return;

            try {
                const res = await fetch('/api/scores-dueling.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-Admin-Token': this.adminToken },
                    body: JSON.stringify({ action: 'delete', id: match.id })
                });
                const data = await res.json();
                if (data.success) {
                    this.duelingList = this.duelingList.filter(m => m.id !== match.id);
                    this.showToast('Match berhasil dihapus', 'info');
                } else {
                    throw new Error(data.error || 'Gagal menghapus match');
                }
            } catch (e) {
                this.showToast(e.message, 'error');
            }
        },

        formatDate(dateStr) {
            if (!dateStr) return '-';
            const d = new Date(dateStr);
            if (isNaN(d.getTime())) return dateStr;
            return d.toLocaleDateString('id-ID', {
                day: '2-digit',
                month: 'short',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        },

        showToast(message, type = 'success') {
            this.toast = { show: true, message, type };
            this.$nextTick(() => {
                if (typeof lucide !== 'undefined') lucide.createIcons();
            });
            setTimeout(() => {
                this.toast.show = false;
            }, 4000);
        }
    };
}
</script>
<?php endif; ?>

<?php include __DIR__ . '/../includes/footer.php'; ?>
