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
        $fieldArray = explode(',', $fields);
        foreach ($fieldArray as $field) {
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
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['table'])) {
    $table = $_POST['table'];
    if ($conn->ping()) {
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
    } else {
        echo "Connection lost.";
    }
}
$conn->close();
?>
$conn->close();

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
    <title>RD CED - Database Viewer</title>
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
                    <li><a href="index.html" class="current">Home</a></li>
                    <li><a href="laboratory.html">Laboratory</a></li>
                    <li><a href="research.html">Research</a></li>
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
        <h2>Show Tables</h2>
        <form method="post" action="display_table.php" class="form-style">
            <button type="submit" name="table" value="admins" class="btn">Show Admins</button>
            <button type="submit" name="table" value="bikes" class="btn">Show Bikes</button>
            <button type="submit" name="table" value="nfc_tags" class="btn">Show NFC Tags</button>
            <button type="submit" name="table" value="transactions" class="btn">Show Transactions</button>
            <button type="submit" name="table" value="users" class="btn">Show Users</button>
        </form>
        <?php
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            if (isset($_POST['table'])) {
                $table = $_POST['table'];
                if ($conn->ping()) {
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
                } else {
                    echo "Connection lost.";
                }
            }
        }
        ?>
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