<?php
include '../config.php';
session_start();

// Biến lưu trữ thông báo lỗi
$errors = [
    'email' => '',
    'password' => ''
];

// Xử lý khi form được submit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Lấy dữ liệu từ form
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Kiểm tra ràng buộc
    if (empty($email)) {
        $errors['email'] = 'Vui lòng nhập email!';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Vui lòng nhập đúng định dạng của email!';
    }

    if (empty($password)) {
        $errors['password'] = 'Vui lòng nhập mật khẩu!';
    }

    // Nếu không có lỗi, tiến hành kiểm tra thông tin đăng nhập
    if (empty($errors['email']) && empty($errors['password'])) {
        $email = mysqli_real_escape_string($conn, $email);
        $password = mysqli_real_escape_string($conn, md5($password));

        // Kiểm tra thông tin đăng nhập
        $select_username = mysqli_query($conn, "SELECT * FROM users WHERE email = '$email' AND password = '$password'");

        if (mysqli_num_rows($select_username) > 0) {
            $result = mysqli_fetch_assoc($select_username);

            // Xử lý đăng nhập theo vai trò
            if ($result['role_id'] == 1) {
                $_SESSION['admin_name'] = $result['user_name'];
                $_SESSION['admin_id'] = $result['user_id'];
                echo "<script>alert('Đăng nhập thành công'); window.location.href='../admin/index.php';</script>";
            } elseif ($result['role_id'] == 2) {
                $_SESSION['employee_name'] = $result['user_name'];
                $_SESSION['employee_id'] = $result['user_id'];
                echo "<script>alert('Đăng nhập thành công'); window.location.href='../employee/index.php';</script>";
            } else {
                $_SESSION['user_name'] = $result['user_name'];
                $_SESSION['user_id'] = $result['user_id'];
                echo "<script>alert('Đăng nhập thành công'); window.location.href='../index.php';</script>";
            }
        } else {
            // Email hoặc mật khẩu không đúng
            $errors['password'] = 'Email hoặc mật khẩu không đúng.';
        }
    }
}
?>


<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký tài khoản</title>
    <link rel="stylesheet" href="./css/signin.css">
</head>

<body>
    <div class="background">
        <div class="form-signin">
            <h3>Đăng nhập tài khoản</h3>
            <form method="post">

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" placeholder="Nhập email của bạn"
                        value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                    <div class="error"><?= $errors['email'] ?></div>
                </div>
                <div class="form-group">
                    <label for="password">Mật khẩu</label>
                    <input type="password" id="password" name="password" placeholder="Nhập mật khẩu">
                    <div class="error"><?= $errors['password'] ?></div>
                </div>
                <div class="form-group">
                    <a href="./forgot_password.php" style="text-decoration: none; font-size: 15px; color: orangered">Quên mật khẩu?</a>
                </div>

                <button type="submit" name="submit">Đăng nhập</button>
                <div class="link-login" style="margin-bottom: 10px">
                    <span>Bạn chưa có tài khoản? <a href="./signin.php">Đăng ký ngay</a></span>
                </div>
                    <a href="../index.php" style="color: orangered; text-decoration: none;">Quay về trang chủ</a>
            </form>
        </div>
    </div>
</body>

</html>