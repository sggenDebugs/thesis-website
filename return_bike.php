<?php
session_start();
require 'classes/bike.php';

if (!isset($_SESSION['user_id']) || !isset($_GET['bike_id'])) {
    header("Location: login.php");
    exit();
}

$servername = "127.0.0.1";
$username = "u388284544_sggen";
$password = "xB@akashinji420x";
$dbname = "u388284544_server";

$mysqli = new mysqli($servername, $username, $password, $dbname);
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

$bike_id = intval($_GET['bike_id']);
$bike = Bike::getBikeById($mysqli, $bike_id);

if (!$bike || !($bike->getTimeRented() && !$bike->getTimeReturned())) {
    header("Location: display_bikes.php");
    exit();
}

$error = '';
try {
    $mysqli->begin_transaction();

    // Update the rentals table
    $sql = "UPDATE rentals SET status = 'completed' WHERE bike_id = ? AND status IN ('active', 'overtime')";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param('i', $bike_id);
    $stmt->execute();
    $stmt->close();

    // Update the bikes table (set time_returned)
    $sql = "UPDATE bikes SET time_returned = NOW() WHERE id = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param('i', $bike_id);
    $stmt->execute();
    $stmt->close();

    $mysqli->commit();
    header("Location: display_bikes.php?message=Bike+returned+successfully");
    exit();
} catch (Exception $e) {
    $mysqli->rollback();
    $error = "Error returning bike: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Return Bike</title>
</head>
<body>
    <h1>Return Bike</h1>
    <?php if ($error): ?>
        <p style="color: red;"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p>
    <?php endif; ?>
</body>
</html>