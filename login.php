<?php
require 'db.php';
session_start(); // Start the session to manage error messages

// Redirect to dashboard if the user is already logged in
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}

// Initialize error array
$errors = [];

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Validate email
    if (empty($email)) {
        $errors[] = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }

    // Validate password
    if (empty($password)) {
        $errors[] = "Password is required.";
    }

    // If no validation errors, proceed to check user credentials
    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            // Successful login
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
            header('Location: dashboard.php'); // Redirect to dashboard
            exit;
        } else {
            // Invalid credentials
            $errors[] = "Invalid email or password!";
        }
    }

    // Save errors to the session to display them after redirect
    $_SESSION['errors'] = $errors;
    $_SESSION['email'] = $email; // Preserve email input
    header('Location: login.php'); // Redirect back to the login page
    exit;
}

// Retrieve errors and input from the session (if any)
if (isset($_SESSION['errors'])) {
    $errors = $_SESSION['errors'];
    unset($_SESSION['errors']); // Clear errors after displaying them
}
$email = $_SESSION['email'] ?? ''; // Retrieve preserved email
unset($_SESSION['email']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Login</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <form method="POST" action="login.php">
        <h2>Login</h2>

        <!-- Display validation errors -->
        <?php if (!empty($errors)) : ?>
            <ul style="color: red;">
                <?php foreach ($errors as $error) : ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <input type="email" name="email" placeholder="Email" value="<?= htmlspecialchars($email) ?>" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Login</button>
    </form>
</body>
</html>
