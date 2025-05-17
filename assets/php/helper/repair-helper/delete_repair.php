<?php
include '../../../../config/dbconnect.php';

header('Content-Type: application/json');

$repair_id = $_POST['repair_id'] ?? '';
$password = $_POST['admin_password'] ?? '';

if (empty($repair_id) || empty($password)) {
    echo json_encode(['error' => 'Missing required fields.']);
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

if (!$authenticated) {
    echo json_encode(['error' => 'Invalid admin password.']);
    exit;
}

// Start deletion
$conn->begin_transaction();
try {
    $stmt = $conn->prepare("DELETE FROM repair_status_history WHERE repair_id = ?");
    $stmt->bind_param("i", $repair_id);
    $stmt->execute();
    $stmt->close();

    $stmt = $conn->prepare("DELETE FROM in_house_repair WHERE ir_id = ?");
    $stmt->bind_param("i", $repair_id);
    $stmt->execute();
    $stmt->close();

    $conn->commit();
    echo json_encode(['success' => 'Repair record deleted successfully.']);
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['error' => 'Deletion failed.']);
}
$conn->close();
?>
