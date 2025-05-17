<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json; charset=UTF-8');
require '../../../../config/dbconnect.php';

$q = isset($_GET['q']) ? trim($_GET['q']) : '';
if ($q === '') {
    echo json_encode(['results' => []]);
    exit;
}

// Tokenize search query
$keywords = preg_split('/\s+/', strtolower($q));
$conditions = [];
$types = '';
$params = [];

foreach ($keywords as $word) {
    if ($word === '') continue;
    $word = "%$word%";
    $conditions[] = "(LOWER(a.accessory_name) LIKE ? OR LOWER(a.brand) LIKE ? OR a.accessory_id LIKE ? OR LOWER(sn.serial_number) LIKE ?)";
    $types .= 'ssss';
    $params[] = $word;
    $params[] = $word;
    $params[] = $word;
    $params[] = $word;
}

$whereClause = implode(' AND ', $conditions);

// Query including accessories WITH and WITHOUT serial numbers
$sql = "
    SELECT a.accessory_id, a.brand, a.accessory_name, sn.serial_number
    FROM accessories a
    LEFT JOIN serial_numbers sn ON a.accessory_id = sn.accessory_id AND sn.status = 'In Stock'
    WHERE $whereClause
    ORDER BY a.accessory_name
    LIMIT 30
";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    http_response_code(500);
    echo json_encode(['results' => []]);
    exit;
}

$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

$seen = []; // prevent duplicate accessories
$items = [];

while ($row = $result->fetch_assoc()) {
    $accessoryId = $row['accessory_id'];
    $serial = $row['serial_number'];

    $uniqueKey = $accessoryId . '||' . ($serial ?? '');

    // Use both cases: one with serial, one fallback
    if ($serial) {
        $items[] = [
            'id' => $uniqueKey,
            'text' => "{$row['brand']} {$row['accessory_name']} (SN: {$serial})"
        ];
    } elseif (!isset($seen[$accessoryId])) {
        $items[] = [
            'id' => $accessoryId . '||',
            'text' => "{$row['brand']} {$row['accessory_name']} (No Serial)"
        ];
        $seen[$accessoryId] = true;
    }
}

echo json_encode(['results' => $items]);
