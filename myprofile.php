<?php
include './config.php';
session_start();
error_reporting(E_ALL & ~E_NOTICE);

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];

$select_user = mysqli_query($conn, "select * from users where user_id = $user_id");
$fetch_user = mysqli_fetch_assoc($select_user);

if (isset($_POST['submit'])) {
    // Lấy dữ liệu từ form
    $user_name = $_POST['user_name'];
    $phone_number = $_POST['phone_number'];
    $address = $_POST['address'];
    $cccd = $_POST['cccd'];
    $sex = $_POST['sex'];
    $birthday = $_POST['birthday'];

    if (empty($user_name)) {
        $errors['user_name'] = "Vui lòng nhập tên đăng nhập!";
    }

    // Kiểm tra số điện thoại
    if (!preg_match('/^\d{10}$/', $phone_number)) {
        $errors['phone_number'] = "Số điện thoại không hợp lệ!";
    }

     // Kiểm tra cccd
     if (!preg_match('/^\d{12}$/', $cccd)) {
        $errors['cccd'] = "CCCD phải đủ 12 số và không chứa văn bản hoặc các kí tự đặc biệt!";
    }

    $current_date = date("Y-m-d");  // Ngày hiện tại
    if ($birthday > $current_date) {
        $errors['birthday'] = "Ngày, tháng, năm sinh không hợp lệ!";
    } else {
        // Tính tuổi từ ngày sinh và kiểm tra xem đủ 12 tuổi chưa
        $birth_date = new DateTime($birthday);
        $age = $birth_date->diff(new DateTime())->y;  // Tính độ tuổi
        if ($age < 12) {
            $errors['birthday'] = "Bạn phải từ đủ 12 tuổi trở lên!";
        }
    }

    if (empty($errors)) {
        // Cập nhật thông tin vào cơ sở dữ liệu
        mysqli_query($conn, "UPDATE users SET user_name = '$user_name', phone_number = '$phone_number', address = '$address',
   cccd = '$cccd', sex = '$sex', birthday = '$birthday' WHERE user_id = '$user_id'");

        // Cập nhật lại thông tin trong session
        $_SESSION['user_name'] = $user_name;

        // Thông báo cập nhật thành công và tải lại trang
        $_SESSION['update_message'] = 'Cập nhật thông tin hồ sơ cá nhân thành công!';
        header("Location: myprofile.php?user_id=$user_id");
        exit();
    }
}
?>
<html>

<head>
    <title>Hồ sơ cá nhân</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="./css/main_new.css">
    <link rel="stylesheet" href="./css/header.css">
    <link rel="stylesheet" href="./icon/fontawesome-free-6.6.0-web/css/all.min.css">
    <link rel="stylesheet" href="./icon/fontawesome-free-6.6.0-web/css/brands.min.css">
    <link rel="stylesheet" href="./icon/fontawesome-free-6.6.0-web/css/fontawesome.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .main_content {
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            padding: 20px;
        }

        .profile {
            width: 70%;
            max-width: 900px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }

        .title {
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .content {
            width: 100%;
            display: flex;
            flex-direction: column;
            padding: 15px;
            border-radius: 8px;
            background-color: #fafafa;
            margin-bottom: 20px;
        }

        .row {
            display: flex;
            align-items: center;
            margin: 10px 0;
            border-bottom: 1px solid #ddd;
            padding-bottom: 10px;
        }

        .row label {
            width: 25%;
            font-size: 16px;
            color: #555;
        }

        .row input {
            width: 70%;
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: #fff;
            color: #333;
            transition: border 0.3s ease;
        }

        .row .sex {
            width: 70%;
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: #fff;
            color: #333;
            transition: border 0.3s ease;
        }

        .row input:focus {
            border-color: #4CAF50;
            outline: none;
        }

        .button {
            width: 100%;
            display: flex;
            justify-content: center;
            padding: 20px 0;
        }

        .button input {
            width: 200px;
            height: 40px;
            background-color: orange;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .button input:hover {
            background-color: orangered;
        }

        .error {
            color: red;
            font-size: 14px;
            margin-top: 5px;
            margin-left: 210px;
        }
    </style>
</head>

<body>

    <?php include 'header.php' ?>

    <!-- Chỉnh sửa để hiển thị thông báo bằng JavaScript -->
    <?php if (isset($_SESSION['update_message'])): ?>
        <script>
            alert('<?php echo $_SESSION['update_message']; ?>');
            <?php unset($_SESSION['update_message']); ?>
        </script>
    <?php endif; ?>

    <form method="POST" class="main_content">
        <div class="profile">
            <div class="title">
                <span>Hồ sơ cá nhân</span>
            </div>

            <div class="content">
                <div class="row">
                    <label for="">Họ và tên</label>
                    <input name="user_name" type="text" value="<?php echo $fetch_user['user_name'] ?>" maxlength="30">
                </div>
                <?php if (isset($errors['user_name'])): ?>
                    <div class="error"><?php echo $errors['user_name']; ?></div>
                <?php endif; ?>
                <div class="row">
                    <label for="">Số điện thoại</label>
                    <input name="phone_number" type="tel" value="<?php echo $fetch_user['phone_number'] ?>" maxlength="10">
                </div>
                <?php if (isset($errors['phone_number'])): ?>
                    <div class="error"><?php echo $errors['phone_number']; ?></div>
                <?php endif; ?>
                <div class="row">
                    <label for="">Email</label>
                    <input style="background-color: #ddd" type="email" value="<?php echo $fetch_user['email'] ?>"
                        disabled>
                </div>
                <div class="row">
                    <label for="">Địa chỉ</label>
                    <input name="address" type="text" value="<?php echo $fetch_user['address'] ?>">
                </div>
                <div class="row">
                    <label for="">CCCD / Định danh</label>
                    <input name="cccd" type="text" value="<?php echo $fetch_user['cccd'] ?>">
                </div>
                <?php if (isset($errors['cccd'])): ?>
                    <div class="error"><?php echo $errors['cccd']; ?></div>
                <?php endif; ?>
                <div class="row">
                    <label for="">Giới tính</label>
                    <select name="sex" class="sex">
                        <option value="Nam" <?php echo $fetch_user['sex'] == 'Nam' ? 'selected' : ''; ?>>Nam
                        </option>
                        <option value="Nữ" <?php echo $fetch_user['sex'] == 'Nữ' ? 'selected' : ''; ?>>Nữ
                        </option>
                        <option value="Khác" <?php echo $fetch_user['sex'] == 'Khác' ? 'selected' : ''; ?>>Khác
                        </option>
                    </select>
                </div>

                <div class="row">
                    <label for="">Ngày, tháng, năm sinh</label>
                    <input name="birthday" type="date" value="<?php echo $fetch_user['birthday'] ?>">
                </div>
                <?php if (isset($errors['birthday'])): ?>
                    <div class="error"><?php echo $errors['birthday']; ?></div>
                <?php endif; ?>
            </div>
        </div>
        <div class="button">
            <input name="submit" type="submit" value="Cập nhật hồ sơ">
        </div>
    </form>

    <?php include 'footer.php' ?>
</body>

</html>