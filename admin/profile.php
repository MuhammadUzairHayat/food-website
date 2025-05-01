<?php
require_once './auth.php';
include './menu.php'; 


// Get admin data
$admin_id = $_SESSION['admin_id'];
$sql = "SELECT * FROM tbl_admin WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $admin_id);
$stmt->execute();
$res = $stmt->get_result();
$admin = $res->fetch_assoc();
$stmt->close();

// Handle form submission
$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $full_name = trim($_POST['full_name']);
    $username = trim($_POST['username']);
    $current_password = trim($_POST['current_password']);
    $new_password = trim($_POST['new_password']);
    $confirm_password = trim($_POST['confirm_password']);

    // Validate inputs
    if (empty($full_name)) {
        $errors[] = "Full name is required";
    }

    if (empty($username)) {
        $errors[] = "Username is required";
    }

    // Only validate passwords if any password field is filled
    if (!empty($current_password) || !empty($new_password) || !empty($confirm_password)) {
        if (empty($current_password)) {
            $errors[] = "Current password is required to change password";
        } elseif (!password_verify($current_password, $admin['password'])) {
            $errors[] = "Current password is incorrect";
        }

        if (empty($new_password)) {
            $errors[] = "New password is required";
        } elseif (strlen($new_password) < 8) {
            $errors[] = "New password must be at least 8 characters";
        }

        if ($new_password !== $confirm_password) {
            $errors[] = "New passwords do not match";
        }
    }

    // Handle avatar upload
    $avatar = $admin['avatar'];
    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $file_type = $_FILES['avatar']['type'];
        
        if (in_array($file_type, $allowed_types)) {
            $upload_dir = '../uploads/avatars/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            
            $file_ext = pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION);
            $new_filename = 'avatar_' . $admin_id . '_' . time() . '.' . $file_ext;
            $destination = $upload_dir . $new_filename;
            
            if (move_uploaded_file($_FILES['avatar']['tmp_name'], $destination)) {
                // Delete old avatar if it's not the default
                if ($avatar && !str_starts_with($avatar, 'https://')) {
                    @unlink($avatar);
                }
                $avatar = $destination;
            } else {
                $errors[] = "Failed to upload avatar";
            }
        } else {
            $errors[] = "Only JPG, PNG, and GIF images are allowed";
        }
    }

    // Update database if no errors
    if (empty($errors)) {
        $update_sql = "UPDATE tbl_admin SET full_name = ?, username = ?, avatar = ?";
        $params = [$full_name, $username, $avatar];
        $types = "sss";
        
        // Add password to update if provided
        if (!empty($new_password)) {
            $update_sql .= ", password = ?";
            $params[] = password_hash($new_password, PASSWORD_DEFAULT);
            $types .= "s";
        }
        
        $update_sql .= " WHERE id = ?";
        $params[] = $admin_id;
        $types .= "i";
        
        $stmt = $conn->prepare($update_sql);
        $stmt->bind_param($types, ...$params);
        
        if ($stmt->execute()) {
            $success = true;
            // Refresh admin data
            $sql = "SELECT * FROM tbl_admin WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('i', $admin_id);
            $stmt->execute();
            $res = $stmt->get_result();
            $admin = $res->fetch_assoc();
            $_SESSION['admin_name'] = $admin['full_name'];
            $_SESSION['admin_avatar'] = $admin['avatar'];
        } else {
            $errors[] = "Failed to update profile: " . $conn->error;
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile | Restaurant Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/admin.css">
</head>
<body>
    
    <div class="profile-container">
        <div class="profile-header">
            <h1><i class="fas fa-user-circle"></i> My Profile</h1>
        </div>

        <?php if ($success): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <div>Profile updated successfully!</div>
            </div>
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i>
                <div>
                    <strong>There were errors with your submission:</strong>
                    <ul style="margin-top: 0.5rem; padding-left: 1.5rem;">
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo htmlspecialchars($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        <?php endif; ?>

        <div class="profile-card">
            <form method="POST" enctype="multipart/form-data">
                <div class="avatar-section">
                    <div class="avatar-container">
                        <img src="<?php echo htmlspecialchars($admin['avatar'] ?: 'https://ui-avatars.com/api/?name='.urlencode($admin['full_name']).'&background=4F46E5&color=fff'); ?>" 
                             alt="Profile Picture" 
                             class="avatar-img"
                             id="avatarPreview">
                        <label class="avatar-upload" title="Change Avatar">
                            <i class="fas fa-camera"></i>
                            <input type="file" name="avatar" id="avatarInput" accept="image/*">
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label for="full_name">Full Name</label>
                    <input type="text" id="full_name" name="full_name" class="form-control" 
                           value="<?php echo htmlspecialchars($admin['full_name']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" class="form-control" 
                           value="<?php echo htmlspecialchars($admin['username']); ?>" required>
                </div>

                <div class="password-fields">
                    <h3><i class="fas fa-lock"></i> Change Password</h3>
                    <div class="form-group password-toggle">
                        <label for="current_password">Current Password</label>
                        <input type="password" id="current_password" name="current_password" class="form-control">
                        <i class="fas fa-eye" id="toggleCurrentPassword"></i>
                    </div>

                    <div class="form-group password-toggle">
                        <label for="new_password">New Password</label>
                        <input type="password" id="new_password" name="new_password" class="form-control">
                        <i class="fas fa-eye" id="toggleNewPassword"></i>
                    </div>

                    <div class="form-group password-toggle">
                        <label for="confirm_password">Confirm New Password</label>
                        <input type="password" id="confirm_password" name="confirm_password" class="form-control">
                        <i class="fas fa-eye" id="toggleConfirmPassword"></i>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="reset" class="btn btn-outline">
                        <i class="fas fa-undo"></i> Reset
                    </button>
                    <button type="submit" name="update_profile" class="btn btn-primary">
                        <i class="fas fa-save"></i> Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Avatar preview
        document.getElementById('avatarInput').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    document.getElementById('avatarPreview').src = event.target.result;
                }
                reader.readAsDataURL(file);
            }
        });

        // Password toggle
        function setupPasswordToggle(inputId, toggleId) {
            const input = document.getElementById(inputId);
            const toggle = document.getElementById(toggleId);
            
            toggle.addEventListener('click', function() {
                if (input.type === 'password') {
                    input.type = 'text';
                    toggle.classList.remove('fa-eye');
                    toggle.classList.add('fa-eye-slash');
                } else {
                    input.type = 'password';
                    toggle.classList.remove('fa-eye-slash');
                    toggle.classList.add('fa-eye');
                }
            });
        }

        setupPasswordToggle('current_password', 'toggleCurrentPassword');
        setupPasswordToggle('new_password', 'toggleNewPassword');
        setupPasswordToggle('confirm_password', 'toggleConfirmPassword');
    </script>
</body>
</html>