<?php
session_start();
include 'dbconnect.php';

// Check if admin is logged in
include 'include/check_admin.php';

$error = '';
$success = '';

// Check if ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: audio.php");
    exit();
}

$audio_id = $_GET['id'];

// Fetch model names for dropdown
$models = [];
$stmt = $conn->prepare("SELECT model_id, model_name FROM 3d_models");
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $models[$row['model_id']] = $row['model_name'];
}
$stmt->close();

// Fetch the existing audio details
$stmt = $conn->prepare("SELECT audio_name, description, model_id, file_path FROM audio WHERE audio_id = ? AND status = 1");
$stmt->bind_param("i", $audio_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    $error = "Audio not found or has been  deleted.";
    $audio_name = '';
    $description = '';
    $model_id = '';
    $audio_path = '';
} else {
    $row = $result->fetch_assoc();
    $audio_name = $row['audio_name'];
    $description = $row['description'];
    $model_id = $row['model_id'];
    $audio_path = $row['file_path'];
}
$stmt->close();

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_audio_name = trim($_POST['audio_name']);
    $new_description = trim($_POST['description']);
    $new_model_id = $_POST['model_id'];
    $new_audio_path = $audio_path;

    // Validate inputs
    if (empty($new_audio_name)) {
        $error = "Audio name is required.";
    } elseif (empty($new_description)) {
        $error = "Description is required.";
    } elseif (!array_key_exists($new_model_id, $models)) {
        $error = "Invalid model selection.";
    } else {
        // Handle audio file upload (if a new file is provided)
        if (isset($_FILES['audio_file']) && $_FILES['audio_file']['error'] != UPLOAD_ERR_NO_FILE) {
            $max_size = 10 * 1024 * 1024; // 10MB
            $upload_dir = 'uploads/audio/';

            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }

            $file_size = $_FILES['audio_file']['size'];
            $file_tmp = $_FILES['audio_file']['tmp_name'];
            $file_name = uniqid() . '_' . basename($_FILES['audio_file']['name']);
            $file_path = $upload_dir . $file_name;

            if ($file_size > $max_size) {
                $error = "Audio size should not exceed 10MB.";
            } else {
                if (move_uploaded_file($file_tmp, $file_path)) {
                    // Delete the old file if it exists
                    if ($new_audio_path && file_exists($new_audio_path)) {
                        unlink($new_audio_path);
                    }
                    $new_audio_path = $file_path;
                } else {
                    $error = "Failed to upload the audio.";
                }
            }
        }

        if (empty($error)) {
            // Update the audio in the database
            $stmt = $conn->prepare("UPDATE audio SET audio_name = ?, description = ?, model_id = ?, file_path = ? WHERE audio_id = ?");
            $stmt->bind_param("ssisi", $new_audio_name, $new_description, $new_model_id, $new_audio_path, $audio_id);

            if ($stmt->execute()) {
                $success = "Audio updated successfully.";
                header("Location: audio.php");
                exit();
            } else {
                $error = "Failed to update audio.";
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
    <title>Update Audio</title>
    <link rel="icon" type="image/x-icon" href="img/logo.png">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/form.css">
    <?php $page = "update_audio"; ?>
</head>
<body>
    <!-- Sidebar -->
    <?php include 'include/sidebar.php'; ?>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Header -->
        <?php include 'include/header.php'; ?>

        <div class="header mb-4">
            <h2>Update Audio</h2>
            <div>Modify the details of the audio file.</div>
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
                    <label for="audio_name" class="form-label">Audio Name</label>
                    <input type="text" class="form-control" id="audio_name" name="audio_name" value="<?php echo htmlspecialchars($audio_name); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control" id="description" name="description" rows="3" required><?php echo htmlspecialchars($description); ?></textarea>
                </div>
                <div class="mb-3">
                    <label for="model_id" class="form-label">Select Model</label>
                    <select class="form-control" id="model_id" name="model_id" required>
                        <option value="">Select a model</option>
                        <?php foreach ($models as $id => $name): ?>
                            <option value="<?php echo htmlspecialchars($id); ?>" <?php echo $id == $model_id ? 'selected' : ''; ?>><?php echo htmlspecialchars($name); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="audio_file" class="form-label">Audio File (Optional)</label>
                    <input type="file" class="form-control" id="audio_file" name="audio_file" accept="audio/*">
                    <small class="form-text text-muted">Max size: 10MB. Leave blank to keep the existing file.</small>
                </div>
                <button type="submit" class="btn btn-primary w-100">Update Audio</button>
            </form>
            <div class="text-center mt-3">
                <a href="audio.php" class="btn btn-secondary"><i class="fas fa-arrow-left me-2"></i>Back to Audio List</a>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/hamburger.js"></script>
</body>
</html>