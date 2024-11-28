<?php
session_start();
require_once 'db.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Get room_id from the query string
$room_id = intval($_GET['room_id'] ?? 0);
if ($room_id === 0) {
    echo "Invalid room ID.";
    exit;
}

// Fetch room details and schedules
$query = "SELECT r.room_name, r.capacity, r.equipment, rs.schedule_id, rs.available_from, rs.available_to, rs.status
          FROM rooms r
          JOIN room_schedules rs ON r.room_id = rs.room_id
          WHERE r.room_id = :room_id";
$stmt = $pdo->prepare($query);
$stmt->execute([':room_id' => $room_id]);
$roomDetails = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($roomDetails)) {
    echo "Room not found or no schedules available.";
    exit;
}

// Handle booking submission
$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $schedule_id = intval($_POST['schedule_id']);

    // Check for conflicts
    $conflictQuery = "SELECT COUNT(*) FROM bookings WHERE room_schedule_id = :schedule_id";
    $conflictStmt = $pdo->prepare($conflictQuery);
    $conflictStmt->execute([':schedule_id' => $schedule_id]);

    if ($conflictStmt->fetchColumn() > 0) {
        $error_message = "This time slot is already booked.";
    } else {
        // Insert the booking
        $insertQuery = "INSERT INTO bookings (user_id, room_schedule_id) VALUES (:user_id, :schedule_id)";
        $insertStmt = $pdo->prepare($insertQuery);
        $insertStmt->execute([
            ':user_id' => $_SESSION['user_id'],
            ':schedule_id' => $schedule_id
        ]);

        $success_message = "Room successfully booked!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Room Details</title>
    <link
  rel="stylesheet"
  href="https://cdn.jsdelivr.net/npm/@picocss/pico@2/css/pico.min.css"
>
</head>
<body>
    <main class="container">
        <h1>Room Details: <?= htmlspecialchars($roomDetails[0]['room_name']) ?></h1>
        <p>Capacity: <?= htmlspecialchars($roomDetails[0]['capacity']) ?></p>
        <p>Equipment: <?= htmlspecialchars($roomDetails[0]['equipment']) ?></p>

        <h2>Available Schedules</h2>

        <!-- Display success or error messages -->
        <?php if ($success_message): ?>
            <div style="color: green;"><?= htmlspecialchars($success_message) ?></div>
        <?php endif; ?>
        <?php if ($error_message): ?>
            <div style="color: red;"><?= htmlspecialchars($error_message) ?></div>
        <?php endif; ?>

        <!-- Booking Form -->
        <form method="POST" action="rooms+.php?room_id=<?= $room_id ?>">
            <?php foreach ($roomDetails as $slot): ?>
                <?php if ($slot['status'] === 'available'): ?>
                    <div>
                        <input type="radio" id="schedule-<?= $slot['schedule_id'] ?>" name="schedule_id" value="<?= $slot['schedule_id'] ?>" required>
                        <label for="schedule-<?= $slot['schedule_id'] ?>">
                            <?= htmlspecialchars($slot['available_from']) ?> to <?= htmlspecialchars($slot['available_to']) ?>
                        </label>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
            <button type="submit">Book Now</button>
        </form>
    </main>
</body>
</html>
