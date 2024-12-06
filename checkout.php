<?php
include './config.php';
session_start();
error_reporting(E_ALL & ~E_NOTICE);

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];  // Lấy user_id của người dùng

if (isset($_POST['submit'])) {
    // Nhận thông tin từ biểu mẫu
    $fullname = mysqli_real_escape_string($conn, $_POST['fullname']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $city = mysqli_real_escape_string($conn, $_POST['city']);
    $district = mysqli_real_escape_string($conn, $_POST['district']);
    $ward = mysqli_real_escape_string($conn, $_POST['ward']);
    $payment_method = mysqli_real_escape_string($conn, $_POST['payment_method']);

    // Tính tổng giá trị của các sản phẩm trong giỏ hàng
    $total1 = 0;
    $cart_sql = "SELECT c.quantity, p.price as p_price FROM cart c JOIN products p ON p.product_id = c.product_id  WHERE user_id = '$user_id' AND selected = 1";
    $cart_query = mysqli_query($conn, $cart_sql);
    while ($cart = mysqli_fetch_array($cart_query)) {
        $total1 += $cart['quantity'] * $cart['p_price'];
    }

    // Thêm thông tin đơn hàng vào cơ sở dữ liệu
// Kiểm tra seller_id có tồn tại trong bảng users không
    $seller_id = $_GET['seller_id']; // Hoặc giá trị seller_id bạn đang sử dụng
    $query_check_seller = "SELECT * FROM users WHERE user_id = '$seller_id'";
    $result = mysqli_query($conn, $query_check_seller);

    if (mysqli_num_rows($result) == 0) {
        // Seller không tồn tại trong bảng users, trả về lỗi hoặc thông báo
        echo "Lỗi: Không tìm thấy người bán với ID này.";
    } else {
        // Seller tồn tại, tiếp tục với truy vấn INSERT INTO orders
        $order_sql = "INSERT INTO orders (fullname, phone_number, address, city, district, ward, purchase_id, seller_id, total_amount, status) VALUES ('$fullname', '$phone', '$address', '$city', '$district', '$ward', '$user_id', '$seller_id', '$total1', 'pending')";
        mysqli_query($conn, $order_sql);
    }

    if (mysqli_query($conn, $order_sql)) {
        // Lấy ID của đơn hàng vừa tạo
        $order_id = mysqli_insert_id($conn);

        // Lấy sản phẩm từ giỏ hàng và thêm vào đơn hàng
        $cart_sql = "SELECT p.product_id, c.quantity, p.price FROM cart c JOIN products p ON p.product_id = c.product_id WHERE user_id = '$user_id' AND selected = 1 ";
        $cart_query = mysqli_query($conn, $cart_sql);

        while ($cart = mysqli_fetch_array($cart_query)) {
            $product_id = $cart['product_id'];
            $quantity = $cart['quantity'];
            $price = $cart['price'];

            // Kiểm tra số lượng sản phẩm có đủ để thêm vào đơn hàng không
            $product_quantity_sql = "SELECT quantity FROM products WHERE product_id = '$product_id'";
            $product_quantity_query = mysqli_query($conn, $product_quantity_sql);
            $fetch_product_quantity = mysqli_fetch_assoc($product_quantity_query);
            $available_quantity = $fetch_product_quantity['quantity'];

            if ($available_quantity >= $quantity) {
                // Thêm vào bảng orderdetails
                $order_detail_sql = "INSERT INTO orderdetails (order_id, product_id, quantity, price) 
                                     VALUES ('$order_id', '$product_id', '$quantity', '$price')";
                mysqli_query($conn, $order_detail_sql);

                // Cập nhật số lượng sản phẩm trong bảng products
                $new_quantity = $available_quantity - $quantity;
                mysqli_query($conn, "UPDATE products SET quantity = '$new_quantity' WHERE product_id = '$product_id'");

                // Nếu số lượng sản phẩm bằng 0, xóa sản phẩm khỏi giỏ hàng
                if ($new_quantity <= 0) {
                    mysqli_query($conn, "DELETE FROM cart WHERE product_id = '$product_id' AND user_id = '$user_id'");
                }
            } else {
                // Nếu số lượng không đủ, xóa sản phẩm khỏi giỏ hàng
                mysqli_query($conn, "DELETE FROM cart WHERE product_id = '$product_id' AND user_id = '$user_id'");
                echo "<script>alert('Sản phẩm $product_id không đủ số lượng và đã được xóa khỏi giỏ hàng.');</script>";
            }
        }

        // Xóa giỏ hàng sau khi thanh toán thành công
        $delete_cart_sql = "DELETE FROM cart WHERE user_id = '$user_id'";
        mysqli_query($conn, $delete_cart_sql);

        // Thông báo người dùng về trạng thái thanh toán
        if ($payment_method === 'cod') {
            mysqli_query($conn, "INSERT INTO transactions (method, created_time, order_id, user_id, status) 
                                 VALUES ('cod', NOW(), '$order_id', '$user_id', 'Chưa thanh toán')") or die('query fail');

            echo "<script>alert('Đặt hàng thành công'); window.location.href = 'index.php';</script>";
        } else {
            // Thanh toán qua thẻ
            echo "<script>alert('Thanh toán thành công! Bạn sẽ được chuyển hướng!'); window.location.href = 'confirmation_test.php';</script>";
        }
        exit();
    } else {
        echo "<script>alert('Có lỗi xảy ra khi thanh toán!');</script>";
    }
}
?>

<!-- HTML code remains unchanged -->



<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thanh toán</title>
    <link rel="stylesheet" href="./css/main_new.css">
    <link rel="stylesheet" href="./css/header.css">

    <!-- Fontawesome css -->
    <link rel="stylesheet" href="./icon/fontawesome-free-6.6.0-web/css/all.min.css">
    <style>
        <style>

        /* General page styles */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f7fc;
            color: #333;
            margin: 0;
            padding: 0;
        }

        h3 {
            text-align: center;
            color: #444;
            margin-bottom: 20px;
        }

        /* Container for main content */
        .main_content {
            max-width: 800px;
            margin: 30px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        /* Payment information form */
        .payment_info {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .payment_info label {
            font-size: 13px;
            font-weight: bold;
            color: #444;
        }

        .payment_info input,
        .payment_info select {
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }

        .payment_info input:focus,
        .payment_info select:focus {
            outline-color: #007BFF;
        }

        /* Checkout products section */
        .checkout_products {
            margin-top: 20px;
        }

        .checkout_products .seller_group {
            width: 100%;
            background-color: #f0f0f0;
            padding: 8px;
            border-radius: 5px;
            margin-bottom: 15px;
            font-weight: bold;
        }

        .checkout_products .product_item {
            width: 100%;
            height: 100px;
            display: flex;
            gap: 12px;
            padding: 10px 0;
            border-bottom: 1px solid #ddd;
            align-items: center;
        }

        .checkout_products .product_item img {
            max-width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 5px;
        }

        .checkout_products .product_info {
            flex: 1;
            font-size: 14px;
        }

        .checkout_products .product_info p {
            margin: 3px 0;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* Total price section */
        .total_price {
            font-size: 16px;
            font-weight: bold;
            text-align: right;
            margin-top: 20px;
            color: orange
        }

        /* Button styles */
        button.btn_confirm_payment {
            background-color: orange;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            width: 100%;
        }

        button.btn_confirm_payment:hover {
            background-color: orangered;
        }

        /* Responsive styles */
        @media (max-width: 768px) {
            .main_content {
                padding: 15px;
            }

            .payment_info input,
            .payment_info select {
                font-size: 14px;
            }

            .checkout_products .product_item {
                flex-direction: column;
                align-items: center;
            }

            .checkout_products .product_item img {
                max-width: 100px;
            }

            img {
                width: 100px;
                height: 120px;
                object-fit: cover;
                border-radius: 5px;
            }
        }
    </style>
</head>

<body>
    <?php include 'header.php'; ?>

    <div class="main_content">
        <h3>Xác nhận đặt hàng</h3>
        <form method="post">
            <div class="payment_info">
                <!-- Form fields for payment info -->
                <label for="fullname">Họ và tên:</label>
                <input type="text" name="fullname" id="fullname" required>
                <label for="phone">Số điện thoại:</label>
                <input type="text" name="phone" id="phone" required>
                <label for="address">Địa chỉ:</label>
                <input type="text" name="address" id="address" required>
                <label for="city">Tỉnh / thành phố:</label>
                <input type="text" name="city" id="city" required>
                <label for="district">Quận / huyện:</label>
                <input type="text" name="district" id="district" required>
                <label for="ward">Phường / xã:</label>
                <input type="text" name="ward" id="ward" required>
                <label for="payment_method">Phương thức thanh toán:</label>
                <select name="payment_method" id="payment_method" required>
                    <option value="cod">Thanh toán khi nhận hàng (COD)</option>
                    <option value="bank">Chuyển khoản ngân hàng</option>
                </select>
            </div>

            <div class="checkout_products">
                <?php
                // Lấy tất cả sản phẩm đã chọn trong giỏ hàng
                $sql = "SELECT cart.product_id, cart.quantity, products.product_name, products.price, products.product_image, products.seller_id, users.user_name
                        FROM cart 
                        JOIN products ON cart.product_id = products.product_id 
                        JOIN users ON products.seller_id = users.user_id
                        WHERE cart.user_id = '$user_id' AND cart.selected = 1";
                $query = mysqli_query($conn, $sql);

                $total_price = 0;
                $current_seller = null;

                if (mysqli_num_rows($query) > 0) {
                    while ($row = mysqli_fetch_array($query)) {
                        if ($current_seller !== $row['seller_id']) {
                            if ($current_seller !== null) {
                                echo "<div class='total_price'><b>Tổng cộng: " . number_format($total_price) . " đ</b></div>";
                            }
                            $current_seller = $row['seller_id'];
                            echo "<div class='seller_group'><h5>Shop: {$row['user_name']}</h5></div>";
                            $total_price = 0;
                        }

                        echo "<div class='product_item'>";
                        echo "<img src='./upload_image/{$row['product_image']}' alt='{$row['product_name']}'>";
                        echo "<div class='product_info'>";
                        echo "<p>{$row['product_name']}</p>";
                        echo "<p>Số lượng: {$row['quantity']}</p>";
                        echo "<p>Giá: " . number_format($row['price']) . " đ</p>";
                        echo "</div>";
                        echo "</div>";

                        $total_price += $row['quantity'] * $row['price'];
                    }

                    echo "<div class='total_price'><b>Tổng cộng: " . number_format($total_price) . " đ</b></div>";
                } else {
                    echo "<p>Không có sản phẩm nào để thanh toán!</p>";
                }
                ?>
            </div>

            <button type="submit" name="submit" class="btn btn_confirm_payment">Xác nhận thanh toán</button>
        </form>
    </div>
</body>

</html>