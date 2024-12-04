<?php
include './config.php';
session_start();
error_reporting(E_ALL & ~E_NOTICE);

// Kiểm tra xem người dùng đã đăng nhập chưa
if (!isset($_SESSION['user_id'])) {
    header('Location: ./login/login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$total = 0;

// Xử lý thanh toán nếu form đã được gửi
if (isset($_POST['submit'])) {

    $errors = [];
    // Nhận thông tin từ biểu mẫu
    $fullname = $_POST['fullname'];
    $phone = $_POST['phone_number'];
    $address = $_POST['address'];
    $city = $_POST['city'];
    $district = $_POST['district'];
    $ward = $_POST['ward'];
    $payment_method = $_POST['payment_method'];


    if (isset($_GET['id'])) {
        $product_id = $_GET['id'];

        // Truy vấn sản phẩm từ cơ sở dữ liệu
        $sql = "SELECT * FROM products WHERE product_id = $product_id";
        $result = mysqli_query($conn, $sql);
        $product = mysqli_fetch_assoc($result);

        if ($product) {
            $seller_id = $product['seller_id'];
            $available_quantity = $product['quantity'];
            $quantity = $_GET['quantity'];

            if ($available_quantity < $quantity) {
                echo "<script>alert('Số lượng sản phẩm không đủ!'); window.location.href = 'index.php';</script>";
                exit();
            }

            $subtotal = $product['price'] * $quantity;
            $total += $subtotal;
        } else {
            echo "<script>alert('Đã có người khác mua sản phẩm!'); window.location.href = 'index.php';</script>";
            exit();
        }
    } else {
        echo "<script>alert('Không có sản phẩm nào được chọn!'); window.location.href = 'index.php';</script>";
        exit();
    }

    $order_sql = "INSERT INTO orders (purchase_id, total_amount, fullname, phone_number, address, city, district, ward, created_time, status, seller_id) 
                  VALUES ('$user_id', '$total', '$fullname', '$phone', '$address', '$city', '$district', '$ward', NOW(), 'pending', '$seller_id')";

    if (mysqli_query($conn, $order_sql)) {
        $order_id = mysqli_insert_id($conn);

        $order_detail_sql = "INSERT INTO orderdetails (order_id, product_id, quantity, price) 
                             VALUES ('$order_id', '$product_id', '$quantity', '{$product['price']}')";

        if (mysqli_query($conn, $order_detail_sql)) {
            $new_quantity = $available_quantity - $quantity;
            mysqli_query($conn, "UPDATE products SET quantity = '$new_quantity' WHERE product_id = '$product_id'");

            // Xóa sản phẩm khỏi giỏ hàng của người khác nếu số lượng còn lại không đủ
            if ($new_quantity <= 0) {
                $delete_cart_sql = "DELETE FROM cart WHERE product_id = '$product_id' AND user_id != '$user_id'";
                mysqli_query($conn, $delete_cart_sql);
            }
        }

        // Thanh toán qua COD
        if ($payment_method === 'cod') {
            mysqli_query($conn, "INSERT INTO transactions (method, created_time, order_id, user_id, status) 
                                 VALUES ('cod', NOW(), '$order_id', '$user_id', 'Chưa thanh toán')");
            echo "<script>alert('Đặt hàng thành công!'); window.location.href = 'index.php';</script>";
        } else {
            // Thanh toán qua thẻ
            if ($payment_method === 'redirect') {

            } else {
                echo "<script>alert('Thông tin thanh toán không thành công!'); window.location.href = 'checkout.php';</script>";
            }
        }
    } else {
        echo "<script>alert('Có lỗi xảy ra khi tạo đơn hàng!'); window.location.href = 'checkout.php';</script>";
    }
} else {
    // echo "<script>alert('Có lỗi xảy ra khi thanh toán!');</script>";
}
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <link rel="stylesheet" href="./css/main_new.css">
    <link rel="stylesheet" href="./css/header.css">
    <link rel="stylesheet" href="./css/Checkout.css">
    <link rel="stylesheet" href="./icon/fontawesome-free-6.6.0-web/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

</head>

<body>
    <?php include 'header.php' ?>

    <form method="POST" class="main_content">
        <div class="content">
            <div class="infor_myself">
                <div class="title">
                    Thông tin cá nhân
                </div>

                <div class="infor_row">
                    <label for="fullname">Họ và tên</label>
                    <input type="text" name="fullname" placeholder="Họ và tên" required>
                </div>

                <div class="infor_row">
                    <label for="phone_number">Số điện thoại</label>
                    <input type="tel" name="phone_number" placeholder="Số điện thoại (bắt đầu bằng số 0 và không chứa văn bản và kí tự đặc biệt)" required maxlength="10"
                        pattern="0\d{9}">
                </div>
                <div class="infor_row">
                    <label for="address">Địa chỉ cụ thể (tên đường, số nhà)</label>
                    <input type="text" name="address" placeholder="Vui lòng nhập địa chỉ của bạn" required>
                </div>

                <div class="infor_row">
                    <label for="city">Tỉnh / Thành phố</label>
                    <input type="text" name="city" placeholder="Vui lòng nhập tỉnh / thành phố" required>
                </div>

                <div class="infor_row">
                    <label for="district">Quận / huyện</label>
                    <input type="text" name="district" placeholder="Vui lòng nhập quận / huyện" required>
                </div>

                <div class="infor_row">
                    <label for="ward">Phường / xã</label>
                    <input type="text" name="ward" placeholder="Vui lòng nhập phường / xã" required>
                </div>
            </div>

            <div class="infor_payment">
                <div class="infor_detail_payment">
                    <div class="title" style="font-size: 15px">
                        Chọn phương thức thanh toán
                    </div>
                    <div class="method_payment">
                        <input type="radio" id="cod" name="payment_method" value="cod" required>
                        <div class="method_avt">
                            <img src="./img/cash.png" alt="">
                        </div>
                        <div class="method_name">
                            <span>Thanh toán khi nhận hàng</span>
                        </div>
                    </div>

                    <div class="method_payment">
                        <input type="radio" id="card" name="payment_method" value="card"
                            onclick="toggleCardPaymentDetails(true)">
                        <div class="method_avt" style="color: blue;">
                            <img src="./img/momo.jpg" alt="">
                        </div>
                        <div class="method_name">
                            <span>Ví điện tử Momo</span>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <div class="order_detail">
            <table class="cart-table">
                <thead>
                    <tr>
                        <th>Sản phẩm</th>
                        <th>Số lượng</th>
                        <th>Đơn giá</th>
                        <th>Tổng giá</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (isset($_GET['id']) && isset($_GET['quantity'])) {
                        $product_id = $_GET['id'];
                        $quantity = $_GET['quantity']; // Lấy số lượng sản phẩm từ URL
                    
                        // Truy vấn sản phẩm từ cơ sở dữ liệu
                        $sql = "SELECT * FROM products WHERE product_id = $product_id";
                        $result = mysqli_query($conn, $sql);
                        $product = mysqli_fetch_assoc($result);

                        // Tính tổng tiền cho sản phẩm
                        $subtotal = $product['price'] * $quantity;
                        $total += $subtotal;
                    }

                    ?>
                    <tr>
                        <td><?php echo $product['product_name']; ?></td>
                        <td><?php echo $quantity; ?></td>
                        <td><?php echo number_format($product['price'], 0, ',', '.') . ' VNĐ'; ?></td>
                        <td><?php echo number_format($subtotal, 0, ',', '.') . ' VND'; ?></td>
                    </tr>

                    <tr class="total-row">
                        <td colspan="3" style="text-align: right;">Tổng cộng:</td>
                        <td><?php echo number_format($total, 0, ',', '.') . ' VND'; ?></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="button_payment">
            <input class="btn_addtocart" type="submit" name="submit" data-toggle="modal" value="Thanh toán">
        </div>
    </form>
    <?php include 'footer.php' ?>
</body>

</html>