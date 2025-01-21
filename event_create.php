<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $location = $_POST['location'];
    $max_capacity = $_POST['max_capacity'];
    $created_by = $_SESSION['user_id'];
    
    $stmt = $pdo->prepare("INSERT INTO events (name, description, start_date, end_date, location, max_capacity, created_by) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$name, $description, $start_date, $end_date, $location, $max_capacity, $created_by]);
    
    header('Location: dashboard.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Create Event</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <form method="POST">
        <h2>Create Event</h2>
        <input type="text" name="name" placeholder="Event Name" required>
        <textarea name="description" placeholder="Description"></textarea>
        <input type="date" name="start_date" required>
        <input type="date" name="end_date" required>
        <input type="text" name="location" placeholder="Location" required>
        <input type="number" name="max_capacity" placeholder="Max Capacity" required>
        <button type="submit">Create Event</button>
    </form>
</body>
</html>
