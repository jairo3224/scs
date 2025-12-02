<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
include __DIR__ . "/../db_connect.php";

// Fetch all appointments set by users with role president, officer, student
$sql = "
    SELECT l.*, u.full_name, u.role 
    FROM logs l
    JOIN users u ON l.user_id = u.id
    WHERE u.role IN ('president','officer','student')
    ORDER BY l.id DESC
";

$result = $conn->query($sql);
$logs = [];
while ($row = $result->fetch_assoc()) {
    $logs[] = $row;
}

echo json_encode(["success" => true, "logs" => $logs]);
?>
