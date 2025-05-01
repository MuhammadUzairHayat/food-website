<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delicious Restaurant - Home</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>

<body>
    <?php include './front_partials/menu.php';

    if (isset($_SESSION['error'])) {
        echo "<div class='alert error-alert'><i class='fas fa-exclamation-circle'></i> {$_SESSION['error']}</div>";
        unset($_SESSION['error']);
    }

    if (isset($_SESSION['success'])) {
        echo "<div class='alert success-alert'><i class='fas fa-check-circle'></i> {$_SESSION['success']}</div>";
        unset($_SESSION['success']);
    }
    function getData($conn, $table)
    {
        $sql = "SELECT * FROM $table";
        $stmt = mysqli_prepare($conn, $sql);
        if ($stmt) {
            mysqli_stmt_execute($stmt);
            $res = mysqli_stmt_get_result($stmt);
            return $res;
        }
    }

    function getFoodImage($conn, $id)
    {
        $sql = "SELECT * FROM ";
        $stmt = mysqli_prepare($conn, $sql);
        if ($stmt) {
            mysqli_stmt_execute($stmt);
            $res = mysqli_stmt_get_result($stmt);
            if ($res) {
                return $res;
            } else {
                return false;
            }
        }
        return false;
    }

    $category_result = getData($conn, 'tbl_category'); // Ensure table name matches database schema
    $food_result = getData($conn, 'tbl_food');
    ?>

    <!-- Hero Section -->
    <section class="food-search text-center">
        <div class="container">
            <h1 class="hero-title animate">Delicious Food Delivered To You</h1>
            <p class="hero-subtitle animate delay-1">Order your favorite meals from our restaurant</p>

            <form action="food-search.html" method="POST" class="animate delay-2">
                <input type="search" name="search" placeholder="Search for Food.." required>
                <input type="submit" name="submit" value="Search" class="btn btn-primary">
            </form>
        </div>
    </section>

    <!-- Categories Section -->
    <section class="categories">
        <div class="container">
            <h2 class="text-center animate">Explore Foods</h2>

            <div class="category-grid">
                <?php while ($category = mysqli_fetch_assoc($category_result)) { ?>
                    <a class="box-3 animate" href="category-foods.php?id=<?php echo $category['id'] ?>"> <!-- Ensure 'id' matches the column name in the database -->
                        <div class="float-container">
                            <img src="<?php echo $category['image_name'] ?>" alt="Pizza" class="img-responsive card-img img-curve">
                            <h3 class="float-text text-white"><?php echo $category['title'] ?></h3>
                        </div>
                    </a>
                <?php } ?>

            </div>
        </div>
    </section>

    <!-- Popular Foods Section -->
    <section class="food-menu">
        <div class="container">
            <h2 class="text-center animate">Popular Menu</h2>

            <div class="food-grid">
                <?php $i = 0;
                while ($food = mysqli_fetch_assoc($food_result)): ?>
                    <div class="food-menu-box animate <?= 'delay-' . ($i++ % 3) ?>">
                        <div class="food-menu-img">
                            <img src="<?php echo $food['image_name'] ?>" alt="Food Item" class="img-responsive img-curve">
                        </div>
                        <div class="food-menu-desc">
                            <h4><?php echo $food['title'] ?></h4>
                            <p class="food-price"><?php echo $food['price'] ?> PKR</p>
                            <p class="food-detail">
                                <?php echo $food['description'] ?>
                            </p>
                            <br>
                            <a href="order.php?id=<?php echo $food['id'] ?>" class="btn btn-primary">Order Now</a>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>

            <p class="text-center animate delay-1 seeAll_btn">
                <a href="foods.php" class="btn btn-primary">See All Foods</a>
            </p>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features">
        <div class="container">
            <div class="feature-box animate">
                <i class="fas fa-shipping-fast"></i>
                <h3>Fast Delivery</h3>
                <p>Get your delicious food delivered in under 30 minutes</p>

            </div>
            <div class="feature-box animate delay-1">
                <i class="fas fa-utensils"></i>
                <h3>Fresh Food</h3>
                <p>Made with fresh and organic ingredients prepared daily</p>
            </div>
            <div class="feature-box animate delay-2">
                <i class="fas fa-tag"></i>
                <h3>Great Deals</h3>
                <p>Special offers and discounts in whole menu for you</p>
            </div>
        </div>
    </section>

    <?php include './front_partials/footer.php' ?>


</body>

</html>