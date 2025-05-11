<?php
session_start();
include 'dbconnect.php';

// Check if admin is logged in
include 'include/check_admin.php';

$error = '';
$success = '';

// Handle soft deletion (set status to 0)
if (isset($_GET['delete_id'])) {
    // Allow only super admin to delete
    if ($_SESSION['user_id'] != 1) {
        header("Location: unauthorized.php");
        exit();
    }

    $delete_id = $_GET['delete_id'];

    // Check if the model exists
    $stmt = $conn->prepare("SELECT model_id FROM 3d_models WHERE model_id = ? AND status = 1");
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        // Update status to 0
        $stmt = $conn->prepare("UPDATE 3d_models SET status = 0 WHERE model_id = ?");
        $stmt->bind_param("i", $delete_id);
        
        if ($stmt->execute()) {
            $success = "3D model deleted successfully.";
        } else {
            $error = "Failed to delete 3D model.";
        }
    } else {
        $error = "3D model not found or already deleted.";
    }
    $stmt->close();
}

// Fetch all 3D models with status = 1
$query = "SELECT model_id, model_name, description FROM 3d_models WHERE status = 1";
$result = $conn->query($query);
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>3D Models Details</title>
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
    $page = 'view_3d_models';
    include 'include/sidebar.php';
    ?>

    <!-- Main Content -->
    <div class="main-content" id="main-content">
        <!-- Header -->
        <?php include 'include/header.php'; ?>

        <div class="header mb-4">
            <h2>3D Model Details</h2>
            <div>List of all active 3D models in the system.</div>
        </div>

        <!-- Messages -->
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <!-- 3D Model List Table -->
        <div class="table-container">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Model Name</th>
                        <th>Description</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['model_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['description']); ?></td>
                                <td>
                                    <a href="update_3d_model.php?id=<?php echo $row['model_id']; ?>" class="btn btn-sm btn-success me-2">
                                        <i class="fas fa-edit"></i> Update
                                    </a>
                                    <a href="3d_models.php?delete_id=<?php echo $row['model_id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to mark this 3D model as deleted?');">
                                        <i class="fas fa-trash"></i> Delete
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3" class="text-center">No active 3D models found.</td>
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