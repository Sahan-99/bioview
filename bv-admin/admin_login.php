<?php
session_start();
include 'dbconnect.php';

// If admin is already logged in, redirect to dashboard
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$error = '';
$success_message = isset($_SESSION['success_message']) ? $_SESSION['success_message'] : '';
unset($_SESSION['success_message']); // Clear the success message after displaying

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Basic validation
    if (empty($email) || empty($password)) {
        $error = "Email and password are required.";
    } else {
        // Fetch admin by email from users table with type 'admin'
        $stmt = $conn->prepare("SELECT user_id, first_name, last_name, password FROM users WHERE email = ? AND type = 'admin'");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $admin = $result->fetch_assoc();
            // Verify password
            if (password_verify($password, $admin['password'])) {
                // Set session variables
                $_SESSION['user_id'] = $admin['user_id'];
                $_SESSION['first_name'] = $admin['first_name'];
                $_SESSION['last_name'] = $admin['last_name'];
                // Redirect to dashboard
                header("Location: index.php");
                exit();
            } else {
                $error = "Invalid email or password.";
            }
        } else {
            $error = "Invalid email or password.";
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
    <meta http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <title>Admin Login</title>
    <link rel="icon" type="image/x-icon" href="img/logo.png">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/admin_login.css">
</head>
<body>
    <div class="login-container">
        <img src="img/logo.png" alt="Logo" class="logo">
        <h2>BioView Admin Login</h2>
        
        <?php if ($error): ?>
            div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        <?php if ($success_message): ?>
            <div class="alert alert-success"><?php echo $success_message; ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="email">Email</label>
                <div class="input-wrapper">
                    <input type="email" id="email" name="email" placeholder="Enter your email" required>
                    <i class="fas fa-envelope input-icon"></i>
                </div>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <div class="input-wrapper">
                    <input type="password" id="password" name="password" placeholder="Enter your password" required>
                    <i class="fas fa-eye password-toggle input-icon" onclick="togglePassword()"></i>
                </div>
            </div>
            <button type="submit">Login</button>
        </form>
    </div>
    
     <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Toggle password visibility
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.querySelector('.password-toggle');
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }

        // Prevent back button issues
        window.onload = function () {
            if (window.history.replaceState) {
                window.history.replaceState(null, null, window.location.href);
            }
        };
    </script>
</body>
</html>