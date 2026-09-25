<?php
/**
 * BDA Shooting Championship 2026 — Admin Dashboard
 * 3 Core Menus: Antrean Pendaftar (default), Peserta (direct edit & direct add), Live Skor (direct scoring & brackets)
 * Mobile bottom navigation, Excel exports, WhatsApp E-ticket integration.
 */
session_start();
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: 0");
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';

// Handle login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['admin_login'])) {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($username) || empty($password)) {
        $loginError = 'ID Admin dan Password wajib diisi.';
    } else {
        $db = getDB();
        try {
            $stmt = $db->prepare('SELECT * FROM admin_users WHERE username = ? LIMIT 1');
            $stmt->execute([$username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user) {
                // Initial fallback for default superadmin
                if ($username === ADMIN_USER && $password === ADMIN_PASS) {
                    $hash = password_hash(ADMIN_PASS, PASSWORD_BCRYPT);
                    $ins = $db->prepare('INSERT INTO admin_users (username, password_hash, display_name, role, permissions, is_active) VALUES (?, ?, ?, ?, ?, 1)');
                    $ins->execute([ADMIN_USER, $hash, 'Super Administrator', 'superadmin', json_encode(['antrean', 'peserta', 'scores', 'users'])]);
                    $user = [
                        'id' => (int)$db->lastInsertId(),
                        'username' => ADMIN_USER,
                        'display_name' => 'Super Administrator',
                        'role' => 'superadmin',
                        'permissions' => ['antrean', 'peserta', 'scores', 'users'],
                        'is_active' => 1
                    ];
                }
            }

            if ($user && password_verify($password, $user['password_hash'])) {
                if ((int)$user['is_active'] !== 1) {
                    $loginError = 'Akun admin Anda sedang dinonaktifkan oleh Super Admin.';
                } else {
                    $db->prepare('UPDATE admin_users SET last_login = NOW() WHERE id = ?')->execute([$user['id']]);
                    $perms = $user['permissions'];
                    if (is_string($perms)) {
                        $perms = json_decode($perms, true) ?: [];
                    }
                    $_SESSION['admin_logged_in'] = true;
                    $_SESSION['admin_user'] = [
                        'id' => (int)$user['id'],
                        'username' => $user['username'],
                        'display_name' => $user['display_name'],
                        'role' => $user['role'],
                        'permissions' => $perms
                    ];
                    header('Location: /admin/');
                    exit;
                }
            } else if (!isset($loginError)) {
                $loginError = 'ID Admin atau Password salah. Silakan coba lagi.';
            }
        } catch (PDOException $e) {
            // Emergency fallback if table not yet migrated
            if ($username === ADMIN_USER && $password === ADMIN_PASS) {
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_user'] = [
                    'id' => 1,
                    'username' => ADMIN_USER,
                    'display_name' => 'Super Administrator',
                    'role' => 'superadmin',
                    'permissions' => ['antrean', 'peserta', 'scores', 'users']
                ];
                header('Location: /admin/');
                exit;
            } else {
                $loginError = 'Terjadi kesalahan sistem: ' . $e->getMessage();
            }
        }
    }
}

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: /admin/');
    exit;
}

