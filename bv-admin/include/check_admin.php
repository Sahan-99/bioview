<?php
if (!isset($_SESSION['user_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Get the logged-in admin's ID from the session
$admin_id = $_SESSION['user_id'];

// Use prepared statement to prevent SQL injection
$stmt = $conn->prepare("SELECT first_name, last_name, profile_picture FROM users WHERE user_id = ? AND type = 'admin'");
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$admin_result = $stmt->get_result();

// Check if the query returned a result
if ($admin_result->num_rows > 0) {
    $admin_data = $admin_result->fetch_assoc();
    $firstname = $admin_data['first_name'] ?? 'Admin';
    $lastname = $admin_data['last_name'] ?? '';
    $profile_picture = $admin_data['profile_picture'] ?? 'https://via.placeholder.com/40';
} else {
    // Handle case where admin is not found (e.g., invalid admin_id or type)
    $firstname = 'Admin';
    $lastname = '';
    $profile_picture = 'https://via.placeholder.com/40';
}

$stmt->close();
?>