<?php include 'menu.php'; ?>

<?php
// session_start();
if (isset($_SESSION['username'])) {
    if (isset($_SESSION['success'])) {
        echo "<div class='alert success-alert'><i class='fas fa-check-circle'></i> Admin '{$_SESSION['username']}' added successfully!</div>";
        unset($_SESSION['admin_added']);
        unset($_SESSION['username']);
    } elseif (isset($_SESSION['error'])) {
        echo "<div class='alert error-alert'><i class='fas fa-exclamation-circle'></i> {$_SESSION['error']}</div>";
        unset($_SESSION['error']);
    }
}

// Query to fetch all admins
function countRecords($conn, $table)
{
    $sql = "SELECT COUNT(*) AS total FROM $table";
    $result = mysqli_query($conn, $sql);

    if ($result === false) {
        die("Database error: " . mysqli_error($conn));
    }

    return mysqli_fetch_assoc($result)['total'];
}
// Query to fetch all admins
function allOrders($conn, $table)
{
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

    return $res;
}

// Get all counts in one go
$total_admins = countRecords($conn, 'tbl_admin');
$total_food = countRecords($conn, 'tbl_food');
$total_categories = countRecords($conn, 'tbl_category');
$total_orders = countRecords($conn, 'tbl_order');
$all_orders_result = allOrders($conn, 'tbl_order');
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | Restaurant Management</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<body>


    <div class="content">
        <div class="dashboard-header">
            <h1>Dashboard Overview</h1>
            <p>Welcome back! Here's what's happening with your restaurant today.</p>
        </div>

        <div class="dashboard-cards">
            <div class="dashboard-card card-primary">
                <div class="dashboard-card-header">
                    <div class="dashboard-card-title">Total Admins</div>
                    <div class="dashboard-card-icon" style="background-color: var(--success);">
                        <i class="fas fa-user-shield"></i>
                    </div>
                </div>
                <div class="dashboard-card-value"><?php echo $total_admins ?></div>
                <div class="dashboard-card-description">Manage system administrators</div>
            </div>

            <div class="dashboard-card card-success" style="border-color: var(--success);">
                <div class="dashboard-card-header">
                    <div class="dashboard-card-title">Food Items</div>
                    <div class="dashboard-card-icon">
                        <i class="fas fa-utensils"></i>
                    </div>
                </div>
                <div class="dashboard-card-value"><?php echo $total_food ?></div>
                <div class="dashboard-card-description">Delicious menu options</div>
            </div>

            <div class="dashboard-card card-warning">
                <div class="dashboard-card-header">
                    <div class="dashboard-card-title">Categories</div>
                    <div class="dashboard-card-icon">
                        <i class="fas fa-list"></i>
                    </div>
                </div>
                <div class="dashboard-card-value"><?php echo $total_categories ?></div>
                <div class="dashboard-card-description">Organized food groups</div>
            </div>

            <div class="dashboard-card card-info">
                <div class="dashboard-card-header">
                    <div class="dashboard-card-title">Orders</div>
                    <div class="dashboard-card-icon">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                </div>
                <div class="dashboard-card-value"><?php echo $total_orders ?></div>
                <div class="dashboard-card-description">Customer orders today</div>
            </div>
        </div>

        <div class="recent-orders">
            <div class="section-header">
                <h2>Recent Orders</h2>
                <a href="order.php" class="view-all">View All <i class="fas fa-arrow-right"></i></a>
            </div>

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
                        <?php if (mysqli_num_rows($all_orders_result) > 0): ?>
                            <?php while ($order = mysqli_fetch_assoc($all_orders_result)): ?>
                                <tr>
                                    <td>#ORD-<?php echo str_pad($order['id'], 3, '0', STR_PAD_LEFT); ?></td>
                                    <td><Img class="avatar" src="<?php echo htmlspecialchars($order['food_image']); ?>" alt="food_image" ></td>
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
                                        <a href="view-order.php?id=<?php echo $order['id']; ?>" class="bg-view-btn">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                        <a href="update-order.php?id=<?php echo $order['id']; ?>" class="bg-update-btn">
                                            <i class="fas fa-edit"></i> update
                                        </a>
                                        <a href="delete-order.php?id=<?php echo $order['id']; ?>" class="bg-delete-btn"
                                            onclick="return confirm('Are you sure you want to delete this order?');">
                                            <i class="fas fa-trash-alt"></i> delete
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
    </div>

    <?php include 'footer.php'; ?>

    <script>
        // Add hover effects to cards
        document.querySelectorAll('.dashboard-card').forEach(card => {
            card.addEventListener('mouseenter', () => {
                card.style.transform = 'translateY(-5px)';
                card.style.boxShadow = '0 10px 20px rgba(0,0,0,0.1)';
            });

            card.addEventListener('mouseleave', () => {
                card.style.transform = '';
                card.style.boxShadow = '';
            });
        });
    </script>
</body>

</html>