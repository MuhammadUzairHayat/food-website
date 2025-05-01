<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation - Restaurant</title>
    <link rel="stylesheet" href="./css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body>
    <?php
    include('./front_partials/menu.php');

    // Check if order ID exists
    $order_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

    if ($order_id <= 0) {
        header("Location: index.php");
        exit;
    }

    // Get order details
    $order_query = "SELECT * FROM tbl_order WHERE id = $order_id";
    $order_result = mysqli_query($conn, $order_query);
    $order = mysqli_fetch_assoc($order_result);

    // Get order details
    $food_query = "SELECT * FROM tbl_food WHERE id = {$order['food']}";
    $food_result = mysqli_query($conn, $food_query);
    $food = mysqli_fetch_assoc($food_result);


    if (!$order) {
        header("Location: index.php");
        exit;
    }
    ?>
    <!-- Navbar is included via menu.php -->

    <section class="food-search" style="padding: 4rem 0%;">
        <div class="container">
            <div class="order-confirmation">
                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert success-alert">
                        <i class="fas fa-check-circle"></i>
                        <h4>Order Confirmed!</h4>
                        <p>Thank you for your order. Here are your order details:</p>
                    </div>
                <?php
                    unset($_SESSION['success']);
                endif; ?>

                <div class="order-details">
                    <div class="order-details-header">
                        <img class="img-responsive card-img img-curve" src="<?php echo $food['image_name']; ?>" alt="">
                        <div>
                            <h3><?php echo $food['title']; ?></h3>
                            <p>Order #<?php echo $order['id']; ?></p>
                        </div>
                    </div>
                    <div class="flex flex-wrap justify-between">
                        <div class="order-food-details">
                            <h4>Order Details</h4>
                            <p><strong>Food:</strong> <?php echo htmlspecialchars($food['title']); ?></p>
                            <p><strong>Price:</strong> <?php echo htmlspecialchars($order['price']); ?></p>
                            <p><strong>Quantity:</strong> <?php echo $order['qty']; ?></p>
                            <p><strong>Total:</strong> <?php echo $order['total']; ?> PKR</p>
                            <p><strong>Payment:</strong> <?php echo $order['payment_method']; ?></p>
                        </div>
                        <div class="order-customer-details ">
                            <h4 class="order-details-title">Customer Details</h4>
                            <p><strong>Name:</strong> <?php echo htmlspecialchars($order['customer_name']); ?></p>
                            <p><strong>Email:</strong> <?php echo $order['customer_email'] ? $order['customer_email'] : "Not Given (optional)" ?></p>
                            <p><strong>Contact:</strong> <?php echo $order['customer_contact']; ?></p>
                            <p><strong>Delivery to:</strong> <?php echo htmlspecialchars($order['customer_address']); ?></p>
                        </div>
                    </div>

                    <a href="home.php" class="btn btn-primary">Back to Menu</a>
                </div>
            </div>
        </div>
    </section>

    <?php include('./front_partials/footer.php'); ?>
</body>

</html>