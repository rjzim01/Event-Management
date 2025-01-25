<?php
session_start();
require 'db.php'; // Replace with your actual database connection file

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
    $eventId = $_POST['event_id'];

    // Check if the logged-in user is the creator of the event
    $stmt = $pdo->prepare("SELECT created_by FROM events WHERE id = ?");
    $stmt->execute([$eventId]);
    $event = $stmt->fetch();

    if ($event && $event['created_by'] == $_SESSION['user_id']) {
        try {
            // Begin transaction
            $pdo->beginTransaction();

            // Delete attendees linked to the event
            $deleteAttendeesStmt = $pdo->prepare("DELETE FROM attendees WHERE event_id = ?");
            $deleteAttendeesStmt->execute([$eventId]);

            // Delete the event itself
            $deleteEventStmt = $pdo->prepare("DELETE FROM events WHERE id = ?");
            $deleteEventStmt->execute([$eventId]);

            // Commit transaction
            $pdo->commit();

            // Redirect with success message
            $_SESSION['flash_message'] = "Event deleted successfully.";
            header("Location: event_view.php");
            exit();
        } catch (Exception $e) {
            // Rollback in case of an error
            $pdo->rollBack();
            $_SESSION['flash_message'] = "Failed to delete the event: " . $e->getMessage();
            header("Location: event_view.php");
            exit();
        }
    } else {
        // Unauthorized access
        $_SESSION['flash_message'] = "You are not authorized to delete this event.";
        header("Location: event_view.php");
        exit();
    }
} else {
    // Invalid request
    $_SESSION['flash_message'] = "Invalid request.";
    header("Location: event_view.php");
    exit();
}
?>
