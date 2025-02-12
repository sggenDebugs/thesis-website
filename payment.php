<?php
session_start();
require 'classes/bike.php';

// Redirect if not authenticated or missing bike ID
if (!isset($_SESSION['user_id']) || !isset($_GET['bike_id'])) {
    header("Location: login.php");
    exit();
}

$servername = "127.0.0.1";
$username = "u388284544_sggen";
$password = "xB@akashinji420x";
$dbname = "u388284544_server";

// Database connection
$mysqli = new mysqli($servername, $username, $password, $dbname);
$bikeManager = new Bike($mysqli);
$bike_id = intval($_GET['bike_id']);

// Verify bike reservation status
$bike = Bike::getBikeById($mysqli, $bike_id);
if (!$bike || $bike->getStatus() !== 'reserved') {
    header("Location: display_bikes.php");
    exit();
}

// Handle payment method selection
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $payment_method = $_POST['payment_method'];
    
    switch ($payment_method) {
        case 'credit_card':
            // Process Stripe payment
            require_once 'vendor/autoload.php';
            \Stripe\Stripe::setApiKey('sk_test_51QrhroCGKzC3AGI8bAzGdkeZzFynXk2rLkyBbzWJWNIrYrYQdlA9hKmNRfGskcHfE5JCzEiiKlJMXwQ4CZcpalT300K7DRYjXn');
            
            try {
                $paymentIntent = \Stripe\PaymentIntent::create([
                    'amount' => 50 * 100, // Amount in cents
                    'currency' => 'php',
                    'metadata' => [
                        'bike_id' => $bike_id,
                        'user_id' => $_SESSION['user_id']
                    ]
                ]);
                
                $_SESSION['payment_intent'] = $paymentIntent->id;
                header("Location: " . $paymentIntent->next_action->redirect_to_url->url);
                exit();
            } catch (Exception $e) {
                $error = "Stripe error: " . $e->getMessage();
            }
            break;

        case 'gcash':
            // Simulate GCash payment redirect
            $gcash_url = "https://api.gcash.com/pay?" . http_build_query([
                'amount' => $bike->getHourlyRate(),
                'bike_id' => $bike_id,
                'user_id' => $_SESSION['user_id']
            ]);
            header("Location: $gcash_url");
            exit();
            break;

        case 'cash':
            // Directly assign bike for cash payments
            if ($bikeManager->assignBike($bike_id, $_SESSION['user_id'], 1)) { // 1 = sample NFC tag ID
                header("Location: success.php?bike_id=$bike_id");
                exit();
            } else {
                $error = "Failed to process cash payment";
            }
            break;

        default:
            $error = "Invalid payment method";
            break;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment</title>
    <style>
        .payment-container { max-width: 600px; margin: 2rem auto; padding: 2rem; border: 1px solid #ddd; }
        .payment-method { margin: 1rem 0; padding: 1rem; border: 1px solid #eee; cursor: pointer; }
        .payment-method:hover { background: #f8f9fa; }
        .error { color: red; margin-bottom: 1rem; }
    </style>
</head>
<body>
    <div class="payment-container">
        <h1>Complete Your Rental</h1>
        <h2>Bike #<?= htmlspecialchars($bike->getId()) ?></h2>
        <p>Hourly Rate: ₱<?= htmlspecialchars($bike->getHourlyRate()) ?></p>
        
        <?php if ($error): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST">
            <h3>Select Payment Method</h3>
            
            <div class="payment-method">
                <label>
                    <input type="radio" name="payment_method" value="credit_card" required>
                    Credit Card (Stripe)
                </label>
            </div>

            <div class="payment-method">
                <label>
                    <input type="radio" name="payment_method" value="gcash">
                    GCash
                </label>
            </div>

            <div class="payment-method">
                <label>
                    <input type="radio" name="payment_method" value="cash">
                    Cash Payment
                </label>
            </div>

            <button type="submit">Continue to Payment</button>
            <a href="display_bikes.php">Cancel</a>
        </form>
    </div>
</body>
</html>