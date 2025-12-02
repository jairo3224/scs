<?php
// CORS setup
$allowedOrigins = ['http://localhost:3000'];
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
if (in_array($origin, $allowedOrigins)) {
    header("Access-Control-Allow-Origin: $origin");
}
header("Content-Type: application/json; charset=utf-8");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Credentials: true");

// Preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

include __DIR__ . "/../db_connect.php";

// Read JSON input
$data = json_decode(file_get_contents("php://input"), true);
$id = $data["id"] ?? null;

if (!$id) {
    echo json_encode(["success" => false, "error" => "User ID is required"]);
    exit;
}

// Start transaction so both deletes succeed together
$conn->begin_transaction();

try {
    // Delete from USERS first (because users has foreign reference to students)
    $stmt1 = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt1->bind_param("i", $id);
    $stmt1->execute();

    // Delete from STUDENTS
    $stmt2 = $conn->prepare("DELETE FROM students WHERE id = ?");
    $stmt2->bind_param("i", $id);
    $stmt2->execute();

    // Commit if both succeed
    $conn->commit();

    echo json_encode(["success" => true, "message" => "User deleted successfully"]);
} catch (Exception $e) {
    // Roll back if something fails
    $conn->rollback();
    echo json_encode(["success" => false, "error" => $e->getMessage()]);
}

$stmt1->close();
$stmt2->close();
$conn->close();
?>
