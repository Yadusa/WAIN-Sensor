<?php
// config.php
// Check if session is already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// IMPORTANT: Update these to match your cPanel database credentials
$servername = "localhost"; // Usually just localhost in cPanel
$username = "root";    // Your cPanel database username
$password = ""; // Your database password
$dbname = "wain_sensor_db"; // Your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset to utf8
$conn->set_charset("utf8");

// Session validation function
function validateSession() {
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['username']) || !isset($_SESSION['role'])) {
        header("Location: admin_login.php");
        exit();
    }
}

// Check if user is admin (for admin-only pages)
function validateAdminSession() {
    validateSession();
    if ($_SESSION['role'] !== 'admin') {
        header("Location: admin_dashboard.php");
        exit();
    }
}
?>