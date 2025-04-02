<?php
header('Content-Type: application/json');
require 'config.php';

$bike_id = intval($_GET['bike_id'] ?? 0);
$last_update = $_GET['last_update'] ?? null;

try {
    $stmt = $conn->prepare("SELECT lat, lng, UNIX_TIMESTAMP(timestamp) as ts FROM gps_data WHERE bike_id = ? ORDER BY timestamp DESC LIMIT 1");
    $stmt->bind_param('i', $bike_id);
    $stmt->execute();
    $data = $stmt->get_result()->fetch_assoc();

    if (!$data) {
        echo json_encode(['error' => 'No GPS data found']);
        exit;
    }

    // Only return new data
    if ($last_update && $data['ts'] <= $last_update) {
        http_response_code(304);
        exit;
    }

    // Notify WebSocket server (new addition)
    $ws_message = json_encode([
        'bikeId' => $bike_id,
        'lat' => $data['lat'],
        'lng' => $data['lng'],
        'timestamp' => $data['ts']
    ]);
    
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => 'http://147.93.30.102:3000/gps-update',
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $ws_message,
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'Authorization: Bearer YOUR_SECRET_KEY'
        ],
        CURLOPT_RETURNTRANSFER => true
    ]);
    curl_exec($ch);
    curl_close($ch);

    echo json_encode([
        'lat' => (float)$data['lat'],
        'lng' => (float)$data['lng'],
        'timestamp' => $data['ts']
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}