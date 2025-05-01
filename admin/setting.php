<?php
include './menu.php';
// Verify admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}
?>

<div style="margin-top: 80px; padding: 2rem;">
    <h1>Account Settings</h1>
    <!-- Settings content would go here -->
</div>

<?php include 'footer.php'; ?>