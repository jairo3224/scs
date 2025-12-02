<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
include __DIR__ . "/../db_connect.php";

$event_id = intval($_GET['event_id'] ?? 0);
if (!$event_id) {
    echo json_encode(["success" => false, "error" => "Event ID is required"]);
    exit;
}

// Get all students with roles president, officer, student
$sql = "
SELECT s.id, s.first_name, s.last_name, s.role,
       a.status
FROM students s
LEFT JOIN attendance a
    ON a.student_id = s.id AND a.event_id = ?
WHERE s.role IN ('president', 'officer', 'student')
ORDER BY FIELD(s.role, 'president','officer','student'), s.last_name
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $event_id);
$stmt->execute();
$result = $stmt->get_result();

$students = [];
while($row = $result->fetch_assoc()){
    $row['name'] = $row['first_name'] . ' ' . $row['last_name'];
    $students[] = $row;
}

echo json_encode(["success"=>true, "students"=>$students]);
$stmt->close();
$conn->close();
?>
