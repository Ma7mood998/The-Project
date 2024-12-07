<?php
session_start();
// Redirect to login page if the user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
?>

<!-- HTML declaration -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Room Details</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@picocss/pico@2/css/pico.min.css">
    <link rel="stylesheet" href="common.css">
    <link rel="stylesheet" href="rooms+_style.css">
    <script src="theme.js"></script>
    <script src="rooms+.js"></script>
</head>
<body>

    <header>
        <nav>
            <label for="theme-toggle">
            <input type="checkbox" id="theme-toggle">
            Dark Mode
            </label>
            <ul>
                <li><a href="rooms.php">Back to Rooms</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>

    <main class="container">
        <h1>Room Details</h1>

        <!-- Room details section to add room details from rooms+.js "JavaScript" -->
        <section id="room-details">
            <!-- Room details will be dynamically inserted here -->
        </section>

        <!-- Room schedule section to add room schedules from rooms+.js "JavaScript" -->
        <section id="room-schedule">
            <h2>Available Schedules</h2>
            <!-- Schedule and booking form will be dynamically inserted here -->
        </section>
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
