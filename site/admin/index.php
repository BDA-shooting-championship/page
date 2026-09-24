<?php
/**
 * BDA Shooting Championship 2026 — Admin Dashboard
 * Manages registrations, verification, and participant communication.
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
<section class="min-h-[80vh] flex items-center justify-center target-pattern px-4">
    <div class="w-full max-w-md">
        <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-800 p-8">
            <!-- Logo & Title -->
            <div class="text-center mb-8">
                <div class="flex justify-center items-center gap-3 mb-4">
                    <img src="/assets/logo-bda-clean.jpg" alt="Logo BDA 750" class="h-14 w-14 rounded-xl object-cover border border-copper-400/50 shadow-md">
                    <img src="/assets/logo-championship-clean.jpg" alt="Logo BSC 2026" class="h-14 w-14 rounded-xl object-cover border border-copper-400/50 shadow-md">
                </div>
                <h1 class="font-display text-2xl font-bold text-gray-900 dark:text-white">Admin Dashboard</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1"><?= EVENT_NAME ?></p>
            </div>

            <!-- Login Form -->
            <form method="POST" action="">
                <input type="hidden" name="admin_login" value="1">

                <?php if (isset($loginError)): ?>
                <div class="mb-4 p-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg text-sm text-red-600 dark:text-red-400 flex items-center gap-2">
                    <i data-lucide="alert-circle" class="w-4 h-4 flex-shrink-0"></i>
                    <?= htmlspecialchars($loginError) ?>
                </div>
                <?php endif; ?>

                <div class="mb-6">
                    <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Password</label>
                    <div class="relative" x-data="{ show: false }">
                        <input
                            :type="show ? 'text' : 'password'"
                            name="password"
                            id="password"
                            required
                            autofocus
                            class="w-full px-4 py-3 pr-12 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-copper-500 focus:border-copper-500 transition"
                            placeholder="Masukkan password admin"
                        >
                        <button type="button" @click="show = !show" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                            <i data-lucide="eye" class="w-5 h-5" x-show="!show"></i>
                            <i data-lucide="eye-off" class="w-5 h-5" x-show="show" x-cloak></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="w-full py-3 bg-copper-600 hover:bg-copper-700 text-white font-semibold rounded-lg transition shadow-lg shadow-copper-600/20">
                    Masuk ke Dashboard
                </button>
            </form>

            <p class="text-center text-xs text-gray-400 dark:text-gray-600 mt-6">
                <i data-lucide="lock" class="w-3 h-3 inline"></i>
                Halaman ini hanya untuk admin panitia.
            </p>
        </div>
    </div>
</section>

<?php else: ?>
<!-- ==================== ADMIN DASHBOARD ==================== -->
<section
    class="min-h-screen bg-gray-50 dark:bg-gray-950 py-6 px-4 sm:px-6 lg:px-8"
    x-data="adminDashboard()"
    x-init="init()"
>
    <div class="max-w-7xl mx-auto">
        <!-- Dashboard Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div>
                <h1 class="font-display text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white">
                    <i data-lucide="layout-dashboard" class="w-7 h-7 inline text-copper-500"></i>
                    Admin Dashboard
                </h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1"><?= EVENT_NAME ?> — Kelola Pendaftaran</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="/admin/scores.php" class="inline-flex items-center gap-2 px-4 py-2 bg-copper-600 hover:bg-copper-700 text-white text-sm font-medium rounded-lg transition shadow">
                    <i data-lucide="crosshair" class="w-4 h-4"></i>
                    Kelola Live Score
                </a>
                <button @click="exportCSV()" class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition shadow">
                    <i data-lucide="download" class="w-4 h-4"></i>
                    Export CSV
                </button>
                <a href="/admin/?logout=1" class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition shadow">
                    <i data-lucide="log-out" class="w-4 h-4"></i>
                    Logout
                </a>
            </div>
        </div>

        <!-- Stats Bar -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <!-- Total -->
            <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Total</p>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white mt-1" x-text="stats.total">0</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                        <i data-lucide="users" class="w-6 h-6 text-blue-600 dark:text-blue-400"></i>
                    </div>
                </div>
            </div>
            <!-- Pending -->
            <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Pending</p>
                        <p class="text-3xl font-bold text-yellow-600 dark:text-yellow-400 mt-1" x-text="stats.pending">0</p>
                    </div>
                    <div class="w-12 h-12 bg-yellow-100 dark:bg-yellow-900/30 rounded-lg flex items-center justify-center">
                        <i data-lucide="clock" class="w-6 h-6 text-yellow-600 dark:text-yellow-400"></i>
                    </div>
                </div>
            </div>
            <!-- Verified -->
            <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Verified</p>
                        <p class="text-3xl font-bold text-green-600 dark:text-green-400 mt-1" x-text="stats.verified">0</p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
                        <i data-lucide="check-circle" class="w-6 h-6 text-green-600 dark:text-green-400"></i>
                    </div>
                </div>
            </div>
            <!-- Rejected -->
            <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Rejected</p>
                        <p class="text-3xl font-bold text-red-600 dark:text-red-400 mt-1" x-text="stats.rejected">0</p>
                    </div>
                    <div class="w-12 h-12 bg-red-100 dark:bg-red-900/30 rounded-lg flex items-center justify-center">
                        <i data-lucide="x-circle" class="w-6 h-6 text-red-600 dark:text-red-400"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Search & Filter Bar -->
        <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 p-4 mb-6 shadow-sm">
            <div class="flex flex-col sm:flex-row gap-3">
                <div class="relative flex-1">
                    <i data-lucide="search" class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <input
                        type="text"
                        x-model="searchQuery"
                        @input.debounce.300ms="filterRegistrations()"
                        placeholder="Cari nama, NRP, satuan, ID registrasi..."
                        class="w-full pl-10 pr-4 py-2.5 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-copper-500 focus:border-copper-500 transition"
                    >
                </div>
                <select
                    x-model="statusFilter"
                    @change="filterRegistrations()"
                    class="px-4 py-2.5 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-copper-500 focus:border-copper-500 transition"
                >
                    <option value="all">Semua Status</option>
                    <option value="pending">Pending</option>
                    <option value="verified">Verified</option>
                    <option value="rejected">Rejected</option>
                </select>
                <button @click="fetchRegistrations()" class="inline-flex items-center gap-2 px-4 py-2.5 bg-copper-600 hover:bg-copper-700 text-white text-sm font-medium rounded-lg transition">
                    <i data-lucide="refresh-cw" class="w-4 h-4" :class="loading && 'animate-spin'"></i>
                    Refresh
                </button>
            </div>
        </div>

        <!-- Loading State -->
        <template x-if="loading">
            <div class="flex items-center justify-center py-20">
                <div class="text-center">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-copper-100 dark:bg-copper-900/30 rounded-full mb-4 animate-pulse">
                        <i data-lucide="loader-2" class="w-8 h-8 text-copper-600 dark:text-copper-400 animate-spin"></i>
                    </div>
                    <p class="text-gray-500 dark:text-gray-400 text-sm">Memuat data pendaftaran...</p>
                </div>
            </div>
        </template>

        <!-- Error State -->
        <template x-if="error && !loading">
            <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl p-6 text-center">
                <i data-lucide="alert-triangle" class="w-10 h-10 text-red-500 mx-auto mb-3"></i>
                <p class="text-red-600 dark:text-red-400 font-medium" x-text="error"></p>
                <button @click="fetchRegistrations()" class="mt-3 text-sm text-red-600 dark:text-red-400 underline hover:no-underline">Coba lagi</button>
            </div>
        </template>

        <!-- Registrations Table -->
        <template x-if="!loading && !error">
            <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 shadow-sm overflow-hidden">
                <!-- Table Desktop -->
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 dark:bg-gray-800/50">
                            <tr>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300 text-xs uppercase tracking-wider">No</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300 text-xs uppercase tracking-wider">ID Registrasi</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300 text-xs uppercase tracking-wider">Nama</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300 text-xs uppercase tracking-wider hidden md:table-cell">Pangkat/NRP</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300 text-xs uppercase tracking-wider hidden lg:table-cell">Satuan</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300 text-xs uppercase tracking-wider hidden lg:table-cell">Kategori</th>
                                <th class="px-4 py-3 text-center font-semibold text-gray-600 dark:text-gray-300 text-xs uppercase tracking-wider">Status</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300 text-xs uppercase tracking-wider hidden xl:table-cell">Tanggal</th>
                                <th class="px-4 py-3 text-center font-semibold text-gray-600 dark:text-gray-300 text-xs uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                            <template x-for="(reg, index) in filtered" :key="reg.id">
                                <tr
                                    class="hover:bg-gray-50 dark:hover:bg-gray-800/50 cursor-pointer transition"
                                    @click="openDetail(reg)"
                                >
                                    <td class="px-4 py-3 text-gray-500 dark:text-gray-400" x-text="index + 1"></td>
                                    <td class="px-4 py-3">
                                        <span class="font-mono text-xs bg-gray-100 dark:bg-gray-800 px-2 py-1 rounded" x-text="reg.registration_id"></span>
                                    </td>
                                    <td class="px-4 py-3 font-medium text-gray-900 dark:text-white" x-text="reg.nama"></td>
                                    <td class="px-4 py-3 text-gray-600 dark:text-gray-300 hidden md:table-cell">
                                        <span x-text="reg.pangkat"></span> / <span class="font-mono text-xs" x-text="reg.nrp"></span>
                                    </td>
                                    <td class="px-4 py-3 text-gray-600 dark:text-gray-300 hidden lg:table-cell" x-text="reg.satuan"></td>
                                    <td class="px-4 py-3 hidden lg:table-cell">
                                        <span
                                            class="inline-block px-2 py-0.5 text-xs font-medium rounded-full"
                                            :class="reg.kategori === 'presisi' ? 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400' : 'bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-400'"
                                            x-text="reg.kategori === 'presisi' ? 'Presisi' : 'Dueling'"
                                        ></span>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <span
                                            class="inline-block px-2.5 py-1 text-xs font-semibold rounded-full"
                                            :class="{
                                                'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400': reg.status === 'pending',
                                                'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400': reg.status === 'verified',
                                                'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400': reg.status === 'rejected'
                                            }"
                                            x-text="reg.status.charAt(0).toUpperCase() + reg.status.slice(1)"
                                        ></span>
                                    </td>
                                    <td class="px-4 py-3 text-gray-500 dark:text-gray-400 text-xs hidden xl:table-cell" x-text="formatDate(reg.created_at)"></td>
                                    <td class="px-4 py-3 text-center">
                                        <button @click.stop="openDetail(reg)" class="inline-flex items-center gap-1 px-3 py-1.5 bg-copper-50 dark:bg-copper-900/20 text-copper-600 dark:text-copper-400 text-xs font-medium rounded-lg hover:bg-copper-100 dark:hover:bg-copper-900/40 transition">
                                            <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                                            Detail
                                        </button>
                                    </td>
                                </tr>
                            </template>
                            <!-- Empty State -->
                            <template x-if="filtered.length === 0">
                                <tr>
                                    <td colspan="9" class="px-4 py-16 text-center">
                                        <i data-lucide="inbox" class="w-12 h-12 text-gray-300 dark:text-gray-700 mx-auto mb-3"></i>
                                        <p class="text-gray-500 dark:text-gray-400 font-medium">Tidak ada data ditemukan</p>
                                        <p class="text-gray-400 dark:text-gray-500 text-xs mt-1">Coba ubah filter atau kata kunci pencarian.</p>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                <!-- Table Footer -->
                <div class="px-4 py-3 bg-gray-50 dark:bg-gray-800/50 border-t border-gray-100 dark:border-gray-800 text-sm text-gray-500 dark:text-gray-400">
                    Menampilkan <span class="font-semibold text-gray-900 dark:text-white" x-text="filtered.length"></span> dari <span class="font-semibold text-gray-900 dark:text-white" x-text="registrations.length"></span> pendaftaran
                </div>
            </div>
        </template>
    </div>

    <!-- ==================== DETAIL MODAL ==================== -->
    <template x-if="selectedReg">
        <div
            class="fixed inset-0 z-[100] flex items-start justify-center p-4 pt-20 sm:pt-24"
            x-show="showModal"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            @keydown.escape.window="closeModal()"
        >
            <!-- Backdrop -->
            <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" @click="closeModal()"></div>

            <!-- Modal Content -->
            <div
                class="relative w-full max-w-3xl max-h-[80vh] overflow-y-auto bg-white dark:bg-gray-900 rounded-2xl shadow-2xl border border-gray-200 dark:border-gray-800 z-10"
                x-show="showModal"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                x-transition:leave-end="opacity-0 scale-95 translate-y-4"
                @click.stop
            >
                <!-- Modal Header -->
                <div class="sticky top-0 z-10 bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-800 px-6 py-4 flex items-center justify-between rounded-t-2xl">
                    <div>
                        <h2 class="font-display text-lg font-bold text-gray-900 dark:text-white">Detail Pendaftaran</h2>
                        <p class="text-xs text-gray-500 dark:text-gray-400 font-mono mt-0.5" x-text="'ID: ' + selectedReg.registration_id"></p>
                    </div>
                    <button @click="closeModal()" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg transition">
                        <i data-lucide="x" class="w-5 h-5 text-gray-500"></i>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="p-6 space-y-6">
                    <!-- Status Badge -->
                    <div class="flex items-center gap-3">
                        <span
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-semibold rounded-full"
                            :class="{
                                'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400': selectedReg.status === 'pending',
                                'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400': selectedReg.status === 'verified',
                                'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400': selectedReg.status === 'rejected'
                            }"
                        >
                            <template x-if="selectedReg.status === 'pending'"><i data-lucide="clock" class="w-4 h-4"></i></template>
                            <template x-if="selectedReg.status === 'verified'"><i data-lucide="check-circle" class="w-4 h-4"></i></template>
                            <template x-if="selectedReg.status === 'rejected'"><i data-lucide="x-circle" class="w-4 h-4"></i></template>
                            <span x-text="selectedReg.status.charAt(0).toUpperCase() + selectedReg.status.slice(1)"></span>
                        </span>
                        <template x-if="selectedReg.no_peserta">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-semibold rounded-full bg-copper-100 dark:bg-copper-900/30 text-copper-700 dark:text-copper-400">
                                <i data-lucide="hash" class="w-4 h-4"></i>
                                No. Peserta: <span x-text="selectedReg.no_peserta"></span>
                            </span>
                        </template>
                    </div>

                    <!-- Data Peserta -->
                    <div class="bg-gray-50 dark:bg-gray-800/50 rounded-xl p-5">
                        <h3 class="font-display font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                            <i data-lucide="user" class="w-5 h-5 text-copper-500"></i>
                            Data Peserta
                        </h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="text-gray-500 dark:text-gray-400">Nama Lengkap</span>
                                <p class="font-medium text-gray-900 dark:text-white mt-0.5" x-text="selectedReg.nama"></p>
                            </div>
                            <div>
                                <span class="text-gray-500 dark:text-gray-400">Email</span>
                                <p class="font-medium text-gray-900 dark:text-white mt-0.5" x-text="selectedReg.email"></p>
                            </div>
                            <div>
                                <span class="text-gray-500 dark:text-gray-400">Telepon</span>
                                <p class="font-medium text-gray-900 dark:text-white mt-0.5" x-text="selectedReg.telepon"></p>
                            </div>
                            <div>
                                <span class="text-gray-500 dark:text-gray-400">Pangkat</span>
                                <p class="font-medium text-gray-900 dark:text-white mt-0.5" x-text="selectedReg.pangkat"></p>
                            </div>
                            <div>
                                <span class="text-gray-500 dark:text-gray-400">NRP</span>
                                <p class="font-mono font-medium text-gray-900 dark:text-white mt-0.5" x-text="selectedReg.nrp"></p>
                            </div>
                            <div>
                                <span class="text-gray-500 dark:text-gray-400">Satuan</span>
                                <p class="font-medium text-gray-900 dark:text-white mt-0.5" x-text="selectedReg.satuan"></p>
                            </div>
                            <div>
                                <span class="text-gray-500 dark:text-gray-400">Kategori</span>
                                <p class="font-medium text-gray-900 dark:text-white mt-0.5" x-text="selectedReg.kategori === 'presisi' ? 'Presisi 20M' : 'Dueling Plat'"></p>
                            </div>
                            <div>
                                <span class="text-gray-500 dark:text-gray-400">Tanggal Daftar</span>
                                <p class="font-medium text-gray-900 dark:text-white mt-0.5" x-text="formatDate(selectedReg.created_at)"></p>
                            </div>
                        </div>
                    </div>

                    <!-- Documents -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- KTA Photo -->
                        <div class="bg-gray-50 dark:bg-gray-800/50 rounded-xl p-5">
                            <h3 class="font-display font-semibold text-gray-900 dark:text-white mb-3 flex items-center gap-2">
                                <i data-lucide="id-card" class="w-5 h-5 text-copper-500"></i>
                                Foto KTA
                            </h3>
                            <template x-if="selectedReg.kta_filename">
                                <a :href="'/uploads/kta/' + selectedReg.kta_filename" target="_blank" class="block">
                                    <img
                                        :src="'/uploads/kta/' + selectedReg.kta_filename"
                                        :alt="'KTA ' + selectedReg.nama"
                                        class="w-full h-48 object-cover rounded-lg border border-gray-200 dark:border-gray-700 hover:opacity-90 transition cursor-zoom-in"
                                        onerror="this.src='data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 400 300%22><rect fill=%22%23f3f4f6%22 width=%22400%22 height=%22300%22/><text x=%2250%%22 y=%2250%%22 text-anchor=%22middle%22 fill=%22%239ca3af%22 font-size=%2214%22>Gambar tidak tersedia</text></svg>'"
                                    >
                                </a>
                            </template>
                            <template x-if="!selectedReg.kta_filename">
                                <div class="w-full h-48 bg-gray-200 dark:bg-gray-700 rounded-lg flex items-center justify-center">
                                    <span class="text-gray-400 dark:text-gray-500 text-sm">Tidak ada file</span>
                                </div>
                            </template>
                        </div>

                        <!-- Bukti Transfer -->
                        <div class="bg-gray-50 dark:bg-gray-800/50 rounded-xl p-5">
                            <h3 class="font-display font-semibold text-gray-900 dark:text-white mb-3 flex items-center gap-2">
                                <i data-lucide="receipt" class="w-5 h-5 text-copper-500"></i>
                                Bukti Transfer
                            </h3>
                            <template x-if="selectedReg.bukti_filename">
                                <a :href="'/uploads/bukti/' + selectedReg.bukti_filename" target="_blank" class="block">
                                    <img
                                        :src="'/uploads/bukti/' + selectedReg.bukti_filename"
                                        :alt="'Bukti Transfer ' + selectedReg.nama"
                                        class="w-full h-48 object-cover rounded-lg border border-gray-200 dark:border-gray-700 hover:opacity-90 transition cursor-zoom-in"
                                        onerror="this.src='data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 400 300%22><rect fill=%22%23f3f4f6%22 width=%22400%22 height=%22300%22/><text x=%2250%%22 y=%2250%%22 text-anchor=%22middle%22 fill=%22%239ca3af%22 font-size=%2214%22>Gambar tidak tersedia</text></svg>'"
                                    >
                                </a>
                            </template>
                            <template x-if="!selectedReg.bukti_filename">
                                <div class="w-full h-48 bg-gray-200 dark:bg-gray-700 rounded-lg flex items-center justify-center">
                                    <span class="text-gray-400 dark:text-gray-500 text-sm">Tidak ada file</span>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Admin Notes -->
                    <template x-if="selectedReg.admin_notes">
                        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl p-4">
                            <h4 class="font-semibold text-blue-700 dark:text-blue-400 text-sm flex items-center gap-2 mb-2">
                                <i data-lucide="sticky-note" class="w-4 h-4"></i>
                                Catatan Admin
                            </h4>
                            <p class="text-blue-600 dark:text-blue-300 text-sm" x-text="selectedReg.admin_notes"></p>
                        </div>
                    </template>

                    <!-- Admin Notes Input -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            <i data-lucide="pencil" class="w-4 h-4 inline"></i>
                            Catatan Admin (opsional)
                        </label>
                        <textarea
                            x-model="adminNotes"
                            rows="2"
                            class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-copper-500 focus:border-copper-500 transition resize-none"
                            placeholder="Tambahkan catatan..."
                        ></textarea>
                    </div>

                    <!-- No Peserta Input (shown for verify action) -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            <i data-lucide="hash" class="w-4 h-4 inline"></i>
                            No. Peserta (wajib untuk verifikasi)
                        </label>
                        <input
                            type="text"
                            x-model="noPeserta"
                            class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-copper-500 focus:border-copper-500 transition"
                            placeholder="Contoh: BSC-001"
                        >
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="sticky bottom-0 z-10 bg-white dark:bg-gray-900 border-t border-gray-200 dark:border-gray-800 px-6 py-4 rounded-b-2xl">
                    <div class="flex flex-col sm:flex-row gap-3">
                        <!-- Verify -->
                        <button
                            @click="updateStatus('verified')"
                            :disabled="updating"
                            class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-green-600 hover:bg-green-700 disabled:opacity-50 disabled:cursor-not-allowed text-white font-semibold rounded-lg transition shadow"
                        >
                            <i data-lucide="check-circle" class="w-4 h-4"></i>
                            <span x-text="updating === 'verified' ? 'Memproses...' : 'Verifikasi'"></span>
                        </button>

                        <!-- Reject -->
                        <button
                            @click="updateStatus('rejected')"
                            :disabled="updating"
                            class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-red-600 hover:bg-red-700 disabled:opacity-50 disabled:cursor-not-allowed text-white font-semibold rounded-lg transition shadow"
                        >
                            <i data-lucide="x-circle" class="w-4 h-4"></i>
                            <span x-text="updating === 'rejected' ? 'Memproses...' : 'Tolak'"></span>
                        </button>

                        <!-- WhatsApp -->
                        <button
                            @click="sendWhatsApp()"
                            class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-lg transition shadow"
                        >
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                            WhatsApp
                        </button>

                        <!-- Close -->
                        <button
                            @click="closeModal()"
                            class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 font-semibold rounded-lg transition"
                        >
                            <i data-lucide="x" class="w-4 h-4"></i>
                            Tutup
                        </button>
                    </div>

                    <!-- Status Update Feedback -->
                    <template x-if="statusMessage">
                        <div
                            class="mt-3 p-3 rounded-lg text-sm font-medium text-center"
                            :class="statusMessageType === 'success' ? 'bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-400 border border-green-200 dark:border-green-800' : 'bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-400 border border-red-200 dark:border-red-800'"
                            x-text="statusMessage"
                        ></div>
                    </template>
                </div>
            </div>
        </div>
    </template>

    <!-- ==================== TOAST NOTIFICATION ==================== -->
    <div
        x-show="toast.show"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-2"
        class="fixed bottom-6 right-6 z-[200] max-w-sm"
    >
        <div
            class="px-5 py-3 rounded-xl shadow-lg font-medium text-sm flex items-center gap-3"
            :class="{
                'bg-green-600 text-white': toast.type === 'success',
                'bg-red-600 text-white': toast.type === 'error',
                'bg-blue-600 text-white': toast.type === 'info'
            }"
        >
            <template x-if="toast.type === 'success'"><i data-lucide="check-circle" class="w-5 h-5 flex-shrink-0"></i></template>
            <template x-if="toast.type === 'error'"><i data-lucide="alert-circle" class="w-5 h-5 flex-shrink-0"></i></template>
            <template x-if="toast.type === 'info'"><i data-lucide="info" class="w-5 h-5 flex-shrink-0"></i></template>
            <span x-text="toast.message"></span>
        </div>
    </div>
</section>

<script>
function adminDashboard() {
    return {
        // State
        registrations: [],
        filtered: [],
        searchQuery: '',
        statusFilter: 'all',
        loading: true,
        error: null,
        updating: false,

        // Modal
        showModal: false,
        selectedReg: null,
        adminNotes: '',
        noPeserta: '',
        statusMessage: '',
        statusMessageType: '',

        // Toast
        toast: { show: false, message: '', type: 'info' },

        // Stats
        stats: { total: 0, pending: 0, verified: 0, rejected: 0 },

        // Config (injected from PHP)
        adminToken: <?= json_encode(ADMIN_TOKEN) ?>,
        siteUrl: <?= json_encode(SITE_URL) ?>,

        async init() {
            await this.fetchRegistrations();
        },

        async fetchRegistrations() {
            this.loading = true;
            this.error = null;
            try {
                const response = await fetch('/api/registrations.php?token=' + encodeURIComponent(this.adminToken));
                if (!response.ok) {
                    throw new Error('HTTP ' + response.status + ': ' + response.statusText);
                }
                const data = await response.json();
                if (data.error) {
                    throw new Error(data.error);
                }
                this.registrations = Array.isArray(data) ? data : (data.data || []);
                this.filterRegistrations();
                this.calculateStats();
            } catch (err) {
                this.error = 'Gagal memuat data: ' + err.message;
                console.error('Fetch error:', err);
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
                pending: regs.filter(r => r.status === 'pending').length,
                verified: regs.filter(r => r.status === 'verified').length,
                rejected: regs.filter(r => r.status === 'rejected').length,
            };
        },

        filterRegistrations() {
            let results = [...this.registrations];

            // Filter by status
            if (this.statusFilter !== 'all') {
                results = results.filter(r => r.status === this.statusFilter);
            }

            // Filter by search query
            if (this.searchQuery.trim()) {
                const query = this.searchQuery.toLowerCase().trim();
                results = results.filter(r =>
                    (r.nama && r.nama.toLowerCase().includes(query)) ||
                    (r.nrp && r.nrp.toLowerCase().includes(query)) ||
                    (r.satuan && r.satuan.toLowerCase().includes(query)) ||
                    (r.registration_id && r.registration_id.toLowerCase().includes(query)) ||
                    (r.email && r.email.toLowerCase().includes(query)) ||
                    (r.pangkat && r.pangkat.toLowerCase().includes(query))
                );
            }

            this.filtered = results;
        },

        openDetail(reg) {
            this.selectedReg = { ...reg };
            this.adminNotes = reg.admin_notes || '';
            this.noPeserta = reg.no_peserta || '';
            this.statusMessage = '';
            this.statusMessageType = '';
            this.showModal = true;
            document.body.style.overflow = 'hidden';
            this.$nextTick(() => {
                if (typeof lucide !== 'undefined') lucide.createIcons();
            });
        },

        closeModal() {
            this.showModal = false;
            this.selectedReg = null;
            this.adminNotes = '';
            this.noPeserta = '';
            this.statusMessage = '';
            document.body.style.overflow = '';
        },

        async updateStatus(newStatus) {
            if (!this.selectedReg) return;

            // Validate no_peserta for verification
            if (newStatus === 'verified' && !this.noPeserta.trim()) {
                this.statusMessage = 'No. Peserta wajib diisi untuk verifikasi.';
                this.statusMessageType = 'error';
                return;
            }

            // Confirm action
            const actionLabel = newStatus === 'verified' ? 'memverifikasi' : 'menolak';
            if (!confirm('Apakah Anda yakin ingin ' + actionLabel + ' pendaftaran ' + this.selectedReg.nama + '?')) {
                return;
            }

            this.updating = newStatus;
            this.statusMessage = '';

            try {
                const payload = {
                    registration_id: this.selectedReg.registration_id,
                    status: newStatus,
                    admin_notes: this.adminNotes.trim(),
                };

                if (newStatus === 'verified') {
                    payload.no_peserta = this.noPeserta.trim();
                }

                const response = await fetch('/api/status.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Admin-Token': this.adminToken,
                    },
                    body: JSON.stringify(payload),
                });

                const data = await response.json();

                if (!response.ok || data.error) {
                    throw new Error(data.error || 'Gagal memperbarui status');
                }

                // Update local data
                const idx = this.registrations.findIndex(r => r.registration_id === this.selectedReg.registration_id);
                if (idx !== -1) {
                    this.registrations[idx].status = newStatus;
                    this.registrations[idx].admin_notes = this.adminNotes.trim();
                    if (newStatus === 'verified') {
                        this.registrations[idx].no_peserta = this.noPeserta.trim();
                    }
                }

                // Update selectedReg
                this.selectedReg.status = newStatus;
                if (newStatus === 'verified') {
                    this.selectedReg.no_peserta = this.noPeserta.trim();
                }
                this.selectedReg.admin_notes = this.adminNotes.trim();

                this.filterRegistrations();
                this.calculateStats();

                const successLabel = newStatus === 'verified' ? 'diverifikasi' : 'ditolak';
                this.statusMessage = 'Status berhasil diubah menjadi ' + successLabel + '.';
                this.statusMessageType = 'success';
                this.showToast('Status ' + this.selectedReg.nama + ' berhasil ' + successLabel, 'success');

            } catch (err) {
                this.statusMessage = 'Error: ' + err.message;
                this.statusMessageType = 'error';
                this.showToast('Gagal memperbarui status: ' + err.message, 'error');
            } finally {
                this.updating = false;
                this.$nextTick(() => {
                    if (typeof lucide !== 'undefined') lucide.createIcons();
                });
            }
        },

        sendWhatsApp() {
            if (!this.selectedReg) return;

            const reg = this.selectedReg;

            // Normalize phone: replace leading 0 with 62
            let phone = (reg.telepon || '').replace(/[\s\-()]/g, '');
            if (phone.startsWith('0')) {
                phone = '62' + phone.substring(1);
            } else if (!phone.startsWith('62') && !phone.startsWith('+62')) {
                phone = '62' + phone;
            }
            phone = phone.replace(/^\+/, '');

            const kategoriLabel = reg.kategori === 'presisi' ? 'Presisi 20M' : 'Dueling Plat';
            const noPeserta = reg.no_peserta || '-';
            const eTicketUrl = this.siteUrl + '/e-ticket.php?id=' + encodeURIComponent(reg.registration_id);

            const message = `🎯 *BDA SHOOTING CHAMPIONSHIP 2026*
━━━━━━━━━━━━━━━━━━━━━━━━━━━━
*PENGESAHAN PENDAFTARAN & E-TICKET*

Yth. *${reg.nama}* (${reg.pangkat}),
Pembayaran Anda telah *DIVERIFIKASI*.

📋 *Data Peserta:*
• No. Peserta: *${noPeserta}*
• NRP: ${reg.nrp}
• Satuan: ${reg.satuan}
• Kategori: ${kategoriLabel}
• Status: ✅ LUNAS

🎫 *E-Ticket:*
${eTicketUrl}

Simpan E-Ticket ini.
*17-18 Oktober 2026*`;

            const waUrl = 'https://wa.me/' + phone + '?text=' + encodeURIComponent(message);
            window.open(waUrl, '_blank');
            this.showToast('Membuka WhatsApp untuk ' + reg.nama, 'info');
        },

        exportCSV() {
            if (this.registrations.length === 0) {
                this.showToast('Tidak ada data untuk diekspor.', 'error');
                return;
            }

            const headers = [
                'No',
                'ID Registrasi',
                'Nama',
                'Email',
                'Telepon',
                'Pangkat',
                'NRP',
                'Satuan',
                'Kategori',
                'Status',
                'No Peserta',
                'Catatan Admin',
                'Tanggal Daftar'
            ];

            const escapeCSV = (val) => {
                if (val === null || val === undefined) return '';
                const str = String(val);
                if (str.includes(',') || str.includes('"') || str.includes('\n')) {
                    return '"' + str.replace(/"/g, '""') + '"';
                }
                return str;
            };

            let csv = '\uFEFF'; // BOM for Excel UTF-8
            csv += headers.map(escapeCSV).join(',') + '\n';

            this.filtered.forEach((reg, index) => {
                const row = [
                    index + 1,
                    reg.registration_id,
                    reg.nama,
                    reg.email,
                    reg.telepon,
                    reg.pangkat,
                    reg.nrp,
                    reg.satuan,
                    reg.kategori === 'presisi' ? 'Presisi 20M' : 'Dueling Plat',
                    reg.status,
                    reg.no_peserta || '',
                    reg.admin_notes || '',
                    reg.created_at
                ];
                csv += row.map(escapeCSV).join(',') + '\n';
            });

            const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
            const url = URL.createObjectURL(blob);
            const link = document.createElement('a');
            const timestamp = new Date().toISOString().slice(0, 10);
            link.href = url;
            link.download = 'bsc2026_registrations_' + timestamp + '.csv';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            URL.revokeObjectURL(url);

            this.showToast('CSV berhasil diunduh (' + this.filtered.length + ' data)', 'success');
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
                minute: '2-digit',
            });
        },

        showToast(message, type = 'info') {
            this.toast = { show: true, message, type };
            setTimeout(() => {
                this.toast.show = false;
            }, 4000);
        },
    };
}
</script>
<?php endif; ?>

<?php include __DIR__ . '/../includes/footer.php'; ?>
