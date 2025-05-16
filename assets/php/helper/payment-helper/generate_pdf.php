<?php
require_once __DIR__ . '/../../../../vendor/autoload.php';
use Mpdf\Mpdf;

include '../../../../config/dbconnect.php';
include '../setting-helper/settings_helper.php'; // Include the settings helper

if (!isset($_GET['sale_id'])) {
    die("Sale ID is required.");
}

$sale_id = intval($_GET['sale_id']);

// Fetch sale and customer data
$query = "
    SELECT s.sale_id, s.sale_date, s.bill_amount, s.total_discount, s.payable_amount, s.payment_method, 
           c.full_name, c.mobile_number, c.email, c.address, i.invoice_number
    FROM sales s
    JOIN customers c ON s.customer_id = c.customer_id
    JOIN invoices i ON s.sale_id = i.sale_id
    WHERE s.sale_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $sale_id);
$stmt->execute();
$saleData = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$saleData) {
    die("No sale found for the provided Sale ID.");
}

// Get current date and time separately
$invoice_date = date('Y-m-d');
$invoice_time = date('H:i:s');

// Get settings data
$settings = getSettings();
$company_name = $settings['company_name'];
$address_line1 = $settings['address_line1'];
$address_line2 = $settings['address_line2'];
$city = $settings['city'];
$brand_color = $settings['brand_color'];
$website = $settings['website'];
$contact_phone = $settings['mobile'];
$logo_path = $settings['logo_path'];

// Ensure brand color starts with '#'
$brand_color = strpos($brand_color, '#') === 0 ? $brand_color : '#' . $brand_color;

// Read and encode the logo image
$logo_full_path = __DIR__ . '/../../../../' . $logo_path; // Adjust the path as needed
if (file_exists($logo_full_path)) {
    $logo_image = file_get_contents($logo_full_path);
    $logo_base64 = base64_encode($logo_image);
    $logo_mime_type = mime_content_type($logo_full_path);
    $logo_src = 'data:' . $logo_mime_type . ';base64,' . $logo_base64;
} else {
    $logo_src = ''; // Logo not found; handle accordingly
}

// Initialize items array
$items = [];

// Fetch accessories data
$query = "
    SELECT item_name, warranty, discount, after_discount_price, 1 AS quantity 
    FROM sale_accessories 
    WHERE sale_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $sale_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $items[] = $row;
}
$stmt->close();

// Fetch mobiles data
$query = "
    SELECT item_name, warranty, discount, after_discount_price, 1 AS quantity 
    FROM sale_mobile 
    WHERE sale_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $sale_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $items[] = $row;
}
$stmt->close();

