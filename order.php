<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Food - Restaurant</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body>
<?php
include('./front_partials/menu.php');

// Initialize variables
$food = null;
$food_id = 0;

// Check if form is submitted (POST request)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data and sanitize
    $food_id = isset($_POST['food_id']) ? intval($_POST['food_id']) : 0;
    $quantity = isset($_POST['qty']) ? intval($_POST['qty']) : 1;
    $customer_name = mysqli_real_escape_string($conn, $_POST['full-name']);
    $customer_contact = mysqli_real_escape_string($conn, $_POST['contact']);
    $customer_email = isset($_POST['email']) ? mysqli_real_escape_string($conn, $_POST['email']) : '';
    $customer_address = mysqli_real_escape_string($conn, $_POST['address']);
    $payment_method = mysqli_real_escape_string($conn, $_POST['payment']);
    $status = 'processing'; // Default status

    // Validate required fields
    if (empty($customer_name) || empty($customer_contact) || empty($customer_address)) {
        $_SESSION['error'] = "Please fill all required fields";
        header("Location: order.php?id=$food_id");
        exit;
    }

    // Get food details
    $food_query = "SELECT id, title, price FROM tbl_food WHERE id = $food_id";
    $food_result = mysqli_query($conn, $food_query);
    
    if (!$food_result) {
        $_SESSION['error'] = "Database error: " . mysqli_error($conn);
        header("Location: order.php?id=$food_id");
        exit;
    }
    
    $food = mysqli_fetch_assoc($food_result);

    if (!$food) {
        $_SESSION['error'] = "Selected food not found";
        header("Location: home.php");
        exit;
    }

    // Calculate total
    $price = $food['price'];
    $total = $price * $quantity;

    // Current date and time
    $order_date = date('Y-m-d H:i:s');

    // Prepare and execute insert query
    $sql = "INSERT INTO tbl_order (
        food, 
        price, 
        qty, 
        total, 
        order_date, 
        status, 
        customer_name, 
        customer_contact, 
        customer_email, 
        customer_address,
        payment_method
    ) VALUES (
        '$food[id]',
        $price,
        $quantity,
        $total,
        '$order_date',
        '$status',
        '$customer_name',
        '$customer_contact',
        '$customer_email',
        '$customer_address',
        '$payment_method'
    )";

    $result = mysqli_query($conn, $sql);

    if ($result) {
        // Get the last inserted order ID
        $order_id = mysqli_insert_id($conn);

        // Success - redirect to confirmation page
        $_SESSION['success'] = "Order placed successfully!";
        header("Location: order-confirmation.php?id=$order_id");
        exit;
    } else {
        // Error handling
        $_SESSION['error'] = "Failed to place order. Error: " . mysqli_error($conn);
        header("Location: order.php?id=$food_id");
        exit;
    }
} 
// GET request handling (initial page load)
else {
    $food_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

    if ($food_id <= 0) {
        $_SESSION['error'] = "Invalid food selection";
        header("Location: home.php");
        exit;
    }

    // Get food details to display on the page
    $food_query = "SELECT * FROM tbl_food WHERE id = $food_id";
    $food_result = mysqli_query($conn, $food_query);
    
    if (!$food_result) {
        $_SESSION['error'] = "Database error: " . mysqli_error($conn);
        header("Location: home.php");
        exit;
    }
    
    $food = mysqli_fetch_assoc($food_result);

    if (!$food) {
        $_SESSION['error'] = "Selected food not found";
        header("Location: home.php");
        exit;
    }
}

