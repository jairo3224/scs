<?php
include 'db_connect.php'; // same folder, simple


$result = $conn->query("SELECT * FROM events ORDER BY event_date DESC");

$events = [];
while ($row = $result->fetch_assoc()) {
    $events[] = $row;
}

echo json_encode($events);
$conn->close();
?>
