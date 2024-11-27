<?php
session_start();
require_once 'db.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Get user information from the session
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
        <h1>Welcome, <?= htmlspecialchars($username); ?>!</h1>
        <nav>
            <a href="logout.php" class="button">Logout</a>
        </nav>
    </header>
    <main class="container">
        <h1>Available Rooms</h1>
        <section id="rooms-container" class="flex">
            <!-- Room cards will be dynamically inserted here -->
        </section>
    </main>

    <!-- Link to external JavaScript -->
    <script src="rooms.js"></script>
</body>
</html>