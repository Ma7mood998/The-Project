<?php
require_once 'db.php';

if (isset($_GET['user_id'])) {
    $user_id = intval($_GET['user_id']);

    // Fetch the user's bookings
    $query = "SELECT b.booking_id, r.room_name, rs.available_from, rs.available_to
              FROM bookings b
              JOIN room_schedules rs ON b.room_schedule_id = rs.schedule_id
              JOIN rooms r ON rs.room_id = r.room_id
              WHERE b.user_id = :user_id";
    $stmt = $pdo->prepare($query);
    $stmt->execute([':user_id' => $user_id]);
    $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($bookings);
} else {
    echo json_encode(["error" => "User ID not provided"]);
}
?>
