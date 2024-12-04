<?php
session_start();
require_once 'db.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Fetch user's bookings
$query = "SELECT b.booking_id, r.room_name, rs.available_from, rs.available_to
          FROM bookings b
          JOIN room_schedules rs ON b.room_schedule_id = rs.schedule_id
          JOIN rooms r ON rs.room_id = r.room_id
          WHERE b.user_id = :user_id";
$stmt = $pdo->prepare($query);
$stmt->execute([':user_id' => $_SESSION['user_id']]);
$bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle booking cancellation
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $booking_id = intval($_POST['booking_id']);

    $deleteQuery = "DELETE FROM bookings WHERE booking_id = :booking_id";
    $deleteStmt = $pdo->prepare($deleteQuery);
    $deleteStmt->execute([':booking_id' => $booking_id]);

    echo "<p style='color:green;'>Booking successfully cancelled.</p>";
    // Refresh bookings after deletion
    header("Location: my_bookings.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Bookings</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@picocss/pico@2/css/pico.min.css">
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="MyBooking_style.css">
    <script src="theme.js"></script>
</head>
<body>
<header class="container">
    <nav>
        <label for="theme-toggle">
        <input type="checkbox" id="theme-toggle">
        Dark Mode
        </label>
        <ul>
            <li><a href="rooms.php">Home</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>
    </header>
    <main class="container">
        <h2>My Bookings</h2>
        <?php if (empty($bookings)): ?>
            <p>No bookings found.</p>
        <?php else: ?>
            
            <ul>
            
                <?php foreach ($bookings as $booking): ?>
                    <article class="mybookings">
            
                        Room: <?= htmlspecialchars($booking['room_name']) ?>
                        Time: <?= htmlspecialchars($booking['available_from']) ?> to <?= htmlspecialchars($booking['available_to']) ?>
                        <form method="POST" action="my_bookings.php" style="display:inline;">
                            <input type="hidden" name="booking_id" value="<?= $booking['booking_id'] ?>">
                            <button type="submit">Cancel</button>
                        </form>
                    
                    </article>
                <?php endforeach; ?>
                
            </ul>
                
        <?php endif; ?>
    </main>
    <footer class="container">
        <p>&copy; <?= date("Y"); ?> Room Booking System</p>
    </footer>
</body>
</html>
