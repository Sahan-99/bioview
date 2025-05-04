<?php
session_start();
include 'dbconnect.php';

// Check if admin is logged in
include 'include/check_admin.php';

$error = '';
$success = '';

// Handle soft deletion (set status to 0)
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];

    // Check if the image exists and has status = 1
    $stmt = $conn->prepare("SELECT image_id FROM scanned_images WHERE image_id = ? AND status = 1");
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Update status to 0
        $stmt = $conn->prepare("UPDATE scanned_images SET status = 0 WHERE image_id = ?");
        $stmt->bind_param("i", $delete_id);

        if ($stmt->execute()) {
            $success = "Image deleted successfully.";
        } else {
            $error = "Failed delete image.";
        }
    } else {
        $error = "Image not found or already deleted.";
    }
    $stmt->close();
}

// Fetch scanned images with corresponding model names where status = 1
$query = "SELECT si.image_id, si.image_name, si.description, m.model_name 
          FROM scanned_images si 
          LEFT JOIN 3d_models m ON si.model_id = m.model_id 
          WHERE si.status = 1";
$result = $conn->query($query);
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scanned Images details</title>
    <link rel="icon" type="image/x-icon" href="img/logo.png">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/style.css">
    <?php $page = 'view_images'; ?>
</head>
<body>
    <!-- Sidebar -->
    <?php include 'include/sidebar.php'; ?>

    <!-- Main Content -->
    <div class="main-content" id="main-content">
        <!-- Header -->
        <?php include 'include/header.php'; ?>

        <div class="header mb-4">
            <h2>Scanned Images</h2>
            <div>List of all active scanned images in the system.</div>
        </div>

        <!-- Messages -->
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <!-- Scanned Images Table -->
        <div class="table-container">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Image Name</th>
                        <th>Description</th>
                        <th>Model Name</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['image_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['description']); ?></td>
                                <td><?php echo htmlspecialchars($row['model_name'] ?: 'N/A'); ?></td>
                                <td>
                                    <a href="update_image.php?id=<?php echo $row['image_id']; ?>" class="btn btn-sm btn-success me-2">
                                        <i class="fas fa-edit"></i> Update
                                    </a>
                                    <a href="images.php?delete_id=<?php echo $row['image_id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to mark this image as deleted?');">
                                        <i class="fas fa-trash"></i> Delete
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="text-center">No active scanned images found.</td>
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