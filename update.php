<?php
require 'config.php';
// Get parameters
$device_id = $_GET['device_id'];
$lat = floatval($_GET['lat']);
$lng = floatval($_GET['lng']);

// Insert data
$stmt = $conn->prepare("INSERT INTO bikes (device_id, lat, lng) VALUES (?, ?, ?)");
$stmt->bind_param("sdd", $device_id, $lat, $lng);
$stmt->execute();
$stmt->close();
$conn->close();
?>