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
    <!-- Link for icons in nav -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<style>
        nav {
            height: 90px;
            padding: 20px;
            background-color: #708090;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between; /* Distribute links and dropdown */
            align-items: center;
        }
        .checkbox-container {
            display: flex;
            align-items: center;
            padding: 5px;
            color: white;
        }
        .nav-links {
            display: flex;
            align-items: center;
            gap: 20px; /* Space between links */
        }
        /* Logo Styling */
        .logo-img {
            height: 60px;
            width: 70px;
            border-radius: 50%;
            border: 2px solid white;
            cursor: pointer;
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
            background-color: #708090;
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
<!-- Nav Bar -->
<nav>
        <div class="nav-links">
        <a href="admin.php" style="float: left;">
            <img src="uploads/rbook.jpg" alt="Site Logo" class="logo-img">
        </a>
        <div class="checkbox-container">
                <input type="checkbox" id="theme-toggle">
                <label for="theme-toggle" class="fas fa-moon" id="darkmode">Dark Mode</label>
            </div>
            <a href="my_bookings.php" class="fas fa-calendar-check">My Bookings</a>
            <?php if ($role === 'admin'): ?>
            <a href="add_room.php" class="fas fa-plus-square">Add Room</a>
            <a href="room.php" class="fas fa-cogs">Room Management</a>
            <a href="room_reports.php" class="fas fa-chart-bar">Room Reports</a>
            <a href="mothly_report.php" class="fas fa-calender-alt">Monthly Room Reports</a>
            <?php endif; ?>
        </div>
        <div class="dropdown">
            <button class="fas fa-user"><?php echo htmlspecialchars($firstName); ?> ▼</button>
            <div class="dropdown-content">
                <a href="profile.php" class="fas fa-user-circle">View Profile</a>
                <a href="logout.php" class="fas fa-sign-out-alt">Logout</a>
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
