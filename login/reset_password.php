<?php
include '../config.php';

$errors = [
    'password' => ''
];

// Kiểm tra email được truyền qua liên kết
if (isset($_GET['email'])) {
    $decoded_email = base64_decode(urldecode($_GET['email'])); // Giải mã email

    // Kiểm tra email tồn tại trong cơ sở dữ liệu
    $check_email = mysqli_query($conn, "SELECT * FROM users WHERE email = '$decoded_email'") or die('Query failed');
    if (mysqli_num_rows($check_email) > 0) {
        // Xử lý form khi bấm "Cập nhật"
        if (isset($_POST['submit'])) {
            $password = $_POST['password'] ?? '';

            // Kiểm tra mật khẩu rỗng
            if (empty($password)) {
                $errors['password'] = 'Vui lòng nhập mật khẩu mới!';
            } 
            // Kiểm tra mật khẩu ít hơn 6 ký tự
            elseif (strlen($password) < 6) {
                $errors['password'] = 'Mật khẩu phải có ít nhất 6 ký tự!';
            } 
            // Cập nhật mật khẩu mới
            else {
                $hashed_password = md5($password);
                mysqli_query($conn, "UPDATE users SET password = '$hashed_password' WHERE email = '$decoded_email'");
                echo "<script>alert('Cập nhật mật khẩu thành công'); window.location.href='./login.php';</script>";
            }
        }
    } 
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quên mật khẩu</title>
    <link rel="stylesheet" href="./css/signin.css">
</head>

<body>
    <div class="background">
        <div class="form-signin">
            <h3>Đặt lại mật khẩu</h3>
            <form method="post">

                <div class="form-group">
                    <label for="email">Mật khẩu mới</label>
                    <input type="password" id="password" name="password" placeholder="Nhập mật khẩu mới của bạn"
                        value="<?= htmlspecialchars($_POST['password'] ?? '') ?>">
                        <div class="error"><?= $errors['password'] ?></div>
                </div>
                
                <button type="submit" name="submit">Cập nhật</button>
               
            </form>
        </div>
    </div>
</body>

</html>