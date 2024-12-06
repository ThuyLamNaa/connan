<?php
include './config.php';
session_start();
error_reporting(E_ALL & ~E_NOTICE);

if (isset($_GET['u_id'])) {
    $seller_id = $_GET['u_id'];
    // Lấy thông tin người bán
    $sql = "SELECT * FROM users WHERE user_id = '$seller_id'";
    $result = mysqli_query($conn, $sql);
    $seller = mysqli_fetch_assoc($result);

    // Lấy các sản phẩm của người bán
    $product_sql = "SELECT * FROM products WHERE seller_id = '$seller_id'";
    $products_result = mysqli_query($conn, $product_sql);
}

// Phân trang
$limit = 6; // Số sản phẩm trên mỗi trang
$page = isset($_GET['page']) ? $_GET['page'] : 1; // Trang hiện tại
$start_from = ($page - 1) * $limit;

// Truy vấn để lấy sản phẩm đã duyệt với phân trang
$query = "SELECT p.* 
          FROM productapproval pa 
          JOIN products p ON pa.product_id = p.product_id 
          WHERE pa.status = 'Accept' 
          AND p.seller_id = $seller_id 
          AND p.quantity > 0 
          LIMIT $start_from, $limit";

$select_all_product = mysqli_query($conn, $query) or die('Query Failed');

// Tính tổng số sản phẩm để tính số trang
$count_query = "SELECT COUNT(*) FROM productapproval pa 
                JOIN products p ON pa.product_id = p.product_id 
                WHERE pa.status = 'Accept' 
                AND p.seller_id = $seller_id 
                AND p.quantity > 0";
$count_result = mysqli_query($conn, $count_query);
$total_products = mysqli_fetch_row($count_result)[0];
$total_pages = ceil($total_products / $limit);

?>

<html>

<head>
    <title>Cửa hàng của tôi</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="./css/main_new.css">
    <link rel="stylesheet" href="./css/header.css">
    <link rel="stylesheet" href="./css/shop_seller.css">
    <link rel="stylesheet" href="./icon/fontawesome-free-6.6.0-web/css/all.min.css">
    <link rel="stylesheet" href="./icon/fontawesome-free-6.6.0-web/css/brands.min.css">
    <link rel="stylesheet" href="./icon/fontawesome-free-6.6.0-web/css/fontawesome.min.css">
</head>

<body>

    <?php include 'header.php' ?>

    <div class="main_content">
        <div class="myshop">
            <?php
            $select_user = mysqli_query($conn, "SELECT * FROM users WHERE user_id = $seller_id");
            $fetch_user = mysqli_fetch_assoc($select_user);
            ?>
            <div class="info_shop">
                <div class="avt_shop">
                    <img src="./img/avt.jfif" alt="Avatar cửa hàng">
                </div>
                <span
                    style="font-size: 18px; color: black; text-align: center"><?php echo $seller['user_name'] ?></span>
                <span><span style="font-size: 15px">Số điện thoại:
                    </span><?php echo $fetch_user['phone_number'] ?></span>
                <?php
                $select_product = mysqli_query($conn, "SELECT * FROM products WHERE seller_id = $seller_id AND quantity > 0");
                $total_product = mysqli_num_rows($select_product);

                $sql = "
                    SELECT SUM(od.quantity) AS total_sold
                    FROM orderdetails od
                    INNER JOIN products p ON od.product_id = p.product_id
                    INNER JOIN orders o ON od.order_id = o.order_id
                    WHERE o.status = 'completed' AND p.seller_id = $seller_id
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
                <div class="product_sale">
                    <?php
                    if (mysqli_num_rows($select_all_product) > 0) {
                        while ($product = mysqli_fetch_assoc($select_all_product)) {
                            ?>
                            <div class="product_item">
                                <a href="product.php?id=<?php echo $product['product_id']; ?>">
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
                        echo "<p class='alert'>Bạn chưa bán sản phẩm nào.</p>";
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
    <!-- Phân trang -->
    <div class="pagination" style="text-align: center">
        <?php if ($page > 1) { ?>
            <a href="?u_id=<?php echo $seller_id; ?>&page=<?php echo $page - 1; ?>">Trang trước</a>
        <?php } ?>
        <?php for ($i = 1; $i <= $total_pages; $i++) { ?>
            <a href="?u_id=<?php echo $seller_id; ?>&page=<?php echo $i; ?>" <?php if ($i == $page)
                      echo 'class="active"'; ?>><?php echo $i; ?></a>
        <?php } ?>
        <?php if ($page < $total_pages) { ?>
            <a href="?u_id=<?php echo $seller_id; ?>&page=<?php echo $page + 1; ?>">Trang sau</a>
        <?php } ?>
    </div>

    <?php include 'footer.php' ?>

</body>

</html>