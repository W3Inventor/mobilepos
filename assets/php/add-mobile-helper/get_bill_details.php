<?php
// Include your database connection file
include '../../../config/dbconnect.php';

// Check if the request is coming through AJAX and if the bill number parameter is set
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['billno'])) {
    $billno = $_GET['billno'];

    // Query to fetch the bill details from the bill table based on the bill number
    $query = "SELECT b.billid, b.billno, b.date, b.bill_amount, s.supplier_id, s.company_name, s.supplier_name, s.mobile_number, s.address 
              FROM bill b 
              LEFT JOIN supplier s ON b.supplier_id = s.supplier_id 
              WHERE b.billno = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $billno);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if a bill was found
    if ($result->num_rows > 0) {
        $bill = $result->fetch_assoc();

        // Return the bill and supplier details as JSON
        echo json_encode([
            'bill' => [
                'billid' => $bill['billid'],
                'billno' => $bill['billno'],
                'date' => $bill['date'],
                'bill_amount' => $bill['bill_amount'],
            ],
            'supplier' => [
                'supplier_id' => $bill['supplier_id'],
                'company_name' => $bill['company_name'],
                'supplier_name' => $bill['supplier_name'],
                'mobile_number' => $bill['mobile_number'],
                'address' => $bill['address'],
            ]
        ]);
    } 
}
?>
