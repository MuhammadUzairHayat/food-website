<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Category Management</title>
    <link rel="stylesheet" href="../css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<style>
    .success-icon {
        color: var(--success-color);
    }

    .error-icon {
        color: var(--danger-color);
    }
</style>

<body>
    <?php
    include 'menu.php';
    if (isset($_SESSION['username'])) {
        if (isset($_SESSION['success'])) {
            echo "<div class='success-alert'><i class='fas fa-check-circle'></i> Admin '{$_SESSION['username']}' added successfully!</div>";
            unset($_SESSION['admin_added']);
            unset($_SESSION['username']);
        } elseif (isset($_SESSION['error'])) {
            echo "<div class='error-alert'><i class='fas fa-exclamation-circle'></i> {$_SESSION['error']}</div>";
            unset($_SESSION['error']);
        }
    }
    ?>
    <div class="content">
        <h1>Category Management</h1>
        <p>Manage your food categories from this panel.</p>

        <a class="add-admin-btn" href="add-category.php"><i class="fas fa-plus"></i> &nbsp; Add Category</a>

        <div class="table-container">
            <table class="admin-table">
                <tr>
                    <th>ID</th>
                    <th>Image</th>
                    <th>Title</th>
                    <th>Featured</th>
                    <th>Active</th>
                    <th>Actions</th>
                </tr>
                <?php
                // Query to fetch all admins
                $sql = "SELECT * FROM tbl_category";
                $result = mysqli_query($conn, $sql);

                if ($result === false) {
                    die("Database error: " . mysqli_error($conn));
                }

                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {

                ?>
                        <tr>
                            <td>#CAT-<?php echo $row['id'] ?></td>
                            <td><img class="avatar" src="<?php echo $row['image_name'] ?>" alt="user_avatar"></img></td>
                            <td><?php echo $row['title'] ?></td>
                            <td class="success-icon">
                                <i class="fas fa-<?php echo $row['feature'] ? 'check-circle' : 'times-circle error-icon'; ?>"></i>
                            </td>
                            <td class="success-icon">
                                <i class="fas fa-<?php echo $row['active'] ? 'check-circle success-icon' : 'times-circle error-icon'; ?>"></i>
                            </td>
                            <td>
                                <a class='bg-update-btn' href='<?php echo SITEURL ?>/update-category.php?id=<?php echo $row['id'] ?>'><i class='fas fa-edit'></i> Update</a>
                                <a class='bg-delete-btn' href='<?php echo SITEURL ?>/delete-category.php?id=<?php echo $row['id'] ?>'><i class='fas fa-trash-alt'></i> Delete</a>
                            </td>
                        </tr>
                <?php
                    }
                } else {
                    echo "<tr><td colspan='6' style='text-align: center; padding: 3rem; font-size: 1rem; color: var(--primary-color);'>
                            <div style='display: flex; flex-direction: column; align-items: center;'>
                                <img style='border-radius: 20px; width: 100%; max-width: 200px; max-height: 200px; object-fit: cover; margin-bottom: 1rem;' src='https://media.istockphoto.com/id/1437459385/vector/business-person-researching-with-magnifying-glass.jpg?s=612x612&w=0&k=20&c=Hxkn3cV-qTZ8SV-TmYqcgxm8T6BGKYaQu-O65hqkUzU=' alt='Not Found img'>
                                no category found.
                            </div>
                          </td></tr>";
                }
                ?>
            </table>
        </div>
    </div>
    <?php include 'footer.php'; ?>
</body>

</html>