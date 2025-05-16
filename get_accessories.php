<?php
include 'config/dbconnect.php'; // Include your database connection

header('Content-Type: application/json');

// Query to fetch accessories along with serial numbers
$query = "SELECT a.accessory_id AS barcode, a.accessory_name, a.brand, a.color, a.other, 
                 p.buying AS buying_price, p.selling AS selling_price, a.quantity,
                 GROUP_CONCAT(sn.serial_number) AS serial_numbers
          FROM accessories a
          JOIN accessories_price p ON a.accessory_id = p.accessory_id
          LEFT JOIN serial_numbers sn ON a.accessory_id = sn.accessory_id
          GROUP BY a.accessory_id
          ORDER BY a.accessory_id";

$result = $conn->query($query);

if ($result->num_rows > 0) {
    $accessories = [];
    while ($row = $result->fetch_assoc()) {
        // Split serial numbers into an array if they exist
        $row['serial_numbers'] = $row['serial_numbers'] ? explode(',', $row['serial_numbers']) : [];
        $accessories[] = $row;
    }
    echo json_encode($accessories);
} else {
    echo json_encode([]);
}

$conn->close();
?>
