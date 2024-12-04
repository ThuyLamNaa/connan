<?php
include './config.php';
session_start();
error_reporting(E_ALL & ~E_NOTICE);

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];
?>
<html>

<head>
    <title>Cửa hàng của tôi</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="./css/main_new.css">
    <link rel="stylesheet" href="./css/header.css">
    <link rel="stylesheet" href="./icon/fontawesome-free-6.6.0-web/css/all.min.css">
    <link rel="stylesheet" href="./icon/fontawesome-free-6.6.0-web/css/brands.min.css">
    <link rel="stylesheet" href="./icon/fontawesome-free-6.6.0-web/css/fontawesome.min.css">
    <style>
        .main_content {
            width: 100%;
            display: flex;
            align-items: flex-start;
            justify-content: center;
            padding: 10px;
        }

        /* Chỉnh giao diện cho container chính của cửa hàng */
        .myshop {
            width: 80%;
            display: flex;
            padding: 20px;
            margin: 0 auto;
            justify-content: space-between;
            background-color: #f9f9f9;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .info_shop {
            width: 30%;
            display: flex;
            flex-direction: column;
            padding: 20px;
            border-right: 1px solid #ddd;
            background-color: #fff;
            border-radius: 10px;
        }

        .avt_shop {
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 10px;
            border-radius: 50%;
            margin-bottom: 15px;
            border-bottom: 2px solid orange;
        }

        .info_shop img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            border: 2px solid #ddd;
            object-fit: cover;
        }

        .info_shop span {
            font-size: 15px;
            color: #333;
            margin-bottom: 10px;
        }

        .all_product {
            width: 70%;
            padding: 10px;
        }

        .tabs {
            display: flex;
            margin-bottom: 20px;
        }

        .tabs div {
            flex: 1;
            text-align: center;
            padding: 12px;
            background-color: #f0f0f0;
            cursor: pointer;
            border-radius: 5px;
            font-size: 14px;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        .tabs div:hover {
            background-color: orange;
        }

        .product_sale,
        .product_approval {
            display: none;
            /* Ẩn cả hai phần sản phẩm ban đầu */
            flex-wrap: wrap;
            gap: 5%;
        }

        .product_item {
            width: 30%;
            text-align: center;
        }

        .alert {
            text-align: center;
            margin-top: 140px;
            margin-left: 230px;
            margin-bottom: 140px;
            font-size: 20px;
            color: orangered;
        }

        .tabs .active {
            background-color: orange;
        }
    </style>
</head>

<body>

    <?php include 'header.php' ?>

    <div class="main_content">
        <div class="myshop">
            <?php
            $select_user = mysqli_query($conn, "SELECT * FROM users WHERE user_id = $user_id");
            $fetch_user = mysqli_fetch_assoc($select_user);
            ?>
            <div class="info_shop">
                <div class="avt_shop">
                    <img src="./img/avt.jfif" alt="Avatar cửa hàng">
                </div>
                <span style="font-size: 18px; color: black; text-align: center"><?php echo $user_name ?></span>
                <span><span style="font-size: 15px">Số điện thoại:
                    </span><?php echo $fetch_user['phone_number'] ?></span>
                <?php
                $select_product = mysqli_query($conn, "SELECT * FROM products WHERE seller_id = $user_id");
                $total_product = mysqli_num_rows($select_product);

                $sql = "
                        SELECT SUM(od.quantity) AS total_sold
                        FROM orderdetails od
                        INNER JOIN products p ON od.product_id = p.product_id
                        INNER JOIN orders o ON od.order_id = o.order_id
                        WHERE o.status = 'completed' AND p.seller_id = $user_id
                    ";
                $select_product_bought = mysqli_query($conn, $sql);
                $total_product_bought = mysqli_fetch_assoc($select_product_bought);
                ?>
                <span><span style="font-size: 15px">Sản phẩm tại cửa hàng:</span>
                    <span><?php echo $total_product ?></span></span>
                <span><span style="font-size: 15px">Sản phẩm đã bán:
                    </span><?php echo $total_product_bought['total_sold'] ?></span>
                <span><span style="font-size: 15px">Địa chỉ: </span><?php echo $fetch_user['address'] ?></span>
            </div>

            <div class="all_product">

                <div class="tabs">
                    <div class="product_for_sale active" onclick="showProducts('sale')">
                        <span>Sản phẩm đang bán</span>
                    </div>
                    <div class="pending_approval" onclick="showProducts('approval')">
                        <span>Sản phẩm bị từ chối</span>
                    </div>
                    <div class="pending_wait" onclick="showProducts('wait')">
                        <span>Sản phẩm đang chờ duyệt</span>
                    </div>
                </div>

                <div class="product_sale">
                    <?php
                    // Truy vấn tất cả các sản phẩm đã duyệt
                    $query = "SELECT p.* FROM productapproval pa JOIN products p ON pa.product_id = p.product_id WHERE pa.status = 'Accept' AND p.seller_id = $user_id AND p.quantity > 0";
                    $select_all_product = mysqli_query($conn, $query) or die('Query Failed');

                    if (mysqli_num_rows($select_all_product) > 0) {
                        while ($product = mysqli_fetch_assoc($select_all_product)) {
                            ?>
                            <div class="product_item">
                                <a href="detail_myproduct.php?id=<?php echo $product['product_id']; ?>">
                                    <div class="product_photo">
                                        <img src="./upload_image/<?php echo $product['product_image']; ?>" alt="">
                                    </div>
                                    <div class="product_info">
                                        <span><?php echo $product['product_name']; ?></span>
                                    </div>
                                    <div class="product_price">
                                        <span><?php echo number_format($product['price'], 0, ',', '.'); ?> VNĐ</span>
                                    </div>
                                </a>
                            </div>
                            <?php
                        }
                    } else {
                        echo '<div class="alert"><p>Bạn chưa bán sản phẩm nào.</p></div>';
                    }
                    ?>
                </div>

                <div class="product_approval">
                    <?php
                    // Truy vấn tất cả các sản phẩm bị từ chối
                    $query = "SELECT p.* FROM productapproval pa JOIN products p ON pa.product_id = p.product_id WHERE pa.status = 'Dismiss' AND p.seller_id = $user_id";
                    $select_all_product = mysqli_query($conn, $query) or die('Query Failed');

                    if (mysqli_num_rows($select_all_product) > 0) {
                        while ($product = mysqli_fetch_assoc($select_all_product)) {
                            ?>
                            <div class="product_item">
                                <a href="detail_myproduct.php?id=<?php echo $product['product_id']; ?>">
                                    <div class="product_photo">
                                        <img src="./upload_image/<?php echo $product['product_image']; ?>" alt="">
                                    </div>
                                    <div class="product_info">
                                        <span><?php echo $product['product_name']; ?></span>
                                    </div>
                                    <div class="product_price">
                                        <span><?php echo number_format($product['price'], 0, ',', '.'); ?> VNĐ</span>
                                    </div>
                                </a>
                            </div>
                            <?php
                        }
                    } else {
                        echo '<div class="alert"><p>Bạn không có sản phẩm nào bị từ chối.</p></div>';
                    }
                    ?>
                </div>

                <div class="product_wait">
                    <?php
                    // Truy vấn tất cả các sản phẩm bị từ chối
                    $query = "SELECT * FROM products WHERE seller_id = $user_id AND status = '0'";
                    $select_all_product = mysqli_query($conn, $query) or die('Query Failed');

                    if (mysqli_num_rows($select_all_product) > 0) {
                        while ($product = mysqli_fetch_assoc($select_all_product)) {
                            ?>
                            <div class="product_item">
                                <a href="detail_myproduct.php?id=<?php echo $product['product_id']; ?>">
                                    <div class="product_photo">
                                        <img src="./upload_image/<?php echo $product['product_image']; ?>" alt="">
                                    </div>
                                    <div class="product_info">
                                        <span><?php echo $product['product_name']; ?></span>
                                    </div>
                                    <div class="product_price">
                                        <span><?php echo number_format($product['price'], 0, ',', '.'); ?> VNĐ</span>
                                    </div>
                                </a>
                            </div>
                            <?php
                        }
                    } else {
                        echo '<div class="alert"><p>Bạn không có sản phẩm nào đang chờ duyệt.</p></div>';
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>

    <script>
        function showProducts(type) {
            const sections = {
                sale: document.querySelector('.product_sale'),
                approval: document.querySelector('.product_approval'),
                wait: document.querySelector('.product_wait')
            };

            const tabs = {
                sale: document.querySelector('.product_for_sale'),
                approval: document.querySelector('.pending_approval'),
                wait: document.querySelector('.pending_wait')
            };

            // Ẩn tất cả các phần sản phẩm và xóa trạng thái active
            Object.values(sections).forEach(section => section.style.display = 'none');
            Object.values(tabs).forEach(tab => tab.classList.remove('active'));

            // Hiển thị phần sản phẩm và thêm trạng thái active cho tab tương ứng
            sections[type].style.display = 'flex';
            tabs[type].classList.add('active');
        }

        // Hiển thị sản phẩm đang bán mặc định khi trang được tải
        document.addEventListener('DOMContentLoaded', () => {
            showProducts('sale');
        });

    </script>
    <?php include 'footer.php' ?>
</body>


</html>