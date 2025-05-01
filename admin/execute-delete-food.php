<?php
session_start();
include '../config.php'; // Make sure this contains your database connection

// Database connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get and validate ID
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    $_SESSION['error'] = "Invalid food ID";
    header("Location: food.php");
    exit();
}

// First check if food item exists
$check_sql = "SELECT id FROM tbl_food WHERE id = ?";
$check_stmt = $conn->prepare($check_sql);
if ($check_stmt === false) {
    $_SESSION['error'] = "Database error: " . $conn->error;
    header("Location: food.php");
    exit();
}

$check_stmt->bind_param("i", $id);
$check_stmt->execute();
$check_stmt->store_result();

if ($check_stmt->num_rows == 0) {
    $_SESSION['error'] = "Food item not found";
    header("Location: food.php");
    exit();
}
$check_stmt->close();

// Perform deletion using prepared statement
$sql = "DELETE FROM tbl_food WHERE id = ?";
$stmt = $conn->prepare($sql);
if ($stmt === false) {
    $_SESSION['error'] = "Database error: " . $conn->error;
    header("Location: food.php");
    exit();
}

$stmt->bind_param("i", $id);
$success = $stmt->execute();
$stmt->close();

if ($success) {
    $_SESSION['success'] = "Food item deleted successfully";
} else {
    $_SESSION['error'] = "Error deleting food item: " . $conn->error;
}

$conn->close();
header("Location: food.php");
exit();
?>