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
// if (isset($_GET['payment_intent'])) {
//     \Stripe\Stripe::setApiKey('sk_test_51QrhroCGKzC3AGI8bAzGdkeZzFynXk2rLkyBbzWJWNIrYrYQdlA9hKmNRfGskcHfE5JCzEiiKlJMXwQ4CZcpalT300K7DRYjXn');
//     $paymentIntent = \Stripe\PaymentIntent::retrieve($_GET['payment_intent']);
    
//     if ($paymentIntent->status === 'succeeded') {
//         $bikeManager->assignBike(
//             $paymentIntent->metadata->bike_id,
//             $paymentIntent->metadata->user_id,
//             1 // NFC tag ID from your system
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