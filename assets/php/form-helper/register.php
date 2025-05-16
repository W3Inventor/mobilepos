<?php
include '../../../config/dbconnect.php'; // Path to your dbconnect file

session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullName = $_POST['full_name'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $phone = $_POST['phone'];
    $role_id = 2; // Example role_id

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO users (full_name, username, email, password, phone, role_id) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssi", $fullName, $username, $email, $hashedPassword, $phone, $role_id);

    if ($stmt->execute()) {
        echo "New record created successfully";
        header("Location: ../auth-login.php"); // Redirect to login page
        exit;
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
} else {
    echo "Invalid request method.";
}
?>
