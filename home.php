<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$role = $_SESSION['role'];

$firstName = isset($_SESSION['first_name']) ? $_SESSION['first_name'] : 'User';

if (!isset($_SESSION['welcome_shown'])) {
    $_SESSION['welcome_shown'] = true; // Set the flag that the modal has been shown
    $showWelcomeModal = true;
} else {
    $showWelcomeModal = false;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        nav {
            background-color: #333;
            color: white;
            padding: 10px 20px;
        }
        .nav-links a {
            color: white;
            text-decoration: none;
            padding: 10px 15px;
            font-size: 18px;
        }
        .nav-links a:hover {
            background-color: #575757;
            border-radius: 5px;
        }
        .dropdown-content {
            display: none;
            position: absolute;
            right: 0;
            background-color: #333;
            box-shadow: 0px 8px 16px rgba(0, 0, 0, 0.2);
            z-index: 1;
        }
        .dropdown-content a {
            color: white;
            text-decoration: none;
            padding: 10px 15px;
            display: block;
        }
        .dropdown-content a:hover {
            background-color: #575757;
        }
        .dropdown:hover .dropdown-content {
            display: block;
        }
    </style>
</head>
<body>
    <!-- Nav Bar -->
    <nav class="d-flex justify-content-between align-items-center">
        <!-- Left-aligned links -->
        <div class="nav-links d-flex">
            <a href="home.php">Home</a>
            <a href="user_room.php">Room Schedule</a>
        </div>
        <!-- Right-aligned dropdown -->
        <div class="dropdown">
            <button class="btn btn-link text-white text-decoration-none">
                <?php echo htmlspecialchars($firstName); ?> ▼
            </button>
            <div class="dropdown-content">
                <a href="profile.php">View Profile</a>
                <a href="logout.php">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <h2>User Dashboard</h2>
        <p>This is the user home page.</p>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
