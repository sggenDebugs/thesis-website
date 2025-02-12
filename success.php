<?php
session_start();
require 'Bike.php';

// Verify Stripe payment success
if (isset($_GET['payment_intent'])) {
    \Stripe\Stripe::setApiKey('sk_test_51QrhroCGKzC3AGI8bAzGdkeZzFynXk2rLkyBbzWJWNIrYrYQdlA9hKmNRfGskcHfE5JCzEiiKlJMXwQ4CZcpalT300K7DRYjXn');
    $paymentIntent = \Stripe\PaymentIntent::retrieve($_GET['payment_intent']);
    
    if ($paymentIntent->status === 'succeeded') {
        $bikeManager->assignBike(
            $paymentIntent->metadata->bike_id,
            $paymentIntent->metadata->user_id,
            1 // NFC tag ID from your system
        );
    }
}

// Display success message and NFC tag