<?php
// Start session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if not logged in
    header('Location: login.php');
    exit; // Make sure no code is executed after the redirect
}

require 'db.php';

// Fetch all events from the database
$stmt = $pdo->prepare("SELECT * FROM events ORDER BY start_date DESC");
$stmt->execute();
$events = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Events</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <header class="bg-primary text-white text-center py-3">
        <h1>Upcoming Events</h1>
    </header>

    <main class="container my-4">
        <?php if (count($events) > 0): ?>
            <div class="row g-4">
                <?php foreach ($events as $event): ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="card h-100">
                            <div class="card-body">
                                <h5 class="card-title">
                                    <a href="event_view.php?event_id=<?= $event['id'] ?>" class="text-decoration-none">
                                        <?= htmlspecialchars($event['name']) ?>
                                    </a>
                                </h5>
                                <p class="card-text"><strong>Location:</strong> <?= htmlspecialchars($event['location']) ?></p>
                                <p class="card-text"><strong>Date:</strong> <?= date('F j, Y', strtotime($event['start_date'])) ?></p>
                                <p class="card-text"><strong>Time:</strong> <?= date('h:i A', strtotime($event['start_date'])) ?></p>
                                <p class="card-text"><strong>Description:</strong> <?= nl2br(htmlspecialchars($event['description'])) ?></p>
                            </div>
                            <div class="card-footer text-center">
                                <?php if (isset($_SESSION['user_id'])): ?>
                                    <form method="POST" action="register_event.php" class="d-inline">
                                        <input type="hidden" name="event_id" value="<?= $event['id'] ?>">
                                        <button type="submit" class="btn btn-primary">Register for Event</button>
                                    </form>
                                <?php else: ?>
                                    <p class="text-danger">You must be logged in to register for the event.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="alert alert-info text-center">
                <p>No upcoming events found.</p>
            </div>
        <?php endif; ?>
    </main>

    <footer class="bg-dark text-white text-center py-3">
        <p>&copy; <?= date('Y') ?> Event Management System</p>
    </footer>

    <!-- Bootstrap Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
