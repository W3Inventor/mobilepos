<?php
// File: verify_admin_password.php
include '../../../config/dbconnect.php';
header('Content-Type: application/json');

$password = $_POST['admin_password'] ?? '';

if (empty($password)) {
    echo json_encode(['status' => 'error', 'message' => 'Password required.']);
    exit;
}

$stmt = $conn->prepare("SELECT password FROM users WHERE role_id = 1");
$stmt->execute();
$result = $stmt->get_result();
$authenticated = false;

while ($row = $result->fetch_assoc()) {
    if (password_verify($password, $row['password'])) {
        $authenticated = true;
        break;
    }
}
$stmt->close();

if ($authenticated) {
    echo json_encode(['status' => 'success', 'message' => 'Authenticated.']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid admin password.']);
}
$conn->close();
