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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@picocss/pico@2/css/pico.min.css">
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="custom-styles.css">
    <script src="theme.js"></script>
    <script src="rooms.js"></script>
</head>
<body>
    <header class="container">
        <!-- Personal welcome message for each user -->
    <h1>Welcome, <?= htmlspecialchars($_SESSION['first_name']); ?>!</h1>
    <nav>
        <label for="theme-toggle">
            <input type="checkbox" id="theme-toggle">
            Dark Mode
        </label>
        <ul>
            <li><a href="rooms.php">Home</a></li>
            <li><a href="rooms.php#filters">Browse Rooms</a></li>
            <li><a href="profile.php">Profile</a></li>
            <!-- link to my_bookings.php to see all bookings the current user has booked -->
            <li><a href="my_bookings.php">My Bookings</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>
    </header>
    <main class="container">
        <h1>Browse Rooms</h1>
        <section id="rooms-container" class="flex">
            <!-- Room cards will be dynamically inserted here by javascript (rooms.js) -->
        </section>
    </main>
    <footer class="container">
        <p>&copy; <?= date("Y"); ?> Room Booking System</p>
    </footer>
</body>
</html>

