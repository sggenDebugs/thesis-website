<?php
$servername = "127.0.0.1";
$username = "u388284544_sggen";
$password = "xB@akashinji420x";
$dbname = "u388284544_server";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT id, name FROM users"; // Adjust the table and column names as needed
$result = $conn->query($sql);

$riders = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $riders[] = $row;
    }
}

header('Content-Type: application/json');
$result = json_encode($riders);
echo $result;

$conn->close();

?>