<?php
session_start();
include 'dbconnect.php';

// Check if admin is logged in
include 'include/check_admin.php';

$error = '';
$success = '';

$quiz_id = isset($_GET['quiz_id']) ? (int)$_GET['quiz_id'] : 0;
$question_id = isset($_GET['question_id']) ? (int)$_GET['question_id'] : 0;

// Fetch quiz title and question details
$quiz_title = '';
$question = null;
$answers = [];
if ($quiz_id && $question_id) {
    // Fetch quiz title
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

    // Fetch question
    $stmt = $conn->prepare("SELECT question_text FROM questions WHERE question_id = ? AND quiz_id = ?");
    $stmt->bind_param("ii", $question_id, $quiz_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $question = $result->fetch_assoc();
        // Fetch answers
        $stmt_answers = $conn->prepare("SELECT answer_id, answer_text, is_correct FROM answers WHERE question_id = ? ORDER BY answer_id");
        $stmt_answers->bind_param("i", $question_id);
        $stmt_answers->execute();
        $answer_result = $stmt_answers->get_result();
        while ($answer = $answer_result->fetch_assoc()) {
            $answers[] = $answer;
        }
        $stmt_answers->close();
    } else {
        $error = "Question not found.";
    }
    $stmt->close();
} else {
    $error = "Invalid quiz or question ID.";
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($error)) {
    $question_text = trim($_POST['question_text']);
    $correct_index = (int)$_POST['correct'];

    // Validate inputs
    if (empty($question_text)) {
        $error = "Question text is required.";
    } elseif (!isset($_POST['answers']) || count($_POST['answers']) != 4) {
        $error = "Exactly four answers are required.";
    } elseif ($correct_index < 0 || $correct_index > 3) {
        $error = "Invalid correct answer selection.";
    } else {
        // Update question
        $stmt = $conn->prepare("UPDATE questions SET question_text = ? WHERE question_id = ? AND quiz_id = ?");
        $stmt->bind_param("sii", $question_text, $question_id, $quiz_id);
        if ($stmt->execute()) {
            // Delete existing answers
            $stmt_answers = $conn->prepare("DELETE FROM answers WHERE question_id = ?");
            $stmt_answers->bind_param("i", $question_id);
            $stmt_answers->execute();
            $stmt_answers->close();

            // Insert updated answers
            foreach ($_POST['answers'] as $index => $answer_text) {
                $answer_text = trim($answer_text);
                if (empty($answer_text)) {
                    $error = "All answer fields must be filled.";
                    break;
                }
                $is_correct = ($index == $correct_index) ? 1 : 0;
                $stmt = $conn->prepare("INSERT INTO answers (question_id, answer_text, is_correct) VALUES (?, ?, ?)");
                $stmt->bind_param("isi", $question_id, $answer_text, $is_correct);
                if (!$stmt->execute()) {
                    $error = "Failed to update answer.";
                    break;
                }
            }
            if (empty($error)) {
                $success = "Question updated successfully. Redirecting to question list...";
                header("refresh:2;url=view_questions.php?quiz_id=$quiz_id");
            }
        } else {
            $error = "Failed to update question.";
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
    <title>Update Question</title>
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
            <h2>Update Question for <?php echo htmlspecialchars($quiz_title); ?></h2>
            <div>Edit the question and its answers below.</div>
        </div>

        <!-- Messages -->
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <!-- Question Update Form -->
        <?php if (empty($error) && $question): ?>
            <div class="form-container">
                <div class="row justify-content-center">
                    
                        
                           
                                <form method="post">
                                    <div class="mb-3">
                                        <label for="question_text" class="form-label">Question Text</label>
                                        <textarea class="form-control" id="question_text" name="question_text" rows="3" required><?php echo htmlspecialchars($question['question_text']); ?></textarea>
                                    </div>
                                    <?php foreach ($answers as $ans_index => $answer): ?>
                                        <div class="mb-3">
                                            <label for="answer_<?php echo $ans_index; ?>" class="form-label">Answer <?php echo $ans_index + 1; ?></label>
                                            <div class="input-group">
                                                <input type="text" class="form-control" id="answer_<?php echo $ans_index; ?>" name="answers[]" value="<?php echo htmlspecialchars($answer['answer_text']); ?>" required>
                                                <div class="input-group-text">
                                                    <input type="radio" id="correct_<?php echo $ans_index; ?>" name="correct" value="<?php echo $ans_index; ?>" <?php echo $answer['is_correct'] ? 'checked' : ''; ?> required>
                                                    <label for="correct_<?php echo $ans_index; ?>" class="ms-2 mb-0">Correct</label>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                    <button type="submit" class="btn btn-primary w-100">
                                        Update Question
                                    </button>
                                </form>
                            
                            <div class="text-center mt-3">
                                <a href="view_questions.php?quiz_id=<?php echo $quiz_id; ?>" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left me-2"></i>Back to Questions
                                </a>
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