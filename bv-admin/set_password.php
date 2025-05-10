<?php
session_start(); // Start session for success message
include 'dbconnect.php';

// Enable error reporting (for debugging during development)
ini_set('display_errors', 1);
error_reporting(E_ALL);

$token = $_GET['token'] ?? '';
$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $token = $_POST['token'];
    $password = trim($_POST['password']);
    $confirm = trim($_POST['confirm_password']);

    if (empty($password) || strlen($password) < 6) {
        $error = "Password must be at least 6 characters long.";
    } elseif ($password !== $confirm) {
        $error = "Passwords do not match.";
    } else {
        if (!$conn) {
            $error = "Database connection failed.";
        } else {
            $stmt = $conn->prepare("SELECT user_id FROM users WHERE reset_token = ? AND token_expiry > NOW()");
            if ($stmt === false) {
                $error = "Prepare failed: " . $conn->error;
            } else {
                $stmt->bind_param("s", $token);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result && $result->num_rows === 1) {
                    $user = $result->fetch_assoc();
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                    $stmt = $conn->prepare("UPDATE users SET password = ?, reset_token = NULL, token_expiry = NULL WHERE user_id = ?");
                    if ($stmt === false) {
                        $error = "Prepare failed: " . $conn->error;
                    } else {
                        $stmt->bind_param("si", $hashed_password, $user['user_id']);
                        if ($stmt->execute()) {
                            // Store success message in session and redirect
                            $_SESSION['success_message'] = "Your password has been set successfully, Login Now.";
                            header("Location: admin_login.php");
                            exit();
                        } else {
                            $error = "Failed to set password. Try again.";
                        }
                    }
                } else {
                    $error = "Invalid or expired token.";
                }
                $stmt->close();
            }
        }
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <title>Set Password</title>
    <link rel="icon" type="image/x-icon" href="img/logo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/admin_login.css">
</head>
<body>
    <div class="login-container">
        <img src="img/logo.png" alt="Logo" class="logo">
        <h2>BioView Set Password</h2>
        
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php elseif ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <?php if (empty($success)): ?>
        <form method="POST" action="">
            <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
            <div class="form-group">
                <label for="password">New Password</label>
                <div class="input-wrapper">
                    <input type="password" id="password" name="password" placeholder="Enter new password" required minlength="6">
                    <i class="fas fa-eye password-toggle input-icon" onclick="togglePassword('password')"></i>
                </div>
            </div>
            <div class="form-group">
                <label for="confirm_password">Confirm Password</label>
                <div class="input-wrapper">
                    <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm your password" required minlength="6">
                    <i class="fas fa-eye password-toggle input-icon" onclick="togglePassword('confirm_password')"></i>
                </div>
            </div>
            <button type="submit">Set Password</button>
        </form>
        <?php endif; ?>
    </div>

	 <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Toggle password visibility for specified input
        function togglePassword(inputId) {
            const passwordInput = document.getElementById(inputId);
            const toggleIcon = passwordInput.nextElementSibling;
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