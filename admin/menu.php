<?php
ob_start();
require_once './auth.php';
include '../config/constants.php';

$admin_id = $_SESSION['admin_id'];
$sql = "SELECT * FROM tbl_admin WHERE id = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
  $_SESSION['error'] = "Query not prepared!!";
  die("Prepare failed: " . $conn->error);
}

$stmt->bind_param('i', $admin_id);
$stmt->execute();
$res = $stmt->get_result();
$admin = $res->fetch_assoc();
$stmt->close();

if (!$admin) {
  $_SESSION['error'] = "Admin not found";
  header("Location: login.php");
  exit();
}

// Set default avatar if not provided
$admin_avatar = !empty($admin['avatar']) ? $admin['avatar'] : 'https://ui-avatars.com/api/?name='.urlencode($admin['full_name']).'&background=4F46E5&color=fff';
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Restaurant Admin</title>
  <link rel="stylesheet" href="../css/admin.css">
  <link rel="icon" href="https://media.istockphoto.com/id/2203688325/video/chef-in-cook-hat-and-baker-cook-fork-spoon-and-leaves-graphic-animation-colored-transparent.avif?s=640x640&k=20&c=SsywiUO3TYpYcoz1gez4GV3FpcnU3ibH9pbDD8wgBvI=" type="image/x-icon">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<style>


</style>

<body>
  <div class="header">
    <div class="logo-div">
      <img src="https://media.istockphoto.com/id/2203688325/video/chef-in-cook-hat-and-baker-cook-fork-spoon-and-leaves-graphic-animation-colored-transparent.avif?s=640x640&k=20&c=SsywiUO3TYpYcoz1gez4GV3FpcnU3ibH9pbDD8wgBvI=" alt="logo">
    </div>
    
    <ul class="menu" style="text-wrap: nowrap;">
      <li><a href="dashboard.php"><i class="fas fa-home"></i> <span>Dashboard</span></a></li>
      <li><a href="admin.php"><i class="fas fa-user-shield"></i><span> Admins</span></a></li>
      <li><a href="category.php"><i class="fas fa-list"></i><span> Categories</span></a></li>
      <li><a href="food.php"><i class="fas fa-utensils"></i> <span>Food</span></a></li>
      <li><a href="order.php"><i class="fas fa-shopping-cart"></i> <span>Orders</span></a></li>
    </ul>
    
    <div class="admin-div">
      <div class="admin-info" id="adminInfo">
        <p class="admin-name"><?php echo htmlspecialchars($admin['full_name']) ?></p>
        <img src="<?php echo htmlspecialchars($admin_avatar) ?>" alt="Admin Avatar" class="admin-avatar">
      </div>
      
      <div class="profile-dropdown" id="profileDropdown">
        <div class="profile-header">
          <img src="<?php echo htmlspecialchars($admin_avatar) ?>" alt="Admin Avatar" class="avatar">
          <div class="profile-info">
            <h3><?php echo htmlspecialchars($admin['full_name']) ?></h3>
            <p>@ <?php echo htmlspecialchars($admin['username']) ?></p>
          </div>
        </div>
        
        <div class="profile-body">
          <a href="profile.php" class="profile-item">
            <i class="fas fa-user"></i>
            <span>My Profile</span>
          </a>
          
          <a href="settings.php" class="profile-item">
            <i class="fas fa-cog"></i>
            <span>Account Settings</span>
          </a>
          
          <div class="profile-divider"></div>
          
          <a href="logout.php" class="profile-item logout-btn">
            <i class="fas fa-sign-out-alt"></i>
            <span>Logout</span>
          </a>
        </div>
      </div>
    </div>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const adminInfo = document.getElementById('adminInfo');
      const dropdown = document.getElementById('profileDropdown');
      
      // Toggle dropdown when clicking admin info
      adminInfo.addEventListener('click', function(e) {
        e.stopPropagation();
        dropdown.classList.toggle('active');
      });
      
      // Close dropdown when clicking outside
      document.addEventListener('click', function() {
        dropdown.classList.remove('active');
      });
      
      // Prevent dropdown from closing when clicking inside it
      dropdown.addEventListener('click', function(e) {
        e.stopPropagation();
      });
    });
  </script>
</body>
</html>