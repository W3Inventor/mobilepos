<?php
include '../../../../config/dbconnect.php';

// Set the relative path for database storage and absolute path for server storage
$uploadDir = 'uploads/';
$uploadPath = '../../../../uploads/'; // Server path for uploads

if (!is_dir($uploadPath)) {
    mkdir($uploadPath, 0755, true); // Create the directory if it doesn't exist
}

$response = ['success' => false, 'error' => ''];

// Fetch current settings to avoid adding a new row
$sql = "SELECT * FROM settings LIMIT 1";
$result = $conn->query($sql);

if (!$result) {
    $response['error'] = 'Database query failed: ' . $conn->error;
    echo json_encode($response);
    exit;
}

$currentSettings = $result->fetch_assoc();

$sql = $currentSettings ? 
    "UPDATE settings SET favicon_path = ?, logo_path = ?, company_name = ?, address_line1 = ?, address_line2 = ?, city = ?, mobile = ?, landline = ?, website = ?, brand_color = ? WHERE id = ?" : 
    "INSERT INTO settings (favicon_path, logo_path, company_name, address_line1, address_line2, city, mobile, landline, website, brand_color) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

// Handle favicon upload
$faviconPath = $currentSettings['favicon_path'] ?? null;
if (isset($_FILES['favicon']) && $_FILES['favicon']['error'] === UPLOAD_ERR_OK) {
    $faviconFilename = 'favicon_' . time() . '.' . pathinfo($_FILES['favicon']['name'], PATHINFO_EXTENSION);
    $faviconPath = $uploadDir . $faviconFilename; // Relative path for database
    if (!move_uploaded_file($_FILES['favicon']['tmp_name'], $uploadPath . $faviconFilename)) {
        $response['error'] = 'Failed to upload favicon.';
        echo json_encode($response);
        exit;
    }
}

// Handle logo upload
$logoPath = $currentSettings['logo_path'] ?? null;
if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
    $logoFilename = 'logo_' . time() . '.' . pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION);
    $logoPath = $uploadDir . $logoFilename; // Relative path for database
    if (!move_uploaded_file($_FILES['logo']['tmp_name'], $uploadPath . $logoFilename)) {
        $response['error'] = 'Failed to upload logo.';
        echo json_encode($response);
        exit;
    }
}

// Bind parameters and execute the query
$stmt = $conn->prepare($sql);
if ($currentSettings) {
    $stmt->bind_param(
        "ssssssssssi",
        $faviconPath,
        $logoPath,
        $_POST['company_name'],
        $_POST['address_line1'],
        $_POST['address_line2'],
        $_POST['city'],
        $_POST['mobile'],
        $_POST['landline'],
        $_POST['website'],
        $_POST['brand_color'],
        $currentSettings['id']
    );
} else {
    $stmt->bind_param(
        "ssssssssss",
        $faviconPath,
        $logoPath,
        $_POST['company_name'],
        $_POST['address_line1'],
        $_POST['address_line2'],
        $_POST['city'],
        $_POST['mobile'],
        $_POST['landline'],
        $_POST['website'],
        $_POST['brand_color']
    );
}

if ($stmt->execute()) {
    $response['success'] = true;
    $response['favicon_path'] = $faviconPath;
    $response['logo_path'] = $logoPath;
} else {
    $response['error'] = 'Failed to execute query: ' . $stmt->error;
}

$stmt->close();
$conn->close();

echo json_encode($response);
?>
