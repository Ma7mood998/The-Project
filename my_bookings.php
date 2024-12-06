<?php
session_start();
require_once 'db.php';

// Redirect to login page if the user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Fetch all booking made by the logged-in user
$query = "SELECT b.booking_id, r.room_name, rs.available_from, rs.available_to
          FROM bookings b
          JOIN room_schedules rs ON b.room_schedule_id = rs.schedule_id
          JOIN rooms r ON rs.room_id = r.room_id
          WHERE b.user_id = :user_id";
$stmt = $pdo->prepare($query);
$stmt->execute([':user_id' => $_SESSION['user_id']]);
$bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle booking cancellation by deleting the "booking_id" from the bookings table
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $booking_id = intval($_POST['booking_id']);

    $deleteQuery = "DELETE FROM bookings WHERE booking_id = :booking_id";
    $deleteStmt = $pdo->prepare($deleteQuery);
    if ($deleteStmt->execute([':booking_id' => $booking_id])) {
        echo "Booking successfully cancelled.";
    } else {
        echo "Error canceling booking.";
    }
    exit;
}
?>

<!-- HTML declaration -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Bookings</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@picocss/pico@2/css/pico.min.css">
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="MyBooking_style.css">
    <script src="theme.js"></script>
    <script src="my_bookings.js" defer></script>
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
                        Room: <?= htmlspecialchars($booking['room_name']) ?><br>
                        Time: <?= htmlspecialchars($booking['available_from']) ?> to <?= htmlspecialchars($booking['available_to']) ?><br>
                        <!-- Button for event listener in my_bookings.js -->
                        <button class="cancel-booking" data-booking-id="<?= $booking['booking_id'] ?>">Cancel</button>
                    </article>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </main>
    <!-- Footer with contact info -->
    <footer id="contact">
            <p><strong>Follow us on:</strong></p>
            <ul>
                <li><a href="#"><i class="fab fa-facebook-f"></i>Facebook</a></li>
                <li><a href="#"><i class="fab fa-twitter"></i>Twitter</a></li>
                <li><a href="#"><i class="fab fa-instagram"></i>Instagram</a></li>
            </ul>
            <p><strong>Contact: info@ITBookings.com</strong></p>
    </footer>
</body>
</html>
