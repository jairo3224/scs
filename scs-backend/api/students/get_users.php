<?php
header("Content-Type: application/json; charset=utf-8");

// Allow React frontend
$allowedOrigins = ['http://localhost:3000'];
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';

if (in_array($origin, $allowedOrigins)) {
    header("Access-Control-Allow-Origin: $origin");
}

header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Credentials: true");

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit;
}

include __DIR__ . "/../db_connect.php";


$sql = "
    SELECT 
        id,
        first_name,
        last_name,
        email,
        student_id,
        phone_number,
        role,
        year_level,
        birthdate,
        created_at
    FROM students
    ORDER BY last_name ASC
";

$result = $conn->query($sql);

if (!$result) {
    echo json_encode(["success" => false, "error" => $conn->error]);
    exit;
}

$users = [];
while ($row = $result->fetch_assoc()) {
    $row['name'] = $row['first_name'] . ' ' . $row['last_name'];
    $users[] = $row;
}

echo json_encode([
    "success" => true,
    "users" => $users
]);

$conn->close();
?>
