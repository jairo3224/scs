<?php
include 'db_connect.php';

$event_id = $_GET['event_id'];

$stmt = $conn->prepare("SELECT a.*, u.full_name FROM attendance a JOIN users u ON a.student_id = u.id WHERE event_id = ?");
$stmt->bind_param("i", $event_id);
$stmt->execute();
$result = $stmt->get_result();

$attendance = [];
while ($row = $result->fetch_assoc()) {
    $attendance[] = $row;
}

echo json_encode($attendance);
$stmt->close();
$conn->close();
?>


