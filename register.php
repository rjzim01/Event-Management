<?php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    
    $stmt = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
    $stmt->execute([$name, $email, $password]);

    session_start();

    // Set a flash message
    $_SESSION['flash_message'] = "Register Successful";
    
    header('Location: login.php');
    exit;
}
?>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
      body {
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 100vh;
        background-color: #f8f9fa;
      }
      .register-card {
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
    <div class="register-card">
      <h2 class="text-center mb-4">Register</h2>
      <form method="POST">
        <div class="mb-3">
          <label for="name" class="form-label">Name</label>
          <input type="text" name="name" id="name" class="form-control" placeholder="Enter your name" required>
        </div>
        <div class="mb-3">
          <label for="email" class="form-label">Email</label>
          <input type="email" name="email" id="email" class="form-control" placeholder="Enter your email" required>
        </div>

        <div class="mb-3">
          <label for="password" class="form-label">Password</label>
          <div class="input-group">
            <input type="password" name="password" id="password" class="form-control" placeholder="Enter a strong password" required>
            <button class="btn btn-outline-secondary" type="button" id="togglePassword">👁️</button>
          </div>
          <div class="invalid-feedback">Password must be at least 9 characters, including an uppercase letter, a lowercase letter, a number, and a special character.</div>
        </div>


        <div class="d-grid">
          <button type="submit" class="btn btn-primary">Register</button>
        </div>
        <div class="text-center mt-3">
          <p>Already have an account? <a href="login.php" class="link-primary">Login</a></p>
        </div>
      </form>
    </div>

    <script>
      document.addEventListener("DOMContentLoaded", function() {

        const form = document.querySelector("form");
        const nameInput = document.getElementById("name");
        const emailInput = document.getElementById("email");
        const passwordInput = document.getElementById("password");
        const togglePassword = document.getElementById("togglePassword");

        emailInput.addEventListener("input", function() {

          const emailPattern = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;

          if (emailPattern.test(emailInput.value)) {
            emailInput.classList.remove("is-invalid");
            emailInput.classList.add("is-valid");
          } else {
            emailInput.classList.remove("is-valid");
            emailInput.classList.add("is-invalid");
          }

        });

        // Strong password validation
        passwordInput.addEventListener("input", function () {
          const password = passwordInput.value;
          const strongPasswordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{9,}$/;

          if (strongPasswordRegex.test(password)) {
            passwordInput.classList.remove("is-invalid");
            passwordInput.classList.add("is-valid");
          } else {
            passwordInput.classList.remove("is-valid");
            passwordInput.classList.add("is-invalid");
          }
        });

        togglePassword.addEventListener("click", function () {
          if (passwordInput.type === "password") {
            passwordInput.type = "text";
            togglePassword.innerHTML = "🙈"; // Change icon to hide
          } else {
            passwordInput.type = "password";
            togglePassword.innerHTML = "👁️"; // Change icon to show
          }
        });

        // Prevent form submission if validation fails
        form.addEventListener("submit", function (event) {
          let isValid = true;

          // Check if name is not empty
          if (nameInput.value.trim() === "") {
            nameInput.classList.add("is-invalid");
            isValid = false;
          } else {
            nameInput.classList.remove("is-invalid");
            nameInput.classList.add("is-valid");
          }

          // Check if email is valid
          if (!emailInput.checkValidity()) {
            emailInput.classList.add("is-invalid");
            isValid = false;
          } else {
            emailInput.classList.remove("is-invalid");
            emailInput.classList.add("is-valid");
          }

          // Check if password is strong
          const password = passwordInput.value;
          const strongPasswordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{9,}$/;
          if (!strongPasswordRegex.test(password)) {
            passwordInput.classList.add("is-invalid");
            isValid = false;
          } else {
            passwordInput.classList.remove("is-invalid");
            passwordInput.classList.add("is-valid");
          }

          // If any field is invalid, prevent form submission
          if (!isValid) {
            event.preventDefault();
          }
        });

      });
    </script>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
  </body>
</html>
