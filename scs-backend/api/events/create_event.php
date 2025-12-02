<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

include __DIR__ . "/../db_connect.php";

// Enable error reporting for debugging
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    // Get JSON POST data
    $data = json_decode(file_get_contents("php://input"), true);

    $title       = trim($data['title'] ?? '');
    $description = trim($data['description'] ?? null);
    $location    = trim($data['location'] ?? null);
    $event_date  = trim($data['event_date'] ?? '');
    $time        = trim($data['time'] ?? null);
    $created_by  = intval($data['created_by'] ?? 1); // default 1 for testing

    // Validation
    if (!$title || !$event_date) {
        echo json_encode(["success" => false, "error" => "Title and Date are required"]);
        exit;
    }

    // Prepare SQL
    $stmt = $conn->prepare("
        INSERT INTO events (title, description, location, event_date, time, created_by) 
        VALUES (?, ?, ?, ?, ?, ?)
    ");

    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }

    // Bind parameters
    $stmt->bind_param(
        "sssssi",
        $title,
        $description,
        $location,
        $event_date,
        $time,
        $created_by
    );

    // Execute
    $stmt->execute();

    echo json_encode(["success" => true, "message" => "Event created successfully"]);

} catch (mysqli_sql_exception $e) {
    echo json_encode(["success" => false, "error" => "MySQL error: " . $e->getMessage()]);
} catch (Exception $e) {
    echo json_encode(["success" => false, "error" => "PHP error: " . $e->getMessage()]);
} finally {
    if (isset($stmt) && $stmt) $stmt->close();
    if (isset($conn) && $conn) $conn->close();
}
