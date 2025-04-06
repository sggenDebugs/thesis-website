<?php
$host = 'localhost';
$dbname = 'u388284544_tracker';
$user = 'u388284544_tracker';
$pass = 'LOE0fN#bV*8';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
