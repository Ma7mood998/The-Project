<?php
session_start();
require_once 'db.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(["error" => "Unauthorized access. Please log in."]);
    exit;
}

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['room_id'])) {
    $room_id = intval($_GET['room_id']);
    if ($room_id === 0) {
        echo json_encode(["error" => "Invalid room ID."]);
        exit;
    }

    // Fetch room details and schedules
    $query = "SELECT r.room_name, r.capacity, r.equipment, r.floor, r.department, rs.schedule_id, rs.available_from, rs.available_to, rs.status
              FROM rooms r
              JOIN room_schedules rs ON r.room_id = rs.room_id
              WHERE r.room_id = :room_id";
    $stmt = $pdo->prepare($query);
    $stmt->execute([':room_id' => $room_id]);
    $roomDetails = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($roomDetails)) {
        echo json_encode(["error" => "Room not found or no schedules available."]);
        exit;
    }

    // Prepare response
    $response = [
        'room_name' => $roomDetails[0]['room_name'],
        'capacity' => $roomDetails[0]['capacity'],
        'equipment' => $roomDetails[0]['equipment'],
        'floor' => $roomDetails[0]['floor'],
        'department' => $roomDetails[0]['department'],
        'schedule' => []
    ];

    foreach ($roomDetails as $slot) {
        $response['schedule'][] = [
            'schedule_id' => $slot['schedule_id'],
            'available_from' => $slot['available_from'],
            'available_to' => $slot['available_to'],
            'status' => $slot['status']
        ];
    }

    echo json_encode($response);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['schedule_id'])) {
    $schedule_id = intval($_POST['schedule_id']);

    // Check for conflicts
    $conflictQuery = "SELECT COUNT(*) FROM bookings WHERE room_schedule_id = :schedule_id";
    $conflictStmt = $pdo->prepare($conflictQuery);
    $conflictStmt->execute([':schedule_id' => $schedule_id]);

    if ($conflictStmt->fetchColumn() > 0) {
        echo json_encode(["error" => "This time slot is already booked."]);
    } else {
        // Insert the booking
        $insertQuery = "INSERT INTO bookings (user_id, room_schedule_id) VALUES (:user_id, :schedule_id)";
        $insertStmt = $pdo->prepare($insertQuery);
        $insertStmt->execute([
            ':user_id' => $_SESSION['user_id'],
            ':schedule_id' => $schedule_id
        ]);

        echo json_encode(["success" => "Room successfully booked!"]);
    }
    exit;
}

http_response_code(400);
echo json_encode(["error" => "Invalid request."]);
?>

