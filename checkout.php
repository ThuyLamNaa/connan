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

// Lấy giỏ hàng của người dùng
$sql = "SELECT cart.product_id, cart.quantity, products.product_name, products.price, products.seller_id 
        FROM cart 
        JOIN products ON cart.product_id = products.product_id 
        WHERE cart.user_id = '$user_id'";
$query = mysqli_query($conn, $sql);

$sql1 = "SELECT cart.product_id, cart.quantity, products.product_name, products.price, products.seller_id 
        FROM cart 
        JOIN products ON cart.product_id = products.product_id 
        WHERE cart.user_id = '$user_id'";
$query1 = mysqli_query($conn, $sql1);

$total1 = 0;
$subtotal1 = 0;
while ($row1 = mysqli_fetch_array($query1)) {
    $subtotal1 = $row1['quantity'] * $row1['price'];
    $total1 += $subtotal1;
}

$total = 0;



// Xử lý thanh toán nếu form đã được gửi
if (isset($_POST['submit'])) {
    // Nhận thông tin từ biểu mẫu
    $full_name = $_POST['full_name'];
    $phone = $_POST['phone_number'];
    $address = $_POST['address'];
    $city = $_POST['city'];
    $district = $_POST['district'];
    $ward = $_POST['ward'];
    $payment_method = $_POST['payment_method'];

    // Thông tin thẻ (nếu có)
    $card_number = $_POST['card_number'] ?? null;
    $expiration_date = $_POST['expiration_date'] ?? null;
    $cvv = $_POST['cvv'] ?? null;

    // Thêm thông tin đơn hàng vào cơ sở dữ liệu
    $order_sql = "INSERT INTO orders (total_amount, full_name, phone_number, city, district, ward, created_time, status, seller_id, purchase_id) 
                  VALUES ('$total1', '$full_name', '$phone', '$city', '$district', '$ward', '$card_number', 'pending', '$seller_id', '$user_id')";

    if (mysqli_query($conn, $order_sql)) {
        // Lấy ID của đơn hàng vừa tạo
        $order_id = mysqli_insert_id($conn);

        // Lấy sản phẩm từ giỏ hàng và thêm vào đơn hàng
        while ($cart = mysqli_fetch_array($query)) {
            $product_id = $cart['product_id'];
            $quantity = $cart['quantity'];
            $price = $cart['price'];

            // Kiểm tra số lượng sản phẩm có đủ để thêm vào đơn hàng không
            $product_quantity = mysqli_query($conn, "SELECT quantity FROM products WHERE product_id = '$product_id'");
            $fetch_product_quantity = mysqli_fetch_assoc($product_quantity);
            $available_quantity = $fetch_product_quantity['quantity'];

            if ($available_quantity >= $quantity) {
                // Thêm vào bảng orderdetail
                $order_detail_sql = "INSERT INTO orderdetail (order_id, product_id, quantity, price) 
                             VALUES ('$order_id', '$product_id', '$quantity', '$price')";
                mysqli_query($conn, $order_detail_sql);

                // Cập nhật số lượng sản phẩm trong bảng products
                $new_quantity = $available_quantity - $quantity;
                mysqli_query($conn, "UPDATE products SET quantity = '$new_quantity' WHERE product_id = '$product_id'");

                // Nếu số lượng sản phẩm bằng 0, xóa sản phẩm khỏi giỏ hàng
                if ($new_quantity <= 0) {
                    // Xóa sản phẩm khỏi giỏ hàng
                    mysqli_query($conn, "DELETE FROM cart WHERE product_id = '$product_id' AND user_id = '$user_id'");
                }
            } else {
                // Nếu số lượng không đủ, xóa sản phẩm khỏi giỏ hàng
                mysqli_query($conn, "DELETE FROM cart WHERE product_id = '$product_id' AND user_id = '$user_id'");
                // Thông báo cho người dùng hoặc xử lý tiếp
                echo "<script>alert('Sản phẩm $product_id không đủ số lượng và đã được xóa khỏi giỏ hàng.');</script>";
            }
        }

        // Xóa giỏ hàng sau khi thanh toán thành công
        $delete_cart_sql = "DELETE FROM cart WHERE user_id = '$user_id'";
        mysqli_query($conn, $delete_cart_sql);

        // Thông báo người dùng về trạng thái thanh toán
        if ($payment_method === 'cod') {
            mysqli_query($conn, "insert into transactions (method, created_time, order_id, user_id) values ('cod', '$created_time', '$order_id','$user_id')") or die('query fail');

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

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <!-- CSS -->
    <link rel="stylesheet" href="./css/main_new.css">
    <link rel="stylesheet" href="./css/header.css">
    <!-- Fontawesome css -->
    <link rel="stylesheet" href="./icon/fontawesome-free-6.6.0-web/css/all.min.css">
    <link rel="stylesheet" href="./icon/fontawesome-free-6.6.0-web/css/brands.min.css">
    <link rel="stylesheet" href="./icon/fontawesome-free-6.6.0-web/css/fontawesome.min.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.0/css/all.css"
        integrity="sha384-lZN37f5QGtY3VHgisS14W3ExzMWZxybE1SJSEsQp9S+oqd12jhcu+A56Ebc1zFSJ" crossorigin="anonymous">
    <!-- Fontawesome js -->
    <link rel="stylesheet" href="./icon/fontawesome-free-6.6.0-web/js/all.min.js">
    <link rel="stylesheet" href="./icon/fontawesome-free-6.6.0-web/js/brands.min.js">
    <link rel="stylesheet" href="./icon/fontawesome-free-6.6.0-web/js/fontawesome.min.js">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .main_content {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 10px;
            flex-direction: column;
        }

        .content {
            width: 70%;
            display: flex;
            padding: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            gap: 2%;
        }

        .infor_myself {
            width: 59%;
            display: flex;
            flex-direction: column;
        }

        .infor_myself .title {
            font-size: 18px;
        }


        .infor_row {
            width: 100%;
            display: flex;
            flex-direction: column;
            margin: 10px 0;
        }

        .infor_row label {
            font-size: 15px;
            color: gray;
        }

        .infor_row input {
            width: 100%;
            height: 35px;
            border: 1px solid yellowgreen;
            border-radius: 5px;
            margin-top: 5px;
            padding: 10px;
        }

        .infor_payment {
            width: 39%;
            padding: 20px;
            background-color: #f8f8f8;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .infor_detail_payment .title {
            font-size: 15px;
            font-weight: bold;
            color: #333;
            margin-bottom: 20px;
            text-align: center;
        }

        .method_payment {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
            padding: 10px;
            border-radius: 8px;
            transition: all 0.3s ease;
            cursor: pointer;
            border: 1px solid #e0e0e0;
        }

        .method_payment:hover {
            border-color: #5a9;
            background-color: #e8f5e9;
        }

        .method_payment input[type="radio"] {
            margin-right: 15px;
            transform: scale(1.2);
            accent-color: #5a9;
        }

        .method_avt {
            font-size: 24px;
            margin-right: 15px;
        }

        .method_name span {
            font-size: 16px;
            color: #333;
        }

        .method_payment img {
            width: 30px;
            height: 30px;
            object-fit: cover;
        }

        .order_detail {
            width: 70%;
            margin-top: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .cart-table {
            width: 100%;
            border-collapse: collapse;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .cart-table th,
        .cart-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .cart-table th {
            background-color: #f8f8f8;
            font-weight: bold;
            color: #333;
        }

        .cart-table tr:hover {
            background-color: #f1f1f1;
        }

        .total-row td {
            font-size: 16px;
            font-weight: bold;
            color: #5a9;
        }

        .infor_payment {
            width: 40%;
            padding: 20px;
            background-color: #fafafa;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .infor_payment .title {
            font-size: 22px;
            color: #333;
            margin-bottom: 15px;
            text-align: center;
            font-weight: bold;
        }

        .button_payment {
            width: 70%;
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: 10px;
        }

        .button_payment input {
            width: 100px;
            height: 30px;
            border-radius: 5px;
            background-color: white;
            border: 1px solid green;
            color: green;
        }
    </style>
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
                    <label for="name">Họ và tên</label>
                    <input type="text" name="full_name" placeholder="Họ và tên" required>
                </div>

                <div class="infor_row">
                    <label for="phone_number">Số điện thoại</label>
                    <input type="phone" name="phone_number" placeholder="Số điện thoại" required>
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
                    <input type="text" name="ward" placeholder="Vui lòng nhập phường / xã">
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
                        <th>Tên sản phẩm</th>
                        <th>Số lượng</th>
                        <th>Giá</th>
                        <th>Tổng</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Hiển thị giỏ hàng
                    while ($row = mysqli_fetch_array($query)) {
                        $subtotal = $row['quantity'] * $row['price'];
                        $total += $subtotal;
                        echo "<tr>
                                <td>{$row['product_name']}</td>
                                <td>{$row['quantity']}</td>
                                <td>" . number_format($row['price'], 0, ',', '.') . " đ</td>
                                <td>" . number_format($subtotal, 0, ',', '.') . " đ</td>
                                </tr>";
                    }
                    ?>
                    <tr class="total-row">
                        <td colspan="3" align="right">Tổng cộng:</td>
                        <td><b><?php echo number_format($total, 0, ',', '.'); ?> đ</b></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="button_payment">
            <input class="btn_addtocart" type="submit" name="submit" data-toggle="modal" value="Thanh toán">
        </div>
    </form>

    <script>
        function toggleCardPaymentDetails(show) {
            var cardDetails = document.getElementById('card-details');
            if (show) {
                cardDetails.classList.add('active');
            } else {
                cardDetails.classList.remove('active');
            }
        }
    </script>
    <?php include 'footer.php' ?>
</body>

</html>