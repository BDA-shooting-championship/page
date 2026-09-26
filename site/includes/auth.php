<?php
/**
 * BDA Shooting Championship 2026 — Authentication & Modular Access Control
 */
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Get current logged in admin user
 */
function getCurrentUser(): ?array {
    return $_SESSION['admin_user'] ?? null;
}

/**
 * Check if an admin is logged in
 */
function isAdminLoggedIn(): bool {
    return !empty($_SESSION['admin_logged_in']) && !empty($_SESSION['admin_user']);
}

/**
 * Check if the logged in user is a Super Admin
 */
function isSuperAdmin(): bool {
    $user = getCurrentUser();
    return $user !== null && ($user['role'] ?? '') === 'superadmin';
}

/**
 * Check if the user has permission for a specific menu
 * Available menus: 'antrean', 'peserta', 'scores', 'users'
 */
function hasPermission(string $menuKey): bool {
    $user = getCurrentUser();
    if (!$user) return false;
    if (($user['role'] ?? '') === 'superadmin') return true;

    $perms = $user['permissions'] ?? [];
    if (is_string($perms)) {
        $perms = json_decode($perms, true) ?: [];
    }
    return in_array($menuKey, (array) $perms);
}

/**
 * Enforce permission check or return 403 JSON
 */
function requirePermission(string $menuKey): void {
    if (!isAdminLoggedIn()) {
        jsonResponse(['success' => false, 'error' => 'Sesi login telah berakhir. Silakan login kembali.'], 401);
    }
    if (!hasPermission($menuKey)) {
        jsonResponse(['success' => false, 'error' => 'Akses ditolak: Akun Anda tidak memiliki izin untuk fitur ini.'], 403);
    }
}
