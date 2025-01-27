<?php
// Start session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
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

$eventsPerPage = 3;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $eventsPerPage;

// Sorting setup
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'start_date';
$order = isset($_GET['order']) && $_GET['order'] === 'asc' ? 'ASC' : 'DESC';
$allowedSortColumns = ['name', 'start_date', 'location', 'seats_left'];
if (!in_array($sort, $allowedSortColumns)) {
    $sort = 'start_date';
}

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
        $sort $order
    LIMIT :limit OFFSET :offset
");
$stmt->bindValue(':limit', $eventsPerPage, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$events = $stmt->fetchAll();

// Get the total number of events
$totalEventsStmt = $pdo->query("SELECT COUNT(*) FROM events");
$totalEvents = $totalEventsStmt->fetchColumn();

// Calculate total pages
$totalPages = ceil($totalEvents / $eventsPerPage);

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

        <main class="container my-3">

            <div style="padding: 10px; border-radius: 5px; margin-bottom: 10px;" class="bg-light shadow-sm text-black">
                <!-- Sorting -->
                <form method="GET" class="d-inline-block">
                    <label for="sort" class="me-2">Sort by:</label>
                    <select name="sort" id="sort" class="form-select d-inline-block w-auto" onchange="this.form.submit()">
                        <option value="start_date" <?= $sort === 'start_date' ? 'selected' : '' ?>>Start Date</option>
                        <option value="name" <?= $sort === 'name' ? 'selected' : '' ?>>Name</option>
                        <option value="location" <?= $sort === 'location' ? 'selected' : '' ?>>Location</option>
                        <option value="seats_left" <?= $sort === 'seats_left' ? 'selected' : '' ?>>Seats Left</option>
                    </select>
                    <input type="hidden" name="order" value="<?= $order === 'ASC' ? 'desc' : 'asc' ?>">
                </form>
            </div>

            <!-- <hr> -->

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
                                    <p><strong>Location:</strong> <?= htmlspecialchars($event['location']) ?></p>
                                    <p><strong>Start Time:</strong> <?= date('h:i A, F j, Y', strtotime($event['start_date'])) ?></p>
                                    <p class="card-text">
                                        <strong>Organized By:</strong> 
                                        <?= ($_SESSION['user_id'] == $event['created_by']) ? htmlspecialchars($event['creator_name']).' ( Yourself )' : htmlspecialchars($event['creator_name'] ?? 'Unknown') ?>
                                    </p>

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

                <hr>
                
                <!-- Pagination Controls -->
                <nav aria-label="Page navigation" class="mt-4">
                    <ul class="pagination justify-content-end">
                        <?php if ($page > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?= $page - 1 ?>" aria-label="Previous">
                                    <span aria-hidden="true">&laquo;</span>
                                </a>
                            </li>
                        <?php endif; ?>

                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                                <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>

                        <?php if ($page < $totalPages): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?= $page + 1 ?>" aria-label="Next">
                                    <span aria-hidden="true">&raquo;</span>
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>

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
