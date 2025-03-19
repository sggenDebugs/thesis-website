<?php
require 'config.php';

try {
    // Handle delete operation
    if (isset($_GET['delete'])) {
        try {
            $stmt = $pdo->prepare("DELETE FROM expenses WHERE id = ?");
            $stmt->execute([$_GET['delete']]);
            header("Location: display.php");
            exit;
        } catch (PDOException $e) {
            die("Error deleting record: " . $e->getMessage());
        }
    }
} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}

try {
    $stmt = $pdo->query("SELECT * FROM expenses ORDER BY created_at DESC");
    $expenses = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>View Expenses</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 8px;
            border: 1px solid #ddd;
            text-align: left;
        }
    </style>
</head>

<body>
    <h1>All Expenses</h1>
    <table>
        <tr>
            <th>Purchased By</th>
            <th>Item</th>
            <th>Unit Cost</th>
            <th>Quantity</th>
            <th>Total</th>
            <th>Payment per Member</th>
            <th>Receipt</th>
            <th>Date</th>
            <th>Action</th>
        </tr>
        <?php foreach ($expenses as $expense): ?>
            <tr>
                <td><?= htmlspecialchars($expense['person_name']) ?></td>
                <td><?= htmlspecialchars($expense['item_name']) ?></td>
                <td><?= number_format($expense['cost'], 2) ?> Php</td>
                <td><?= $expense['quantity'] ?></td>
                <td><?= number_format($expense['total'], 2) ?> Php</td>
                <td><?= htmlspecialchars($expense['payment_per_member']) ?> Php</td>
                <td>
                    <?php if ($expense['receipt_path']): ?>
                        <a href="<?= $expense['receipt_path'] ?>" target="_blank">View Receipt</a>
                    <?php endif; ?>
                </td>
                <td><?= $expense['created_at'] ?></td>
                <td>
                    <form method="GET" onsubmit="return confirm('Delete this record?')">
                        <input type="hidden" name="delete" value="<?= $expense['id'] ?>">
                        <button type="submit">Delete</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
    <a href="index.html">Back to Input</a>
</body>

</html>