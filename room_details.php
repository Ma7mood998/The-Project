<?php
require_once 'db.php';

$room_id = intval($_GET['room_id'] ?? 0);
if ($room_id === 0) {
    echo json_encode(["error" => "Invalid room ID."]);
    exit;
}

// Fetch room details
$query = "SELECT r.room_name, r.capacity, r.equipment, rs.schedule_id, rs.available_from, rs.available_to, rs.status
          FROM rooms r
          JOIN room_schedules rs ON r.room_id = rs.room_id
          WHERE r.room_id = :room_id";
$stmt = $pdo->prepare($query);
$stmt->execute([':room_id' => $room_id]);
$roomDetails = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($roomDetails)) {
    echo json_encode(["error" => "Room not found or no available schedules."]);
    exit;
}

// Prepare response data
$response = [
    'room_name' => $roomDetails[0]['room_name'],
    'capacity' => $roomDetails[0]['capacity'],
    'equipment' => $roomDetails[0]['equipment'],
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
?>
