<?php
include './config.php';
session_start();
error_reporting(E_ALL & ~E_NOTICE);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout Success</title>
    <!-- CSS -->
    <link rel="stylesheet" href="./css/main_new.css">
    <!-- Fontawesome css -->
    <link rel="stylesheet" href="./icon/fontawesome-free-6.6.0-web/css/all.min.css">
    <style>
        .main_content {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 10px;
            flex-direction: column;
            text-align: center;
        }

        .content {
            width: 60%;
            height: 300px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            background-color: #f8f9fa;
            border-radius: 8px;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
        }

        .content h2 {
            color: yellowgreen;
            font-size: 24px;
            margin-bottom: 20px;
        }

        .btn-home {
            display: inline-block;
            padding: 10px 20px;
            color: green;
            background-color: white;
            border-radius: 5px;
            text-decoration: none;
            font-size: 16px;
            margin-top: 20px;
            border: 1px solid green;
        }

        .btn-home:hover {
            background-color: khaki;
        }
    </style>
</head>

<body>
    <?php include 'header.php' ?>

    <div class="main_content">
        <div class="content">
            <h2>Đặt hàng thành công!</h2>
            <p>Cảm ơn bạn đã mua hàng. Chúng tôi sẽ xử lý đơn hàng của bạn và liên hệ sớm nhất có thể.</p>
            <a href="index_new.php" class="btn-home">Mua sắm tiếp thôi nào</a>
        </div>
    </div>
    <?php include 'footer.php' ?>
</body>

</html>
