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

$selectedtable = $_POST['table'] ?? null;
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
    <title>RD CED - Database Viewer</title>
    <style>
    h3 {
        font-size: 200%;
        font-weight: 500;
        line-height: 160%;
        text-align: center;
        padding: 2% 0;
        color: #000000;
    }

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
                    <li><a href="index.php" class="current">Display Tables</a></li>
                    <li><a href="add_record.php">Add Record</a></li>
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

    <!-- Database Table Display Section -->
    <section class="wrapper">
        <h2>Database Tables</h2>
        <form method="post" action="index.php" class="form-style">
            <label for="table">Table Name:</label>
            <select name="table" id="table" onchange="this.form.submit()">
                <option value="">Select a table</option>
                <option value="admins" <?= $selectedtable == 'admins' ? 'selected' : '' ?>>Admins</option>
                <option value="bikes" <?= $selectedtable == 'bikes' ? 'selected' : '' ?>>Bikes</option>
                <option value="nfc_tags" <?= $selectedtable == 'nfc_tags' ? 'selected' : '' ?>>NFC Tags</option>
                <option value="transactions" <?= $selectedtable == 'transactions' ? 'selected' : '' ?>>Transactions</option>
                <option value="users" <?= $selectedtable == 'users' ? 'selected' : '' ?>>Users</option>
            </select><br><br>
        </form>

        <?php if ($selectedtable): ?>
        <?php
        // Get table columns
            $result = $conn->query("SHOW COLUMNS FROM $selectedtable");
            $columns = [];
            while ($row = $result->fetch_assoc()) {
                $columns[] = $row['Field'];
            }

            // Display records
            $result = $conn->query("SELECT * FROM $selectedtable");
            if ($result->num_rows > 0): ?>
                <table class="styled-table">
                    <thead>
                        <tr>
                            <?php foreach ($columns as $column): ?>
                                <th><?= ucfirst(str_replace('_', ' ', $column)) ?></th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <?php foreach ($columns as $column): ?>
                                <td><?= $row[$column] ?></td>
                            <?php endforeach; ?>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>0 results</p>
            <?php endif; ?>
        <?php endif; ?>
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