<?php
class Bike {
    private $mysqli;
    private $id;
    private $created_at;
    private $time_rented;
    private $time_returned;
    private $card_id;

    public function __construct($mysqli) {
        $this->mysqli = $mysqli;
    }

    public static function getBikeById($mysqli, $bike_id) {
        $query = "SELECT id, created_at, time_rented, time_returned, card_id FROM bikes WHERE id = ?";
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param('i', $bike_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            $bike = new Bike($mysqli);
            $bike->id = $row['id'];
            $bike->created_at = $row['created_at'];
            $bike->time_rented = $row['time_rented'];
            $bike->time_returned = $row['time_returned'];
            $bike->card_id = $row['card_id'];
            return $bike;
        }
        return null;
    }

    public static function getAvailableBikes($mysqli) {
        $bikes = [];
        $query = "SELECT id, created_at, time_rented, time_returned, card_id FROM bikes WHERE time_rented IS NULL OR time_returned IS NOT NULL";
        $stmt = $mysqli->prepare($query);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $bike = new Bike($mysqli);
            $bike->id = $row['id'];
            $bike->created_at = $row['created_at'];
            $bike->time_rented = $row['time_rented'];
            $bike->time_returned = $row['time_returned'];
            $bike->card_id = $row['card_id'];
            $bikes[] = $bike;
        }

        $stmt->close();
        return $bikes;
    }

    public function getId() { return $this->id; }
    public function getCreatedAt() { return $this->created_at; }
    public function getTimeRented() { return $this->time_rented; }
    public function getTimeReturned() { return $this->time_returned; }
    public function getCardId() { return $this->card_id; }

    public function isAvailable() {
        return $this->time_rented === null || $this->time_returned !== null;
    }

    public function reserve($user_id) {
        if (!$this->isAvailable()) {
            return false;
        }

        $current_time = date('Y-m-d H:i:s');
        $query = "UPDATE bikes SET time_rented = ?, card_id = ? WHERE id = ? AND (time_rented IS NULL OR time_returned IS NOT NULL)";
        $stmt = $this->mysqli->prepare($query);
        $stmt->bind_param('sii', $current_time, $user_id, $this->id);
        $stmt->execute();
        $affected = $stmt->affected_rows;
        $stmt->close();

        if ($affected > 0) {
            $this->time_rented = $current_time;
            $this->card_id = $user_id;
            return true;
        }
        return false;
    }
}
?>