<?php
session_start();

// Check if the user is logged in and their role
if (!isset($_SESSION['user_id']) || ($_SESSION['role_id'] != 1 && $_SESSION['role_id'] != 2)) {
    // User is not logged in or not an admin or cashier
    header("Location: auth-login.php?message=access_denied");
    exit;
}

?>