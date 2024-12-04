<?php
include './config.php';
session_start();
error_reporting(E_ALL & ~E_NOTICE);

?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giới thiệu</title>
    <link rel="stylesheet" href="./css/main_new.css">
    <link rel="stylesheet" href="./css/header.css">
    <link rel="stylesheet" href="./css/aboutus.css">

    <link rel="stylesheet" href="./icon/fontawesome-free-6.6.0-web/css/all.min.css">

</head>

<body>

    <?php include 'header.php' ?>

    <div class="main">
        <div class="main_content">
            <div class="title">
                <span class="title_primary" style="font-size: 25px; color: black">GIỚI THIỆU</span>
                <span class="subtitle" style="font-size: 11px; color: black">
                    <i>"Chúng tôi kết nối cộng đồng mua bán đồ cũ đáng tin cậy, bền vững và tiết kiệm."</i>
                </span>
            </div>

            <div class="introduction">
                <span><strong>CONNAN</strong> là nền tảng thương mại điện tử chuyên về mua bán đồ cũ theo mô hình C2C, nơi người mua và
                    người bán kết nối trực tiếp với nhau. Chúng tôi tin rằng mỗi món đồ cũ đều có một câu chuyện và giá
                    trị riêng, và sứ mệnh của chúng tôi là mang đến cho chúng một vòng đời mới trong tay những người chủ
                    mới.</span>
            </div>

            <div class="content">
                <div class="content_item">
                    <div class="content_item_title">
                        <span>1. TẦM NHÌN VÀ SỨ MỆNH</span>
                    </div>
                    <div class="content_item_text">
                        <div class="vision">
                            <span class="vision_title">TẦM NHÌN</span>
                            <i class="vision_content">
                                Trở thành nền tảng hàng đầu về giao dịch đồ cũ trực tuyến tại Việt Nam, cung cấp một
                                không gian mua bán đáng tin cậy và dễ sử dụng cho người tiêu dùng.
                            </i>
                        </div>
                        <div class="mission">
                            <span class="mission_title">SỨ MỆNH</span>
                            <i class="mission_content">
                                Chúng tôi mong muốn thúc đẩy phong trào tiêu dùng bền vững, giúp người dùng tiết kiệm
                                chi phí và giảm thiểu tác động đến môi trường thông qua việc tái sử dụng đồ cũ.
                            </i>
                        </div>
                    </div>
                </div>

                <div class="content_item">
                    <div class="content_item_title">
                        <span>2. GIÁ TRỊ CỐT LÕI</span>
                    </div>
                    <div class="content_item_text">
                        <div class="trust">
                            <img src="./img/tincay.png" alt="">
                            <span class="trust_text">TIN CẬY</span>
                        </div>

                        <div class="friendly">
                            <img src="./img/thanthien.png" alt="">
                            <span class="friendly_text">THÂN THIỆN</span>
                        </div>

                        <div class="sustainable">
                            <img src="./img/benvung.png" alt="">
                            <span class="sustainable_text">BỀN VỮNG</span>
                        </div>
                    </div>
                </div>




            </div>
        </div>
    </div>

    <?php include 'footer.php' ?>
</body>



</html>