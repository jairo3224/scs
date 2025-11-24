<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// always include ONLY db_connect.php
include __DIR__ . "/../db_connect.php";


$data = json_decode(file_get_contents("php://input"), true);

$full_name  = $data["full_name"] ?? "";
$email      = $data["email"] ?? "";
$password   = $data["password"] ?? "";
$role       = $data["role"] ?? "";
$student_id = $data["student_id"] ?? null;
$phone      = $data["phone"] ?? null;

if (!$full_name || !$email || !$password || !$role) {
    echo json_encode(["success" => false, "error" => "Missing required fields"]);
    exit;
}

$hashed = md5($password); 

$query = $conn->prepare("
    INSERT INTO users (full_name, email, password, role, student_id, phone)
    VALUES (?, ?, ?, ?, ?, ?)
");

$query->bind_param("ssssss", $full_name, $email, $hashed, $role, $student_id, $phone);

if ($query->execute()) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode([
        "success" => false,
        "error" => "Database error: " . $conn->error
    ]);
}
?>
