<?php
include '../config.php';

$errors = ['email' => ''];

if (isset($_POST['submit'])) {
    if (empty($_POST['email'])) {
        $errors['email'] = 'Vui lòng nhập email!';
    } else {
        $email = mysqli_real_escape_string($conn, $_POST['email']);

        // Kiểm tra email có tồn tại trong cơ sở dữ liệu hay không
        $check_email = mysqli_query($conn, "SELECT * FROM users WHERE email = '$email'") or die('Query failed');

        if (mysqli_num_rows($check_email) > 0) {
            // Nếu email tồn tại
            $encoded_email = urlencode(base64_encode($email)); // Mã hóa email
            $reset_link = "http://localhost/CONNANC2C/login/reset_password.php?email=" . $encoded_email;

            // Gửi email
            require "PHPMailer-master/src/PHPMailer.php";
            require "PHPMailer-master/src/SMTP.php";
            require 'PHPMailer-master/src/Exception.php';

            $mail = new PHPMailer\PHPMailer\PHPMailer(true);

            try {
                $mail->SMTPDebug = 0;
                $mail->isSMTP();
                $mail->CharSet = "utf-8";
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'zantumusic@gmail.com';
                $mail->Password = 'zclegjbplkegmnrv';
                $mail->SMTPSecure = 'ssl';
                $mail->Port = 465;
                $mail->setFrom('zantumusic@gmail.com', 'CONNAN');
                $mail->addAddress($email);
                $mail->isHTML(true);
                $mail->Subject = 'Đặt lại mật khẩu';

                // Nội dung email
                $noidungthu = '
                    <p>Chào bạn,</p>
                    <p>Bạn đã yêu cầu đặt lại mật khẩu từ trang web của chúng tôi.</p>
                    <p>Vui lòng nhấn vào liên kết bên dưới để đặt lại mật khẩu:</p>
                    <p><a href="' . $reset_link . '" style="color: blue; text-decoration: underline;">Đặt lại mật khẩu</a></p>
                    <p>Liên kết sẽ hết hạn sau 1 giờ.</p>
                    <p>Trân trọng,<br>Đội ngũ hỗ trợ</p>
                ';
                $mail->Body = $noidungthu;

                $mail->smtpConnect([
                    "ssl" => [
                        "verify_peer" => false,
                        "verify_peer_name" => false,
                        "allow_self_signed" => true,
                    ]
                ]);

                $mail->send();

            } catch (Exception $e) {
                $mail->ErrorInfo;
            }
        } else {
            $errors['email'] = 'Email không hợp lệ, vui lòng nhập lại!';
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
            <h3>Yêu cầu đặt lại mật khẩu</h3>
            <form method="post">

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" placeholder="Nhập email của bạn"
                        value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                    <div class="error"><?= $errors['email'] ?></div>
                </div>

                <button type="submit" name="submit" style="margin-bottom: 10px">Gửi yêu cầu</button>
                <a href="./login.php" style="color: orangered; text-decoration: none;">Quay về trang đăng nhập</a>

            </form>
        </div>
    </div>
</body>

</html>