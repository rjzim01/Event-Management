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

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <header>
        <h1>Welcome to the Event Management System!</h1>
        <p>Hello, <strong><?php echo htmlspecialchars($role); ?></strong>!</p>
    </header>
    
    <main>
        <!-- Links to other pages based on role -->
        <nav>
            <ul>
                <li><a href="event_create.php">Create Event</a></li>
                <li><a href="event_view.php">View Events</a></li>
                <?php if ($role === 'Admin'): ?>
                    <!-- Additional options for admin users -->
                    <li><a href="user_management.php">User Management</a></li>
                    <li><a href="settings.php">Settings</a></li>
                <?php endif; ?>
            </ul>
        </nav>
        
        <!-- Logout Button -->
        <form action="logout.php" method="POST" style="display: inline;">
            <button type="submit">Logout</button>
        </form>
    </main>

    <footer>
        <p>&copy; <?= date('Y') ?> Event Management System</p>
    </footer>
</body>
</html>
