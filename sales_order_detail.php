<?php
include './config.php';
session_start();
error_reporting(E_ALL & ~E_NOTICE);

$order_id = $_GET['order_id'];

if (!$order_id) {
    header('Location: order_purchase.php'); // Điều hướng nếu không có mã đơn hàng
    exit();
}

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
    if (isset($_POST['update_order'])) {
        $new_status = $_POST['status'];

        // Cập nhật trạng thái đơn hàng trong bảng orders
        $update_query = "UPDATE orders SET status='$new_status' WHERE order_id='$order_id'";

        if (mysqli_query($conn, $update_query)) {
            echo "<script>alert('Cập nhật trạng thái thành công!');</script>";

            // Nếu trạng thái đơn hàng là "completed", cập nhật trạng thái thanh toán trong bảng transactions
            if ($new_status == 'completed') {
                // Cập nhật trạng thái thanh toán trong bảng transactions
                $update_transaction_query = "UPDATE transactions SET status ='Đã thanh toán' WHERE order_id='$order_id'";

                if (mysqli_query($conn, $update_transaction_query)) {
                } else {
                    echo "<script>alert('Có lỗi xảy ra khi cập nhật trạng thái đơn hàng!');</script>";
                }
            }

            // Sau khi cập nhật, load lại trang để hiển thị trạng thái mới
            echo "<script>window.location.href = 'sales_order_detail.php?order_id=$order_id';</script>";
        } else {
            echo "<script>alert('Có lỗi xảy ra khi cập nhật trạng thái đơn hàng!');</script>";
        }
    }

    ?>

    <html lang="vi">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Chi tiết đơn hàng</title>
        <link rel="stylesheet" href="./css/main_new.css">
        <link rel="stylesheet" href="./css/header.css">
        <link rel="stylesheet" href="./css/sale_order_detail.css">
        <link rel="stylesheet" href="./icon/fontawesome-free-6.6.0-web/css/all.min.css">

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
                        <p class="main_title">THÔNG TIN KHÁCH HÀNG</p>
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
                                    <td><img src="./upload_image/<?php echo $fetch_product['product_image'] ?>"
                                            alt="Ảnh sản phẩm"></td>
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
                    <div class="total_order_button">
                        <input type="submit" value="Cập nhật đơn hàng" class="update_status_order">
                    </div>
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
                                    <td class="total_order_title">Phí vận chuyển:</td>
                                    <td class="total_order_value">Miễn phí</td>
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

        <!-- Modal cập nhật trạng thái đơn hàng -->
        <div class="modal" id="updateModal">
            <div class="modal-content">
                <h4>Cập nhật trạng thái đơn hàng</h4>
                <form method="post" action="">
                    <select name="status">
                        <option value="processing" <?php echo $fetch_order['status'] == 'processing' ? 'selected' : ''; ?>>
                            Đang xử lý</option>
                        <option value="pending" <?php echo $fetch_order['status'] == 'pending' ? 'selected' : ''; ?>>Chưa xử
                            lý</option>
                        <option value="shipping" <?php echo $fetch_order['status'] == 'shipping' ? 'selected' : ''; ?>>Đang
                            vận chuyển</option>
                        <option value="canceled" <?php echo $fetch_order['status'] == 'canceled' ? 'selected' : ''; ?>>Đã hủy
                        </option>
                        <option value="completed" <?php echo $fetch_order['status'] == 'completed' ? 'selected' : ''; ?>>Đã
                            nhận hàng</option>
                    </select>
                    <button type="submit" name="update_order">Cập nhật</button>
                    <button type="button" class="close-btn">Hủy</button>
                </form>
            </div>
        </div>

        <?php include 'footer.php' ?>
    </body>
    <SCript>
        // JavaScript để hiển thị modal
        document.querySelector('.update_status_order').addEventListener('click', function () {
            document.getElementById('updateModal').style.display = 'flex';
        });

        // Đóng modal khi nhấn nút 'Đóng'
        document.querySelector('.close-btn').addEventListener('click', function () {
            document.getElementById('updateModal').style.display = 'none';
        });

        // Đóng modal khi nhấn ra ngoài modal
        window.addEventListener('click', function (event) {
            const modal = document.getElementById('updateModal');
            if (event.target === modal) {
                modal.style.display = 'none';
            }
        });


    </SCript>

    </html>
    <?php
}
?>