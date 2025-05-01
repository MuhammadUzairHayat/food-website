<?php
include './menu.php';

// Database connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    // Validate and sanitize inputs
    $title = trim($_POST['title']);
    $image_url = trim($_POST['image_url']);
    $featured = isset($_POST['featured']) ? 1 : 0;
    $active = isset($_POST['active']) ? 1 : 0;

    // Input validation
    $errors = [];

    if (empty($title)) {
        $errors[] = "Category title is required";
    }

    if (empty($image_url)) {
        $errors[] = "Image URL is required";
    } elseif (!filter_var($image_url, FILTER_VALIDATE_URL)) {
        $errors[] = "Invalid image URL format";
    }

    // If no errors, proceed with database operation
    if (empty($errors)) {
        // Check if category already exists
        $check_sql = "SELECT id FROM tbl_category WHERE title = ?";
        $check_stmt = $conn->prepare($check_sql);

        if ($check_stmt === false) {
            $errors[] = "Database error: " . $conn->error;
        } else {
            $check_stmt->bind_param("s", $title);
            $check_stmt->execute();
            $check_stmt->store_result();

            if ($check_stmt->num_rows > 0) {
                $errors[] = "Category already exists";
            } else {
                // Insert new category - CORRECTED TABLE NAME
                $insert_sql = "INSERT INTO tbl_category (title, image_name, feature, active) VALUES (?, ?, ?, ?)";
                $insert_stmt = $conn->prepare($insert_sql);

                if ($insert_stmt === false) {
                    $errors[] = "Database error: " . $conn->error;
                } else {
                    $insert_stmt->bind_param("ssii", $title, $image_url, $featured, $active);

                    if ($insert_stmt->execute()) {
                        $_SESSION['success'] = "Category created successfully!";
                        header("Location: category.php");
                        exit();
                    } else {
                        $errors[] = "Error creating category: " . $insert_stmt->error;
                    }

                    $insert_stmt->close();
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
    <title>Add Category | Restaurant Management</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        
    </style>
</head>

<body>
    <div class="admin-container">
        <div class="form-header">
            <h2><i class="fas fa-tags"></i> Add New Category</h2>
            <p>Create a new food category for your restaurant menu.</p>
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
                    <img src="https://media.istockphoto.com/id/1166559399/vector/trolley.jpg?s=612x612&w=0&k=20&c=OPOHWiLwlyw7jzMMQPlG85HiACz_rws_W0sIsa3KWQw="
                        alt="Category Preview"
                        class="image-preview"
                        id="image-preview">
                    <label for="image-upload" class="image-upload-btn">
                        <i class="fas fa-camera"></i> Upload Image
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
                            value="<?php echo isset($_POST['title']) ? htmlspecialchars($_POST['title']) : ''; ?>">
                    </div>
                </div>

                <!-- Image URL -->
                <div class="form-group">
                    <label for="image_url"><i class="fas fa-link"></i> Image URL</label>
                    <div class="input-icon">
                        <i class="fas fa-image"></i>
                        <input type="url" id="image_url" name="image_url" style="padding: .5rem 2.5rem;" class="form-control"
                            placeholder="https://example.com/category-image.jpg"
                            value="<?php echo isset($_POST['image_url']) ? htmlspecialchars($_POST['image_url']) : ''; ?>">
                    </div>
                </div>

                <!-- Featured Category -->
                <div class="form-group">
                    <label><i class="fas fa-star"></i> Featured Category</label>
                    <div class="featured-toggle">
                        <label class="toggle-switch">
                            <input type="checkbox" id="featured" name="featured"
                                <?php echo isset($_POST['featured']) ? 'checked' : ''; ?>>
                            <span class="slider"></span>
                        </label>
                        <span id="featured-status"><?php echo isset($_POST['featured']) ? 'Yes' : 'No'; ?></span>
                    </div>
                </div>

                <!-- Active Status -->
                <div class="form-group">
                    <label><i class="fas fa-toggle-on"></i> Active Status</label>
                    <div class="featured-toggle">
                        <label class="toggle-switch">
                            <input type="checkbox" id="active" name="active"
                                <?php echo !isset($_POST['active']) || $_POST['active'] ? 'checked' : ''; ?>>
                            <span class="slider"></span>
                        </label>
                        <span id="active-status"><?php echo !isset($_POST['active']) || $_POST['active'] ? 'Yes' : 'No'; ?></span>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="form-actions">
                    <a href="categories.php" class="btn btn-outline">
                        <i class="fas fa-arrow-left"></i> Back to List
                    </a>
                    <button type="submit" name="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Create Category
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
                    document.getElementById('image_url').value = event.target.result;
                };
                reader.readAsDataURL(file);
            }
        });

        // Featured Toggle
        document.getElementById('featured').addEventListener('change', function() {
            document.getElementById('featured-status').textContent = this.checked ? 'Yes' : 'No';
        });

        // Active Toggle
        document.getElementById('active').addEventListener('change', function() {
            document.getElementById('active-status').textContent = this.checked ? 'Yes' : 'No';
        });
    </script>
</body>

</html>
<?php include './footer.php' ?>