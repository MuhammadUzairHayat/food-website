<?php
include './menu.php';
session_start();

// Verify admin is logged in and has permission
// if (!isset($_SESSION['admin_logged_in'])) {
//     $_SESSION['error'] = "Unauthorized access";
//     header("Location: login.php");
//     exit();
// }

// Get and validate ID
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    $_SESSION['error'] = "Invalid admin ID";
    header("Location: admin.php");
    exit();
}

// Perform deletion
$sql = "DELETE FROM tbl_admin WHERE id = $id";
$res = mysqli_query($conn, $sql);

if ($res == false) {
    $_SESSION['success'] = "Admin deleted successfully";
} else {
    $_SESSION['error'] = "Error deleting admin: " . $conn->error;
}


header("Location: admin.php");
exit();
?>