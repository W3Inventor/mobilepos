<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json; charset=UTF-8');
require '../../../../config/dbconnect.php';

// Get and sanitize the query
$q = isset($_GET['q']) ? trim($_GET['q']) : '';
if ($q === '') {
    echo json_encode(['results' => []]);
    exit;
}

// Split query into keywords
$keywords = preg_split('/\s+/', strtolower($q));
$conditions = [];
$types = '';
$params = [];

foreach ($keywords as $word) {
    $word = trim($word);
    if ($word === '') continue;
    $conditions[] = "(LOWER(accessory_name) LIKE ? OR LOWER(brand) LIKE ? OR accessory_id LIKE ?)";
    $types .= 'sss';
    $param = "%$word%";
    $params[] = $param;
    $params[] = $param;
    $params[] = $param;
}

if (empty($conditions)) {
    echo json_encode(['results' => []]);
    exit;
}

// Build SQL
$sql = "SELECT accessory_id, accessory_name, brand 
        FROM accessories 
        WHERE " . implode(' AND ', $conditions) . " 
        ORDER BY accessory_name 
        LIMIT 25";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    error_log("MySQL prepare failed: " . $conn->error);
    http_response_code(500);
    echo json_encode(['results' => []]);
    exit;
}

// Bind dynamic parameters
$stmt->bind_param($types, ...$params);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($accessory_id, $accessory_name, $brand);

// Format results
$items = [];
while ($stmt->fetch()) {
    $items[] = [
        'id' => (string)$accessory_id,
        'text' => "$brand $accessory_name (ID: $accessory_id)"
    ];
}

$stmt->close();
$conn->close();
echo json_encode(['results' => $items]);
