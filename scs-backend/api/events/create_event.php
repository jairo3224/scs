<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
header("Access-Control-Allow-Headers: *");

include "../config/db.php";

$data = json_decode(file_get_contents("php://input"), true);

$title = $data['title'];
$description = $data['description'];
$date = $data['date'];
$time = $data['time'];
$location = $data['location'];

$sql = "INSERT INTO events (title, description, date, time, location)
        VALUES (?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("sssss", $title, $description, $date, $time, $location);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Event created successfully"]);
} else {
    echo json_encode(["success" => false, "message" => "Error creating event"]);
}
?>
