<?php
// handle_payment.php
\Stripe\Stripe::setApiKey('sk_test_51QrhroCGKzC3AGI8bAzGdkeZzFynXk2rLkyBbzWJWNIrYrYQdlA9hKmNRfGskcHfE5JCzEiiKlJMXwQ4CZcpalT300K7DRYjXn');
$payload = @file_get_contents('php://input');
$event = \Stripe\Event::constructFrom(json_decode($payload, true));

if ($event->type == 'payment_intent.succeeded') {
    $bikeManager->assignBike(
        $event->data->object->metadata->bike_id,
        $event->data->object->metadata->user_id,
        1 // NFC tag ID
    );
}
?>