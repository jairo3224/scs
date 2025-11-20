<?php
include 'db_connect.php'; // your database connection file

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $event_date = $_POST['event_date'];
    $created_by = $_POST['created_by']; // user id of the creator

    $stmt = $conn->prepare("INSERT INTO events (title, description, event_date, created_by) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sssi", $title, $description, $event_date, $created_by);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Event created"]);
    } else {
        echo json_encode(["status" => "error", "message" => $stmt->error]);
    }

    $stmt->close();
    $conn->close();
}
?>
