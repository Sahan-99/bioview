<div id="sidebar" class="sidebar">
    <button id="close-sidebar" class="close-sidebar"><i class="fas fa-times"></i></button>
    <div class="logo">
        <a href="index.php"><img src="img/logo.png" alt="logo"></a>
    </div>
    <a href="index.php" class="<?php echo ($page == 'index' ? 'active' : '') ?>"><i class="fas fa-tachometer-alt me-2"></i> Dashboard</a>
    
    <!-- Users Menu -->
    <a href="#usersSubmenu" class="<?php echo (in_array($page, ['students', 'teachers', 'admins']) ? 'active' : '') ?>" data-bs-toggle="collapse" aria-expanded="<?php echo (in_array($page, ['students', 'teachers', 'admins']) ? 'true' : 'false') ?>">
        <i class="fas fa-users me-2"></i> Users
        <i class="fas fa-chevron-down ms-auto"></i>
    </a>
    <div id="usersSubmenu" class="collapse <?php echo (in_array($page, ['students', 'teachers', 'admins']) ? 'show' : '') ?>">
        <a href="students.php" class="submenu <?php echo ($page == 'students' ? 'active' : '') ?>"><i class="fas fa-user-graduate me-2"></i> Students</a>
        <a href="teachers.php" class="submenu <?php echo ($page == 'teachers' ? 'active' : '') ?>"><i class="fas fa-chalkboard-teacher me-2"></i> Teachers</a>
        <a href="admins.php" class="submenu <?php echo ($page == 'admins' ? 'active' : '') ?>"><i class="fas fa-user-shield me-2"></i> Admins</a>
    </div>

    <!-- 3D Models Menu -->
    <a href="#modelsSubmenu" class="<?php echo (in_array($page, ['view_3d_models', 'add_3d_models']) ? 'active' : '') ?>" data-bs-toggle="collapse" aria-expanded="<?php echo (in_array($page, ['view_3d_models', 'add_3d_models']) ? 'true' : 'false') ?>">
        <i class="fas fa-cube me-2"></i> 3D Models
        <i class="fas fa-chevron-down ms-auto"></i>
    </a>
    <div id="modelsSubmenu" class="collapse <?php echo (in_array($page, ['view_3d_models', 'add_3d_models']) ? 'show' : '') ?>">
        <a href="3d_models.php" class="submenu <?php echo ($page == 'view_3d_models' ? 'active' : '') ?>"><i class="fas fa-eye me-2"></i> View 3D Models</a>
        <a href="add_3d_models.php" class="submenu <?php echo ($page == 'add_3d_models' ? 'active' : '') ?>"><i class="fas fa-plus me-2"></i> Add 3D Models</a>
    </div>

    <!-- Scanned Images Menu -->
    <a href="#imagesSubmenu" class="<?php echo (in_array($page, ['view_images', 'add_images']) ? 'active' : '') ?>" data-bs-toggle="collapse" aria-expanded="<?php echo (in_array($page, ['view_images', 'add_images']) ? 'true' : 'false') ?>">
        <i class="fas fa-image me-2"></i> Scanned Images
        <i class="fas fa-chevron-down ms-auto"></i>
    </a>
    <div id="imagesSubmenu" class="collapse <?php echo (in_array($page, ['view_images', 'add_images']) ? 'show' : '') ?>">
        <a href="images.php" class="submenu <?php echo ($page == 'view_images' ? 'active' : '') ?>"><i class="fas fa-eye me-2"></i> View Images</a>
        <a href="add_images.php" class="submenu <?php echo ($page == 'add_images' ? 'active' : '') ?>"><i class="fas fa-plus me-2"></i> Add Images</a>
    </div>

    <!-- Audio Menu -->
    <a href="#audioSubmenu" class="<?php echo (in_array($page, ['view_audio', 'add_audio', 'update_audio']) ? 'active' : '') ?>" data-bs-toggle="collapse" aria-expanded="<?php echo (in_array($page, ['view_audio', 'add_audio']) ? 'true' : 'false') ?>">
        <i class="fas fa-music me-2"></i> Audio
        <i class="fas fa-chevron-down ms-auto"></i>
    </a>
    <div id="audioSubmenu" class="collapse <?php echo (in_array($page, ['view_audio', 'add_audio']) ? 'show' : '') ?>">
        <a href="audio.php" class="submenu <?php echo ($page == 'view_audio' ? 'active' : '') ?>"><i class="fas fa-eye me-2"></i> View Audio</a>
        <a href="add_audio.php" class="submenu <?php echo ($page == 'add_audio' ? 'active' : '') ?>"><i class="fas fa-plus me-2"></i> Add Audio</a>
    </div>

    <!-- Quiz Menu -->
    <a href="#quizSubmenu" class="<?php echo (in_array($page, ['view_quiz', 'add_quiz']) ? 'active' : '') ?>" data-bs-toggle="collapse" aria-expanded="<?php echo (in_array($page, ['view_quiz', 'add_quiz']) ? 'true' : 'false') ?>">
        <i class="fas fa-question-circle me-2"></i> Quiz
        <i class="fas fa-chevron-down ms-auto"></i>
    </a>
    <div id="quizSubmenu" class="collapse <?php echo (in_array($page, ['view_quiz', 'add_quiz']) ? 'show' : '') ?>">
        <a href="quiz.php" class="submenu <?php echo ($page == 'view_quiz' ? 'active' : '') ?>"><i class="fas fa-eye me-2"></i> View Quiz</a>
        <a href="add_quiz.php" class="submenu <?php echo ($page == 'add_quiz' ? 'active' : '') ?>"><i class="fas fa-plus me-2"></i> Add Quiz</a>
    </div>

    <!-- Quiz Attempts Menu -->
    <a href="quiz_attempts.php" class="<?php echo ($page == 'view_quiz_attempts' ? 'active' : '') ?>"><i class="fas fa-history me-2"></i> Quiz Attempts</a>

    <!-- Reports Menu -->
    <a href="reports.php" class="<?php echo ($page == 'view_reports' ? 'active' : '') ?>"><i class="fas fa-file-alt me-2"></i> Reports</a>
</div>