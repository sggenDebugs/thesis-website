<?php
session_start();
require 'classes/bike.php';

// Redirect if not authenticated or missing bike ID
if (!isset($_SESSION['user_id']) || !isset($_GET['bike_id'])) {
    header("Location: login.php");
    exit();
}

$servername = "127.0.0.1";
$username = "u388284544_sggen";
$password = "xB@akashinji420x";
$dbname = "u388284544_server";

// Database connection
$mysqli = new mysqli($servername, $username, $password, $dbname);
$bikeManager = new Bike($mysqli);
$bike_id = intval($_GET['bike_id']);

// Cancel the bike reservation
$bike = Bike::getBikeById($mysqli, $bike_id);
if ($bike && $bike->getStatus() === 'reserved') {
    $bikeManager->markAsAvailable($bike_id);
}

// Redirect to available bikes page
header("Location: available_bikes.php");
exit();
?>