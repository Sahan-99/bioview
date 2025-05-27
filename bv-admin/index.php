<?php
include 'dbconnect.php';
include 'include/check_session.php';

// Check if admin is logged in
include 'include/check_admin.php';



// Fetch counts from all tables
$student_count = $conn->query("SELECT COUNT(*) FROM users WHERE type = 'student'")->fetch_row()[0];
$teacher_count = $conn->query("SELECT COUNT(*) FROM users WHERE type = 'teacher'")->fetch_row()[0];
$admin_count = $conn->query("SELECT COUNT(*) FROM users WHERE type = 'admin'")->fetch_row()[0];
$models_count = $conn->query("SELECT COUNT(*) FROM 3d_models WHERE status=1")->fetch_row()[0];
$images_count = $conn->query("SELECT COUNT(*) FROM scanned_images WHERE status=1")->fetch_row()[0];
$audio_count = $conn->query("SELECT COUNT(*) FROM audio WHERE status=1")->fetch_row()[0];
$quiz_count = $conn->query("SELECT COUNT(*) FROM quizzes")->fetch_row()[0];
$question_count = $conn->query("SELECT COUNT(*) FROM questions")->fetch_row()[0];
$attempt_count = $conn->query("SELECT COUNT(*) FROM quiz_attempts")->fetch_row()[0];
$report_count = $conn->query("SELECT COUNT(*) FROM reports")->fetch_row()[0];

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="icon" type="image/x-icon" href="img/logo.png">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Font Awesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/index.css">
    <style>
 /* Chart container styling */
.chart-container {
    width: 100%;
    height: 350px;
    position: relative;
    display: flex;
    justify-content: center;
    align-items: center;
}

/* Ensure the canvas scales properly */
#usageChart {
    max-width: 90%;
    max-height: 90%;
}

/* App Usage header styling */
.app-usage-header {
    border-radius: 5px 5px 0 0;
    width: 100%;
}

.btn-outline-light {
    border-color: #fff;
    color: #fff;
    background-color: transparent;
    font-size: 12px;
    padding: 2px 10px;
    margin-left: 5px;
    transition: all 0.3s ease;
}

.btn-outline-light:hover {
    background-color: #fff;
    color:rgb(0, 138, 30);
    border-color: #fff;
}

/* Card styling */
.card {
    border: none;
    border-radius: 10px;
    overflow: hidden;
}

.shadow {
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .chart-container {
        height: 300px;
    }

    .app-usage-header h5 {
        font-size: 16px;
    }

    .btn-outline-light {
        font-size: 10px;
        padding: 2px 8px;
    }
}

@media (max-width: 576px) {
    .chart-container {
        height: 250px;
    }

    .app-usage-header {
        flex-direction: column;
        text-align: center;
    }

    .app-usage-header .btn-outline-light {
        margin: 5px 0;
        width: 100px;
    }
}
    </style>

    <?php $page = "index"; ?>
</head>

