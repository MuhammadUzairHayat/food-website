<?php
include './menu.php';
// Verify admin is logged in
$admin_id = $_SESSION['admin_id'];
function redirectToLogin() {
    header("Location: login.php");
    exit();
}
if (!isset($admin_id)) {
    redirectToLogin();
} else {
   $sql = "SELECT * from tbl_admin where id = ?";
   $stmt = $conn->prepare($sql);

   if(!$stmt) {
    $_SESSION['error'] = "Query not Prepared";
    redirectToLogin();
   }

   $stmt->bind_param('i', $admin_id);
   $stmt->execute();
   $res = $stmt->get_result();
   $admin = $res->fetch_assoc();
   $stmt->close();

   echo $admin['full_name'];
}
?>

<div style="margin-top: 30px; padding: 2rem;">
    <h1>Account Settings</h1>
    <!-- Settings content would go here -->
</div>

<?php include 'footer.php'; ?>