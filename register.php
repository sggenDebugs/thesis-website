<?php
session_start();
require 'classes/user.php'; // Include the User class

$servername = "127.0.0.1";
$email = "u388284544_sggen";
$password = "xB@akashinji420x";
$dbname = "u388284544_server";

$error = "";
$success = "";

// Database connection
$conn = new mysqli($servername, $email, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create a User object
$user = new User($conn);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $contact_number = trim($_POST['contact_number']);
    $government_id = trim($_POST['government_id']);

    // Call the register method
    $result = $user->register($first_name, $last_name, $email, $password, $contact_number, $government_id);

    if ($result === "Registration successful! You can now <a href='login.php'>login</a>.") {
        $success = $result;
    } else {
        $error = $result;
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" type="text/css" href="css/register.css">
</head>
<body>
    <div class="container">
        <h2>Register</h2>
        <form action="register.php" method="POST">
            <div class="form-group">
                <label for="first_name">First Name:</label>
                <input type="text" id="first_name" name="first_name" required>
            </div>
            <div class="form-group">
                <label for="last_name">Last Name:</label>
                <input type="text" id="last_name" name="last_name" required>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="form-group">
                <label for="contact_number">Contact Number:</label>
                <input type="text" id="contact_number" name="contact_number">
            </div>
            <div class="form-group">
                <label for="government_id">Government ID:</label>
                <input type="text" id="government_id" name="government_id" required>
            </div>
            <button type="submit">Register</button>
        </form>
        <?php if (isset($error)): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>
        <?php if (isset($success)): ?>
            <p class="success"><?php echo $success; ?></p>
        <?php endif; ?>
    </div>
</body>
</html>