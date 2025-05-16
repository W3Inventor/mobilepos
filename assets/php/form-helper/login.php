<?php
// Set session configurations before starting the session
ini_set('session.gc_maxlifetime', 3600); // Session expires after 1 hour of inactivity
session_set_cookie_params(3600); // Session cookie expires after 1 hour

session_start(); // Start the session

// Function to set flash messages
function set_flash_message($type, $message) {
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

// Check session timeout for inactivity
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 3600)) {
    session_unset(); // Unset $_SESSION variable for the run-time
    session_destroy(); // Destroy session data in storage
    header("Location: login.php"); // Redirect to login page
    exit;
}

$_SESSION['last_activity'] = time(); // Update last activity time stamp

include '../../../config/dbconnect.php'; // Include your database connection script

// Handle login form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $login = trim($_POST['login']); // Accepts either username or email
    $password = $_POST['password'];

    // Prepare SQL to check username or email
    $stmt = $conn->prepare("SELECT user_id, username, email, password, role_id FROM users WHERE username = ? OR email = ?");
    $stmt->bind_param("ss", $login, $login);

    if ($stmt->execute()) {
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        // Verify user and password
        if ($user && password_verify($password, $user['password'])) {
            // Set session variables for logged-in user
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role_id'] = $user['role_id'];

            // Redirect user based on role
            switch ($user['role_id']) {
                case 1: // Admin
                    header("Location: ../../../index.php");
                    break;
                case 2: // Cashier
                    header("Location: ../../../index2.php");
                    break;
                default: // General user dashboard
                    header("Location: user_dashboard.php");
                    break;
            }
            exit;
        } else {
            set_flash_message('error', 'Invalid credentials!'); // Set error message
            header("Location: login.php");
            exit;
        }
    } else {
        set_flash_message('error', 'Database error: ' . $stmt->error); // Handle database errors
        header("Location: login.php");
        exit;
    }

    $stmt->close();
    $conn->close();
}

// Display flash messages if set
if (isset($_SESSION['flash'])) {
    $flash = $_SESSION['flash'];
    echo '<div class="alert alert-' . htmlspecialchars($flash['type']) . '">' . htmlspecialchars($flash['message']) . '</div>';
    unset($_SESSION['flash']); // Remove flash message after displaying it
}
?>