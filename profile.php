<?php
// Start the session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // If not logged in, redirect to login page
    header('Location: login.php');
    exit;
}

// Fetch user details from the session
$user_id = $_SESSION['user_id'];
$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'User';

require 'db.php';

// Fetch the logged-in user's details from the database
$stmtUser = $pdo->prepare("SELECT name, email FROM users WHERE id = :user_id");
$stmtUser->execute(['user_id' => $user_id]);
$user = $stmtUser->fetch(PDO::FETCH_ASSOC);
$username = $user['name'];

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Simulate profile update (in a real case, you'd save the changes to the database)
    $name = $_POST['name'];

    // Validate the name (add more validations as needed)
    if (empty($name)) {
        $_SESSION['flash_message'] = "Name is required!";
        header('Location: profile.php');
        exit;
    }

    // Get the user ID from the session
    $user_id = $_SESSION['user_id'];

    // Prepare the SQL statement to update the name in the users table
    $stmt = $pdo->prepare("UPDATE users SET name = ? WHERE id = ?");
    
    // Execute the query with the user-provided data
    $stmt->execute([$name, $user_id]);

    $_SESSION['flash_message'] = "Profile updated successfully!";
    header('Location: profile.php');
    exit;
}

?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Profile - Event Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>

<div class="content container">

    <?php if (isset($_SESSION['flash_message'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($_SESSION['flash_message']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['flash_message']); ?>
    <?php endif; ?>

    <!-- Navbar -->
    <?php include 'templates/navbar.php'; ?>

    <!-- Header -->
    <header class="bg-light shadow-sm text-black text-center mb-4 py-2">
        <h1 class="display-5 fw-bold">Your Profile</h1>
        <p class="lead">Manage your account details and settings.</p>
    </header>

    <!-- Profile Form -->
    <main>
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card border-primary shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Profile Information</h5>
                        <form method="POST" action="profile.php">
                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" class="form-control" id="username" name="name" value="<?= htmlspecialchars($username); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" value="user@example.com" disabled>
                            </div>
                            <div class="d-flex justify-content-between">
                                <button type="submit" class="btn btn-primary">Update Profile</button>
                                <a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>

</div>

<!-- Footer -->
<?php include 'templates/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

</body>
</html>
