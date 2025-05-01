    <?php


    define("SITEURL", "http://localhost/mock-food/admin");
    define("FRONT_SITEURL", "http://localhost/mock-food");
    define("LOCALHOST", "localhost");
    define("DB_USERNAME", "root");
    define("DB_PASSWORD", "");
    define("DB_NAME", "mock-food");


    $conn = new mysqli(LOCALHOST, DB_USERNAME, DB_PASSWORD, DB_NAME);

    if ($conn->connect_error) {
        die("❌ Connection failed: " . $conn->connect_error . "<br>");
    } else {
        // echo "✅ Connected successfully<br>";
    }

    ?>
    </div>