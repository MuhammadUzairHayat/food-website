<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <!-- Important to make website responsive -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restaurant Website</title>

    <!-- Link our CSS file -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <?php
    include './front_partials/menu.php';
    $id = $_GET['id'];
    if (!$id || $id <= 0) {
        $_SESSION['error'] = "ID not Available";
        header('Location: home.php');
        exit;
    }

    function getFoodsResult($id, $conn, $table)
    {
        $sql = "SELECT * FROM tbl_food WHERE category_id = $id";
        $stmt = mysqli_prepare($conn, $sql);
        if ($stmt) {
            mysqli_stmt_execute($stmt);
            $res = mysqli_stmt_get_result($stmt);
            if ($res) {
                return $res;
            } else {
                return false;
            }
            return false;
        }
    }

    $sql = "SELECT * FROM tbl_category WHERE id = $id";
    $category_query = mysqli_query($conn, $sql);
    $category = mysqli_fetch_assoc($category_query);

    $foods_result = getFoodsResult($id, $conn, 'tbl_food');

    ?>

    <!-- fOOD sEARCH Section Starts Here -->
    <section class="food-search text-center">
        <div class="container">

            <h2><span style="color: var(--secondary);" >Foods on</span> <a href="#" class="text-white">"<?php echo $category['title'] ?>"</a></h2>
            <p style="color: var(--success-light); max-width: 40rem; text-align: left">Our food is prepared with the finest ingredients and utmost care to ensure a delightful dining experience. Taste the perfection in every bite!</p>

        </div>
    </section>
    <!-- fOOD sEARCH Section Ends Here -->



    <!-- fOOD MEnu Section Starts Here -->
    <section class="food-menu">
        <div class="container">
            <h2 class="text-center">Food Menu</h2>

            <div class="food-grid">
            <?php if ($foods_result) {
                while ($food = mysqli_fetch_assoc($foods_result)) { ?>

                    <div class="food-menu-box">
                        <div class="food-menu-img">
                            <img src="<?php echo $food['image_name'] ?>" alt="Chicke Hawain Pizza" class="img-responsive img-curve">
                        </div>

                        <div class="food-menu-desc">
                            <h4><?php echo $food['title'] ?> </h4>
                            <p class="food-price"><?php echo $food['price'] ?> PKR</p>
                            <p class="food-detail">
                                <?php echo $food['description'] ?> </p>
                            <br>

                            <a href="order.php?id=<?php echo $food['id'] ?>" class="btn btn-primary">Order Now</a>
                        </div>
                    </div>

            <?php }
            } ?>
            </div>


            <div class="clearfix"></div>



        </div>

    </section>
    <!-- fOOD Menu Section Ends Here -->

    <!-- social Section Starts Here -->
    <section class="social">
        <div class="container text-center">
            <ul>
                <li>
                    <a href="#"><img src="https://img.icons8.com/fluent/50/000000/facebook-new.png" /></a>
                </li>
                <li>
                    <a href="#"><img src="https://img.icons8.com/fluent/48/000000/instagram-new.png" /></a>
                </li>
                <li>
                    <a href="#"><img src="https://img.icons8.com/fluent/48/000000/twitter.png" /></a>
                </li>
            </ul>
        </div>
    </section>
    <!-- social Section Ends Here -->

    <!-- footer Section Starts Here -->
    <?php include './front_partials/footer.php' ?>

    <!-- footer Section Ends Here -->

</body>

</html>