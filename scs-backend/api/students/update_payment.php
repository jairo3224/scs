<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

include __DIR__ . "/../db_connect.php";

// Get JSON POST data
$data = json_decode(file_get_contents("php://input"), true);

$student_id = intval($data['student_id'] ?? 0);
$paid = intval($data['paid'] ?? 0);
$updater_role = trim($data['role'] ?? ''); // role of logged-in user

// Only chairperson or president can update
if (!in_array($updater_role, ['chairperson', 'president'])) {
    echo json_encode(["success" => false, "error" => "Unauthorized"]);
    exit;
}

if ($student_id <= 0) {
    echo json_encode(["success" => false, "error" => "Invalid student ID"]);
    exit;
}

// Update payment status
$stmt = $conn->prepare("UPDATE students SET paid = ? WHERE id = ?");
$stmt->bind_param("ii", $paid, $student_id);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Payment status updated"]);
} else {
    echo json_encode(["success" => false, "error" => $stmt->error]);
}

$stmt->close();
$conn->close();
?>
