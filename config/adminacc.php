<?php
session_start();

// Check if the user is logged in and if they are an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 1) {
    // If not an admin or not logged in, check if it's a cashier
    if (isset($_SESSION['role_id']) && $_SESSION['role_id'] == 2) {
        // If a cashier, show a 403 Forbidden error
        header("HTTP/1.1 403 Forbidden");
        exit("403 Forbidden: You do not have access to this resource.");
    } else {
        // If not logged in at all, redirect to the login page
        header("Location: auth-login.php");
        exit;
    }
}

?>