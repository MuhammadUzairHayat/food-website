<?php
include './menu.php';

// Database connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get order ID
$order_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($order_id <= 0) {
    $_SESSION['error'] = "Invalid order ID";
    header("Location: order.php");
    exit();
}

// Fetch order details
$order_sql = "SELECT * FROM tbl_order WHERE id = ?";
$stmt = $conn->prepare($order_sql);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$order_result = $stmt->get_result();
$order = $order_result->fetch_assoc();
$stmt->close();

if (!$order) {
    $_SESSION['error'] = "Order not found";
    header("Location: order.php");
    exit();
}

// Fetch active food items
$foods = [];
$food_sql = "SELECT id, title, price FROM tbl_food WHERE active = 1 ORDER BY title ASC";
$food_res = $conn->query($food_sql);
if ($food_res && $food_res->num_rows > 0) {
    while ($food_row = $food_res->fetch_assoc()) {
        $foods[] = $food_row;
    }
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    // Get form data
    $food_id = (int)$_POST['food_id'];
    $qty = (int)$_POST['qty'];
    $customer_name = trim($_POST['customer_name']);
    $customer_contact = trim($_POST['customer_contact']);
    $customer_email = trim($_POST['customer_email']);
    $customer_address = trim($_POST['customer_address']);
    $status = trim($_POST['status']);

    // Get food price
    $food_price = 0;
    foreach ($foods as $food) {
        if ($food['id'] == $food_id) {
            $food_price = $food['price'];
            break;
        }
    }

    // Calculate total
    $total = $food_price * $qty;

    // Input validation
    $errors = [];
    
    if ($food_id <= 0) {
        $errors[] = "Food item is required";
    }
    
    if ($qty <= 0) {
        $errors[] = "Quantity must be at least 1";
    }
    
    if (empty($customer_name)) {
        $errors[] = "Customer name is required";
    }
    
    if (empty($customer_contact)) {
        $errors[] = "Customer contact is required";
    }
    
    if (empty($status)) {
        $errors[] = "Order status is required";
    }

    // If no errors, update order
    if (empty($errors)) {
        $update_sql = "UPDATE tbl_order SET
                      food = ?,
                      price = ?,
                      qty = ?,
                      total = ?,
                      status = ?,
                      customer_name = ?,
                      customer_contact = ?,
                      customer_email = ?,
                      customer_address = ?
                      WHERE id = ?";
        
        $stmt = $conn->prepare($update_sql);
        
        if ($stmt === false) {
            $errors[] = "Database error: " . $conn->error;
        } else {
            $stmt->bind_param("idiisssssi", 
                $food_id, $food_price, $qty, $total, $status,
                $customer_name, $customer_contact, $customer_email, $customer_address,
                $order_id
            );
            
            if ($stmt->execute()) {
                $_SESSION['success'] = "Order updated successfully!";
                header("Location: order.php");
                exit();
            } else {
                $errors[] = "Error updating order: " . $stmt->error;
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
    <title>Update Order | Restaurant Management</title>
    <link rel="stylesheet" href="../css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Same styles as add-order.php */
       
    </style>
</head>
<body>
    <div class="admin-container">
        <div class="form-header">
            <h2><i class="fas fa-edit"></i> Update Order #ORD-<?php echo str_pad($order['id'], 3, '0', STR_PAD_LEFT); ?></h2>
            <p>Update this customer order</p>
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
            <form method="POST">
                <!-- Food Selection -->
                <div class="form-group">
                    <label for="food_id"><i class="fas fa-utensils"></i> Food Item</label>
                    <select id="food_id" name="food_id" class="form-control" required>
                        <option value="">Select a food item</option>
                        <?php foreach ($foods as $food): ?>
                            <option value="<?php echo $food['id']; ?>"
                                <?php echo ((isset($_POST['food_id']) && $_POST['food_id'] == $food['id']) || $order['food'] == $food['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($food['title']); ?> ($<?php echo number_format($food['price'], 2); ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Quantity -->
                <div class="form-group">
                    <label for="qty"><i class="fas fa-list-ol"></i> Quantity</label>
                    <input type="number" id="qty" name="qty" min="1" class="form-control" 
                           value="<?php echo isset($_POST['qty']) ? htmlspecialchars($_POST['qty']) : $order['qty']; ?>" required>
                </div>

                <!-- Customer Information -->
                <div class="form-group">
                    <label for="customer_name"><i class="fas fa-user"></i> Customer Name</label>
                    <input type="text" id="customer_name" name="customer_name" class="form-control"
                           value="<?php echo isset($_POST['customer_name']) ? htmlspecialchars($_POST['customer_name']) : $order['customer_name']; ?>" required>
                </div>

                <div class="form-group">
                    <label for="customer_contact"><i class="fas fa-phone"></i> Contact Number</label>
                    <input type="text" id="customer_contact" name="customer_contact" class="form-control"
                           value="<?php echo isset($_POST['customer_contact']) ? htmlspecialchars($_POST['customer_contact']) : $order['customer_contact']; ?>" required>
                </div>

                <div class="form-group">
                    <label for="customer_email"><i class="fas fa-envelope"></i> Email (Optional)</label>
                    <input type="email" id="customer_email" name="customer_email" class="form-control"
                           value="<?php echo isset($_POST['customer_email']) ? htmlspecialchars($_POST['customer_email']) : $order['customer_email']; ?>">
                </div>

                <div class="form-group">
                    <label for="customer_address"><i class="fas fa-map-marker-alt"></i> Delivery Address</label>
                    <textarea id="customer_address" name="customer_address" class="form-control"><?php 
                        echo isset($_POST['customer_address']) ? htmlspecialchars($_POST['customer_address']) : $order['customer_address']; 
                    ?></textarea>
                </div>

                <!-- Order Status -->
                <div class="form-group">
                    <label for="status"><i class="fas fa-info-circle"></i> Order Status</label>
                    <select id="status" name="status" class="form-control" required>
                        <option value="Processing" <?php echo ((isset($_POST['status']) && $_POST['status'] == 'Processing') || $order['status'] == 'Processing') ? 'selected' : ''; ?>>Processing</option>
                        <option value="On Delivery" <?php echo ((isset($_POST['status']) && $_POST['status'] == 'On Delivery') || $order['status'] == 'On Delivery') ? 'selected' : ''; ?>>On Delivery</option>
                        <option value="Delivered" <?php echo ((isset($_POST['status']) && $_POST['status'] == 'Delivered') || $order['status'] == 'Delivered') ? 'selected' : ''; ?>>Delivered</option>
                        <option value="Cancelled" <?php echo ((isset($_POST['status']) && $_POST['status'] == 'Cancelled') || $order['status'] == 'Cancelled') ? 'selected' : ''; ?>>Cancelled</option>
                    </select>
                </div>

                <!-- Form Actions -->
                <div class="form-actions">
                    <a href="order.php" class="btn btn-outline">
                        <i class="fas fa-arrow-left"></i> Cancel
                    </a>
                    <button type="submit" name="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update Order
                    </button>
                </div>
            </form>
        </div>
    </div>
    <?php include 'footer.php'; ?>
</body>
</html>
<?php $conn->close(); ?>