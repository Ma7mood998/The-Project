<?php
require_once 'db.php';

// Initialize query
$query = "SELECT rooms.room_id, rooms.room_name, rooms.capacity, 
                 rooms.equipment, rooms.available,
                 (CASE 
                    WHEN rooms.available = 1 THEN 'available'
                    ELSE 'unavailable' 
                 END) AS status
          FROM rooms";

// Add filters dynamically
$filters = [];
if (isset($_GET['availability']) && $_GET['availability'] !== 'all') {
    $availability = $_GET['availability'];
    if ($availability === 'available') {
        $filters[] = "rooms.available = 1";
    } elseif ($availability === 'unavailable') {
        $filters[] = "rooms.available = 0";
    }
}

if (isset($_GET['capacity']) && $_GET['capacity'] === 'gt30') {
    $filters[] = "rooms.capacity > 30";
}

if (isset($_GET['room_name']) && !empty(trim($_GET['room_name']))) {
    $room_name = "%" . trim($_GET['room_name']) . "%";
    $filters[] = "rooms.room_name LIKE :room_name";
}

// Append filters to query if any
if (!empty($filters)) {
    $query .= " WHERE " . implode(' AND ', $filters);
}

// Execute the query
try {
    $stmt = $pdo->prepare($query);

    // Bind search parameter if present
    if (isset($room_name)) {
        $stmt->bindParam(':room_name', $room_name, PDO::PARAM_STR);
    }

    $stmt->execute();
    $rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Return rooms as JSON
    echo json_encode($rooms);
} catch (PDOException $e) {
    // Handle query errors
    echo json_encode(['error' => $e->getMessage()]);
}
?>
