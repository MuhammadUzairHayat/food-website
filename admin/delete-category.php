<?php
include './menu.php';

// Database connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get and validate ID parameter
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    $_SESSION['error'] = "Invalid category ID";
    header("Location: categories.php");
    exit();
}

// Fetch category data using prepared statement
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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Category | Restaurant Management</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        
    </style>
</head>
<body>
    <section style="display: flex; justify-content: center; margin-block: 5rem;">
        <div class="delete-container">
            <div class="delete-header">
                <h2><i class="fas fa-trash-alt"></i> Confirm Deletion</h2>
                <img src="<?php echo htmlspecialchars($category['image_name'] ?: 'https://ui-avatars.com/api/?name='.urlencode($category['title']).'&background=f72585&color=fff'); ?>" 
                     alt="<?php echo htmlspecialchars($category['title']); ?>">
                <div>
                    <h4><?php echo htmlspecialchars($category['title']); ?></h4>
                    <p>Category ID: <?php echo htmlspecialchars($category['id']); ?></p>
                </div>
            </div>
            <div class="delete-body">
                <div class="delete-icon" style="font-size: 1rem; font-weight: 500">
                    <i class="fas fa-exclamation-triangle fa-2x"></i>
                    <p class="">
                        This action cannot be undone. All menu items in this category will be affected.
                    </p>
                </div>
                <div class="delete-details">
                    <p><strong>ID:</strong> <?php echo htmlspecialchars($category['id']); ?></p>
                    <p><strong>Title:</strong> <?php echo htmlspecialchars($category['title']); ?></p>
                    <p><strong>Feature:</strong> <?php echo $category['feature'] ? 'Yes' : 'No'; ?></p>
                    <p><strong>Active:</strong> <?php echo $category['active'] ? 'Yes' : 'No'; ?></p>
                </div>
                <div class="delete-actions">
                    <a href="categories.php" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                    <a href="execute-delete-category.php?id=<?php echo htmlspecialchars($category['id']); ?>" class="btn btn-danger">
                        <i class="fas fa-trash-alt"></i> Delete Permanently
                    </a>
                </div>
            </div>
        </div>
    </section>

    <script>
        function confirmDelete() {
            return confirm("Are you sure you want to delete this category? This action cannot be undone.");
        }
        
        // Attach confirmation to delete button
        document.querySelector('.btn-danger').addEventListener('click', function(e) {
            if (!confirmDelete()) {
                e.preventDefault();
            }
        });
    </script>
</body>
</html>
<?php include './footer.php'; ?>