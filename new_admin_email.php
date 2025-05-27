<?php
session_start();
include 'dbconnect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] != 1) {
    header("Location: unauthorized.php");
    exit();
}

// Check if admin is logged in
include 'include/check_admin.php';

// Enable error reporting (for debugging during development)
ini_set('display_errors', 1);
error_reporting(E_ALL);

$success = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $type = 'admin';

    if (empty($first_name) || empty($last_name) || empty($email)) {
        $error = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } else {
        // Check if email is already registered
        $stmt = $conn->prepare("SELECT email FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $error = "This email is already registered.";
        } else {
            // Generate token and expiry
            $token = bin2hex(random_bytes(32));
            $expiry = date("Y-m-d H:i:s", strtotime('+1 day'));

            // Insert user into DB
            $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, email, type, reset_token, token_expiry) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssss", $first_name, $last_name, $email, $type, $token, $expiry);

            if ($stmt->execute()) {
                // Send email with password setup link
                $link = "https://bioview.sahans.online/bv-admin/set_password.php?token=$token";
                $subject = "Welcome to the BioView Admin Panel";
                $body = "Hello $first_name $last_name,\n\nYou have been added as an admin in BioView.\nLogin email: $email\n\nPlease set your password using below link and loging the system using email and password:\n\n$link\n\nThis link is valid for 24 hours.\nPlease keep your credentials secure and contact us if you have any questions.\n\nBest regards,\nAdmin Team,\nBioView.";
                $headers = "From: support@bioview.sahans.online\r\n";
                $headers .= "Reply-To: support@bioview.sahans.online\r\n";
                $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

                if (mail($email, $subject, $body, $headers)) {
                    $success = "New admin added and email sent successfully!";
                } else {
                    $error = "Admin added, but email could not be sent.";
                }
            } else {
                $error = "Failed to add admin to the database.";
            }
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Admin</title>
    <link rel="icon" type="image/x-icon" href="img/logo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/form.css">
</head>
<body>
    <?php include 'include/sidebar.php'; ?>

    <div class="main-content">
        <?php include 'include/header.php'; ?>

        <div class="header mb-4">
            <h2>Add New Admin</h2>
            <div>Create a new admin account using email.</div>
        </div>

        <div class="form-container">
            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>

            <form method="POST" action="">
        <div class="mb-3">
            <label class="form-label">First Name</label>
            <input type="text" name="first_name" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Last Name</label>
            <input type="text" name="last_name" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Email Address</label>
            <input type="email" name="email" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary w-100">Add Admin</button>
    </form>

            <div class="text-center mt-3">
                <a href="admins.php" class="btn btn-secondary"><i class="fas fa-arrow-left me-2"></i>Back to Admin List</a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/hamburger.js"></script>
</body>
</html>
