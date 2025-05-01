<?php
include './menu.php';

// Database connection
$conn = new mysqli('localhost', 'root', '', 'mock-food');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch all food items
$sql = "SELECT * FROM tbl_food ORDER BY title ASC";
$res = mysqli_query($conn, $sql);

// Check if query executed successfully
if ($res === false) {
    $_SESSION['error'] = "Error fetching food items: " . $conn->error;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Food Management</title>
    <link rel="stylesheet" href="../css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
      
    </style>
</head>

<body>
    <div class="content">
        <h1>Food Management</h1>
        <p>Manage your restaurant's menu items from this panel.</p>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <?php echo $_SESSION['success'];
                unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-error">
                <?php echo $_SESSION['error'];
                unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <a class="add-food-btn" href="add-food.php"><i class="fas fa-plus"></i> Add Food</a>

        <div class="table-container">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Image</th>
                        <th>Title</th>
                        <th>Description</th>
                        <th>Price</th>
                        <th>Category</th>
                        <th>Feature</th>
                        <th>Active</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($res) > 0) { ?>
                        <?php while ($row = mysqli_fetch_assoc($res)) { ?>
                            <tr>
                                <td>#FD-<?php echo htmlspecialchars($row['id']); ?></td>
                                <td>
                                    <img class="avatar" src="<?php echo htmlspecialchars($row['image_name'] ?: 'https://via.placeholder.com/50x50?text=Food'); ?>"
                                        alt="<?php echo htmlspecialchars($row['title']); ?>">
                                </td>
                                <td><?php echo htmlspecialchars($row['title']); ?></td>
                                <td><?php echo htmlspecialchars(substr($row['description'], 0, 50)); ?>...</td>
                                <td>$<?php echo number_format($row['price'], 2); ?></td>
                                <td>
                                    <?php
                                    // Fetch category name
                                    $cat_sql = "SELECT title FROM tbl_category WHERE id = " . (int)$row['category_id'];
                                    $cat_res = mysqli_query($conn, $cat_sql);
                                    if ($cat_res && mysqli_num_rows($cat_res) > 0) {
                                        $cat_row = mysqli_fetch_assoc($cat_res);
                                        echo htmlspecialchars($cat_row['title']);
                                    } else {
                                        echo "Uncategorized";
                                    }
                                    ?>
                                </td>
                                <td class="success-icon">
                                    <i class="fas fa-<?php echo $row['feature'] ? 'check-circle' : 'times-circle error-icon'; ?>"></i>
                                </td>
                                <td class="success-icon">
                                    <i class="fas fa-<?php echo $row['active'] ? 'check-circle success-icon' : 'times-circle error-icon'; ?>"></i>
                                </td>
                                <td>
                                    <a href="update-food.php?id=<?php echo $row['id']; ?>" class="bg-update-btn">
                                        <i class="fas fa-edit"></i> Update
                                    </a>
                                    <a href="delete-food.php?id=<?php echo $row['id']; ?>" class="bg-delete-btn"
                                        onclick="return confirm('Are you sure you want to delete this food item?');">
                                        <i class="fas fa-trash-alt"></i> Delete
                                    </a>
                                </td>
                            </tr>
                        <?php }
                    } else { ?>
                        <tr>
                            <td colspan="9">
                                <div class="no-items-found">
                                    <img src="https://media.istockphoto.com/id/1437459385/vector/business-person-researching-with-magnifying-glass.jpg?s=612x612&w=0&k=20&c=Hxkn3cV-qTZ8SV-TmYqcgxm8T6BGKYaQu-O65hqkUzU=" alt="No items found">
                                    <p>No food items found</p>
                                </div>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php include 'footer.php'; ?>
</body>

</html>
<?php
mysqli_close($conn);
?>