<?php
session_start();
require 'classes/bike.php';
require 'classes/transaction.php';

// Redirect if not authenticated or missing bike ID
if (!isset($_SESSION['user_id']) || !isset($_GET['bike_id'])) {
    header("Location: login.php");
    exit();
}

$servername = "127.0.0.1";
$username = "u388284544_sggen";
$password = "xB@akashinji420x";
$dbname = "u388284544_server";

$hostname = $_SERVER['HTTP_HOST'];

// Database connection
$mysqli = new mysqli($servername, $username, $password, $dbname);
$bikeManager = new Bike($mysqli);
$bike_id = intval($_GET['bike_id']);

// Verify bike reservation status
$bike = Bike::getBikeById($mysqli, $bike_id); // call the assigned bike
if (!$bike || $bike->getStatus() !== 'reserved') {
    header("Location: display_bikes.php");
    exit();
}

// Handle payment method selection
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $transaction = new Transaction($mysqli);
        $transaction_id = $transaction->create($_SESSION['user_id'], $_POST['payment_method']);
        $payment_method = $_POST['payment_method'];

        switch ($payment_method) {
            case 'credit_card':
                // Process Stripe payment
                require_once 'vendor/autoload.php';
                \Stripe\Stripe::setApiKey('sk_test_51QrhroCGKzC3AGI8bAzGdkeZzFynXk2rLkyBbzWJWNIrYrYQdlA9hKmNRfGskcHfE5JCzEiiKlJMXwQ4CZcpalT300K7DRYjXn');

                try {
                    $checkout_session = \Stripe\Checkout\Session::create([
                        "mode" => "payment",
                        "success_url" => "http://$hostname/success.php?bike_id=$bike_id",
                        "line_items" => [[
                            "price_data" => [
                                "currency" => "php",
                                "product_data" => [
                                    "name" => "Bike Rental",
                                ],
                                "unit_amount" => $bike->getHourlyRate() * 100,
                            ],
                            "quantity" => 1,
                        ]],
                    ]);

                    http_response_code(303);
                    $_SESSION['checkout_session'] = $checkout_session->id;
                    $_SESSION['nfc_tag_id'] = $_POST['nfc_tag_id'];
                    header("Location: " . $checkout_session->url);
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
                try {
                    $sql = "INSERT INTO payments (bike_id, user_id, amount, payment_method) VALUES (?, ?, ?, ?)";
                    exit();
                } catch (Exception $e) {
                    $error = "Failed to reserve bike: " . $e->getMessage();
                    break;
                }
                // Directly assign bike for cash payments
                $nfc_tag_id = $_POST['nfc_tag_id']; // Get NFC tag ID from form input
                if ($bikeManager->assignBike($bike_id, $_SESSION['user_id'], $nfc_tag_id)) {
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
    } catch (Exception $e) {
        $error = "Failed to process payment: " . $e->getMessage();
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
        .payment-container {
            max-width: 600px;
            margin: 2rem auto;
            padding: 2rem;
            border: 1px solid #ddd;
        }

        .payment-method {
            margin: 1rem 0;
            padding: 1rem;
            border: 1px solid #eee;
            cursor: pointer;
        }

        .payment-method:hover {
            background: #f8f9fa;
        }

        .error {
            color: red;
            margin-bottom: 1rem;
        }
    </style>
</head>

<body>
    <div class="payment-container">
        <h2>Bike #<?= htmlspecialchars($bike->getId(), ENT_QUOTES, 'UTF-8') ?></h2>
        <h2>Bike #<?= htmlspecialchars($bike->getId(), ENT_QUOTES, 'UTF-8') ?></h2>
        <p>Hourly Rate: ₱<?= htmlspecialchars($bike->getHourlyRate(), ENT_QUOTES, 'UTF-8') ?></p>

        <?php if ($error): ?>
            <div class="error"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
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

            <div class="payment-method">
                <label>
                    NFC Tag ID:
                    <input type="text" name="nfc_tag_id" required>
                </label>
            </div>
            <button type="submit">Continue to Payment</button>
            <a href="cancel_reservation.php?bike_id=<?= htmlspecialchars($bike->getId()) ?>">Cancel</a>
        </form>
    </div>
</body>

</html>