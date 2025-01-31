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

// Function to add a record to the database
function addRecord($conn, $tableName, $data) {
    $columns = implode(", ", array_keys($data));
    $values = "'" . implode("', '", array_values($data)) . "'";
    $sql = "INSERT INTO $tableName ($columns) VALUES ($values)";

    if ($conn->query($sql) === TRUE) {
        echo "New record created successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $table = $_POST['table'];
    $data = $_POST['data'];

    addRecord($conn, $table, $data);
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Record</title>
</head>
<body>
    <h2>Add Record</h2>
    <form method="post" action="add_record.php">
        <label for="table">Table Name:</label>
        <select name="table" id="table">
            <option value="admins">Admins</option>
            <option value="bikes">Bikes</option>
            <option value="nfc_tags">NFC Tags</option>
            <option value="transactions">Transactions</option>
            <option value="users">Users</option>
        </select><br><br>

        <div id="fields">
            <!-- Fields will be dynamically added here based on the selected table -->
        </div>

        <button type="submit">Add Record</button>
    </form>

    <script>
        document.getElementById('table').addEventListener('change', function() {
            var table = this.value;
            var fieldsDiv = document.getElementById('fields');
            fieldsDiv.innerHTML = '';

            var fields = {
                'admins': ['id', 'created_at', 'last_signed_in_at', 'first_name', 'last_name', 'email', 'gov_id'],
                'bikes': ['id', 'rider_id', 'tag_id', 'size', 'created_at', 'last_used_at', 'status', 'longitude', 'latitude'],
                'nfc_tags': ['id', 'uid', 'client_id', 'admin_id', 'created_at', 'updated_at', 'status'],
                'transactions': ['id', 'client_id', 'invoice_num', 'payment_method', 'amount_due', 'status'],
                'users': ['id', 'created_at', 'last_signed_in_at', 'first_name', 'last_name', 'email', 'contact_num', 'gov_id']
            };

            fields[table].forEach(function(field) {
                var label = document.createElement('label');
                label.setAttribute('for', field);
                label.textContent = field + ':';

                var input = document.createElement('input');
                input.setAttribute('type', 'text');
                input.setAttribute('name', 'data[' + field + ']');
                input.setAttribute('id', field);

                fieldsDiv.appendChild(label);
                fieldsDiv.appendChild(input);
                fieldsDiv.appendChild(document.createElement('br'));
            });
        });
    </script>
</body>
</html>