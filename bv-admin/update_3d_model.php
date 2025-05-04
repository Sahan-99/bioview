<?php
session_start();
include 'dbconnect.php';

// Check if admin is logged in
include 'include/check_admin.php';

$error = '';
$success = '';

// Check if ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: 3d_model_details.php");
    exit();
}

$model_id = $_GET['id'];

// Fetch the existing 3D model details
$stmt = $conn->prepare("SELECT model_name, description, file_path FROM 3d_models WHERE model_id = ?");
$stmt->bind_param("i", $model_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    $error = "3D model not found.";
    $model_name = '';
    $description = '';
    $model_path = '';
} else {
    $row = $result->fetch_assoc();
    $model_name = $row['model_name'];
    $description = $row['description'];
    $model_path = $row['file_path'];
}
$stmt->close();

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_model_name = trim($_POST['model_name']);
    $new_description = trim($_POST['description']);
    $new_model_path = $model_path;

    // Validate inputs
    if (empty($new_model_name)) {
        $error = "Model name is required.";
    } elseif (empty($new_description)) {
        $error = "Description is required.";
    } else {
        // Handle model file upload (if a new file is provided)
        if (isset($_FILES['model_file']) && $_FILES['model_file']['error'] != UPLOAD_ERR_NO_FILE) {
            $max_size = 10 * 1024 * 1024; // 10MB
            $upload_dir = 'uploads/model/';

            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }

            $file_size = $_FILES['model_file']['size'];
            $file_tmp = $_FILES['model_file']['tmp_name'];
            $file_name = uniqid() . '_' . basename($_FILES['model_file']['name']);
            $file_path = $upload_dir . $file_name;

            if ($file_size > $max_size) {
                $error = "Model size should not exceed 10MB.";
            } else {
                if (move_uploaded_file($file_tmp, $file_path)) {
                    // Delete the old file if it exists
                    if ($new_model_path && file_exists($new_model_path)) {
                        unlink($new_model_path);
                    }
                    $new_model_path = $file_path;
                } else {
                    $error = "Failed to upload the 3D model.";
                }
            }
        }

        if (empty($error)) {
            // Update the 3D model in the database
            $stmt = $conn->prepare("UPDATE 3d_models SET model_name = ?, description = ?, file_path = ? WHERE model_id = ?");
            $stmt->bind_param("sssi", $new_model_name, $new_description, $new_model_path, $model_id);

            if ($stmt->execute()) {
                $success = "3D model updated successfully.";
                header("Location: 3d_models.php");
                exit();
            } else {
                $error = "Failed to update 3D model.";
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
    <title>Update 3D Model</title>
    <link rel="icon" type="image/x-icon" href="img/logo.png">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/form.css">
    <?php $page = "update_3d_models"; ?>
</head>
<body>
    <!-- Sidebar -->
    <?php include 'include/sidebar.php'; ?>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Header -->
        <?php include 'include/header.php'; ?>

        <div class="header mb-4">
            <h2>Update 3D Model</h2>
            <div>Modify the details of the 3D model.</div>
        </div>

        <div class="form-container">
            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            <form method="POST" action="" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="model_name" class="form-label">Model Name</label>
                    <input type="text" class="form-control" id="model_name" name="model_name" value="<?php echo htmlspecialchars($model_name); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control" id="description" name="description" rows="3" required><?php echo htmlspecialchars($description); ?></textarea>
                </div>
                <div class="mb-3">
                    <label for="model_file" class="form-label">3D Model File (.glb, Optional)</label>
                    <input type="file" class="form-control" id="model_file" name="model_file" accept=".glb">
                    <small class="form-text text-muted">Max size: 10MB. Leave blank to keep the existing file.</small>
                </div>
                <button type="submit" class="btn btn-primary w-100">Update 3D Model</button>
            </form>
            <div class="text-center mt-3">
                <a href="3d_models.php" class="btn btn-secondary"><i class="fas fa-arrow-left me-2"></i>Back to 3D Model List</a>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/hamburger.js"></script>
</body>
</html>