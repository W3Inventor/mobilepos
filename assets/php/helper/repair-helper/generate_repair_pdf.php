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
$query = "SELECT r.ir_id AS repair_id, r.imei, r.brand, r.model, r.reason, r.images, r.estimate_price, r.status,
                 c.nic, c.full_name, c.mobile_number, c.email, c.address
          FROM in_house_repair r
          JOIN customers c ON r.customer_id = c.customer_id
          WHERE r.ir_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $repair_id);
$stmt->execute();
$repairData = $stmt->get_result()->fetch_assoc();
$stmt->close();
if (!$repairData) {
    die("No repair found for the provided Repair ID.");
}

$repair_number = 'Rep' . str_pad($repairData['repair_id'], 4, '0', STR_PAD_LEFT);
$invoice_date  = date('Y-m-d');

$settings     = getSettings();
$company_name = $settings['company_name'];
$address1     = $settings['address_line1'];
$address2     = $settings['address_line2'];
$city         = $settings['city'];
$brand_color  = $settings['brand_color'] ?? '#007bff';
$website      = $settings['website'];
$contact_phone= $settings['mobile'];
$logo_path    = $settings['logo_path'];
if ($brand_color && strpos($brand_color, '#') !== 0) {
    $brand_color = "#$brand_color";
}
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

$html = '<style>
    body { font-family: sans-serif; color: #333; font-size: 12px; }
    .header { text-align: center; margin-bottom: 20px; }
    .header h1 { margin: 10px 0; color: '.$brand_color.'; }
    .details, .footer { margin: 20px 0; }
    .details p { margin: 4px 0; }
    .section-title { font-size: 14px; font-weight: bold; margin-top: 20px; color: '.$brand_color.'; }
    table { width: 100%; border-collapse: collapse; margin-top: 10px; }
    table, th, td { border: 1px solid #ccc; }
    th, td { padding: 8px; text-align: left; }
    th { background-color: #f0f0f0; }
    .images img { width: 100px; height: auto; margin-right: 5px; border: 1px solid #ccc; }
</style>';

$html .= '<div class="header">';
if (!empty($logo_src)) {
    $html .= '<img src="$logo_src" alt="Logo" style="height: 80px;"><br>';
}
$html .= '<h1>Repair Receipt</h1>';
$html .= '<p>' . htmlspecialchars($company_name);
if (!empty($address1)) $html .= ', ' . htmlspecialchars($address1);
if (!empty($address2)) $html .= ', ' . htmlspecialchars($address2);
if (!empty($city))    $html .= ', ' . htmlspecialchars($city);
$html .= '</p>';
$html .= '<p>Phone: ' . htmlspecialchars($contact_phone);
if (!empty($website)) {
    $html .= ' | Website: ' . htmlspecialchars($website);
}
$html .= '</p></div>';

$html .= '<div class="details">
    <p><strong>Repair No:</strong> '.$repair_number.'</p>
    <p><strong>Date:</strong> '.$invoice_date.'</p>
    <p><strong>Status:</strong> '.htmlspecialchars($repairData['status']).'</p>
</div>';

$html .= '<div class="details">
    <p class="section-title">Customer Details</p>
    <p><strong>Name:</strong> '.htmlspecialchars($repairData['full_name']).'</p>';
if (!empty($repairData['nic']))        $html .= '<p><strong>NIC:</strong> '.htmlspecialchars($repairData['nic']).'</p>';
if (!empty($repairData['mobile_number'])) $html .= '<p><strong>Phone:</strong> '.htmlspecialchars($repairData['mobile_number']).'</p>';
if (!empty($repairData['email']))      $html .= '<p><strong>Email:</strong> '.htmlspecialchars($repairData['email']).'</p>';
if (!empty($repairData['address']))    $html .= '<p><strong>Address:</strong> '.htmlspecialchars($repairData['address']).'</p>';
$html .= '</div>';

$html .= '<div class="details">
    <p class="section-title">Device & Issue Details</p>
    <table>
        <tr><th>Detail</th><th>Information</th></tr>
        <tr><td>Brand</td><td>'.htmlspecialchars($repairData['brand']).'</td></tr>
        <tr><td>Model</td><td>'.htmlspecialchars($repairData['model']).'</td></tr>
        <tr><td>IMEI</td><td>'.htmlspecialchars($repairData['imei']).'</td></tr>
        <tr><td>Problem</td><td>'.htmlspecialchars($repairData['reason']).'</td></tr>
        <tr><td>Estimate</td><td>LKR '.number_format((float)$repairData['estimate_price'], 2).'</td></tr>
    </table>
</div>';

if (!empty($repairData['images'])) {
    $imagePaths = array_filter(array_map('trim', explode(',', $repairData['images'])));
    if (!empty($imagePaths)) {
        $html .= '<div class="details">
        <p class="section-title">Device Images</p>
        <div class="images">';
        foreach ($imagePaths as $imgPath) {
            $img_file = __DIR__ . '/../../../../' . $imgPath;
            if (file_exists($img_file)) {
                $imgData = file_get_contents($img_file);
                $imgBase64 = base64_encode($imgData);
                $imgMime   = mime_content_type($img_file);
                $imgSrc    = "data:$imgMime;base64,$imgBase64";
                $html .= '<img src="'.$imgSrc.'" alt="Image">';
            }
        }
        $html .= '</div></div>';
    }
}

$html .= '<div class="footer">
    <p>Thank you for choosing '.htmlspecialchars($company_name).'. We will contact you once the repair is complete.</p>
</div>';

try {
    $mpdf = new Mpdf();
    $mpdf->SetTitle("Repair $repair_number");
    $mpdf->WriteHTML($html);
    $mpdf->Output("Repair_$repair_number.pdf", 'I');
} catch (\Mpdf\MpdfException $e) {
    echo "Error generating PDF: " . $e->getMessage();
}

$conn->close();
