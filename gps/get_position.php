<?php
header('Content-Type: application/json');
require 'config.php'; // Your database connection file

$bike_id = isset($_GET['bike_id']) ? intval($_GET['bike_id']) : 0;
$last_update = isset($_GET['last_update']) ? $_GET['last_update'] : null;

try {
    $stmt = $conn->prepare("
        SELECT latitude, longitude, UNIX_TIMESTAMP(last_update) as ts 
        FROM bikes 
        WHERE id = ? 
        ORDER BY last_update DESC 
        LIMIT 1
    ");
    
    $stmt->execute([$bike_id]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$data) {
        echo json_encode(['error' => 'Bike not found']);
        exit;
    }

    // Check if data is newer than client's last update
    if ($last_update && $data['ts'] <= $last_update) {
        http_response_code(304); // Not Modified
        exit;
    }

    echo json_encode([
        'lat' => (float)$data['latitude'],
        'lng' => (float)$data['longitude'],
        'timestamp' => $data['ts']
    ]);

} catch(PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>