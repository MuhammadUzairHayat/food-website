<?php
session_start();
include '../config.php'; // Make sure this contains your database connection

// Verify admin is logged in and has permission
// if (!isset($_SESSION['admin_logged_in'])) {
//     $_SESSION['error'] = "Unauthorized access";
//     header("Location: login.php");
//     exit();
// }

// Database connection
$conn = new mysqli('localhost', 'root', '', 'mock-food');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get and validate ID
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    $_SESSION['error'] = "Invalid category ID";
    header("Location: categories.php");
    exit();
}

// First check if category exists
$check_sql = "SELECT id FROM tbl_category WHERE id = ?";
$check_stmt = $conn->prepare($check_sql);
if ($check_stmt === false) {
    $_SESSION['error'] = "Database error: " . $conn->error;
    header("Location: categories.php");
    exit();
}

$check_stmt->bind_param("i", $id);
$check_stmt->execute();
$check_stmt->store_result();

if ($check_stmt->num_rows == 0) {
    $_SESSION['error'] = "Category not found";
    header("Location: categories.php");
    exit();
}
$check_stmt->close();

// Perform deletion using prepared statement
$sql = "DELETE FROM tbl_category WHERE id = ?";
$stmt = $conn->prepare($sql);
if ($stmt === false) {
    $_SESSION['error'] = "Database error: " . $conn->error;
    header("Location: categories.php");
    exit();
}

$stmt->bind_param("i", $id);
$success = $stmt->execute();
$stmt->close();

if ($success) {
    $_SESSION['success'] = "Category deleted successfully";
    
    // You may want to also delete or update related menu items
    // $update_items = "UPDATE tbl_food SET category_id = NULL WHERE category_id = ?";
    // Or delete them:
    // $delete_items = "DELETE FROM tbl_food WHERE category_id = ?";
} else {
    $_SESSION['error'] = "Error deleting category: " . $conn->error;
}

$conn->close();
header("Location: category.php");
exit();
?>