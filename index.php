<?php
session_start();
if (isset($_SESSION['username'])) {
    $username = $_SESSION['username'];
    $role = $_SESSION['role'];
} else {
    $username = "";
    $role = "";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Trang chủ</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="container">
        <h2>Trang chủ</h2>
        <?php if ($username) : ?>
        <p>Xin chào, <?php echo $username; ?>!</p>
        <?php if ($role === 'admin') : ?>
        <a href="quantri.php">Trang quản trị</a>
        <?php endif; ?>
        <a href="logout.php">Đăng xuất</a>
        <?php else : ?>
        <a href="login.php">Đăng nhập</a>
        <?php endif; ?>

        <!-- Danh sách sản phẩm -->
        <h3>Danh sách sản phẩm</h3>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tên sản phẩm</th>
                    <th>Mô tả</th>
                    <th>Giá</th>
                    <th>Ảnh</th>
                </tr>
            </thead>
            <tbody>
                <?php
                require_once "includes/db.php";
                $products = $pdo->query("SELECT * FROM products")->fetchAll(PDO::FETCH_ASSOC);
                foreach ($products as $product) :
                ?>
                <tr>
                    <td><?php echo $product['id']; ?></td>
                    <td><?php echo $product['name']; ?></td>
                    <td><?php echo $product['description']; ?></td>
                    <td><?php echo $product['price']; ?></td>
                    <td><img src="<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>"></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>

</html>