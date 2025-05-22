<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'config/dbconnect.php';

if (!isset($_GET['token'])) {
    echo "No token provided.";
    exit;
}

$token = $_GET['token'];

$stmt = $conn->prepare("
    SELECT 
        r.invoice_id,
        r.invoice_date,
        r.total_amount,
        ih.customer_id,
        ih.ir_id,
        ih.imei,
        ih.brand,
        ih.model,
        ih.reason,
        ih.estimate_price,
        c.full_name,
        c.email,
        c.mobile_number,
        c.address
    FROM repair_invoices r
    JOIN in_house_repair ih ON r.repair_id = ih.ir_id
    JOIN customers c ON ih.customer_id = c.customer_id
    WHERE r.token = ?
");


if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}

$stmt->bind_param("s", $token);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Invalid or expired token.";
    exit;
}

$invoice = $result->fetch_assoc();
$repair_id = $invoice['ir_id'];
$invoice_number = 'Repair-' . str_pad($invoice['invoice_id'], 6, '0', STR_PAD_LEFT);
$stmt->close();

// Fetch repair revenue items
$items = [];
$itemStmt = $conn->prepare("SELECT item_name, quantity, price FROM repair_revenue WHERE repair_id = ?");
$itemStmt->bind_param("i", $repair_id);
$itemStmt->execute();
$itemResult = $itemStmt->get_result();

while ($row = $itemResult->fetch_assoc()) {
    $items[] = $row;
}

$itemStmt->close();
$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Repair Invoice</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            display: flex;
            justify-content: center;
            padding: 20px;
        }
        .invoice-container {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            width: 100%;
            padding: 20px;
            color: #333;
        }
        .invoice-header {
            text-align: center;
            border-bottom: 2px solid #4CAF50;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .invoice-header h1 { font-size: 24px; color: #4CAF50; }
        .invoice-details, .customer-details, .invoice-summary {
            margin-bottom: 20px;
        }
        .invoice-details h2, .customer-details h2, .invoice-summary h2 {
            font-size: 18px;
            color: #4CAF50;
            margin-bottom: 10px;
        }
        .invoice-info, .customer-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
        }
        .invoice-info span, .customer-info span {
            font-weight: bold;
        }
        .item-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .item-table th, .item-table td {
            padding: 8px;
            text-align: left;
            border: 1px solid #ddd;
            font-size: 14px;
        }
        .item-table th { background-color: #f0f0f0; }
        .total-row { font-weight: bold; color: #4CAF50; }
        @media (max-width: 600px) {
            .invoice-info, .customer-info {
                flex-direction: column;
            }
            .invoice-info span, .customer-info span {
                margin-bottom: 5px;
            }
            .invoice-header h1 { font-size: 20px; }
            .invoice-container { padding: 15px; }
        }
    </style>
</head>
<body>
<div class="invoice-container">
    <div class="invoice-header">
        <h1>Repair Invoice</h1>
        <p><strong>Invoice No:</strong> <?= htmlspecialchars($invoice['invoice_id']) ?></p>
    </div>
    <div class="invoice-details">
        <h2>Invoice Details</h2>
        <div class="invoice-info">
            <span>Invoice Date:</span>
            <span><?= htmlspecialchars($invoice['invoice_date']) ?></span>
        </div>
        <div class="invoice-info">
            <span>Total Amount:</span>
            <span>LKR <?= number_format($invoice['total_amount'], 2) ?></span>
        </div>
    </div>
    <div class="customer-details">
        <h2>Customer Details</h2>
        <div class="customer-info">
            <span>Name:</span>
            <span><?= htmlspecialchars($invoice['full_name']) ?></span>
        </div>
        <div class="customer-info">
            <span>Email:</span>
            <span><?= htmlspecialchars($invoice['email']) ?></span>
        </div>
        <div class="customer-info">
            <span>Mobile:</span>
            <span><?= htmlspecialchars($invoice['mobile_number']) ?></span>
        </div>
        <div class="customer-info">
            <span>Address:</span>
            <span><?= htmlspecialchars($invoice['address']) ?></span>
        </div>
        <h2>Device Details</h2>
        <div class="customer-info">
            <span>IMEI:</span>
            <span><?= htmlspecialchars($invoice['imei']) ?></span>
        </div>
        <div class="customer-info">
            <span>Brand:</span>
            <span><?= htmlspecialchars($invoice['brand']) ?></span>
        </div>
        <div class="customer-info">
            <span>Model:</span>
            <span><?= htmlspecialchars($invoice['model']) ?></span>
        </div>
        <div class="customer-info">
            <span>Issue Reported:</span>
            <span><?= htmlspecialchars($invoice['reason']) ?></span>
        </div>
        <div class="customer-info">
            <span>Estimate Price:</span>
            <span>LKR <?= number_format($invoice['estimate_price'], 2) ?></span>
        </div>
    </div>
    <div class="invoice-summary">
        <h2>Summary</h2>
        <table class="item-table">
            <tr>
                <th>Description</th>
                <th>Quantity</th>
                <th>Price</th>
                <th>Total</th>
            </tr>
            <?php foreach ($items as $item): ?>
            <tr>
                <td><?= htmlspecialchars($item['item_name']) ?></td>
                <td><?= $item['quantity'] ?></td>
                <td>LKR <?= number_format($item['price'], 2) ?></td>
                <td>LKR <?= number_format($item['price'] * $item['quantity'], 2) ?></td>
            </tr>
            <?php endforeach; ?>
            <tr class="total-row">
                <td colspan="3">Grand Total</td>
                <td>LKR <?= number_format($invoice['total_amount'], 2) ?></td>
            </tr>
        </table>
    </div>
</div>
</body>
</html>
