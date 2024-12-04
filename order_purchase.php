<?php
include './config.php';
session_start();
error_reporting(E_ALL & ~E_NOTICE);

$user_id = $_SESSION['user_id'];

if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];

    // Xóa tất cả chi tiết đơn hàng
    $delete_detail_query = "DELETE FROM `orderdetails` WHERE order_id = '$delete_id'";
    if (!mysqli_query($conn, $delete_detail_query)) {
        die('Error deleting order detail: ' . mysqli_error($conn));
    }

    // Xóa đơn hàng
    $delete_query = "DELETE FROM `orders` WHERE order_id = '$delete_id'";
    if (mysqli_query($conn, $delete_query)) {
        echo "<script type='text/javascript'>
            alert('Xóa đơn hàng thành công!');
            window.location.href='order_purchase.php'; // Điều hướng về trang danh sách đơn hàng sau khi xóa
            </script>";
    } else {
        die('Error deleting order: ' . mysqli_error($conn));
    }
}

// Truy vấn đơn hàng theo trạng thái
$statuses = ['pending', 'processing', 'shipping', 'completed', 'canceled'];
$orders = [];

foreach ($statuses as $status) {
    $query = "SELECT p.product_image, p.product_name, od.quantity, o.created_time, o.total_amount, o.order_id, o.status 
              FROM products p 
              JOIN orderdetails od ON p.product_id = od.product_id
              JOIN orders o ON od.order_id = o.order_id 
              WHERE o.purchase_id = '$user_id' AND o.status = '$status'";

    $result = mysqli_query($conn, $query);
    $orders[$status] = mysqli_fetch_all($result, MYSQLI_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý đơn hàng</title>
    <link rel="stylesheet" href="./css/main_new.css">
    <link rel="stylesheet" href="./css/header.css">
    <link rel="stylesheet" href="./icon/fontawesome-free-6.6.0-web/css/all.min.css">
    <style>
        /* Định dạng các tab */
        .tabs {
            display: flex;
            gap: 20px;
            margin: 20px 0;
            justify-content: center;
        }

        .tab {
            width: 200px;
            height: 40px;
            border-radius: 10px;
            padding: 12px 25px;
            background-color: #f7f7f7;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
            transition: background-color 0.3s ease, color 0.3s ease;
            box-shadow: 0 3px 6px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .tab.active {
            background-color: orange;
            color: #fff;
            box-shadow: 0 4px 8px rgba(0, 123, 255, 0.3);
        }

        .tab:hover {
            background-color: orange;
        }

        /* Định dạng các nội dung trong từng tab */
        .content {
            display: none;
            margin-top: 20px;
        }

        .content.active {
            display: block;
        }

        .order_detail {
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            margin: 15px 0;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s ease;
        }

        .order_detail:hover {
            transform: translateY(-5px);
        }

        /* Header của đơn hàng */
        .order_header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .order_status {
            font-weight: bold;
            font-size: 16px;
        }

        .order_status span {
            font-weight: normal;
        }

        /* Nội dung của đơn hàng */
        .order_content {
            display: flex;
            gap: 15px;
            align-items: center;
        }

        .product_image img {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .product_name {
            flex: 1;
            font-size: 16px;
        }

        .product_name strong {
            font-size: 18px;
            color: #333;
        }

        .product_name p {
            font-size: 14px;
            color: #666;
            margin-top: 5px;
        }

        /* Footer của đơn hàng */
        .order_footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 20px;
        }

        .total_order {
            font-size: 16px;
            font-weight: bold;
            color: #333;
        }

        .button_order {
            display: flex;
            gap: 10px;
        }

        .btn_detail,
        .btn_delete {
            padding: 8px 20px;
            background-color: orange;
            color: #fff;
            font-size: 14px;
            border: none;
            border-radius: 25px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            box-shadow: 0 3px 6px rgba(0, 0, 0, 0.1);
        }

        .btn_detail:hover,
        .btn_delete:hover {
            background-color: #0056b3;
        }

        .btn_delete {
            background-color: orangered;
        }

        .btn_delete:hover {
            background-color: #c82333;
        }

        /* Hiển thị alert khi không có đơn hàng */
        .alert {
            text-align: center;
            padding: 20px;
            border-radius: 8px;
            color: orangered;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 10px;
        }

        .alert img {
            width: 250px;
            height: 250px;
            object-fit: cover;
            margin-bottom: 10px;
        }

        .alert p {
            font-size: 20px;
        }

        /* Cải tiến các trạng thái đơn hàng */
        .order_status {
            font-size: 16px;
            font-weight: bold;
        }

        .order_status span {
            font-weight: normal;
        }

        /* Các màu sắc cho từng trạng thái */
        .pending {
            color: #f39c12;
        }

        .processing {
            color: #27ae60;
        }

        .shipping {
            color: #2980b9;
        }

        .completed {
            color: #7f8c8d;
        }

        .canceled {
            color: #e74c3c;
        }

        /* Tạo hiệu ứng hover cho các sản phẩm */
        .product_image img:hover {
            transform: scale(1.1);
            transition: transform 0.3s ease;
        }
    </style>
</head>

<body>
    <?php include 'header.php'; ?>

    <div class="main_content">
        <div class="tabs">
            <div class="tab active" onclick="showTab('pending')">Chưa xử lý</div>
            <div class="tab" onclick="showTab('processing')">Đang xử lý</div>
            <div class="tab" onclick="showTab('shipping')">Đang vận chuyển</div>
            <div class="tab" onclick="showTab('completed')">Hoàn tất</div>
            <div class="tab" onclick="showTab('canceled')">Đã hủy</div>
        </div>


        <?php foreach ($statuses as $status): ?>
            <div class="content" id="<?php echo $status; ?>">
                <?php if (!empty($orders[$status])): ?>
                    <?php foreach ($orders[$status] as $order): ?>
                        <div class="order_detail">
                            <div class="order_header">
                                <div class="order_status">
                                    Trạng thái: <span
                                        style="color: <?php echo getStatusColor($status); ?>"><?php echo ucfirst($status); ?></span>
                                </div>
                                <div class="order_time">
                                    Thời gian: <?php echo $order['created_time']; ?>
                                </div>
                            </div>
                            <div class="order_content">
                                <div class="product_image">
                                    <img src="./upload_image/<?php echo $order['product_image']; ?>" alt="Ảnh sản phẩm" width="100">
                                </div>
                                <div class="product_name">
                                    <strong><?php echo $order['product_name']; ?></strong>
                                    <p>Số lượng: <?php echo $order['quantity']; ?></p>
                                </div>
                            </div>
                            <div class="order_footer">
                                <div class="total_order">
                                    Thành tiền: <?php echo number_format($order['total_amount'], 0, ',', '.') . ' VNĐ'; ?>
                                </div>
                                <div class="button_order">
                                    <a href="purchase_detail_order.php?order_id=<?php echo $order['order_id']; ?>">
                                        <button class="btn_detail">Xem chi tiết</button>
                                    </a>
                                    <a href="order_purchase.php?delete=<?php echo $order['order_id']; ?>"
                                        onclick="return confirm('Bạn có chắc muốn xóa đơn hàng này?')">
                                        <button class="btn_delete">Xóa</button>
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="alert">
                        <img src="./img/order.png" alt="Ảnh giỏ hàng">
                        <p>Bạn không có đơn hàng nào</p>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>

    <script>
        function showTab(tabName) {
            // Ẩn tất cả các tab
            var contents = document.querySelectorAll('.content');
            contents.forEach(function (content) {
                content.classList.remove('active');
            });

            // Hiển thị tab được chọn
            var activeTab = document.getElementById(tabName);
            activeTab.classList.add('active');

            // Cập nhật trạng thái các tab
            var tabs = document.querySelectorAll('.tab');
            tabs.forEach(function (tab) {
                tab.classList.remove('active');
            });

            document.querySelector(`.tab[onclick="showTab('${tabName}')"]`).classList.add('active');
        }
    </script>

    <?php include 'footer.php'; ?>
</body>
<script>
    // Hàm hiển thị tab
    function showTab(tabName) {
        // Ẩn tất cả các tab
        var contents = document.querySelectorAll('.content');
        contents.forEach(function (content) {
            content.classList.remove('active');
        });

        // Hiển thị tab được chọn
        var activeTab = document.getElementById(tabName);
        activeTab.classList.add('active');

        // Cập nhật trạng thái các tab
        var tabs = document.querySelectorAll('.tab');
        tabs.forEach(function (tab) {
            tab.classList.remove('active');
        });

        document.querySelector(`.tab[onclick="showTab('${tabName}')"]`).classList.add('active');
    }

    // Đảm bảo khi trang được tải, tab "Chưa xử lý" được hiển thị
    document.addEventListener('DOMContentLoaded', function () {
        showTab('pending');
    });

</script>

</html>

<?php
// Hàm xác định màu sắc của trạng thái đơn hàng
function getStatusColor($status)
{
    switch ($status) {
        case 'pending':
            return 'orange';
        case 'processing':
            return 'green';
        case 'shipping':
            return 'blue';
        case 'completed':
            return 'gray';
        case 'canceled':
            return 'red';
        default:
            return 'black';
    }
}
?>