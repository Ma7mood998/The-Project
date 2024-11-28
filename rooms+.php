<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
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
<link rel="stylesheet" href="styles.css">
<script src="theme.js"></script> 
</head>
<body>
    <header>
        <nav>
        <label for="theme-toggle">
        <input type="checkbox" id="theme-toggle">
        Dark Mode
    </label>
        </nav>
    </header>
    <main class="container">
        <h1>Room Details</h1>
        <section id="room-details">
            <!-- Room details will be dynamically inserted here -->
        </section>
        <section id="room-schedule">
            <h2>Available Schedules</h2>
            <!-- Schedule and booking form will be dynamically inserted here -->
        </section>
    </main>
    <script src="rooms+.js"></script>
</body>
</html>
