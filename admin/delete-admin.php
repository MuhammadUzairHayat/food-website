<?php
include './menu.php';
$sql = "SELECT * from where id=echo SITEURL";

$current_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") .
    "://" . $_SERVER['HTTP_HOST'] .
    $_SERVER['REQUEST_URI'];

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);


// echo $current_url, '<br>';
// echo $id;

$sql = "SELECT * from tbl_admin where id=$id";
$res = mysqli_query($conn, $sql);

if ($res == false) {
    echo "Connection Error Occurred: In Deletion";
}

if (mysqli_num_rows($res) > 0) {
    // $row = mysqli_fetch_assoc($res);
    while ($row = mysqli_fetch_assoc($res)) {
        $id = $row['id'];
        $avatar = $row['avatar'];
        $username = $row['username']; // Access by column name
        $full_name = $row['full_name'];
    }
}
?>

<section style="display: flex; justify-content: center; margin-top: 5rem;">
    <div class="delete-container">
        <div class="delete-header">
            <h2>Confirm Deletion</h2>
            <img src="<?php echo $avatar?>" alt="">
             <div>
                <h4><?php echo $full_name ?></h4>
                <p><?php echo $username ?></p>
             </div>
        </div>
        <div class="delete-body">
            <div class="delete-icon">
                <i class="fas fa-exclamation-triangle"></i>
                <p class="delete-message" style="font-size: 16px;">
                    This action cannot be undone.
                </p>
            </div>
            <div class="delete-details">
                <p><strong>ID:</strong> <?php echo $id; ?></p>
                <p><strong>Username:</strong> <?php echo $username; ?></p>
                <p><strong>Full Name:</strong> <?php echo $full_name; ?></p>
            </div>
            <div class="delete-actions">
                <a href="<?php echo SITEURL?>/dashboard.php" class="btn btn-secondary" onclick="cancelDelete()">
                    Cancel
                </a>
                <a class="btn btn-danger" href="execute-delete.php?id=<?php echo $id; ?>">
                    Delete
                </a>
            </div>
        </div>
    </div>
</section>
