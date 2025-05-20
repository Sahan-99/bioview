<?php
session_start();
include 'dbconnect.php';

// Check if admin is logged in
include 'include/check_admin.php';

$error = '';
$success = '';

// Check if viewing another admin's profile is allowed (e.g., only super admin with user_id = 1)
if (isset($_GET['id']) && $_SESSION['user_id'] != 1) {
    header("Location: unauthorized.php");
    exit();
}

// Determine which user_id to fetch
$user_id = isset($_GET['id']) ? (int)$_GET['id'] : $_SESSION['user_id'];

// Fetch admin profile data
$stmt = $conn->prepare("SELECT email, first_name, last_name, profile_picture FROM users WHERE user_id = ? AND type = 'admin'");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $admin = $result->fetch_assoc();
} else {
    $error = "Admin profile not found.";
}
$stmt->close();
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
            <div>View and manage admin profile details.</div>
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
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <div class="card shadow-sm">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0"><i class="fas fa-user me-2"></i>Admin Profile</h5>
                            </div>
                            <div class="card-body">
                                <?php if ($admin['profile_picture']): ?>
                                    <div class="text-center mb-4">
                                        <img src="<?php echo htmlspecialchars($admin['profile_picture']); ?>" alt="Profile Picture" class="rounded-circle" style="width: 120px; height: 120px; object-fit: cover;">
                                    </div>
                                <?php endif; ?>
                                <div class="row mb-3">
                                    <div class="col-md-3 fw-bold">Username:</div>
                                    <div class="col-md-9"><?php echo htmlspecialchars($admin['username']); ?></div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-3 fw-bold">Email:</div>
                                    <div class="col-md-9"><?php echo htmlspecialchars($admin['email']); ?></div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-3 fw-bold">First Name:</div>
                                    <div class="col-md-9"><?php echo htmlspecialchars($admin['first_name'] ?: 'Not set'); ?></div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-3 fw-bold">Last Name:</div>
                                    <div class="col-md-9"><?php echo htmlspecialchars($admin['last_name'] ?: 'Not set'); ?></div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-3 fw-bold">Joined:</div>
                                    <div class="col-md-9"><?php echo date('F j, Y', strtotime($admin['created_at'])); ?></div>
                                </div>
                            </div>
                            
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