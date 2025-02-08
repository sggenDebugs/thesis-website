<?php
class Bike
{
    private $id;
    private $user_id;     // Foreign key to users table
    private $tag_id;      // Foreign key to nfc_tags table
    private $created_at;
    private $last_used_at;
    private $status;      // e.g., 'available', 'reserved', 'in_use'
    private $location;
    private $hourly_rate;
    private $reserved_until;
    private $mysqli;

    // Constructor
    public function __construct($mysqli)
    {
        $this->mysqli = $mysqli;
    }

    // Getters
    public function getId() { return $this->id; }
    public function getUserId() { return $this->user_id; }
    public function getTagId() { return $this->tag_id; }
    public function getCreatedAt() { return $this->created_at; }
    public function getLastUsedAt() { return $this->last_used_at; }
    public function getLocation() { return $this->location; }
    public function getStatus() { return $this->status; }
    public function getHourlyRate() { return $this->hourly_rate; }
    public function getReservedUntil() { return $this->reserved_until; }

    // Setters (optional)
    public function setStatus($status) { $this->status = $status; }

    /**
     * Fetch all available bikes from the database
     * @return array Array of Bike objects
     */
    public function getAvailableBikes()
    {
        $bikes = [];
        $current_time = date('Y-m-d H:i:s');

        $query = "
            SELECT id, user_id, tag_id, created_at, last_used_at, 
                   status, location, hourly_rate, reserved_until 
            FROM bikes 
            WHERE status = 'active' 
            AND user_id IS NULL 
            AND (reserved_until IS NULL OR reserved_until < ?)
        ";

        $stmt = $this->mysqli->prepare($query);
        $stmt->bind_param('s', $current_time);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $bike = new Bike($this->mysqli);
            self::populateBikeFromRow($bike, $row);
            $bikes[] = $bike;
        }

        $stmt->close();
        return $bikes;
    }

    /**
     * Reserve a bike for a user
     * @param int $bike_id
     * @param int $user_id
     * @return bool True if reservation succeeded
     */
    public function reserveBike($bike_id, $user_id)
    {
        $reserved_until = date('Y-m-d H:i:s', strtotime('+10 minutes'));

        $query = "
            UPDATE bikes 
            SET status = 'reserved', reserved_until = ?
            WHERE id = ? 
            AND status = 'active'
            AND user_id IS NULL
        ";

        $stmt = $this->mysqli->prepare($query);
        $stmt->bind_param('si', $reserved_until, $bike_id);
        $success = $stmt->execute();
        $stmt->close();

        return $success;
    }

    /**
     * Assign a bike to a user with NFC tag (after payment)
     * @param int $bike_id
     * @param int $user_id
     * @param int $tag_id
     * @return bool True if assignment succeeded
     */
    public function assignBike($bike_id, $user_id, $tag_id)
    {
        $query = "
            UPDATE bikes 
            SET 
                status = 'in_use',
                user_id = ?,
                tag_id = ?,
                reserved_until = NULL,
                last_used_at = NOW()
            WHERE id = ?
            AND status = 'reserved'
        ";

        $stmt = $this->mysqli->prepare($query);
        $stmt->bind_param('iii', $user_id, $tag_id, $bike_id);
        $success = $stmt->execute();
        $stmt->close();

        return $success;
    }

    /**
     * Mark bike as available after return
     * @param int $bike_id
     * @return bool True if update succeeded
     */
    public function markAsAvailable($bike_id)
    {
        $query = "
            UPDATE bikes 
            SET 
                status = 'available',
                user_id = NULL,
                tag_id = NULL,
                reserved_until = NULL,
                last_used_at = NOW()
            WHERE id = ?
        ";

        $stmt = $this->mysqli->prepare($query);
        $stmt->bind_param('i', $bike_id);
        $success = $stmt->execute();
        $stmt->close();

        return $success;
    }

    /**
     * Get bike details by ID
     * @param int $bike_id
     * @return Bike|null
     */
    public static function getBikeById($mysqli, $bike_id)
    {
        $query = "SELECT * FROM bikes WHERE id = ?";
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param('i', $bike_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            $bike = new Bike($mysqli);
            self::populateBikeFromRow($bike, $row);
            return $bike;
        }

        return null;
    }

    /**
     * Populate bike object from database row
     * @param Bike $bike
     * @param array $row
     */
    private static function populateBikeFromRow($bike, $row)
    {
        $bike->id = $row['id'];
        $bike->user_id = $row['user_id'];
        $bike->tag_id = $row['tag_id'];
        $bike->created_at = $row['created_at'];
        $bike->last_used_at = $row['last_used_at'];
        $bike->status = $row['status'];
        $bike->location = $row['location'];
        $bike->hourly_rate = $row['hourly_rate'];
        $bike->reserved_until = $row['reserved_until'];
    }
}
?>