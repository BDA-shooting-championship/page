<?php
/**
 * Database Connection (PDO Singleton)
 */
require_once __DIR__ . '/config.php';

function getDB(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        try {
            $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
            $pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        } catch (PDOException $e) {
            http_response_code(500);
            die(json_encode(['error' => 'Database connection failed']));
        }
    }
    return $pdo;
}

function jsonResponse($data, int $code = 200): void {
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

function requireAdmin(): void {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (!empty($_SESSION['admin_logged_in']) && !empty($_SESSION['admin_user'])) {
        return;
    }
    $token = $_SERVER['HTTP_X_ADMIN_TOKEN'] ?? $_GET['token'] ?? $_POST['token'] ?? '';
    if ($token === ADMIN_TOKEN) {
        return;
    }
    // Also check token in JSON request body if present
    $rawInput = file_get_contents('php://input');
    if ($rawInput) {
        $json = json_decode($rawInput, true);
        if (is_array($json) && isset($json['token']) && $json['token'] === ADMIN_TOKEN) {
            return;
        }
    }
    jsonResponse(['error' => 'Unauthorized'], 401);
}

function cors(): void {
    $origin = $_SERVER['HTTP_ORIGIN'] ?? '';
    if ($origin) {
        $parsed = parse_url($origin);
        $host = strtolower($parsed['host'] ?? '');
        $allowed = false;

        if ($host === 'bda-shooting-championship.sbs' || 
            $host === 'www.bda-shooting-championship.sbs' ||
            str_ends_with($host, '.bda-shooting-championship.sbs') ||
            $host === 'localhost' || 
            $host === '127.0.0.1') {
            $allowed = true;
        }

        $currentHost = strtolower(explode(':', $_SERVER['HTTP_HOST'] ?? '')[0]);
        if ($currentHost && $host === $currentHost) {
            $allowed = true;
        }

        if ($allowed) {
            header("Access-Control-Allow-Origin: $origin");
            header('Access-Control-Allow-Credentials: true');
        }
    } else {
        header("Access-Control-Allow-Origin: *");
    }

    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type, X-Admin-Token, Authorization, X-Requested-With");
    header("Access-Control-Max-Age: 86400");

    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(204);
        exit;
    }
}
