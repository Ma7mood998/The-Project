<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

session_start();

// Check if user is logged in and has admin privileges
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$role = $_SESSION['role'];
$firstName = $_SESSION['first_name'] ?? 'Admin';

require_once 'db.php';

// Fetch all booking made by the logged-in user
$query = "SELECT b.booking_id, r.room_name, rs.available_from, rs.available_to
          FROM bookings b
          JOIN room_schedules rs ON b.room_schedule_id = rs.schedule_id
          JOIN rooms r ON rs.room_id = r.room_id
          WHERE b.user_id = :user_id";
$stmt = $pdo->prepare($query);
$stmt->execute([':user_id' => $_SESSION['user_id']]);
$bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle booking cancellation by deleting the "booking_id" from the bookings table
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $booking_id = intval($_POST['booking_id']);

    $deleteQuery = "DELETE FROM bookings WHERE booking_id = :booking_id";
    $deleteStmt = $pdo->prepare($deleteQuery);
    if ($deleteStmt->execute([':booking_id' => $booking_id])) {
        echo "Booking successfully cancelled.";
    } else {
        echo "Error canceling booking.";
    }
    exit;
}
?>

<!-- HTML declaration -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $room_id ? "Update Room" : "Add Room"; ?></title>
    <!-- Link for icons in nav -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@picocss/pico@2/css/pico.min.css">
    <link rel="stylesheet" href="common.css">
    <link rel="stylesheet" href="MyBooking_style.css">
    <script src="theme.js"></script>
    <script src="my_bookings.js" defer></script>
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
</head>
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

    <main class="container">
        <h2>My Bookings</h2>
        <?php if (empty($bookings)): ?>
            <p>No bookings found.</p>
        <?php else: ?>
            <ul>
                <?php foreach ($bookings as $booking): ?>
                    <article class="mybookings">
                        Room: <?= htmlspecialchars($booking['room_name']) ?><br>
                        Time: <?= htmlspecialchars($booking['available_from']) ?> to <?= htmlspecialchars($booking['available_to']) ?><br>
                        <!-- Button for event listener in my_bookings.js -->
                        <button class="cancel-booking" data-booking-id="<?= $booking['booking_id'] ?>">Cancel</button>
                    </article>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </main>
    <div class="footer">
        <p>&copy; 2024 IT College Room Booking System</p>
    </div>
</body>
</html>
