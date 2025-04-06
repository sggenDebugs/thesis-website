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
        :root {
            --primary-color: #2196F3;
            --secondary-color: #1976D2;
            --background-color: #f5f5f5;
            --text-color: #333;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
            background-color: var(--background-color);
            color: var(--text-color);
        }

        h1 {
            color: var(--primary-color);
            text-align: center;
            margin-bottom: 30px;
            font-size: 2.5em;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background-color: white;
            box-shadow: 0 1px 3px rgba(0,0,0,0.2);
            border-radius: 8px;
            overflow: hidden;
        }

        th {
            background-color: var(--primary-color);
            color: white;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.9em;
        }

        th, td {
            padding: 12px 15px;
            text-align: left;
            border: none;
        }

        tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        tr:hover {
            background-color: #f1f4f6;
        }

        .paid-checkbox {
            cursor: pointer;
                    }

        .paid-label {
            display: inline-block;
            margin-right: 15px;
        }

        button {
            background-color: #dc3545;
            color: white;
            border: none;
            padding: 8px 12px;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #c82333;
        }

        a {
            color: var(--primary-color);
            text-decoration: none;
            transition: color 0.3s;
        }

        a:hover {
            color: var(--secondary-color);
        }

        .back-link {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background-color: var(--primary-color);
            color: white;
            border-radius: 4px;
            text-decoration: none;
            transition: background-color 0.3s;
        }

        .back-link:hover {
            background-color: var(--secondary-color);
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
    <div class="container">
        <h1>Expense Tracker</h1>
        <div class="table-responsive">
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
        </div>
        <a href="index.html" class="back-link">Back to Input</a>
    </div>
</body>

</html>