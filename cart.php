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
    <title>Giỏ hàng</title>
    <link rel="stylesheet" href="./css/main_new.css">
    <link rel="stylesheet" href="./css/header.css">
    <link rel="stylesheet" href="./css/cart.css">

    <!-- Fontawesome css -->
    <link rel="stylesheet" href="./icon/fontawesome-free-6.6.0-web/css/all.min.css">
</head>

<body>
    <?php include 'header.php'; ?>

    <div class="main_content">
        <div class="cart">
            <?php
            $total = 0;
            $user_id = $_SESSION['user_id'];

            if (isset($user_id)) {
                if (isset($_POST['submit'])) {
                    foreach ($_POST['qty'] as $product_id => $new_quantity) {
                        $new_quantity = intval($new_quantity);
                        $is_checked = isset($_POST['selected'][$product_id]) ? 1 : 0;

                        if ($new_quantity > 0) {
                            $update_sql = "UPDATE cart SET quantity = $new_quantity, selected = $is_checked WHERE user_id = '$user_id' AND product_id = '$product_id'";
                            mysqli_query($conn, $update_sql);
                        } else {
                            $delete_sql = "DELETE FROM cart WHERE user_id = '$user_id' AND product_id = '$product_id'";
                            mysqli_query($conn, $delete_sql);
                        }
                    }
                }

                $sql = "SELECT cart.product_id, cart.quantity, cart.selected, products.product_name, products.price, products.product_image, products.seller_id, products.quantity as product_quantity, users.user_name
                    FROM cart 
                    JOIN products ON cart.product_id = products.product_id 
                    JOIN users ON products.seller_id = users.user_id 
                    WHERE cart.user_id = '$user_id' 
                    ORDER BY users.user_id ";
                $query = mysqli_query($conn, $sql);

                if (mysqli_num_rows($query) > 0) {
                    $current_seller = null;
                    echo "<form action='' method='post'>";

                    while ($row = mysqli_fetch_array($query)) {
                        if ($current_seller !== $row['seller_id']) {
                            $current_seller = $row['seller_id'];
                            echo "<div class='seller_group'><h3>Shop: {$row['user_name']}</h3></div>";
                        }

                        $checked = $row['selected'] ? "checked" : "";

                        echo "<div class='cart_item'>";
                        echo "<input type='checkbox' name='selected[{$row['product_id']}]' $checked>";
                        echo "<div class='product_photo'><img src='./upload_image/{$row['product_image']}' alt='{$row['product_name']}'></div>";
                        echo "<div class='product_name'><span>{$row['product_name']}</span></div>";
                        echo "<div class='price_product'><span>" . number_format($row['price']) . "đ</span></div>";
                        echo "<div class='product_quantity'>Số lượng: 
                            <input type='number' name='qty[{$row['product_id']}]' value='{$row['quantity']}' min='1' max='{$row['product_quantity']}' style='width: 60px;'>
                        </div>";
                        echo "<div class='product_totalprice'>" . number_format($row['quantity'] * $row['price']) . " đ</div>";
                        echo "<div class='operation'><a href='./delete_cart.php?product_id={$row['product_id']}'><i class='fa-solid fa-x'></i></a></div>";
                        echo "</div>";

                        if ($row['selected']) {
                            $total += $row['quantity'] * $row['price'];
                        }
                    }

                    echo "<div class='total_price_cart'><b>Tổng tiền: <font color='red'>" . number_format($total) . " đ</font></b></div>";
                    echo "<div class='button_group'>
                    <a href='./checkout.php?seller_id=$current_seller' class='btn btn_payment'>Thanh toán</a>
                    <input type='submit' name='submit' value='Cập nhật giỏ hàng' class='btn btn_update_cart'>
                  </div>";

                    echo "</form>";
                } else {
                    echo "<div class='alert'>
                            <img src='./img/cart.png' alt='Ảnh giỏ hàng'>
                            <p>Bạn không có sản phẩm nào trong giỏ hàng</p>
                            <p style='font-size: 15px; margin-top: 10px'><a href='./index.php'>Mua sắm tiếp thôi!</a></p>
                          </div>";
                }
            }
            ?>
        </div>
    </div>

    <?php include 'footer.php'; ?>
</body>

</html>