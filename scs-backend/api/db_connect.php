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
// ...existing code...
$host = "localhost";
$dbname = "scs_db";
$username = "root";
$password = "";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die(json_encode(["success" => false, "error" => "Database connection failed: " . $e->getMessage()]));
}
?>