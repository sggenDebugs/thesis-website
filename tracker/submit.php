<?php
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Calculate values
    $cost = (float)$_POST['cost'];
    $quantity = (int)$_POST['quantity'];
    $total = $cost * $quantity;
    $paymentPerMember = $total / 3;

    // Handle file upload
    $receiptPath = null;
    if (isset($_FILES['receipt']) && $_FILES['receipt']['error'] === UPLOAD_ERR_OK) {
        $targetDir = "uploads/";
        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0755, true);
        }
        
        // Validate image
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $detectedType = mime_content_type($_FILES['receipt']['tmp_name']);
        
        if (in_array($detectedType, $allowedTypes)) {
            $fileName = uniqid() . '_' . basename($_FILES['receipt']['name']);
            $targetFile = $targetDir . $fileName;
            
            if (move_uploaded_file($_FILES['receipt']['tmp_name'], $targetFile)) {
                $receiptPath = $targetFile;
            }
        }
    }

    // Insert into database
    try {
        $stmt = $pdo->prepare("INSERT INTO expenses 
            (item_name, cost, receipt_path, quantity, total, payment_per_member)
            VALUES (?, ?, ?, ?, ?, ?)");
        
        $stmt->execute([
            $_POST['item_name'],
            $cost,
            $receiptPath,
            $quantity,
            $total,
            $paymentPerMember
        ]);
        
        header("Location: display.php");
    } catch(PDOException $e) {
        die("Error: " . $e->getMessage());
    }
}
?>