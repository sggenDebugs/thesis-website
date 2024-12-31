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

function updateRecord($conn, $tableName, $id, $fields)
{
    $setClause = [];
    foreach ($fields as $field => $value) {
        $setClause[] = "$field='$value'";
    }
    $setClause = implode(', ', $setClause);
    $sql = "UPDATE $tableName SET $setClause WHERE id=$id";

    if ($conn->query($sql) === TRUE) {
        echo "Record updated successfully";
    } else {
        echo "Error updating record: " . $conn->error;
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $table = $_POST['table'];
    $id = $_POST['id'];
    $fields = json_decode($_POST['fields'], true);
    updateRecord($conn, $table, $id, $fields);
}

$conn->close();
?>