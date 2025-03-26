<?php
header('Content-Type: application/json');
require 'config.php'; // Your database connection file

$bike_id = isset($_GET['bike_id']) ? intval($_GET['bike_id']) : 0;
$last_update = isset($_GET['last_update']) ? $_GET['last_update'] : null;

try {
    // Select the latest GPS data for the given bike from the gps_data table
    $stmt = $conn->prepare("
        SELECT lat, lng, UNIX_TIMESTAMP(timestamp) as ts 
        FROM gps_data 
        WHERE bike_id = ? 
        ORDER BY timestamp DESC 
        LIMIT 1
    ");

    $stmt->bind_param('i', $bike_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();

    if (!$data) {
        echo json_encode(['error' => 'No GPS data found for this bike']);
        exit;
    }

    // Only return data if it is newer than the client's last update timestamp
    if ($last_update && $data['ts'] <= $last_update) {
        http_response_code(304); // Not Modified
        exit;
    }

    echo json_encode([
        'lat' => (float)$data['lat'],
        'lng' => (float)$data['lng'],
        'timestamp' => $data['ts']
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
