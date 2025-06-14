<?php
session_start();
include 'dbconnect.php';

// Check if admin is logged in
include 'include/check_admin.php';

$error = '';
$success = '';

// Determine which user_id to fetch
$user_id = isset($_GET['id']) ? (int)$_GET['id'] : $_SESSION['user_id'];

// Fetch admin profile data
$stmt = $conn->prepare("SELECT email, first_name, last_name, profile_picture, type FROM users WHERE user_id = ? AND type = 'admin'");
if ($stmt === false) {
    $error = "Database error: " . $conn->error;
} else {
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $admin = $result->fetch_assoc();
    } else {
        $error = "Admin profile not found.";
    }
    $stmt->close();
}
$conn->close();
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
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/style.css">

</head>
<body>
    <!-- Include Sidebar -->
    <?php
    $page = 'view_admin_profile';
    include 'include/sidebar.php';
    ?>

    <!-- Main Content -->
    <div class="main-content" id="main-content">
        <!-- Header -->
        <?php include 'include/header.php'; ?>

        <div class="header mb-4">
            <h2>Admin Profile</h2>
            <div>View and manage admin profile.</div>
        </div>

        <!-- Messages -->
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <!-- Profile Card -->
        <?php if (isset($admin)): ?>
            <div class="container">
                <div class="profile-card card shadow-sm">
                    <div class="profile-header">
                        <div style="height: 75px;"></div> <!-- Spacer for profile picture overlap -->
                        <img src="<?php echo $admin['profile_picture'] ? htmlspecialchars($admin['profile_picture']) : 'img/default-profile.jpg'; ?>" alt="Profile Picture" class="profile-picture">
                        <h4 class="mt-3"><?php echo htmlspecialchars($admin['first_name'] . ' ' . $admin['last_name']); ?></h4>
                        <p class="text-muted"><?php echo htmlspecialchars($admin['type']); ?></p>
                    </div>
                    <div class="profile-body">
                        <div class="profile-detail">
                            <span class="profile-label">Email</span>
                            <span class="profile-value"><?php echo htmlspecialchars($admin['email']); ?></span>
                        </div>
                        <div class="profile-detail">
                            <span class="profile-label">First Name</span>
                            <span class="profile-value"><?php echo htmlspecialchars($admin['first_name'] ?: 'Not set'); ?></span>
                        </div>
                        <div class="profile-detail">
                            <span class="profile-label">Last Name</span>
                            <span class="profile-value"><?php echo htmlspecialchars($admin['last_name'] ?: 'Not set'); ?></span>
                        </div>
                        <div class="profile-detail">
                            <span class="profile-label"></i>Type</span>
                            <span class="profile-value"><?php echo htmlspecialchars($admin['type']); ?></span>
                        </div>
                        <div class="actions mt-4 d-flex justify-content-center gap-3">
                            <a href="mailto:<?php echo htmlspecialchars($admin['email']); ?>" class="btn btn-outline-primary"><i class="fas fa-envelope"></i></a>
                        </div>
                        <div class="text-center mt-3">
                <a href="admins.php" class="btn btn-secondary"><i class="fas fa-arrow-left me-2"></i>Back to Admin List</a>
            </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/hamburger.js"></script>
</body>
</html>