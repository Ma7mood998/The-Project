<?php
session_start();
require_once 'db.php';

// Redirect if the user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Get user information from the session we opened in login
$user_role = $_SESSION['role']; // 'user' or 'admin'
$username = $_SESSION['first_name']; // For personalized messages
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Available Rooms</title>
    <link
  rel="stylesheet"
  href="https://cdn.jsdelivr.net/npm/@picocss/pico@2/css/pico.min.css"
>
</head>
<body>
    <header>
        <!-- Personal welcome message for each user -->
    <h1>Welcome, <?= htmlspecialchars($_SESSION['first_name']); ?>!</h1>
    <nav>
        <!-- link to my_bookings.php to see all bookings the current user has booked -->
        <a href="my_bookings.php">My Bookings</a>
        <a href="logout.php" class="button">Logout</a>
    </nav>
    </header>
    <main class="container">
        <h1>Available Rooms</h1>
        <section id="rooms-container" class="flex">
            <!-- Room cards will be dynamically inserted here by javascript (rooms.js) -->
        </section>
    </main>

    <!-- Link to external JavaScript (rooms.js) for the id="room-container" -->
    <script src="rooms.js"></script>
</body>
</html>