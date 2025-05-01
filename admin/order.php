<?php
include './menu.php';

// Database connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch all orders with customer and food details
$sql = "SELECT o.*, 
               f.title AS food_name,
               f.price AS food_price,
               f.image_name AS food_image,
               c.title AS category_name
        FROM tbl_order o
        JOIN tbl_food f ON o.food = f.id
        LEFT JOIN tbl_category c ON f.category_id = c.id
        ORDER BY o.order_date DESC";
$res = mysqli_query($conn, $sql);

// Check if query executed successfully
if ($res === false) {
    $_SESSION['error'] = "Error fetching orders: " . $conn->error;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Management | Restaurant Management</title>
    <link rel="stylesheet" href="../css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
       
    </style>
</head>

<body>
    <div class="content">
        <h1>Order Management</h1>
        <p>Manage customer orders from this panel.</p>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <?php echo $_SESSION['success'];
                unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i>
                <?php echo $_SESSION['error'];
                unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <a class="add-order-btn" href="add-order.php"><i class="fas fa-plus"></i> Add Order</a>

        <div class="table-container">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Food</th>
                        <th>Title</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Total</th>
                        <th>Order Date</th>
                        <th>Status</th>
                        <th>Customer</th>
                        <th>Contact</th>
                        <th>Address</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($res) > 0): ?>
                        <?php while ($order = mysqli_fetch_assoc($res)): ?>
                            <tr>
                                <td>#ORD-<?php echo str_pad($order['id'], 3, '0', STR_PAD_LEFT); ?></td>
                                <td><img class="avatar" src="<?php echo htmlspecialchars($order['food_image']); ?>" alt="food_image"></td>
                                <td><?php echo htmlspecialchars($order['food_name']); ?></td>
                                <td>$<?php echo number_format($order['food_price'], 2); ?></td>
                                <td><?php echo htmlspecialchars($order['qty']); ?></td>
                                <td>$<?php echo number_format($order['food_price'] * $order['qty'], 2); ?></td>
                                <td><?php echo date('Y-m-d', strtotime($order['order_date'])); ?></td>
                                <td>
                                    <?php
                                    $status_class = '';
                                    switch (strtolower($order['status'])) {
                                        case 'delivered':
                                            $status_class = 'status-delivered';
                                            break;
                                        case 'processing':
                                            $status_class = 'status-processing';
                                            break;
                                        case 'cancelled':
                                            $status_class = 'status-cancelled';
                                            break;
                                        default:
                                            $status_class = 'status-processing';
                                    }
                                    ?>
                                    <span class="<?php echo $status_class; ?>">
                                        <?php echo ucfirst($order['status']); ?>
                                    </span>
                                </td>
                                <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
                                <td><?php echo htmlspecialchars($order['customer_contact']); ?></td>
                                <td><?php echo htmlspecialchars($order['customer_address']); ?></td>
                                <td>
                                    <a href="update-order.php?id=<?php echo $order['id']; ?>" class="bg-update-btn">
                                        <i class="fas fa-edit"></i> Update
                                    </a>
                                    <a href="delete-order.php?id=<?php echo $order['id']; ?>" class="bg-delete-btn"
                                        onclick="return confirm('Are you sure you want to delete this order?');">
                                        <i class="fas fa-trash-alt"></i> Delete
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="10" style="text-align: center; padding: 2rem;">
                                <div style="display: flex; flex-direction: column; align-items: center;">
                                    <img src="https://cdn-icons-png.flaticon.com/512/4076/4076478.png"
                                        alt="No orders"
                                        style="width: 100px; height: 100px; margin-bottom: 1rem;">
                                    <p>No orders found</p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php include 'footer.php'; ?>
</body>

</html>
<?php
// Close database connection
mysqli_close($conn);
?>