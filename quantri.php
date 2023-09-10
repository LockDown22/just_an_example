<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

require_once "includes/db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_product'])) {
    $name = $_POST['product_name'];
    $description = $_POST['product_description'];
    $price = $_POST['product_price'];

    // Xử lý tệp ảnh
    $image = $_FILES['product_image']['name'];
    $image_tmp = $_FILES['product_image']['tmp_name'];
    $image_path = "uploads/" . $image;

    move_uploaded_file($image_tmp, $image_path);

    $sql = "INSERT INTO products (name, description, price, image) VALUES (?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$name, $description, $price, $image_path]);
}

// Xử lý sửa sản phẩm
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit_product'])) {
    $product_id = $_POST['product_id'];
    $name = $_POST['product_name'];
    $description = $_POST['product_description'];
    $price = $_POST['product_price'];

    // Xử lý tệp ảnh
    if (!empty($_FILES['product_image']['name'])) {
        $image = $_FILES['product_image']['name'];
        $image_tmp = $_FILES['product_image']['tmp_name'];
        $image_path = "uploads/" . $image;

        move_uploaded_file($image_tmp, $image_path);
    } else {
        // Nếu không tải lên ảnh mới, giữ nguyên đường dẫn ảnh cũ
        $image_path = $_POST['product_image_path'];
    }

    $sql = "UPDATE products SET name = ?, description = ?, price = ?, image = ? WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$name, $description, $price, $image_path, $product_id]);
}

// Xử lý xóa sản phẩm
if (isset($_GET['delete_product'])) {
    $product_id = $_GET['delete_product'];

    $sql = "DELETE FROM products WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$product_id]);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_user'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    $sql = "INSERT INTO users (username, password, role) VALUES (?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$username, $password, $role]);
}

// Xử lý sửa người dùng
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit_user'])) {
    $user_id = $_POST['user_id'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    $sql = "UPDATE users SET username = ?, password = ?, role = ? WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$username, $password, $role, $user_id]);
}

// Xử lý xóa người dùng
if (isset($_GET['delete_user'])) {
    $user_id = $_GET['delete_user'];

    $sql = "DELETE FROM users WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$user_id]);
}

$products = $pdo->query("SELECT * FROM products")->fetchAll(PDO::FETCH_ASSOC);
$users = $pdo->query("SELECT * FROM users")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Trang quản trị</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <h2>Trang quản trị</h2>
    <a href="index.php">Trang chủ</a>
    <a href="logout.php">Đăng xuất</a>
    <div class="container">

        <!-- Form thêm sản phẩm -->
        <!-- Form thêm sản phẩm -->
        <h3>Thêm sản phẩm</h3>
        <form action="quantri.php" method="POST" enctype="multipart/form-data">
            <input type="text" name="product_name" placeholder="Tên sản phẩm" required>
            <textarea name="product_description" placeholder="Mô tả sản phẩm"></textarea>
            <input type="number" name="product_price" placeholder="Giá sản phẩm" step="0.01" required>
            <input type="file" name="product_image" accept="image/*" required>
            <button type="submit" name="add_product">Thêm</button>
        </form>

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
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $product) : ?>
                    <tr>
                        <td><?php echo $product['id']; ?></td>
                        <td><?php echo $product['name']; ?></td>
                        <td><?php echo $product['description']; ?></td>
                        <td><?php echo $product['price']; ?></td>
                        <td><img src="<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>"></td>
                        <td>
                            <form action="quantri.php" method="POST">
                                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                <input type="text" name="product_name" value="<?php echo $product['name']; ?>">
                                <textarea name="product_description"><?php echo $product['description']; ?></textarea>
                                <input type="number" name="product_price" value="<?php echo $product['price']; ?>" step="0.01">
                                <input type="file" name="product_image" accept="image/*">
                                <button type="submit" name="edit_product">Sửa</button>
                            </form>
                            <a href="quantri.php?delete_product=<?php echo $product['id']; ?>">Xóa</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

    </div>
    <div class="container">
        <h3>Thêm người dùng</h3>
        <form action="quantri.php" method="POST">
            <input type="text" name="username" placeholder="Tên người dùng" required>
            <input type="password" name="password" placeholder="Mật khẩu" required>
            <select name="role">
                <option value="user">User</option>
                <option value="admin">Admin</option>
            </select>
            <button type="submit" name="add_user">Thêm</button>
        </form>

        <!-- Danh sách người dùng -->
        <h3>Danh sách người dùng</h3>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tên người dùng</th>
                    <th>Loại người dùng</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user) : ?>
                    <tr>
                        <td><?php echo $user['id']; ?></td>
                        <td><?php echo $user['username']; ?></td>
                        <td><?php echo $user['role']; ?></td>
                        <td>
                            <form action="quantri.php" method="POST">
                                <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                <input type="text" name="username" value="<?php echo $user['username']; ?>">
                                <input type="password" name="password" value="<?php echo $user['password']; ?>">
                                <select name="role">
                                    <option value="user" <?php echo $user['role'] === 'user' ? 'selected' : ''; ?>>User
                                    </option>
                                    <option value="admin" <?php echo $user['role'] === 'admin' ? 'selected' : ''; ?>>Admin
                                    </option>
                                </select>
                                <button type="submit" name="edit_user">Sửa</button>
                            </form>
                            <a href="quantri.php?delete_user=<?php echo $user['id']; ?>">Xóa</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>

</html>