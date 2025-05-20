<?php
$conn = new mysqli("localhost", "root", "", "test");
if ($conn->connect_error) die("Connection failed");

$start = $_GET['start'] ?? date('Y-m-d', strtotime('-6 days'));
$end = $_GET['end'] ?? date('Y-m-d');

$sql = "SELECT usage_date, usage_count 
        FROM app_usage 
        WHERE usage_date BETWEEN ? AND ?
        ORDER BY usage_date ASC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $start, $end);
$stmt->execute();
$result = $stmt->get_result();

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode($data);
?>
