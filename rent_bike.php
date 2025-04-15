<?php
session_start();

// Redirect to login if user is not authenticated
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Include the Bike class and database connection
require 'classes/bike.php';
require 'db/config.php'; // Include the database configuration file
// Get bike ID from URL parameter
$bike_id = isset($_GET['bike_id']) ? intval($_GET['bike_id']) : 0;

// Get the bike instance
$bike = Bike::getBikeById($conn, $bike_id);

// Process reservation
$success = false;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Confirm reservation
    try {
        if ($bike && $bike->isAvailable()) {
            $success = $bike->reserve(); // No parameter needed
            if ($success) {
                header("Location: payment.php?bike_id=" . $bike_id);
                exit();
            } else {
                $error = "Bike is no longer available for reservation.";
            }
        } else {
            $error = "Selected bike is no longer available.";
        }
    } catch (Exception $e) {
        $error = "Error processing reservation: " . $e->getMessage();
    }
} else {
    // Verify bike availability before showing reservation page
    if (!$bike || !$bike->isAvailable()) {
        $error = "Selected bike is no longer available.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rent Bike</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            padding: 20px;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
        }
        .bike-details {
            border: 1px solid #ddd;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        .error {
            color: #dc3545;
            padding: 10px;
            border: 1px solid #f5c6cb;
            background-color: #f8d7da;
            border-radius: 5px;
        }
        form {
            margin-top: 20px;
        }
        button {
            background-color: #28a745;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Rent Bike</h1>
        
        <?php if (!empty($error)): ?>
            <div class="error"><?php echo $error; ?></div>
            <p><a href="available_bikes.php">Back to Available Bikes</a></p>
        <?php elseif ($bike): ?>
            <div class="bike-details">
                <h2>Bike #<?php echo htmlspecialchars($bike->getId(), ENT_QUOTES, 'UTF-8'); ?></h2>
                <p><strong>Created At:</strong> <?php echo htmlspecialchars($bike->getCreatedAt(), ENT_QUOTES, 'UTF-8'); ?></p>

                <form action="rent_bike.php?bike_id=<?php echo $bike_id; ?>" method="POST">
                    <h3>Reservation Details</h3>
                    <p>This bike will be reserved while you complete payment.</p>
                    <button type="submit">Confirm Reservation</button>
                    <a href="available_bikes.php">Cancel</a>
                </form>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>

<?php
// Close database connection
$conn->close();
?>