<?php

class Transaction
{
    private $id;
    private $client_id;
    private $payment_method;
    private $status;
    private $mysqli;

    public function __construct($mysqli)
    {
        $this->mysqli = $mysqli;
    }

    // Getters
    public function getId()
    {
        return $this->id;
    }
    public function getClientId()
    {
        return $this->client_id;
    }
    public function getPaymentMethod()
    {
        return $this->payment_method;
    }
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Create a new transaction
     */
    public function create($client_id, $payment_method)
    {
        $sql = "INSERT INTO transactions (client_id, payment_method, status) VALUES (?, ?, 'pending')";
        $stmt = $this->mysqli->prepare($sql);
        $stmt->bind_param('is', $client_id, $payment_method);

        if ($stmt->execute()) {
            $this->id = $stmt->insert_id;
            $this->client_id = $client_id;
            $this->payment_method = $payment_method;
            $this->status = 'pending';
            return $this->id;
        }
        throw new Exception("Transaction creation failed: " . $stmt->error);
    }

    /**
     * Update transaction status
     */
    public function updateStatus($transaction_id, $status)
    {
        $valid_statuses = ['pending', 'completed', 'failed'];
        if (!in_array($status, $valid_statuses)) {
            throw new InvalidArgumentException("Invalid status value");
        }

        $sql = "UPDATE transactions SET status = ? WHERE id = ?";
        $stmt = $this->mysqli->prepare("
            UPDATE transactions 
            SET status = ? 
            WHERE id = ?
        ");
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
    public static function getById($mysqli, $transaction_id)
    {
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
    public static function getByClient($mysqli, $client_id)
    {
        $sql = "SELECT id, payment_method, status, created_at FROM transactions WHERE client_id = ?";
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
