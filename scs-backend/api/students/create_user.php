<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

include __DIR__ . "/../db_connect.php";

$data = json_decode(file_get_contents("php://input"), true);

$first_name    = $data['first_name'] ?? '';
$last_name     = $data['last_name'] ?? '';
$email         = $data['email'] ?? ''; // <- EMAIL restored
$birthdate     = $data['birthdate'] ?? '';
$phone_number  = $data['phone_number'] ?? '';
$student_id    = $data['student_id'] ?? '';
$temp_password = $data['temp_password'] ?? '';
$year_level    = $data['year_level'] ?? '';

if (!$first_name || !$last_name || !$email || !$birthdate || !$phone_number || !$student_id || !$temp_password || !$year_level) {
    echo json_encode(["success" => false, "error" => "All fields are required"]);
    exit;
}

$full_name = $first_name . " " . $last_name;
$hashed_password = password_hash($temp_password, PASSWORD_DEFAULT);

$conn->begin_transaction();

try {
    // Insert student
    $stmt = $conn->prepare("
        INSERT INTO students 
        (first_name, last_name, email, birthdate, phone_number, student_id, temp_password, year_level, role)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'student')
    ");
    $stmt->bind_param(
        "ssssssss",
        $first_name,
        $last_name,
        $email,
        $birthdate,
        $phone_number,
        $student_id,
        $hashed_password,
        $year_level
    );
    $stmt->execute();
    $stmt->close();

    // Insert into users (login)
    $stmt2 = $conn->prepare("
        INSERT INTO users (full_name, email, password, role, student_id, phone)
        VALUES (?, ?, ?, 'student', ?, ?)
    ");
    $stmt2->bind_param(
        "sssss",
        $full_name,
        $email,
        $hashed_password,
        $student_id,
        $phone_number
    );
    $stmt2->execute();
    $stmt2->close();

    $conn->commit();

    echo json_encode([
        "success" => true,
        "message" => "Student created successfully."
    ]);

} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(["success" => false, "error" => $e->getMessage()]);
}

$conn->close();
?>
