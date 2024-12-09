<?php
session_start();
require_once 'db.php'; 

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$role = $_SESSION['role'];
$firstName = isset($_SESSION['first_name']) ? $_SESSION['first_name'] : 'User';

$message = ''; // Feedback message

// Fetch room schedules
$room_id = isset($_GET['room_id']) ? intval($_GET['room_id']) : 0;
$schedules = [];

if ($room_id > 0) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM room_schedules WHERE room_id = :room_id ORDER BY available_from");
        $stmt->bindParam(':room_id', $room_id);
        $stmt->execute();
        $schedules = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $message = "Error fetching schedules: " . $e->getMessage();
    }
}

// Handle delete schedule request
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    try {
        $stmt = $pdo->prepare("DELETE FROM room_schedules WHERE schedule_id = :schedule_id");
        $stmt->bindParam(':schedule_id', $delete_id);
        if ($stmt->execute()) {
            $message = "Schedule deleted successfully!";
        } else {
            $message = "Failed to delete schedule.";
        }
    } catch (PDOException $e) {
        $message = "Error deleting schedule: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Room Schedule Viewer</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<style>
        nav {
            background-color: #333;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between; /* Distribute links and dropdown */
            align-items: center;
        }
        .nav-links {
            display: flex;
            gap: 20px; /* Space between links */
        }
        .nav-links a {
            color: white;
            text-decoration: none;
            padding: 10px 15px;
            font-size: 18px;
        }
        .nav-links a:hover {
            background-color: #575757;
            border-radius: 4px;
        }
        .dropdown {
            position: relative;
        }
        .dropdown button {
            background-color: transparent;
            color: white;
            border: none;
            font-size: 18px;
            padding: 10px 15px;
            cursor: pointer;
        }
        .dropdown button:hover {
            background-color: #575757;
            border-radius: 4px;
        }
        .dropdown-content {
            display: none;
            position: absolute;
            right: 0;
            background-color: #333;
            box-shadow: 0px 8px 16px rgba(0, 0, 0, 0.2);
            z-index: 1;
        }
        .dropdown-content a,
        .dropdown-content form button {
            color: white;
            padding: 10px 15px;
            text-decoration: none;
            display: block;
            background-color: transparent;
            border: none;
            text-align: left;
            cursor: pointer;
        }
        .dropdown-content a:hover,
        .dropdown-content form button:hover {
            background-color: #575757;
        }
        .dropdown:hover .dropdown-content {
            display: block;
        }
        .footer {
            background-color: black;
            color: white;
            text-align: center;
            padding: 10px;
            margin-top: 30px;
        }

        .footer p {
            text-align: center;
            color: white;
            font-size: 14px;
        }
    </style>
<body>
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
    <h2>Room Schedule Viewer</h2>
    <?php if (!empty($message)): ?>
        <div class="alert alert-info"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <!-- Schedule Table -->
    <table class="table table-bordered">
        <thead>
        <tr>
            <th>ID</th>
            <th>Available From</th>
            <th>Available To</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($schedules as $schedule): ?>
            <tr>
                <td><?php echo htmlspecialchars($schedule['schedule_id']); ?></td>
                <td><?php echo htmlspecialchars($schedule['available_from']); ?></td>
                <td><?php echo htmlspecialchars($schedule['available_to']); ?></td>
                <td><?php echo htmlspecialchars($schedule['status']); ?></td>
                <td>
                    <a href="?room_id=<?php echo $room_id; ?>" class="btn btn-sm btn-info" >Booking</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<div class="footer">
        <p>&copy; 2024 IT College Room Booking System</p>
    </div>
</body>
</html>
