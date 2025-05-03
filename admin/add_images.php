<?php
session_start();
include 'dbconnect.php';

// Check if admin is logged in
include 'include/check_admin.php';

$error = '';
$success = '';

// Fetch model names for dropdown
$models = [];
$stmt = $conn->prepare("SELECT model_id, model_name FROM 3d_models");
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $models[$row['model_id']] = $row['model_name'];
}
$stmt->close();

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $image_name = trim($_POST['image_name']);
    $description = trim($_POST['description']);
    $model_id = $_POST['model_id'];
    $image_path = '';

    // Validate inputs
    if (empty($image_name)) {
        $error = "Image name is required.";
    } elseif (empty($description)) {
        $error = "Description is required.";
    } elseif (!array_key_exists($model_id, $models)) {
        $error = "Invalid model selection.";
    } else {
        // Handle image file upload
        if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] != UPLOAD_ERR_NO_FILE) {
            $max_size = 5 * 1024 * 1024; // 5MB
            $upload_dir = 'uploads/images/'; // Store in uploads/images/

            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true); // Create the folder if it doesn't exist
            }

            $file_size = $_FILES['image_file']['size'];
            $file_tmp = $_FILES['image_file']['tmp_name'];
            $file_name = uniqid() . '_' . basename($_FILES['image_file']['name']);
            $file_path = $upload_dir . $file_name;

            if ($file_size > $max_size) {
                $error = "Image size should not exceed 5MB.";
            } else {
                if (move_uploaded_file($file_tmp, $file_path)) {
                    $image_path = $file_path;
                } else {
                    $error = "Failed to upload the image.";
                }
            }
        } else {
            $error = "Please upload an image file.";
        }

        if (empty($error)) {
            // Insert into scanned_images table
            $stmt = $conn->prepare("INSERT INTO scanned_images (image_name, description, model_id, file_path) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssis", $image_name, $description, $model_id, $image_path);

            if ($stmt->execute()) {
                $success = "Image added successfully.";
                // Redirect to images.php after success
                header("Location: images.php");
                exit();
            } else {
                $error = "Failed to add image to database.";
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
    <title>Add Image</title>
    <link rel="icon" type="image/x-icon" href="img/logo.png">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/form.css">
    <?php $page = "add_images"; ?>
</head>
<body>
    <!-- Sidebar -->
    <?php include 'include/sidebar.php'; ?>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Header -->
        <?php include 'include/header.php'; ?>

        <div class="header mb-4">
            <h2>Add Image</h2>
            <div>Upload a new scanned image.</div>
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
                    <label for="image_name" class="form-label">Image Name</label>
                    <input type="text" class="form-control" id="image_name" name="image_name" placeholder="Enter image name" required>
                </div>
                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control" id="description" name="description" placeholder="Enter image description" rows="3" required></textarea>
                </div>
                <div class="mb-3">
                    <label for="model_id" class="form-label">Select Model</label>
                    <select class="form-control" id="model_id" name="model_id" required>
                        <option value="">Select a model</option>
                        <?php foreach ($models as $id => $name): ?>
                            <option value="<?php echo htmlspecialchars($id); ?>"><?php echo htmlspecialchars($name); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="image_file" class="form-label">Image File</label>
                    <input type="file" class="form-control" id="image_file" name="image_file" accept="image/*" required>
                    <small class="form-text text-muted">Max size: 5MB.</small>
                </div>
                <button type="submit" class="btn btn-primary w-100">Upload Image</button>
            </form>
            <div class="text-center mt-3">
                <a href="images.php" class="btn btn-secondary"><i class="fas fa-arrow-left me-2"></i>Back to Image List</a>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/hamburger.js"></script>
</body>
</html>