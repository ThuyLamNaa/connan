<?php
include '../config.php';

// Biến lưu trữ thông báo lỗi
$errors = [
    'user_name' => '',
    'email' => '',
    'password' => ''
];

if (isset($_POST['submit'])) {
    $user_name = trim(mysqli_real_escape_string($conn, $_POST['user_name']));
    $email = trim(mysqli_real_escape_string($conn, $_POST['email']));
    $password = trim(mysqli_real_escape_string($conn, $_POST['password']));

    // Kiểm tra tên đăng nhập
    if (empty($user_name)) {
        $errors['user_name'] = "Vui lòng nhập tên đăng nhập!";
    }

    // Kiểm tra email
    if (empty($email)) {
        $errors['email'] = "Vui lòng nhập email!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Email không hợp lệ!";
    }

    // Kiểm tra mật khẩu
    if (empty($password)) {
        $errors['password'] = "Vui lòng nhập mật khẩu!";
    } elseif (strlen($password) < 6) {
        $errors['password'] = "Mật khẩu phải có ít nhất 6 ký tự!";
    }

    // Nếu không có lỗi, tiếp tục kiểm tra email trong cơ sở dữ liệu
    if (!array_filter($errors)) {
        $password_hashed = md5($password); // Mã hóa mật khẩu
        $select_user = mysqli_query($conn, "SELECT * FROM users WHERE email = '$email'") or die('query fail');

        if (mysqli_num_rows($select_user) > 0) {
            $errors['email'] = "Email đã tồn tại!";
        } else {
            // Thực hiện thêm người dùng
            $create_time = date('Y-m-d H:i:s');
            mysqli_query($conn, "INSERT INTO users (user_name, password, email, role_id,created_time) VALUES ('$user_name', '$password_hashed', '$email', '3', '$created_time')") or die('query fail');
            echo "<script>alert('Đăng ký tài khoản thành công'); window.location.href='./login.php';</script>";

            // Gửi email thông báo đăng ký thành công
            require "PHPMailer-master/src/PHPMailer.php"; 
            require "PHPMailer-master/src/SMTP.php"; 
            require 'PHPMailer-master/src/Exception.php'; 

            $mail = new PHPMailer\PHPMailer\PHPMailer(true); // true: enables exceptions

            try {
                $mail->SMTPDebug = 0; // 0,1,2: chế độ debug, khi chạy ngon thì chỉnh lại 0
                $mail->isSMTP();  
                $mail->CharSet  = "utf-8";
                $mail->Host = 'smtp.gmail.com';  // SMTP servers
                $mail->SMTPAuth = true; // Enable authentication
                $mail->Username = 'zantumusic@gmail.com'; // SMTP username
                $mail->Password = 'zclegjbplkegmnrv';   // SMTP password
                $mail->SMTPSecure = 'ssl';  // encryption TLS/SSL 
                $mail->Port = 465;  // port to connect to                
                $mail->setFrom('zantumusic@gmail.com', 'CONNAN'); 
                $mail->addAddress($email, $user_name); // Gửi đến email của người dùng mới đăng ký
                $mail->isHTML(true);  // Set email format to HTML
                $mail->Subject = 'Đăng ký tài khoản thành công';
                $noidungthu = 'Chào bạn ' . $user_name . ',<br> Bạn đã đăng ký tài khoản thành công trên trang web của chúng tôi. Chúc bạn có một trải nghiệm thật tuyệt vời!'; 
                $mail->Body = $noidungthu;
                $mail->smtpConnect(array(
                    "ssl" => array(
                        "verify_peer" => false,
                        "verify_peer_name" => false,
                        "allow_self_signed" => true
                    )
                ));
                $mail->send();
            } catch (Exception $e) {
               $mail->ErrorInfo;
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
    <title>Đăng ký tài khoản</title>
    <link rel="stylesheet" href="./css/signin.css">
</head>
<body>
    <div class="background">
        <div class="form-signin">
            <h3>Đăng Ký Tài Khoản</h3>
            <form method="post">
                <div class="form-group">
                    <label for="username">Tên đăng nhập</label>
                    <input type="text" id="username" name="user_name" maxlength= 15 placeholder="Nhập tên đăng nhập" value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
                    <div class="error"><?= $errors['user_name'] ?></div>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" placeholder="Nhập email của bạn" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                    <div class="error"><?= $errors['email'] ?></div>
                </div>
                <div class="form-group">
                    <label for="password">Mật khẩu</label>
                    <input type="password" id="password" name="password" placeholder="Nhập mật khẩu">
                    <div class="error"><?= $errors['password'] ?></div>
                </div>
                <button type="submit" name="submit">Đăng ký</button>
                <div class="link-login">
                    <span>Đã có tài khoản? <a href="./login.php">Đăng nhập ngay</a></span>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
