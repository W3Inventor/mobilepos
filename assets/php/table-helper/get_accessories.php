<?php
include '../../../config/dbconnect.php';

header('Content-Type: application/json');

// First, fetch grouped accessories
$query = "
    SELECT 
        a.accessory_id,
        a.brand,
        a.accessory_name,
        SUM(p.quantity) AS total_quantity,
        MIN(p.selling) AS min_price,
        MAX(p.selling) AS max_price,
        GROUP_CONCAT(DISTINCT sn.serial_number) AS serial_numbers
    FROM accessories a
    JOIN accessories_price p ON a.accessory_id = p.accessory_id
    LEFT JOIN serial_numbers sn ON p.id = sn.accbill_id
    GROUP BY a.accessory_id, a.brand, a.accessory_name
    ORDER BY a.accessory_id
";

$result = $conn->query($query);

$accessories = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $serials = $row['serial_numbers'] ? explode(',', $row['serial_numbers']) : [];

        $accessories[] = [
            'accessory_id'    => $row['accessory_id'],
            'brand'           => $row['brand'],
            'accessory_name'  => $row['accessory_name'],
            'quantity'        => (int) $row['total_quantity'],
            'selling_price'   => ($row['min_price'] === $row['max_price']) 
                                 ? $row['min_price'] 
                                 : $row['min_price'] . ' - ' . $row['max_price'],
            'serial_numbers'  => $serials
        ];
    }
}

$conn->close();

echo json_encode($accessories);
