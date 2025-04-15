<?php
session_start();
require 'classes/bike.php';
require 'classes/transaction.php';
require 'db/config.php';

// Redirect if not authenticated or missing bike ID
if (!isset($_SESSION['user_id']) || !isset($_GET['bike_id'])) {
    header("Location: login.php");
    exit();
}


$hostname = $_SERVER['HTTP_HOST'];

$bike_id = intval($_GET['bike_id']);

// Fetch the bike instance
$bike = Bike::getBikeById($conn, $bike_id);

// Check if the bike exists and is reserved
if (!$bike || !($bike->getTimeRented() && !$bike->getTimeReturned())) {
    header("Location: display_bikes.php");
    exit();
}

// Function to assign NFC tag to user
function assignNfcTagToUser($conn, $nfc_tag_id, $user_id) {
    // Check if the NFC tag exists and is not assigned to another user
    $sql = "SELECT assigned_user FROM nfc_tags WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $nfc_tag_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();

    if ($row) {
        if ($row['user_id'] === null) {
            // Tag is available, assign it to the user
            $sql = "UPDATE nfc_tags SET assigned_user = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('ii', $user_id, $nfc_tag_id);
            $stmt->execute();
            $stmt->close();
            return true;
        } else {
            // Tag is already assigned to another user
            return false;
        }
    } else {
        // Tag does not exist
        return false;
    }
}

// Handle payment method selection
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $payment_method = $_POST['payment_method'];
        $db_payment_method = $payment_method; // Already matches ENUM
        $amount = 100; // Base amount for 30 minutes; adjust as needed
        $duration_minutes = 30; // User-selected duration; you can make this dynamic

        // Start a transaction to ensure data consistency
        $conn->begin_transaction();

        switch ($payment_method) {
            case 'credit_card':
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
                                "unit_amount" => $amount * 100,
                            ],
                            "quantity" => 1,
                        ]],
                    ]);

                    http_response_code(303);
                    $_SESSION['checkout_session'] = $checkout_session->id;
                    $_SESSION['nfc_tag_id'] = $_POST['nfc_tag_id'];
                    header("Location: " . $checkout_session->url);
                    $conn->commit();
                    exit();
                } catch (Exception $e) {
                    $conn->rollback();
                    $error = "Stripe error: " . $e->getMessage();
                }
                break;

            case 'gcash':
                $gcash_url = "https://api.gcash.com/pay?" . http_build_query([
                    'amount' => $amount,
                    'bike_id' => $bike_id,
                    'user_id' => $_SESSION['user_id']
                ]);
                header("Location: $gcash_url");
                $conn->commit();
                exit();
                break;

            case 'cash':
                $nfc_tag_id = intval($_POST['nfc_tag_id']);
                if (assignNfcTagToUser($conn, $nfc_tag_id, $_SESSION['user_id'])) {
                    // Create a rental record
                    $rental_start = date('Y-m-d H:i:s');
                    $rental_end = date('Y-m-d H:i:s', strtotime($rental_start . ' + ' . $duration_minutes . ' minutes'));
                    $sql = "INSERT INTO rentals (bike_id, user_id, rental_start, rental_end, duration_minutes) VALUES (?, ?, ?, ?, ?)";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param('iissi', $bike_id, $_SESSION['user_id'], $rental_start, $rental_end, $duration_minutes);
                    $stmt->execute();
                    $rental_id = $conn->insert_id;
                    $stmt->close();

                    // Create a transaction record with the rental ID
                    $transaction = new Transaction($conn);
                    $transaction_id = $transaction->create($bike_id, $_SESSION['user_id'], $amount, $db_payment_method);
                    
                    // Update the transaction with the rental ID
                    $sql = "UPDATE transactions SET rental_id = ? WHERE id = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param('ii', $rental_id, $transaction_id);
                    $stmt->execute();
                    $stmt->close();

                    $transaction->updateStatus($transaction_id, 'completed');
                    $conn->commit();
                    header("Location: success.php?bike_id=$bike_id");
                    exit();
                } else {
                    $conn->rollback();
                    $error = "Failed to assign NFC tag: Tag is invalid or already assigned.";
                }
                break;

            default:
                $conn->rollback();
                $error = "Invalid payment method";
                break;
        }
    } catch (Exception $e) {
        $conn->rollback();
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
        <p>Hourly Rate: ₱100</p>

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
            <a href="cancel_reservation.php?bike_id=<?= htmlspecialchars($bike->getId(), ENT_QUOTES, 'UTF-8') ?>">Cancel</a>
        </form>
    </div>
</body>
</html>