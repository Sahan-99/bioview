<div class="header1 mb-4 d-flex justify-content-between align-items-center">
            <div class="input-group w-25">
                <span class="input-group-text"><i class="fas fa-search"></i></span>
                <input type="text" class="form-control" placeholder="Search...">
            </div>
            <div class="dropdown">
                <div class="d-flex align-items-center" data-bs-toggle="dropdown" style="cursor: pointer;">
                    <img src="<?php echo htmlspecialchars($profile_picture); ?>" alt="Profile" class="rounded-circle me-2" style="width: 60px;">
                </div>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="admin_profile.php">Profile</a></li>
                    <li><a class="dropdown-item" href="new_admin.php">Add Admin</a></li>
                    <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                </ul>
            </div>
        </div>