$currentUser = getCurrentUser();
$isLoggedIn = isAdminLoggedIn();

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

                <div class="mb-4">
                    <label for="username" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">ID Admin / Username</label>
                    <input
                        type="text"
                        name="username"
                        id="username"
                        required
                        autofocus
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl bg-gray-50 text-gray-900 focus:bg-white focus:ring-2 focus:ring-copper-500 focus:border-copper-500 text-sm transition"
                        placeholder="Contoh: superadmin atau nama admin"
                    >
                </div>

                <div class="mb-6">
                    <label for="password" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Password Admin</label>
                    <div class="relative" x-data="{ show: false }">
                        <input
                            :type="show ? 'text' : 'password'"
                            name="password"
                            id="password"
                            required
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
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider <?= ($currentUser['role'] ?? '') === 'superadmin' ? 'bg-purple-100 text-purple-800 border border-purple-200' : 'bg-copper-100 text-copper-800 border border-copper-200' ?>">
                            <?= htmlspecialchars(($currentUser['role'] ?? '') === 'superadmin' ? 'Super Admin' : 'Admin') ?>
                        </span>
                        <span class="text-xs text-gray-400">•</span>
                        <span class="text-xs text-gray-700 font-semibold"><?= htmlspecialchars($currentUser['display_name'] ?? 'Admin') ?> (<?= htmlspecialchars($currentUser['username'] ?? '') ?>)</span>
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

        <!-- ==================== DESKTOP TOP NAVIGATION TABS (Modular Leveled Access) ==================== -->
        <div class="hidden md:flex items-center space-x-2 bg-white p-1.5 rounded-2xl border border-gray-200 shadow-sm mb-6">
            <!-- Tab 1: Antrean Pendaftar -->
            <button
                x-show="canAccess('antrean')"
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
                x-show="canAccess('peserta')"
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
                x-show="canAccess('scores')"
                @click="setTab('scores')"
                type="button"
                class="flex-1 py-2.5 px-4 rounded-xl text-xs sm:text-sm font-bold transition flex items-center justify-center gap-2"
                :class="activeTab === 'scores' ? 'bg-copper-600 text-white shadow-md shadow-copper-600/20' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900'"
            >
                <i data-lucide="crosshair" class="w-4 h-4"></i>
                <span>Live Skor</span>
                <span class="w-2 h-2 rounded-full" :class="activeTab === 'scores' ? 'bg-white animate-pulse' : 'bg-copper-500'"></span>
            </button>

            <!-- Tab 4: Kelola Akun (Super Admin / Users Permission) -->
            <button
                x-show="canAccess('users')"
                @click="setTab('users')"
                type="button"
                class="flex-1 py-2.5 px-4 rounded-xl text-xs sm:text-sm font-bold transition flex items-center justify-center gap-2"
                :class="activeTab === 'users' ? 'bg-copper-600 text-white shadow-md shadow-copper-600/20' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900'"
            >
                <i data-lucide="users-cog" class="w-4 h-4"></i>
                <span>Kelola Akun</span>
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
                <div class="flex flex-col sm:flex-row flex-wrap items-stretch sm:items-center gap-3">
                    <div class="relative flex-1 min-w-[200px]">
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

                    <!-- Toggle Sembunyikan / Tampilkan yang Sudah Dikonfirmasi -->
                    <button
                        type="button"
                        @click="showVerifiedInAntrean = !showVerifiedInAntrean; filterRegistrations()"
                        class="inline-flex items-center justify-center gap-1.5 px-3.5 py-2.5 rounded-xl text-xs font-bold border transition select-none"
                        :class="showVerifiedInAntrean ? 'bg-emerald-50 text-emerald-800 border-emerald-300 shadow-sm' : 'bg-gray-50 hover:bg-gray-100 text-gray-700 border-gray-300'"
                    >
                        <i data-lucide="eye" class="w-3.5 h-3.5" x-show="!showVerifiedInAntrean"></i>
                        <i data-lucide="eye-off" class="w-3.5 h-3.5" x-show="showVerifiedInAntrean"></i>
                        <span x-text="showVerifiedInAntrean ? 'Sembunyikan Terkonfirmasi' : 'Tampilkan Terkonfirmasi (' + stats.verified + ')'"></span>
                    </button>

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
                                        <ul class="space-y-1 list-none p-0 m-0">
                                            <template x-for="item in formatKategoriList(reg.kategori)" :key="item">
                                                <li class="flex items-center gap-1.5 text-xs font-semibold text-gray-800">
                                                    <span class="w-1.5 h-1.5 rounded-full shrink-0"
                                                          :class="item.toLowerCase().includes('presisi') ? 'bg-blue-600' : (item.toLowerCase().includes('dueling') ? 'bg-purple-600' : 'bg-gray-400')"></span>
                                                    <span x-text="item"></span>
                                                </li>
                                            </template>
                                        </ul>
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
                                <tr class="hover:bg-emerald-50/40 transition cursor-pointer" @click="openDetail(p)">
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
                                        <ul class="space-y-1 list-none p-0 m-0">
                                            <template x-for="item in formatKategoriList(p.kategori)" :key="item">
                                                <li class="flex items-center gap-1.5 text-xs font-semibold text-gray-800">
                                                    <span class="w-1.5 h-1.5 rounded-full shrink-0"
                                                          :class="item.toLowerCase().includes('presisi') ? 'bg-blue-600' : (item.toLowerCase().includes('dueling') ? 'bg-purple-600' : 'bg-gray-400')"></span>
                                                    <span x-text="item"></span>
                                                </li>
                                            </template>
                                        </ul>
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
                                        <div class="flex items-center justify-center gap-1.5" @click.stop>
                                            <!-- Button Edit Langsung -->
                                            <button
                                                type="button"
                                                @click.stop="openEditPeserta(p)"
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
                                                @click.stop
                                                class="p-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition text-xs"
                                                title="Buka E-Ticket"
                                            >
                                                <i data-lucide="ticket" class="w-3.5 h-3.5"></i>
                                            </a>

                                            <!-- Button Kirim WA -->
                                            <button
                                                type="button"
                                                @click.stop="sendWhatsApp(p)"
                                                class="p-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200 rounded-lg transition text-xs"
                                                title="Kirim E-Ticket ke WA"
                                            >
                                                <i data-lucide="send" class="w-3.5 h-3.5"></i>
                                            </button>

                                            <!-- Button Hapus Peserta -->
                                            <button
                                                type="button"
                                                @click.stop="deletePeserta(p)"
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
                        <p class="font-bold text-gray-800">Panduan Input Skor Ring Presisi 20M:</p>
                        <p>Ketik jumlah peluru masuk pada masing-masing ring (10 s/d 1). <strong>Jumlah Masuk</strong> dan <strong>Nilai</strong> otomatis dihitung secara real-time. Nilai <strong>X</strong> tidak masuk dalam Nilai maupun Jumlah Masuk, namun menjadi penentu utama jika terjadi nilai sama. Klik <strong>Simpan</strong> untuk memperbarui skor.</p>
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
                                    <th class="py-3 px-3 w-10 text-center">Rank</th>
                                    <th class="py-3 px-3 w-20">No. Peserta</th>
                                    <th class="py-3 px-3">Nama &amp; Satuan</th>
                                    <th class="py-3 px-1 text-center w-11 bg-amber-50 text-amber-700 font-extrabold" title="Inner X (Penentu seri sama)">X</th>
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
                                    <tr class="hover:bg-gray-50/80 transition">
                                        <td class="py-2 px-3 text-center font-bold text-gray-500" x-text="index + 1"></td>
                                        <td class="py-2 px-3 font-mono font-bold text-copper-700" x-text="item.no_peserta || '-'"></td>
                                        <td class="py-2 px-3">
                                            <div class="font-bold text-gray-900" x-text="item.nama"></div>
                                            <div class="text-[11px] text-gray-500" x-text="item.satuan"></div>
                                        </td>

                                        <!-- Ring X Input -->
                                        <td class="py-2 px-1 text-center bg-amber-50/30">
                                            <input
                                                type="number"
                                                min="0"
                                                max="10"
                                                x-model.number="item.ring_x"
                                                @input="calculateNilai(item); item._saved = false;"
                                                class="w-9 text-center py-1 rounded bg-amber-50 border border-amber-300 text-xs font-mono font-bold text-amber-800 focus:ring-1 focus:ring-amber-500 focus:outline-none"
                                            >
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

                                        <!-- Jumlah Masuk (Auto real-time) -->
                                        <td class="py-2 px-2 text-center font-mono font-bold text-gray-800 bg-gray-50/50" x-text="item.jumlah_masuk"></td>

                                        <!-- Nilai (Auto real-time) -->
                                        <td class="py-2 px-3 text-center font-mono font-extrabold text-sm text-copper-700 bg-copper-50/30" x-text="item.nilai"></td>

                                        <!-- Direct Save Action: ALWAYS ENABLED -->
                                        <td class="py-2 px-3 text-center whitespace-nowrap">
                                            <button
                                                type="button"
                                                @click="savePresisiScore(item)"
                                                class="px-3.5 py-1.5 rounded-lg font-bold text-xs shadow transition-all duration-150 flex items-center justify-center gap-1 mx-auto min-w-[75px] cursor-pointer"
                                                :class="item._saved ? 'bg-emerald-600 text-white ring-2 ring-emerald-300' : (item._saving ? 'bg-amber-600 text-white animate-pulse' : 'bg-copper-600 hover:bg-copper-700 active:scale-95 text-white')"
                                            >
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
                                        <td colspan="17" class="py-10 text-center text-gray-400">
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
                
                <!-- Tournament Bracket Maker Toolbar -->
                <div class="bg-gradient-to-r from-copper-50 via-white to-amber-50 rounded-2xl shadow-sm p-5 border border-copper-200 mb-6">
                    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-copper-600 text-white uppercase tracking-wider">Tournament Bracket Maker</span>
                                <span class="text-xs text-gray-500 font-semibold" x-text="duelingList.length + ' Match Terdaftar'"></span>
                            </div>
                            <h3 class="font-display text-lg font-bold text-gray-900 mt-1">Bagan Turnamen Eliminasi Dueling Plat</h3>
                            <p class="text-xs text-gray-600 mt-0.5">
                                Input peserta &amp; tentukan pemenang <strong>langsung pada kotak bagan visual</strong>. Klik tombol 👑 <strong>Menang</strong> untuk langsung menaikkan pemenang ke babak berikutnya!
                            </p>
                        </div>

                        <!-- Participant Count & Generator Controls -->
                        <div class="flex flex-wrap items-center gap-2.5 shrink-0">
                            <div class="flex items-center gap-1.5 bg-white px-3 py-1.5 rounded-xl border border-gray-300 shadow-sm">
                                <label class="text-xs font-bold text-gray-700 whitespace-nowrap">Jml Peserta:</label>
                                <input type="number" min="2" max="256" x-model.number="bracketParticipantCount"
                                       class="w-16 text-center py-1 rounded bg-gray-50 border border-gray-300 text-xs font-mono font-bold focus:ring-1 focus:ring-copper-500">
                                <div class="flex flex-wrap items-center gap-1 border-l border-gray-200 pl-2">
                                    <button type="button" @click="bracketParticipantCount = 8" :class="bracketParticipantCount === 8 ? 'bg-copper-600 text-white font-bold' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'" class="px-1.5 py-0.5 rounded text-[11px] transition">8</button>
                                    <button type="button" @click="bracketParticipantCount = 16" :class="bracketParticipantCount === 16 ? 'bg-copper-600 text-white font-bold' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'" class="px-1.5 py-0.5 rounded text-[11px] transition">16</button>
                                    <button type="button" @click="bracketParticipantCount = 32" :class="bracketParticipantCount === 32 ? 'bg-copper-600 text-white font-bold' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'" class="px-1.5 py-0.5 rounded text-[11px] transition">32</button>
                                    <button type="button" @click="bracketParticipantCount = 55" :class="bracketParticipantCount === 55 ? 'bg-amber-600 text-white font-bold' : 'bg-amber-50 text-amber-800 hover:bg-amber-100 border border-amber-200'" class="px-1.5 py-0.5 rounded text-[11px] transition font-semibold" title="Preset 55 peserta (Kapasitas 64, 9 BYE)">55</button>
                                    <button type="button" @click="bracketParticipantCount = 64" :class="bracketParticipantCount === 64 ? 'bg-copper-600 text-white font-bold' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'" class="px-1.5 py-0.5 rounded text-[11px] transition">64</button>
                                    <button type="button" @click="bracketParticipantCount = 67" :class="bracketParticipantCount === 67 ? 'bg-amber-600 text-white font-bold' : 'bg-amber-50 text-amber-800 hover:bg-amber-100 border border-amber-200'" class="px-1.5 py-0.5 rounded text-[11px] transition font-semibold" title="Preset 67 peserta (Kapasitas 128, 61 BYE)">67</button>
                                    <button type="button" @click="bracketParticipantCount = 128" :class="bracketParticipantCount === 128 ? 'bg-copper-600 text-white font-bold' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'" class="px-1.5 py-0.5 rounded text-[11px] transition">128</button>
                                </div>
                            </div>

                            <button type="button" @click="generateTournamentBracket(bracketParticipantCount)" 
                                    class="px-4 py-2 bg-copper-600 hover:bg-copper-700 text-white rounded-xl font-bold text-xs shadow transition flex items-center gap-1.5 cursor-pointer">
                                <i data-lucide="git-merge" class="w-4 h-4"></i>
                                <span x-text="'⚡ Buat Bagan ' + bracketParticipantCount + ' Peserta'"></span>
                            </button>

                            <button type="button" @click="resetTournamentBracket()" 
                                    class="px-3 py-2 bg-white hover:bg-red-50 text-red-600 border border-red-200 rounded-xl font-bold text-xs transition flex items-center gap-1 cursor-pointer" title="Reset semua pertandingan">
                                <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                <span>Reset</span>
                            </button>

                            <div class="w-full flex items-center gap-2 mt-0.5">
                                <span class="text-[11px] font-semibold text-copper-800 bg-copper-50 px-2.5 py-0.5 rounded-lg border border-copper-200 flex items-center gap-1">
                                    <i data-lucide="info" class="w-3 h-3 text-copper-600"></i>
                                    <span x-text="bracketCapacityInfo.label"></span>
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- View Mode Switcher -->
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mt-4 pt-3 border-t border-copper-100 text-xs">
                        <div class="flex items-center gap-2">
                            <span class="font-bold text-gray-700">Tampilan Mode Input:</span>
                            <div class="inline-flex rounded-xl p-1 bg-gray-200/80 border border-gray-300">
                                <button type="button" @click="duelingAdminView = 'bracket'" 
                                        :class="duelingAdminView === 'bracket' ? 'bg-white shadow text-copper-700 font-bold' : 'text-gray-600 hover:text-gray-900'"
                                        class="px-3 py-1 text-xs rounded-lg transition flex items-center gap-1.5 cursor-pointer">
                                    <i data-lucide="git-merge" class="w-3.5 h-3.5"></i>
                                    <span>Bagan Visual (Input Langsung)</span>
                                </button>
                                <button type="button" @click="duelingAdminView = 'table'" 
                                        :class="duelingAdminView === 'table' ? 'bg-white shadow text-copper-700 font-bold' : 'text-gray-600 hover:text-gray-900'"
                                        class="px-3 py-1 text-xs rounded-lg transition flex items-center gap-1.5 cursor-pointer">
                                    <i data-lucide="table" class="w-3.5 h-3.5"></i>
                                    <span>Tabel Datar</span>
                                </button>
                            </div>
                        </div>
                        <div class="text-[11px] text-gray-500 flex items-center gap-2">
                            <span class="inline-block w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                            <span>Normalisasi otomatis ganjil dengan BYE (Lolos Otomatis). Tanpa aturan waktu (hanya pemenang duel).</span>
                        </div>
                    </div>
                </div>

                <!-- Participant Datalist Autocomplete -->
                <datalist id="duelingParticipantsListIndex">
                    <template x-for="p in (registrations || []).filter(r => (r.status || '').toLowerCase() === 'verified')" :key="p.registration_id || p.no_peserta">
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
                                                            <input type="text" list="duelingParticipantsListIndex"
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
                                                        <span class="text-xs">👑</span>
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
                                                            <input type="text" list="duelingParticipantsListIndex"
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
                                                        <span class="text-xs">👑</span>
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
                                    <tr class="hover:bg-gray-50/80 transition">
                                        <td class="py-2.5 px-3 font-semibold text-gray-900" x-text="match.round_name"></td>
                                        <td class="py-2.5 px-2 text-center font-mono font-bold text-copper-700" x-text="match.match_number"></td>

                                        <!-- Participant 1 -->
                                        <td class="py-2.5 px-3">
                                            <input type="text" list="duelingParticipantsListIndex" x-model="match.participant_1_name" 
                                                   @change="onParticipantChange(match, 1)"
                                                   class="w-full px-2 py-1 rounded border border-gray-300 bg-gray-50 text-xs">
                                            <div class="text-[10px] text-gray-400 mt-0.5" x-text="match.participant_1_satuan || '-'"></div>
                                        </td>

                                        <!-- Participant 2 -->
                                        <td class="py-2.5 px-3">
                                            <input type="text" list="duelingParticipantsListIndex" x-model="match.participant_2_name" 
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
                                                        x-text="match.participant_1_name ? '👑 ' + match.participant_1_name : 'Peserta 1'"></option>
                                                <option :value="match.participant_2_id || match.participant_2_name" 
                                                        x-text="match.participant_2_name ? '👑 ' + match.participant_2_name : 'Peserta 2'"></option>
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
                                        <td colspan="7" class="py-10 text-center text-gray-400">
                                            Belum ada jadwal pertandingan dueling plat. Gunakan tombol Buat Bagan di atas untuk membuat jadwal otomatis.
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Empty Bracket Notice -->
                <template x-if="duelingList.length === 0">
                    <div class="bg-white rounded-2xl p-12 text-center border border-gray-200 shadow-sm mb-6">
                        <i data-lucide="git-merge" class="w-12 h-12 text-gray-300 mx-auto mb-3"></i>
                        <h4 class="font-display font-bold text-gray-800 text-base">Belum Ada Pertandingan Dueling Plat</h4>
                        <p class="text-xs text-gray-500 mt-1 max-w-md mx-auto">
                            Masukkan jumlah peserta pada kolom di atas (misal 8, 16, atau jumlah kustom seperti 7 atau 11) dan klik <strong>Buat Bagan</strong> untuk menyusun bagan turnamen secara otomatis.
                        </p>
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
                            <label class="block font-bold text-gray-700 mb-1">Babak Pertandingan</label>
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
                            <label class="block font-bold text-gray-700 mb-1">Nomor Match</label>
                            <input type="number" min="1" x-model.number="newMatch.match_number" required placeholder="1"
                                   class="w-full px-3 py-2 rounded-lg border border-gray-300 bg-gray-50 text-xs font-mono font-bold">
                        </div>

                        <div>
                            <label class="block font-bold text-gray-700 mb-1">Nama Peserta 1</label>
                            <input type="text" list="duelingParticipantsListIndex" x-model="newMatch.participant_1_name" placeholder="Nama Peserta 1" required
                                   class="w-full px-3 py-2 rounded-lg border border-gray-300 bg-gray-50 text-xs">
                        </div>

                        <div>
                            <label class="block font-bold text-gray-700 mb-1">Nama Peserta 2</label>
                            <input type="text" list="duelingParticipantsListIndex" x-model="newMatch.participant_2_name" placeholder="Nama Peserta 2" required
                                   class="w-full px-3 py-2 rounded-lg border border-gray-300 bg-gray-50 text-xs">
                        </div>

                        <div class="sm:col-span-2 md:col-span-4 flex justify-end">
                            <button type="submit" class="px-5 py-2 bg-copper-600 hover:bg-copper-700 text-white rounded-xl font-bold text-xs shadow-md transition flex items-center gap-1.5 cursor-pointer">
                                <i data-lucide="plus" class="w-4 h-4"></i>
                                <span>Tambah ke Bagan</span>
                            </button>
                        </div>
                    </form>
                </details>
            </div>
        </div>

        <!-- ==================== TAB 4: KELOLA AKUN ADMIN (SUPER ADMIN ONLY / USERS PERMISSION) ==================== -->
        <div x-show="activeTab === 'users'" x-transition>
            <!-- Control Header -->
            <div class="bg-white rounded-2xl border border-gray-200 p-5 mb-6 shadow-sm flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <div class="flex items-center gap-2">
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-purple-100 text-purple-800 flex items-center gap-1.5">
                            <i data-lucide="shield" class="w-3.5 h-3.5"></i>
                            Leveling Access & Multi-User Admin
                        </span>
                    </div>
                    <h2 class="font-display text-xl sm:text-2xl font-bold text-gray-900 mt-1">
                        Manajemen Akun Admin & Hak Akses
                    </h2>
                    <p class="text-xs text-gray-500 mt-0.5">
                        Kelola akun admin panitia, atur hak akses modular per-menu (Antrean Pendaftar, Data Peserta, Live Skor, Kelola Akun), dan buat akun baru.
                    </p>
                </div>

                <div class="flex items-center gap-2.5">
                    <button
                        type="button"
                        @click="openAddUserModal()"
                        class="px-4 py-2.5 bg-gradient-to-r from-copper-600 to-amber-600 hover:from-copper-700 hover:to-amber-700 text-white font-bold rounded-xl text-xs sm:text-sm transition flex items-center gap-2 shadow-md shadow-copper-600/20 shrink-0"
                    >
                        <i data-lucide="user-plus" class="w-4 h-4"></i>
                        <span>Tambah Akun Admin</span>
                    </button>
                    <button
                        type="button"
                        @click="fetchAdminUsers()"
                        :disabled="loadingUsers"
                        class="p-2.5 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 rounded-xl transition shadow-sm shrink-0"
                        title="Muat ulang data akun"
                    >
                        <i data-lucide="refresh-cw" class="w-4 h-4" :class="loadingUsers && 'animate-spin'"></i>
                    </button>
                </div>
            </div>

            <!-- Table of Admin Accounts -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-gray-100 uppercase font-bold text-gray-600 border-b border-gray-200">
                            <tr>
                                <th class="py-3 px-3 w-10 text-center">No</th>
                                <th class="py-3 px-4">ID Admin / Username</th>
                                <th class="py-3 px-4">Nama Lengkap</th>
                                <th class="py-3 px-3">Role</th>
                                <th class="py-3 px-4">Hak Akses Modular</th>
                                <th class="py-3 px-3 text-center">Status</th>
                                <th class="py-3 px-4">Login Terakhir</th>
                                <th class="py-3 px-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <template x-for="(user, index) in adminUsersList" :key="user.id">
                                <tr class="hover:bg-gray-50/80 transition">
                                    <td class="py-3 px-3 text-center font-bold text-gray-500" x-text="index + 1"></td>
                                    <td class="py-3 px-4 font-mono font-bold text-copper-700" x-text="user.username"></td>
                                    <td class="py-3 px-4 font-bold text-gray-900" x-text="user.display_name"></td>
                                    <td class="py-3 px-3">
                                        <span
                                            class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider"
                                            :class="user.role === 'superadmin' ? 'bg-purple-100 text-purple-800 border border-purple-200' : 'bg-blue-100 text-blue-800 border border-blue-200'"
                                        >
                                            <i data-lucide="shield" class="w-3 h-3" x-show="user.role === 'superadmin'"></i>
                                            <span x-text="user.role === 'superadmin' ? 'Super Admin' : 'Admin'"></span>
                                        </span>
                                    </td>
                                    <td class="py-3 px-4">
                                        <div class="flex flex-wrap gap-1">
                                            <template x-if="user.role === 'superadmin'">
                                                <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-amber-50 text-amber-800 border border-amber-200">
                                                    Akses Penuh Seluruh Menu
                                                </span>
                                            </template>
                                            <template x-if="user.role !== 'superadmin'">
                                                <div class="flex flex-wrap gap-1">
                                                    <span x-show="(user.permissions || []).includes('antrean')" class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200">Antrean</span>
                                                    <span x-show="(user.permissions || []).includes('peserta')" class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">Peserta</span>
                                                    <span x-show="(user.permissions || []).includes('scores')" class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-copper-50 text-copper-700 border border-copper-200">Live Skor</span>
                                                    <span x-show="(user.permissions || []).includes('users')" class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-purple-50 text-purple-700 border border-purple-200">Kelola Akun</span>
                                                    <span x-show="!(user.permissions || []).length" class="text-gray-400 italic text-[10px]">Tanpa hak akses</span>
                                                </div>
                                            </template>
                                        </div>
                                    </td>
                                    <td class="py-3 px-3 text-center">
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold"
                                            :class="user.is_active === 1 ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800'"
                                            x-text="user.is_active === 1 ? 'Aktif' : 'Nonaktif'"
                                        ></span>
                                    </td>
                                    <td class="py-3 px-4 text-gray-500 font-mono text-[11px]" x-text="user.last_login ? user.last_login : 'Belum pernah'"></td>
                                    <td class="py-3 px-4 text-center">
                                        <div class="inline-flex items-center gap-1.5">
                                            <!-- Edit -->
                                            <button
                                                type="button"
                                                @click="openEditUserModal(user)"
                                                class="p-1.5 bg-blue-50 hover:bg-blue-100 text-blue-700 rounded-lg transition"
                                                title="Edit Akun & Hak Akses"
                                            >
                                                <i data-lucide="edit-3" class="w-4 h-4"></i>
                                            </button>

                                            <!-- Toggle Active -->
                                            <button
                                                type="button"
                                                @click="toggleUserStatus(user)"
                                                class="p-1.5 rounded-lg transition"
                                                :class="user.is_active === 1 ? 'bg-amber-50 hover:bg-amber-100 text-amber-700' : 'bg-emerald-50 hover:bg-emerald-100 text-emerald-700'"
                                                :title="user.is_active === 1 ? 'Nonaktifkan Akun' : 'Aktifkan Akun'"
                                            >
                                                <i data-lucide="power" class="w-4 h-4"></i>
                                            </button>

                                            <!-- Delete (Only if not self and not primary superadmin) -->
                                            <template x-if="currentUser && currentUser.id !== user.id && user.username !== 'superadmin'">
                                                <button
                                                    type="button"
                                                    @click="deleteAdminUser(user)"
                                                    class="p-1.5 bg-red-50 hover:bg-red-100 text-red-600 rounded-lg transition"
                                                    title="Hapus Akun"
                                                >
                                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                                </button>
                                            </template>
                                        </div>
                                    </td>
                                </tr>
                            </template>
                            <template x-if="adminUsersList.length === 0">
                                <tr>
                                    <td colspan="8" class="py-10 text-center text-gray-400">
                                        <p>Belum ada data akun admin yang dimuat.</p>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

    <!-- ==================== MOBILE BOTTOM NAVIGATION BAR (FIXED) ==================== -->
    <nav class="md:hidden fixed bottom-0 left-0 right-0 z-50 bg-white/95 backdrop-blur-md border-t border-gray-200 shadow-[0_-4px_16px_rgba(0,0,0,0.08)] py-2 px-3 flex items-center justify-around">
        <!-- Menu 1: Antrean Pendaftar (Default) -->
        <button
            x-show="canAccess('antrean')"
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
            x-show="canAccess('peserta')"
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
            x-show="canAccess('scores')"
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

        <!-- Menu 4: Kelola Akun -->
        <button
            x-show="canAccess('users')"
            type="button"
            @click="setTab('users')"
            class="flex-1 flex flex-col items-center justify-center py-1 transition relative"
            :class="activeTab === 'users' ? 'text-copper-600 font-bold' : 'text-gray-400 hover:text-gray-600'"
        >
            <div class="relative">
                <i data-lucide="users-cog" class="w-5 h-5"></i>
            </div>
            <span class="text-[11px] mt-1 tracking-tight">Akun</span>
        </button>
    </nav>


    <!-- ==================== MODAL 1: RINCIAN PENDAFTAR & VERIFIKASI ==================== -->
    <template x-if="selectedReg">
        <div
            class="fixed inset-0 z-[100] flex items-center justify-center p-2 sm:p-4 md:p-6"
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

            <!-- Modal Content (Wider, Flex Column, Scrollable Body) -->
            <div
                class="relative w-full max-w-4xl lg:max-w-5xl max-h-[92vh] flex flex-col bg-white rounded-3xl shadow-2xl border border-gray-200 z-10 overflow-hidden"
                @click.stop
            >
                <!-- Modal Header -->
                <div class="shrink-0 bg-white/95 backdrop-blur-md border-b border-gray-200 px-6 py-4 flex items-center justify-between">
                    <div>
                        <h2 class="font-display text-lg sm:text-xl font-bold text-gray-900" x-text="(selectedReg.status || '').toLowerCase() === 'verified' ? 'Rincian Peserta' : 'Rincian Pendaftar'"></h2>
                        <p class="text-xs text-gray-500 font-mono mt-0.5" x-text="'ID: ' + selectedReg.registration_id"></p>
                    </div>
                    <div class="flex items-center gap-2">
                        <!-- Mode Edit Button di Header -->
                        <button
                            type="button"
                            @click="openEditFromDetail()"
                            class="px-3 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-700 font-bold rounded-xl transition text-xs inline-flex items-center gap-1.5 border border-blue-200 shadow-sm"
                            title="Buka Mode Edit Peserta"
                        >
                            <i data-lucide="edit-3" class="w-3.5 h-3.5"></i>
                            <span>Mode Edit</span>
                        </button>
                        <button @click="closeDetailModal()" class="p-2 hover:bg-gray-100 rounded-full transition">
                            <i data-lucide="x" class="w-5 h-5 text-gray-500"></i>
                        </button>
                    </div>
                </div>

                <!-- Modal Body (Scrollable) -->
                <div class="flex-1 overflow-y-auto p-4 sm:p-6 md:p-8 space-y-6 overscroll-contain">
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
                                <span class="text-gray-400 font-semibold block text-[11px] uppercase mb-1">Kategori Pertandingan</span>
                                <ul class="space-y-1 list-none p-0 m-0">
                                    <template x-for="item in formatKategoriList(selectedReg.kategori)" :key="item">
                                        <li class="flex items-center gap-1.5 text-xs sm:text-sm font-bold text-copper-800">
                                            <span class="w-1.5 h-1.5 rounded-full shrink-0"
                                                  :class="item.toLowerCase().includes('presisi') ? 'bg-blue-600' : (item.toLowerCase().includes('dueling') ? 'bg-purple-600' : 'bg-copper-600')"></span>
                                            <span x-text="item"></span>
                                        </li>
                                    </template>
                                </ul>
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
                <div class="shrink-0 bg-white/95 backdrop-blur-md border-t border-gray-200 px-6 py-4">
                    <div class="flex flex-col sm:flex-row gap-2.5">
                        <!-- Mode Edit Button -->
                        <button
                            type="button"
                            @click="openEditFromDetail()"
                            class="px-4 py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl text-xs sm:text-sm shadow-md shadow-blue-600/20 transition flex items-center justify-center gap-2"
                        >
                            <i data-lucide="edit-3" class="w-4 h-4"></i>
                            <span>Mode Edit</span>
                        </button>

                        <!-- Action Konfirmasi (Hanya jika belum verified) -->
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

                        <!-- Action Kirim WA E-Ticket (HANYA MUNCUL SETELAH PESERTA DIKONFIRMASI / VERIFIED) -->
                        <template x-if="(selectedReg.status || '').toLowerCase() === 'verified'">
                            <button
                                type="button"
                                @click="sendWhatsApp(selectedReg)"
                                class="flex-1 px-4 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl text-xs sm:text-sm shadow-md transition flex items-center justify-center gap-2"
                            >
                                <i data-lucide="send" class="w-4 h-4"></i>
                                <span>Kirim E-Ticket ke WA Peserta</span>
                            </button>
                        </template>

                        <!-- Action Tolak (HANYA MUNCUL JIKA BELUM DITOLAK DAN BELUM DIKONFIRMASI) -->
                        <template x-if="(selectedReg.status || '').toLowerCase() !== 'rejected' && (selectedReg.status || '').toLowerCase() !== 'verified'">
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


    <!-- ==================== MODAL 4: TAMBAH AKUN ADMIN ==================== -->
    <template x-if="showAddUserModal">
        <div
            class="fixed inset-0 z-[110] flex items-start justify-center p-3 sm:p-4 pt-14 sm:pt-16"
            x-show="showAddUserModal"
            x-transition
            @keydown.escape.window="showAddUserModal = false"
        >
            <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" @click="showAddUserModal = false"></div>

            <div class="relative w-full max-w-xl max-h-[85vh] overflow-y-auto bg-white rounded-3xl shadow-2xl border border-gray-200 z-10" @click.stop>
                <!-- Header -->
                <div class="sticky top-0 z-10 bg-white/95 backdrop-blur-md border-b border-gray-200 px-6 py-4 flex items-center justify-between rounded-t-3xl">
                    <div class="flex items-center gap-2.5">
                        <div class="w-9 h-9 rounded-xl bg-purple-50 text-purple-700 flex items-center justify-center">
                            <i data-lucide="user-plus" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <h3 class="font-display text-lg font-bold text-gray-900">Tambah Akun Admin Baru</h3>
                            <p class="text-xs text-gray-500">Buat ID dan password bebas, tentukan hak akses modular.</p>
                        </div>
                    </div>
                    <button @click="showAddUserModal = false" class="p-2 hover:bg-gray-100 rounded-full transition">
                        <i data-lucide="x" class="w-5 h-5 text-gray-500"></i>
                    </button>
                </div>

                <!-- Form Body -->
                <form @submit.prevent="createAdminUser()" class="p-6 space-y-4 text-xs sm:text-sm">
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">ID Admin / Username *</label>
                        <input
                            type="text"
                            x-model="newUserForm.username"
                            required
                            placeholder="Contoh: admin_scoring, bda_panitia, dll (tanpa spasi)"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 bg-gray-50 font-mono font-bold text-copper-700 focus:bg-white focus:ring-2 focus:ring-copper-500"
                        >
                        <p class="text-[10px] text-gray-400 mt-1">ID digunakan untuk login ke Admin Dashboard.</p>
                    </div>

                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Nama Lengkap Panitia *</label>
                        <input
                            type="text"
                            x-model="newUserForm.display_name"
                            required
                            placeholder="Contoh: Bripda Andi / Tim Panitia Scoring"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 bg-gray-50 font-semibold focus:bg-white focus:ring-2 focus:ring-copper-500"
                        >
                    </div>

                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Password Admin *</label>
                        <input
                            type="text"
                            x-model="newUserForm.password"
                            required
                            placeholder="Tentukan password bebas (minimal 6 karakter)"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 bg-gray-50 font-mono focus:bg-white focus:ring-2 focus:ring-copper-500"
                        >
                    </div>

                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Role / Peran</label>
                        <select x-model="newUserForm.role" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 bg-gray-50 font-bold focus:bg-white">
                            <option value="admin">Admin Biasa (Akses Berdasarkan Pilihan Menu)</option>
                            <option value="superadmin">Super Administrator (Akses Penuh Seluruh Fitur)</option>
                        </select>
                    </div>

                    <!-- Hak Akses Modular Checkboxes -->
                    <div class="pt-2">
                        <label class="block font-bold text-gray-700 mb-2">Hak Akses Modular Menu</label>
                        <div class="space-y-2 bg-gray-50 p-4 rounded-2xl border border-gray-200" :class="newUserForm.role === 'superadmin' ? 'opacity-60' : ''">
                            <label class="flex items-center gap-2.5 cursor-pointer">
                                <input
                                    type="checkbox"
                                    value="antrean"
                                    x-model="newUserForm.permissions"
                                    :disabled="newUserForm.role === 'superadmin'"
                                    class="rounded border-gray-300 text-copper-600 focus:ring-copper-500 w-4 h-4"
                                >
                                <span class="font-medium text-gray-800">1. Antrean Pendaftar (Verifikasi &amp; Konfirmasi, Kirim WA E-Ticket)</span>
                            </label>
                            <label class="flex items-center gap-2.5 cursor-pointer">
                                <input
                                    type="checkbox"
                                    value="peserta"
                                    x-model="newUserForm.permissions"
                                    :disabled="newUserForm.role === 'superadmin'"
                                    class="rounded border-gray-300 text-copper-600 focus:ring-copper-500 w-4 h-4"
                                >
                                <span class="font-medium text-gray-800">2. Data Peserta (Edit Detail Peserta &amp; Tambah Peserta Langsung)</span>
                            </label>
                            <label class="flex items-center gap-2.5 cursor-pointer">
                                <input
                                    type="checkbox"
                                    value="scores"
                                    x-model="newUserForm.permissions"
                                    :disabled="newUserForm.role === 'superadmin'"
                                    class="rounded border-gray-300 text-copper-600 focus:ring-copper-500 w-4 h-4"
                                >
                                <span class="font-medium text-gray-800">3. Live Skor &amp; Pertandingan (Input Skor Ring Presisi &amp; Bagan Dueling Plat)</span>
                            </label>
                            <label class="flex items-center gap-2.5 cursor-pointer">
                                <input
                                    type="checkbox"
                                    value="users"
                                    x-model="newUserForm.permissions"
                                    :disabled="newUserForm.role === 'superadmin'"
                                    class="rounded border-gray-300 text-copper-600 focus:ring-copper-500 w-4 h-4"
                                >
                                <span class="font-medium text-gray-800">4. Kelola Akun Admin (Manajemen Akun Admin Lainnya)</span>
                            </label>
                            <p x-show="newUserForm.role === 'superadmin'" class="text-[11px] text-purple-700 font-bold mt-1">
                                * Super Admin secara otomatis memiliki hak akses penuh ke semua menu di atas.
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-2.5 pt-4 border-t border-gray-200">
                        <button type="button" @click="showAddUserModal = false" class="px-4 py-2.5 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold transition">
                            Batal
                        </button>
                        <button type="submit" :disabled="savingUser" class="px-6 py-2.5 rounded-xl bg-copper-600 hover:bg-copper-700 text-white font-bold transition shadow-md flex items-center gap-1.5">
                            <i data-lucide="check" class="w-4 h-4"></i>
                            <span x-text="savingUser ? 'Membuat...' : 'Buat Akun Admin'"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </template>


    <!-- ==================== MODAL 5: EDIT AKUN ADMIN ==================== -->
    <template x-if="showEditUserModal">
        <div
            class="fixed inset-0 z-[110] flex items-start justify-center p-3 sm:p-4 pt-14 sm:pt-16"
            x-show="showEditUserModal"
            x-transition
            @keydown.escape.window="showEditUserModal = false"
        >
            <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" @click="showEditUserModal = false"></div>

            <div class="relative w-full max-w-xl max-h-[85vh] overflow-y-auto bg-white rounded-3xl shadow-2xl border border-gray-200 z-10" @click.stop>
                <!-- Header -->
                <div class="sticky top-0 z-10 bg-white/95 backdrop-blur-md border-b border-gray-200 px-6 py-4 flex items-center justify-between rounded-t-3xl">
                    <div class="flex items-center gap-2.5">
                        <div class="w-9 h-9 rounded-xl bg-blue-50 text-blue-700 flex items-center justify-center">
                            <i data-lucide="edit-3" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <h3 class="font-display text-lg font-bold text-gray-900">Edit Akun Admin</h3>
                            <p class="text-xs text-gray-500" x-text="'ID: ' + editUserForm.username"></p>
                        </div>
                    </div>
                    <button @click="showEditUserModal = false" class="p-2 hover:bg-gray-100 rounded-full transition">
                        <i data-lucide="x" class="w-5 h-5 text-gray-500"></i>
                    </button>
                </div>

                <!-- Form Body -->
                <form @submit.prevent="saveEditAdminUser()" class="p-6 space-y-4 text-xs sm:text-sm">
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">ID Admin / Username</label>
                        <input
                            type="text"
                            :value="editUserForm.username"
                            disabled
                            class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 bg-gray-100 font-mono font-bold text-gray-500 cursor-not-allowed"
                        >
                    </div>

                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Nama Lengkap Panitia *</label>
                        <input
                            type="text"
                            x-model="editUserForm.display_name"
                            required
                            class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 bg-gray-50 font-semibold focus:bg-white focus:ring-2 focus:ring-copper-500"
                        >
                    </div>

                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Password Baru (Opsional)</label>
                        <input
                            type="text"
                            x-model="editUserForm.password"
                            placeholder="Biarkan kosong jika tidak ingin mengubah password"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 bg-gray-50 font-mono focus:bg-white focus:ring-2 focus:ring-copper-500"
                        >
                    </div>

                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Role / Peran</label>
                        <select x-model="editUserForm.role" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 bg-gray-50 font-bold focus:bg-white">
                            <option value="admin">Admin Biasa</option>
                            <option value="superadmin">Super Administrator</option>
                        </select>
                    </div>

                    <!-- Hak Akses Modular Checkboxes -->
                    <div class="pt-2">
                        <label class="block font-bold text-gray-700 mb-2">Hak Akses Modular Menu</label>
                        <div class="space-y-2 bg-gray-50 p-4 rounded-2xl border border-gray-200" :class="editUserForm.role === 'superadmin' ? 'opacity-60' : ''">
                            <label class="flex items-center gap-2.5 cursor-pointer">
                                <input
                                    type="checkbox"
                                    value="antrean"
                                    x-model="editUserForm.permissions"
                                    :disabled="editUserForm.role === 'superadmin'"
                                    class="rounded border-gray-300 text-copper-600 focus:ring-copper-500 w-4 h-4"
                                >
                                <span class="font-medium text-gray-800">1. Antrean Pendaftar (Verifikasi &amp; Konfirmasi, Kirim WA E-Ticket)</span>
                            </label>
                            <label class="flex items-center gap-2.5 cursor-pointer">
                                <input
                                    type="checkbox"
                                    value="peserta"
                                    x-model="editUserForm.permissions"
                                    :disabled="editUserForm.role === 'superadmin'"
                                    class="rounded border-gray-300 text-copper-600 focus:ring-copper-500 w-4 h-4"
                                >
                                <span class="font-medium text-gray-800">2. Data Peserta (Edit Detail Peserta &amp; Tambah Peserta Langsung)</span>
                            </label>
                            <label class="flex items-center gap-2.5 cursor-pointer">
                                <input
                                    type="checkbox"
                                    value="scores"
                                    x-model="editUserForm.permissions"
                                    :disabled="editUserForm.role === 'superadmin'"
                                    class="rounded border-gray-300 text-copper-600 focus:ring-copper-500 w-4 h-4"
                                >
                                <span class="font-medium text-gray-800">3. Live Skor &amp; Pertandingan (Input Skor Ring Presisi &amp; Bagan Dueling Plat)</span>
                            </label>
                            <label class="flex items-center gap-2.5 cursor-pointer">
                                <input
                                    type="checkbox"
                                    value="users"
                                    x-model="editUserForm.permissions"
                                    :disabled="editUserForm.role === 'superadmin'"
                                    class="rounded border-gray-300 text-copper-600 focus:ring-copper-500 w-4 h-4"
                                >
                                <span class="font-medium text-gray-800">4. Kelola Akun Admin (Manajemen Akun Admin Lainnya)</span>
                            </label>
                            <p x-show="editUserForm.role === 'superadmin'" class="text-[11px] text-purple-700 font-bold mt-1">
                                * Super Admin secara otomatis memiliki hak akses penuh ke semua menu di atas.
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-2.5 pt-4 border-t border-gray-200">
                        <button type="button" @click="showEditUserModal = false" class="px-4 py-2.5 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold transition">
                            Batal
                        </button>
                        <button type="submit" :disabled="savingUser" class="px-6 py-2.5 rounded-xl bg-copper-600 hover:bg-copper-700 text-white font-bold transition shadow-md flex items-center gap-1.5">
                            <i data-lucide="check" class="w-4 h-4"></i>
                            <span x-text="savingUser ? 'Menyimpan...' : 'Perbarui Akun'"></span>
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
        duelingAdminView: 'bracket',
        bracketParticipantCount: 8,

        // Data arrays
        registrations: [],
        filtered: [],
        filteredPeserta: [],
        presisiList: [],
        duelingList: [],

        // Filter & Search states
        searchQuery: '',
        statusFilter: 'all',
        showVerifiedInAntrean: false,
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
            participant_1_id: '',
            participant_1_satuan: '',
            participant_2_name: '',
            participant_2_id: '',
            participant_2_satuan: ''
        },

        // Current Logged In Admin User & Permissions
        currentUser: <?= json_encode($currentUser) ?>,

        // Admin Users Management (Super Admin / Leveling Access)
        adminUsersList: [],
        loadingUsers: false,
        showAddUserModal: false,
        showEditUserModal: false,
        savingUser: false,
        newUserForm: {
            username: '',
            password: '',
            display_name: '',
            role: 'admin',
            permissions: ['antrean', 'peserta', 'scores']
        },
        editUserForm: {
            id: null,
            username: '',
            display_name: '',
            password: '',
            role: 'admin',
            permissions: []
        },

        // Toast & Stats
        toast: { show: false, message: '', type: 'success' },
        stats: { total: 0, pending: 0, verified: 0, rejected: 0 },

        // Injected Config
        adminToken: <?= json_encode(ADMIN_TOKEN) ?>,
        siteUrl: <?= json_encode(SITE_URL) ?>,

        canAccess(perm) {
            if (!this.currentUser) return false;
            if (this.currentUser.role === 'superadmin') return true;
            const perms = this.currentUser.permissions || [];
            return perms.includes(perm);
        },

        async init() {
            // Select first accessible tab
            if (this.canAccess('antrean')) {
                this.activeTab = 'antrean';
            } else if (this.canAccess('peserta')) {
                this.activeTab = 'peserta';
            } else if (this.canAccess('scores')) {
                this.activeTab = 'scores';
            } else if (this.canAccess('users')) {
                this.activeTab = 'users';
            }

            if (this.canAccess('antrean') || this.canAccess('peserta')) {
                await this.fetchRegistrations();
            }
            if (this.canAccess('scores')) {
                await this.fetchPresisiScores();
                await this.fetchDuelingMatches();
            }
            if (this.canAccess('users')) {
                await this.fetchAdminUsers();
            }
        },

        setTab(tab) {
            this.activeTab = tab;
            if (tab === 'users' && this.adminUsersList.length === 0) {
                this.fetchAdminUsers();
            }
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
            } else if (!this.showVerifiedInAntrean) {
                // Sembunyikan yang sudah dikonfirmasi (Verified) dari antrean pendaftar secara default
                res = res.filter(r => (r.status || '').toLowerCase() !== 'verified');
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

        openEditFromDetail() {
            if (!this.selectedReg) return;
            const reg = { ...this.selectedReg };
            this.closeDetailModal();
            this.openEditPeserta(reg);
        },

        // Edit Peserta Langsung
        openEditPeserta(p) {
            let kat = (p.kategori || 'presisi').toLowerCase();
            if (kat.includes('keduanya') || (kat.includes('presisi') && kat.includes('dueling'))) {
                kat = 'keduanya';
            } else if (kat.includes('presisi')) {
                kat = 'presisi';
            } else if (kat.includes('dueling')) {
                kat = 'dueling';
            }

            this.editForm = {
                id: p.id,
                registration_id: p.registration_id,
                nama: p.nama || '',
                pangkat: p.pangkat || '',
                nrp: p.nrp || '',
                satuan: p.satuan || '',
                telepon: p.telepon || '',
                email: p.email || '',
                kategori: kat,
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
            const eTicketUrl = this.siteUrl + '/e-ticket.php?id=' + encodeURIComponent(reg.registration_id);
            const isVerified = (reg.status || '').toLowerCase() === 'verified';
            const statusLabel = isVerified ? 'VERIFIED (TERKONFIRMASI RESMI)' : 'PENDING (Menunggu Konfirmasi)';

            // Format kategori lomba as bulleted list
            let katItems = [];
            if (reg.kategori) {
                katItems = reg.kategori.split(/[,+]/).map(k => k.trim()).filter(Boolean);
            }
            let katText = '';
            if (katItems.length > 1) {
                katText = '\n' + katItems.map(k => '  - ' + k).join('\n');
            } else if (katItems.length === 1) {
                katText = '*' + katItems[0] + '*';
            } else {
                katText = '-';
            }

            const message = `*BDA SHOOTING CHAMPIONSHIP 2026*
*RESIMEN I PASUKAN PELOPOR KORPS BRIMOB POLRI*
================================
*PENGESAHAN PENDAFTARAN & E-TICKET RESMI*

Halo Bapak/Ibu/Sdr *${reg.nama}*,
Pendaftaran Anda pada BDA Shooting Championship 2026 telah *DIKONFIRMASI & VERIFIED*.

*RINCIAN PESERTA:*
- No. Peserta: *${noPeserta}*
- ID Registrasi: ${reg.registration_id}
- Nama Lengkap: ${reg.nama}
- Pangkat: ${reg.pangkat || '-'}
- NRP / NIK: ${reg.nrp}
- Satuan / Club: ${reg.satuan}
- Kategori Lomba: ${katText}
- Status Registrasi: *${statusLabel}*

*LINK E-TICKET & QR CODE RESMI:*
${eTicketUrl}
(Tautan unik di atas memuat QR Code resmi dan tanda pengenal peserta)

*CATATAN PENTING PESERTA (WAJIB DIBACA):*
1. *Simpan pesan ini, link E-Ticket, dan QR Code* agar tidak hilang.
2. Tunjukkan E-Ticket & QR Code ini (pada layar HP atau cetak) saat *Daftar Ulang* di lokasi kejuaraan.
3. *Wajib membawa fisik KTA & KTP Asli* untuk verifikasi data keabsahan peserta di meja panitia.
4. Seluruh peserta *Wajib Hadir saat Technical Meeting (TM: Kamis, 15 Oktober 2026)* sebelum rangkaian pertandingan dimulai.

- Lokasi: Lapangan Tembak Resimen I Pasukan Pelopor, Kedunghalang, Bogor
- Jadwal: 17 - 18 Oktober 2026 (TM: 15 Oktober 2026)

Salam Hormat,
*Panitia Pelaksana BDA Shooting Championship 2026*`;

            const waUrl = 'https://wa.me/' + phone + '?text=' + encodeURIComponent(message);
            window.open(waUrl, '_blank');
            this.showToast('Membuka WhatsApp untuk ' + reg.nama, 'success');
        },

        // Live Skor Methods (Sequential Ring Scoring)
        calculateNilai(item) {
            let jmlMasuk = 0;
            let totalNilai = 0;
            for (let r = 1; r <= 10; r++) {
                const count = parseInt(item['ring_' + r]) || 0;
                jmlMasuk += count;
                totalNilai += (r * count);
            }
            const ringX = parseInt(item.ring_x) || 0;
            totalNilai += (ringX * 0.1);
            item.jumlah_masuk = jmlMasuk;
            item.nilai = Math.round(totalNilai * 10) / 10;
        },

        async fetchPresisiScores() {
            try {
                const res = await fetch('/api/scores-presisi.php?token=' + encodeURIComponent(this.adminToken));
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
                                    ring_x: 0,
                                    ring_10: 0, ring_9: 0, ring_8: 0, ring_7: 0, ring_6: 0,
                                    ring_5: 0, ring_4: 0, ring_3: 0, ring_2: 0, ring_1: 0,
                                    jumlah_masuk: 0,
                                    nilai: 0
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
                token: this.adminToken
            };
            for (let r = 1; r <= 10; r++) {
                payload['ring_' + r] = parseInt(item['ring_' + r]) || 0;
            }

            try {
                const res = await fetch('/api/scores-presisi.php?token=' + encodeURIComponent(this.adminToken), {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-Admin-Token': this.adminToken },
                    body: JSON.stringify(payload)
                });
                const data = await res.json();
                if (data.success) {
                    item._saved = true;
                    item._error = null;
                    setTimeout(() => { item._saved = false; }, 3000);
                    this.showToast('Skor ' + item.nama + ' berhasil disimpan! (Nilai: ' + payload.nilai + ')', 'success');
                    if (data.data) {
                        item.nilai = data.data.nilai;
                        item.jumlah_masuk = data.data.jumlah_masuk;
                    }
                } else {
                    throw new Error(data.error || data.message || 'Gagal menyimpan skor');
                }
            } catch (e) {
                item._error = e.message || 'Gagal menyimpan';
                this.showToast(item._error, 'error');
                alert('Pemberitahuan: ' + item._error);
            } finally {
                item._saving = false;
            }
        },

        // ==================== ADMIN USERS MANAGEMENT (SUPER ADMIN / USERS PERMISSION) ====================
        async fetchAdminUsers() {
            this.loadingUsers = true;
            try {
                const res = await fetch('/api/admin-users.php?token=' + encodeURIComponent(this.adminToken));
                const data = await res.json();
                if (data.success && Array.isArray(data.data)) {
                    this.adminUsersList = data.data;
                }
            } catch (e) {
                console.error('Error fetching admin users:', e);
            } finally {
                this.loadingUsers = false;
                this.$nextTick(() => { if (typeof lucide !== 'undefined') lucide.createIcons(); });
            }
        },

        openAddUserModal() {
            this.newUserForm = {
                username: '',
                password: '',
                display_name: '',
                role: 'admin',
                permissions: ['antrean', 'peserta', 'scores']
            };
            this.showAddUserModal = true;
            this.$nextTick(() => { if (typeof lucide !== 'undefined') lucide.createIcons(); });
        },

        async createAdminUser() {
            this.savingUser = true;
            try {
                const res = await fetch('/api/admin-users.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-Admin-Token': this.adminToken },
                    body: JSON.stringify({ action: 'create', ...this.newUserForm })
                });
                const data = await res.json();
                if (!res.ok || !data.success) throw new Error(data.error || 'Gagal membuat akun');

                this.showToast(data.message || 'Akun admin berhasil dibuat!', 'success');
                this.showAddUserModal = false;
                await this.fetchAdminUsers();
            } catch (err) {
                this.showToast(err.message, 'error');
            } finally {
                this.savingUser = false;
            }
        },

        openEditUserModal(user) {
            this.editUserForm = {
                id: user.id,
                username: user.username,
                display_name: user.display_name,
                password: '',
                role: user.role,
                permissions: [...(user.permissions || [])]
            };
            this.showEditUserModal = true;
            this.$nextTick(() => { if (typeof lucide !== 'undefined') lucide.createIcons(); });
        },

        async saveEditAdminUser() {
            this.savingUser = true;
            try {
                const res = await fetch('/api/admin-users.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-Admin-Token': this.adminToken },
                    body: JSON.stringify({ action: 'edit', ...this.editUserForm })
                });
                const data = await res.json();
                if (!res.ok || !data.success) throw new Error(data.error || 'Gagal menyimpan perubahan');

                this.showToast(data.message || 'Akun admin berhasil diperbarui!', 'success');
                this.showEditUserModal = false;
                await this.fetchAdminUsers();
            } catch (err) {
                this.showToast(err.message, 'error');
            } finally {
                this.savingUser = false;
            }
        },

        async toggleUserStatus(user) {
            const actionName = (user.is_active === 1) ? 'menonaktifkan' : 'mengaktifkan';
            if (!confirm(`Apakah Anda yakin ingin ${actionName} akun "${user.username}"?`)) return;

            try {
                const res = await fetch('/api/admin-users.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-Admin-Token': this.adminToken },
                    body: JSON.stringify({ action: 'toggle_active', id: user.id })
                });
                const data = await res.json();
                if (!res.ok || !data.success) throw new Error(data.error || 'Gagal mengubah status');

                this.showToast(data.message, 'success');
                await this.fetchAdminUsers();
            } catch (err) {
                this.showToast(err.message, 'error');
            }
        },

        async deleteAdminUser(user) {
            if (!confirm(`Hapus akun admin "${user.username}" (${user.display_name}) secara permanen? Tindakan ini tidak dapat dibatalkan.`)) return;

            try {
                const res = await fetch('/api/admin-users.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-Admin-Token': this.adminToken },
                    body: JSON.stringify({ action: 'delete', id: user.id })
                });
                const data = await res.json();
                if (!res.ok || !data.success) throw new Error(data.error || 'Gagal menghapus akun');

                this.showToast(data.message, 'info');
                await this.fetchAdminUsers();
            } catch (err) {
                this.showToast(err.message, 'error');
            }
        },

        getDuelingParticipants(query = '') {
            const list = (this.registrations || []).filter(r =>
                (r.status || '').toLowerCase() === 'verified'
            );
            if (!query || !String(query).trim()) return list.slice(0, 15);
            const q = String(query).toLowerCase().trim();
            return list.filter(r =>
                (r.nama && r.nama.toLowerCase().includes(q)) ||
                (r.no_peserta && r.no_peserta.toLowerCase().includes(q)) ||
                (r.satuan && r.satuan.toLowerCase().includes(q)) ||
                (r.nrp && r.nrp.toLowerCase().includes(q))
            ).slice(0, 15);
        },

        selectNewMatchParticipant(slot, p) {
            if (slot === 1) {
                this.newMatch.participant_1_name = p.nama;
                this.newMatch.participant_1_id = p.no_peserta || p.registration_id;
                this.newMatch.participant_1_satuan = p.satuan || '';
            } else {
                this.newMatch.participant_2_name = p.nama;
                this.newMatch.participant_2_id = p.no_peserta || p.registration_id;
                this.newMatch.participant_2_satuan = p.satuan || '';
            }
        },

        selectMatchParticipant(match, slot, p) {
            if (slot === 1) {
                match.participant_1_name = p.nama;
                match.participant_1_id = p.no_peserta || p.registration_id;
                match.participant_1_satuan = p.satuan || '';
            } else {
                match.participant_2_name = p.nama;
                match.participant_2_id = p.no_peserta || p.registration_id;
                match.participant_2_satuan = p.satuan || '';
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
                participant_1_id: this.newMatch.participant_1_id || '',
                participant_1_name: this.newMatch.participant_1_name,
                participant_1_satuan: this.newMatch.participant_1_satuan || '',
                participant_2_id: this.newMatch.participant_2_id || '',
                participant_2_name: this.newMatch.participant_2_name,
                participant_2_satuan: this.newMatch.participant_2_satuan || '',
                match_status: 'upcoming'
            };

            try {
                const res = await fetch('/api/scores-dueling.php?token=' + encodeURIComponent(this.adminToken), {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-Admin-Token': this.adminToken },
                    body: JSON.stringify({ ...payload, token: this.adminToken })
                });
                const data = await res.json();
                if (data.success) {
                    this.showToast('Match baru berhasil ditambahkan!', 'success');
                    this.newMatch.match_number++;
                    this.newMatch.participant_1_name = '';
                    this.newMatch.participant_1_id = '';
                    this.newMatch.participant_1_satuan = '';
                    this.newMatch.participant_2_name = '';
                    this.newMatch.participant_2_id = '';
                    this.newMatch.participant_2_satuan = '';
                    await this.fetchDuelingMatches();
                } else {
                    throw new Error(data.error || data.message || 'Gagal menambahkan match');
                }
            } catch (e) {
                this.showToast(e.message, 'error');
            }
        },

        async saveDuelingMatch(match) {
            match._saving = true;
            try {
                const res = await fetch('/api/scores-dueling.php?token=' + encodeURIComponent(this.adminToken), {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-Admin-Token': this.adminToken },
                    body: JSON.stringify({ ...match, token: this.adminToken })
                });
                const data = await res.json();
                if (data.success) {
                    match._saved = true;
                    setTimeout(() => { match._saved = false; }, 2500);
                    this.showToast(data.message || ('Data match #' + match.match_number + ' diperbarui!'), 'success');
                    await this.fetchDuelingMatches();
                } else {
                    throw new Error(data.error || data.message || 'Gagal menyimpan match');
                }
            } catch (e) {
                this.showToast(e.message, 'error');
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
            const verified = (this.registrations || []).filter(r => (r.status || '').toLowerCase() === 'verified');
            const found = verified.find(p => (p.nama || '').trim().toLowerCase() === name);
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
                const res = await fetch('/api/scores-dueling.php?token=' + encodeURIComponent(this.adminToken), {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-Admin-Token': this.adminToken },
                    body: JSON.stringify({ action: 'generate_bracket', count: count, size: count, token: this.adminToken })
                });
                const data = await res.json();
                if (data.success) {
                    this.showToast(data.message, 'success');
                    await this.fetchDuelingMatches();
                } else {
                    throw new Error(data.message || 'Gagal membuat bagan turnamen');
                }
            } catch (e) {
                this.showToast(e.message, 'error');
                alert('Gagal membuat bagan: ' + e.message);
            }
        },

        async resetTournamentBracket() {
            if (!confirm('Yakin ingin mereset dan mengosongkan seluruh bagan pertandingan dueling plat?')) return;
            try {
                const res = await fetch('/api/scores-dueling.php?token=' + encodeURIComponent(this.adminToken), {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-Admin-Token': this.adminToken },
                    body: JSON.stringify({ action: 'reset_bracket', token: this.adminToken })
                });
                const data = await res.json();
                if (data.success) {
                    this.showToast(data.message, 'info');
                    await this.fetchDuelingMatches();
                } else {
                    throw new Error(data.message || 'Gagal mereset bagan');
                }
            } catch (e) {
                this.showToast(e.message, 'error');
            }
        },

        async deleteDuelingMatch(match) {
            if (!confirm(`Hapus pertandingan Match #${match.match_number} (${match.participant_1_name} vs ${match.participant_2_name})?`)) return;

            try {
                const res = await fetch('/api/scores-dueling.php?token=' + encodeURIComponent(this.adminToken), {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-Admin-Token': this.adminToken },
                    body: JSON.stringify({ action: 'delete', id: match.id, token: this.adminToken })
                });
                const data = await res.json();
                if (data.success) {
                    this.duelingList = this.duelingList.filter(m => m.id !== match.id);
                    this.showToast('Match berhasil dihapus', 'info');
                } else {
                    throw new Error(data.error || data.message || 'Gagal menghapus match');
                }
            } catch (e) {
                this.showToast(e.message, 'error');
            }
        },

        formatKategoriList(kat) {
            if (!kat) return ['-'];
            const s = String(kat).trim();
            const lower = s.toLowerCase();
            
            if (lower === 'keduanya' || lower.includes('keduanya')) {
                return ['Pistol Presisi 20M', 'Dueling Plat'];
            }
            if (lower.includes('presisi') && lower.includes('dueling')) {
                return ['Pistol Presisi 20M', 'Dueling Plat'];
            }
            if (s.includes(',') || s.includes(';') || s.includes('+')) {
                return s.split(/[,;+]/).map(p => p.trim()).filter(Boolean).map(p => {
                    const pl = p.toLowerCase();
                    if (pl.includes('presisi')) return 'Pistol Presisi 20M';
                    if (pl.includes('dueling')) return 'Dueling Plat';
                    return p;
                });
            }
            if (lower === 'presisi' || lower.includes('presisi')) {
                return ['Pistol Presisi 20M'];
            }
            if (lower === 'dueling' || lower.includes('dueling')) {
                return ['Dueling Plat'];
            }
            return [s];
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
