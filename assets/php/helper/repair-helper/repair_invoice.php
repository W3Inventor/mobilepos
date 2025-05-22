<?php 
require_once __DIR__.'/../../../../vendor/autoload.php';
use Mpdf\Mpdf;
include '../../../../config/dbconnect.php';
include '../setting-helper/settings_helper.php';

$repair_id = $_GET['repair_id'] ?? 0;
if (!$repair_id) die("Repair ID is required.");
$repair_id = intval($repair_id);

// 1. Fetch repair details, customer info, and related invoice data
$stmt = $conn->prepare("
    SELECT r.ir_id, r.imei, r.brand, r.model, r.reason, r.status,
           c.full_name, c.mobile_number, c.email, c.address
    FROM in_house_repair r 
    JOIN customers c ON r.customer_id = c.customer_id
    WHERE r.ir_id = ?");
$stmt->bind_param("i", $repair_id);
$stmt->execute();
$repairData = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Fetch the corresponding repair invoice record and items
$stmt = $conn->prepare("SELECT invoice_id, total_amount FROM repair_invoices WHERE repair_id = ?");
$stmt->bind_param("i", $repair_id);
$stmt->execute();
$invoiceData = $stmt->get_result()->fetch_assoc();
$stmt->close();
$invoiceId    = $invoiceData['invoice_id'];
$totalAmount  = $invoiceData['total_amount'] ?? 0.00;
$itemsResult  = $conn->query("SELECT item_name, warranty, quantity, price 
                              FROM repair_invoice_items WHERE invoice_id = $invoiceId");
$items = $itemsResult->fetch_all(MYSQLI_ASSOC);

// 2. Prepare company settings and invoice identifiers
$settings    = getSettings();
$companyName = $settings['company_name'];
$brandColor  = $settings['brand_color'] ?? '#000';
$brandColor  = '#' . ltrim($brandColor, '#');  // ensure it starts with '#'
$logoPath    = $settings['logo_path'];
$repairNumber = 'REP' . str_pad($repairData['ir_id'], 4, '0', STR_PAD_LEFT);  // e.g. REP0005:contentReference[oaicite:0]{index=0}

// Convert logo to base64 (for embedding in PDF)
$logoSrc = "";
if (!empty($logoPath) && file_exists(__DIR__."/../../../../$logoPath")) {
    $imgData = file_get_contents(__DIR__."/../../../../$logoPath");
    $logoSrc = "data:" . mime_content_type(__DIR__."/../../../../$logoPath") . ";base64," . base64_encode($imgData);
}

// 3. Build the HTML content for the PDF (mirroring POS invoice layout)
$html = "<!DOCTYPE html><html><head><style>
    body { font-family: Roboto, sans-serif; color: #333; background: #fff; font-size: 12px; margin: 0; }
    .invoice-box { max-width: 750px; margin: auto; padding: 10px; }
    .company-header { text-align: center; margin-bottom: 10px; }
    .company-header h1 { color: $brandColor; font-size: 18px; margin-bottom: 5px; }
    .details { margin-bottom: 12px; }
    .section-title { font-weight: bold; font-size: 14px; color: $brandColor; border-bottom: 1px solid $brandColor; padding-bottom: 2px; margin-bottom: 8px !important; }
    .row { display: flex; justify-content: space-between; gap: 20px; }
    .column { flex: 1; }
    .info-pair { margin: 4px 0; font-size: 12px; }
    .info-pair span { font-weight: bold; }
    table { width: 100%; border-collapse: collapse; margin-top: 5px; }
    th, td { border: 1px solid #ccc; padding: 6px; text-align: left; }
    th { background: #f0f0f0; font-size: 12px; }
    .total-row td { font-weight: bold; }
    .footer { text-align: center; font-size: 10px; color: #777; margin-top: 10px; border-top: 1px solid #eaeaea; padding-top: 6px; }
</style></head><body><div class='invoice-box'>";

// Company Header
$html .= "<div class='company-header'>";
if ($logoSrc) {
    $html .= "<img src='$logoSrc' alt='Logo' height='80'><br>";
}
$html .= "<h1>$companyName</h1></div>";

// Side-by-side: Repair Info and Customer Details
$html .= "<table style='width:100%; margin-bottom: 12px;' border='0' cellspacing='0' cellpadding='0'>
<tr>
  <td style='width:50%; vertical-align:top; padding-right: 10px; border: none;'>
    <div class='section-title' style='border-bottom: none !important;'>Repair Info</div><br>
    <p class='info-pair'><span>Repair No:</span> $repairNumber</p>
    <p class='info-pair'><span>Date:</span> " . date('Y-m-d') . "</p>
    <p class='info-pair'><span>Status:</span> " . htmlspecialchars($repairData['status']) . "</p>
  </td>
  <td style='width:50%; vertical-align:top; text-align:right; border: none; padding-left: 10px;'>
    <div class='section-title' style='text-align:right; border-bottom: none !important;'>Customer Details</div>
    <br>
    <p class='info-pair' style='text-align:right;'><span>Name:</span> " . htmlspecialchars($repairData['full_name']) . "</p>";
if ($repairData['mobile_number']) {
    $html .= "<p class='info-pair' style='text-align:right;'><span>Phone:</span> " . htmlspecialchars($repairData['mobile_number']) . "</p>";
}
if ($repairData['email']) {
    $html .= "<p class='info-pair' style='text-align:right;'><span>Email:</span> " . htmlspecialchars($repairData['email']) . "</p>";
}
if ($repairData['address']) {
    $html .= "<p class='info-pair' style='text-align:right;'><span>Address:</span> " . htmlspecialchars($repairData['address']) . "</p>";
}
$html .= "</td>
</tr>
</table>";




// Device & Issue Details
$html .= "<div class='details'>
    <p class='section-title'>Device & Issue Details</p>
    <table>
      <tr><th>Detail</th><th>Information</th></tr>
      <tr><td>Brand</td><td>" . htmlspecialchars($repairData['brand']) . "</td></tr>
      <tr><td>Model</td><td>" . htmlspecialchars($repairData['model']) . "</td></tr>
      <tr><td>IMEI</td><td>" . htmlspecialchars($repairData['imei']) . "</td></tr>
      <tr><td>Problem</td><td>" . htmlspecialchars($repairData['reason']) . "</td></tr>
    </table>
</div>";

// Parts/Services Breakdown
$html .= "<div class='details'>
    <p class='section-title'>Parts & Services</p>
    <table>
      <tr><th>Item</th><th>Qty</th><th>Unit Price</th><th>Total</th></tr>";
foreach ($items as $it) {
    $lineTotal = $it['price'] * $it['quantity'];
    $html .= "<tr>
        <td>" . htmlspecialchars($it['item_name']) . "</td>
        <td>" . (int)$it['quantity'] . "</td>
        <td>LKR " . number_format($it['price'], 2) . "</td>
        <td>LKR " . number_format($lineTotal, 2) . "</td>
      </tr>";
}
$html .= "</table>
</div>";

// Totals section
$html .= "<div class='details'><table>";
$html .= "<tr class='total-row'><td colspan='3' style='text-align:right;'>Subtotal:</td>
            <td>LKR " . number_format($repairData['estimate_price'] ?? $totalAmount, 2) . "</td></tr>";
if (!empty($invoiceData['discount_amount'])) {
    $html .= "<tr class='total-row'><td colspan='3' style='text-align:right;'>Discount:</td>
              <td>-LKR " . number_format($invoiceData['discount_amount'], 2) . "</td></tr>";
}
$html .= "<tr class='total-row'><td colspan='3' style='text-align:right;'>Total Payable:</td>
            <td>LKR " . number_format($totalAmount, 2) . "</td></tr>";
$html .= "</table></div>";

// Footer/Thank you
$html .= "<div class='details' style='text-align:center; font-size:12px; color:#777; margin-top:30px; border-top:1px solid #eaeaea; padding-top:10px;'>
            <p>Thank you for your business!</p>
            <p>If you have any questions, feel free to contact us.</p>
          </div>";

$html .= "</div></body></html>";

// 4. Output PDF in the browser
$mpdf = new Mpdf(['format' => 'A4']);
$mpdf->SetTitle("Repair Invoice $repairNumber");
$mpdf->WriteHTML($html);
$mpdf->Output("Repair_$repairNumber.pdf", 'I');
