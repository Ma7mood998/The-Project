<?php
session_start();
require_once 'db.php';; // Include your PDO database connection file

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$role = $_SESSION['role'];
$firstName = isset($_SESSION['first_name']) ? $_SESSION['first_name'] : 'User';

try {
    // Fetch all rooms from the database using PDO
    $stmt = $pdo->prepare("SELECT * FROM rooms"); // Replace `rooms` with your table name
    $stmt->execute();
    $rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Delete room if 'delete' parameter is set
    if (isset($_GET['delete_id'])) {
        $deleteId = $_GET['delete_id'];

        // Prepare and execute delete query
        $deleteStmt = $pdo->prepare("DELETE FROM rooms WHERE id = :id");
        $deleteStmt->bindParam(':id', $deleteId, PDO::PARAM_INT);
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
            justify-content: space-between;
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
    </style>
</head>
<body>
    <?php
   require_once('navbar.php');
   ?>

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
                                <a href="room_schedule.php?room_id=<?php echo $room['room_id']; ?>" class="me-3 btn btn-info">Schedule</a>
                                <a href="add_room.php?id=<?php echo $room['room_id']; ?>" class="btn btn-primary">Edit</a>
                                <!-- Delete button will pass the room ID via GET parameter 'delete_id' -->
                                <a href="room.php?delete_id=<?php echo $room['room_id']; ?>" class="ms-3 btn btn-danger" onclick="return confirm('Are you sure you want to delete this room?');">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No rooms found.</p>
        <?php endif; ?>
    </div>
</body>
</html>
