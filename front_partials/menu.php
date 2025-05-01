    <!-- Navbar Section Starts Here -->
    <?php
    session_start();
    include './config/constants.php';
    ?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>MOCK-FOOD | Delicious Food</title>
        <link rel="icon" href="https://media.istockphoto.com/id/2203688325/video/chef-in-cook-hat-and-baker-cook-fork-spoon-and-leaves-graphic-animation-colored-transparent.avif?s=640x640&k=20&c=SsywiUO3TYpYcoz1gez4GV3FpcnU3ibH9pbDD8wgBvI=" type="image/x-icon">
        <link rel="stylesheet" href="../css/style.css">
    </head>

    <body>
        <section class="navbar">
            <div class="container">
                <div class="logo">
                    <a href="#" title="Logo">
                        <img src="images/logo.png" alt="Restaurant Logo" class="img-responsive">
                    </a>
                </div>

                <div class="menu text-right">
                    <ul>
                        <li>
                            <a href="home.php">Home</a>
                        </li>
                        <li>
                            <a href="categories.php">Categories</a>
                        </li>
                        <li>
                            <a href="foods.php">Foods</a>
                        </li>
                        <li>
                            <a href="#">Contact</a>
                        </li>
                        <li class="admin_redirect_btn">
                            <a href="<?php echo SITEURL ?>/dashboard.php">Admin Panel</a>
                        </li>
                    </ul>
                </div>

                <div class="clearfix"></div>
            </div>
        </section>
    </body>

    </html>