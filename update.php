<?php
header('Content-Type: application/json');

// Get parameters
$device_id = $_GET['device_id'];
$uid = $_GET['uid'];
$lat = floatval($_GET['lat']);
$lng = floatval($_GET['lng']);

try {
    require_once 'config.php';
    // Update bike location
    $stmt = $conn->prepare("
        UPDATE bikes 
        SET latitude = :lat, longitude = :lng 
        WHERE card_id = (
            SELECT id FROM nfc_tags WHERE uid = :uid
        )
    ");
    $stmt->execute([
        ':lat' => $lat,
        ':lng' => $lng,
        ':uid' => $uid
    ]);

    echo json_encode(['status' => 'success', 'message' => 'Location updated']);
    
} catch(PDOException $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>