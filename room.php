<?php
session_start();
require_once 'db.php';  

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$role = $_SESSION['role'];
$firstName = isset($_SESSION['first_name']) ? $_SESSION['first_name'] : 'User';

try {
    // Fetch all rooms from the database using PDO
    $stmt = $pdo->prepare("SELECT * FROM rooms"); 
    $stmt->execute();
    $rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Delete room if 'delete' parameter is set
    if (isset($_GET['delete_id'])) {
        $deleteId = $_GET['delete_id'];

        // Prepare and execute delete query
        $deleteStmt = $pdo->prepare("DELETE FROM rooms WHERE room_id = :room_id");
        $deleteStmt->bindParam(':room_id', $deleteId, PDO::PARAM_INT);
        $deleteStmt->execute();

        // Redirect back to the same page to refresh the room list
        header("Location: room.php");
        exit;
    }
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}

//logout code remove 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Room Management</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

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
        table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 8px 12px;
            text-align: center;
        }
        th {
            background-color: #f4f4f4;
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
</head>
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

    <div class="container mt-4">
        <h2>Room Management</h2>
        <?php if (!empty($rooms)): ?>
            <table class="table">
                <thead>
                    <tr class="text-center">
                        <th>ID</th>
                        <th>Room Name</th>
                        <th>Capacity</th>
                        <th>Department</th>
                        <th>Description</th>
                        <th>Floor</th>
                        <th>Available</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rooms as $room): ?>
                        <tr class="text-center">
                            <td><?php echo $room['room_id']; ?></td>
                            <td><?php echo htmlspecialchars($room['room_name']); ?></td>
                            <td><?php echo htmlspecialchars($room['capacity']); ?></td>
                            <td><?php echo htmlspecialchars($room['department']); ?></td>
                            <td><?php echo htmlspecialchars($room['equipment']); ?></td>
                            <td><?php echo htmlspecialchars($room['floor']); ?></td>
                            <td><?php echo htmlspecialchars($room['available']); ?></td>
                            <td>
                                <a href="room_schedule.php?room_id=<?php echo $room['room_id']; ?>" class="ms-3 btn btn-info">Schedule</a>
                                <a href="add_schedule.php?room_id=<?php echo $room['room_id']; ?>" class=" btn btn-primary">Add Schedule </a> <br>
                                <a href="add_room.php?id=<?php echo $room['room_id']; ?>" class="btn mt-3   btn-warning">Edit</a>
                                <!-- Delete button will pass the room ID via GET parameter 'delete_id' -->
                                <a href="room.php?delete_id=<?php echo $room['room_id']; ?>" class="ms-3 mt-3 btn btn-danger" onclick="return confirm('Are you sure you want to delete this room?');">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No rooms found.</p>
        <?php endif; ?>
    </div>
    <div class="footer">
        <p>&copy; 2024 IT College Room Booking System</p>
    </div>
</body>
</html>
