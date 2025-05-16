<?php
include '../../../config/dbconnect.php'; // Include your database connection

header('Content-Type: application/json');

// Query to fetch accessories along with their price and serial numbers, allowing multiple rows per accessory
$query = "SELECT a.accessory_id, a.brand, a.accessory_name, p.buying AS buying_price, 
                 p.selling AS selling_price, p.quantity, p.id AS price_id,
                 GROUP_CONCAT(sn.serial_number) AS serial_numbers
          FROM accessories a
          JOIN accessories_price p ON a.accessory_id = p.accessory_id
          LEFT JOIN serial_numbers sn ON p.id = sn.accbill_id
          GROUP BY p.id
          ORDER BY a.accessory_id, p.id";

$result = $conn->query($query);

$accessories = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Split serial numbers into an array if they exist
        $row['serial_numbers'] = $row['serial_numbers'] ? explode(',', $row['serial_numbers']) : [];
        $accessories[] = $row;

        // Log the fetched data for debugging
        error_log('Fetched row data: ' . json_encode($row));
    }
} else {
    error_log('No accessories found in the database.');
}
$conn->close();

echo json_encode($accessories);
?>
