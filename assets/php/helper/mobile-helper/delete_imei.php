<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include '../../../../config/dbconnect.php';

$imei = $_POST['imei'] ?? '';

if (empty($imei)) {
    echo json_encode(['success' => false, 'error' => 'IMEI not provided.']);
    exit;
}

// Start a transaction to ensure all operations are successful before committing
$conn->begin_transaction();

try {
    // Step 1: Get the vid_1 and vid_2 associated with the IMEI
    $query = "SELECT vid_1, vid_2 FROM mobile WHERE imei = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('s', $imei);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        throw new Exception('IMEI not found.');
    }

    $row = $result->fetch_assoc();
    $vid_1 = $row['vid_1'];
    $vid_2 = $row['vid_2'];

    // Step 2: Delete the IMEI from the mobile table
    $query = "DELETE FROM mobile WHERE imei = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('s', $imei);
    if (!$stmt->execute()) {
        throw new Exception($stmt->error);
    }

    // Step 3: Decrement quantity1 in variation_1
    $query = "UPDATE variation_1 SET quantity1 = quantity1 - 1 WHERE vid_1 = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $vid_1);
    if (!$stmt->execute()) {
        throw new Exception($stmt->error);
    }

    // Step 4: Decrement quantity2 in variation_2
    $query = "UPDATE variation_2 SET quantity2 = quantity2 - 1 WHERE vid_2 = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $vid_2);
    if (!$stmt->execute()) {
        throw new Exception($stmt->error);
    }

    // Commit the transaction if all operations succeed
    $conn->commit();

    echo json_encode(['success' => true]);

} catch (Exception $e) {
    // Rollback the transaction if any operation fails
    $conn->rollback();
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

// Close the statement and connection
$stmt->close();
$conn->close();
