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
$schedule_id = 0; // Default: no schedule is being edited
$available_from = '';
$available_to = '';
$status = 'available'; // Default status

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

// Check if editing an existing schedule
if (isset($_GET['edit_id'])) {
    $edit_id = intval($_GET['edit_id']);
    try {
        $stmt = $pdo->prepare("SELECT * FROM room_schedules WHERE schedule_id = :schedule_id");
        $stmt->bindParam(':schedule_id', $edit_id);
        $stmt->execute();
        $schedule = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($schedule) {
            $schedule_id = $schedule['schedule_id'];
            $available_from = $schedule['available_from'];
            $available_to = $schedule['available_to'];
            $status = $schedule['status'];
        } else {
            $message = "Schedule not found.";
        }
    } catch (PDOException $e) {
        $message = "Error fetching schedule for edit: " . $e->getMessage();
    }
}

// Handle form submission for add/update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $available_from = trim($_POST['available_from']);
    $available_to = trim($_POST['available_to']);
    $status = trim($_POST['status']);
    $schedule_id = isset($_POST['schedule_id']) ? intval($_POST['schedule_id']) : 0;

    if (!empty($available_from) && !empty($available_to) && !empty($status)) {
        try {
            if ($schedule_id > 0) {
                // Update existing schedule
                $sql = "UPDATE room_schedules 
                        SET available_from = :available_from, 
                            available_to = :available_to, 
                            status = :status 
                        WHERE schedule_id = :schedule_id";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':schedule_id', $schedule_id);
            } else {
                // Add new schedule
                $sql = "INSERT INTO room_schedules (room_id, available_from, available_to, status) 
                        VALUES (:room_id, :available_from, :available_to, :status)";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':room_id', $room_id);
            }
            $stmt->bindParam(':available_from', $available_from);
            $stmt->bindParam(':available_to', $available_to);
            $stmt->bindParam(':status', $status);

            if ($stmt->execute()) {
                $message = $schedule_id > 0 ? "Schedule updated successfully!" : "Schedule added successfully!";
            } else {
                $message = $schedule_id > 0 ? "Failed to update schedule." : "Failed to add schedule.";
            }
        } catch (PDOException $e) {
            $message = "Error saving schedule: " . $e->getMessage();
        }
    } else {
        $message = "Please fill in all fields.";
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
        /* Logo Styling */
        .logo-img {
            height: 60px;
            width: 70px;
            border-radius: 50%;
            border: 2px solid white;
            cursor: pointer;
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
    <h2>Room Schedule Manager</h2>
    <?php if (!empty($message)): ?>
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($message); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- Add/Update Form -->
    <form method="POST" class="mb-4">
        <input type="hidden" name="schedule_id" value="<?php echo $schedule_id; ?>">
        <div class="mb-3">
            <label for="available_from" class="form-label">Available From</label>
            <input type="datetime-local" id="available_from" name="available_from" class="form-control" value="<?php echo htmlspecialchars($available_from); ?>" required>
        </div>
        <div class="mb-3">
            <label for="available_to" class="form-label">Available To</label>
            <input type="datetime-local" id="available_to" name="available_to" class="form-control" value="<?php echo htmlspecialchars($available_to); ?>" required>
        </div>
        <div class="mb-3">
            <label for="status" class="form-label">Status</label>
            <select id="status" name="status" class="form-select" required>
                <option value="available" <?php echo $status === 'available' ? 'selected' : ''; ?>>Available</option>
                <option value="unavailable" <?php echo $status === 'unavailable' ? 'selected' : ''; ?>>Unavailable</option>
            </select>
        </div>
        <div class="d-flex justify-content-center algin-center">
            <button type="submit" class="btn btn-primary"><?php echo $schedule_id > 0 ? "Update Schedule" : "Add Schedule"; ?></button>
        </div>
    </form>
</div>
<div class="footer">
        <p>&copy; 2024 IT College Room Booking System</p>
    </div>
</body>
</html>
