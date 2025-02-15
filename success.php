<?php
session_start();
require 'classes/bike.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
elseif (!isset($_GET['bike_id'])) {
    header("Location: dashboard.php");
    exit();
}

// Verify Stripe payment success
// if (isset($_GET['checkout_session'])) {
//     \Stripe\Stripe::setApiKey('sk_test_51QrhroCGKzC3AGI8bAzGdkeZzFynXk2rLkyBbzWJWNIrYrYQdlA9hKmNRfGskcHfE5JCzEiiKlJMXwQ4CZcpalT300K7DRYjXn');
//     $checkout_session = \Stripe\checkout_session::retrieve($_GET['checkout_session']);
//     $nfc_tag_id = $_SESSION['nfc_tag_id'];
    
//     if ($checkout_session->status === 'succeeded') {
//         $bikeManager->assignBike(
//             $checkout_session->metadata->bike_id,
//             $checkout_session->metadata->user_id,
//             $nfc_tag_id // NFC tag ID from your system
//         );
//     }
// }
?>

<!DOCTYPE html>
<html>
<head>
    <title>Payment Success</title>
</head>
<body>
    <h1>Payment Successful</h1>
    <p>Your bike is now ready for use. Have a great ride!</p>
</body>
</html>