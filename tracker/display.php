<?php
require 'config.php';

// Handle payment status updates
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)$_POST['id'];
    $member = $_POST['member'];
    $paid = isset($_POST['paid']) ? 1 : 0;

    try {
        $stmt = $pdo->prepare("UPDATE expenses 
            SET paid_$member = ?
            WHERE id = ?");
        $stmt->execute([$paid, $id]);
        exit;
    } catch (PDOException $e) {
        http_response_code(500);
        echo "Error updating status: " . $e->getMessage();
        exit;
    }
}

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

        .paid-checkbox {
            cursor: pointer;
        }

        .paid-label {
            display: inline-block;
            margin-right: 15px;
        }

        th,
        td {
            padding: 8px;
            border: 1px solid #ddd;
            text-align: left;
        }
    </style>
    <script>
        function updatePaymentStatus(id, member) {
            const checkbox = document.getElementById(`paid_${member}_${id}`);
            const paid = checkbox.checked ? 1 : 0;

            fetch('display.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `id=${id}&member=${member}&paid=${paid}`
                })
                .then(response => {
                    if (!response.ok) throw Error('Update failed');
                })
                .catch(error => {
                    console.error(error);
                    checkbox.checked = !checkbox.checked;
                });
        }
    </script>
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
            <th>INIGO Paid</th>
            <th>NINO Paid</th>
            <th>HANNAH Paid</th>
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
                    <label class="paid-label">
                        <input type="checkbox"
                            id="paid_inigo_<?= $expense['id'] ?>"
                            <?= $expense['paid_inigo'] ? 'checked' : '' ?>
                            onchange="updatePaymentStatus(<?= $expense['id'] ?>, 'inigo')"
                            class="paid-checkbox">
                    </label>
                </td>
                <td>
                    <label class="paid-label">
                        <input type="checkbox"
                            id="paid_nino_<?= $expense['id'] ?>"
                            <?= $expense['paid_nino'] ? 'checked' : '' ?>
                            onchange="updatePaymentStatus(<?= $expense['id'] ?>, 'nino')"
                            class="paid-checkbox">
                    </label>
                </td>
                <td>
                    <label class="paid-label">
                        <input type="checkbox"
                            id="paid_hannah_<?= $expense['id'] ?>"
                            <?= $expense['paid_hannah'] ? 'checked' : '' ?>
                            onchange="updatePaymentStatus(<?= $expense['id'] ?>, 'hannah')"
                            class="paid-checkbox">
                    </label>
                </td>
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