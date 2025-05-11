<?php
session_start();
include 'dbconnect.php'; // Include the database connection

if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] != 1) {
    header("Location: unauthorized.php");
    exit();
}

// Check if admin is logged in
include 'include/check_admin.php';

$error = '';
$success = '';

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {


    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Validate inputs
    if (empty($email) || empty($password)) {
        $error = "Email and password are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters long.";
    } else {
        // Check if email already exists in the users table
        $stmt = $conn->prepare("SELECT email FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $error = "Email already exists.";
        } else {
            // Insert new admin into the users table with type 'admin'
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $default_firstname = "New";
            $default_lastname = "Admin";
            $user_type = "admin";
            $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, email, password, type) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $default_firstname, $default_lastname, $email, $hashed_password, $user_type);

            if ($stmt->execute()) {
                $success = "New admin added successfully!";

            } else {
                $error = "Failed to add new admin. Please try again.";
            }
        }
        $stmt->close();
    }
    $conn->close();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Admin</title>
    <link rel="icon" type="image/x-icon" href="img/logo.png">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/form.css">
</head>
<body>
    <!-- Sidebar -->
    <?php include 'include/sidebar.php'; ?>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Header -->
        <?php include 'include/header.php'; ?>

        <div class="header mb-4">
            <h2>Add New Admin</h2>
            <div>Create a new admin account using email and password.</div>
        </div>

        <!-- Add New Admin Form -->
        <div class="form-container">
            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" placeholder="Enter email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" placeholder="Enter password" required>
                    <small class="form-text text-muted">Password must be at least 6 characters long.</small>
                </div>
                <button type="submit" class="btn btn-primary w-100">Add New Admin</button>
            </form>

            <div class="text-center mt-3">
                <a href="admin_details.php" class="btn btn-secondary"><i class="fas fa-arrow-left me-2"></i>Back to Admin List</a>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/hamburger.js"></script>
</body>
</html>