<body>
    <!-- Sidebar -->
    <?php include 'include/sidebar.php'; ?>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Header -->
        <?php include 'include/header.php'; ?>

        <div class="header mb-4">
            <div>
                <h2 id="greeting">Good Morning <?php echo htmlspecialchars($firstname); ?>!</h2>
            </div>
            <div class="date-time" id="date-time"></div>
        </div>

        <!-- Database Table Counts -->
        <div class="row">
            <div class="col-md-3 mb-4">
                <div class="card">
                    <div class="card-body">
                        <i class="fas fa-user-graduate card-icon-bg students-icon"></i>
                        <h3 class="students-icon"><?php echo $student_count; ?></h3>
                        <h5>STUDENTS</h5>
                        <a href="students.php">click to view</a>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="card">
                    <div class="card-body">
                        <i class="fas fa-chalkboard-teacher card-icon-bg teachers-icon"></i>
                        <h3 class="teachers-icon"><?php echo $teacher_count; ?></h3>
                        <h5>TEACHERS</h5>
                        <a href="teachers.php">click to view</a>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="card">
                    <div class="card-body">
                        <i class="fas fa-user-shield card-icon-bg admins-icon"></i>
                        <h3 class="admins-icon"><?php echo $admin_count; ?></h3>
                        <h5>ADMINS</h5>
                        <a href="admins.php">click to view</a>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="card">
                    <div class="card-body">
                        <i class="fas fa-cube card-icon-bg models-icon"></i>
                        <h3 class="models-icon"><?php echo $models_count; ?></h3>
                        <h5>3D MODELS</h5>
                        <a href="3d_models.php">click to view</a>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="card">
                    <div class="card-body">
                        <i class="fas fa-image card-icon-bg images-icon"></i>
                        <h3 class="images-icon"><?php echo $images_count; ?></h3>
                        <h5>SCANNED IMAGES</h5>
                        <a href="images.php">click to view</a>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="card">
                    <div class="card-body">
                        <i class="fas fa-music card-icon-bg audio-icon"></i>
                        <h3 class="audio-icon"><?php echo $audio_count; ?></h3>
                        <h5>AUDIOS</h5>
                        <a href="audio.php">click to view</a>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="card">
                    <div class="card-body">
                        <i class="fas fa-question-circle card-icon-bg quiz-icon"></i>
                        <h3 class="quiz-icon"><?php echo $quiz_count; ?></h3>
                        <h5>QUIZZES</h5>
                        <a href="quiz.php">click to view</a>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="card">
                    <div class="card-body">
                        <i class="fas fa-history card-icon-bg attempts-icon"></i>
                        <h3 class="attempts-icon"><?php echo $attempt_count; ?></h3>
                        <h5>QUIZ ATTEMPTS</h5>
                        <a href="quiz_attempts.php">click to view</a>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="card">
                    <div class="card-body">
                        <i class="fas fa-file-alt card-icon-bg reports-icon"></i>
                        <h3 class="reports-icon"><?php echo $report_count; ?></h3>
                        <h5>REPORTS</h5>
                        <a href="reports.php">click to view</a>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="card">
                    <div class="card-body">
                        <i class="fas fa-question card-icon-bg question-icon"></i>
                        <h3 class="question-icon"><?php echo $question_count; ?></h3>
                        <h5>QUESTIONS</h5>
                    </div>
                </div>
            </div>
        </div>

<div class="card shadow p-3 mt-4" style="min-height: 450px;">
    <div class="app-usage-header" style="background-color:rgb(0, 138, 30); color: white; padding: 10px 15px; border-radius: 5px 5px 0 0; display: flex; justify-content: space-between; align-items: center;">
        <h5 class="mb-0">App Usage</h5>
        <div>
            <button class="btn btn-outline-light btn-sm" id="prevWeek">← Previous</button>
            <button class="btn btn-outline-light btn-sm" id="nextWeek">Next →</button>
        </div>
    </div>
    <div class="chart-container d-flex justify-content-center align-items-center" style="height: 350px; width: 100%;">
        <canvas id="usageChart" style="max-width: 90%; max-height: 90%;"></canvas>
    </div>
</div>

    </div>


    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script src="js/hamburger.js"></script>
    <script src="js/chart.js"></script>
    <script>
        // Store the admin name in a JavaScript variable
        const adminName = "<?php echo htmlspecialchars($firstname); ?>";

        // Function to determine the greeting based on the time of day
        function getGreeting() {
            const now = new Date();
            const hour = now.getHours();
            if (hour >= 5 && hour < 12) {
                return "Good Morning";
            } else if (hour >= 12 && hour < 17) {
                return "Good Afternoon";
            } else {
                return "Good Evening";
            }
        }

        // Function to format and display the current date and time in the user's local timezone
        function updateDateTime() {
            const now = new Date();
            const options = {
                day: '2-digit',
                month: 'short',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
                hour12: true
            };
            // Format the date and time: "30 Apr 2025, 14:30"
            const formattedDateTime = now.toLocaleString('en-GB', options)
                .replace(/(\d{2}) (\w{3}) (\d{4}), (\d{2}):(\d{2})/, '$1 $2 $3, $4:$5');
            document.getElementById('date-time').textContent = formattedDateTime;
        }

        // Function to update the greeting and date/time
        function updateHeader() {
            const greeting = getGreeting();
            document.getElementById('greeting').textContent = `${greeting} ${adminName}!`;
            updateDateTime();
        }

        // Update immediately on page load
        updateHeader();
        // Update every minute to keep the time current and adjust greeting if needed
        setInterval(updateHeader, 60000);
    </script>
</body>

</html>