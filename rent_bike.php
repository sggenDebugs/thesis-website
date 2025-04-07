<?php
session_start();

// Redirect to login if user is not authenticated
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require 'classes/bike.php';
require 'db/config.php'; // Include the database configuration file

// Get bike ID from URL
$bike_id = isset($_GET['bike_id']) ? intval($_GET['bike_id']) : 0;

// Fetch the bike
$bike = Bike::getBikeById($conn, $bike_id);

// Process reservation
$success = false;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($bike && $bike->isAvailable()) {
        $success = $bike->reserve($_SESSION['user_id']);
        if ($success) {
            header("Location: payment.php?bike_id=" . $bike_id);
            exit();
        } else {
            $error = "Bike is no longer available.";
        }
    } else {
        $error = "Selected bike is not available.";
    }
} else {
    if (!$bike || !$bike->isAvailable()) {
        $error = "Selected bike is not available.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Rent Bike</title>
</head>
<body>
    <h1>Rent Bike</h1>
    <?php if (!empty($error)): ?>
        <p style="color: red;"><?php echo $error; ?></p>
        <p><a href="available_bikes.php">Back to Available Bikes</a></p>
    <?php elseif ($bike): ?>
        <h2>Bike #<?php echo $bike->getId(); ?></h2>
        <p><strong>Created At:</strong> <?php echo $bike->getCreatedAt(); ?></p>
        <form action="rent_bike.php?bike_id=<?php echo $bike_id; ?>" method="POST">
            <p>This bike will be reserved while you complete payment.</p>
            <button type="submit">Confirm Reservation</button>
            <a href="available_bikes.php">Cancel</a>
        </form>
    <?php endif; ?>
</body>
</html>

<?php $conn->close(); ?>