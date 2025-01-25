<?php
// Start session
session_start();

// echo '<pre>';
// print_r($_SESSION);
// echo '</pre>';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if not logged in
    header('Location: login.php');
    exit; // Make sure no code is executed after the redirect
}


// Check the user's role (assuming role is stored in session)
$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'User';

require 'db.php';

// Fetch the logged-in user's details from the database
$stmtUser = $pdo->prepare("SELECT name, email FROM users WHERE id = :user_id");
$stmtUser->execute(['user_id' => $_SESSION['user_id']]);
$user = $stmtUser->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die('User details not found.');
}

// // Fetch all events from the database
// $stmt = $pdo->prepare("SELECT * FROM events ORDER BY start_date DESC");
// $stmt->execute();
// $events = $stmt->fetchAll();

// Fetch all events with creator's name from the database
$stmt = $pdo->prepare("
    SELECT 
    events.*,
    users.name AS creator_name,
    users.email AS creator_email,
    events.max_capacity AS total_seats,
    COUNT(attendees.id) AS reserved_seats,
    (events.max_capacity - COUNT(attendees.id)) AS seats_left
    FROM 
        events
    LEFT JOIN 
        users ON events.created_by = users.id
    LEFT JOIN 
        attendees ON events.id = attendees.event_id
    GROUP BY 
        events.id
    ORDER BY 
        events.start_date DESC;

");
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
    
    <link rel="stylesheet" href="assets/css/style.css">

</head>

<body>

    <div class="content container">

        <?php if (isset($_SESSION['flash_message'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($_SESSION['flash_message']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['flash_message']); ?>
        <?php endif; ?>

        <!-- Navbar -->
        <?php include 'templates/navbar.php'; ?>

        <header class="bg-light shadow-sm text-black text-center py-2">
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

                                    <hr>

                                    <p class="card-text">
                                        <strong>Organized By:</strong> 
                                        <?= ($_SESSION['user_id'] == $event['created_by']) ? htmlspecialchars($event['creator_name']).' ( Yourself )' : htmlspecialchars($event['creator_name'] ?? 'Unknown') ?>
                                    </p>

                                    <p class="card-text"><strong>Location:</strong> <?= htmlspecialchars($event['location']) ?></p>
                                    <p class="card-text"><strong>Start Time:</strong> <?= date('h:i A, F j, Y', strtotime($event['start_date'])) ?></p>

                                    <p class="card-text"><strong>Description:</strong> <?= nl2br(htmlspecialchars($event['description'])) ?></p>

                                    <p class="card-text">
                                        <strong>Seats Left:</strong> 
                                        <?php 
                                        // Calculate remaining seats
                                        $seatsLeft = $event['total_seats'] - $event['reserved_seats'];
                                        
                                        if ($seatsLeft > 0) {
                                            // Display remaining seats as fraction
                                            echo $seatsLeft . ' / ' . $event['total_seats'];
                                        } else {
                                            // Display 'Sold Out' if no seats are left
                                            echo 'Sold Out';
                                        }

                                        ?>
                                    </p>

                                </div>

                                <div class="card-footer text-start">
                                    <?php if (isset($_SESSION['user_id'])): ?>
                                        
                                        <?php
                                        // Check if the user is already registered for this event
                                        $stmt = $pdo->prepare("SELECT COUNT(*) FROM attendees WHERE event_id = ? AND email = ?");
                                        $stmt->execute([$event['id'], $user['email']]);
                                        $isRegistered = $stmt->fetchColumn() > 0;

                                        // Check if the current time is before the event start time
                                        $currentTime = new DateTime();
                                        $eventStartTime = new DateTime($event['start_date']);
                                        $isRegistrationOpen = $currentTime < $eventStartTime;

                                        // Check if the logged-in user is the creator of the event
                                        $isCreator = ($_SESSION['user_id'] == $event['created_by']);
                                        ?>

                                        <?php if ($isCreator): ?>
                                            <!-- Show a message for the event creator -->
                                            <!-- <p class="text-info">You are the creator of this event</p> -->
                                            <a href="event_edit.php?event_id=<?= $event['id'] ?>" class="btn btn-warning">Edit Event</a>

                                            <!-- Delete Event Option -->
                                            <form method="POST" action="event_delete.php" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this event?');">
                                                <input type="hidden" name="event_id" value="<?= $event['id'] ?>">
                                                <button type="submit" class="btn btn-danger">Delete Event</button>
                                            </form>

                                        <?php elseif ($isRegistered): ?>
                                            <!-- Show if the user is already registered -->
                                            <p class="text-success">Registration complete</p>
                                        <?php elseif (!$isRegistrationOpen): ?>
                                            <!-- Show if registration is closed -->
                                            <p class="text-danger">Registration closed</p>
                                        <?php else: ?>
                                            <!-- Show registration button for eligible users -->
                                            <form method="POST" action="attendee_register.php" class="d-inline">
                                                <input type="hidden" name="event_id" value="<?= $event['id'] ?>">
                                                <input type="hidden" name="user_name" value="<?= htmlspecialchars($user['name']) ?>">
                                                <input type="hidden" name="user_email" value="<?= htmlspecialchars($user['email']) ?>">
                                                <button type="submit" class="btn btn-primary">Register for Event</button>
                                            </form>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <!-- Message for guests (not logged in) -->
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

    </div>

    <!-- Footer -->
    <?php include 'templates/footer.php'; ?>

    <!-- Bootstrap Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>




</body>

</html>
