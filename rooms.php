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
    <link rel="stylesheet" href="rooms_styles.css">
    <script src="theme.js"></script>
    <script src="rooms.js" defer></script>
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

        <!-- Dropdown Menu -->
        <form id="filter-form">
            <label for="filter-availability">Availability:</label>
            <select id="filter-availability" name="availability">
                <option value="all">All</option>
                <option value="available">Available</option>
                <option value="unavailable">Unavailable</option>
            </select>

            <label for="filter-capacity">Capacity:</label>
            <select id="filter-capacity" name="capacity">
                <option value="all">All</option>
                <option value="gt30">Capacity > 30</option>
            </select>

            <label for="filter-floor">Floor:</label>
            <select id="filter-floor" name="floor">
                <option value="all">All</option>
                <option value="0">0</option>
                <option value="1">1</option>
                <option value="2">2</option>
            </select>

            <label for="filter-department">Department:</label>
            <select id="filter-department" name="department">
                <option value="all">All</option>
                <option value="information systems">Information Systems</option>
                <option value="computer science">Computer Science</option>
                <option value="computer engineering">Computer Engineering</option>
            </select>
            <!-- Search Input -->
        <label for="search-room">Search Room:</label>
        <input type="text" id="search-room" name="room_name" placeholder="Enter room name" />
            <button type="submit">Apply Filters</button>
            <button type="button" id="reset-filters" class="secondary">Reset Filters</button>
        </form>

        <!-- Rooms Container -->
        <section id="rooms-container" class="flex">
            <!-- Room cards will be dynamically inserted here -->
        </section>
    </main>
    <footer class="container">
        <p>&copy; <?= date("Y"); ?> Room Booking System</p>
    </footer>
</body>
</html>

