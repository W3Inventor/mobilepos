<?php
include '../../../../config/dbconnect.php';

// Function to retrieve request values safely
function get_request_value($key) {
    return isset($_POST[$key]) ? trim($_POST[$key]) : (isset($_GET[$key]) ? trim($_GET[$key]) : null);
}

// Retrieve parameters from the request
$brand = get_request_value('brand');
$model = get_request_value('model');
$ram = get_request_value('ram');
$storage = get_request_value('storage');
$colour = get_request_value('colour');
$trcsl = get_request_value('trcsl');
$condition = get_request_value('condition');  // New/Used condition value
$status = get_request_value('status');  // In Stock/Out of Stock

// Check if all required inputs are provided, otherwise return error
if (!$brand || !$model || !$ram || !$storage || !$colour || !$trcsl || !$condition || !$status) {
    echo json_encode(['error' => 'Invalid input']);
    exit;
}

// Prepare SQL query to fetch IMEIs based on the provided status and variation combination
$query = "
    SELECT 
        b.billid, 
        b.billno, 
        b.date, 
        s.supplier_name, 
        s.company_name, 
        s.address, 
        s.mobile_number, 
        v2.selling AS amount,
        v2.quantity2 AS quantity2,
        mb.imei AS imei_number,
        mb.status
    FROM 
        bill b
    JOIN 
        supplier s ON b.supplier_id = s.supplier_id
    JOIN 
        variation_2 v2 ON b.billid = v2.billid
    JOIN 
        mobile mb ON v2.vid_2 = mb.vid_2
    JOIN 
        variation_1 v1 ON mb.vid_1 = v1.vid_1
    WHERE 
        v1.brand = ? 
        AND v1.model = ? 
        AND v1.ram = ? 
        AND v1.storage = ? 
        AND v1.colour = ? 
        AND v1.trcsl = ? 
        AND mb.`condition` = ?
        AND mb.status = ?
    ORDER BY 
        b.date ASC;
";

// Prepare and execute the SQL query with the provided parameters
$stmt = $conn->prepare($query);
$stmt->bind_param('ssssssss', $brand, $model, $ram, $storage, $colour, $trcsl, $condition, $status);
$stmt->execute();
$result = $stmt->get_result();

// Initialize an array to store the fetched data
$bills = [];

// Process the result set and create the structured response
while ($row = $result->fetch_assoc()) {
    $billKey = $row['billid'] . '-' . $row['amount'];  // Unique key for each bill and amount combination

    // If this bill is not yet in the array, add it
    if (!isset($bills[$billKey])) {
        $bills[$billKey] = [
            'billno' => $row['billno'],
            'date' => $row['date'],
            'supplier_name' => $row['supplier_name'],
            'company_name' => $row['company_name'],
            'address' => $row['address'],
            'mobile_number' => $row['mobile_number'],
            'amount' => $row['amount'],
            'quantity2' => $row['quantity2'],
            'imeis' => []
        ];
    }

    // Add the IMEI number and status to the corresponding bill entry
    $bills[$billKey]['imeis'][] = [
        'imei_number' => $row['imei_number'],
        'status' => $row['status']
    ];
}

// Return the structured response as JSON
echo json_encode(array_values($bills));

// Close the statement and the connection
$stmt->close();
$conn->close();
?>
