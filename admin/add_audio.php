<?php
session_start();
include 'dbconnect.php';

// Check if admin is logged in
include 'include/check_admin.php';

$error = '';
$success = '';

// Fetch model names for dropdown
$models = [];
$stmt = $conn->prepare("SELECT model_id, model_name FROM 3d_models WHERE status=1");
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $models[$row['model_id']] = $row['model_name'];
}
$stmt->close();

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $audio_name = trim($_POST['audio_name']);
    $description = trim($_POST['description']);
    $model_id = $_POST['model_id'];
    $audio_path = '';

    // Validate inputs
    if (empty($audio_name)) {
        $error = "Audio name is required.";
    } elseif (empty($description)) {
        $error = "Description is required.";
    } elseif (!array_key_exists($model_id, $models)) {
        $error = "Invalid model selection.";
    } else {
        // Handle audio file upload
        if (isset($_FILES['audio_file']) && $_FILES['audio_file']['error'] != UPLOAD_ERR_NO_FILE) {
            $max_size = 10 * 1024 * 1024; // 10MB
            $upload_dir = 'uploads/audio/'; // Store in uploads/audio/

            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true); // Create the folder if it doesn't exist
            }

            $file_size = $_FILES['audio_file']['size'];
            $file_tmp = $_FILES['audio_file']['tmp_name'];
            $file_name = uniqid() . '_' . basename($_FILES['audio_file']['name']);
            $file_path = $upload_dir . $file_name;

            if ($file_size > $max_size) {
                $error = "Audio size should not exceed 10MB.";
            } else {
                if (move_uploaded_file($file_tmp, $file_path)) {
                    $audio_path = $file_path;
                } else {
                    $error = "Failed to upload the audio.";
                }
            }
        } else {
            $error = "Please upload an audio file.";
        }

        if (empty($error)) {
            // Insert into audio table with status set to 1
            $status = 1;
            $stmt = $conn->prepare("INSERT INTO audio (audio_name, description, model_id, file_path, status) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("ssisi", $audio_name, $description, $model_id, $audio_path, $status);

            if ($stmt->execute()) {
                $success = "Audio added successfully.";
                // Redirect to audio.php after success
                header("Location: audio.php");
                exit();
            } else {
                $error = "Failed to add audio to database.";
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
    <title>Add Audio</title>
    <link rel="icon" type="image/x-icon" href="img/logo.png">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/form.css">
    <?php $page = "add_audio"; ?>
</head>
<body>
    <!-- Sidebar -->
    <?php include 'include/sidebar.php'; ?>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Header -->
        <?php include 'include/header.php'; ?>

        <div class="header mb-4">
            <h2>Add Audio</h2>
            <div>Upload a new audio file.</div>
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
                    <input type="text" class="form-control" id="audio_name" name="audio_name" placeholder="Enter audio name" required>
                </div>
                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control" id="description" name="description" placeholder="Enter audio description" rows="3" required></textarea>
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
                    <label for="audio_file" class="form-label">Audio File</label>
                    <input type="file" class="form-control" id="audio_file" name="audio_file" accept="audio/*" required>
                    <small class="form-text text-muted">Max size: 10MB.</small>
                </div>
                <button type="submit" class="btn btn-primary w-100">Upload Audio</button>
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