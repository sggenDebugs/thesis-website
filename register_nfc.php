<?php
// session_start();
// Add admin authentication check here

require "config.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $uid = $conn->real_escape_string($_POST['uid']);
    
    try {
        $conn->query("
            INSERT INTO nfc_tags (uid) 
            VALUES ('$uid')
        ");
        $success = "NFC tag registered successfully!";
    } catch (mysqli_sql_exception $e) {
        $error = "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register New NFC Tag</title>
</head>
<body>
    <h1>Register New NFC Tag</h1>
    
    <?php if (isset($success)): ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php endif; ?>
    
    <?php if (isset($error)): ?>
        <div class="alert alert-error"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST">
        <label>NFC UID:
            <input type="text" name="uid" required 
                   pattern="[0-9A-Fa-f]+" title="Hexadecimal format">
        </label>
        <button type="submit">Register Tag</button>
    </form>
</body>
</html>