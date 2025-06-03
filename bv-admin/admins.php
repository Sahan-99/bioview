<?php
session_start();
include 'dbconnect.php'; // Include the database connection

// Check if admin is logged in
include 'include/check_admin.php';

$error = '';
$success = '';

// Handle admin deletion
if (isset($_GET['delete_id'])) {
    // Restrict deletion to super admin (user_id = 1)
    if ($_SESSION['user_id'] != 1) {
        header("Location: unauthorized.php");
        exit();
    }

    $delete_id = (int)$_GET['delete_id'];
    // Prevent super admin from deleting themselves
    if ($delete_id == $_SESSION['user_id']) {
        $error = "You cannot delete your own account.";
    } else {
        // Check if the admin exists
        $stmt = $conn->prepare("SELECT user_id FROM users WHERE user_id = ? AND type = 'admin'");
        if ($stmt === false) {
            $error = "Database error: " . $conn->error;
        } else {
            $stmt->bind_param("i", $delete_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                // Perform hard deletion since status column doesn't exist
                $stmt = $conn->prepare("DELETE FROM users WHERE user_id = ?");
                $stmt->bind_param("i", $delete_id);

                if ($stmt->execute()) {
                    $success = "Admin deleted successfully.";
                } else {
                    $error = "Failed to delete admin.";
                }
            } else {
                $error = "Admin not found.";
            }
            $stmt->close();
        }
    }
}

// Fetch all admins
$query = "SELECT user_id, first_name, last_name, email, profile_picture FROM users WHERE type='admin'";
$result = $conn->query($query);
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admins Details</title>
    <link rel="icon" type="image/x-icon" href="img/logo.png">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        .table-container {
            max-width: 100%;
            overflow-x: auto;
        }
        .table th, .table td {
            vertical-align: middle;
        }
        .table img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
        }
        .btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
        }
    </style>
    <?php $page = "admins"; ?>
</head>
<body>
    <!-- Sidebar -->
    <?php include 'include/sidebar.php'; ?>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Header -->
        <?php include 'include/header.php'; ?>

        <div class="header mb-4">
            <h2>Admin Details</h2>
            <div>List of all administrators in the system.</div>
        </div>

        <!-- Messages -->
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <!-- Admin List Table -->
        <div class="table-container">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Profile Picture</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['email']); ?></td>
                                <td>
                                    <img src="<?php echo htmlspecialchars($row['profile_picture'] ?: 'img/default-profile.jpg'); ?>" alt="Profile Picture">
                                </td>
                                <td>
                                    <a href="view_admin_profile.php?id=<?php echo $row['user_id']; ?>" class="btn btn-sm btn-primary me-2">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                    <a href="admins.php?delete_id=<?php echo $row['user_id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this admin?');">
                                        <i class="fas fa-trash"></i> Delete
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="text-center">No admins found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/hamburger.js"></script>
</body>
</html>