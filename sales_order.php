<?php
include './config.php';
session_start();
error_reporting(E_ALL & ~E_NOTICE);

$user_id = $_SESSION['user_id'];

if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];

    // Xóa tất cả chi tiết đơn hàng liên quan trước
    $delete_detail_query = "DELETE FROM `orderdetails` WHERE order_id = '$delete_id'";
    mysqli_query($conn, $delete_detail_query);

    // Sau đó xóa đơn hàng
    $delete_query = "DELETE FROM `orders` WHERE order_id = '$delete_id'";

    if (mysqli_query($conn, $delete_query)) {
        echo "<script type='text/javascript'>
            window.alert('Xóa đơn hàng thành công!');
            </script>";
    } else {
        die('Query failed: ' . mysqli_error($conn));
    }
}
?>


<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đơn Bán Hàng</title>
    <link rel="stylesheet" href="./css/main_new.css">
    <link rel="stylesheet" href="./css/header.css">

    <link rel="stylesheet" href="./icon/fontawesome-free-6.6.0-web/css/all.min.css">

    <style>
        .main_content {
            width: 100%;
            min-height: 300px;
            display: flex;
            align-items: center;
            flex-direction: column;
        }

        .list_order {
            width: 80%;
            margin: 10px;
            padding: 10px;
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

        table {
            margin: 10px;
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

        .button_icon a {
            text-decoration: none;
        }

        .alert {
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
        }

        .alert img {
            width: 200px;
            height: 200px;
            object-fit: cover;
        }

        .alert p {
            font-size: 20px;
            color: green;
        }

        /* THỐNG KÊ */
        .statistical {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: flex-start;
        }

        .statistical .statisticsl_revenue {
            width: 150px;
            display: flex;
            justify-content: center;
            align-items: center;
            border: none;
            background-color: none;
            color: green;
            font-size: 15px;
            border-bottom: 1px solid yellowgreen;
            cursor: pointer;
        }

        .statistical a {
            text-decoration: none;
        }
    </style>

</head>

<body>

    <?php include 'header.php' ?>

    <div class="main_content">
    <div class="list_order">
        <?php
        $select_order = mysqli_query($conn, "SELECT 
        p.quantity, 
        od.quantity, 
        o.order_id, 
        o.created_time, 
        o.status,
        t.status as t_status,
        o.total_amount, o.fullname, o.phone_number, o.city, o.district, o.ward
        FROM products p 
        JOIN orderdetails od ON p.product_id = od.product_id
        JOIN orders o ON od.order_id = o.order_id
        JOIN transactions t ON od.order_id = t.order_id
        WHERE p.seller_id = $user_id
        ORDER BY o.created_time DESC");

        if (mysqli_num_rows($select_order) > 0) {
            // Hiển thị tiêu đề và bảng nếu có đơn hàng
            echo '<div class="primary_title">
                      <h4>DANH SÁCH ĐƠN BÁN HÀNG</h4>
                  </div>';
            echo '<div class="statistical">
                      <a href="./statistical.php" class="statisticsl_revenue">Thống kê doanh thu</a>
                  </div>';
            echo '<table>
                      <thead>
                          <tr>
                              <th>Mã đơn hàng</th>
                              <th>Họ tên khách hàng</th>
                              <th>Số điện thoại</th>
                              <th>Tổng tiền</th>
                              <th>Phương thức thanh toán</th>
                              <th>Ngày đặt hàng</th>
                              <th>Trạng thái</th>
                              <th>Thao tác</th>
                          </tr>
                      </thead>
                      <tbody>';

            while ($fetch_order = mysqli_fetch_assoc($select_order)) {
                echo '<tr>
                          <td>' . $fetch_order['order_id'] . '</td>
                          <td>' . $fetch_order['fullname'] . '</td>
                          <td>' . $fetch_order['phone_number'] . '</td>
                          <td>' . number_format($fetch_order['total_amount'], 0, ',', '.') . ' VNĐ</td>
                          <td>' . $fetch_order['t_status'] . '</td>
                          <td>' . $fetch_order['created_time'] . '</td>
                          <td>' . ($fetch_order['status'] == 'processing' ? '<span style="color: green;">' . $fetch_order['status'] . '</span>' : ($fetch_order['status'] == 'pending' ? '<span style="color: orange;">' . $fetch_order['status'] . '</span>' : '<span style="color: red;">' . $fetch_order['status'] . '</span>')) . '</td>
                          <td class="button_icon">
                              <a href="sales_order_detail.php?order_id=' . $fetch_order['order_id'] . '">
                                  <i class="fa-solid fa-pen-to-square" style="color: blue; font-size: 20px;"></i>
                              </a>
                              <a href="sales_order.php?delete=' . $fetch_order['order_id'] . '" 
                                 onclick="return confirm(\'Bạn có chắc muốn xóa đơn hàng này?\')" 
                                 style="margin-left: 10px; color: red; font-size: 20px">
                                  <i class="fa-solid fa-trash"></i>
                              </a>
                          </td>
                      </tr>';
            }

            echo '</tbody>
                  </table>';
        } else {
            // Hiển thị thông báo nếu không có đơn hàng
            echo '<div class="alert">
                      <img src="./img/saleorder.png" alt="Ảnh giỏ hàng">
                      <p>Bạn chưa có đơn hàng nào!</p>
                  </div>';
        }
        ?>
    </div>
</div>


    <?php include 'footer.php' ?>
</body>

</html>