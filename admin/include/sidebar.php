<div class="sidebar">
        <div class="logo">
            <a href="index.php"><img src="img/logo.png" alt="logo"></a>
        </div>
        <a href="index.php" class="<?php echo ($page == "index" ? "active" : "")?>"><i class="fas fa-tachometer-alt me-2"></i> Dashboard</a>
        <a href="user_details.php" class="<?php echo ($page == "users" ? "active" : "")?>"><i class="fas fa-users me-2"></i> Users</a>
        <a href="3d_model_details.php" class="<?php echo ($page == "3d_models" ? "active" : "")?>"><i class="fas fa-cube me-2"></i> 3D Models</a>
        <a href="#"><i class="fas fa-image me-2"></i> Scanned Images</a>
        <a href="#"><i class="fas fa-music me-2"></i> Audio</a>
        <a href="#"><i class="fas fa-question-circle me-2"></i> Quiz</a>
        <a href="#"><i class="fas fa-history me-2"></i> Quiz Attempts</a>
        <a href="#"><i class="fas fa-file-alt me-2"></i> Reports</a>
        <a href="admin_details.php" class="<?php echo ($page == "admins" ? "active" : "")?>"><i class="fas fa-user-shield me-2"></i> Admins</a>
    </div>