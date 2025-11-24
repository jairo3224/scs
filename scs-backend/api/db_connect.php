<?php
$allowedOrigins = [
    'http://localhost:3000',
];

$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
if (in_array($origin, $allowedOrigins)) {
    header("Access-Control-Allow-Origin: $origin");
}

header("Content-Type: application/json; charset=utf-8");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Credentials: true");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$host = "127.0.0.1";
$port = 3306;
$dbname = "scs_db";
$username = "root";
$password = "";

// PDO (not used in your code but kept for future use)
try {
    $dsn = "mysql:host={$host};port={$port};dbname={$dbname};charset=utf8";
    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "error" => "DB connection failed: " . $e->getMessage()
    ]);
    exit;
}

// MySQLi (your scripts use this)
$conn = new mysqli($host, $username, $password, $dbname, $port);

if ($conn->connect_errno) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "error" => "MySQLi error: " . $conn->connect_error
    ]);
    exit;
}
?>
