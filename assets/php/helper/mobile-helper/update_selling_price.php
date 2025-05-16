<?php
include '../../../../config/dbconnect.php';

// Retrieve the IMEIs and new selling price from the POST request
$imeis = $_POST['imeis'];
$newSellingPrice = $_POST['newSellingPrice'];

if (!empty($imeis) && $newSellingPrice) {
    // Convert IMEIs to a format suitable for SQL IN clause
    $imeiList = "'" . implode("','", $imeis) . "'";

    // Prepare the SQL query to update the selling price in the variation_2 table
    $query = "
        UPDATE variation_2 v2
        JOIN mobile mb ON v2.vid_2 = mb.vid_2
        SET v2.selling = ?
        WHERE mb.imei IN ($imeiList)
    ";

    $stmt = $conn->prepare($query);
    $stmt->bind_param('d', $newSellingPrice); // 'd' for double/decimal

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => $stmt->error]);
    }

    $stmt->close();
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid input']);
}

$conn->close();
?>
