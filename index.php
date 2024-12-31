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

function displayTable($conn, $tableName, $fields)
{
    $sql = "SELECT $fields FROM $tableName";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        echo "<table border='1'><tr>";
        foreach (explode(',', $fields) as $field) {
            echo "<th>" . trim($field) . "</th>";
        }
        echo "</tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            foreach (explode(',', $fields) as $field) {
                echo "<td>" . $row[trim($field)] . "</td>";
            }
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "0 results";
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['table'])) {
        $table = $_POST['table'];
        switch ($table) {
            case 'admins':
                displayTable($conn, 'admins', 'id, created_at, last_signed_in_at, first_name, last_name, email, gov_id');
                break;
            case 'bikes':
                displayTable($conn, 'bikes', 'id, rider_id, tag_id, size, created_at, last_used_at, status, longitude, latitude'); 
                break;
            case 'nfc_tags':
                displayTable($conn, 'nfc_tags', 'id, uid, client_id, admin_id, created_at, updated_at, status'); 
                break;
            case 'transactions':
                displayTable($conn, 'transactions', 'id, client_id, invoice_num, payment_method, amount_due, status'); 
                break;
            case 'users':
                displayTable($conn, 'users', 'id, created_at, last_signed_in_at, first_name, last_name, email, contact_num, gov_id'); 
                break;
        }
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html>

<head>
    <title>Manage Tables</title>
    <script>
        function updateFields(table) {
            let fields = {
                'admins': ['first_name', 'last_name', 'email', 'gov_id'],
                'bikes': ['rider_id', 'tag_id', 'size', 'status', 'longitude', 'latitude'],
                'nfc_tags': ['uid', 'client_id', 'admin_id', 'status'],
                'transactions': ['client_id', 'invoice_num', 'payment_method', 'amount_due', 'status'],
                'users': ['first_name', 'last_name', 'email', 'contact_num', 'gov_id']
            };

            let form = document.getElementById('addForm');
            form.innerHTML = '';
            fields[table].forEach(field => {
                let input = document.createElement('input');
                input.type = 'text';
                input.name = field;
                input.placeholder = field;
                form.appendChild(input);
                form.appendChild(document.createElement('br'));
            });

            let tableInput = document.createElement('input');
            tableInput.type = 'hidden';
            tableInput.name = 'table';
            tableInput.value = table;
            form.appendChild(tableInput);

            let submit = document.createElement('button');
            submit.type = 'submit';
            submit.name = 'add';
            submit.textContent = 'Add Record';
            form.appendChild(submit);
        }
    </script>
</head>

<body>
    <form method="post">
        <button type="submit" name="table" value="admins" onclick="updateFields('admins'); return false;">Show Admins</button>
        <button type="submit" name="table" value="bikes" onclick="updateFields('bikes'); return false;">Show Bikes</button>
        <button type="submit" name="table" value="nfc_tags" onclick="updateFields('nfc_tags'); return false;">Show NFC Tags</button>
        <button type="submit" name="table" value="transactions" onclick="updateFields('transactions'); return false;">Show Transactions</button>
        <button type="submit" name="table" value="users" onclick="updateFields('users'); return false;">Show Users</button>
    </form>
    <form id="addForm" method="post" action="addRecord.php">
        <!-- Dynamic fields will be added here -->
    </form>
</body>

</html>