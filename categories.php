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

    <?php include './front_partials/menu.php';
    function getCategoryResult($conn, $table)
    {
        $sql = "SELECT * FROM $table";
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

    $category_result = getCategoryResult($conn, 'tbl_category');

    ?>
    <!-- CAtegories Section Starts Here -->
    <section class="categories">
        <div class="container">
            <h2 class="text-center">Explore Foods</h2>
            <div class="category-grid">
                <?php if ($category_result) {
                    while ($category = mysqli_fetch_assoc($category_result)) { ?>
                        <a href="category-foods.php?id=<?php echo $category['id'] ?>">
                            <div class="box-3 float-container">
                                <img src="<?php echo $category['image_name'] ?>" alt="Pizza" class="img-responsive card-img img-curve">

                                <h3 class="float-text text-white">Pizza</h3>
                            </div>
                        </a>

                <?php }
                } ?>
            </div>

            <div class="clearfix"></div>
        </div>
    </section>
    <!-- Categories Section Ends Here -->


    <!-- social Section Starts Here -->
    <section class="social">
        <div class="container text-center">
            <ul>
                <li>
                    <a href="#"><img title="img_title" src="https://img.icons8.com/fluent/50/000000/facebook-new.png" /></a>
                </li>
                <li>
                    <a href="#"><img title="img_title" src="https://img.icons8.com/fluent/48/000000/instagram-new.png" /></a>
                </li>
                <li>
                    <a href="#"><img title="img_title" src="https://img.icons8.com/fluent/48/000000/twitter.png" /></a>
                </li>
            </ul>
        </div>
    </section>
    <!-- social Section Ends Here -->

    <?php include './front_partials/footer.php' ?>


</body>

</html>