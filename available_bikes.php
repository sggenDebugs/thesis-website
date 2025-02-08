<?php
session_start();
if (!isset($_SESSION['email'])) {
    header('Location: login.php');
    exit();
}

// Include the Bike class
require 'classes/bike.php';

// Database credentials (use environment variables or a secure config file in production)
$servername = "127.0.0.1";
$username = "u388284544_sggen";
$password = "xB@akashinji420x";
$dbname = "u388284544_server";

// Create a database connection
$mysqli = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Create a Bike object
$bikeManager = new Bike($mysqli);

// Fetch available bikes
$availableBikes = $bikeManager->getAvailableBikes();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Available Bikes</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f4f4f4;
        }
        .no-bikes {
            color: #888;
            font-style: italic;
        }
    </style>
</head>
<body>
    <h1>Available Bikes</h1>
    <?php if (empty($availableBikes)): ?>
        <p class="no-bikes">No bikes available at the moment.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Location</th>
                    <th>Hourly Rate</th>
                    <th>Status</th>
                    <th>Last Used At</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($availableBikes as $bike): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($bike->getId()); ?></td>
                        <td><?php echo htmlspecialchars($bike->getLocation()); ?></td>
                        <td>$<?php echo htmlspecialchars($bike->getHourlyRate()); ?> / hour</td>
                        <td><?php echo htmlspecialchars($bike->getStatus()); ?></td>
                        <td><?php echo htmlspecialchars($bike->getLastUsedAt()); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
    <a href="dashboard.php">Back to Dashboard</a>
</body>
</html>

<?php
// Close the database connection
$mysqli->close();
?>