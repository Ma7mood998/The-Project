<?php
//Error Reporting
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

session_start();

// Check if user is logged in and has admin privileges
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}
//retrieves first name from the session if it is not set it defaults to Admin
$firstName = $_SESSION['first_name'] ?? 'Admin';

require_once 'db.php'; // Ensure this file establishes a $pdo connection

$message = ''; // Feedback message

// Default values for the form
$room_id = null; // Null indicates a new record
$room_name = '';
$capacity = '';
$equipment = '';
$available = 1; // Default to available
$floor = '';
$department = '';

// Check if updating an existing room
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $room_id = $_GET['id'];

    try {
        // Fetch room details for editing
        $stmt = $pdo->prepare("SELECT * FROM rooms WHERE room_id = :room_id");
        $stmt->bindParam(':room_id', $room_id, PDO::PARAM_INT);
        $stmt->execute();
        $room = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($room) {
            // Pre-fill form with existing room data
            $room_name = $room['room_name'];
            $capacity = $room['capacity'];
            $equipment = $room['equipment'];
            $available = $room['available'];
            $floor = $room['floor'];
            $department = $room['department'];
        } else {
            $message = "Room not found!";
        }
    } catch (PDOException $e) {
        $message = "Error: " . $e->getMessage();
    }
}

// Handle form submission
//Retrieving and Sanitizing Input Data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $room_name = trim($_POST['room_name']); //Removes any whitespace
    $capacity = intval($_POST['capacity']);//Converts the capacity input to an integer to  ensuring that the value is numeric
    $equipment = trim($_POST['equipment']);
    $available = isset($_POST['available']) ? 1 : 0;  //Checks if the 'available' checkbox was checked it sets to 1  otherwise, it sets it to 0 
    $floor = trim($_POST['floor']);
    $department = trim($_POST['department']);

    if (!empty($room_name) && $capacity > 0 && !empty($floor) && !empty($department)) {
        //If all these conditions are met do the followig (edit or add)
        try {
            if ($room_id) {
                // Update the room if ID exists
                $sql = "UPDATE rooms 
                        SET room_name = :room_name, 
                            capacity = :capacity, 
                            equipment = :equipment, 
                            available = :available, 
                            floor = :floor, 
                            department = :department 
                        WHERE room_id = :room_id";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':room_id', $room_id, PDO::PARAM_INT);
            } else {
                // Insert a new room
                $sql = "INSERT INTO rooms (room_name, capacity, equipment, available, floor, department) 
                        VALUES (:room_name, :capacity, :equipment, :available, :floor, :department)";
                $stmt = $pdo->prepare($sql);
            }

            // Bind common parameters
            $stmt->bindParam(':room_name', $room_name);
            $stmt->bindParam(':capacity', $capacity);
            $stmt->bindParam(':equipment', $equipment);
            $stmt->bindParam(':available', $available, PDO::PARAM_INT);
            $stmt->bindParam(':floor', $floor);
            $stmt->bindParam(':department', $department);

            //executes ,Success and Failure Messages
            if ($stmt->execute()) {
                $message = $room_id ? "Room updated successfully!" : "Room added successfully!";
            } else {
                $message = $room_id ? "Failed to update the room." : "Failed to add the room.";
            }
        } catch (PDOException $e) {
            $message = "Error: " . $e->getMessage();
        }
        // input validation fails
    } else {
        $message = "Please fill in all required fields.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $room_id ? "Update Room" : "Add Room"; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
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
        <div class="row">
            <div class="col-md-3"></div>
            <div class="col-md-6 card rounded my-3">
                <h2 class="text-center"><?php echo $room_id ? "Update Room Details" : "Add a New Room"; ?></h2>
                <p class="text-center text-muted"><?php echo $message; ?></p>

                <form method="POST" action="">
                    <div class="mb-3">
                        <label for="room_name" class="form-label">Room Name</label>
                        <input type="text" id="room_name" name="room_name" class="form-control" value="<?php echo htmlspecialchars($room_name); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="capacity" class="form-label">Capacity</label>
                        <input type="number" id="capacity" name="capacity" class="form-control" value="<?php echo htmlspecialchars($capacity); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="equipment" class="form-label">Equipment</label>
                        <input type="text" id="equipment" name="equipment" class="form-control" value="<?php echo htmlspecialchars($equipment); ?>">
                    </div>
                    <div class="mb-3">
                        <label for="available" class="form-label">Availability</label>
                        <select id="available" name="available" class="form-select" required>
                            <option value="1" <?php echo $available == 1 ? "selected" : ""; ?>>Available</option>
                            <option value="0" <?php echo $available == 0 ? "selected" : ""; ?>>Unavailable</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="floor" class="form-label">Floor</label>
                        <input type="text" id="floor" name="floor" class="form-control" value="<?php echo htmlspecialchars($floor); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="department" class="form-label">Department</label>
                        <input type="text" id="department" name="department" class="form-control" value="<?php echo htmlspecialchars($department); ?>" required>
                    </div>
                    <div class="d-flex justify-content-center mb-5">
                        <button type="submit" class="btn btn-primary"><?php echo $room_id ? "Update Room" : "Add Room"; ?></button>
                    </div>
                </form>
            </div>
            <div class="col-md-3"></div>
        </div>
    </div>
</body>

</html>