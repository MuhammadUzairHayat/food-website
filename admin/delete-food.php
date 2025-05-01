<?php
include './menu.php';

// Database connection
$conn = new mysqli('localhost', 'root', '', 'mock-food');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Secure the ID parameter
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    $_SESSION['error'] = "Invalid food ID";
    header("Location: food.php");
    exit();
}

// Fetch food data using prepared statement
$sql = "SELECT * FROM tbl_food WHERE id = ?";
$stmt = $conn->prepare($sql);
if ($stmt === false) {
    die("Prepare failed: " . $conn->error);
}

$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();
$food = $res->fetch_assoc();
$stmt->close();

if (!$food) {
    $_SESSION['error'] = "Food item not found";
    header("Location: food.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Food Item | Restaurant Management</title>
    <link rel="stylesheet" href="../css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
       
    </style>
</head>
<body>
    <section style="display: flex; justify-content: center; margin-block: 5rem;">
        <div class="delete-container">
            <div class="delete-header">
                <h2><i class="fas fa-trash-alt"></i> Confirm Deletion</h2>
                <img src="<?php echo htmlspecialchars($food['image_name'] ?: 'https://ui-avatars.com/api/?name='.urlencode($food['title']).'&background=f72585&color=fff'); ?>" 
                     alt="<?php echo htmlspecialchars($food['title']); ?>">
                <div>
                    <h4><?php echo htmlspecialchars($food['title']); ?></h4>
                    <p>Food ID: <?php echo htmlspecialchars($food['id']); ?></p>
                </div>
            </div>
            <div class="delete-body">
                <div class="delete-icon" style="font-size: 1rem; text-align:left">
                    <i class="fas fa-exclamation-triangle fa-2x"></i>
                    <p >
                        This action cannot be undone. This food item will be permanently removed.
                    </p>
                </div>
                <div class="delete-details">
                    <p><strong>ID:</strong> <?php echo htmlspecialchars($food['id']); ?></p>
                    <p><strong>Title:</strong> <?php echo htmlspecialchars($food['title']); ?></p>
                    <p><strong>Price:</strong> $<?php echo number_format($food['price'], 2); ?></p>
                    <p><strong>Feature:</strong> <?php echo $food['feature'] ? 'Yes' : 'No'; ?></p>
                    <p><strong>Active:</strong> <?php echo $food['active'] ? 'Yes' : 'No'; ?></p>
                </div>
                <div class="delete-actions">
                    <a href="food.php" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                    <a href="execute-delete-food.php?id=<?php echo htmlspecialchars($food['id']); ?>" class="btn btn-danger">
                        <i class="fas fa-trash-alt"></i> Delete Permanently
                    </a>
                </div>
            </div>
        </div>
    </section>

    <script>
        function confirmDelete() {
            return confirm("Are you sure you want to delete this food item? This action cannot be undone.");
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