<?php
// Include the database connection
include '../../../../config/dbconnect.php';  // Adjust this path according to your directory structure

// Check if $conn is defined
if (!isset($conn)) {
    die("Database connection failed: conn is not defined.");
}

// Get the search query from the AJAX request and trim it
$search_query = trim($conn->real_escape_string($_POST['search_query']));

// Initialize arrays to hold search results and avoid duplicates
$searchResults = [];
$serialNumbersShown = [];  // Avoid duplicate serial numbers

// Check if the search query is numeric (could be an IMEI, serial number, or accessory ID)
$isNumericSearch = preg_match('/^\d+$/', $search_query);

/* ---- ACCESSORY SEARCH ---- */
$queryAccessories = "
    SELECT 
        a.accessory_id AS id,
        CONCAT(a.brand, ' ', a.accessory_name, ' ', a.color, ' ', a.other) AS name,
        s.serial_number,
        s.status AS serial_status,
        a.quantity
    FROM accessories a
    LEFT JOIN serial_numbers s ON a.accessory_id = s.accessory_id
    WHERE (
        a.accessory_name LIKE '%$search_query%' 
        OR a.brand LIKE '%$search_query%' 
        OR a.accessory_id LIKE '%$search_query%' 
        OR s.serial_number LIKE '%$search_query%'
    )
    AND (s.status IS NULL OR s.status != 'Out of Stock')
";

$resultAccessories = $conn->query($queryAccessories);
$processedAccessories = [];  // Track accessories to avoid duplicates

if ($resultAccessories->num_rows > 0) {
    while ($row = $resultAccessories->fetch_assoc()) {
        $accessoryId = $row['id'];
        $serialNumber = $row['serial_number'];

        if (!isset($processedAccessories[$accessoryId])) {
            $processedAccessories[$accessoryId] = [
                'name' => $row['name'],
                'serial_count' => 0,
                'quantity' => $row['quantity']
            ];
        }

        // Add serial numbers if not already shown
        if (!empty($serialNumber) && !in_array($serialNumber, $serialNumbersShown)) {
            $searchResults[] = [
                'id' => $accessoryId,
                'name' => $row['name'] . ' (' . $serialNumber . ')',
                'type' => 'accessory'
            ];
            $serialNumbersShown[] = $serialNumber;
            $processedAccessories[$accessoryId]['serial_count']++;
        }
    }
}

// Add accessories without serial numbers
foreach ($processedAccessories as $id => $info) {
    if ($info['quantity'] > $info['serial_count']) {
        $searchResults[] = [
            'id' => $id,
            'name' => $info['name'] . ' (without serial)',
            'type' => 'accessory'
        ];
    }
}

/* ---- SERIAL NUMBER SEARCH ---- */
if ($isNumericSearch) {
    $querySerialNumber = "
        SELECT 
            s.serial_number AS id,
            CONCAT(a.brand, ' ', a.accessory_name, ' ', a.color, ' ', a.other, ' (', s.serial_number, ')') AS name,
            'serial' AS type
        FROM serial_numbers s
        LEFT JOIN accessories a ON s.accessory_id = a.accessory_id
        WHERE s.serial_number LIKE '%$search_query%'
        AND s.status != 'Out of Stock'
    ";

    $resultSerialNumber = $conn->query($querySerialNumber);

    if ($resultSerialNumber->num_rows > 0) {
        while ($row = $resultSerialNumber->fetch_assoc()) {
            if (!in_array($row['id'], $serialNumbersShown)) {
                $searchResults[] = [
                    'id' => $row['id'],
                    'name' => $row['name'],
                    'type' => 'serial'
                ];
                $serialNumbersShown[] = $row['id'];
            }
        }
    }
}

/* ---- IMEI SEARCH ---- */
$queryMobile = "
    SELECT 
        m.imei AS id,
        CONCAT(v1.brand, ' ', v1.model, ' ', v1.ram, ' ', v1.storage, ' ', v1.colour, ' (', m.imei, ')') AS name,
        'mobile' AS type
    FROM mobile m
    LEFT JOIN variation_1 v1 ON m.vid_1 = v1.vid_1
    WHERE TRIM(m.imei) LIKE '%$search_query%' AND m.status = 'In Stock'
";

$resultMobile = $conn->query($queryMobile);

if ($resultMobile->num_rows > 0) {
    while ($row = $resultMobile->fetch_assoc()) {
        $searchResults[] = [
            'id' => $row['id'],
            'name' => $row['name'],
            'type' => 'mobile'
        ];
    }
}

/* ---- VARIATION_1 SEARCH ---- */
$queryVariation1 = "
    SELECT 
        v1.vid_1 AS id,
        CONCAT(v1.model, ' ', v1.ram, ' ', v1.storage, ' ', v1.colour, ' (', m.imei, ')') AS name,
        'mobile' AS type,
        m.imei AS imei
    FROM variation_1 v1
    LEFT JOIN mobile m ON v1.vid_1 = m.vid_1
    WHERE (v1.model LIKE '%$search_query%' OR v1.brand LIKE '%$search_query%')
    AND m.status = 'In Stock'
";

$resultVariation1 = $conn->query($queryVariation1);

if ($resultVariation1->num_rows > 0) {
    while ($row = $resultVariation1->fetch_assoc()) {
        $imei = $row['imei'];
        if ($imei) {
            $searchResults[] = [
                'id' => $imei,
                'name' => $row['name'],
                'type' => 'mobile'
            ];
        }
    }
}

/* ---- VARIATION_2 SEARCH ---- */
$queryVariation2 = "
    SELECT 
        vid_2 AS id,
        '' AS name,
        'variation_2' AS type
    FROM variation_2
    WHERE vid_2 LIKE '%$search_query%' OR billid LIKE '%$search_query%'
";

$resultVariation2 = $conn->query($queryVariation2);

if ($resultVariation2->num_rows > 0) {
    while ($row = $resultVariation2->fetch_assoc()) {
        $searchResults[] = [
            'id' => $row['id'],
            'name' => 'Variation 2 (ID: ' . $row['id'] . ')',
            'type' => 'variation_2'
        ];
    }
}

/* ---- DISPLAY SEARCH RESULTS ---- */
if (!empty($searchResults)) {
    foreach ($searchResults as $result) {
        echo "<li class='list-group-item search-result-item' data-id='" . htmlspecialchars($result['id']) . "' data-type='" . htmlspecialchars($result['type']) . "'>"
            . htmlspecialchars($result['name']) . "</li>";
    }
} else {
    echo "<li class='list-group-item'>No results found</li>";
}

// Close the database connection
$conn->close();
?>
