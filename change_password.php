<?php
include './config.php';
session_start();
error_reporting(E_ALL & ~E_NOTICE);

$errors = [
    'old_password' => '',
    'new_password' => '',
    'confirm_password' => ''
];
// Lấy thông tin người dùng
$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];

// Lấy dữ liệu người dùng từ cơ sở dữ liệu
$select_user = mysqli_query($conn, "SELECT * FROM users WHERE user_id = $user_id");
$fetch_user = mysqli_fetch_assoc($select_user);

if (isset($_POST['submit'])) {
    $old_password = trim($_POST['old_password']);
    $new_password = trim($_POST['new_password']);
    $confirm_password = trim($_POST['confirm_password']);

    // Kiểm tra mật khẩu cũ
    if (empty($old_password)) {
        $errors['old_password'] = "Vui lòng nhập mật khẩu cũ!";
    } else {
        // Kiểm tra mật khẩu cũ có khớp với dữ liệu không
        if (md5($old_password) !== $fetch_user['password']) {
            $errors['old_password'] = "Mật khẩu cũ không chính xác!";
        }

    }

    // Kiểm tra mật khẩu mới
    if (empty($new_password)) {
        $errors['new_password'] = "Vui lòng nhập mật khẩu mới!";
    } elseif (strlen($new_password) < 6) {
        $errors['new_password'] = "Mật khẩu mới phải có ít nhất 6 ký tự!";
    }

    // Kiểm tra xác nhận mật khẩu mới
    if (empty($confirm_password)) {
        $errors['confirm_password'] = "Vui lòng xác nhận mật khẩu mới!";
    } elseif ($new_password !== $confirm_password) {
        $errors['confirm_password'] = "Xác nhận mật khẩu mới không khớp!";
    }

    // Nếu không có lỗi, cập nhật mật khẩu
    if (empty($errors['old_password']) && empty($errors['new_password']) && empty($errors['confirm_password'])) {
        $new_password_hashed = md5($new_password);
        $update_password = mysqli_query($conn, "UPDATE users SET password = '$new_password_hashed' WHERE user_id = $user_id");
        
        if ($update_password) {
            // Hiển thị thông báo trước khi chuyển hướng
            echo "<script>alert('Đổi mật khẩu thành công!');</script>";

            // Điều hướng về trang đăng nhập sau khi hiển thị thông báo
            header("Location: ./index.php");
            exit; // Dừng script để tránh thực thi thêm mã không cần thiết
        } else {
            echo "<script>alert('Có lỗi xảy ra. Vui lòng thử lại!');</script>";
        }
    }
}

?>

<html lang="vi">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đổi mật khẩu</title>
    <link rel="stylesheet" type="text/css" href="./css/main_new.css">
    <link rel="stylesheet" href="./css/change_password.css">
    <link rel="stylesheet" href="./css/header.css">
    <link rel="stylesheet" href="./icon/fontawesome-free-6.6.0-web/css/all.min.css">
    <link rel="stylesheet" href="./icon/fontawesome-free-6.6.0-web/css/brands.min.css">
    <link rel="stylesheet" href="./icon/fontawesome-free-6.6.0-web/css/fontawesome.min.css">
</head>

<body>

    <?php include 'header.php'; ?>

    <div class="change-password-container">
        <h3>Đổi mật khẩu</h3>
        <form method="POST" action="">
            <div class="form-group">
                <label for="user_name">Tên đăng nhập</label>
                <input type="text" id="user_name" name="user_name" value="<?php echo $user_name; ?>" disabled>
            </div>
            <div class="form-group">
                <label for="old_password">Mật khẩu cũ</label>
                <input type="password" id="old_password" name="old_password">
                <div class="error"><?= $errors['old_password'] ?></div>
            </div>
            <div class="form-group">
                <label for="new_password">Mật khẩu mới</label>
                <input type="password" id="new_password" name="new_password">
                <div class="error"><?= $errors['new_password'] ?></div>
            </div>
            <div class="form-group">
                <label for="confirm_password">Xác nhận mật khẩu mới</label>
                <input type="password" id="confirm_password" name="confirm_password">
                <div class="error"><?= $errors['confirm_password'] ?></div>
            </div>
            <button type="submit" class="btn-submit" name="submit">Đổi mật khẩu</button>
        </form>
    </div>

    <?php include 'footer.php'; ?>

</body>

</html>