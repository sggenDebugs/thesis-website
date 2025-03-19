<?php
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle file upload
    $receiptPath = null;
    if (isset($_FILES['receipt']) && $_FILES['receipt']['error'] === UPLOAD_ERR_OK) {
        $targetDir = "uploads/";
        $fileName = uniqid() . '_' . basename($_FILES['receipt']['name']);
        $targetFile = $targetDir . $fileName;
        
        if (move_uploaded_file($_FILES['receipt']['tmp_name'], $targetFile)) {
            $receiptPath = $targetFile;
        }
    }

    // Insert into database
    try {
        $stmt = $pdo->prepare("INSERT INTO expenses 
            (item_name, cost, receipt_path, quantity, total, payment_per_member)
            VALUES (?, ?, ?, ?, ?, ?)");
        
        $stmt->execute([
            $_POST['item_name'],
            $_POST['cost'],
            $receiptPath,
            $_POST['quantity'],
            $_POST['total'],
            $_POST['payment_per_member']
        ]);
        
        header("Location: index.html?success=1");
    } catch(PDOException $e) {
        die("Error: " . $e->getMessage());
    }
}
?>