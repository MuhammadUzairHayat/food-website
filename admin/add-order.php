<?php
include './menu.php';

// Database connection
$conn = new mysqli('localhost', 'root', '', 'mock-food');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch active food items for dropdown
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
    // Validate and sanitize inputs
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

    // If no errors, proceed with database operation
    if (empty($errors)) {
        $insert_sql = "INSERT INTO tbl_order (
                      food, price, qty, total, order_date, status,
                      customer_name, customer_contact, customer_email, customer_address
                      ) VALUES (?, ?, ?, ?, NOW(), ?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($insert_sql);
        
        if ($stmt === false) {
            $errors[] = "Database error: " . $conn->error;
        } else {
            $stmt->bind_param("idiisssss", 
                $food_id, $food_price, $qty, $total, $status,
                $customer_name, $customer_contact, $customer_email, $customer_address
            );
            
            if ($stmt->execute()) {
                $_SESSION['success'] = "Order added successfully!";
                header("Location: order.php");
                exit();
            } else {
                $errors[] = "Error adding order: " . $stmt->error;
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
    <title>Add Order | Restaurant Management System</title>
    <link rel="stylesheet" href="../css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
      
    </style>
</head>
<body>
    <div class="admin-container">
        <div class="form-header">
            <h2><i class="fas fa-plus-circle" style="color: var(--primary);"></i> Create New Order</h2>
            <p>Fill in the details below to add a new customer order to the system</p>
        </div>

        <?php if (!empty($errors)): ?>
            <div class="error-message">
                <i class="fas fa-exclamation-circle"></i>
                <div class="error-content">
                    <p><strong>There were errors with your submission:</strong></p>
                    <?php foreach ($errors as $error): ?>
                        <p><?php echo htmlspecialchars($error); ?></p>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <div class="form-card">
            <form method="POST" id="orderForm">
                <!-- Food Selection -->
                <div class="form-group">
                    <label for="food_id">Food Item</label>
                    <select id="food_id" name="food_id" class="form-control" required>
                        <option value="">Select a food item</option>
                        <?php foreach ($foods as $food): ?>
                            <option value="<?php echo $food['id']; ?>"
                                data-price="<?php echo $food['price']; ?>"
                                <?php echo (isset($_POST['food_id']) && $_POST['food_id'] == $food['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($food['title']); ?> - $<?php echo number_format($food['price'], 2); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Quantity -->
                <div class="form-group">
                    <label for="qty">Quantity</label>
                    <input type="number" id="qty" name="qty" min="1" class="form-control" 
                           value="<?php echo isset($_POST['qty']) ? htmlspecialchars($_POST['qty']) : '1'; ?>" required>
                </div>

                <!-- Price Preview (dynamic) -->
                <div class="price-preview" id="pricePreview" style="display: none;">
                    <span>Order Total:</span>
                    <strong id="totalPrice">$0.00</strong>
                </div>

                <!-- Customer Information Section -->
                <div style="margin: 2.5rem 0 1.5rem;">
                    <h3 style="font-size: 1.1rem; color: var(--darker); font-weight: 600; border-bottom: 1px solid var(--border); padding-bottom: 0.75rem;">
                        <i class="fas fa-user-circle" style="margin-right: 0.5rem; color: var(--primary);"></i>
                        Customer Information
                    </h3>
                </div>

                <div class="form-group input-icon">
                    <i class="fas fa-user"></i>
                    <input type="text" id="customer_name" name="customer_name" class="form-control" placeholder="Full Name"
                           value="<?php echo isset($_POST['customer_name']) ? htmlspecialchars($_POST['customer_name']) : ''; ?>" required>
                </div>

                <div class="form-group input-icon">
                    <i class="fas fa-phone-alt"></i>
                    <input type="text" id="customer_contact" name="customer_contact" class="form-control" placeholder="Phone Number"
                           value="<?php echo isset($_POST['customer_contact']) ? htmlspecialchars($_POST['customer_contact']) : ''; ?>" required>
                </div>

                <div class="form-group input-icon">
                    <i class="fas fa-envelope"></i>
                    <input type="email" id="customer_email" name="customer_email" class="form-control" placeholder="Email Address (Optional)"
                           value="<?php echo isset($_POST['customer_email']) ? htmlspecialchars($_POST['customer_email']) : ''; ?>">
                </div>

                <div class="form-group input-icon">
                    <i class="fas fa-map-marker-alt"></i>
                    <textarea id="customer_address" name="customer_address" class="form-control" placeholder="Delivery Address"><?php 
                        echo isset($_POST['customer_address']) ? htmlspecialchars($_POST['customer_address']) : ''; 
                    ?></textarea>
                </div>

                <!-- Order Status -->
                <div class="form-group">
                    <label for="status">Order Status</label>
                    <select id="status" name="status" class="form-control" required>
                        <option value="Processing" <?php echo (isset($_POST['status']) && $_POST['status'] == 'Processing') ? 'selected' : ''; ?>>Processing</option>
                        <option value="On Delivery" <?php echo (isset($_POST['status']) && $_POST['status'] == 'On Delivery') ? 'selected' : ''; ?>>On Delivery</option>
                        <option value="Delivered" <?php echo (isset($_POST['status']) && $_POST['status'] == 'Delivered') ? 'selected' : ''; ?>>Delivered</option>
                        <option value="Cancelled" <?php echo (isset($_POST['status']) && $_POST['status'] == 'Cancelled') ? 'selected' : ''; ?>>Cancelled</option>
                    </select>
                </div>

                <!-- Form Actions -->
                <div class="form-actions">
                    <a href="order.php" class="btn btn-outline">
                        <i class="fas fa-arrow-left"></i> Back to Orders
                    </a>
                    <button type="submit" name="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Create Order
                    </button>
                </div>
            </form>
        </div>
    </div>
    <?php include 'footer.php'; ?>

    <script>
        // Dynamic price calculation
        document.addEventListener('DOMContentLoaded', function() {
            const foodSelect = document.getElementById('food_id');
            const qtyInput = document.getElementById('qty');
            const pricePreview = document.getElementById('pricePreview');
            const totalPrice = document.getElementById('totalPrice');
            
            function calculateTotal() {
                const selectedOption = foodSelect.options[foodSelect.selectedIndex];
                if (selectedOption.value === "") {
                    pricePreview.style.display = 'none';
                    return;
                }
                
                const price = parseFloat(selectedOption.dataset.price);
                const quantity = parseInt(qtyInput.value) || 0;
                const total = price * quantity;
                
                totalPrice.textContent = '$' + total.toFixed(2);
                pricePreview.style.display = 'flex';
            }
            
            foodSelect.addEventListener('change', calculateTotal);
            qtyInput.addEventListener('input', calculateTotal);
            
            // Calculate on page load if food is already selected
            if (foodSelect.value !== "") {
                calculateTotal();
            }
        });
    </script>
</body>
</html>
<?php $conn->close(); ?>