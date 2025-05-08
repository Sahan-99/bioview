<?php
session_start();
include 'dbconnect.php';

// Check if admin is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Fetch admin details
$stmt = $conn->prepare("SELECT first_name, last_name, email, password, profile_picture FROM users WHERE user_id = ? AND type = 'admin'");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$admin = $result->fetch_assoc();
$stmt->close();

// Set profile picture path (use default if not set or file doesn't exist)
$profile_picture = !empty($admin['profile_picture']) && file_exists($admin['profile_picture']) 
    ? $admin['profile_picture'] 
    : 'images/default_profile.jpg';

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $password = trim($_POST['password']);

    // Validate password
    if (empty($password)) {
        $error = "Password is required.";
    } else {
        // Verify password
        if (password_verify($password, $admin['password'])) {
            // Update session to indicate active status
            $_SESSION['last_activity'] = time();
            header("Location: index.php");
            exit();
        } else {
            $error = "Invalid password.";
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
    <title>Bioview Lock Screen</title>
    <link rel="icon" type="image/x-icon" href="img/logo.png">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/lock_screen.css">
</head>
<body>
    <div class="lock-screen-container">
        <img src="img/logo.png" alt="Logo" class="logo">
        <h2>BioView Lock Screen</h2>
        <p class="description">Your session is locked due to inactivity. Please enter your password to continue.</p>
        
        <div class="profile-container">
            <img src="<?php echo htmlspecialchars($profile_picture); ?>" alt="Profile" class="rounded-circle me-2" style="width: 80px;">
        </div>
        <div class="admin-name"><?php echo htmlspecialchars($admin['first_name'] . ' ' . $admin['last_name']); ?></div>
        <div class="admin-email"><?php echo htmlspecialchars($admin['email']); ?></div>
        
        <?php if ($error): ?>
            <div class="alert" role="alert">
                <p><?php echo $error; ?></p>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                
                <div class="input-wrapper">
                    <input type="password" id="password" name="password" placeholder="Enter your password" required>
                    <i class="fas fa-eye password-toggle input-icon" onclick="togglePassword()"></i>
                </div>
            </div>
            <button type="submit">Unlock</button>
        </form>
    </div>

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