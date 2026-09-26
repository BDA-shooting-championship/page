<?php
/**
 * BDA Shooting Championship 2026 — Admin Users Management API
 * Super Admin only: Create, Read, Update, Toggle Active, and Delete Admin Accounts
 */
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
cors();

$currentUser = getCurrentUser();

// Only Super Admin can access user management
if (!isSuperAdmin()) {
    // Also allow if valid admin token is passed via header for emergency administrative CLI access
    $token = $_SERVER['HTTP_X_ADMIN_TOKEN'] ?? $_GET['token'] ?? '';
    if ($token !== ADMIN_TOKEN) {
        jsonResponse(['success' => false, 'error' => 'Akses ditolak: Hanya Super Admin yang dapat mengelola akun admin.'], 403);
    }
}

$method = $_SERVER['REQUEST_METHOD'];
$db = getDB();

if ($method === 'GET') {
    try {
        $stmt = $db->query('
            SELECT id, username, display_name, role, permissions, is_active, last_login, created_at, updated_at
            FROM admin_users
            ORDER BY FIELD(role, "superadmin", "admin"), id ASC
        ');
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Parse permissions JSON
        foreach ($users as &$u) {
            $u['id'] = (int) $u['id'];
            $u['is_active'] = (int) $u['is_active'];
            if (is_string($u['permissions'])) {
                $u['permissions'] = json_decode($u['permissions'], true) ?: [];
            }
        }
        unset($u);

        jsonResponse([
            'success' => true,
            'data' => $users,
            'total' => count($users)
        ]);
    } catch (PDOException $e) {
        error_log('Gagal memuat akun admin: ' . $e->getMessage());
        jsonResponse(['success' => false, 'error' => 'Terjadi kesalahan sistem saat memuat akun admin.'], 500);
    }

} elseif ($method === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
    $action = $input['action'] ?? '';

    if ($action === 'create') {
        $username = trim($input['username'] ?? '');
        $password = trim($input['password'] ?? '');
        $displayName = trim($input['display_name'] ?? '');
        $role = trim($input['role'] ?? 'admin');
        $permissions = $input['permissions'] ?? ['antrean'];

        if (empty($username) || empty($password) || empty($displayName)) {
            jsonResponse(['success' => false, 'error' => 'ID Admin (Username), Password, dan Nama Lengkap wajib diisi.'], 400);
        }

        if (!in_array($role, ['superadmin', 'admin'])) {
            $role = 'admin';
        }

        if (!is_array($permissions)) {
            $permissions = [];
        }

        // If superadmin, always grant all permissions
        if ($role === 'superadmin') {
            $permissions = ['antrean', 'peserta', 'scores', 'users'];
        }

        try {
            // Check username uniqueness
            $check = $db->prepare('SELECT id FROM admin_users WHERE username = ?');
            $check->execute([$username]);
            if ($check->fetch()) {
                jsonResponse(['success' => false, 'error' => 'ID / Username "' . htmlspecialchars($username) . '" sudah digunakan. Silakan gunakan yang lain.'], 400);
            }

            $passwordHash = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $db->prepare('
                INSERT INTO admin_users (username, password_hash, display_name, role, permissions, is_active, created_at)
                VALUES (?, ?, ?, ?, ?, 1, NOW())
            ');
            $stmt->execute([$username, $passwordHash, $displayName, $role, json_encode($permissions)]);
            $newId = (int) $db->lastInsertId();

            jsonResponse([
                'success' => true,
                'message' => 'Akun admin "' . htmlspecialchars($username) . '" berhasil dibuat!',
                'data' => [
                    'id' => $newId,
                    'username' => $username,
                    'display_name' => $displayName,
                    'role' => $role,
                    'permissions' => $permissions,
                    'is_active' => 1
                ]
            ]);
        } catch (PDOException $e) {
            error_log('Gagal membuat akun: ' . $e->getMessage());
            jsonResponse(['success' => false, 'error' => 'Terjadi kesalahan sistem saat membuat akun.'], 500);
        }

    } elseif ($action === 'edit') {
        $id = (int)($input['id'] ?? 0);
        $displayName = trim($input['display_name'] ?? '');
        $role = trim($input['role'] ?? 'admin');
        $permissions = $input['permissions'] ?? [];
        $newPassword = trim($input['password'] ?? '');

        if ($id <= 0 || empty($displayName)) {
            jsonResponse(['success' => false, 'error' => 'ID Akun dan Nama Lengkap wajib diisi.'], 400);
        }

        if (!in_array($role, ['superadmin', 'admin'])) {
            $role = 'admin';
        }

        if (!is_array($permissions)) {
            $permissions = [];
        }

        if ($role === 'superadmin') {
            $permissions = ['antrean', 'peserta', 'scores', 'users'];
        }

        try {
            $stmt = $db->prepare('SELECT * FROM admin_users WHERE id = ?');
            $stmt->execute([$id]);
            $existing = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$existing) {
                jsonResponse(['success' => false, 'error' => 'Akun tidak ditemukan.'], 404);
            }

            if (!empty($newPassword)) {
                $passwordHash = password_hash($newPassword, PASSWORD_BCRYPT);
                $update = $db->prepare('
                    UPDATE admin_users 
                    SET display_name = ?, role = ?, permissions = ?, password_hash = ?, updated_at = NOW()
                    WHERE id = ?
                ');
                $update->execute([$displayName, $role, json_encode($permissions), $passwordHash, $id]);
            } else {
                $update = $db->prepare('
                    UPDATE admin_users 
                    SET display_name = ?, role = ?, permissions = ?, updated_at = NOW()
                    WHERE id = ?
                ');
                $update->execute([$displayName, $role, json_encode($permissions), $id]);
            }

            // If updating current logged in user, refresh session
            if ($currentUser && (int)$currentUser['id'] === $id) {
                $_SESSION['admin_user']['display_name'] = $displayName;
                $_SESSION['admin_user']['role'] = $role;
                $_SESSION['admin_user']['permissions'] = $permissions;
            }

            jsonResponse([
                'success' => true,
                'message' => 'Akun "' . htmlspecialchars($existing['username']) . '" berhasil diperbarui!'
            ]);
        } catch (PDOException $e) {
            error_log('Gagal mengedit akun: ' . $e->getMessage());
            jsonResponse(['success' => false, 'error' => 'Terjadi kesalahan sistem saat memperbarui akun.'], 500);
        }

    } elseif ($action === 'toggle_active') {
        $id = (int)($input['id'] ?? 0);
        if ($id <= 0) {
            jsonResponse(['success' => false, 'error' => 'ID Akun tidak valid.'], 400);
        }

        // Self-protection: cannot deactivate self
        if ($currentUser && (int)$currentUser['id'] === $id) {
            jsonResponse(['success' => false, 'error' => 'Anda tidak dapat menonaktifkan akun Anda sendiri.'], 400);
        }

        try {
            $stmt = $db->prepare('SELECT id, username, is_active FROM admin_users WHERE id = ?');
            $stmt->execute([$id]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$user) {
                jsonResponse(['success' => false, 'error' => 'Akun tidak ditemukan.'], 404);
            }

            $newStatus = ((int)$user['is_active'] === 1) ? 0 : 1;
            $db->prepare('UPDATE admin_users SET is_active = ?, updated_at = NOW() WHERE id = ?')->execute([$newStatus, $id]);

            $statusText = $newStatus === 1 ? 'diaktifkan' : 'dinonaktifkan';
            jsonResponse([
                'success' => true,
                'message' => 'Akun "' . htmlspecialchars($user['username']) . '" berhasil ' . $statusText . '.',
                'is_active' => $newStatus
            ]);
        } catch (PDOException $e) {
            error_log('Gagal mengubah status akun: ' . $e->getMessage());
            jsonResponse(['success' => false, 'error' => 'Terjadi kesalahan sistem saat mengubah status akun.'], 500);
        }

    } elseif ($action === 'delete') {
        $id = (int)($input['id'] ?? 0);
        if ($id <= 0) {
            jsonResponse(['success' => false, 'error' => 'ID Akun tidak valid.'], 400);
        }

        // Self-protection: cannot delete self
        if ($currentUser && (int)$currentUser['id'] === $id) {
            jsonResponse(['success' => false, 'error' => 'Anda tidak dapat menghapus akun Anda sendiri.'], 400);
        }

        try {
            $stmt = $db->prepare('SELECT id, username FROM admin_users WHERE id = ?');
            $stmt->execute([$id]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$user) {
                jsonResponse(['success' => false, 'error' => 'Akun tidak ditemukan.'], 404);
            }

            $db->prepare('DELETE FROM admin_users WHERE id = ?')->execute([$id]);

            jsonResponse([
                'success' => true,
                'message' => 'Akun "' . htmlspecialchars($user['username']) . '" berhasil dihapus.'
            ]);
        } catch (PDOException $e) {
            error_log('Gagal menghapus akun: ' . $e->getMessage());
            jsonResponse(['success' => false, 'error' => 'Terjadi kesalahan sistem saat menghapus akun.'], 500);
        }

    } else {
        jsonResponse(['success' => false, 'error' => 'Action tidak dikenal.'], 400);
    }

} else {
    jsonResponse(['success' => false, 'error' => 'Method not allowed'], 405);
}
