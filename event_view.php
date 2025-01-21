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
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <header>
        <h1>Upcoming Events</h1>
    </header>

    <main>
        <?php if (count($events) > 0): ?>
            <div class="events-list">
                <?php foreach ($events as $event): ?>
                    <div class="event-item">
                        <h2><a href="event_view.php?event_id=<?= $event['id'] ?>"><?= htmlspecialchars($event['name']) ?></a></h2>
                        <p><strong>Location:</strong> <?= htmlspecialchars($event['location']) ?></p>
                        <p><strong>Date:</strong> <?= date('F j, Y', strtotime($event['start_date'])) ?></p>
                        <p><strong>Time:</strong> <?= date('h:i A', strtotime($event['start_date'])) ?></p>
                        <p><strong>Description:</strong> <?= nl2br(htmlspecialchars($event['description'])) ?></p>

                        <?php if (isset($_SESSION['user_id'])): ?>
                            <form method="POST" action="register_event.php">
                                <input type="hidden" name="event_id" value="<?= $event['id'] ?>">
                                <button type="submit">Register for Event</button>
                            </form>
                        <?php else: ?>
                            <p>You must be logged in to register for the event.</p>
                        <?php endif; ?>
                    </div>
                    <hr>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p>No upcoming events found.</p>
        <?php endif; ?>
    </main>

    <footer>
        <p>&copy; <?= date('Y') ?> Event Management System</p>
    </footer>
</body>
</html>
