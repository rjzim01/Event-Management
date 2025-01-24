<?php
// Start the session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // If not logged in, redirect to login page
    header('Location: login.php');
    exit;
}

// Check the user's role (assuming role is stored in session)
$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'User';
?>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Event Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  </head>
  <body>

    <div class="container">
        
        <?php if (isset($_SESSION['flash_message'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($_SESSION['flash_message']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['flash_message']); ?>
        <?php endif; ?>

        <!-- Navbar -->
        <nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm mb-4">
            <div class="container-fluid">
                <a class="navbar-brand fw-bold" href="#">Event Manager</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        <li class="nav-item">
                            <a class="nav-link active" aria-current="page" href="#">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="event_view.php">View Events</a>
                        </li>
                        <?php if ($role === 'Admin'): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Admin Menu
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="user_management.php">User Management</a></li>
                                <li><a class="dropdown-item" href="settings.php">Settings</a></li>
                            </ul>
                        </li>
                        <?php endif; ?>
                    </ul>
                    
                    <div class="d-flex">
                        <button class="btn btn-outline-primary me-2" type="button" onclick="window.location.href='profile.php';">
                            Profile
                        </button>
                        <form action="logout.php" method="POST">
                            <button type="submit" class="btn btn-danger">Logout</button>
                        </form>
                    </div>

                </div>
            </div>
        </nav>

        <!-- Header -->
        <header class="text-center mb-4">
            <h1 class="display-5 fw-bold">Welcome to the Event Management System!</h1>
            <p class="lead">Hello, <strong><?php echo htmlspecialchars($role); ?></strong>! Manage your events efficiently.</p>
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

        <!-- Footer -->
        <footer class="text-center mt-4">
            <p class="text-muted">&copy; <?= date('Y') ?> Event Management System. All rights reserved.</p>
        </footer>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
  </body>
</html>
