<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");

require 'db.php';

// Get the request method
$method = $_SERVER['REQUEST_METHOD'];

if ($method == 'GET') {
    if (isset($_GET['id'])) {
        // Fetch a single event
        $id = intval($_GET['id']);
        $stmt = $pdo->prepare("SELECT * FROM events WHERE id = ?");
        $stmt->execute([$id]);
        $event = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($event) {
            echo json_encode($event);
        } else {
            echo json_encode(['message' => 'Event not found']);
        }
    } else {
        // Fetch all events
        $stmt = $pdo->query("SELECT * FROM events");
        $events = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($events);
    }
} else {
    echo json_encode(['error' => 'Invalid request method']);
}
?>
