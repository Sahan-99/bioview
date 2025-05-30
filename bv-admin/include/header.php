<div class="header1 mb-4 d-flex justify-content-between align-items-center">
    <button id="hamburger" class="navbar-toggler" type="button">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="dropdown">
        <div class="d-flex align-items-center" data-bs-toggle="dropdown" style="cursor: pointer;">
            <img src="<?php echo !empty($profile_picture) ? htmlspecialchars($profile_picture) : 'img/default-profile.png'; ?>" alt="Profile" class="rounded-circle me-2" style="width: 50px;">
        </div>
        <ul class="dropdown-menu dropdown-menu-end">
            <li><a class="dropdown-item" href="admin_profile.php"><i class="fas fa-user me-2"></i> Profile</a></li>

            <li><a class="dropdown-item" href="lock_screen.php"><i class="fas fa-lock me-2"></i> Lock Screen</a></li>

            <li><a class="dropdown-item" href="new_admin.php"><i class="fas fa-user-plus me-2"></i> Add Admin</a></li>

            <li><a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out me-2"></i> Logout</a></li>
        </ul>
    </div>
</div>