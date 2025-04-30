<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="icon" type="image/x-icon" href="img/logo.png">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/index.css">
    

    <?php $page = "index"; ?>
</head>
<body>
    <?php
    include 'dbconnect.php'; 
    session_start();

    if (!isset($_SESSION['admin_id'])) {
        header("Location: admin_login.php");
        exit();
    }

    // Get the logged-in admin's ID from the session
    $admin_id = $_SESSION['admin_id'];

    // Use prepared statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT firstname, lastname, profile_picture FROM admin WHERE admin_id = ?");
    $stmt->bind_param("i", $admin_id);
    $stmt->execute();
    $admin_result = $stmt->get_result();

    // Check if the query returned a result
    if ($admin_result->num_rows > 0) {
        $admin_data = $admin_result->fetch_assoc();
        $firstname = $admin_data['firstname'] ?? 'Admin';
        $lastname = $admin_data['lastname'] ?? '';
        $profile_picture = $admin_data['profile_picture'] ?? 'https://via.placeholder.com/40';
    } else {
        // Handle case where admin is not found (e.g., invalid admin_id)
        $firstname = 'Admin';
        $lastname = '';
        $profile_picture = 'https://via.placeholder.com/40';
    }

    $stmt->close();

    // Fetch counts from all tables
    $users_count = $conn->query("SELECT COUNT(*) FROM users")->fetch_row()[0];
    $models_count = $conn->query("SELECT COUNT(*) FROM 3d_models")->fetch_row()[0];
    $images_count = $conn->query("SELECT COUNT(*) FROM scanned_images")->fetch_row()[0];
    $audio_count = $conn->query("SELECT COUNT(*) FROM audio")->fetch_row()[0];
    $quiz_count = $conn->query("SELECT COUNT(*) FROM quiz")->fetch_row()[0];
    $attempt_count = $conn->query("SELECT COUNT(*) FROM quiz_attempt")->fetch_row()[0];
    $report_count = $conn->query("SELECT COUNT(*) FROM report")->fetch_row()[0];
    $admin_count = $conn->query("SELECT COUNT(*) FROM admin")->fetch_row()[0];

    $conn->close();
    ?>

    <!-- Sidebar -->
    <?php include 'include/sidebar.php'; ?>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Header -->
        <?php include 'include/header.php'; ?>

        <div class="header mb-4">
            <div>
                <h2 id="greeting">Good Morning <?php echo htmlspecialchars($firstname); ?></h2>
                <p>Welcome back!</p>
            </div>
            <div class="date-time" id="date-time"></div>
        </div>

        <!-- Database Table Counts -->
        <div class="row">
            <div class="col-md-3 mb-4">
                <div class="card">
                    <div class="card-body">
                        <i class="fas fa-users card-icon-bg users-icon"></i>
                        <h3 class="users-icon"><?php echo $users_count; ?></h3>
                        <h5>USERS</h5>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="card">
                    <div class="card-body">
                        <i class="fas fa-cube card-icon-bg models-icon"></i>
                        <h3 class="models-icon"><?php echo $models_count; ?></h3>
                        <h5>3D MODELS</h5>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="card">
                    <div class="card-body">
                        <i class="fas fa-image card-icon-bg images-icon"></i>
                        <h3 class="images-icon"><?php echo $images_count; ?></h3>
                        <h5>SCANNED IMAGES</h5>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="card">
                    <div class="card-body">
                        <i class="fas fa-music card-icon-bg audio-icon"></i>
                        <h3 class="audio-icon"><?php echo $audio_count; ?></h3>
                        <h5>AUDIO</h>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="card">
                    <div class="card-body">
                        <i class="fas fa-question-circle card-icon-bg quiz-icon"></i>
                        <h3 class="quiz-icon"><?php echo $quiz_count; ?></h3>
                        <h5>QUIZ</>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="card">
                    <div class="card-body">
                        <i class="fas fa-history card-icon-bg attempts-icon"></i>
                        <h3 class="attempts-icon"><?php echo $attempt_count; ?></h3>
                        <h5>QUIZ ATTEMPTS</>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="card">
                    <div class="card-body">
                        <i class="fas fa-file-alt card-icon-bg reports-icon"></i>
                        <h3 class="reports-icon"><?php echo $report_count; ?></h3>
                        <h5>REPORTS</h5>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="card">
                    <div class="card-body">
                        <i class="fas fa-user-shield card-icon-bg admins-icon"></i>
                        <h3 class="admins-icon"><?php echo $admin_count; ?></h3>
                        <h5>ADMINS</h5>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
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
            hour12: false
        };
        // Format the date and time: "30 Apr 2025, 14:30"
        const formattedDateTime = now.toLocaleString('en-GB', options)
            .replace(/(\d{2}) (\w{3}) (\d{4}), (\d{2}):(\d{2})/, '$1 $2 $3, $4:$5');
        document.getElementById('date-time').textContent = formattedDateTime;
    }

    // Function to update the greeting and date/time
    function updateHeader() {
        const greeting = getGreeting();
        document.getElementById('greeting').textContent = `${greeting} ${adminName}`;
        updateDateTime();
    }

    // Update immediately on page load
    updateHeader();
    // Update every minute to keep the time current and adjust greeting if needed
    setInterval(updateHeader, 60000);
    </script>
</body>
</html>