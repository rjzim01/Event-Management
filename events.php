<?php
require 'db.php';
session_start();

// Prepare SQL query to fetch all events from the database
$stmt = $pdo->prepare("SELECT * FROM events ORDER BY date ASC");
$stmt->execute();
$events = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Events List</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <header>
        <h1>Upcoming Events</h1>
    </header>

    <main>
        <section class="events-list">
            <?php if (count($events) > 0): ?>
                <ul>
                    <?php foreach ($events as $event): ?>
                        <li>
                            <h2><a href="event_view.php?event_id=<?= $event['id'] ?>"><?= htmlspecialchars($event['title']) ?></a></h2>
                            <p><strong>Location:</strong> <?= htmlspecialchars($event['location']) ?></p>
                            <p><strong>Date:</strong> <?= date('F j, Y', strtotime($event['date'])) ?></p>
                            <p><strong>Time:</strong> <?= date('h:i A', strtotime($event['time'])) ?></p>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>No events found. Please check back later!</p>
            <?php endif; ?>
        </section>

        <?php if (isset($_SESSION['user_id'])): ?>
            <section class="add-event">
                <h3>Add New Event</h3>
                <form method="POST" action="add_event.php">
                    <input type="text" name="title" placeholder="Event Title" required>
                    <input type="text" name="location" placeholder="Event Location" required>
                    <input type="date" name="date" required>
                    <input type="time" name="time" required>
                    <textarea name="description" placeholder="Event Description" required></textarea>
                    <button type="submit">Add Event</button>
                </form>
            </section>
        <?php else: ?>
            <p>You must be logged in to add a new event.</p>
        <?php endif; ?>
    </main>

    <footer>
        <p>&copy; <?= date('Y') ?> Event Management System</p>
    </footer>
</body>
</html>
