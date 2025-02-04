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

// Handle form submission and delete mechanism
$message = '';
$selected_table = $_POST['table'] ?? null;
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_ids'])) { // isset determines if a variable is set and is not NULL
    $delete_ids = $_POST['delete_ids'];
    foreach ($delete_ids as $id) {
        $conn->query("DELETE FROM $selected_table WHERE id = $id");
    } 
    $message = "Selected records deleted successfully";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <link rel="stylesheet" href="css/font-awesome.min.css">
    <link rel="stylesheet" href="css/technology-icons.css">
    <link rel="shortcut icon" type="image/png" href="img/favicon.png">
    <title>Delete Record</title>
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
                    <li><a href="add_record.php">Add Record</a></li>
                    <li><a href="delete_record.php" class="current">Delete Record</a></li>
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

    <!-- Delete Record Section -->
    <section class="wrapper">
        <h2>Delete Record</h2>
        <form method="post" action="delete_record.php" class="form-style">
            <label for="table">Table Name:</label>
            <select name="table" id="table" onchange="this.form.submit()">
                <option value="">Select a table</option>
                <option value="admins" <?= $selected_table == 'admins' ? 'selected' : '' ?>>Admins</option>
                <option value="bikes" <?= $selected_table == 'bikes' ? 'selected' : '' ?>>Bikes</option>
                <option value="nfc_tags" <?= $selected_table == 'nfc_tags' ? 'selected' : '' ?>>NFC Tags</option>
                <option value="transactions" <?= $selected_table == 'transactions' ? 'selected' : '' ?>>Transactions</option>
                <option value="users" <?= $selected_table == 'users' ? 'selected' : '' ?>>Users</option>
            </select><br><br>

            <?php if ($message) echo "<p>$message</p>"; ?>

            <?php if ($selected_table): ?>
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
                    <p>0 results</p>
                <?php endif; ?>
            <?php endif; ?>
        </form>
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
<?php $conn->close(); ?>