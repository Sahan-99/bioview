<?php
$conn = new mysqli("localhost", "root", "", "bioview");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>