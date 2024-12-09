<?php
require_once 'db.php';

// Initialize A base SQL query to fetch room details
// The CASE statement is to compute the status column "available or unavailable"
$query = "SELECT rooms.room_id, rooms.room_name, rooms.capacity, 
                 rooms.equipment, rooms.available, rooms.floor, rooms.department,
                 (CASE 
                    WHEN rooms.available = 1 THEN 'available'
                    ELSE 'unavailable' 
                 END) AS status
          FROM rooms";


$filters = []; // // An array to store filters dynamically "SQL conditions"
$params = []; // An associative array to store named parameters for prepared statements.

// Filter by availability
if (isset($_GET['availability']) && $_GET['availability'] !== 'all') {
    $availability = $_GET['availability'];
    if ($availability === 'available') {
        $filters[] = "rooms.available = 1";
    } elseif ($availability === 'unavailable') {
        $filters[] = "rooms.available = 0";
    }
}

// Filter by capacity greater than 30
if (isset($_GET['capacity']) && $_GET['capacity'] === 'gt30') {
    $filters[] = "rooms.capacity > 30";
}

// Filter by floor "0,1,2"
if (isset($_GET['floor']) && $_GET['floor'] !== 'all') {
    $filters[] = "rooms.floor = :floor";
    $params[':floor'] = $_GET['floor'];
}

// Filter by department "IS,CS,CE"
if (isset($_GET['department']) && $_GET['department'] !== 'all') {
    $filters[] = "rooms.department = :department";
    $params[':department'] = $_GET['department'];
}

// Filter by room name (partial match "LIKE")
if (isset($_GET['room_name']) && !empty(trim($_GET['room_name']))) {
    $room_name = "%" . trim($_GET['room_name']) . "%";
    $filters[] = "rooms.room_name LIKE :room_name";
    $params[':room_name'] = $room_name;
}

// Using "AND" it combines all filter conditions and adds it to the base query we did at the top
if (!empty($filters)) {
    $query .= " WHERE " . implode(' AND ', $filters);
}

try {
    // Executing the query

    // Prepare query using PDO from "db.php"
    $stmt = $pdo->prepare($query);

    // Bind parameters dynamically
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }

    // Execute query
    $stmt->execute();
    // Fetch as associative array using "PDO::FETCH_ASSOC"
    $rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Return rooms as JSON to use in (rooms.js)
    echo json_encode($rooms);
} catch (PDOException $e) {
    // Handle query errors
    echo json_encode(['error' => $e->getMessage()]);
}
?>