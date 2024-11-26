<?php

require_once 'db.php';

try {
    
    $query = "SELECT room_id, room_name, capacity, equipment FROM rooms WHERE available = 1";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    
    $rooms = $stmt->fetchAll();

    header('Content-Type: application/json');
    echo json_encode($rooms);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => $e->getMessage()]);
}
?>
