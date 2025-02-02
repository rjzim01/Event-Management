<?php
// Start session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if not logged in
    header('Location: login.php');
    exit;
}

require 'db.php'; // Database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $event_id = $_POST['event_id'] ?? null;
    $name = $_POST['user_name'] ?? null;
    $email = $_POST['user_email'] ?? null;
    $user_id = $_SESSION['user_id']; // Logged-in user's ID

    // Check for missing inputs
    if (empty($event_id) || empty($name) || empty($email)) {
        die('Missing required information.');
    }

    // Check if the user is the host of the event
    $hostCheckStmt = $pdo->prepare("SELECT created_by FROM events WHERE id = ?");
    $hostCheckStmt->execute([$event_id]);
    $event = $hostCheckStmt->fetch();

    if ($event && $event['created_by'] == $user_id) {
        //echo '<script>alert("You cannot register for your own event."); window.location.href="event_view.php";</script>';
        $_SESSION['flash_message'] = "You cannot register for your own event.";
        header('Location: event_view.php');
        exit;
    }

    // Check if the event has a seat limit and if it's reached
    $seatLimitStmt = $pdo->prepare("SELECT max_capacity, (SELECT COUNT(*) FROM attendees WHERE event_id = ?) AS current_attendees FROM events WHERE id = ?");
    $seatLimitStmt->execute([$event_id, $event_id]);
    $seatInfo = $seatLimitStmt->fetch();

    if ($seatInfo) {
        $seatLimit = $seatInfo['max_capacity'];
        $currentAttendees = $seatInfo['current_attendees'];

        // Check if the seat limit is reached
        if ($currentAttendees >= $seatLimit) {
            //echo '<script>alert("The seat limit for this event has been reached. Registration is closed."); window.location.href="event_view.php";</script>';
            $_SESSION['flash_message'] = "The seat limit for this event has been reached. Registration is closed.";
            header('Location: event_view.php');
            exit;
        }
    } else {
        echo '<script>alert("Event not found."); window.location.href="event_view.php";</script>';
        exit;
    }

    // Check if the user is already registered for the event
    $checkStmt = $pdo->prepare("SELECT * FROM attendees WHERE event_id = ? AND email = ?");
    $checkStmt->execute([$event_id, $email]);

    if ($checkStmt->rowCount() > 0) {
        //echo '<script>alert("You have already registered for this event."); window.location.href="event_view.php";</script>';
        $_SESSION['flash_message'] = "You have already registered for this event.";
        header('Location: event_view.php');
        exit;
    }

    // Insert attendee into the database
    $stmt = $pdo->prepare("INSERT INTO attendees (event_id, name, email, registered_at) VALUES (?, ?, ?, NOW())");

    if ($stmt->execute([$event_id, $name, $email])) {
        $_SESSION['flash_message'] = "Successfully registered for the event.";
        header('Location: event_view.php');
        exit;
        // echo '<script>alert("Successfully registered for the event."); window.location.href="event_view.php";</script>';
        // echo '<script>$_SESSION['flash_message'] = "Successfully registered for the event."; window.location.href="event_view.php";</script>';
        // $_SESSION['flash_message'] = "Login successful";
    } else {
        $_SESSION['flash_message'] = "Failed to register for the event.";
        header('Location: event_view.php');
        exit;
        // echo '<script>alert("Failed to register for the event."); window.location.href="event_view.php";</script>';
    }
} else {
    // Redirect if not a POST request
    header('Location: event_view.php');
    exit;
}
?>
