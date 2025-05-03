<?php
session_start();
include 'dbconnect.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

$admin_id = $_SESSION['admin_id'];
$error = '';
$success = '';

// Fetch admin details
$stmt = $conn->prepare("SELECT first_name, last_name, email, profile_picture FROM users WHERE user_id = ? AND type = 'admin'");
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $admin = $result->fetch_assoc();
    $firstname = $admin['first_name'];
    $lastname = $admin['last_name'];
    $email = $admin['email'];
    $profile_picture = $admin['profile_picture'] ?: 'https://via.placeholder.com/150';
} else {
    $error = "Admin not found.";
    $firstname = '';
    $lastname = '';
    $email = '';
    $profile_picture = 'https://via.placeholder.com/150';
}
$stmt->close();

// Handle profile update
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_firstname = trim($_POST['first_name']);
    $new_lastname = trim($_POST['last_name']);
    $new_email = trim($_POST['email']);
    $new_password = trim($_POST['password']);
    $profile_picture_path = $admin['profile_picture'] ?: '';

    // Validate inputs
    if (empty($new_firstname) || empty($new_lastname) || empty($new_email)) {
        $error = "First name, last name, and email are required.";
    } elseif (!filter_var($new_email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } else {
        // Check if email already exists (excluding current admin)
        $stmt = $conn->prepare("SELECT email FROM users WHERE email = ? AND user_id != ? AND type = 'admin'");
        $stmt->bind_param("si", $new_email, $admin_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $error = "Email already exists.";
        }
        $stmt->close();

        // Handle profile picture upload
        if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] != UPLOAD_ERR_NO_FILE) {
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
            $max_size = 5 * 1024 * 1024; // 5MB
            $upload_dir = 'uploads/';

            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }

            $file_type = $_FILES['profile_picture']['type'];
            $file_size = $_FILES['profile_picture']['size'];
            $file_tmp = $_FILES['profile_picture']['tmp_name'];
            $file_name = uniqid() . '_' . $_FILES['profile_picture']['name'];
            $file_path = $upload_dir . $file_name;

            if (!in_array($file_type, $allowed_types)) {
                $error = "Only JPEG, PNG, and GIF images are allowed.";
            } elseif ($file_size > $max_size) {
                $error = "Image size should not exceed 5MB.";
            } else {
                if (move_uploaded_file($file_tmp, $file_path)) {
                    // Delete old profile picture if it exists
                    if ($profile_picture_path && file_exists($profile_picture_path)) {
                        unlink($profile_picture_path);
                    }
                    $profile_picture_path = $file_path;
                } else {
                    $error = "Failed to upload the profile picture.";
                }
            }
        }

        if (empty($error)) {
            // Update admin details
            if (!empty($new_password)) {
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("UPDATE users SET first_name = ?, last_name = ?, email = ?, password = ?, profile_picture = ? WHERE user_id = ? AND type = 'admin'");
                $stmt->bind_param("sssssi", $new_firstname, $new_lastname, $new_email, $hashed_password, $profile_picture_path, $admin_id);
            } else {
                $stmt = $conn->prepare("UPDATE users SET first_name = ?, last_name = ?, email = ?, profile_picture = ? WHERE user_id = ? AND type = 'admin'");
                $stmt->bind_param("ssssi", $new_firstname, $new_lastname, $new_email, $profile_picture_path, $admin_id);
            }

            if ($stmt->execute()) {
                $success = "Profile updated successfully.";
                $firstname = $new_firstname;
                $lastname = $new_lastname;
                $email = $new_email;
                $profile_picture = $profile_picture_path ?: 'https://via.placeholder.com/150';
            } else {
                $error = "Failed to update profile. Please try again.";
            }
            $stmt->close();
        }
    }
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Profile</title>
    <link rel="icon" type="image/x-icon" href="img/logo.png">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/admin_profile.css">
</head>
<body>
    <!-- Sidebar -->
    <?php include 'include/sidebar.php'; ?>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Header -->
        <?php include 'include/header.php'; ?>
        
        <div class="header mb-4">
            <h2>Admin Profile</h2>
            <div>Manage your profile details below.</div>
        </div>

        <div class="profile-container">
            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>

            <div class="text-center mb-4">
                <img src="<?php echo htmlspecialchars($profile_picture); ?>" alt="Profile Picture" class="profile-img">
                <h3><?php echo htmlspecialchars($firstname . ' ' . $lastname); ?></h3>
                <p class="text-muted"><?php echo htmlspecialchars($email); ?></p>
            </div>

            <form method="POST" action="" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="first_name" class="form-label">First Name</label>
                    <input type="text" class="form-control" id="first_name" name="first_name" value="<?php echo htmlspecialchars($firstname); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="last_name" class="form-label">Last Name</label>
                    <input type="text" class="form-control" id="last_name" name="last_name" value="<?php echo htmlspecialchars($lastname); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">New Password (Optional)</label>
                    <input type="password" class="form-control" id="password" name="password" placeholder="Enter new password">
                </div>
                <div class="mb-3">
                    <label for="profile_picture" class="form-label">Profile Picture (Optional)</label>
                    <input type="file" class="form-control custom-file-input" id="profile_picture" name="profile_picture" accept="image/jpeg,image/png,image/gif">
                    <small class="form-text text-muted">Accepted formats: JPEG, PNG, GIF. Max size: 5MB.</small>
                </div>
                <button type="submit" class="btn btn-primary w-100">Update Profile</button>
            </form>
            <div class="text-center mt-3">
                <a href="logout.php" class="btn btn-danger"><i class="fas fa-sign-out me-2"></i> Logout</a>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/hamburger.js"></script>
</body>
</html>