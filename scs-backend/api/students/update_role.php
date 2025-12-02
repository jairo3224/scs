<?php
$allowedOrigins = ['http://localhost:3000'];
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
if (in_array($origin, $allowedOrigins)) {
    header("Access-Control-Allow-Origin: $origin");
}

header("Content-Type: application/json; charset=utf-8");

include __DIR__ . "/../db_connect.php";

$data = json_decode(file_get_contents("php://input"), true);

$id = $data['id'] ?? null;
$role = $data['role'] ?? null;

if (!$id || !$role) {
    echo json_encode(["success" => false, "error" => "ID and role are required"]);
    exit;
}

$conn->begin_transaction();

try {
    // Get the student_id
    $res = $conn->prepare("SELECT student_id FROM students WHERE id = ?");
    $res->bind_param("i", $id);
    $res->execute();
    $row = $res->get_result()->fetch_assoc();
    $res->close();

    if (!$row) {
        throw new Exception("Student not found");
    }

    $student_id = $row['student_id'];

    // Update students table
    $stmt1 = $conn->prepare("UPDATE students SET role = ? WHERE id = ?");
    $stmt1->bind_param("si", $role, $id);
    $stmt1->execute();
    $stmt1->close();

    // Update users table
    $stmt2 = $conn->prepare("UPDATE users SET role = ? WHERE student_id = ?");
    $stmt2->bind_param("ss", $role, $student_id);
    $stmt2->execute();
    $stmt2->close();

    $conn->commit();

    echo json_encode(["success" => true, "message" => "Role updated successfully"]);

} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(["success" => false, "error" => $e->getMessage()]);
}

$conn->close();
?>
