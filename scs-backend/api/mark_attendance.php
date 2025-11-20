<?php
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $event_id = $_POST['event_id'];
    $student_id = $_POST['student_id'];
    $status = $_POST['status']; // 'present' or 'absent'

    // Check if attendance already exists
    $check = $conn->prepare("SELECT id FROM attendance WHERE event_id = ? AND student_id = ?");
    $check->bind_param("ii", $event_id, $student_id);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        // Update existing record
        $stmt = $conn->prepare("UPDATE attendance SET status = ? WHERE event_id = ? AND student_id = ?");
        $stmt->bind_param("sii", $status, $event_id, $student_id);
    } else {
        // Insert new record
        $stmt = $conn->prepare("INSERT INTO attendance (event_id, student_id, status) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $event_id, $student_id, $status);
    }

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Attendance saved"]);
    } else {
        echo json_encode(["status" => "error", "message" => $stmt->error]);
    }

    $stmt->close();
    $conn->close();
}
?>
