<?php
include '../config.php';
session_start();
$employee_id = $_SESSION['employee_id'];

$product_id = @$_GET['product_id'] ? @$_GET['product_id'] : null;

// Truy vấn sản phẩm
$select_product = mysqli_query($conn, "SELECT 
                  p.seller_id, p.product_id, p.product_name, p.quantity, p.price, p.description, p.created_time, p.time_used,
                   p.product_image, p.warranty_period, p.place_of_purchase, p.purchase_price
                FROM products p
                WHERE p.product_id = '$product_id'");

// Truy vấn thông tin người dùng dựa trên user_id của sản phẩm
if ($row_product = mysqli_fetch_assoc($select_product)) {
    $seller_id = $row_product['seller_id']; // Lấy user_id từ sản phẩm
    $select_user = mysqli_query($conn, "SELECT 
                    u.user_id, u.user_name, u.phone_number, u.address
                    FROM users u
                    WHERE u.user_id = '$seller_id'");

    if (mysqli_num_rows($select_user) > 0) {
        $fetch_user = mysqli_fetch_assoc($select_user);
    }
} else {
    // Nếu không tìm thấy sản phẩm
    echo "Không tìm thấy sản phẩm.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - Admin</title>
    <link rel="stylesheet" href="./css/index.css">
    <link rel="stylesheet" href="../icon/fontawesome-free-6.6.0-web/css/all.min.css">
    <style>
        .account-name {
            position: relative;
            cursor: pointer;
        }

        .dropdown-content {
            display: none;
            position: absolute;
            top: 100%;
            right: 0;
            background-color: white;
            box-shadow: 0px 8px 16px rgba(0, 0, 0, 0.2);
            z-index: 1;
            border-radius: 4px;
            width: 150px;
        }

        .dropdown-content a {
            padding: 10px;
            text-decoration: none;
            display: block;
            color: black;
        }

        .dropdown-content a:hover {
            background-color: #f1f1f1;
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background-color: white;
            padding: 20px;
            border-radius: 4px;
            text-align: center;
            width: 350px;
            height: 100px;
        }

        .modal-content button {
            margin: 10px;
            padding: 8px 16px;
            cursor: pointer;
        }

        .modal-content .btn-ok {
            background-color: white;
            width: 80px;
            border-radius: 5px;
            color: green;
            border: 1px solid green;
        }

        .btn-ok:hover {
            background-color: green;
            color: white;
        }

        .modal-content .btn-cancel {
            width: 80px;
            background-color: white;
            color: red;
            border: 1px solid red;
            border-radius: 5px;
        }

        .btn-cancel:hover {
            background-color: red;
            color: white;
        }

        .content .content_logout {
            width: 100%;
            height: 35px;
            display: flex;
            justify-content: flex-end;
            align-items: center;
        }

        .content_logout button {
            width: 80px;
            height: 35px;
            background-color: white;
            border: none;
            margin-right: 5px;
            cursor: pointer;
        }

        .content_logout button:hover {
            background-color: white;
            border-bottom: 1px solid #ddd;
        }

        /* modal logout */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background-color: white;
            padding: 20px;
            border-radius: 4px;
            text-align: center;
            width: 350px;
            height: 100px;
        }

        .modal-content button {
            margin: 10px;
            padding: 8px 16px;
            cursor: pointer;
        }

        .modal-content .btn-ok {
            background-color: white;
            width: 80px;
            border-radius: 5px;
            color: green;
            border: 1px solid green;
        }

        .btn-ok:hover {
            background-color: green;
            color: white;
        }

        .modal-content .btn-cancel {
            width: 80px;
            background-color: white;
            color: red;
            border: 1px solid red;
            border-radius: 5px;
        }

        .btn-cancel:hover {
            background-color: red;
            color: white;
        }

        .product {
            margin: 20px;
        }

        .product h3 {
            color: #333;
            margin-bottom: 10px;
        }

        .infor_user,
        .infor_product {
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
        }

        th,
        td {
            border: 1px solid #dddddd;
            text-align: left;
            padding: 8px;
        }

        th {
            background-color: #f2f2f2;
            color: #333;
        }

        tbody tr:hover {
            background-color: #f1f1f1;
            /* Hiệu ứng hover cho hàng */
        }

        img {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border-radius: 5px;
            /* Bo góc cho ảnh sản phẩm */
        }
    </style>
</head>

<body>
    <aside class="sidebar">
        <div class="name_website">
            <span>CONNAN</span>
        </div>
        <ul>
            <li class="menu_item">
                <div class="menu_link">
                    <a href="index.php" style="text-decoration: none; color: white">
                        <i class="fa-solid fa-list"></i>
                        <span>Bảng điều khiển</span>
                    </a>
                </div>
            </li>
            <li class="menu_item">
                <div class="menu_link">
                    <a href="approval_product.php" style="text-decoration: none; color: white">
                        <i class="fa-solid fa-check-to-slot"></i>
                        <span>Duyệt sản phẩm</span>
                    </a>
                </div>
            </li>
            <li class="menu_item">
                <div class="menu_link">
                    <a href="management_product.php" style="text-decoration: none; color: white">
                        <i class="fa-brands fa-shopify"></i>
                        <span>Quản lý sản phẩm</span>
                    </a>
                </div>
            </li>
            <li class="menu_item">
                <div class="menu_link">
                    <a href="management_category.php" style="text-decoration: none; color: white">
                        <i class="fa-solid fa-tag"></i>
                        <span>Quản lý danh mục</span>
                    </a>
                </div>
            </li>
            <li class="menu_item">
                <div class="menu_link">
                    <a href="notification.php" style="text-decoration: none; color: white">
                        <i class="fa-solid fa-paper-plane"></i>
                        <span>Gừi thông báo</span>
                    </a>
                </div>
            </li>
            <li class="menu_item">
                <div class="menu_link">
                    <a href="transaction_statistics.php" style="text-decoration: none; color: white">
                        <i class="fa-solid fa-hand-holding-dollar"></i>
                        <span>Thống kê giao dịch</span>
                    </a>
                </div>
            </li>
        </ul>
    </aside>

    <!-- Nội dung chính bên phải -->
    <div class="main-content">
        <header class="header">
            <div class="account-name" onclick="toggleDropdown()">
                <span><?php echo $_SESSION['employee_name'] ?></span>
                <i class="fa-regular fa-circle-user"></i>
                <div id="accountDropdown" class="dropdown-content">
                    <a href="#">Hồ sơ cá nhân</a>
                    <a href="#" onclick="showLogoutModal()">Đăng xuất</a>
                </div>
            </div>
        </header>
        <div class="content">
            <div class="content_title">
                <span>CHI TIẾT SẢN PHẨM</span>
            </div>

            <div class="product">
                <h3>Thông tin người dùng đăng sản phẩm</h3>
                <div class="infor_user">
                    <table>
                        <thead>
                            <tr>
                                <th style="text-align: center; font-size: 15px">Mã người dùng</th>
                                <th style="text-align: center; font-size: 15px">Tên người dùng</th>
                                <th style="text-align: center; font-size: 15px">Số điện thoại</th>
                                <th style="text-align: center; font-size: 15px">Địa chỉ</th>
                                <th style="text-align: center; font-size: 15px">Thời gian đăng</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><?php echo $fetch_user['user_id']; ?></td>
                                <td><?php echo $fetch_user['user_name']; ?></td>
                                <td><?php echo $fetch_user['phone_number']; ?></td>
                                <td><?php echo $fetch_user['address']; ?></td>
                                <td><?php echo $row_product['created_time']; ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <h3>Thông tin sản phẩm</h3>
                <div class="infor_product">
                    <table>
                        <thead>
                            <tr>
                                <th style="text-align: center; font-size: 15px">Ảnh sản phẩm</th>
                                <th style="text-align: center; font-size: 15px">Tên sản phẩm</th>
                                <th style="text-align: center; font-size: 15px">Số lượng</th>
                                <th style="text-align: center; font-size: 15px">Giá</th>
                                <th style="text-align: center; font-size: 15px">Mô tả</th>
                                <th style="text-align: center; font-size: 15px">Thời gian sử dụng</th>
                                <th style="text-align: center; font-size: 15px">Địa chỉ mua</th>
                                <th style="text-align: center; font-size: 15px">Giá khi mua</th>
                                <th style="text-align: center; font-size: 15px">Hạn sử dụng</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><img src="../upload_image/<?php echo $row_product['product_image']; ?>"
                                        alt="Ảnh sản phẩm"></td>

                                <td><?php echo $row_product['product_name']; ?></td>
                                <td><?php echo $row_product['quantity']; ?></td>
                                <td><?php echo number_format($row_product['price'], 0, ',', '.'); ?> VNĐ</td>
                                <td><?php echo $row_product['description']; ?></td>
                                <td><?php echo $row_product['time_used']; ?></td>
                                <td><?php echo $row_product['place_of_purchase']; ?></td>
                                <td><?php echo $row_product['purchase_price']; ?></td>
                                <td><?php echo $row_product['warranty_period']; ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>


    <!-- Modal xác nhận đăng xuất -->
    <div id="logoutModal" class="modal">
        <div class="modal-content">
            <p>Bạn có chắc chắn muốn đăng xuất không?</p>
            <button class="btn-cancel" onclick="hideLogoutModal()">Hủy</button>
            <button class="btn-ok" onclick="confirmLogout()">OK</button>
        </div>
    </div>
</body>
<script>
    function toggleDropdown() {
        const dropdown = document.getElementById('accountDropdown');
        dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
    }

    // Đóng dropdown khi nhấp ra ngoài
    window.addEventListener('click', function (event) {
        const dropdown = document.getElementById('accountDropdown');
        const accountName = document.querySelector('.account-name');
        if (!accountName.contains(event.target)) {
            dropdown.style.display = 'none';
        }
    });

    // Hiển thị modal xác nhận đăng xuất
    function showLogoutModal() {
        document.getElementById('logoutModal').style.display = 'flex';
    }

    // Ẩn modal xác nhận đăng xuất
    function hideLogoutModal() {
        document.getElementById('logoutModal').style.display = 'none';
    }

    // Xác nhận đăng xuất
    function confirmLogout() {
        window.location.href = '../login/logout.php'; // Đường dẫn đến trang xử lý đăng xuất
    }

</script>

</html>