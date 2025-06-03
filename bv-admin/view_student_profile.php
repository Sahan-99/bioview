<?php
session_start();
include 'dbconnect.php';
include 'include/check_admin.php';

$error = '';
$success = '';
$user_id = isset($_GET['id']) ? (int)$_GET['id'] : $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT email, first_name, last_name, profile_picture, type FROM users WHERE user_id = ? AND type = 'student'");
if ($stmt === false) {
    $error = "Database error: " . $conn->error;
} else {
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $student = $result->fetch_assoc();
    } else {
        $error = "Student profile not found.";
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
    <title>Student Profile</title>
    <link rel="icon" type="image/x-icon" href="img/logo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    
</head>
<body>
    <?php $page = 'view_student_profile'; include 'include/sidebar.php'; ?>
    <div class="main-content" id="main-content">
        <?php include 'include/header.php'; ?>
        <div class="header mb-4">
            <h2>Student Profile</h2>
            <div>View and manage student profile.</div>
        </div>
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        <?php if (isset($student)): ?>
            <div class="container">
                <div class="profile-card card shadow-sm">
                    <div class="profile-header">
                        <div style="height: 75px;"></div>
                        <img src="<?php echo $student['profile_picture'] ? htmlspecialchars($student['profile_picture']) : 'img/default-profile.jpg'; ?>" alt="Profile Picture" class="profile-picture">
                        <h4 class="mt-3"><?php echo htmlspecialchars($student['first_name'] . ' ' . $student['last_name']); ?></h4>
                        <p class="text-muted"><?php echo htmlspecialchars($student['type']); ?></p>
                    </div>
                    <div class="profile-body">
                        <div class="profile-detail">
                            <span class="profile-label">Email</span>
                            <span class="profile-value"><?php echo htmlspecialchars($student['email']); ?></span>
                        </div>
                        <div class="profile-detail">
                            <span class="profile-label">First Name</span>
                            <span class="profile-value"><?php echo htmlspecialchars($student['first_name'] ?: 'Not set'); ?></span>
                        </div>
                        <div class="profile-detail">
                            <span class="profile-label">Last Name</span>
                            <span class="profile-value"><?php echo htmlspecialchars($student['last_name'] ?: 'Not set'); ?></span>
                        </div>
                        <div class="profile-detail">
                            <span class="profile-label">Type</span>
                            <span class="profile-value"><?php echo htmlspecialchars($student['type']); ?></span>
                        </div>
                        <div class="actions mt-4 d-flex justify-content-center gap-3">
                            <a href="mailto:<?php echo htmlspecialchars($student['email']); ?>" class="btn btn-outline-primary"><i class="fas fa-envelope"></i></a>
                        </div>
                        <div class="text-center mt-3">
                            <a href="students.php" class="btn btn-secondary"><i class="fas fa-arrow-left me-2"></i>Back to Student List</a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/hamburger.js"></script>
</body>
</html>