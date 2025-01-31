<?php

session_start(); // Start the session

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // If not logged in, redirect to login page
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

?>

<!doctype html>
<html lang="en">

  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Event Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

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

        <!-- Header -->
        <header class="bg-light shadow-sm text-black text-center mb-4 py-2">
            <h1 class="display-5 fw-bold">Welcome to the Event Management System!</h1>
            <p class="lead">Hello, <strong><?= htmlspecialchars($user['name']) ?></strong>! Manage your events efficiently.</p>
        </header>

        <!-- Main Content -->
        <main>
            <div class="row text-center">
                <div class="col-md-4 mb-3">
                    <div class="card border-primary shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">Create an Event</h5>
                            <p class="card-text">Organize new events and share details with ease.</p>
                            <a href="event_create.php" class="btn btn-primary">Create Event</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="card border-success shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">View Events</h5>
                            <p class="card-text">Explore and manage existing events.</p>
                            <a href="event_view.php" class="btn btn-success">View Events</a>
                        </div>
                    </div>
                </div>
                <?php if ($role === 'Admin'): ?>
                <div class="col-md-4 mb-3">
                    <div class="card border-warning shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">Admin Settings</h5>
                            <p class="card-text">Access user management and system settings.</p>
                            <a href="settings.php" class="btn btn-warning">Go to Settings</a>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </main>

    </div>

    <!-- Footer -->
    <?php include 'templates/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
  </body>
</html>
