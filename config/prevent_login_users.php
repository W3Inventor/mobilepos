<?php
session_start();

// Check if user is already logged in
if (isset($_SESSION['user_id'])) {
    $role_id = $_SESSION['role_id'];

    // Redirect based on role
    if ($role_id == 1) {  // Assuming 1 is for admin
        header("Location: index.php?message=already_logged_in");
        exit;
    } elseif ($role_id == 2) {  // Assuming 2 is for cashier
        header("Location: index2.php?message=already_logged_in");
        exit;
    }
}
?>