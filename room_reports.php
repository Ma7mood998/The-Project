<?php
session_start();
require_once 'db.php'; 


if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$role = $_SESSION['role'];
$firstName = isset($_SESSION['first_name']) ? $_SESSION['first_name'] : 'User';

// Fetch room schedule statistics
$scheduleData = [];
try {
    // Fetch all rooms with their schedule counts (if any)
    $stmt = $pdo->prepare("
        SELECT rooms.room_name, 
               COALESCE(SUM(CASE WHEN room_schedules.status = 'available' THEN 1 ELSE 0 END), 0) AS available_count
        FROM rooms
        LEFT JOIN room_schedules ON rooms.room_id = room_schedules.room_id
        GROUP BY rooms.room_name
    ");
    $stmt->execute();
    $scheduleData = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching data: " . $e->getMessage());
}

// Convert data for JavaScript
$roomNames = [];
$availableCounts = [];
$emptyRoomCounts = [];

foreach ($scheduleData as $data) {
    $roomNames[] = $data['room_name'];
    if ($data['available_count'] > 0) {
        $availableCounts[] = $data['available_count'];
        $emptyRoomCounts[] = 0; // No empty representation for rooms with schedules
    } else {
        $availableCounts[] = 0; // No available schedules
        $emptyRoomCounts[] = 1; // Represent empty room
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporting and Analytics</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
        /* Logo Styling */
        .logo-img {
            height: 50px;
            width: 50px;
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
<!-- Nav Bar -->
<nav >
        <div class="nav-links">
        <a href="admin.php" style="float: left;">
            <img src="uploads/rbook.jpg" alt="Site Logo" class="logo-img">
        </a>
            <label for="theme-toggle">
                <input type="checkbox" id="theme-toggle">
                Dark Mode
            </label>
            <a href="my_bookings.php">My Bookings</a>
            <?php if ($role === 'admin'): ?>
            <a href="add_room.php">Add Room</a>
            <a href="room.php">Room Management</a>
            <a href="room_reports.php">Room Reports</a>
            <a href="mothly_report.php">Monthly Room Reports</a>
            <?php endif; ?>
        </div>
        <div class="dropdown">
            <button><?php echo htmlspecialchars($firstName); ?> ▼</button>
            <div class="dropdown-content">
                <a href="profile.php">View Profile</a>
                <a href="logout.php">Logout</a>
            </div>
        </div>
    </nav>
<div class="container my-5 ">
    <h2>Reporting and Analytics</h2>
    <p>Available and Empty Rooms</p>

    <!-- Chart Container -->
    <div class="card mt-4 ">
        <div class="card-body">
            <h5 class="card-title">Room Schedule Statistics</h5>
            <canvas id="scheduleChart" width="500" height="200"></canvas>
        </div>
    </div>
</div>

<script>
    // Pass PHP data to JavaScript
    const roomNames = <?php echo json_encode($roomNames); ?>;
    const availableCounts = <?php echo json_encode($availableCounts); ?>;
    const emptyRoomCounts = <?php echo json_encode($emptyRoomCounts); ?>;

    // Chart.js Configuration
    const ctx = document.getElementById('scheduleChart').getContext('2d');
    const scheduleChart = new Chart(ctx, {
        type: 'bar', // Chart type: bar
        data: {
            labels: roomNames,
            datasets: [
                {
                    label: 'Available Schedules',
                    data: availableCounts,
                    backgroundColor: 'rgba(54, 162, 235, 1)', // Full Blue color
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 2
                },
                {
                    label: 'Empty Rooms',
                    data: emptyRoomCounts,
                    backgroundColor: 'rgba(255, 159, 64, 1)', // Orange color
                    borderColor: 'rgba(255, 159, 64, 1)',
                    borderWidth: 2
                }
            ]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                }
            },
            scales: {
                y: {
                    beginAtZero: true, // Start Y-axis at zero
                    max: 12, // Fixed maximum value for Y-axis
                    ticks: {
                        stepSize: 1, // Increment by 1
                        callback: function(value) {
                            if (Number.isInteger(value)) {
                                return value; // Display only integers
                            }
                            return null; // Skip non-integer values
                        }
                    }
                }
            }
        }
    });
</script>
<div class="footer">
        <p>&copy; 2024 IT College Room Booking System</p>
    </div>

</body>
</html>



