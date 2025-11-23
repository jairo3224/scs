<?php
// --- CORS / global headers (runs for every API that includes this file) ---
$allowedOrigins = [
    'http://localhost:3000',
    // add other allowed origins here
];

$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
if (in_array($origin, $allowedOrigins)) {
    header("Access-Control-Allow-Origin: $origin");
}
// Content type for JSON responses
header("Content-Type: application/json; charset=utf-8");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Credentials: true");

// handle preflight and exit early
if (php_sapi_name() !== 'cli' && ($_SERVER['REQUEST_METHOD'] ?? '') === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// DB config — use 127.0.0.1 and explicit port for Windows/XAMPP
$host = "127.0.0.1";
$port = 3306;
$dbname = "scs_db";
$username = "root";
$password = "";

// Helpful debug: show clean JSON error instead of dying with raw PDO message
try {
    $dsn = "mysql:host={$host};port={$port};dbname={$dbname};charset=utf8";
    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    // Temporary: return JSON with helpful error for local debugging.
    // Remove detailed message in production.
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "error" => "Database connection failed: " . $e->getMessage()
    ]);
    exit;
}
?>