try {
    $mpdf = new Mpdf([
        'mode' => 'utf-8',
        'format' => 'A5',
        'margin_top' => 10,
        'margin_bottom' => 10,
        'margin_left' => 10,
        'margin_right' => 10,
    ]);
    $mpdf->SetTitle('Invoice ' . $saleData['invoice_number']);

    // Build company details dynamically
    $company_details = '
        <div class="company-details">';
    if (!empty($logo_src)) {
        // Display only the logo with increased size
        $company_details .= '<img src="' . $logo_src . '" alt="' . htmlspecialchars($company_name) . ' Logo" style="max-width: 180px; height: auto; margin-bottom: 0px;">';
    } else {
        // If no logo, display the company name
        $company_details .= '<h1>' . htmlspecialchars($company_name) . '</h1>';
    }

    // Build address parts
    $address_parts = [];
    if (!empty($address_line1)) {
        $address_parts[] = htmlspecialchars($address_line1);
    }
    if (!empty($address_line2)) {
        $address_parts[] = htmlspecialchars($address_line2);
    }
    if (!empty($city)) {
        $address_parts[] = htmlspecialchars($city);
    }

    if (!empty($address_parts)) {
        $company_details .= '<p>' . implode(', ', $address_parts) . '</p>';
    }

    // Build contact parts
    $contact_parts = [];
    if (!empty($contact_phone)) {
        $contact_parts[] = 'Phone: ' . htmlspecialchars($contact_phone);
    }
    if (!empty($website)) {
        $contact_parts[] = 'Website: ' . htmlspecialchars($website);
    }

    if (!empty($contact_parts)) {
        $company_details .= '<p>' . implode(' | ', $contact_parts) . '</p>';
    }

    $company_details .= '</div>';

    // Build customer details
    $customer_details = '
        <div class="invoice-to-section">
            <h2 class="section-title">Invoice To</h2><br>
            <div class="invoice-to">
                <p><strong>Name:</strong> ' . htmlspecialchars($saleData['full_name']) . '</p>';
    if (!empty($saleData['email'])) {
        $customer_details .= '<p><strong>Email:</strong> ' . htmlspecialchars($saleData['email']) . '</p>';
    }
    if (!empty($saleData['address'])) {
        $customer_details .= '<p><strong>Address:</strong> ' . htmlspecialchars($saleData['address']) . '</p>';
    }
    $customer_details .= '</div></div>';

    // Build invoice details
    $invoice_details = '
        <div class="invoice-details">
            <p><strong>Invoice Number:</strong> ' . htmlspecialchars($saleData['invoice_number']) . '</p>
            <p><strong>Date:</strong> ' . htmlspecialchars($invoice_date) . '</p>
            <p><strong>Time:</strong> ' . htmlspecialchars($invoice_time) . '</p>
        </div>';

    // Build items table
    $items_html = '';
    foreach ($items as $item) {
        $item_total = $item['after_discount_price'];
        $unit_price = $item['after_discount_price'] + $item['discount'];
        $items_html .= '
            <tr>
                <td>' . htmlspecialchars($item['item_name']) . '</td>
                <td>' . htmlspecialchars($item['quantity']) . '</td>
                <td>LKR ' . number_format($unit_price, 2) . '</td>
                <td>LKR ' . number_format($item['discount'], 2) . '</td>
                <td>LKR ' . number_format($item_total, 2) . '</td>
            </tr>';
    }

    // Terms and Conditions
    $terms_and_conditions = '
        <div class="terms">
            <h2 class="section-title">Terms and Conditions</h2>
            <p>Please read our terms and conditions carefully. By making a purchase, you agree to our policies.</p>
            <ul>
                <li>All sales are final. No refunds or exchanges.</li>
                <li>Warranty claims must be accompanied by the original invoice.</li>
                <li>Goods sold remain the property of the company until fully paid.</li>
            </ul>
        </div>';

    // Prepare the HTML content with modern and attractive design
    $html = '
    <!DOCTYPE html>
    <html>
    <head>
        <style>
            body {
                font-family: Arial, sans-serif;
                color: #333333;
                margin: 0;
                padding: 0;
                font-size: 10px;
            }
            .invoice-container {
                width: 100%;
                margin: 0 auto;
                padding: 5px;
                background-color: #ffffff;
                border-radius: 8px;
            }
            .company-details {
                text-align: center;
                margin-bottom: 10px;
            }
            .company-details img {
                max-width: 80px; /* Increased logo size */
                height: auto;
                margin-bottom: 5px;
            }
            .company-details h1 {
                color: ' . htmlspecialchars($brand_color) . ';
                margin-bottom: 3px;
                font-size: 16px;
                text-transform: uppercase;
            }
            .company-details p {
                margin: 2px;
                font-size: 8px;
                color: #777777;
            }
            .invoice-header {
                width: 100%;
                margin-bottom: 10px;
            }
            .invoice-header table {
                width: 100%;
                border-collapse: collapse;
            }
            .invoice-header td {
                vertical-align: top;
                padding: 5px;
            }
            .invoice-to-section {
                margin-bottom: 10px; /* Ensure margin-bottom is applied */
            }
            .invoice-to {
                font-size: 9px;
            }
            .invoice-details {
                font-size: 9px;
                text-align: right;
            }
            .invoice-details p, .invoice-to p {
                margin: 2px 0;
            }
            .section-title {
                color: ' . htmlspecialchars($brand_color) . ';
                font-size: 12px;
                margin-bottom: 5px;
                border-bottom: 1px solid ' . htmlspecialchars($brand_color) . ';
                padding-bottom: 2px;
                text-transform: uppercase;
            }
            .invoice-items {
                margin-bottom: 10px;
            }
            .invoice-items table {
                width: 100%;
                border-collapse: collapse;
                font-size: 9px;
            }
            .invoice-items th, .invoice-items td {
                border: 1px solid #dddddd;
                padding: 5px;
                text-align: left;
            }
            .invoice-items th {
                background-color: ' . htmlspecialchars($brand_color) . ';
                color: #ffffff;
            }
            .totals table {
                width: 100%;
                border-collapse: collapse;
                font-size: 9px;
            }
            .totals td {
                padding: 5px;
                text-align: right;
            }
            .totals .total-row td {
                font-weight: bold;
                border-top: 1px solid #dddddd;
            }
            .terms {
                font-size: 8px;
                margin-top: 10px;
            }
            .terms ul {
                list-style-type: disc;
                margin: 5px 0 0 15px;
                padding: 0;
            }
            .footer {
                text-align: center;
                font-size: 8px;
                color: #888888;
                padding-top: 5px;
                margin-top: 10px;
                border-top: 1px solid #eaeaea;
            }
            .footer p {
                margin: 2px;
            }
            a {
                color: ' . htmlspecialchars($brand_color) . ';
                text-decoration: none;
            }
        </style>
    </head>
    <body>
        <div class="invoice-container">
            ' . $company_details . '
            <div class="invoice-header">
                <table>
                    <tr>
                        <td style="width: 50%;">
                            <!-- Wrapped customer details in a div to ensure margins are applied -->
                            ' . $customer_details . '
                        </td>
                        <td style="width: 50%; text-align: right;">
                            ' . $invoice_details . '
                        </td>
                    </tr>
                </table>
            </div>
            <div class="invoice-items">
                <h2 class="section-title">Invoice Details</h2>
                <table>
                    <thead>
                        <tr>
                            <th style="width:40%;">Item</th>
                            <th style="width:15%;">Qty</th>
                            <th style="width:15%;">Unit Price</th>
                            <th style="width:15%;">Discount</th>
                            <th style="width:15%;">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        ' . $items_html . '
                    </tbody>
                </table>
            </div>
            <div class="totals">
                <table>
                    <tr class="total-row">
                        <td colspan="4" style="text-align:right;">Subtotal:</td>
                        <td>LKR ' . number_format($saleData['bill_amount'], 2) . '</td>
                    </tr>
                    <tr class="total-row">
                        <td colspan="4" style="text-align:right;">Total Discount:</td>
                        <td>LKR ' . number_format($saleData['total_discount'], 2) . '</td>
                    </tr>
                    <tr class="total-row">
                        <td colspan="4" style="text-align:right;">Payable Amount:</td>
                        <td>LKR ' . number_format($saleData['payable_amount'], 2) . '</td>
                    </tr>
                </table>
            </div>
            ' . $terms_and_conditions . '
            <!-- Footer -->
            <div class="footer">
                <p>Thank you for your business!</p>
                <p>Software Solution by W3Inventor (Pvt.) Ltd.</p>
            </div>
        </div>
    </body>
    </html>';

    // Write the HTML content to the PDF
    $mpdf->WriteHTML($html);
    $mpdf->Output('invoice_' . $saleData['invoice_number'] . '.pdf', 'I'); // 'I' to view inline in the browser
} catch (\Mpdf\MpdfException $e) {
    echo 'Error generating PDF: ' . $e->getMessage();
}

$conn->close();
?>
