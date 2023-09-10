<?php
session_start();
if (isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Đăng nhập</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="container">
        <h2>Đăng nhập</h2>
        <form action="login_process.php" method="POST">
            <label for="username">Tài khoản:</label>
            <input type="text" name="username" required><br>
            <label for="password">Mật khẩu:</label>
            <input type="password" name="password" required><br>
            <button type="submit">Đăng nhập</button>
        </form>
    </div>
</body>

</html>