<?php
session_start();
include 'dbconnect.php';

// Check if admin is logged in
include 'include/check_admin.php';

$error = '';
$success = '';

$search = '';
$sql = "
    SELECT 
        CONCAT(u.first_name, ' ', u.last_name) AS name,
        q.title AS quiz_title,
        qa.total_questions,
        qa.score,
        qa.attempt_time
    FROM 
        quiz_attempts qa
    JOIN 
        users u ON qa.user_id = u.user_id
    JOIN 
        quizzes q ON qa.quiz_id = q.quiz_id
";

// Handle search
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['search'])) {
    $search = trim($_POST['search']);
    if (!empty($search)) {
        $search = $conn->real_escape_string($search);
        $sql .= " WHERE CONCAT(u.first_name, ' ', u.last_name) LIKE '%$search%' OR q.title LIKE '%$search%'";
    }
}

$sql .= " ORDER BY qa.attempt_time DESC";
$result = $conn->query($sql);
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz Attempt</title>
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
    $page = 'view_quiz_attempts';
    include 'include/sidebar.php';
    ?>

    <!-- Main Content -->
    <div class="main-content" id="main-content">
        <!-- Header -->
        <?php include 'include/header.php'; ?>

        <div class="header mb-4">
            <h2>Quiz Attempt</h2>
            <div>List of all quiz attempts in the system.</div>
        </div>

        <!-- Search Bar -->
        <div class="container mb-4">
            <form method="post" class="d-flex">
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                    <input type="text" class="form-control" name="search" placeholder="Search by user name or quiz title" value="<?php echo htmlspecialchars($search); ?>">
                    <button type="submit" class="btn btn-secondary">Search</button>
                </div>
            </form>
        </div>

        <!-- Messages -->
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <!-- Quiz Attempts Table -->
        <div class="table-container">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Quiz Title</th>
                        <th>Total Questions</th>
                        <th>Score</th>
                        <th>Attempt Time</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result && $result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['name']); ?></td>
                                <td><?php echo htmlspecialchars($row['quiz_title']); ?></td>
                                <td><?php echo $row['total_questions']; ?></td>
                                <td><?php echo $row['score']; ?></td>
                                <td><?php echo htmlspecialchars($row['attempt_time']); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center">No quiz attempts found.</td>
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