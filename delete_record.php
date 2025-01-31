<?php
$servername = "127.0.0.1";
$username = "u388284544_sggen";
$password = "xB@akashinji420x";
$dbname = "u388284544_server";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle record deletion
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_ids'])) {
    $table = $_POST['table'];
    $delete_ids = $_POST['delete_ids'];
    
    foreach ($delete_ids as $id) {
        $sql = "DELETE FROM $table WHERE id = " . intval($id);
        if (!$conn->query($sql)) {
            echo "Error deleting record: " . $conn->error;
        }
    }
    echo "<p class='success'>Selected records deleted successfully!</p>";
}

$selected_table = $_POST['table'] ?? '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Records</title>
    <style>
        /* Reuse styles from previous files */
        .styled-table {
            width: 100%;
            border-collapse: collapse;
            margin: 25px 0;
            font-size: 0.9em;
            font-family: sans-serif;
            min-width: 400px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.15);
        }

        .styled-table thead tr {
            background-color: #009879;
            color: #ffffff;
            text-align: left;
        }

        .styled-table th,
        .styled-table td {
            padding: 12px 15px;
        }

        .styled-table tbody tr {
            border-bottom: 1px solid #dddddd;
        }

        .styled-table tbody tr:nth-of-type(even) {
            background-color: #f3f3f3;
        }

        .styled-table tbody tr:last-of-type {
            border-bottom: 2px solid #009879;
        }

        .styled-table tbody tr:hover {
            background-color: #f1f1f1;
        }

        .btn {
            background-color: #dc3545;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            margin: 5px;
        }

        .btn:hover {
            background-color: #c82333;
        }

        .success {
            color: green;
            padding: 10px;
        }
    </style>
</head>
<body>
    <section class="banner">
        <div class="bannerInside">
            <img src="img/logo.png" center>
        </div>
    </section>
    
    <!-- Navigation (same as your other files) -->
    
    <section class="wrapper">
        <h2>Delete Records</h2>
        <form method="post" action="delete_record.php">
            <label for="table">Select Table:</label>
            <select name="table" id="table" onchange="this.form.submit()">
                <option value="">Select a table</option>
                <option value="admins" <?= $selected_table == 'admins' ? 'selected' : '' ?>>Admins</option>
                <option value="bikes" <?= $selected_table == 'bikes' ? 'selected' : '' ?>>Bikes</option>
                <option value="nfc_tags" <?= $selected_table == 'nfc_tags' ? 'selected' : '' ?>>NFC Tags</option>
                <option value="transactions" <?= $selected_table == 'transactions' ? 'selected' : '' ?>>Transactions</option>
                <option value="users" <?= $selected_table == 'users' ? 'selected' : '' ?>>Users</option>
            </select>
        </form>

        <?php if ($selected_table): ?>
        <form method="post" action="delete_record.php">
            <input type="hidden" name="table" value="<?= $selected_table ?>">
            
            <?php
            // Get table columns
            $result = $conn->query("SHOW COLUMNS FROM $selected_table");
            $columns = [];
            while ($row = $result->fetch_assoc()) {
                $columns[] = $row['Field'];
            }
            
            // Display records
            $result = $conn->query("SELECT * FROM $selected_table");
            if ($result->num_rows > 0): ?>
                <table class="styled-table">
                    <thead>
                        <tr>
                            <th>Select</th>
                            <?php foreach ($columns as $column): ?>
                                <th><?= ucfirst(str_replace('_', ' ', $column)) ?></th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><input type="checkbox" name="delete_ids[]" value="<?= $row['id'] ?>"></td>
                            <?php foreach ($columns as $column): ?>
                                <td><?= $row[$column] ?></td>
                            <?php endforeach; ?>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
                <button type="submit" class="btn">Delete Selected Records</button>
            <?php else: ?>
                <p>No records found in this table.</p>
            <?php endif; ?>
        </form>
        <?php endif; ?>
    </section>

    <!-- Footer (same as your other files) -->
</body>
</html>
<?php $conn->close(); ?>