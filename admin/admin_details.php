<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Details</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f5f7fa;
        }
        .sidebar {
            height: 100vh;
            width: 250px;
            position: fixed;
            background-color: #fff;
            box-shadow: 2px 0 5px rgba(0,0,0,0.1);
            padding-top: 20px;
        }
        .sidebar a {
            color: #6c757d;
            padding: 15px 20px;
            display: block;
            text-decoration: none;
        }
        .sidebar a.active {
            background-color: #007bff;
            color: #fff;
            border-radius: 5px;
            margin: 0 10px;
        }
        .sidebar a:hover {
            color: #007bff;
        }
        .main-content {
            margin-left: 250px;
            padding: 20px;
        }
        .header {
            background: linear-gradient(90deg, #007bff 0%, #00c6ff 100%);
            color: #fff;
            padding: 20px;
            border-radius: 10px;
        }
        .header1 {
            background-color: #fff;
            padding: 15px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .table-container {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .table img {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 50%;
        }
        .table th, .table td {
            vertical-align: middle;
        }
    </style>

    <?php $page = "admin_details"; ?>
</head>
<body>
    <?php
    include 'dbconnect.php'; // Include the database connection
    session_start();

    // Check if admin is logged in
    if (!isset($_SESSION['admin_id'])) {
        header("Location: admin_login.php");
        exit();
    }

    $admin_id = $_SESSION['admin_id'];

    // Fetch current admin details for the header
    $stmt = $conn->prepare("SELECT firstname, lastname, profile_picture FROM admin WHERE admin_id = ?");
    $stmt->bind_param("i", $admin_id);
    $stmt->execute();
    $admin_result = $stmt->get_result();

    if ($admin_result->num_rows > 0) {
        $admin_data = $admin_result->fetch_assoc();
        $firstname = $admin_data['firstname'] ?? 'Admin';
        $lastname = $admin_data['lastname'] ?? '';
        $profile_picture = $admin_data['profile_picture'] ?? 'https://via.placeholder.com/40';
    } else {
        $firstname = 'Admin';
        $lastname = '';
        $profile_picture = 'https://via.placeholder.com/40';
    }
    $stmt->close();

    // Fetch all admins
    $query = "SELECT admin_id, firstname, lastname, email, profile_picture FROM admin";
    $result = $conn->query($query);
    $conn->close();
    ?>

    <!-- Sidebar -->
    <?php include 'include/sidebar.php'; ?>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Header -->
        <?php include 'include/header.php'; ?>

        <div class="header mb-4">
            <h1>Admin Details</h1>
            <p>List of all administrators in the system.</p>
        </div>

        <!-- Admin List Table -->
        <div class="table-container">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Profile Picture</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['firstname'] . ' ' . $row['lastname']); ?></td>
                                <td><?php echo htmlspecialchars($row['email']); ?></td>
                                <td>
                                    <img src="<?php echo htmlspecialchars($row['profile_picture'] ?: 'https://via.placeholder.com/50'); ?>" alt="Profile Picture">
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="text-center">No admins found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>