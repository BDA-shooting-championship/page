<?php
/**
 * BDA Shooting Championship 2026 — Auth API (Login / Logout / Profile)
 */
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
cors();

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? $_POST['action'] ?? '';

// Fallback to reading JSON input
$input = [];
if ($method === 'POST') {
    $raw = file_get_contents('php://input');
    if (!empty($raw)) {
        $input = json_decode($raw, true) ?: [];
    }
    if (empty($action) && isset($input['action'])) {
        $action = $input['action'];
    }
}

if ($action === 'login' || ($method === 'POST' && empty($action) && isset($input['username']))) {
    $username = trim($input['username'] ?? $_POST['username'] ?? '');
    $password = trim($input['password'] ?? $_POST['password'] ?? '');

    if (empty($username) || empty($password)) {
        jsonResponse(['success' => false, 'error' => 'ID Admin dan Password wajib diisi.'], 400);
    }

    $db = getDB();
    try {
        $stmt = $db->prepare('SELECT * FROM admin_users WHERE username = ? LIMIT 1');
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            // Check fallback for initial superadmin if table exists but empty
            if ($username === ADMIN_USER && $password === ADMIN_PASS) {
                // Auto seed superadmin
                $hash = password_hash(ADMIN_PASS, PASSWORD_BCRYPT);
                $ins = $db->prepare('INSERT INTO admin_users (username, password_hash, display_name, role, permissions, is_active) VALUES (?, ?, ?, ?, ?, 1)');
                $ins->execute([ADMIN_USER, $hash, 'Super Administrator', 'superadmin', json_encode(['antrean', 'peserta', 'scores', 'users'])]);
                
                $user = [
                    'id' => $db->lastInsertId(),
                    'username' => ADMIN_USER,
                    'display_name' => 'Super Administrator',
                    'role' => 'superadmin',
                    'permissions' => ['antrean', 'peserta', 'scores', 'users'],
                    'is_active' => 1
                ];
            } else {
                jsonResponse(['success' => false, 'error' => 'ID Admin atau Password tidak valid.'], 401);
            }
        } else {
            // Verify password
            if (!password_verify($password, $user['password_hash'])) {
                jsonResponse(['success' => false, 'error' => 'ID Admin atau Password tidak valid.'], 401);
            }

            if ((int)$user['is_active'] !== 1) {
                jsonResponse(['success' => false, 'error' => 'Akun admin Anda sedang dinonaktifkan oleh Super Admin.'], 403);
            }
        }

        // Update last login
        $db->prepare('UPDATE admin_users SET last_login = NOW() WHERE id = ?')->execute([$user['id']]);

        // Decode permissions
        $perms = $user['permissions'];
        if (is_string($perms)) {
            $perms = json_decode($perms, true) ?: [];
        }

        // Set session
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_user'] = [
            'id' => (int)$user['id'],
            'username' => $user['username'],
            'display_name' => $user['display_name'],
            'role' => $user['role'],
            'permissions' => $perms
        ];

        jsonResponse([
            'success' => true,
            'message' => 'Login berhasil. Selamat datang, ' . htmlspecialchars($user['display_name']) . '!',
            'data' => $_SESSION['admin_user'],
            'redirect' => '/admin/'
        ]);

    } catch (PDOException $e) {
        error_log('Database error in auth.php: ' . $e->getMessage());
        jsonResponse(['success' => false, 'error' => 'Terjadi kesalahan sistem pada database'], 500);
    }

} elseif ($action === 'logout' || isset($_GET['logout'])) {
    $_SESSION = [];
    if (session_id() !== '') {
        session_destroy();
    }
    if ($method === 'POST') {
        jsonResponse(['success' => true, 'message' => 'Logout berhasil', 'redirect' => '/admin/']);
    } else {
        header('Location: /admin/');
        exit;
    }

} elseif ($action === 'me') {
    if (!isAdminLoggedIn()) {
        jsonResponse(['success' => false, 'error' => 'Belum login'], 401);
    }
    jsonResponse([
        'success' => true,
        'data' => getCurrentUser()
    ]);

} else {
    jsonResponse(['success' => false, 'error' => 'Invalid action'], 400);
}