// Display error message if exists
if (isset($_SESSION['error'])) {
    echo '<div class="error-message">' . $_SESSION['error'] . '</div>';
    unset($_SESSION['error']);
}
?>
    <!-- Remaining HTML below (unchanged except for the extra character removed) -->

    <!-- Order Section -->
    <section class="food-search">
        <div class="container">
            <?php
            if (isset($_SESSION['error'])) {
                echo "<div class='alert error-alert'><i class='fas fa-exclamation-circle'></i> {$_SESSION['error']}</div>";
                unset($_SESSION['error']);
            }

            if (isset($_SESSION['success'])) {
                echo "<div class='alert success-alert'><i class='fas fa-check-circle'></i> {$_SESSION['success']}</div>";
                unset($_SESSION['success']);
            }
            ?>
            <h2 class="text-center text-white animate">Complete Your Order</h2>

            <form action="order.php" method="POST" class="order animate delay-1">
                <?php echo $food['title']; ?>
                <input type="hidden" name="food_id" value="<?php echo $food['id']; ?>">
                <fieldset>
                    <legend><i class="fas fa-utensils"></i> Selected Food</legend>

                    <div class="order-item">
                        <div class="food-menu-img">
                            <img src="<?php echo $food['image_name'] ?>" alt="Chicke Hawain Pizza" class="img-responsive img-curve">
                        </div>

                        <div class="food-menu-desc">
                            <h3 class="food-title"><?php echo $food['title'] ?></h3>
                            <p class="food-price"><?php echo $food['price'] ?> PKR</p>


                            <div class="food-totaling-div">
                                <div>
                                    <p class="quantity-label">Quantity</p>
                                    <div class="quantity-selector">
                                        <button type="button" class="quantity-btn minus"><i class="fas fa-minus"></i></button>
                                        <input type="number" name="qty" class="input-responsive quantity-input" value="1" min="1" required>
                                        <button type="button" class="quantity-btn plus"><i class="fas fa-plus"></i></button>
                                    </div>
                                </div>ٖ
                                <div class="food-total-price">
                                    <p>Total:</p>
                                    <p><span id="total-price"><?php echo $food['price'] ?></span> PKR</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </fieldset>

                <fieldset class="delivery-details-container animate delay-2">
                    <legend><i class="fas fa-truck"></i> Delivery Details</legend>
                    <div class="form-group">
                        <label class="order-label">Full Name</label>
                        <input type="text" name="full-name" placeholder="E.g. Muhammad Uzair" class="input-responsive" required>
                    </div>

                    <div class="form-group">
                        <label class="order-label">Phone Number</label>
                        <input type="tel" name="contact" placeholder="E.g. 9843xxxxxx" class="input-responsive" required>
                    </div>

                    <div class="form-group">
                        <label class="order-label">Email</label>
                        <input type="email" name="email" placeholder="E.g. hi@muhammaduzair.com  (Optional)" class="input-responsive">
                    </div>

                    <div class="form-group">
                        <label class="order-label">Address</label>
                        <textarea name="address" rows="5" placeholder="E.g. Street, City, Country" class="input-responsive" required></textarea>
                    </div>

                    <div class="form-group">
                        <label class="order-label">Payment Method</label>
                        <div class="payment-methods">
                            <label style="margin: 0" class="payment-option">
                                <input type="radio" name="payment" value="cash" checked>
                                <div class="payment-card">
                                    <i class="fas fa-money-bill-wave"></i>
                                    <span>Cash on Delivery</span>
                                </div>
                            </label>
                            <label style="margin: 0" class="payment-option">
                                <input type="radio" name="payment" value="card">
                                <div class="payment-card">
                                    <i class="far fa-credit-card"></i>
                                    <span>Credit Card</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    <button type="submit" name="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-shopping-bag"></i> Confirm Order
                    </button>
                </fieldset>
            </form>
        </div>
    </section>

    <?php include './front_partials/footer.php' ?>

    <script>
        // Quantity selector
        document.querySelectorAll('.quantity-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const input = this.parentElement.querySelector('.quantity-input');
                if (this.classList.contains('minus')) {
                    if (input.value > 1) {
                        input.value--;
                        document.getElementById('total-price').textContent = input.value * <?php echo (int) $food['price']; ?>;
                    };
                } else {
                    input.value++;
                    document.getElementById('total-price').textContent = input.value * <?php echo (int) $food['price']; ?>;
                }
            });
        });

        // Form validation
        document.querySelector('form').addEventListener('submit', function(e) {
            const inputs = this.querySelectorAll('input[required], textarea[required]');
            let isValid = true;

            inputs.forEach(input => {
                if (!input.value.trim()) {
                    input.style.borderColor = 'red';
                    isValid = false;
                } else {
                    input.style.borderColor = '#ddd';
                }
            });

            if (!isValid) {
                e.preventDefault();
                alert('Please fill all required fields');
            }
        });
    </script>
</body>

</html>