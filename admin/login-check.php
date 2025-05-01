<?php
// Start session FIRST
session_start();
include '../config/constants.php';

// Redirect if accessed directly
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['submit'])) {
    $_SESSION['login_error'] = "Invalid access method";
    header("Location: login.php");
    exit();
}

// Sanitize inputs
$username = trim($_POST['username'] ?? '');
$raw_password = $_POST['password'] ?? ''; // Renamed to clarify it's not hashed yet

// Validate inputs
if (empty($username) || empty($raw_password)) {
    $_SESSION['login_error'] = "Both username and password are required";
    header("Location: login.php");
    exit();
}

// Verify credentials
$sql = "SELECT * FROM tbl_admin WHERE username = ? LIMIT 1";
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    error_log("Database error: " . $conn->error);
    $_SESSION['login_error'] = "System error. Please try again later.";
    header("Location: login.php");
    exit();
}

$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
    
if ($result->num_rows === 1) {
    $admin = $result->fetch_assoc();
    
    // This is the CORRECT way to verify passwords
    // password_verify() compares the raw password with the stored hash
    if (password_verify($raw_password, $admin['password'])) {
        // Successful login - set session variables
        session_regenerate_id(true);
        
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_username'] = $admin['username'];
        $_SESSION['admin_full_name'] = $admin['full_name'];
        $_SESSION['last_login'] = time();
        $_SESSION['login_attempts'] = 0; // Reset attempts
        
        // Set success message (sanitized)
        $_SESSION['login_success'] = "Welcome back, " . htmlspecialchars($admin['full_name']);
        header("Location: dashboard.php");
        exit();
    }
}

// If we get here, login failed
$_SESSION['login_error'] = "Invalid username or password";
error_log("Failed login attempt for username: " . $username);

// Implement basic brute force protection
$_SESSION['login_attempts'] = ($_SESSION['login_attempts'] ?? 0) + 1;

// Progressive delay for failed attempts
sleep(min($_SESSION['login_attempts'], 5)); // Max 5 seconds delay

header("Location: login.php");
exit();
?>