<?php
include '../config.php';
session_start();

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
    header('location:../login/login.php');
}

if (isset($_POST['add_account'])) {
    $user_name = mysqli_real_escape_string($conn, $_POST['user_name']);
    $password = $_POST['password'];
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $created_time = date('Y-m-d H:i:s');

    if (empty($user_name)) {
        $errors['user_name'] = "Vui lòng nhập tên tài khoản!";
    }

    if (empty($email)) {
        $errors['email'] = "Vui lòng nhập email!";
    }

    if (empty($password)) {
        $errors['password'] = "Vui lòng nhập mật khẩu!";
    } elseif (strlen($password) < 6) {
        $errors['password'] = "Mật khẩu phải có ít nhất 6 ký tự!";
    }


    $select_user = mysqli_query($conn, "select * from users where email = '$email'") or die('query fail');

    if (mysqli_num_rows($select_user) > 0) {
        $errors['account_exists'] = "Email này đã có người đăng ký tài khoản!";
    }

    if (empty($errors)) {
        // Mã hóa mật khẩu sau khi đã kiểm tra
        $hashed_password = md5($password);

        mysqli_query($conn, "INSERT INTO users (user_name, password, email, role_id, created_time) 
                             VALUES ('$user_name', '$hashed_password', '$email', '2', '$created_time')")
            or die('query fail');

        echo "<script type='text/javascript'>
                window.alert('Thêm tài khoản thành công, đã gửi thông báo đến email của nhân viên!');
                </script>";

        // Gửi email thông báo đăng ký thành công
        require "PHPMailer-master/src/PHPMailer.php";
        require "PHPMailer-master/src/SMTP.php";
        require 'PHPMailer-master/src/Exception.php';

        $mail = new PHPMailer\PHPMailer\PHPMailer(true); // true: enables exceptions

        try {
            $mail->SMTPDebug = 0; // 0,1,2: chế độ debug, khi chạy ngon thì chỉnh lại 0
            $mail->isSMTP();
            $mail->CharSet = "utf-8";
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
            $noidungthu = 'Chào bạn ' . $user_name . ',<br> Bạn đã được quản trị viên cấp tài khoản nhân viên để quản lý hệ thống CONNAN! <br> 
                Từ bây giờ bạn có thể truy cập vào hệ thống CONNA bằng tài khoản bên dưới <br>
                Emai:' . $email . '<br> Mật khẩu: ' . $hashed_password = md5($password);
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

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tạo tài khoản nhân viên</title>
    <link rel="stylesheet" href="./css/index.css">
    <link rel="stylesheet" href="./css/create_account.css">
    <link rel="stylesheet" href="../icon/fontawesome-free-6.6.0-web/css/all.min.css">
    <style>
        .content .content_logout {
            width: 100%;
            height: 35px;
            display: flex;
            justify-content: flex-end;
            align-items: center;
        }

        .content_logout button {
            width: 80px;
            height: 35px;
            background-color: white;
            border: none;
            margin-right: 5px;
            cursor: pointer;
        }

        .content_logout button:hover {
            background-color: white;
            border-bottom: 1px solid #ddd;
        }

        /* modal logout */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background-color: white;
            padding: 20px;
            border-radius: 4px;
            text-align: center;
            width: 350px;
            height: 100px;
        }

        .modal-content button {
            margin: 10px;
            padding: 8px 16px;
            cursor: pointer;
        }

        .modal-content .btn-ok {
            background-color: white;
            width: 80px;
            border-radius: 5px;
            color: green;
            border: 1px solid green;
        }

        .btn-ok:hover {
            background-color: green;
            color: white;
        }

        .modal-content .btn-cancel {
            width: 80px;
            background-color: white;
            color: red;
            border: 1px solid red;
            border-radius: 5px;
        }

        .btn-cancel:hover {
            background-color: red;
            color: white;
        }

        .error {
            color: red;
            font-size: 14px;
            margin-top: 5px;
        }
    </style>
</head>

<body>
    <aside class="sidebar">
        <div class="name_website">
            <span>CONNAN</span>
        </div>
        <ul>
            <li class="menu_item">
                <div class="menu_link">
                    <a href="./index.php" style="text-decoration: none; color: white">
                        <i class="fa-solid fa-list"></i>
                        <span>Bảng điều khiển</span>
                    </a>
                </div>
            </li>
            <li class="menu_item">
                <div class="menu_link" onclick="toggleSubmenu()">
                    <i class="fa-solid fa-user"></i>
                    <span>Quản lý tài khoản</span>
                    <i class="fa-solid fa-angle-down" style="font-size: 10px"></i>
                </div>
                <ul id="submenu" class="submenu">
                    <li>
                        <div class="submenu_link">
                            <a href="./management_account.php" style="text-decoration: none; color: white">
                                <i class="fa-solid fa-users"></i><span>Tất cả tài khoản</span>
                            </a>
                        </div>
                    </li>
                    <li>
                        <div class="submenu_link">
                            <a href="./account_employee.php" style="text-decoration: none; color: white">
                                <i class="fa-solid fa-users-line"></i><span>Tài khoản nhân viên</span>
                            </a>
                        </div>
                    </li>
                </ul>
            </li>
            <li class="menu_item">
                <div class="menu_link">
                    <a href="./management_product.php" style="text-decoration: none; color: white">
                        <i class="fa-brands fa-shopify"></i>
                        <span>Quản lý sản phẩm</span>
                    </a>
                </div>
            </li>
            <li class="menu_item">
                <div class="menu_link">
                    <a href="./management_category.php" style="text-decoration: none; color: white">
                        <i class="fa-solid fa-tag"></i>
                        <span>Quản lý danh mục</span>
                    </a>
                </div>
            </li>
        </ul>
    </aside>

    <div class="main-content">
        <header class="header">
            <div class="account-name" onclick="toggleModal()">
                <span><?php echo $_SESSION['admin_name'] ?></span>
                <i class="fa-regular fa-circle-user"></i>
            </div>
        </header>

        <div class="content">
            <div class="content_logout">
                <button type="button" onclick="showLogoutModal()">
                    <i class="fa-solid fa-right-from-bracket"></i> <span>Đăng xuất</span>
                </button>
            </div>
            <div class="content_title">
                <span>TẠO TÀI KHOẢN NHÂN VIÊN QUẢN LÝ</span>
            </div>

            <form method="post" class="form_information">
                <div class="form_group">
                    <label for="fullname">Tên tài khoản:</label>
                    <input type="text" class="form-control" id="fullname" name="user_name" maxlength="30">
                    <?php if (isset($errors['user_name'])): ?>
                        <div class="error"><?php echo $errors['user_name']; ?></div>
                    <?php endif; ?>
                </div>

                <div class="form_group">
                    <label for="email">Email:</label>
                    <input type="email" class="form-control" name="email">
                    <?php if (isset($errors['email'])): ?>
                        <div class="error"><?php echo $errors['email']; ?></div>
                    <?php endif; ?>
                    <?php if (isset($errors['account_exists'])): ?>
                        <div class="error"><?php echo $errors['account_exists']; ?></div>
                    <?php endif; ?>
                </div>
                <div class="form_group">
                    <label for="password">Mật khẩu:</label>
                    <input type="password" class="form-control" id="password" name="password">
                    <?php if (isset($errors['password'])): ?>
                        <div class="error"><?php echo $errors['password']; ?></div>
                    <?php endif; ?>
                </div>
                <div class="button_create">
                    <input name="add_account" type="submit" class="btn" value="Tạo tài khoản">
                </div>
            </form>


        </div>
    </div>

    <!-- Modal xác nhận đăng xuất -->
    <div id="logoutModal" class="modal">
        <div class="modal-content">
            <p>Bạn có chắc chắn muốn đăng xuất không?</p>
            <button class="btn-cancel" onclick="hideLogoutModal()">Hủy</button>
            <button class="btn-ok" onclick="confirmLogout()">OK</button>
        </div>
    </div>
</body>

<script>
    function toggleSubmenu() {
        const submenu = document.getElementById('submenu');
        submenu.style.display = submenu.style.display === 'none' || submenu.style.display === '' ? 'block' : 'none';
    }

    // Hiển thị modal xác nhận đăng xuất
    function showLogoutModal() {
        document.getElementById('logoutModal').style.display = 'flex';
    }

    // Ẩn modal xác nhận đăng xuất
    function hideLogoutModal() {
        document.getElementById('logoutModal').style.display = 'none';
    }

    // Xác nhận đăng xuất
    function confirmLogout() {
        window.location.href = '../login/logout.php'; // Đường dẫn đến trang xử lý đăng xuất
    }
</script>

</html>