<?php
include './config.php';
session_start();
error_reporting(E_ALL & ~E_NOTICE);

$order_id = @$_GET['order_id'] ? @$_GET['order_id'] : null;

// if (!$order_id) {
//     header('Location: sales_order.php'); // Điều hướng nếu không có mã đơn hàng
//     exit();
// }

// Truy vấn chi tiết đơn hàng
$select_order = mysqli_query($conn, "SELECT 
                  o.purchase_id, o.order_id, o.created_time, o.total_amount, o.status, o.fullname, o.phone_number, o.address, o.city, o.district, o.ward, t.status as t_status, t.method
                FROM orders o
                JOIN transactions t ON o.order_id = t.order_id
                WHERE o.order_id = '$order_id'");

// Truy vấn sản phẩm trong đơn hàng
$select_product = mysqli_query($conn, "SELECT 
                od.quantity, od.price,
                p.product_name, p.product_image
                FROM orderdetails od
                JOIN products p ON od.product_id = p.product_id
                WHERE od.order_id = '$order_id'");

    if (mysqli_num_rows($select_order) > 0) {
        $fetch_order = mysqli_fetch_assoc($select_order);
    }

    if (isset($_GET['cancel_order_id'])) {
        $cancel_order_id = $_GET['cancel_order_id'];
    
        // Truy vấn chi tiết sản phẩm trong đơn hàng
        $select_product = mysqli_query($conn, "SELECT 
                    od.product_id, od.quantity
                    FROM orderdetails od
                    WHERE od.order_id = '$cancel_order_id'");
    
        // Cập nhật lại số lượng sản phẩm trong kho
        while ($fetch_product = mysqli_fetch_assoc($select_product)) {
            $product_id = $fetch_product['product_id'];
            $order_quantity = $fetch_product['quantity'];
    
            // Lấy số lượng hiện tại trong kho
            $select_stock = mysqli_query($conn, "SELECT quantity FROM products WHERE product_id = '$product_id'");
            $fetch_stock = mysqli_fetch_assoc($select_stock);
            $current_quantity = $fetch_stock['quantity'];
    
            // Cập nhật số lượng trong kho sau khi hủy đơn hàng
            $new_quantity = $current_quantity + $order_quantity; // Cộng số lượng sản phẩm vào kho
            mysqli_query($conn, "UPDATE products SET quantity = '$new_quantity' WHERE product_id = '$product_id'");
        }
    
        // Cập nhật trạng thái đơn hàng thành 'Canceled'
        mysqli_query($conn, "UPDATE orders SET status = 'Canceled' WHERE order_id = '$cancel_order_id'");
    
        echo "<script type='text/javascript'>
        window.alert('Hủy đơn hàng thành công.');
        window.location.href = 'order_purchase.php';
        </script>";
    }
?>

    <!DOCTYPE html>
    <html lang="vi">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Chi tiết đơn hàng</title>
        <link rel="stylesheet" href="./css/main_new.css">
        <link rel="stylesheet" href="./css/header.css">
        <link rel="stylesheet" href="./icon/fontawesome-free-6.6.0-web/css/all.min.css">
        <style>
            .main_content {
                width: 100%;
                display: flex;
                justify-content: center;
                align-items: center;
                flex-direction: column;
            }


            .main_content .detail_order {
                width: 80%;
                padding: 20px;
                margin-top: 10px;
                display: flex;
                justify-content: center;
                align-items: center;
                flex-direction: column;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            }

            .main_content .primary_title {
                width: 100%;
                height: 30px;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .title {
                width: 100%;
                display: flex;
                justify-content: flex-start;
                margin-bottom: 10px;
                flex-direction: column;
                line-height: 25px;
            }

            .title .main_title {
                font-size: 13px;
                font-weight: bold;
                margin: 0;
            }

            .title .time {
                font-size: 13px;
                color: black;
            }

            .title .status_order {
                font-size: 13px;
                color: black;
            }

            .detail_order .infor_customer {
                width: 100%;
            }

            .detail_order .infor_order {
                width: 100%;
                margin-top: 20px;
            }

            table {
                width: 100%;
                border-collapse: collapse;
                text-align: center;
                background-color: #f9f9f9;
            }

            table th,
            table td {
                padding: 5px 5px;
                border: 1px solid #ddd;
                background-color: white;
            }

            table th {
                background-color: #f1f1f1;
                font-weight: bold;
                font-size: 15px;
            }

            table td {
                font-size: 15px;
            }

            .infor_order img {
                width: 80px;
                height: 80px;
                object-fit: cover;
                border-radius: 5px;
            }

            .total_order {
                width: 100%;
                margin-top: 10px;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .total_order_button {
                width: 50%;
                display: flex;
                justify-content: center;
                align-items: center;
            }

            .total_order_button .cancel_order {
                width: 150px;
                height: 30px;
                border-radius: 5px;
                border: none;
                background-color: red;
                color: white;
                font-size: 15px;
                cursor: pointer;
                display: flex;
                align-items: center;
                justify-content: center;
            }
            .total_order_button a {
                text-decoration: none;
            }

            .total_order_button .cancel_order:hover {
                background-color: orangered;
                transform: scale(1.05);
            }

            .total_order_button .cancel_order:active {
                background-color: orangered;
                transform: scale(0.95);
            }

            .total_order_content {
                width: 50%;
            }

            .total_order_title {
                width: 40%;
                text-align: left;
                color: black;
            }

            .total_order_value {
                width: 60%;
                text-align: right;
            }

        </style>
    </head>

    <body>
        <?php include 'header.php'; ?>

        <div class="main_content">
            <div class="detail_order">
                <div class="primary_title">
                    <h4>THÔNG TIN CHI TIẾT ĐƠN HÀNG</h4>
                </div>
                <div class="infor_customer">
                    <div class="title">
                        <p class="main_title">THÔNG TIN GIAO HÀNG</p>
                    </div>
                    <table>
                        <thead>
                            <tr>
                                <th>Ma KH</th>
                                <th>Họ tên khách hàng</th>
                                <th>Số điện thoại</th>
                                <th>Địa chỉ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><?php echo $fetch_order['purchase_id'] ?></td>
                                <td><?php echo $fetch_order['fullname'] ?></td>
                                <td><?php echo $fetch_order['phone_number'] ?></td>
                                <td><?php echo $fetch_order['address'], " - ", $fetch_order['ward'], " - ", $fetch_order['district'], " - ", $fetch_order['city']; ?>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="infor_order">
                    <div class="title">
                        <p class="main_title">CHI TIẾT ĐƠN HÀNG</p>
                        <span class="time">Thời gian mua hàng: <?php echo $fetch_order['created_time']; ?></span>
                        <span class="status_order">Trạng thái đơn hàng: <span
                                style="color: green"><i><?php echo $fetch_order['status']; ?></i></span></span>
                    </div>
                    <table>
                        <thead>
                            <tr>
                                <th>Mã SP</th>
                                <th>Hình ảnh</th>
                                <th>Tên sản phẩm</th>
                                <th>Số lượng</th>
                                <th>Giá</th>
                                <th>Tổng tiền</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                            $stt = 1; // Khởi tạo số thứ tự
                            $total_order_price = 0; // Tổng giá trị đơn hàng
                        
                            while ($fetch_product = mysqli_fetch_assoc($select_product)) {
                                $item_total = $fetch_product['price'] * $fetch_product['quantity'];
                                $total_order_price += $item_total;
                                ?>
                                <tr>
                                    <td><?php echo $stt ?></td>
                                    <td><img src="./upload_image/<?php echo $fetch_product['product_image'] ?>" alt="Ảnh sản phẩm"></td>
                                    <td><?php echo $fetch_product['product_name'] ?></td>
                                    <td><?php echo $fetch_product['quantity'] ?></td>
                                    <td><?php echo number_format($fetch_product['price'], 0, ',', '.') . ' VNĐ'; ?></td>
                                    <td><?php echo number_format($fetch_product['price'] * $fetch_product['quantity'], 0, ',', '.') . ' VNĐ'; ?>
                                    </td>
                                </tr>
                                <?php $stt++;
                            } ?>
                        </tbody>
                    </table>
                </div>

                <div class="total_order">
                    <?php if(trim($fetch_order['status']) == 'Pending')  { ?> 
                    <div class="total_order_button">
                        <a href="purchase_detail_order.php?cancel_order_id=<?php echo $fetch_order['order_id'] ?>" onclick="return confirm('Bạn muốn hủy đơn hàng này?')" class="cancel_order">
                            Hủy đơn hàng
                        </a>
                    </div>
                    <?php } ?>
                    <div class="total_order_content">
                        <table>
                            <tbody>
                                <tr>
                                    <td class="total_order_title">Phương thức thanh toán:</td>
                                    <td class="total_order_value"><?php echo $fetch_order['method'] ?></td>
                                </tr>
                                <tr>
                                    <td class="total_order_title">Trạng thái thanh toán:</td>
                                    <td class="total_order_value"><?php echo $fetch_order['t_status'] ?></td>
                                </tr>
                              
                                <tr>
                                    <td class="total_order_title">Tổng đơn hàng:</td>
                                    <td class="total_order_value" style="color: orange; font-weight: bold; font-size: 18px">

                                    <?php echo number_format($total_order_price, 0, ',', '.') . ' VNĐ'; ?>

                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <?php include 'footer.php' ?>
    </body>
    </SCript>

    </html>
    <?php
?>