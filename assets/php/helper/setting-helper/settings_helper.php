<?php
include '../../../../config/dbconnect.php';

function getSettings() {
    global $conn;

    // Fetch settings data from the database
    $query = "SELECT * FROM settings LIMIT 1";
    $result = $conn->query($query);

    if ($result && $result->num_rows > 0) {
        return $result->fetch_assoc();
    }

    // Return empty array if settings are not found
    return [];
}
?>
