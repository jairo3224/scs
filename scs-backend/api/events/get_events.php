<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

include __DIR__ . "/../db_connect.php";

// Fetch all events, ordered by event_date ascending
$sql = "SELECT id, title, description, location, event_date, time, created_by FROM events ORDER BY event_date ASC";
$result = $conn->query($sql);

if (!$result) {
    echo json_encode([
        "success" => false,
        "error" => "Query failed: " . $conn->error
    ]);
    exit;
}

$events = [];

while ($row = $result->fetch_assoc()) {
    // Ensure optional fields are returned as null if empty
    $events[] = [
        "id"          => intval($row['id']),
        "title"       => $row['title'],
        "description" => $row['description'] ?? null,
        "location"    => $row['location'] ?? null,
        "event_date"  => $row['event_date'],
        "time"        => $row['time'] ?? null,
        "created_by"  => intval($row['created_by']),
    ];
}

echo json_encode([
    "success" => true,
    "events"  => $events
]);

$conn->close();
?>
