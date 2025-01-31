<?php
session_start();
require 'db.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Check the user's role (assuming role is stored in session)
$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'User';

// Get the event ID from the query parameter
$eventId = $_GET['event_id'] ?? null;

if (!$eventId) {
    die('Event ID is required.');
}

// Fetch the event details
$stmt = $pdo->prepare("SELECT * FROM events WHERE id = :id");
$stmt->execute(['id' => $eventId]);
$event = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$event) {
    die('Event not found.');
}

// Check if the logged-in user is the creator of the event
if ($_SESSION['user_id'] != $event['created_by']) {
    die('You are not authorized to edit this event.');
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $location = $_POST['location'];
    $start_date = $_POST['start_date'];
    $max_capacity = $_POST['max_capacity'];

    $stmt = $pdo->prepare("
        UPDATE events 
        SET name = :name, description = :description, location = :location, start_date = :start_date, max_capacity = :max_capacity
        WHERE id = :id
    ");

    $stmt->execute([
        'name' => $name,
        'description' => $description,
        'location' => $location,
        'start_date' => $start_date,
        'max_capacity' => $max_capacity,
        'id' => $eventId,
    ]);

    // Redirect to events list
    header('Location: event_view.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Event</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    
    <div class="content container">
        <!-- Navbar -->
        <?php include 'templates/navbar.php'; ?>

        <header class="bg-light shadow-sm text-black text-center py-2">
            <h1>Edit Event</h1>
        </header>

        <main class="container my-4 border p-4">
            <form method="POST" action="">
                <div class="mb-3">
                    <label for="name" class="form-label">Event Name</label>
                    <input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($event['name']) ?>" required>
                </div>
                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control" id="description" name="description" rows="5" required><?= htmlspecialchars($event['description']) ?></textarea>
                </div>
                <div class="mb-3">
                    <label for="location" class="form-label">Location</label>
                    <input type="text" class="form-control" id="location" name="location" value="<?= htmlspecialchars($event['location']) ?>" required>
                </div>
                <div class="mb-3">
                    <label for="start_date" class="form-label">Start Date and Time</label>
                    <input type="datetime-local" class="form-control" id="start_date" name="start_date" value="<?= date('Y-m-d\TH:i', strtotime($event['start_date'])) ?>" required>
                </div>
                <div class="mb-3">
                    <label for="max_capacity" class="form-label">Max Capacity</label>
                    <input type="number" class="form-control" id="max_capacity" name="max_capacity" value="<?= htmlspecialchars($event['max_capacity']) ?>" required>
                </div>
                <button type="submit" class="btn btn-success">Update Event</button>
                <a href="view_events.php" class="btn btn-secondary">Cancel</a>
            </form>
        </main>

    </div>
    
    <!-- Footer -->
    <?php include 'templates/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
