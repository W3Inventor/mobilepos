**assets/php/helper/repair-helper/generate_repair_pdf.php** (new)
<?php
require_once __DIR__ . '/../../../../vendor/autoload.php';
use Mpdf\Mpdf;
include '../../../../config/dbconnect.php';
include '../setting-helper/settings_helper.php';

if (!isset($_GET['repair_id'])) {
    die("Repair ID is required.");
}
$repair_id = intval($_GET['repair_id']);

// Fetch repair and customer data
$query = "SELECT r.repair_id, r.imei, r.brand, r.model, r.reason, r.images, r.estimate_price, r.status,
                 c.nic, c.full_name, c.mobile_number, c.email, c.address 
          FROM in_house_repair r 
          JOIN customers c ON r.customer_id = c.customer_id
          WHERE r.repair_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $repair_id);
$stmt->execute();
$repairData = $stmt->get_result()->fetch_assoc();
$stmt->close();
if (!$repairData) {
    die("No repair found for the provided Repair ID.");
}

// Prepare data for the PDF
$repair_number = 'Rep' . str_pad($repairData['repair_id'], 4, '0', STR_PAD_LEFT);
$invoice_date  = date('Y-m-d');
$invoice_time  = date('H:i:s');

// Get company settings for header
$settings     = getSettings();
$company_name = $settings['company_name'];
$address1     = $settings['address_line1'];
$address2     = $settings['address_line2'];
$city         = $settings['city'];
$brand_color  = $settings['brand_color'];
$website      = $settings['website'];
$contact_phone= $settings['mobile'];
$logo_path    = $settings['logo_path'];
// Ensure brand_color has a '#' for CSS
if ($brand_color && strpos($brand_color, '#') !== 0) {
    $brand_color = "#$brand_color";
}
// Prepare logo for embedding (convert to base64 data URI if file exists)
$logo_src = "";
if (!empty($logo_path)) {
    $logo_file = __DIR__ . '/../../../../' . $logo_path;
    if (file_exists($logo_file)) {
        $imageData = file_get_contents($logo_file);
        $base64    = base64_encode($imageData);
        $mime      = mime_content_type($logo_file);
        $logo_src  = "data:$mime;base64,$base64";
    }
}

// Build HTML content for the PDF
$html = "<html><head><style>
    body { font-family: sans-serif; color: #333; }
    .company-header { text-align: center; margin-bottom: 20px; }
    .company-header img { max-width: 150px; }
    .company-header h1 { color: $brand_color; margin: 5px 0; }
    .company-header p { margin: 2px 0; font-size: 12px; color: #555; }
    .section-title { font-size: 16px; font-weight: bold; color: $brand_color; margin-top: 20px; }
    .details p { margin: 4px 0; }
    .details strong { color: #000; }
    table.items { width: 100%; border-collapse: collapse; margin-top: 15px; }
    table.items td, table.items th { border: 1px solid #ddd; padding: 8px; }
    table.items th { background: #f0f0f0; text-align: left; }
</style></head><body>";

// Company logo and info
$html .= "<div class='company-header'>";
if (!empty($logo_src)) {
    $html .= "<img src='$logo_src' alt='Logo'><br>";
}
$html .= "<h1>" . htmlspecialchars($company_name) . "</h1>";
$html .= "<p>" . htmlspecialchars($address1);
if (!empty($address2)) $html .= ", " . htmlspecialchars($address2);
if (!empty($city))    $html .= ", " . htmlspecialchars($city);
$html .= "</p>";
$html .= "<p>Phone: " . htmlspecialchars($contact_phone);
if (!empty($website)) {
    $html .= " | Website: " . htmlspecialchars($website);
}
$html .= "</p></div>";

// Repair invoice title and metadata
$html .= "<h2 class='section-title'>Repair Receipt</h2>";
$html .= "<div class='details'>";
$html .= "<p><strong>Repair No:</strong> $repair_number</p>";
$html .= "<p><strong>Date:</strong> $invoice_date</p>";
$html .= "<p><strong>Status:</strong> " . htmlspecialchars($repairData['status']) . "</p>";
$html .= "</div>";

// Customer details
$html .= "<div class='details'><p class='section-title'>Customer Details</p>";
$html .= "<p><strong>Name:</strong> " . htmlspecialchars($repairData['full_name']) . "</p>";
if (!empty($repairData['nic'])) {
    $html .= "<p><strong>NIC:</strong> " . htmlspecialchars($repairData['nic']) . "</p>";
}
$html .= "<p><strong>Phone:</strong> " . htmlspecialchars($repairData['mobile_number']) . "</p>";
if (!empty($repairData['email'])) {
    $html .= "<p><strong>Email:</strong> " . htmlspecialchars($repairData['email']) . "</p>";
}
$html .= "<p><strong>Address:</strong> " . htmlspecialchars($repairData['address']) . "</p>";
$html .= "</div>";

// Device/repair details
$html .= "<div class='details'><p class='section-title'>Device & Issue Details</p>";
$html .= "<p><strong>Brand:</strong> " . htmlspecialchars($repairData['brand']) . "</p>";
$html .= "<p><strong>Model:</strong> " . htmlspecialchars($repairData['model']) . "</p>";
$html .= "<p><strong>IMEI:</strong> " . htmlspecialchars($repairData['imei']) . "</p>";
$html .= "<p><strong>Problem:</strong> " . htmlspecialchars($repairData['reason']) . "</p>";
$html .= "<p><strong>Estimated Cost:</strong> LKR " . number_format((float)$repairData['estimate_price'], 2) . "</p>";
$html .= "</div>";

// (If needed, list images or other notes here)

$html .= "<p>Thank you for choosing " . htmlspecialchars($company_name) . ". We will contact you once the repair is completed.</p>";
$html .= "</body></html>";

// Generate PDF from HTML
$mpdf = new Mpdf();
$mpdf->WriteHTML($html);
$filename = "Repair_$repair_number.pdf";
$mpdf->Output($filename, 'I');  // Inline output to browser
?>
