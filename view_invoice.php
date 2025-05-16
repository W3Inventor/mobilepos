<?php
include 'config/dbconnect.php';

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // Fetch the invoice data based on the token
    $stmt = $conn->prepare("
        SELECT invoices.invoice_number, invoices.invoice_date, invoices.total_amount, invoices.payment_method_charge, 
               customers.full_name, customers.email, customers.mobile_number, customers.address 
        FROM invoices 
        JOIN sales ON invoices.sale_id = sales.sale_id 
        JOIN customers ON sales.customer_id = customers.customer_id 
        WHERE invoices.token = ?");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $invoice = $result->fetch_assoc();
    } else {
        echo "Invalid or expired link.";
        exit;
    }
    $stmt->close();
} else {
    echo "No token provided.";
    exit;
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice</title>
    <style>
        /* Basic reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
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

        .invoice-header h1 {
            font-size: 24px;
            color: #4CAF50;
        }

        .invoice-details,
        .customer-details,
        .invoice-summary {
            margin-bottom: 20px;
        }

        .invoice-details h2,
        .customer-details h2,
        .invoice-summary h2 {
            font-size: 18px;
            color: #4CAF50;
            margin-bottom: 10px;
        }

        .invoice-info, .customer-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
        }

        .invoice-info span,
        .customer-info span {
            font-weight: bold;
        }

        /* Responsive Table */
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

        .item-table th {
            background-color: #f0f0f0;
        }

        .total-row {
            font-weight: bold;
            color: #4CAF50;
        }

        /* Mobile Responsiveness */
        @media (max-width: 600px) {
            .invoice-info, .customer-info {
                flex-direction: column;
            }
            .invoice-info span, .customer-info span {
                margin-bottom: 5px;
            }
            .invoice-header h1 {
                font-size: 20px;
            }
            .invoice-container {
                padding: 15px;
            }
        }
    </style>
</head>
<body>

<div class="invoice-container">
    <div class="invoice-header">
        <h1>Invoice</h1>
        <p><strong>Invoice No:</strong> <?php echo htmlspecialchars($invoice['invoice_number']); ?></p>
    </div>

    <div class="invoice-details">
        <h2>Invoice Details</h2>
        <div class="invoice-info">
            <span>Invoice Date:</span>
            <span><?php echo htmlspecialchars($invoice['invoice_date']); ?></span>
        </div>
        <div class="invoice-info">
            <span>Total Amount:</span>
            <span>LKR <?php echo number_format($invoice['total_amount'], 2); ?></span>
        </div>
        <div class="invoice-info">
            <span>Payment Method Charge:</span>
            <span>LKR <?php echo number_format($invoice['payment_method_charge'], 2); ?></span>
        </div>
    </div>

    <div class="customer-details">
        <h2>Customer Details</h2>
        <div class="customer-info">
            <span>Customer Name:</span>
            <span><?php echo htmlspecialchars($invoice['full_name']); ?></span>
        </div>
        <div class="customer-info">
            <span>Email:</span>
            <span><?php echo htmlspecialchars($invoice['email']); ?></span>
        </div>
        <div class="customer-info">
            <span>Mobile Number:</span>
            <span><?php echo htmlspecialchars($invoice['mobile_number']); ?></span>
        </div>
        <div class="customer-info">
            <span>Address:</span>
            <span><?php echo htmlspecialchars($invoice['address']); ?></span>
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
            <!-- This is where item details would go. Replace with dynamic data as needed. -->
            <!-- Example Row -->
            <tr>
                <td>Sample Item</td>
                <td>1</td>
                <td>LKR 1000.00</td>
                <td>LKR 1000.00</td>
            </tr>
            <tr class="total-row">
                <td colspan="3">Grand Total</td>
                <td>LKR <?php echo number_format($invoice['total_amount'], 2); ?></td>
            </tr>
        </table>
    </div>
</div>

</body>
</html>
