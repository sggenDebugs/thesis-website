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
        return "New record created successfully";
    } else {
        return "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Handle form submission
$message = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $table = $_POST['table'];
    $data = $_POST['data'];

    // Add the current timestamp for created_at and last_signed_in_at fields (if applicable)
    if ($table === 'admins') {
        $data['created_at'] = date('Y-m-d H:i:s'); // Current timestamp
        $data['last_signed_in_at'] = date('Y-m-d H:i:s'); // Current timestamp
    }

    $message = addRecord($conn, $table, $data);
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" type="text/css" href="style.css">
    <link rel="stylesheet" href="css/font-awesome.min.css">
    <link rel="stylesheet" href="css/technology-icons.css">
    <link rel="shortcut icon" type="image/png" href="img/favicon.png">
    <title>Add Record</title>
    <style>
    /* Style for the table */
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

    /* Style for buttons */
    .btn {
        background-color: #009879;
        color: white;
        padding: 10px 20px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-size: 16px;
        margin: 5px;
    }

    .btn:hover {
        background-color: #007f63;
    }
    </style>
</head>

<body>
    <section class="banner">
        <div class="bannerInside">
            <img src="img/logo.png" center>
        </div>
    </section>
    <header>
        <div id="header">
            <a href="index.html" id="rdlogo"></a>
            <nav>
                <a href="#" id="menu_icon"></a>
                <ul id="menu">
                    <li><a href="index.php">Display Tables</a></li>
                    <li><a href="add_record.php" class="current">Add Record</a></li>
                    <li><a href="delete_record.php">Delete Record</a></li>
                    <li><a href="#">Services</a>
                        <ul class="hidden">
                            <li><a href="3dprinting.html">3D Printing</a></li>
                            <li><a href="design.html">PCB Design</a></li>
                        </ul>
                    </li>
                    <li><a href="#">Tutorials</a>
                        <ul class="hidden">
                            <li><a href="qt.html">Qt C++</a></li>
                            <li><a href="xhtml.html">XHTML</a></li>
                            <li><a href="php.html">php</a></li>
                            <li><a href="mysql.html">MySql</a></li>
                        </ul>
                    </li>
                    <li><a href="Contact.html">Contact</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <!-- Add Record Section -->
    <section class="wrapper">
        <h2>Add Record</h2>
        <form method="post" action="add_record.php" class="form-style">
            <label for="table">Table Name:</label>
            <select name="table" id="table">
                <option value="admins">Admins</option>
                <option value="bikes">Bikes</option>
                <option value="nfc_tags">NFC Tags</option>
                <option value="transactions">Transactions</option>
                <option value="users">Users</option>
            </select><br><br>

            <?php if ($message) echo "<p>$message</p>"; ?>

            <div id="table-name"></div>
            <div id="fields">
                <!-- Fields will be dynamically added here based on the selected table -->
            </div>

            <button type="submit" class="btn">Add Record</button>
        </form>

        <script>
        document.getElementById('table').addEventListener('change', function() {
            var table = this.value;
            var fieldsDiv = document.getElementById('fields');
            var tableNameDiv = document.getElementById('table-name');
            fieldsDiv.innerHTML = '';
            tableNameDiv.innerHTML = '<h3>Table: ' + table.charAt(0).toUpperCase() + table.slice(1) + '</h3>';

            // Define fields for each table
            var fields = {
                'admins': ['first_name', 'last_name', 'email', 'gov_id'],
                'bikes': ['rider_id', 'tag_id', 'size', 'status', 'longitude', 'latitude'],
                'nfc_tags': ['uid', 'client_id', 'admin_id', 'status'],
                'transactions': ['client_id', 'invoice_num', 'payment_method', 'amount_due', 'status'],
                'users': ['first_name', 'last_name', 'email', 'contact_num', 'gov_id']
            };

            // Dynamically generate input fields for the selected table
            fields[table].forEach(function(field) {
                var label = document.createElement('label');
                label.setAttribute('for', field);
                label.textContent = field + ':';

                var input;
                if (table === 'bikes' && (field === 'rider_id' || field === 'tag_id')) {
                    input = document.createElement('select');
                    input.setAttribute('name', 'data[' + field + ']');
                    input.setAttribute('id', field);

                    // Fetch existing riders or tags from the database
                    fetch(`fetch_${field}.php`)
                        .then(response => response.json())
                        .then(data => {
                            data.forEach(item => {
                                var option = document.createElement('option');
                                option.value = item.id;
                                option.textContent = item.name;
                                input.appendChild(option);
                            });
                        });
                } else if (table === 'bikes' && field === 'status') {
                    input = document.createElement('select');
                    input.setAttribute('name', 'data[' + field + ']');
                    input.setAttribute('id', field);

                    ['active', 'inactive', 'under maintenance', 'removed'].forEach(status => {
                        var option = document.createElement('option');
                        option.value = status;
                        option.textContent = status;
                        input.appendChild(option);
                    });
                } else if (table === 'bikes' && field === 'size') {
                    input = document.createElement('select');
                    input.setAttribute('name', 'data[' + field + ']');
                    input.setAttribute('id', field);

                    ['small', 'large'].forEach(size => {
                        var option = document.createElement('option');
                        option.value = size;
                        option.textContent = size;
                        input.appendChild(option);
                    });
                } else {
                    input = document.createElement('input');
                    input.setAttribute('type', 'text');
                    input.setAttribute('name', 'data[' + field + ']');
                    input.setAttribute('id', field);
                }

                fieldsDiv.appendChild(label);
                fieldsDiv.appendChild(input);
                fieldsDiv.appendChild(document.createElement('br'));
            });
        });
        </script>
    </section>

    <!-- Footer Section -->
    <footer>
        <ul class="socialMedia">
            <li><a href="https://www.facebook.com" target="_blank"><i class="fa fa-facebook"></i></a></li>
            <li><a href="https://www.plus.google.com" target="_blank"><i class="fa fa-google-plus"></i></a></li>
            <li><a href="https://www.twitter.com" target="_blank"><i class="fa fa-twitter"></i></a></li>
            <li><a href="https://www.youtube.com" target="_blank"><i class="fa fa-youtube"></i></a></li>
        </ul>
    </footer>
    <footer class="last">
        <p>&copy; IP</p>
    </footer>
</body>

</html>