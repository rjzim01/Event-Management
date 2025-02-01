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

// Get the POST data
$event_id = $_POST['event_id'] ?? null;
$user_name = $_POST['user_name'] ?? null;
$user_email = $_POST['user_email'] ?? null;
$seatsLeft = $_POST['seatsLeft'] ?? null;

if (!$event_id || !$user_name || !$user_email) {
    echo "Missing required data.";
    exit;
}

// Fetch event details
$stmt = $pdo->prepare("SELECT name FROM events WHERE id = ?");
$stmt->execute([$event_id]);
$event = $stmt->fetch();

if (!$event) {
    echo "Event not found.";
    exit;
}

// Store user data in session (optional, if needed in the next steps)
$_SESSION['event_id'] = $event_id;
$_SESSION['user_name'] = $user_name;
$_SESSION['user_email'] = $user_email;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirm Your Registration</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

    <div class="content container">
        <!-- Navbar -->
        <?php include 'templates/navbar.php'; ?>

        <header class="bg-light shadow-sm text-black text-center py-2">
            <h1>Confirm Your Registration for <?= htmlspecialchars($event['name']) ?></h1>
        </header>

        <main class="container my-3">
            <div class="row justify-content-center">
                <div class="col-md-12">
                    <!-- Display User and Event Info -->
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="user_name" class="form-label">Name</label>
                                <input type="text" id="user_name" class="form-control" value="<?= htmlspecialchars($user_name) ?>" readonly>
                            </div>
                            <div class="mb-3">
                                <label for="user_email" class="form-label">Email</label>
                                <input type="email" id="user_email" class="form-control" value="<?= htmlspecialchars($user_email) ?>" readonly>
                            </div>
                            <div class="mb-3">
                                <label for="event_name" class="form-label">Event Name</label>
                                <input type="text" id="event_name" class="form-control" value="<?= htmlspecialchars($event['name']) ?>" readonly>
                            </div>
                            <div class="mb-3">
                                <label for="seats_left" class="form-label">Seats Left</label>
                                <input type="text" id="seats_left" class="form-control" value="<?= htmlspecialchars($seatsLeft) ?>" readonly>
                            </div>

                            <!-- Confirm Button to Submit the Final Registration -->
                            <form method="POST" action="attendee_register.php">
                                <input type="hidden" name="event_id" value="<?= $event_id ?>">
                                <input type="hidden" name="user_name" value="<?= htmlspecialchars($user_name) ?>">
                                <input type="hidden" name="user_email" value="<?= htmlspecialchars($user_email) ?>">

                                <div class="d-flex justify-content-between">
                                    <button type="submit" class="btn btn-success">Confirm Registration</button>
                                    <a href="event_view.php" class="btn btn-danger">Cancel</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Footer -->
    <?php include 'templates/footer.php'; ?>

</body>
</html>
