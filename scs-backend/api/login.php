<?php

header("Content-Type: application/json; charset=UTF-8");
error_reporting(0);


header("Content-Type: application/json");
// Allow requests from your frontend (you can use * during development)
header("Access-Control-Allow-Origin: http://localhost:3000");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// respond to preflight and stop
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    echo json_encode(['ok' => true, 'time' => date('c')]);
    exit;
}

// include file in same folder (was ../db_connect.php which caused "no such file")
include 'db_connect.php';

$data = json_decode(file_get_contents("php://input"), true);
$email = $data['email'] ?? '';
$password = $data['password'] ?? '';

try {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        echo json_encode(["success" => false, "error" => "Invalid email or password"]);
        exit;
    }

    if (!password_verify($password, $user['password'])) {
        echo json_encode(["success" => false, "error" => "Invalid email or password"]);
        exit;
    }

    echo json_encode([
        "success" => true,
        "user" => [
            "role" => $user['role'],
            "full_name" => $user['full_name'],
            "id" => $user['id'],
            "student_id" => $user['student_id'],
            "phone" => $user['phone']
        ]
    ]);

} catch (Exception $e) {
    echo json_encode(["success" => false, "error" => $e->getMessage()]);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["success" => false, "message" => "POST required"]);
    exit;
}

?>