<?php
// Start session
session_start();

// Check if user is an admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die("Unauthorized access.");
}

require 'db.php';

if (!isset($_GET['event_id']) || !is_numeric($_GET['event_id'])) {
    die("Invalid event ID.");
}

$event_id = $_GET['event_id'];

// Fetch event details
$stmtEvent = $pdo->prepare("SELECT name FROM events WHERE id = ?");
$stmtEvent->execute([$event_id]);
$event = $stmtEvent->fetch(PDO::FETCH_ASSOC);

if (!$event) {
    die("Event not found.");
}

// Fetch attendee list
$stmt = $pdo->prepare("SELECT name, email, registered_at FROM attendees WHERE event_id = ?");
$stmt->execute([$event_id]);

$attendees = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Set headers for CSV download
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="attendees_' . $event_id . '.csv"');

// Open file pointer to output stream
$output = fopen('php://output', 'w');

// Write CSV column headers
fputcsv($output, ['Name', 'Email', 'Registered At']);

// Write attendee data
foreach ($attendees as $row) {
    fputcsv($output, $row);
}

// Close output
fclose($output);
exit;
