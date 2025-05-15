<?php
if (!isset($_SESSION['user_id'])) {
    header("Location: admin_login.php");
    exit();
}

$admin_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT first_name, last_name, profile_picture, type FROM users WHERE user_id = ?");
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$admin_result = $stmt->get_result();

if ($admin_result->num_rows > 0) {
    $admin_data = $admin_result->fetch_assoc();

    // Check if the user is an admin
    if ($admin_data['type'] !== 'admin') {
        // Not an admin, redirect
        header("Location: unauthorized.php");
        exit();
    }

    // Admin info
    $firstname = $admin_data['first_name'] ?? 'Admin';
    $lastname = $admin_data['last_name'] ?? '';
    $profile_picture = $admin_data['profile_picture'] ?? 'https://via.placeholder.com/40';

} else {
    // Admin not found
    header("Location: admin_login.php");
    exit();
}

$stmt->close();
?>
