<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: http://localhost:3000");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        "success" => false,
        "error" => "POST request required"
    ]);
    exit;
}

include 'db_connect.php';

$data = json_decode(file_get_contents("php://input"), true);

$email = trim($data['email'] ?? '');
$password = $data['password'] ?? '';

if ($email === '' || $password === '') {
    echo json_encode([
        "success" => false,
        "error" => "Email and password required"
    ]);
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        echo json_encode([
            "success" => false,
            "error" => "Invalid email or password"
        ]);
        exit;
    }

    $storedPassword = $user['password'];

    // Allow hashed or plaintext passwords
    $valid = password_verify($password, $storedPassword) || $password === $storedPassword;

    if (!$valid) {
        echo json_encode([
            "success" => false,
            "error" => "Invalid email or password"
        ]);
        exit;
    }

    // SUCCESS RESPONSE — no error sent
    echo json_encode([
        "success" => true,
        "user" => [
            "id" => $user['id'],
            "full_name" => $user['full_name'],
            "email" => $user['email'],
            "role" => $user['role'],
            "phone" => $user['phone'],
            "student_id" => $user['student_id']
        ]
    ]);
    exit;

} catch (Exception $e) {
    echo json_encode([
        "success" => false,
        "error" => "Server error: " . $e->getMessage()
    ]);
}
?>
