<?php
session_start();
include 'dbconnect.php';

// Check if admin is logged in
include 'include/check_admin.php';

$error = '';
$success = '';

// Fetch model list
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
        // Insert quiz
        $stmt = $conn->prepare("INSERT INTO quizzes (title, model_id) VALUES (?, ?)");
        $stmt->bind_param("si", $title, $model_id);
        if ($stmt->execute()) {
            $quiz_id = $stmt->insert_id;
            $success = "Quiz created successfully. Redirecting to add questions...";
            // Redirect after a short delay to show success message
            header("refresh:2;url=add_question.php?quiz_id=$quiz_id");
        } else {
            $error = "Failed to create quiz. Please try again.";
        }
        $stmt->close();
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New Quiz</title>
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
    $page = 'add_quiz';
    include 'include/sidebar.php';
    ?>

    <!-- Main Content -->
    <div class="main-content" id="main-content">
        <!-- Header -->
        <?php include 'include/header.php'; ?>

        <div class="header mb-4">
            <h2>Create New Quiz</h2>
            <div>Create a new quiz and select a 3D model.</div>
        </div>

        <!-- Messages -->
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <!-- Quiz Creation Form -->
        <div class="form-container">
            <div class="row justify-content-center">
                <form method="post">
                    <div class="mb-3">
                        <label for="title" class="form-label">Quiz Title</label>
                        <input type="text" class="form-control" id="title" name="title" required>
                    </div>
                    <div class="mb-3">
                        <label for="model_id" class="form-label">3D Model</label>
                        <select class="form-select" id="model_id" name="model_id" required>
                            <option value="">-- Select Model --</option>
                            <?php while ($m = $models->fetch_assoc()): ?>
                                <option value="<?php echo $m['model_id']; ?>">
                                    <?php echo htmlspecialchars($m['model_name']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Create Quiz</button>
                </form>

                <div class="text-center mt-3">
                    <a href="quiz.php" class="btn btn-secondary"><i class="fas fa-arrow-left me-2"></i>Back
                        to Quiz List</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/hamburger.js"></script>
</body>

</html>