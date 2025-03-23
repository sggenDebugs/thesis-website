<?php
// session_start();
// Add admin authentication check here
require "config.php";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nfc_id = intval($_POST['nfc_id']);
    $bike_id = intval($_POST['bike_id']);
    
    try {
        $conn->begin_transaction();
        
        // Check NFC tag availability
        $stmt = $conn->prepare("SELECT status FROM nfc_tags WHERE id = ?");
        $stmt->bind_param('i', $nfc_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0 || $result->fetch_assoc()['status'] !== 'available') {
            throw new Exception("Invalid or unavailable NFC tag");
        }
        
        // Check bike availability
        $stmt = $conn->prepare("SELECT card_id FROM bikes WHERE id = ?");
        $stmt->bind_param('i', $bike_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0 || $result->fetch_assoc()['card_id'] !== null) {
            throw new Exception("Invalid bike or already assigned");
        }
        
        // Update NFC tag
        $stmt = $conn->prepare("
            UPDATE nfc_tags 
            SET status = 'assigned', assigned_bike = ? 
            WHERE id = ?
        ");
        $stmt->bind_param('ii', $bike_id, $nfc_id);
        $stmt->execute();
        
        // Update bike
        $stmt = $conn->prepare("
            UPDATE bikes 
            SET card_id = ? 
            WHERE id = ?
        ");
        $stmt->bind_param('ii', $nfc_id, $bike_id);
        $stmt->execute();
        
        $conn->commit();
        $success = "NFC tag successfully assigned to bike!";
    } catch (Exception $e) {
        $conn->rollback();
        $error = $e->getMessage();
    }
}

// Get available NFC tags
$nfc_tags = $conn->query("
    SELECT id, uid 
    FROM nfc_tags 
    WHERE status = 'available'
");

// Get unassigned bikes
$bikes = $conn->query("
    SELECT id 
    FROM bikes 
    WHERE card_id IS NULL
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Assign NFC to Bike</title>
    <style>
        .container { max-width: 800px; margin: 20px auto; padding: 20px; }
        .form-group { margin-bottom: 15px; }
        .alert { padding: 10px; margin-bottom: 20px; }
        .alert-success { background-color: #dff0d8; }
        .alert-error { background-color: #f2dede; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Assign NFC Tag to Bike</h1>
        
        <?php if (isset($success)): ?>
            <div class="alert alert-success"><?= $success ?></div>
        <?php endif; ?>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-error"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>Select NFC Tag:</label>
                <select name="nfc_id" required>
                    <option value="">Choose NFC Tag</option>
                    <?php while ($tag = $nfc_tags->fetch_assoc()): ?>
                        <option value="<?= $tag['id'] ?>">
                            <?= htmlspecialchars($tag['uid']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Select Bike:</label>
                <select name="bike_id" required>
                    <option value="">Choose Bike</option>
                    <?php while ($bike = $bikes->fetch_assoc()): ?>
                        <option value="<?= $bike['id'] ?>">
                            <?= htmlspecialchars($bike['id']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <button type="submit">Assign NFC Tag</button>
        </form>

        <h2>Current Assignments</h2>
        <table>
            <tr>
                <th>Bike</th>
                <th>NFC UID</th>
                <th>Assigned On</th>
            </tr>
            <?php
            $assignments = $conn->query("
                SELECT b.id, n.uid, n.updated_at 
                FROM bikes b
                JOIN nfc_tags n ON b.card_id = n.id
            ");
            while ($row = $assignments->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['id']) ?></td>
                    <td><?= htmlspecialchars($row['uid']) ?></td>
                    <td><?= $row['updated_at'] ?></td>
                </tr>
            <?php endwhile; ?>
        </table>
    </div>
</body>
</html>