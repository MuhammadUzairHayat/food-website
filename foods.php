<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Food Menu - Restaurant Website</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body>
    <?php 
    // Include database connection
    include('./front_partials/menu.php');
    
    // Fetch food items from database
    $sql = "SELECT f.*
            FROM tbl_food f 
            LEFT JOIN tbl_category c ON f.category_id = c.id 
            WHERE f.active = '1'";
    $result = mysqli_query($conn, $sql);
    $count = mysqli_num_rows($result);
    ?>

    <!-- Food Search Section -->
    <section class="food-search text-center">
        <div class="container">
            <form action="food-search.php" method="GET">
                <input type="search" name="search" placeholder="Search for Food.." required>
                <input type="submit" name="submit" value="Search" class="btn btn-primary">
            </form>
        </div>
    </section>

    <!-- Food Menu Section -->
    <section class="food-menu">
        <div class="container">
            <h2 class="text-center">Food Menu</h2>

            <?php if($count > 0): ?>
                <div class="food-grid">
                    <?php while($row = mysqli_fetch_assoc($result)): ?>
                        <div class="food-menu-box">
                            <div class="food-menu-img">
                                <?php if(!empty($row['image_name'])): ?>
                                    <img src="<?php echo htmlspecialchars($row['image_name']); ?>" 
                                         alt="<?php echo htmlspecialchars($row['title']); ?>" 
                                         class="img-responsive img-curve">
                                <?php else: ?>
                                    <img src="images/default-food.jpg" 
                                         alt="Default food image" 
                                         class="img-responsive img-curve">
                                <?php endif; ?>
                            </div>

                            <div class="food-menu-desc">
                                <h4><?php echo htmlspecialchars($row['title']); ?></h4>
                                <p class="food-price">$<?php echo number_format($row['price'], 2); ?></p>
                                <p class="food-detail">
                                    <?php echo htmlspecialchars($row['description']); ?>
                                </p>
                                <?php if(!empty($row['category_title'])): ?>
                                    <p class="food-category">
                                        <i class="fas fa-tag"></i> <?php echo htmlspecialchars($row['category_title']); ?>
                                    </p>
                                <?php endif; ?>
                                <br>
                                <a href="order.php?id=<?php echo $row['id']; ?>" class="btn btn-primary">Order Now</a>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="text-center" style="padding: 50px 0;">
                    <h3>No food items available at the moment.</h3>
                </div>
            <?php endif; ?>

            <div class="clearfix"></div>
        </div>
    </section>

    <!-- Social Media Section -->
    <section class="social">
        <div class="container text-center">
            <ul>
                <li>
                    <a href="#"><i class="fab fa-facebook-f"></i></a>
                </li>
                <li>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                </li>
                <li>
                    <a href="#"><i class="fab fa-twitter"></i></a>
                </li>
            </ul>
        </div>
    </section>

    <?php include './front_partials/footer.php'; ?>

    <!-- Back to Top Button -->
    <a href="#" class="back-to-top" id="backToTop">
        <i class="fas fa-arrow-up"></i>
    </a>

    <script>
        // Back to top button
        window.addEventListener('scroll', function() {
            const backToTop = document.getElementById('backToTop');
            if (window.scrollY > 300) {
                backToTop.classList.add('active');
            } else {
                backToTop.classList.remove('active');
            }
        });
        
        // Smooth scroll for back to top
        document.getElementById('backToTop').addEventListener('click', function(e) {
            e.preventDefault();
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    </script>
</body>
</html>