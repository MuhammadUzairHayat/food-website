<?php
session_start();
include('../config/constants.php');

$order_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($order_id <= 0) {
    die("Invalid order ID");
}

try {
    $order_query = "SELECT * FROM tbl_order WHERE id = ?";
    $order_stmt = $conn->prepare($order_query);
    $order_stmt->bind_param("i", $order_id);
    $order_stmt->execute();
    $order_result = $order_stmt->get_result();
    $order = $order_result->fetch_assoc();

    if (!$order) {
        die("Order not found");
    }

    // Get food details (if needed)
    $food_query = "SELECT *
                   FROM tbl_food f 
                   LEFT JOIN tbl_order o ON f.category_id = o.id 
                   WHERE f.id = ?";
    $food_stmt = $conn->prepare($food_query);
    $food_stmt->bind_param("i", $order['food']);
    $food_stmt->execute();
    $food_result = $food_stmt->get_result();
    $food = $food_result->fetch_assoc();
} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}

// Function to format status for display
function formatStatus($status)
{
    $statusMap = [
        'ordered' => 'Ordered',
        'on delivery' => 'On Delivery',
        'delivered' => 'Delivered',
        'cancelled' => 'Cancelled'
    ];
    return $statusMap[strtolower($status)] ?? ucfirst($status);
}

// Function to get status CSS class
function getStatusClass($status)
{
    $status = strtolower($status);
    if ($status === 'on delivery') return 'status-processing';
    if ($status === 'delivered') return 'status-delivered';
    if ($status === 'cancelled') return 'status-cancelled';
    return 'status-pending';
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order #<?= htmlspecialchars($order['id']) ?> - Food Order System</title>
    <link rel="stylesheet" href="../css/admin.css">
</head>

<body>
    <div class="order-header">
        <h1 class="order-title">Order #<?= htmlspecialchars($order['id']) ?></h1>
        <span class="order-status <?= getStatusClass($order['status']) ?>"><?= formatStatus($order['status']) ?></span>
        <div class="action-buttons">
        <button class="btn btn-primary">Print Invoice</button>
    </div>
    </div>




    <div class="order-details-grid">
        <div class="order-main">
            <div class="order-section">
                <h2 class="section-title">Order Items</h2>
                <table class="order-items">
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th>Price</th>
                            <th>Qty</th>
                            <th>Total</th>
                            <th>Order Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <div style="display: flex; gap: 15px; align-items: center;">
                                    <?php if ($food && !empty($food['image_name'])): ?>
                                        <img src="<?php echo $food['image_name'] ?>" alt="<?= htmlspecialchars($order['food']) ?>" class="item-image">
                                    <?php else: ?>
                                        <div style="width: 80px; height: 80px; background: #f5f5f5; display: flex; align-items: center; justify-content: center; border-radius: 4px;">
                                            <span style="color: #999;">No image</span>
                                        </div>
                                    <?php endif; ?>
                                    <div>
                                        <div class="item-name"><?= htmlspecialchars($food['title']) ?></div>
                                        <?php if ($food && !empty($food['category_title'])): ?>
                                            <div style="font-size: 13px; color: #888; margin-top: 5px;">Category: <?= htmlspecialchars($food['category_title']) ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </td>
                            <td><?= number_format($order['price'], 2) ?> PKR</td>
                            <td><?= htmlspecialchars($order['qty']) ?></td>
                            <td><?= number_format($order['total'], 2) ?> PKR</td>
                            <td><?= date('M j, Y g:i a', strtotime($order['order_date'])) ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="order-section">
                <h2 class="section-title">Delivery Information</h2>
                <div class="address-box">
                    <div class="address-title">Customer Details</div>
                    <div><strong>Name:</strong> <?= htmlspecialchars($order['customer_name']) ?></div>
                    <div><strong>Contact:</strong> <?= htmlspecialchars($order['customer_contact']) ?></div>
                    <div><strong>Email:</strong> <?= htmlspecialchars($order['customer_email']) ?></div>
                </div>

                <div class="address-box" style="margin-top: 20px;">
                    <div class="address-title">Delivery Address</div>
                    <div><?= htmlspecialchars($order['customer_address']) ?></div>
                </div>
            </div>
        </div>

        <div class="order-sidebar">

            <div class="order-section">
                <h2 class="section-title">Order Details</h2>
                <div><strong>Order Date:</strong> <?= date('M j, Y g:i a', strtotime($order['order_date'])) ?></div>
                <div><strong>Order ID:</strong> <?= htmlspecialchars($order['id']) ?></div>
                <?php if ($food): ?>
                    <div style="margin-top: 10px;">
                        <div class="address-title">Food Details</div>
                        <?php if (!empty($food['feature'])): ?>
                            <div><strong>Feature:</strong> <?= htmlspecialchars($food['feature']) ? "✅ Yes" : "❌ No" ?></div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        // Simple print function for the invoice button
        document.querySelector('.btn-primary').addEventListener('click', function() {
            window.print();
        });

        // Cancel order functionality (would need AJAX in a real implementation)
        document.querySelector('.btn-outline')?.addEventListener('click', function() {
            if (confirm('Are you sure you want to cancel this order?')) {
                alert('Order cancellation request sent. This would connect to your backend in a real implementation.');
                // In a real app, you would make an AJAX call here to update the order status
            }
        });
    </script>
</body>

</html>