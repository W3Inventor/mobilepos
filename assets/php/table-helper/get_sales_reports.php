<?php
include 'config/adminacc.php';
?>

<?php
include '../../../config/dbconnect.php';

header('Content-Type: application/json');

$sql = "
    SELECT i.invoice_id AS id, i.invoice_number AS invoice_no, i.invoice_date AS date, i.total_amount AS amount,
           c.full_name AS customer_name, s.payment_method
    FROM invoices i
    INNER JOIN sales s ON s.sale_id = i.sale_id
    LEFT JOIN customers c ON s.customer_id = c.customer_id

    UNION

    SELECT ri.invoice_id AS id, ri.invoice_id AS raw_invoice_id, ri.invoice_date AS date, ri.total_amount AS amount,
           cu.full_name AS customer_name, 'Repair' AS payment_method
    FROM repair_invoices ri
    INNER JOIN in_house_repair r ON ri.repair_id = r.ir_id
    LEFT JOIN customers cu ON r.customer_id = cu.customer_id

    ORDER BY date DESC
";

$result = $conn->query($sql);

$invoices = [];

while ($row = $result->fetch_assoc()) {
    $isRepair = isset($row['payment_method']) && $row['payment_method'] === 'Repair';
    $invoiceNo = $isRepair
        ? 'Rep' . str_pad($row['id'], 4, '0', STR_PAD_LEFT)
        : $row['invoice_no'];

    $invoices[] = [
        'invoice_id' => $row['id'],
        'invoice_no' => $invoiceNo,
        'customer' => $row['customer_name'] ?? 'N/A',
        'amount' => number_format((float)$row['amount'], 2),
        'date' => date('Y-m-d, h:iA', strtotime($row['date'])),
        'method' => $row['payment_method']
    ];
}

echo json_encode(["data" => $invoices]);

$conn->close();
