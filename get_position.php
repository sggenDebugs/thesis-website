<?php
header('Content-Type: application/json');

require 'config.php';
$bike_id = $_GET['bike_id'];

$result = $conn->query("
    SELECT latitude, longitude 
    FROM bikes 
    WHERE id = '$bike_id' 
    ORDER BY last_used_at DESC 
    LIMIT 1
");

if ($result->num_rows > 0) {
    echo json_encode($result->fetch_assoc());
} else {
    echo json_encode(['error' => 'No data found']);
}
$conn->close();
?>