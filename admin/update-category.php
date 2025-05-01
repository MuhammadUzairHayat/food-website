<?php 
include './menu.php';

// Database connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Secure the ID parameter
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    $_SESSION['error'] = "Invalid category ID";
    header("Location: categories.php");
    exit();
}

// Fetch category data
$sql = "SELECT * FROM tbl_category WHERE id = ?";
$stmt = $conn->prepare($sql);
if ($stmt === false) {
    die("Prepare failed: " . $conn->error);
}

$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();
$category = $res->fetch_assoc();
$stmt->close();

if (!$category) {
    $_SESSION['error'] = "Category not found";
    header("Location: categories.php");
    exit();
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    // Validate and sanitize inputs
    $title = trim($_POST['title']);
    $image_name = trim($_POST['image_name']);
    $feature = isset($_POST['feature']) ? 1 : 0;
    $active = isset($_POST['active']) ? 1 : 0;

    // Input validation
    $errors = [];
    
    if (empty($title)) {
        $errors[] = "Category title is required";
    }
    
    if (empty($image_name)) {
        $errors[] = "Image URL is required";
    } elseif (!filter_var($image_name, FILTER_VALIDATE_URL)) {
        $errors[] = "Invalid image URL format";
    }

    if (empty($errors)) {
        // Check if title already exists (excluding current category)
        $check_sql = "SELECT id FROM tbl_category WHERE title = ? AND id != ?";
        $check_stmt = $conn->prepare($check_sql);
        
        if ($check_stmt === false) {
            $errors[] = "Database error: " . $conn->error;
        } else {
            $check_stmt->bind_param("si", $title, $id);
            $check_stmt->execute();
            $check_stmt->store_result();
            
            if ($check_stmt->num_rows > 0) {
                $errors[] = "Category title already exists";
            } else {
                // Update category
                $update_sql = "UPDATE tbl_category SET 
                               title = ?, 
                               image_name = ?, 
                               feature = ?, 
                               active = ? 
                               WHERE id = ?";
                $update_stmt = $conn->prepare($update_sql);
                
                if ($update_stmt === false) {
                    $errors[] = "Database error: " . $conn->error;
                } else {
                    $update_stmt->bind_param("ssiii", $title, $image_name, $feature, $active, $id);

                    if ($update_stmt->execute()) {
                        $_SESSION['success'] = "Category updated successfully!";
                        header("Location: category.php");
                        exit();
                    } else {
                        $errors[] = "Error updating category: " . $update_stmt->error;
                    }
                    $update_stmt->close();
                }
            }
            $check_stmt->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Category | Restaurant Management</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
       
    </style>
</head>
<body>
    <div class="admin-container">
        <div class="form-header">
            <h2><i class="fas fa-edit"></i> Update Category</h2>
            <p>Modify the category details below.</p>
        </div>

        <?php if (!empty($errors)): ?>
            <div class="error-message">
                <i class="fas fa-exclamation-circle"></i>
                <div>
                    <?php foreach ($errors as $error): ?>
                        <p><?php echo htmlspecialchars($error); ?></p>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <div class="form-card">
            <form method="POST" class="category-form">
                <!-- Image Upload -->
                <div class="image-upload">
                    <img src="<?php echo htmlspecialchars($category['image_name'] ?: 'https://via.placeholder.com/200x150?text=Category+Image'); ?>" 
                         alt="Category Preview" 
                         class="image-preview"
                         id="image-preview">
                    <label for="image-upload" class="image-upload-btn">
                        <i class="fas fa-camera"></i> Change Image
                    </label>
                    <input type="file" id="image-upload" accept="image/*">
                </div>

                <!-- Title -->
                <div class="form-group">
                    <label for="title"><i class="fas fa-heading"></i> Category Title</label>
                    <div class="input-icon">
                        <i class="fas fa-tag"></i>
                        <input type="text" id="title" name="title" style="padding: .5rem 2.5rem;" class="form-control"
                            placeholder="Enter category title" required
                            value="<?php echo htmlspecialchars($category['title']); ?>">
                    </div>
                </div>
                
                <!-- Image URL -->
                <div class="form-group">
                    <label for="image_name"><i class="fas fa-link"></i> Image URL</label>
                    <div class="input-icon">
                        <i class="fas fa-image"></i>
                        <input type="url" id="image_name" name="image_name" style="padding: .5rem 2.5rem;" class="form-control"
                            placeholder="https://example.com/category-image.jpg"
                            value="<?php echo htmlspecialchars($category['image_name']); ?>">
                    </div>
                </div>

                <!-- Feature Category -->
                <div class="form-group">
                    <label><i class="fas fa-star"></i> Feature Category</label>
                    <div class="feature-toggle">
                        <label class="toggle-switch">
                            <input type="checkbox" id="feature" name="feature" 
                                   <?php echo $category['feature'] ? 'checked' : ''; ?>>
                            <span class="slider"></span>
                        </label>
                        <span id="feature-status"><?php echo $category['feature'] ? 'Yes' : 'No'; ?></span>
                    </div>
                </div>

                <!-- Active Status -->
                <div class="form-group">
                    <label><i class="fas fa-toggle-on"></i> Active Status</label>
                    <div class="feature-toggle">
                        <label class="toggle-switch">
                            <input type="checkbox" id="active" name="active" 
                                   <?php echo $category['active'] ? 'checked' : ''; ?>>
                            <span class="slider"></span>
                        </label>
                        <span id="active-status"><?php echo $category['active'] ? 'Yes' : 'No'; ?></span>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="form-actions">
                    <a href="category.php" class="btn btn-outline">
                        <i class="fas fa-arrow-left"></i> Back to List
                    </a>
                    <button type="submit" name="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update Category
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Image Preview
        document.getElementById('image-upload').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    document.getElementById('image-preview').src = event.target.result;
                    document.getElementById('image_name').value = event.target.result;
                };
                reader.readAsDataURL(file);
            }
        });

        // Feature Toggle
        document.getElementById('feature').addEventListener('change', function() {
            document.getElementById('feature-status').textContent = this.checked ? 'Yes' : 'No';
        });

        // Active Toggle
        document.getElementById('active').addEventListener('change', function() {
            document.getElementById('active-status').textContent = this.checked ? 'Yes' : 'No';
        });
    </script>
</body>
</html>
<?php include './footer.php' ?>