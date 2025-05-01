<?php
include './menu.php';

// Database connection
$conn = new mysqli('localhost', 'root', '', 'mock-food');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch categories for dropdown
$categories = [];
$cat_sql = "SELECT id, title FROM tbl_category WHERE active = 1 ORDER BY title ASC";
$cat_res = $conn->query($cat_sql);
if ($cat_res && $cat_res->num_rows > 0) {
    while ($cat_row = $cat_res->fetch_assoc()) {
        $categories[] = $cat_row;
    }
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    // Validate and sanitize inputs
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $price = filter_input(INPUT_POST, 'price', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $category_id = (int)$_POST['category_id'];
    $feature = isset($_POST['feature']) ? 1 : 0;
    $active = isset($_POST['active']) ? 1 : 0;
    $image_name = trim($_POST['image_name']);

    // Input validation
    $errors = [];
    
    if (empty($title)) {
        $errors[] = "Food title is required";
    }
    
    if (empty($description)) {
        $errors[] = "Description is required";
    }
    
    if (!is_numeric($price) || $price <= 0) {
        $errors[] = "Valid price is required";
    }
    
    if (empty($category_id) || $category_id <= 0) {
        $errors[] = "Category is required";
    }
    
    if (empty($image_name)) {
        $errors[] = "Image URL is required";
    } elseif (!filter_var($image_name, FILTER_VALIDATE_URL)) {
        $errors[] = "Invalid image URL format";
    }

    // If no errors, proceed with database operation
    if (empty($errors)) {
        $insert_sql = "INSERT INTO tbl_food (title, description, price, image_name, category_id, feature, active) 
                      VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($insert_sql);
        
        if ($stmt === false) {
            $errors[] = "Database error: " . $conn->error;
        } else {
            $stmt->bind_param("ssdsiii", $title, $description, $price, $image_name, $category_id, $feature, $active);
            
            if ($stmt->execute()) {
                $_SESSION['success'] = "Food item added successfully!";
                header("Location: food.php");
                exit();
            } else {
                $errors[] = "Error adding food item: " . $stmt->error;
            }
            
            $stmt->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Food Item | Restaurant Management</title>
    <link rel="stylesheet" href="../css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        
    </style>
</head>

<body>
    <div class="admin-container">
        <div class="form-header">
            <h2><i class="fas fa-utensils"></i> Add New Food Item</h2>
            <p>Add a new menu item to your restaurant's offerings.</p>
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
            <form method="POST" class="food-form">
                <!-- Image Upload -->
                <div class="image-upload">
                    <img src="https://media.istockphoto.com/id/1472448713/vector/crate-of-vegetables-line-icon-outline-symbol-vector-illustration-concept-sign.jpg?s=612x612&w=0&k=20&c=j-s8sl6HEJ7haWYL1VAB1Y8yWPAKCAfvVxLcTE0lPCw=" 
                         alt="Food Preview" 
                         class="image-preview"
                         id="image-preview">
                    <label for="image-upload" class="image-upload-btn">
                        <i class="fas fa-camera"></i> Upload Image
                    </label>
                    <input type="file" id="image-upload" accept="image/*">
                </div>

                <!-- Title -->
                <div class="form-group">
                    <label for="title"><i class="fas fa-heading"></i> Food Title</label>
                    <div class="input-icon">
                        <i class="fas fa-tag"></i>
                        <input type="text" id="title" name="title" style="padding: .5rem 2.5rem;" class="form-control"
                            placeholder="Enter food title" required
                            value="<?php echo isset($_POST['title']) ? htmlspecialchars($_POST['title']) : ''; ?>">
                    </div>
                </div>
                
                <!-- Description -->
                <div class="form-group">
                    <label for="description"><i class="fas fa-align-left"></i> Description</label>
                    <div class="input-icon">
                        <i class="fas fa-pen"></i>
                        <textarea id="description" name="description" style="padding: .5rem 2.5rem;" class="form-control"
                            placeholder="Enter food description" required><?php echo isset($_POST['description']) ? htmlspecialchars($_POST['description']) : ''; ?></textarea>
                    </div>
                </div>
                
                <!-- Price -->
                <div class="form-group">
                    <label for="price"><i class="fas fa-dollar-sign"></i> Price</label>
                    <div class="input-icon">
                        <i class="fas fa-money-bill-wave"></i>
                        <input type="number" step="0.01" id="price" name="price" style="padding: .5rem 2.5rem;" class="form-control"
                            placeholder="Enter price (e.g. 9.99)" required
                            value="<?php echo isset($_POST['price']) ? htmlspecialchars($_POST['price']) : ''; ?>">
                    </div>
                </div>
                
                <!-- Category -->
                <div class="form-group">
                    <label for="category_id"><i class="fas fa-tags"></i> Category</label>
                    <div class="input-icon">
                        <i class="fas fa-list"></i>
                        <select id="category_id" name="category_id" style="padding: .5rem 2.5rem;" class="form-control" required>
                            <option value="">Select a category</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?php echo $category['id']; ?>"
                                    <?php echo (isset($_POST['category_id']) && $_POST['category_id'] == $category['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($category['title']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                
                <!-- Image URL -->
                <div class="form-group">
                    <label for="image_name"><i class="fas fa-link"></i> Image URL</label>
                    <div class="input-icon">
                        <i class="fas fa-image"></i>
                        <input type="url" id="image_name" name="image_name" style="padding: .5rem 2.5rem;" class="form-control"
                            placeholder="https://example.com/food-image.jpg" required
                            value="<?php echo isset($_POST['image_name']) ? htmlspecialchars($_POST['image_name']) : ''; ?>">
                    </div>
                </div>

                <!-- Feature Food -->
                <div class="form-group">
                    <label><i class="fas fa-star"></i> Feature Item</label>
                    <div class="feature-toggle">
                        <label class="toggle-switch">
                            <input type="checkbox" id="feature" name="feature" 
                                   <?php echo isset($_POST['feature']) ? 'checked' : ''; ?>>
                            <span class="slider"></span>
                        </label>
                        <span id="feature-status"><?php echo isset($_POST['feature']) ? 'Yes' : 'No'; ?></span>
                    </div>
                </div>

                <!-- Active Status -->
                <div class="form-group">
                    <label><i class="fas fa-toggle-on"></i> Active Status</label>
                    <div class="feature-toggle">
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
                    <a href="food.php" class="btn btn-outline">
                        <i class="fas fa-arrow-left"></i> Back to List
                    </a>
                    <button type="submit" name="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Add Food Item
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
<?php include './footer.php'; ?>