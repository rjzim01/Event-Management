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
            
            // Set a flash message
            $_SESSION['flash_message'] = "Login successful";

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

<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
      body {
        display: flex;
        align-items: center;
        justify-content: center;
        height: 100vh;
        background-color: #f8f9fa;
      }
      .login-card {
        max-width: 400px;
        width: 100%;
        padding: 20px;
        border: 1px solid #ddd;
        border-radius: 8px;
        background-color: #fff;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
      }
    </style>
  </head>
  <body>
    <div class="login-card">
      <h2 class="text-center mb-4">Login</h2>

      <!-- Display validation errors -->
      <?php if (!empty($errors)) : ?>
        <div class="alert alert-danger" role="alert">
          <ul class="mb-0">
            <?php foreach ($errors as $error) : ?>
              <li><?= htmlspecialchars($error) ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>

      
      <?php if (isset($_SESSION['flash_message'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($_SESSION['flash_message']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['flash_message']); ?>
      <?php endif; ?>

      <form method="POST" action="login.php">
        <div class="mb-3">
          <label for="email" class="form-label">Email</label>
          <input type="email" name="email" id="email" class="form-control" required>
        </div>
        <div class="mb-3">
          <label for="password" class="form-label">Password</label>
          <input type="password" name="password" id="password" class="form-control" required>
        </div>
        <div class="d-grid">
          <button type="submit" class="btn btn-primary">Login</button>
        </div>

        <!-- <div class="text-center mt-3">
          <a href="forgot_password.php" class="link-primary">Forgot Password?</a>
        </div> -->

        <div class="text-center mt-3">
        <p>Not Registered Yet? <a href="register.php" class="link-primary">Register</a></p>    
        </div>

      </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  </body>
</html>
