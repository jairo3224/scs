<?php
header("Content-Type: application/json");
include "../db.php";

$data = json_decode(file_get_contents("php://input"), true);

if (
    empty($data['full_name']) ||
    empty($data['email']) ||
    empty($data['password']) ||
    empty($data['role'])
) {
    echo json_encode(["success" => false, "error" => "Missing fields"]);
    exit;
}

$role = $data['role'];

// 🔒 Prevent creation of chairperson account
if ($role === "chairperson") {
    echo json_encode(["success" => false, "error" => "You cannot create another chairperson account"]);
    exit;
}

$full_name = $data['full_name'];
$email = $data['email'];
$password = password_hash($data['password'], PASSWORD_DEFAULT);
$student_id = $data['student_id'] ?? null;
$phone = $data['phone'] ?? null;

// SQL Insert
$sql = "INSERT INTO users (full_name, email, password, role, student_id, phone)
        VALUES (
            '$full_name',
            '$email',
            '$password',
            '$role',
            " . ($student_id ? "'$student_id'" : "NULL") . ",
            " . ($phone ? "'$phone'" : "NULL") . "
        )";

$result = mysqli_query($conn, $sql);

if ($result) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "error" => mysqli_error($conn)]);
}
?>
