<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

include __DIR__ . "/../db_connect.php";

// Fetch students whose role is president, officer, or student
$sql = "SELECT id, first_name, last_name, role, paid 
        FROM students 
        WHERE role IN ('president', 'officer', 'student')
        ORDER BY role ASC, last_name ASC";

$result = $conn->query($sql);

if (!$result) {
    echo json_encode([
        "success" => false,
        "error" => "Database query failed: " . $conn->error
    ]);
    exit;
}

$students = [];
while ($row = $result->fetch_assoc()) {
    // Check that first_name and last_name exist
    $first = isset($row['first_name']) ? $row['first_name'] : '';
    $last = isset($row['last_name']) ? $row['last_name'] : '';
    
    $students[] = [
        "id" => $row["id"],
        "name" => trim($first . " " . $last), // combine first and last
        "role" => $row["role"],
        "paid" => (bool)$row["paid"]
    ];
}

echo json_encode([
    "success" => true,
    "students" => $students
]);

$conn->close();
?>
