<?php
class Transaction {
    private $id;
    private $client_id;
    private $payment_method;
    private $status;
    private $mysqli;

    public function __construct($mysqli) {
        $this->mysqli = $mysqli;
    }

    // Getters
    public function getId() {
        return $this->id;
    }
    public function getClientId() {
        return $this->client_id;
    }
    public function getPaymentMethod() {
        return $this->payment_method;
    }
    public function getStatus() {
        return $this->status;
    }

    /**
     * Create a new transaction
     */
    public function create($bike_id, $client_id, $amount, $payment_method) {
        // Prepare the insert query with invoice_num set to NULL
        $invoice_num = NULL;
        $sql = "INSERT INTO transactions (bike_id, client_id, amount_due, payment_method, status, invoice_num) 
                VALUES (?, ?, ?, ?, 'pending', ?)";
        $stmt = $this->mysqli->prepare($sql);
        
        // Bind parameters, including NULL for invoice_num
        $stmt->bind_param('iidsi', $bike_id, $client_id, $amount, $payment_method, $invoice_num);
    
        // Execute the insert
        if ($stmt->execute()) {
            // Get the ID of the newly inserted transaction
            $this->id = $stmt->insert_id;
            $this->client_id = $client_id;
            $this->payment_method = $payment_method;
            $this->status = 'pending';
    
            // Update invoice_num to match the transaction ID
            $update_sql = "UPDATE transactions SET invoice_num = ? WHERE id = ?";
            $update_stmt = $this->mysqli->prepare($update_sql);
            $update_stmt->bind_param('ii', $this->id, $this->id);
            $update_stmt->execute();
            $update_stmt->close();
    
            // Return the transaction ID
            return $this->id;
        } else {
            throw new Exception("Transaction creation failed: " . $stmt->error);
        }
        
        $stmt->close();
    }
    /**
     * Update transaction status
     */
    public function updateStatus($transaction_id, $status) {
        $valid_statuses = ['pending', 'completed', 'failed'];
        if (!in_array($status, $valid_statuses)) {
            throw new InvalidArgumentException("Invalid status value");
        }

        $sql = "UPDATE transactions SET status = ? WHERE id = ?";
        $stmt = $this->mysqli->prepare($sql);
        $stmt->bind_param('si', $status, $transaction_id);

        if ($stmt->execute()) {
            $this->status = $status;
            return true;
        }
        throw new Exception("Status update failed: " . $stmt->error);
    }

    /**
     * Retrieve transaction by ID
     */
    public static function getById($mysqli, $transaction_id) {
        $sql = "SELECT id, client_id, payment_method, status FROM transactions WHERE id = ?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param('i', $transaction_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) return null;

        $data = $result->fetch_assoc();
        $transaction = new Transaction($mysqli);
        $transaction->id = $data['id'];
        $transaction->client_id = $data['client_id'];
        $transaction->payment_method = $data['payment_method'];
        $transaction->status = $data['status'];

        return $transaction;
    }

    /**
     * Get all transactions for a client
     */
    public static function getByClient($mysqli, $client_id) {
        $sql = "SELECT id, payment_method, status FROM transactions WHERE client_id = ?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param('i', $client_id);
        $stmt->execute();
        $result = $stmt->get_result();

        $transactions = [];
        while ($row = $result->fetch_assoc()) {
            $transaction = new Transaction($mysqli);
            $transaction->id = $row['id'];
            $transaction->client_id = $client_id;
            $transaction->payment_method = $row['payment_method'];
            $transaction->status = $row['status'];
            $transactions[] = $transaction;
        }

        return $transactions;
    }
}
?>