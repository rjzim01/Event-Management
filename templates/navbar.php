<?php
// Get the current script name (e.g., "dashboard.php")
$current_page = basename($_SERVER['PHP_SELF']);
?>

<nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm mb-2">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold" href="dashboard.php">Event Manager</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">

                <li class="nav-item">
                    <a class="nav-link <?= $current_page === 'dashboard.php' ? 'active' : ''; ?>" aria-current="page" href="dashboard.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $current_page === 'event_view.php' ? 'active' : ''; ?>" href="event_view.php">View Events</a>
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