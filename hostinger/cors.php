<?php
// CORS Header Handler
require_once __DIR__ . '/config.php';

header('Content-Type: application/json; charset=utf-8');

// Mendukung origin frontend GitHub Pages dan local dev
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
$allowedOrigins = [
    FRONTEND_URL,
    'http://localhost:3000',
    'http://127.0.0.1:3000'
];

if (in_array($origin, $allowedOrigins) || empty($origin)) {
    header('Access-Control-Allow-Origin: ' . ($origin ?: '*'));
} else {
    header('Access-Control-Allow-Origin: *');
}

header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
header('Access-Control-Allow-Credentials: true');

// Handle Preflight Request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}
