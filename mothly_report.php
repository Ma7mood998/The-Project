<?php
session_start();
require_once 'db.php'; 
//checks if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$role = $_SESSION['role'];
$firstName = isset($_SESSION['first_name']) ? $_SESSION['first_name'] : 'User';

// Fetch total schedule data for each month
$monthlyData = [];
try {
    // Query to get total schedule counts per month
    $stmt = $pdo->prepare("
        SELECT 
            MONTHNAME(room_schedules.available_from) AS month,
            COUNT(*) AS total_schedules
        FROM room_schedules
        WHERE room_schedules.status = 'available'
        GROUP BY MONTH(room_schedules.available_from)
        ORDER BY MONTH(room_schedules.available_from)
    ");
    $stmt->execute();
    $monthlyData = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching data: " . $e->getMessage());
}

// Convert data for JavaScript
$allMonths = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
$months = [];//array  to store the names of the months that have schedule data
$totalSchedules = array_fill(0, 12, 0);// Initialize all months with 0,with the index corresponding to the month 

// Fill data with actual values
//contains the actual schedule counts for specific months 
foreach ($monthlyData as $data) {
    $monthIndex = array_search($data['month'], $allMonths); // this function is  to find the index of the month name in the $allMonths array
    //checks if the month was found in $allMonths array
    if ($monthIndex !== false) {
        $months[] = $data['month']; // add  Month name to $months
        $totalSchedules[$monthIndex] = $data['total_schedules']; // Total schedules for that month
    }
}

// Fill in missing months for display
$months = $allMonths; // Show all months on the x-axis of a chart  even if some months have zero schedules
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monthly Total Schedules</title>
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
    /* Logo Styling */
    .logo-img {
            height: 50px;
            width: 50px;
            border-radius: 50%;
            border: 2px solid white;
            cursor: pointer;
        }
    .nav-links {
        display: flex;
        gap: 20px;
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
<!-- Nav Bar -->
<nav >
        <div class="nav-links">
        <a href="welcome.php" style="float: left;">
            <img src="uploads/rbook.jpg" alt="Site Logo" class="logo-img">
        </a>
            <label for="theme-toggle">
                <input type="checkbox" id="theme-toggle">
                Dark Mode
            </label>
            <a href="admin.php">Home</a>
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
<div class="container my-5">
    <h2>Total Schedules per Month</h2>
    <p>Empty bars represent months with no data..</p>

    <!-- Chart Container -->
    <div class="card mt-4">
        <div class="card-body">
            <h5 class="card-title">Monthly Total Schedules</h5>
            <canvas id="monthlyScheduleChart" width="500" height="200"></canvas>
        </div>
    </div>
</div>

<script>
    // Pass PHP data to JavaScript 
    //using json_encode() function to convert the PHP arrays $months and $totalSchedules into JSON format

    const months = <?php echo json_encode($months); ?>;//contain an array of month names
    const totalSchedules = <?php echo json_encode($totalSchedules); ?>;//contain an array of total schedules to each month 


    // Calculate the maximum schedule count to set as the Y-axis max
    const maxSchedule = Math.max(...totalSchedules); // (...)Find the highest value in the data
    const yAxisMax = maxSchedule + (maxSchedule % 10 === 0 ? 10 : 5); // Add buffer: Add 10 if the max value is divisible by 10, else 5

    // Chart.js Configuration
    const ctx = document.getElementById('monthlyScheduleChart').getContext('2d');
    const monthlyScheduleChart = new Chart(ctx, {
        type: 'bar', // Chart type: bar
        data: {
            labels: months,
            datasets: [
                {
                    label: 'Total Schedules',
                    data: totalSchedules,
                    backgroundColor: 'rgba(75, 192, 192, 0.8)', // Teal color
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
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
                    max: yAxisMax, // Dynamically set max value based on data
                    ticks: {
                        stepSize: Math.ceil(yAxisMax / 10), // Set step size for ticks based on max value
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
