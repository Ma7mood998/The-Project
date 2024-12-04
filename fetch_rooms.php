<?php
require_once 'db.php';

// Initialize query
$query = "SELECT rooms.room_id, rooms.room_name, rooms.capacity, 
                 rooms.equipment, rooms.available, rooms.floor, rooms.department,
                 (CASE 
                    WHEN rooms.available = 1 THEN 'available'
                    ELSE 'unavailable' 
                 END) AS status
          FROM rooms";

// Add filters dynamically
$filters = [];
$params = []; // Array to hold parameters for prepared statements

// Filter by availability
if (isset($_GET['availability']) && $_GET['availability'] !== 'all') {
    $availability = $_GET['availability'];
    if ($availability === 'available') {
        $filters[] = "rooms.available = 1";
    } elseif ($availability === 'unavailable') {
        $filters[] = "rooms.available = 0";
    }
}

// Filter by capacity
if (isset($_GET['capacity']) && $_GET['capacity'] === 'gt30') {
    $filters[] = "rooms.capacity > 30";
}

// Filter by floor
if (isset($_GET['floor']) && $_GET['floor'] !== 'all') {
    $filters[] = "rooms.floor = :floor";
    $params[':floor'] = $_GET['floor'];
}

// Filter by department
if (isset($_GET['department']) && $_GET['department'] !== 'all') {
    $filters[] = "rooms.department = :department";
    $params[':department'] = $_GET['department'];
}

// Filter by room name (partial match)
if (isset($_GET['room_name']) && !empty(trim($_GET['room_name']))) {
    $room_name = "%" . trim($_GET['room_name']) . "%";
    $filters[] = "rooms.room_name LIKE :room_name";
    $params[':room_name'] = $room_name;
}

// Append filters to query if any
if (!empty($filters)) {
    $query .= " WHERE " . implode(' AND ', $filters);
}

try {
    // Prepare query
    $stmt = $pdo->prepare($query);

    // Bind parameters dynamically
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }

    // Execute query
    $stmt->execute();
    $rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Return rooms as JSON
    echo json_encode($rooms);
} catch (PDOException $e) {
    // Handle query errors
    echo json_encode(['error' => $e->getMessage()]);
}
?>