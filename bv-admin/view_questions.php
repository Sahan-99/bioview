<?php
session_start();
include 'dbconnect.php';

// Check if admin is logged in
include 'include/check_admin.php';

$error = '';
$success = '';

$quiz_id = isset($_GET['quiz_id']) ? (int)$_GET['quiz_id'] : 0;

// Fetch quiz title
$quiz_title = '';
if ($quiz_id) {
    $stmt = $conn->prepare("SELECT title FROM quizzes WHERE quiz_id = ?");
    $stmt->bind_param("i", $quiz_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $quiz = $result->fetch_assoc();
        $quiz_title = $quiz['title'];
    } else {
        $error = "Quiz not found.";
    }
    $stmt->close();
} else {
    $error = "Invalid quiz ID.";
}

// Handle question deletion
if (isset($_GET['delete_id']) && $_SESSION['user_id'] == 1) {
    $delete_id = (int)$_GET['delete_id'];
    $stmt = $conn->prepare("SELECT question_id FROM questions WHERE question_id = ? AND quiz_id = ?");
    $stmt->bind_param("ii", $delete_id, $quiz_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Check if questions table has a status column for soft deletion
        $check_status = $conn->query("SHOW COLUMNS FROM questions LIKE 'status'");
        if ($check_status->num_rows > 0) {
            // Soft deletion
            $stmt = $conn->prepare("UPDATE questions SET status = 0 WHERE question_id = ?");
            $stmt->bind_param("i", $delete_id);
        } else {
            // Hard deletion
            $stmt = $conn->prepare("DELETE FROM questions WHERE question_id = ?");
            $stmt->bind_param("i", $delete_id);
        }

        if ($stmt->execute()) {
            $success = "Question deleted successfully.";
            // Delete associated answers
            $stmt_answers = $conn->prepare("DELETE FROM answers WHERE question_id = ?");
            $stmt_answers->bind_param("i", $delete_id);
            $stmt_answers->execute();
            $stmt_answers->close();
        } else {
            $error = "Failed to delete question.";
        }
    } else {
        $error = "Question not found.";
    }
    $stmt->close();
}

// Fetch questions and answers
$questions = [];
if (empty($error) || $success) {
    $stmt = $conn->prepare("
        SELECT q.question_id, q.question_text
        FROM questions q
        WHERE q.quiz_id = ?
        ORDER BY q.question_id
    ");
    $stmt->bind_param("i", $quiz_id);
    $stmt->execute();
    $question_result = $stmt->get_result();

    while ($question = $question_result->fetch_assoc()) {
        $question_id = $question['question_id'];
        $stmt_answers = $conn->prepare("
            SELECT answer_id, answer_text, is_correct
            FROM answers
            WHERE question_id = ?
            ORDER BY answer_id
        ");
        $stmt_answers->bind_param("i", $question_id);
        $stmt_answers->execute();
        $answer_result = $stmt_answers->get_result();
        $answers = [];
        while ($answer = $answer_result->fetch_assoc()) {
            $answers[] = $answer;
        }
        $question['answers'] = $answers;
        $questions[] = $question;
        $stmt_answers->close();
    }
    $stmt->close();
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Questions</title>
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
    $page = 'update_quiz';
    include 'include/sidebar.php';
    ?>

    <!-- Main Content -->
    <div class="main-content" id="main-content">
        <!-- Header -->
        <?php include 'include/header.php'; ?>

        <div class="header mb-4">
            <h2>Questions for <?php echo htmlspecialchars($quiz_title); ?></h2>
            <div>List of all questions and answers for the selected quiz.</div>
        </div>

        <!-- Messages -->
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <!-- Questions List -->
        <?php if (empty($error) || $success): ?>
            <div class="container">
                <?php if (empty($questions)): ?>
                    <div class="alert alert-info">No questions found for this quiz.</div>
                <?php else: ?>
                    <?php foreach ($questions as $index => $question): ?>
                        <div class="card shadow-sm mb-3">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0">Question <?php echo $index + 1; ?></h5>
                            </div>
                            <div class="card-body">
                                <p class="fw-bold mb-3"><?php echo htmlspecialchars($question['question_text']); ?></p>
                                <ul class="list-group">
                                    <?php foreach ($question['answers'] as $ans_index => $answer): ?>
                                        <li class="list-group-item <?php echo $answer['is_correct'] ? 'bg-success-subtle' : ''; ?>">
                                            <span class="fw-bold">Answer <?php echo $ans_index + 1; ?>:</span>
                                            <?php echo htmlspecialchars($answer['answer_text']); ?>
                                            <?php if ($answer['is_correct']): ?>
                                                <span class="badge bg-success ms-2">Correct</span>
                                            <?php endif; ?>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                            <div class="card-footer text-end">
                                <a href="update_question.php?quiz_id=<?php echo $quiz_id; ?>&question_id=<?php echo $question['question_id']; ?>" class="btn btn-sm btn-success me-2">
                                    <i class="fas fa-edit"></i> Update
                                </a>
                                <?php if ($_SESSION['user_id'] == 1): ?>
                                    <a href="view_questions.php?quiz_id=<?php echo $quiz_id; ?>&delete_id=<?php echo $question['question_id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this question?');">
                                        <i class="fas fa-trash"></i> Delete
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
                <div class="mt-3">
                    <a href="add_question.php?quiz_id=<?php echo $quiz_id; ?>" class="btn btn-outline-dark">
                        <i class="fas fa-arrow-left me-2"></i>Back to Add Question
                    </a>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/hamburger.js"></script>
</body>
</html>