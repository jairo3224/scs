<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

include __DIR__ . "/../db_connect.php";

$data = json_decode(file_get_contents("php://input"), true);

$id           = $data['id'] ?? '';
$first_name   = $data['first_name'] ?? '';
$last_name    = $data['last_name'] ?? '';
$email        = $data['email'] ?? '';
$phone_number = $data['phone_number'] ?? '';
$year_level   = $data['year_level'] ?? '';
$birthdate    = $data['birthdate'] ?? '';

if (!$id || !$first_name || !$last_name || !$email) {
    echo json_encode(["success" => false, "error" => "Required fields missing"]);
    exit;
}

$stmt = $conn->prepare("
    UPDATE students SET 
        first_name = ?, 
        last_name = ?, 
        email = ?, 
        phone_number = ?, 
        year_level = ?, 
        birthdate = ? 
    WHERE id = ?
");
$stmt->bind_param("ssssssi", $first_name, $last_name, $email, $phone_number, $year_level, $birthdate, $id);
$success = $stmt->execute();
$stmt->close();

echo json_encode(["success" => $success]);
$conn->close();
?>
