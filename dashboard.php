<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Dashboard</title>
        <link rel="stylesheet" type="text/css" href="css/style.css">
    </head>
    <body>
        <h1>Welcome to the Dashboard</h1>
        <p>You are logged in as <?php echo $_SESSION['email']; ?>.</p>
        <a href="logout.php">Logout</a>
</html>