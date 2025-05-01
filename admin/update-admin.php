<?php 
include './menu.php';

// Database connection (should be in a separate config file)
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Secure the ID parameter
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    $_SESSION['error'] = "Invalid admin ID";
    header("Location: admin.php");
    exit();
}

// Fetch admin data using prepared statement
$sql = "SELECT * FROM tbl_admin WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();
$admin = $res->fetch_assoc();
$stmt->close();

if (!$admin) {
    $_SESSION['error'] = "Admin not found";
    header("Location: admin.php");
    exit();
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    // Validate and sanitize inputs
    $username = trim($_POST['username']);
    $full_name = trim($_POST['full_name']);
    $avatar = trim($_POST['avatar']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Input validation
    $errors = [];
    
    if (empty($username)) {
        $errors[] = "Username is required";
    }
    
    if (empty($full_name)) {
        $errors[] = "Full name is required";
    }
    
    if (!empty($password) && strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters long";
    } elseif (!empty($password) && $password !== $confirm_password) {
        $errors[] = "Passwords do not match";
    }

    // If no errors, proceed with update
    if (empty($errors)) {
        // Check if username already exists (excluding current admin)
        $check_sql = "SELECT id FROM tbl_admin WHERE username = ? AND id != ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("si", $username, $id);
        $check_stmt->execute();
        $check_stmt->store_result();
        
        if ($check_stmt->num_rows > 0) {
            $errors[] = "Username already exists";
        } else {
            // Build SQL based on whether password was changed
            $password_update = "";
            $params = [$username, $full_name, $avatar, $id];
            $types = "sssi";
            
            if (!empty($password)) {
                $password_update = ", password = ?";
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $params = [$username, $full_name, $avatar, $hashed_password, $id];
                $types = "ssssi";
            }
            
            $sql = "UPDATE tbl_admin SET 
                    username = ?, 
                    full_name = ?, 
                    avatar = ?
                    {$password_update}
                    WHERE id = ?";
            
            $stmt = $conn->prepare($sql);
            $stmt->bind_param($types, ...$params);

            if ($stmt->execute()) {
                $_SESSION['success'] = "Admin updated successfully";
                header("Location: admin.php");
                exit();
            } else {
                $errors[] = "Error updating admin: " . $stmt->error;
            }
            $stmt->close();
        }
        $check_stmt->close();
    }
    
    // Store errors in session if redirect is needed
    if (!empty($errors)) {
        $_SESSION['error'] = implode("<br>", $errors);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Admin | Restaurant Management</title>
    <link rel="stylesheet" href="../css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        
    </style>
</head>
<body>
    <div class="form-container content">
        <form method="POST" class="form">
            <input type="hidden" name="id" value="<?php echo $admin['id']; ?>">
            <h1 class="text-center"><i class="fas fa-user-edit"></i> Edit Admin</h1>
            
            <?php if (isset($_SESSION['error'])): ?>
                <div class="error-alert">
                    <i class="fas fa-exclamation-circle"></i>
                    <div><?php echo $_SESSION['error']; ?></div>
                </div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>

            <!-- Avatar Upload -->
            <div class="form-group" style="display: flex; flex-direction: column; align-items: center; gap: 1rem;">
                <img style="width: 120px; height: 120px; object-fit: cover; border-radius: 50%; border: 3px solid var(--info-color)" 
                     src="<?php echo htmlspecialchars($admin['avatar'] ?: 'https://ui-avatars.com/api/?name=' . urlencode($admin['username']) . '&background=4361ee&color=fff'); ?>" 
                     alt="Admin Avatar"
                     id="avatar-preview">
                <label for="avatar-upload" style="cursor: pointer; display: flex; align-items: center; gap: 0.5rem;">
                    <i class="fas fa-camera"></i> Change profile avatar
                    <input type="file" name="avatar-upload" id="avatar-upload" accept="image/*" hidden>
                </label>
                <input type="hidden" name="avatar" id="avatar-url" value="<?php echo htmlspecialchars($admin['avatar']); ?>">
            </div>

            <!-- Form Fields -->
            <div class="form-group">
                <label for="username"><i class="fas fa-user"></i> Username</label>
                <input type="text" name="username" id="username" required 
                       placeholder="Enter username" class="form-control" 
                       value="<?php echo htmlspecialchars($admin['username']); ?>">
            </div>
            
            <div class="form-group">
                <label for="full_name"><i class="fas fa-id-card"></i> Full Name</label>
                <input type="text" name="full_name" id="full_name" required 
                       placeholder="Enter full name" class="form-control" 
                       value="<?php echo htmlspecialchars($admin['full_name']); ?>">
            </div>
            
            <div class="form-group">
                <label for="password"><i class="fas fa-lock"></i> New Password (leave blank to keep current)</label>
                <div style="position: relative;">
                    <input type="password" name="password" id="password" 
                           placeholder="Enter new password" class="form-control">
                    <i class="fas fa-eye password-toggle" style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); cursor: pointer;"></i>
                </div>
                <small style="display: block; margin-top: 0.5rem; color: var(--gray);">Minimum 8 characters</small>
            </div>
            
            <div class="form-group">
                <label for="confirm_password"><i class="fas fa-check-circle"></i> Confirm Password</label>
                <input type="password" name="confirm_password" id="confirm_password" 
                       placeholder="Confirm password" class="form-control">
            </div>

            <!-- Form Actions -->
            <div class="form-buttons-div">
                <a href="admin.php" class="form-back-btn">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
                <button type="submit" name="submit" class="form-submit-btn">
                    <i class="fas fa-save"></i> Update Admin
                </button>
            </div>
        </form>
    </div>
    <?php include './footer.php' ?>
    <script>
        // Avatar preview functionality
        document.getElementById('avatar-upload').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    document.getElementById('avatar-preview').src = event.target.result;
                    document.getElementById('avatar-url').value = event.target.result;
                }
                reader.readAsDataURL(file);
            }
        });

        // Password toggle visibility
        document.querySelector('.password-toggle').addEventListener('click', function() {
            const passwordInput = document.getElementById('password');
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                this.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                this.classList.replace('fa-eye-slash', 'fa-eye');
            }
        });

        // Form validation
        document.querySelector('form').addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            
            if (password && password !== confirmPassword) {
                e.preventDefault();
                alert('Passwords do not match!');
                document.getElementById('confirm_password').focus();
            }
        });
    </script>

</body>
</html>