<?php
require 'config.php';

try {
    $stmt = $pdo->query("SELECT * FROM expenses ORDER BY created_at DESC");
    $expenses = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Expenses</title>
    <style>
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 8px; border: 1px solid #ddd; text-align: left; }
    </style>
</head>
<body>
    <h1>All Expenses</h1>
    <table>
        <tr>
            <th>Item</th>
            <th>Cost</th>
            <th>Quantity</th>
            <th>Total</th>
            <th>Payment per Member</th>
            <th>Receipt</th>
            <th>Date</th>
        </tr>
        <?php foreach ($expenses as $expense): ?>
        <tr>
            <td><?= htmlspecialchars($expense['item_name']) ?></td>
            <td>$<?= number_format($expense['cost'], 2) ?></td>
            <td><?= $expense['quantity'] ?></td>
            <td>$<?= number_format($expense['total'], 2) ?></td>
            <td><?= htmlspecialchars($expense['payment_per_member']) ?></td>
            <td>
                <?php if ($expense['receipt_path']): ?>
                    <a href="<?= $expense['receipt_path'] ?>" target="_blank">View Receipt</a>
                <?php endif; ?>
            </td>
            <td><?= $expense['created_at'] ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>