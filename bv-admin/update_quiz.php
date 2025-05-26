<?php
session_start();
include 'dbconnect.php';

// Check if admin is logged in
include 'include/check_admin.php';

$error = '';
$success = '';

$quiz_id = isset($_GET['quiz_id']) ? (int) $_GET['quiz_id'] : 0;

if (!$quiz_id) {
    $error = "Invalid quiz ID.";
} else {
    // Fetch quiz details
    $stmt = $conn->prepare("SELECT * FROM quizzes WHERE quiz_id = ?");
    $stmt->bind_param("i", $quiz_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $quiz = $result->fetch_assoc();
    } else {
        $error = "Quiz not found.";
    }
    $stmt->close();

    // Fetch all 3D models with status = 1
    $models = $conn->query("SELECT model_id, model_name FROM 3d_models WHERE status = 1");

    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $title = trim($_POST['title']);
        $model_id = (int) $_POST['model_id'];

        // Validate inputs
        if (empty($title)) {
            $error = "Quiz title is required.";
        } elseif ($model_id <= 0) {
            $error = "Please select a valid model.";
        } else {
            // Update quiz
            $stmt = $conn->prepare("UPDATE quizzes SET title = ?, model_id = ? WHERE quiz_id = ?");
            $stmt->bind_param("sii", $title, $model_id, $quiz_id);
            if ($stmt->execute()) {
                $success = "Quiz updated successfully.";
                // Redirect after a short delay to show success message
                header("refresh:2;url=quiz.php");
            } else {
                $error = "Failed to update quiz. Please try again.";
            }
            $stmt->close();
        }
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Quiz</title>
    <link rel="icon" type="image/x-icon" href="img/logo.png">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/form.css">
</head>

<body>
    <!-- Include Sidebar -->
    <?php
    $page = 'update_quiz';
    include 'include/sidebar.php';
    ?>

    <!-- Main Content -->
    <div class="main-content" id="main-content">
        <!-- Header -->
        <?php include 'include/header.php'; ?>

        <div class="header mb-4">
            <h2>Update Quiz</h2>
            <div>Update quiz details below.</div>
        </div>

        <!-- Messages -->
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <!-- Quiz Edit Form -->
        <?php if (isset($quiz) && empty($error)): ?>
            <div class="form-container">
                <div class="row justify-content-center">
                    <form method="post">
                        <div class="mb-3">
                            <label for="title" class="form-label">Quiz Title</label>
                            <input type="text" class="form-control" id="title" name="title"
                                value="<?php echo htmlspecialchars($quiz['title']); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="model_id" class="form-label">Model</label>
                            <select class="form-select" id="model_id" name="model_id" required>
                                <?php while ($m = $models->fetch_assoc()): ?>
                                    <option value="<?php echo $m['model_id']; ?>" <?php echo $quiz['model_id'] == $m['model_id'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($m['model_name']); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Update Quiz</button>
                    </form>
                    <div class="text-center mt-3">
                        <a href="quiz.php" class="btn btn-secondary"><i class="fas fa-arrow-left me-2"></i>Back to 3D quiz
                            List</a>
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