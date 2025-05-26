<?php
session_start();
include 'dbconnect.php';

// Check if admin is logged in
include 'include/check_admin.php';

$error = '';
$success = '';

$quizzes = $conn->query("
    SELECT q.quiz_id, q.title, m.model_name
    FROM quizzes q
    JOIN 3d_models m ON q.model_id = m.model_id
    WHERE m.status = 1
    ORDER BY q.quiz_id DESC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz Details</title>
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
    $page = 'view_quiz';
    include 'include/sidebar.php';
    ?>

    <!-- Main Content -->
    <div class="main-content" id="main-content">
        <!-- Header -->
        <?php include 'include/header.php'; ?>

        <div class="header mb-4">
            <h2>Quiz Details</h2>
            <div>List of all quizzes in the system.</div>
        </div>

        <!-- Messages -->
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <!-- Quiz List Table -->
        <div class="table-container">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Model</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($quizzes->num_rows > 0): ?>
                        <?php while ($row = $quizzes->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['title']); ?></td>
                                <td><?php echo htmlspecialchars($row['model_name']); ?></td>
                                <td>
                                    <a href="add_question.php?quiz_id=<?php echo $row['quiz_id']; ?>" class="btn btn-sm btn-primary me-2">
                                        <i class="fas fa-plus"></i> Add Question
                                    </a>
                                    <a href="update_quiz.php?quiz_id=<?php echo $row['quiz_id']; ?>" class="btn btn-sm btn-success me-2">
                                        <i class="fas fa-edit"></i> Update
                                    </a>
                                    <a href="admin_delete_quiz.php?quiz_id=<?php echo $row['quiz_id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this quiz?');">
                                        <i class="fas fa-trash"></i> Delete
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="text-center">No quizzes found.</td>
                        </tr>;
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