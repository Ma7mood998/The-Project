<?php
require_once 'db.php';

// Check if room_id is provided
if (!isset($_GET['room_id'])) {
    http_response_code(400);
    echo json_encode(["error" => "room_id parameter is required"]);
    exit;
}

$room_id = intval($_GET['room_id']);

try {
    // SQL query to fetch room details
    $roomQuery = "SELECT room_name, capacity, equipment 
                  FROM rooms 
                  WHERE room_id = :room_id";
    $roomStmt = $pdo->prepare($roomQuery);
    $roomStmt->execute([':room_id' => $room_id]);
    $room = $roomStmt->fetch();

    if (!$room) {
        http_response_code(404);
        echo json_encode(["error" => "Room not found"]);
        exit;
    }

    // SQL query to fetch room schedule
    $scheduleQuery = "SELECT available_from, available_to, status 
                      FROM room_schedules 
                      WHERE room_id = :room_id 
                      ORDER BY available_from ASC";
    $scheduleStmt = $pdo->prepare($scheduleQuery);
    $scheduleStmt->execute([':room_id' => $room_id]);
    $schedule = $scheduleStmt->fetchAll();

    // Combine room details and schedule
    $room['schedule'] = $schedule;

    // Return results as JSON
    header('Content-Type: application/json');
    echo json_encode($room);
} catch (PDOException $e) {
    // Handle errors
    http_response_code(500);
    echo json_encode(["error" => $e->getMessage()]);
}
?>
