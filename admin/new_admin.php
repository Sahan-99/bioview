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

    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/new_admin.css">

</head>
<body>
    <?php
    include 'dbconnect.php'; // Include the database connection
    session_start();

    // Check if admin is logged in
    if (!isset($_SESSION['admin_id'])) {
        header("Location: admin_login.php");
        exit();
    }

    $admin_id = $_SESSION['admin_id'];

    // Fetch current admin details for the header
    $stmt = $conn->prepare("SELECT firstname, lastname, profile_picture FROM admin WHERE admin_id = ?");
    $stmt->bind_param("i", $admin_id);
    $stmt->execute();
    $admin_result = $stmt->get_result();

    if ($admin_result->num_rows > 0) {
        $admin_data = $admin_result->fetch_assoc();
        $firstname = $admin_data['firstname'] ?? 'Admin';
        $lastname = $admin_data['lastname'] ?? '';
        $profile_picture = $admin_data['profile_picture'] ?? 'https://via.placeholder.com/40';
    } else {
        $firstname = 'Admin';
        $lastname = '';
        $profile_picture = 'https://via.placeholder.com/40';
    }
    $stmt->close();

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
            // Check if email already exists
            $stmt = $conn->prepare("SELECT email FROM admin WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $error = "Email already exists.";
            } else {
                // Insert new admin into the database
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $default_firstname = "New";
                $default_lastname = "Admin";
                $stmt = $conn->prepare("INSERT INTO admin (firstname, lastname, email, password) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("ssss", $default_firstname, $default_lastname, $email, $hashed_password);

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

    <!-- Sidebar -->
    <?php include 'include/sidebar.php'; ?>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Header -->
        <?php include 'include/header.php'; ?>

        <div class="header mb-4">
            <h1>Add New Admin</h1>
            <p>Create a new admin account using email and password.</p>
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
                <button type="submit" class="btn btn-primary w-100">Add Admin</button>
            </form>
            <div class="text-center mt-3">
                <a href="admin_details.php" class="btn btn-secondary">Back to Admin List</a>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>