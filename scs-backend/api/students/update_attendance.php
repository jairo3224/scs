<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
include __DIR__ . "/../db_connect.php";

$data = json_decode(file_get_contents("php://input"), true);
$event_id = intval($data['event_id'] ?? 0);
$student_id = intval($data['student_id'] ?? 0);
$status = $data['status'] ?? '';

if (!$event_id || !$student_id || !in_array($status, ['present','absent'])) {
    echo json_encode(["success"=>false,"error"=>"Invalid data"]);
    exit;
}

// Check if attendance record exists
$stmt = $conn->prepare("SELECT id FROM attendance WHERE event_id=? AND student_id=?");
$stmt->bind_param("ii",$event_id,$student_id);
$stmt->execute();
$result = $stmt->get_result();
$exists = $result->num_rows > 0;
$stmt->close();

if($exists){
    // update
    $stmt = $conn->prepare("UPDATE attendance SET status=? WHERE event_id=? AND student_id=?");
    $stmt->bind_param("sii",$status,$event_id,$student_id);
    $stmt->execute();
    $stmt->close();
} else {
    // insert
    $stmt = $conn->prepare("INSERT INTO attendance (event_id, student_id, status) VALUES (?,?,?)");
    $stmt->bind_param("iis",$event_id,$student_id,$status);
    $stmt->execute();
    $stmt->close();
}

echo json_encode(["success"=>true,"message"=>"Attendance updated"]);
$conn->close();
?>
